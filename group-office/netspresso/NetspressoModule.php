<?php
class GO_Netspresso_NetspressoModule extends GO_Base_Module{

	public function autoInstall() {
			return true;
	}

	public function author() {
			return 'Club Cedille - ETS';
	}

	public function authorEmail() {
			return 'webmaster@cedille.club';
	}

	public static function initListeners() {
	
		// Every model that is derived from GO_Base_Db_ActiveRecord has a "save" and "delete" event. 
		// They listeners will be called with the model as the first argument.
		// attaching to the GO_Calendar_Model_Event model
		GO_Calendar_Model_Event::model()->addListener('save', 'GO_Netspresso_NetspressoModule', 'save');
		GO_Calendar_Model_Event::model()->addListener('delete', 'GO_Netspresso_NetspressoModule', 'delete');


		// Every controller that is derived from GO_Base_Controller_AbstractModelController 
		// has a "submit", "load", "display" and "delete" event. 
		// They are all called with these parameters: $controller, $response,$model,$params,$modifiedAttributes
		// attaching to a controller works a bit different
		$c = new GO_Calendar_Controller_Event();
		$c->addListener('submit', 'GO_Netspresso_NetspressoModule', 'submit');
		//$c->addListener('load', 'GO_Netspresso_NetspressoModule', 'load');
		//$c->addListener('store', 'GO_Netspresso_NetspressoModule', 'store');
		//$c->addListener('display', 'GO_Netspresso_NetspressoModule', 'display');
		
		//addListener(dismiss,GO_Calendar_Model_Event, reminderDismissed)
		//addListener(reminderdisplay,GO_Email_EmailModule, reminderDisplay)
		//GO_Base_Model_Reminder::model()->addListener('dismiss', "GO_Calendar_Model_Event", "reminderDismissed");

	}


	public static function save(&$model, $wasNew){
		//do something with the GO_Calendar_Model_Event module here
		GO::debug("Netspresso: saving an event !");

		if (! $model->isResource()) {
			GO::debug("Netspresso: It's not an resource event !");
			return;
		}
		
		if ($model->calendar->name !== 'nÉTSpresso') {
			GO::debug("Netspresso: Calendar Name => " . $model->calendar->name);
			GO::debug("Netspresso: It's not an nÉTSpresso resource event !");
			return;
		}
		
		GO::debug("Netspresso: export => " . var_export(get_object_vars($model), true));
		
		$myClassReflection = new ReflectionClass(get_class($model));
      	$secret = $myClassReflection->getProperty('_attributes');
      	$secret->setAccessible(true);
      	//echo $secret->getValue($model);
		
		GO::debug("Netspresso: export => " . var_export($secret->getValue($model), true));
		
		
// 		GO::debug("Netspresso: event_id => " . $model->id);
// 		GO::debug("Netspresso: resource_event_id => " . $model->resource_event_id);
// 		GO::debug("Netspresso: status => " . $model->status);
// 		GO::debug("Netspresso: calendar_id => " . $model->calendar_id);
// 		GO::debug("Netspresso: user_id => " . $model->user_id);

		// Prepare the message body
		$message = array (
			'action'		=> 'ADD',
			'user_id'		=> $model->user_id,
			'calendar_id'	=> $model->calendar_id,
			'event_id'		=> $model->resource_event_id,
			'resource_id'	=> $model->id,
		);

		// send message to nETSpresso
		//self::sendToNetspreso($message);

		// nothing else to do
		return;

	}

	public static function delete(&$model){
		//do something with the GO_Calendar_Model_Event module here
		GO::debug("Netspresso: deleting an event !");

		if (! $model->isResource()) {
			GO::debug("Netspresso: It's not an resource event !");
			return;
		}
		
		// nothing else to do
		return;
	}

	private static function sendToNetspreso($data) {

		//GO::debug("::sendToNetspreso (" . var_export($data, true) . ")");

		// Set the destination URL
		$URL = 'http://netspresso.cedille.club/go/event';

		//create the http request object
		$request = new HTTP_Request2($URL, HTTP_Request2::METHOD_GET);

		//add the headers
		$headers = array('Content-Type' => 'application/json',
						 'X-Auth-Token' => 'tr9D96HJtlcH');
		$request->setHeader($headers);

		//add the json encoded data
		$request->setBody(json_encode($data));
		
		//send the http request
		try {
			$response = $request->send();
			
    		if (200 == $response->getStatus()) {
        		GO::debug("::sendToNetspreso response : " . $response->getBody());

    		} else {    	
    			GO::debug("::sendToNetspreso Unexpected response: " . $response->getStatus() . ' ' . $response->getReasonPhrase());
    		}
		} catch (HTTP_Request2_Exception $e) {
    		GO::debug("::sendToNetspreso Error: " . $e->getMessage() );
		}
		
		//debug the result
		//GO::debug("::sendToNetspreso HTTP response status => " . $result->status() );
		//GO::debug("::sendToNetspreso HTTP request body => " . $result->getRequestBody() );

	}

	public static function submit(&$controller, &$response, &$model, &$params, $modifiedAttributes){
		//do something with the GO_Calendar_Controller_Event module here
		GO::debug("Netspresso: submiting an event !");
	}

	public static function load(&$controller, &$response, &$model, &$params){
		//do something with the GO_Calendar_Controller_Event module here
		GO::debug("Netspresso: loading an event !");
	}

	public static function store(&$controller, &$response, &$model, &$params){
		//do something with the GO_Calendar_Controller_Event module here
		GO::debug("Netspresso: storing an event !");
	}

	public static function display(&$controller, &$response, &$model){
		//do something with the GO_Calendar_Controller_Event module here
		GO::debug("Netspresso: displaying an event !");
	}

}

?>

