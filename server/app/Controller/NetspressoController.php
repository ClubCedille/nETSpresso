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
		$response = array (	'date' => $datetime->format(DateTime::ISO8601),
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
		$heartbeat = $this->request->data;

		// Throw an error in case JSON data is not properly decoded
		if(is_null($heartbeat) or $heartbeat == false) {
			throw new BadRequestException(__('The received input json data is malformed'));
		}

		//For debuging
		//$this->log("NetspressoController::heartbeat input data: " . var_export($heartbeat, true));

		// Save metrics 
		$this->save_metrics($heartbeat['metrics']);
		
		// Initialize the nETSpresso object
		$this->Netspresso->load($heartbeat['network']['mac']);

		//For debuging
		//$this->log("NetspressoController::heartbeat current state " . $heartbeat['box']['state']);
		//$this->log("NetspressoController::heartbeat final state " . $this->Netspresso->get('state'));

		// Validate current state
		if (! $this->Netspresso->validateState($heartbeat['box']['state'])) {
			throw new BadRequestException(__("The current state (" . $heartbeat['box']['state'] . ") is not valid"));
		}
		
		// Adjust box state if nescesary
		$action = $this->Netspresso->getNextAction($heartbeat['box']['state']);
		
		// set the last 
		$this->Netspresso->saveField('last', date("Y-m-d H:i:s"));

		// Create the response object
		$response = $this->create_response_object_reply($action['code'], $action['message']);

		// Serialize the response object
		//
		$this->set(array('response' => $response));
		$this->set('_serialize', array('response'));

	} // end of heartbeat

} // end of class
