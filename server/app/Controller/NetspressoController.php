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
	public $uses = array('Metric');

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
 * metric method
 *
 * @return void
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
			if ($this->Metric->save($this->request->data)) {

			} else {
	         	throw new InternalErrorException(__('The metric could not be saved. Please, try again.'));	
			}

		} catch(Exception $e) {

			// TODO : Verify for duplicate entries as the client keep trying
			// instead of throwing an error

			$this->log("NetspressoController::metric caught exception: " .   $e->getMessage());	
			$this->log("NetspressoController::metric input data: " . var_export($this->request->data,true));	

		}

		// Create response object reply
		//
		$response = $this->create_response_object_reply('200', 'OK');

		// Serialize the response object
		//
		$this->set(array('response' => $response));
		$this->set('_serialize', array('response'));
		
		
	} // end of metric

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
							'response' => array('reply_code' => $code,
												'reply_message' => $message,),);
 		return $response;

	} // end of create_response_object_reply


} // end of class
