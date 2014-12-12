<?php
App::uses('AppController', 'Controller');
/**
 * Netspresso Controller
 *
 * @property Metric $Metric
 * @property PaginatorComponent $Paginator
 */
class NetspressoController extends AppController {

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
	public $uses = array('Netspresso', 'Metric');

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
//$this->log("NetspressoController::beforeFilter input data: " . var_export($this->request,true));

		parent::beforeFilter();
}


/**
 * method json_decompress
 *
 * @throws BadRequestException, InternalErrorException, Exception
 * @param  string
 * @return array
 */

private function json_decompress($received){
    $build = array();
    //$received = json_decode($input);
    $states = array("Stand-By", "Warming-Up", "Ready", "Cooling-Down", "Locked");

    // Build basic box info
    $build["box"] = array(
        "name" => $received["box"]["n"],
        "state" => $states[$received["box"]["s"]],
        "temperature" => $received["box"]["t"],
    );

    //Keep the box name as an easy to access var
    $boxName = $build["box"]["name"];

    // Build metrics
    $build["metrics"] = array();

	if (!is_array($received["sensors"])) return $build;
	
    //foreach(array_keys($received["sensors"]) as $sensorType => $values) {
    foreach($received["sensors"] as $sensorType => $values) {
        // Go through each sensor type
        $sensorIndex = 1;
        foreach($values as $val) {
            // And each of the individual sensors within
            $nameFormat = '%s.%s.%02d';
            $sensor = array(
                //Build the sensor name
                "sensor" => sprintf($nameFormat, $boxName, $sensorType, $sensorIndex),
                "acquired" => date("c")
            );

            if(!is_array($val)) {
                // If the value is not an object, interpret it as no unit
                $sensor["units"] = "Bool";
                $sensor["value"] = $val;
            } else {
                // When the value is an object, map the keys
                $sensor["units"] = $this->expand_units($val["u"]);
                $sensor["value"] = $val["v"];
            }

            // Add the sensor to the metrics array
            array_push($build["metrics"], $sensor);
            $sensorIndex++;
        }
    }

    return $build;
}

/**
 * method netspresso_expand_units
 *
 * @throws none
 * @param  string
 * @return string
 */
private function expand_units($unitType) {
    switch($unitType) {
        case "V":
            return "Volts";
        case "C":
        	return "°C";
        case "A":
            return "Amperes";
        default:
            return $unitType;
    }
}


/**
 * method metric
 *
 * @throws BadRequestException, InternalErrorException, Exception
 * @param  none
 * @return array array $response
 */
	public function metric() {
	
		// Only handle POST method
		$this->request->allowMethod('post');

		// Get JSON dencoded data received from client
		$jsonData = $this->request->data; 

		// For debuging
		//$this->log("NetspressoController::metric input data: " . var_export($jsonData, true));

		// Throw an error in case JSON data is not properly decoded
		if(is_null($jsonData) or $jsonData == false) {
			throw new BadRequestException(__('The received input json data is malformed'));
		}

		// Initialize the Metric model
		$this->Metric->create();

		try {

			// Save the Event model with request data
			if (!$this->Metric->save($this->request->data)) {
				throw new InternalErrorException(__('The metric could not be saved. Please, try again.'));
			}

		} catch(Exception $e) {

			// TODO : Verify for duplicate entries as the client keep trying
			// instead of throwing an error

			$this->log("NetspressoController::metric caught exception: " .   $e->getMessage());
			$this->log("NetspressoController::metric input data: " . var_export($this->request->data,true));

		}

		// Create the response object
		$response = $this->create_response_object_reply('000', 'Ok');

		// Serialize the response object
		//
		$this->set(array('response' => $response));
		$this->set('_serialize', array('response'));
		
	} // end of metric

