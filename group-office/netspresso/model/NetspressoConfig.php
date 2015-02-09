<?php

/**
 * The NetspressoConfig model
 * 
 */

namespace GO\Netspresso\Model;

class NetspressoConfig extends \GO\Base\Db\ActiveRecord {
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO\Netspresso\Model\NetspressoConfig 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	protected function init() {
		//$this->columns["income"]["gotype"]="number";
		//$this->columns['calendar_id']['required']=true;
		//$this->columns['start_time']['gotype'] = 'unixtimestamp';
		//$this->columns['end_time']['greater'] = 'start_time';
		//$this->columns['end_time']['gotype'] = 'unixtimestamp';

		return parent::init();
	}
	
	public function tableName(){
		return 'netspresso_config';
	}
	
	public static function getReadyBefore() {	

		$config = self::model()->findByPk(1);
		//\GO::debug("NetspressoConfig::getReadyBefore(" . var_export($config->ready_before, true) . ")");
		return $config->ready_before;
	}
	
	public static function getStdbyAfter() {		

		$config = self::model()->findByPk(1);
		//\GO::debug("NetspressoConfig::getStdbyAfter(" . var_export($config->stdby_after, true) . ")");
		return $config->stdby_after;
	}
	
	public static function getResourceId() {

		$config = self::model()->findByPk(1);
		//\GO::debug("NetspressoConfig::getResourceId(" . var_export($config->resource_id, true) . ")");
		return $config->resource_id;
	}

// 	private static function get_resource()
// 	{
// 		$sql = "SELECT resource_id FROM netspresso_config;";
// 	
// 		//\GO::debug("NetspressoConfig::get_resource (" . var_export($sql, true) . ")");
// 
// 		$stmt = \GO::getDbConnection()->query($sql);	
// 		$resource = $stmt->fetch();
// 		
// 		$model = self::model()->findByPk(1);
// 		\GO::debug("NetspressoConfig::get_resource (" . var_export($model->resource_id, true) . ")");
// 		
// 		return $resource[0];
// 	}	
	
	

}