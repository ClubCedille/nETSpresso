<?php

/**
 * The Netspresso model
 * 
 * @property int $id
 * @property int $party_id
 * @property string $firstname
 * @property string $lastame
 * @property date $tookoffice
 * @property date $leftoffice
 * @property float $income
 */
class GO_Netspresso_Model_Netspresso extends GO_Base_Db_ActiveRecord {
	
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
		//$this->columns["income"]["gotype"]="number";

		//$this->columns['calendar_id']['required']=true;
		$this->columns['start_time']['gotype'] = 'unixtimestamp';
		$this->columns['end_time']['greater'] = 'start_time';
		$this->columns['end_time']['gotype'] = 'unixtimestamp';
		//$this->columns['repeat_end_time']['gotype'] = 'unixtimestamp';		
		//$this->columns['repeat_end_time']['greater'] = 'start_time';
		//$this->columns['category_id']['required'] = GO_Calendar_CalendarModule::commentsRequired();
		//parent::init();

		return parent::init();
	}
	
	public function tableName(){
		//return 'cal_events';
		return 'netspresso_events';
	}
	
// 	public function customfieldsModel(){
// 		return "GO_Calendar_Customfields_Model_Event";
// 	}
// 
	public function hasFiles() {
		return false;
	}
	
	public function hasLinks() {
		return false;
	}
// 	
// 	public function getFullname()
// 	{
// 		return $this->firstname . " " . $this->lastname;
// 	}
// 	
// 	protected function getCacheAttributes() {
// 		//return array("name"=>$this->fullname, "description"=>$this->party->name);
// 		
// 		$calendarName = empty($this->calendar) ? '' : ', '.$this->calendar->name;
// 		return array(
// 				'name' => $this->private ?  GO::t('privateEvent','calendar') : $this->name.' ('.GO_Base_Util_Date::get_timestamp($this->start_time, false).$calendarName.')',
// 				'description' => $this->private ?  "" : $this->description,
// 				'mtime'=>$this->start_time
// 		);	
// 	}
// 	
// 	protected function getLocalizedName() {
// 		return GO::t('event', 'calendar');
// 	}
// 
// 	public function relations(){
// 	
// 		return array(
// 				'_exceptionEvent'=>array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'exception_for_event_id'),
// 				'recurringEventException'=>array('type' => self::HAS_ONE, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'exception_event_id'),//If this event is an exception for a recurring series. This relation points to the exception of the recurring series.
// 				'calendar' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Calendar', 'field' => 'calendar_id'),
// 				'category' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Category', 'field' => 'category_id'),
// 				'participants' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Participant', 'field' => 'event_id', 'delete' => true),
// 				'exceptions' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Exception', 'field' => 'event_id', 'delete' => true),
// 				'exceptionEvents' => array('type' => self::HAS_MANY, 'model' => 'GO_Calendar_Model_Event', 'field' => 'exception_for_event_id', 'delete' => true),
// 				'resources' => array('type' => self::BELONGS_TO, 'model' => 'GO_Calendar_Model_Event', 'field' => 'resource_event_id'),//, 'delete' => true),
// 				'users' => array('type' => self::BELONGS_TO, 'model' => 'GO_Base_Model_User', 'field' => 'user_id'),
// 		);
// 	}

}