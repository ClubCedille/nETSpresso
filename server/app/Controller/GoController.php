<?php
App::uses('AppController', 'Controller');
/**
 * Groupe Office Controller
 *
 * @property Event $Event
 */
class GoController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

/**
 * Models
 *
 * @var array
 */
	public $uses = array('Netspresso', 'Event', 'Metric');

/**
 * beforeFilter method
 *
 * @return void
 */
	public function beforeFilter() {

		// Throw an error in case json_decode function is not defined
    	if(!function_exists('json_decode')) {
			throw new NotImplementedException(__('json_decode function is not available'));    	
    	}

		// Automatically decoding request data
		$this->RequestHandler->addInputType('json', array('json_decode', true));
		
// TODO : 
//	- add detector for client accepting JSON data ex:
//$this->request->addDetector('json',array('callback'=>function($req){return $req->accepts('application/json');}));
//  - add security on headers X-Auth-Token: tr9D96HJtlcH
// For debuging
//$this->log("GoController::event input data: " . var_export($this->request,true));		

		parent::beforeFilter();
}



/**
 * add events method
 *
 * @throws BadRequestException, InternalErrorException
 * @param  none
 * @return void
 */
	public function event() {

		// Only handle POST method
		$this->request->allowMethod('post');

		// Get JSON dencoded data received from client
		$jsonData = $this->request->data;

		// Throw an error in case JSON data is not properly decoded
     	if(is_null($jsonData) or $jsonData == false) {
         	throw new BadRequestException(__('The received input json data is malformed'));	
     	}

		// Try to save request data
		try {
			// Save the Event model with request data
			$this->Event->save($jsonData['event']);
		} catch(Exception $e) {
			$this->log("GoController::event caught exception: " .   $e->getMessage());	
			$this->log("GoController::event input data: " . var_export($jsonData,true));
			// TODO : Verify for duplicate entries as the client keep trying
			// instead of throwing an error
			// throw new InternalErrorException(__('The event could not be saved. Please, try again.'));
		}

		// Initialize the nETSpresso object
		$this->Netspresso->loadByName($jsonData['box']['name'], true);

		//For debuging
		//$this->log("NetspressoController::heartbeat current state " . $heartbeat['box']['state']);
		//$this->log("NetspressoController::heartbeat final state " . $this->Netspresso->get('state'));

		// Adjust box state if nescesary
		//
		if ($this->Netspresso->getEventState() !== 'Locked') {
			$this->Netspresso->setEventState($jsonData['box']['state']);
			$this->Netspresso->setEventTime($jsonData['event']['ready_time']);
			$this->Netspresso->setEventStdbyTime($jsonData['event']['stdby_time']);
		}
		
		// Try to save the new state
		try {
			$this->Netspresso->save();
		} catch(Exception $e) { 
			$this->log("GoController::event caught exception: " . $e->getMessage());
			$this->log("GoController::event input data: " . var_export($jsonData, true));
			throw new InternalErrorException(__('The state could not be saved. Please, try again.'));
		}

		// Create response object reply
		//
		$response = $this->create_response_object_reply('200', 'OK');

		// Serialize the response object
		//
		$this->set(array('response' => $response));
		$this->set('_serialize', array('response'));

	} //end of event

/**
 * create_response_object_reply method
 *
 * @throws none
 * @param  string $code, string $message
 * @return array $response
 */
	private function create_response_object_reply($code = null, $message = null) {
		
		// Preapare response object reply
		//
		$datetime = new DateTime();		
		$response = array (	'date' => $datetime->format(DateTime::ISO8601),
							'code' => $code,
							'message' => $message,);
 		return $response;

	} // end of create_response_object_reply

/**
 * status method
 *
 * @throws none
 * @param  none
 * @return void
 */
	public function status() {
		
		// Only handle GET method
		$this->request->allowMethod('get');

		// Get box name received from client
		$boxname = $this->request->query('box');
		
		// Throw an error in case box name is not received
     	if(is_null($boxname)) {
         	throw new BadRequestException(__('Missing parameter url parameter: box'));	
     	}

		// For debug
		// $this->log("GoController::status input data: " . $boxname);
	
		// Initialize the nETSpresso object
		if (!$this->Netspresso->loadByName($boxname, false)){
			throw new BadRequestException(__('Unable to find the box named :' . $boxname));
		}

		// For debug
		//$this->log("GoController::status netspresso box found: " . var_export($this->Netspresso, true));

 		// Get the heartbeat status
		$status = $this->Netspresso->getHeartbeatState();

		// Get the heartbeat temperature
		$temperature = $this->Netspresso->getHeartbeatTemperature();

		// Initialize the reponse object
		$datetime = new DateTime();		
		$this->set(array (	'date' => $datetime->format(DateTime::ISO8601),
							'status' => $status,
							'temperature' => $temperature,));

		// Serialize the response object
		//
		$this->set('_serialize', array('date', 'status', 'temperature' ));


	} // end of status



} // end of class
