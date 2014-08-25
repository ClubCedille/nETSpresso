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
 * Current state
 *
 * @var string
 */
//	private $state;
	
/**
 * method getCurrentState
 *
 * @throws none
 * @param  none
 * @return string $state
 */

	public function getFinalState()
	{
		return $this->state;
	}

/**
 * method setCurrentState
 *
 * @throws none
 * @param  string $state
 * @return none
 */
	public function setFinalState($state)
	{
		$this->state = $state;
	}

/**
 * method validateState
 *
 * @throws none
 * @param  string $state
 * @return boolean
 */
	public function validateState($state)
	{

		$states = array ('Stand-by', 'Locked', 'Warming-Up', 'Ready', 'Cooling-Down');

		return in_array($state , $states);
	}

/**
 * method load
 *
 * @throws none
 * @param  string $mac
 * @return none
 */
	public function load($mac)
	{

		// For debug
		//$this->log("NetspressoModel::load input data: " . var_export($mac, true));	

		// Try to find an existing object
		$found = $this->findByMac(strtoupper($mac));
		if (!empty($found) and !empty($found['Netspresso']))
		{
			// For debug
			//$this->log("NetspressoModel::load findByMac : " . var_export($found, true));
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
				$this->log("NetspressoModel::load caught exception: " . $e->getMessage());
				$this->log("NetspressoModel::load input data: " . var_export($mac, true));
		}

		// For debug
		//$this->log("NetspressoModel::load data set: " . var_export($this->data, true));

		return;
	} // end of load


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
//$this->log("NetspressoModel::getNextAction current state: " . $state);
//$this->log("NetspressoModel::getNextAction next state: " . $this->get('state') );

		// Actions are build based on the current state
		$actions = array (	'Stand-By' => array (
												'Stand-by'		=> 'Ok',
												'Locked'		=> 'Override',
												'Warming-Up'	=> 'Cold-Down',
												'Ready'			=> 'Cold-Down',
												'Cooling-Down'	=> 'Ok',
												),
							'Locked' => array	(
												'Stand-by'		=> 'Lock-Down',
												'Locked'		=> 'Ok',
												'Warming-Up'	=> 'Lock-Down',
												'Ready'			=> 'Lock-Down',
												'Cooling-Down'	=> 'Lock-Down',
												),
//							'Warming-Up' => array	(
//												'Stand-by'		=> 'Warm-Up',
//												'Locked'		=> 'Override',
//												'Warming-Up'	=> 'Ok',
//												'Ready'			=> 'Ok',
//												'Cooling-Down'	=> 'Warm-Up',
//												),
							'Ready' => array	(
												'Stand-by'		=> 'Warm-Up',
												'Locked'		=> 'Override',
												'Warming-Up'	=> 'Ok',
												'Ready'			=> 'Ok',
												'Cooling-Down'	=> 'Warm-Up',
												),
//							'Cooling-Down' => array (
//												'Stand-by'		=> 'Ok',
//												'Locked'		=> 'Override',
//												'Warming-Up'	=> 'Cold-Down',
//												'Ready'			=> 'Cold-Down',
//												'Cooling-Down'	=> 'Ok',
//												)
						);

		$codes = array (	'Ok'		=> '000',
							'Warm-Up'	=> '001',
							'Cold-Down' => '002',
							'Lock-Down' => '003',
							'Override'	=> '004',
						);

// For debug
//$this->log("NetspressoModel::getNextAction actions: " . var_export($actions[$this->get('state')], true));

		$action = $actions[$this->get('state')][$state];
		$code = $codes[$action];

// For debug
//$this->log("NetspressoModel::getNextAction output " . $code . " - " . $action);

		return array('code' => $code, 'message' => $action);

	} // end of getNextAction
	
} // End of class
