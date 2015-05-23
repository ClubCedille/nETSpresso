<?php

namespace GO\Netspresso\Cron;

require_once 'HTTP/Request2.php';

class NetspressoInitiator extends \GO\Base\Cron\AbstractCron {
	
	const ISO8601 = "Y-m-d\TH:i:sO" ;
	const SEARCH_OFSFSET = 300;
	
	/**
	 * Return true or false to enable the selection for users and groups for 
	 * this cronjob.
	 * 
	 * CAUTION: This will give the run() function a different behaviour. 
	 *					Please see the documentation of the run() function 
	 *					to see what is different.
	 */
	public function enableUserAndGroupSupport(){
		return false;
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getLabel(){
		return \GO::t('cronNetspressoInitiator','netspresso');
	}
	
	/**
	 * Get the unique name of the Cronjob
	 * 
	 * @return String
	 */
	public function getDescription(){
		return \GO::t('cronNetspressoInitiatorDescription','netspresso');
	}

	/**
	 * Get the incoming events for the associated calendar
	 * 
	 * @param Array $params
	 * @return Array The array with the incomming events 
	 */	 
	public function getEventsForPeriod($calendar_id, $start, $end) {
		return \GO\Calendar\Model\Event::model()->findCalculatedForPeriod(
			\GO\Base\Db\FindParams::newInstance()->criteria(
				\GO\Base\Db\FindCriteria::newInstance()->addCondition('calendar_id', '','!=')
			)->select(),
			$start, 
			$end
		);
	}
	
	/**
	 * Get the incoming events for the associated calendar
	 * 
	 * @param Array $params
	 * @return Array The array with the incomming events 
	 */	 
	private function findNextEvents($calendar_id, $start, $end) {
		
		$joinCriteria = \GO\Base\Db\FindCriteria::newInstance()
//						->addCondition('user_id', GO::user()->id,'=','pt')
						->addCondition('calendar_id', 'pt.calendar_id', '=', 't', true, true);
		
		$calendarJoinCriteria = \GO\Base\Db\FindCriteria::newInstance()
						->addCondition('calendar_id', 'tl.id', '=', 't', true, true);
		
		$findParams = \GO\Base\Db\FindParams::newInstance()
						->select('t.*, tl.name AS calendar_name')
						->ignoreAcl()
						->join(\GO\Calendar\Model\PortletCalendar::model()->tableName(),$joinCriteria,'pt')
						->join(\GO\Calendar\Model\Calendar::model()->tableName(), $calendarJoinCriteria,'tl');
		
			
		$events = \GO\Calendar\Model\Event::model()->findCalculatedForPeriod($findParams, $start, $end);
		//GO::debug("Netspresso::findNextEvents events => " . var_export($events, true));
		
		return $events;

	}
	
	/**
	 * The code that needs to be called when the cron is running
	 * 
	 * If $this->enableUserAndGroupSupport() returns TRUE then the run function 
	 * will be called for each $user. (The $user parameter will be given)
	 * 
	 * If $this->enableUserAndGroupSupport() returns FALSE then the 
	 * $user parameter is null and the run function will be called only once.
	 * 
	 * @param GO_Base_Cron_CronJob $cronJob
	 * @param GO_Base_Model_User $user [OPTIONAL]
	 */
	public function run(\GO\Base\Cron\CronJob $cronJob,\GO\Base\Model\User $user = null){
		
		// Run the as root
		\GO::session()->runAsRoot();

		// Get the calendar id associated to the nÉTSpresso resource
		//$resource_calendar_id = self::getConfigResourceId();
		$resource_calendar_id = \GO\Netspresso\Model\NetspressoConfig::getResourceId();

		// calculate start and end period times	
		$start = strtotime("now");
		//$end   = strtotime('+5 minutes');
		$end   = $start + \GO\Netspresso\Model\NetspressoConfig::getReadyBefore() + self::SEARCH_OFSFSET;

		// Search events associated to the nÉTSpresso resource
		//$events = self::findNextEvents($resource_calendar_id, $start, $end);
		$events = self::getEventsForPeriod($resource_calendar_id, $start, $end);

		// Parse events to locate next immediate event	
		foreach($events as $event){

			$record = $event->getResponseData();
 			//\GO::debug("Netspresso: next event => " . var_export($record, true));
 			
			// Ensure the resource status is CONFIRMED
			if ($record['status'] != \GO\Calendar\Model\Event::STATUS_CONFIRMED) {
				\GO::debug("Netspresso: resource status (" . $record['status'] . ") is not CONFIRMED");
				continue;
			}
			
			// Ensure the event start within the required time frame
			//GO::debug("Netspresso::run runjb time between " . date("Y-m-d H:i", $start) . " and " . date("Y-m-d H:i", $end));
			//GO::debug("Netspresso::run event time between " . date("Y-m-d H:i", $event->getAlternateStartTime()) . " and " . date("Y-m-d H:i", $event->getAlternateEndTime()));				
			
			if( $end < $event->getAlternateStartTime() or $event->getAlternateStartTime() < $start) {
				\GO::debug("Netspresso::run runjb time between " . date("Y-m-d H:i", $start) . " and " . date("Y-m-d H:i", $end));
				\GO::debug("Netspresso::run event time between " . date("Y-m-d H:i", $event->getAlternateStartTime()) . " and " . date("Y-m-d H:i", $event->getAlternateEndTime()));				
				continue;
			}

 			$eventRecord = \GO\Calendar\Model\Event::model()->findByPk($record['id']);
 			$cfRecord = $eventRecord->getCustomfieldsRecord();
 			//\GO::debug("Netspresso: next event custom fields => " . var_export($cfRecord, true));
 			$attribute = $cfRecord->getAttributeByName('Détail de salles', 'Netspresso');

 			// Ensure the netspresso was requested 
 			if ($attribute != 1) {
				\GO::debug("Netspresso: resource Netspresso (" . $attribute . ") was not requested");
				continue;
			}
			
			// Send this record to the nÉTSpresso server
			self::sendEventToNetspreso($event->getEvent());

		} //end foreach
		
	}
	
	/**
	 * Get the incoming events for the associated calendar
	 * 
	 * @param Array $params
	 * @return Array The array with the incomming events 
	 */	 
	
	private static function sendEventToNetspreso($event) {

		//\GO::debug("Netspresso::sendToNetspreso (" . var_export($data, true) . ")");
		
		// Prepare the request body
		$message = array (
			'box' => array (
				'name'  => 'netspresso01',
				'state' => 'Ready'
			),
			'event' => array (
				'uuid'				=> $event->uuid,
				'event_id'			=> $event->resource_event_id,
				'resource_event_id'	=> $event->resource_event_id,
				'calendar_id'		=> $event->calendar_id,
				'user_id'			=> $event->user_id,
				'username'			=> $event->user->getName(),
				'start_time'		=> date(\DateTime::ISO8601, $event->start_time),
				'end_time'			=> date(\DateTime::ISO8601, $event->end_time),
				'subjet'			=> $event->name,
				'status'			=> $event->status,
				'ready_time'		=> date(\DateTime::ISO8601, $event->start_time - \GO\Netspresso\Model\NetspressoConfig::getReadyBefore()),
				'stdby_time'		=> date(\DateTime::ISO8601, $event->start_time + \GO\Netspresso\Model\NetspressoConfig::getStdbyAfter()),
			)
		);
		\GO::debug("Netspresso::sendToNetspreso (" . var_export($message, true) . ")");

		// Set the destination URL
		$URL = 'http://critias.etsmtl.ca:8080/go/event.json';

		//create the http request object
		$request = new \HTTP_Request2($URL, \HTTP_Request2::METHOD_POST);

		//add the headers
		$headers = array('Content-Type' => 'application/json',
						 'X-Auth-Token' => 'tr9D96HJtlcH');
		$request->setHeader($headers);

		//add the json encoded data
		$request->setBody(json_encode($message));
		
		//send the http request
		try {
			$response = $request->send();
			
    		if (200 == $response->getStatus()) {
        		\GO::debug("Netspresso::sendToNetspreso response : " . $response->getBody());
        		
        		$response = json_decode($response->getBody(), true);

    		} else {    	
    			\GO::debug("Netspresso::sendToNetspreso Unexpected response: " . $response->getStatus() . ' ' . $response->getReasonPhrase());
    		}
		} catch (\HTTP_Request2_Exception $e) {
    		\GO::debug("Netspresso::sendToNetspreso Error: " . $e->getMessage() );
		}
		
	}

	
}
