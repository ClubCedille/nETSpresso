<?php
App::uses('AppModel', 'Model');
/**
 * Netspresso Model
 *
 */
class Netspresso extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'boxes';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	
/**
 * method setEventTime
 *
 * @throws none
 * @param  string $time
 * @return none
 */
	public function setEventTime($time)
	{
		$this->set('ev_time', $time);

// 		try {
// 			// Save the new state
// 			$this->saveField('ev_time', $time);
// 
// 		} catch(Exception $e) { 
// 				$this->log("NetspressoModel::setEventTime caught exception: " . $e->getMessage());
// 				$this->log("NetspressoModel::setEventTime input data: " . var_export($state, true));
// 		}
	} // end of setEventTime	

/**
 * method getEventTime
 *
 * @throws none
 * @param  none
 * @return string $time
 */

	public function getEventTime()
	{
		return $this->get('ev_time');
	}

/**
 * method setEventState
 *
 * @throws none
 * @param  string $state
 * @return none
 */
	public function setEventState($state)
	{
		// Validate current state
		if (! $this->validateState($state)) {
			throw new BadRequestException(__("The submited state (" . $state . ") is not valid"));
		}

		$this->set('ev_state', $state);

// 		try {
// 			// Save the new state
// 			$this->saveField('ev_state', $state);
// 
// 		} catch(Exception $e) { 
// 				$this->log("NetspressoModel::setEventState caught exception: " . $e->getMessage());
// 				$this->log("NetspressoModel::setEventState input data: " . var_export($state, true));
// 		}
	} // end of setEventState
	
	
/**
 * method getEventState
 *
 * @throws none
 * @param  none
 * @return string $state
 */

	public function getEventState()
	{
		return $this->get('ev_state');
	}

	
/**
 * method setHeartbeatTime
 *
 * @throws none
 * @param  string $time
 * @return none
 */
	public function setHeartbeatTime($time)
	{
		$this->set('hb_time', $time);

// 		try {
// 			// Save the new state
// 			$this->saveField('hb_time', $time);
// 
// 		} catch(Exception $e) { 
// 				$this->log("NetspressoModel::setHeartbeatTime caught exception: " . $e->getMessage());
// 				$this->log("NetspressoModel::setHeartbeatTime input data: " . var_export($state, true));
// 		}
	} // end of setHeartbeatTime	

/**
 * method setHeartbeatState
 *
 * @throws none
 * @param  string $state
 * @return none
 */
	public function setHeartbeatState($state)
	{
		// Validate heartbeat state
		if (! $this->validateState($state)) {
			throw new BadRequestException(__("The submited state (" . $state . ") is not valid"));
		}
		
		$this->set('hb_state', $state);

// 		try {
// 			// Save the new state
// 			$this->saveField('hb_state', $state);
// 
// 		} catch(Exception $e) { 
// 				$this->log("NetspressoModel::setHeartbeatState caught exception: " . $e->getMessage());
// 				$this->log("NetspressoModel::setHeartbeatState input data: " . var_export($state, true));
// 		}
	} // end of setHeartbeatState	

/**
 * method getHeartbeatState
 *
 * @throws none
 * @param  none
 * @return string $state
 */
	public function getHeartbeatState()
	{		
		return $this->get('hb_state');
	} // end of getHeartbeatState	


/**
 * method setHeartbeatTemperature
 *
 * @throws none
 * @param  string $temperature
 * @return none
 */
	public function setHeartbeatTemperature($temperature)
	{		
		$this->set('hb_temp', $temperature);
	} // end of setHeartbeatTemperature	

/**
 * method getHeartbeatTemperature
 *
 * @throws none
 * @param  none
 * @return string $temperature
 */
	public function getHeartbeatTemperature()
	{		
		return $this->get('hb_temp');
	} // end of getHeartbeatTemperature	


/**
 * method validateState
 *
 * @throws none
 * @param  string $state
 * @return boolean
 */
	public function validateState($state)
	{

		$states = array ('Stand-By', 'Locked', 'Warming-Up', 'Ready', 'Cooling-Down', 'Network-Error');

		return in_array($state , $states);
	}

/**
 * method loadByMac
 *
 * @throws none
 * @param  string $mac
 * @return none
 */
	public function loadByMac($mac = null)
	{

		// For debug
		//$this->log("NetspressoModel::loadByMac input data: " . var_export($mac, true));	

		// Try to find an existing object
		$found = $this->findByMac(strtoupper($mac));
		if (!empty($found) and !empty($found['Netspresso']))
		{
			// For debug
			//$this->log("NetspressoModel::loadByMac findByMac : " . var_export($found, true));
			$this->set($found['Netspresso']);
			return;
		}

		// Otherwise create a new one		
		try {
				// Save the model with current data
				$data = array ('mac' => strtoupper($mac), 'state' => 'Stand-By', 'name' => 'Unknown');
				$this->save($data);
				$this->read();
		} catch(Exception $e) { 
				$this->log("NetspressoModel::loadByMac caught exception: " . $e->getMessage());
				$this->log("NetspressoModel::loadByMac input data: " . var_export($mac, true));
		}

		// For debug
		//$this->log("NetspressoModel::loadByMac data set: " . var_export($this->data, true));

		return;
	} // end of loadByMac


