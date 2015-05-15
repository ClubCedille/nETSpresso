<?php

/**
 * The Netspresso model
 * 
 */

namespace GO\Netspresso\Model;

class Netspresso extends \GO\Base\Db\ActiveRecord {
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Netspresso_Model_Netspresso 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {

		$this->columns['start_time']['gotype'] = 'unixtimestamp';
		$this->columns['end_time']['greater'] = 'start_time';
		$this->columns['end_time']['gotype'] = 'unixtimestamp';

		return parent::init();
	}
	
	public function tableName(){
		return 'netspresso_events';
	}
	
// 	public function customfieldsModel(){
// 		return "GO_Calendar_Customfields_Model_Event";
// 	}

	public function hasFiles() {
		return false;
	}
	
	public function hasLinks() {
		return false;
	}


}