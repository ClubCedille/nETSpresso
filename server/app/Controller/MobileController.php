<?php
App::uses('AppController', 'Controller');
/**
 * Mobile Controller
 *
 */
class MobileController extends AppController {

/**
 * Scaffold
 *
 * @var mixed
 */
	public $scaffold;

/**
 * Models
 *
 * @var array
 */
	public $uses = array('Netspresso');


/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->layout = 'mobile';
	}


/**
 * status method
 *
 * @throws none
 * @param  none
 * @return void
 */
// http://netspresso.cedille.club/mobile/status.json?box=netspresso01

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