/**
 * method getNextAction
 *
 * @throws none
 * @param  string $state
 * @return none
 */
	public function getNextAction($state)
	{

		// For debug
		$this->log("NetspressoModel::getNextAction current state: " . $state);
		$this->log("NetspressoModel::getNextAction next state: " . $this->getEventState());

		// Actions are build based on the current state
		$actions = array (	'Stand-By' => array (
												'Stand-By'		=> 'Ok',
												'Locked'		=> 'Override',
												'Warming-Up'	=> 'Cold-Down',
												'Ready'			=> 'Cold-Down',
												'Cooling-Down'	=> 'Ok',
												),
							'Locked' => array	(
												'Stand-By'		=> 'Lock-Down',
												'Locked'		=> 'Ok',
												'Warming-Up'	=> 'Lock-Down',
												'Ready'			=> 'Lock-Down',
												'Cooling-Down'	=> 'Lock-Down',
												),
							'Warming-Up' => array (
												'Stand-By'		=> 'Warm-Up',
												'Locked'		=> 'Override',
												'Warming-Up'	=> 'Ok',
												'Ready'			=> 'Ok',
												'Cooling-Down'	=> 'Warm-Up',
												),
							'Ready' => array	(
												'Stand-By'		=> 'Warm-Up',
												'Locked'		=> 'Override',
												'Warming-Up'	=> 'Ok',
												'Ready'			=> 'Ok',
												'Cooling-Down'	=> 'Warm-Up',
												),
							'Cooling-Down' => array (
												'Stand-By'		=> 'Ok',
												'Locked'		=> 'Override',
												'Warming-Up'	=> 'Cold-Down',
												'Ready'			=> 'Cold-Down',
												'Cooling-Down'	=> 'Ok',
												)
						);

		$codes = array (	'Ok'		=> '000',
							'Warm-Up'	=> '001',
							'Cold-Down' => '002',
							'Lock-Down' => '003',
							'Override'	=> '004',
						);

		// For debug
		//$this->log("NetspressoModel::getNextAction actions: " . var_export($actions[$this->getEventState()], true));
		// Override in case of network error oterwise evaluate the nex action
		if ($state === 'Network-Error'){
			$action = 'Override';
		} else {
			$action = $actions[$this->getEventState()][$state];
		}
		$code = $codes[$action];

		// For debug
		$this->log("NetspressoModel::getNextAction output " . $code . " - " . $action);

		return array('code' => $code, 'message' => $action);

	} // end of getNextAction




/**
 * method loadByName
 *
 * @throws none
 * @param  string $name, boolean $new
 * @return boolean
 */
	public function loadByName($name = null, $new = false)
	{

		// For debug
		//$this->log("NetspressoModel::loadByName input data: " . var_export($name, true));	

		// Try to find an existing object
		$found = $this->findByName(strtolower($name));
		if (!empty($found) and !empty($found['Netspresso']))
		{
			// For debug
			//$this->log("NetspressoModel::loadByName findByName : " . var_export($found, true));
			$this->set($found['Netspresso']);
			return true;
		}
		
		// do not create new unless asked
		if (!$new) return false;

		// Otherwise create a new one		
		try {
				// Save the model with current data
				$data = array ('mac' => 'FF:FF:FF:FF:FF:FF', 'state' => 'Stand-By', 'name' => $name);
				$this->save($data);
				$this->read();
		} catch(Exception $e) { 
				$this->log("NetspressoModel::loadByName caught exception: " . $e->getMessage());
				$this->log("NetspressoModel::loadByName input data: " . var_export($name, true));
				return false;
		}

		// For debug
		//$this->log("NetspressoModel::loadByName data set: " . var_export($this->data, true));

		return true;
	} // end of loadByName

/**
 * method evaluateEventState
 *
 * @throws none
 * @param  array $data
 * @return none
 */
	public function evaluateEventState($data = null)
	{
		// For debug
		//$this->log("NetspressoModel::evaluateEventState input data: " . var_export($data, true));	
		//$this->log("NetspressoModel::evaluateEventState current data: " . var_export($this->data, true));

		// Compute the elapsed time since last call
		$elapsed = strtotime("now") - strtotime($this->getEventTime());
		$this->log("NetspressoModel::evaluateEventState elapsed time: " . round($elapsed / 60) . " minutes" );
		
		// Adjust event 'Ready' state to 'Cooling-Down' after 2 minutes ~ 120 secondes
		//
		if ($this->getEventState() === 'Ready' and 
		(strtotime("now") - strtotime($this->getEventTime()) > 120))
		{
			$this->setEventState('Cooling-Down');
			$this->setEventTime(date("Y-m-d H:i:s"));
		}
		
		// Adjust event 'Cooling-Down' state to 'Stand-By' after 3 minutes ~ 180 secondes
		//		
		if ($this->getEventState() === 'Cooling-Down' and 
		(strtotime("now") - strtotime($this->getEventTime()) > 180))
		{
			$this->setEventState('Stand-By');
			$this->setEventTime(date("Y-m-d H:i:s"));
		}
	
		//$this->log("NetspressoModel::evaluateEventState modified data: " . var_export($this->data, true));
		return;

	} // end of evaluateEventState

	
} // End of class