/**
 * method create_response_object_reply method
 *
 * @throws none
 * @param  string $code, string $message
 * @return array $response
 */
	private function create_response_object_reply($code = null, $message = null) {
		
		// Preapare response object reply
		//
		$datetime = new DateTime();
		$response = array (	//'date' => $datetime->format(DateTime::ISO8601),
							'code' => $code,
							'message' => $message,);
		return $response;

	} // end of create_response_object_reply

/**
 * method save_metrics
 *
 * @throws BadRequestException, InternalErrorException, Exception
 * @param  array $metrics
 * @return none
 */
	private function save_metrics($metrics = null) {

		//For debuging
		//$this->log("NetspressoController::save_metrics input data: " . var_export($metrics, true));

		// Loop on received metrics
		foreach ( $metrics as $metric ) {

			// Initialize the Metric model
			$this->Metric->create();

			try {
				// Save the Event model with request data
				if (! $this->Metric->save($metric)) {
					throw new InternalErrorException(__('The metric could not be saved. Please, try again.'));
				}
			} catch(Exception $e) { 
				$this->log("NetspressoController::save_metrics caught exception: " .   $e->getMessage());
				$this->log("NetspressoController::save_metrics input data: " . var_export($metric, true));
			}
			
			// Clear the metric model before loop
			$this->Metric->clear();
		}

	} // end of save_metrics


/**
 * method heartbeat
 *
 * @throws BadRequestException, InternalErrorException, Exception
 * @param  none
 * @return array array $response
 */

	public function heartbeat() {
	
		// Only handle POST method
		$this->request->allowMethod('post');

		// Get JSON dencoded data received from client
		$heartbeat = $this->json_decompress($this->request->data);

		// Throw an error in case JSON data is not properly decoded
		if(is_null($heartbeat) or $heartbeat == false) {
			throw new BadRequestException(__('The received input json data is malformed'));
		}

		//For debuging
		$this->log("NetspressoController::heartbeat input data: " . var_export($heartbeat, true));

		// Save metrics
		if(!is_null($heartbeat['metrics']) and $heartbeat['metrics'] != false) {
			$this->save_metrics($heartbeat['metrics']);
		}
		
		// Initialize the nETSpresso object
		//$this->Netspresso->loadByMac($heartbeat['network']['mac']);
		$this->Netspresso->loadByName($heartbeat['box']['name'], true);

		//For debuging
		//$this->log("NetspressoController::heartbeat current state " . $heartbeat['box']['state']);
		//$this->log("NetspressoController::heartbeat final state " . $this->Netspresso->get('state'));

		// Validate received current state
		if (! $this->Netspresso->validateState($heartbeat['box']['state'])) {
			throw new BadRequestException(__("The current state (" . $heartbeat['box']['state'] . ") is not valid"));
		}
		
		// Adjust event state if nescesary
		$this->Netspresso->evaluateEventState($heartbeat);
		
		// Get the next action from current to final state
		$action = $this->Netspresso->getNextAction($heartbeat['box']['state']);
		
		// set the last heartbeat time
		$this->Netspresso->setHeartbeatTime(date("Y-m-d H:i:s"));
		
		// Set the last heartbeat temperature
		$this->Netspresso->setHeartbeatTemperature($heartbeat['box']['temperature']);

		// set the last heartbeat state
		$this->Netspresso->setHeartbeatState($heartbeat['box']['state']);
		
		// Save the new state
		try {	
			$this->Netspresso->save();
		} catch(Exception $e) { 
				$this->log("NetspressoController::heartbeat caught exception: " . $e->getMessage());
				$this->log("NetspressoController::heartbeat input data: " . var_export($heartbeat, true));
				throw new InternalErrorException(__('The state could not be saved. Please, try again.'));
		}

		// Create the response object
		$response = $this->create_response_object_reply($action['code'], $action['message']);

		// Serialize the response object
		//
		$this->set(array('response' => $response));
		$this->set('_serialize', array('response'));

	} // end of heartbeat	

} // end of class
