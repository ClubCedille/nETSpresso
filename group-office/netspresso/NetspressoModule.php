<?php

namespace GO\Netspresso;

class NetspressoModule extends \GO\Base\Module{

	public function autoInstall() {
			return true;
	}
	
	public function package() {
		return self::PACKAGE_CUSTOM;
	}

	public function author() {
			return 'Club Cedille - ÉTS';
	}

	public function authorEmail() {
			return 'cedille@etsmtl.ca';
	}

	public static function initListeners() {
	
		// Every model that is derived from GO_Base_Db_ActiveRecord has a "save" and "delete" event. 
		// They listeners will be called with the model as the first argument.
		// attaching to the GO_Calendar_Model_Event model
		//\GO\Calendar\Model\Event::model()->addListener('save', 'GO\Netspresso\NetspressoModule', 'save');
		//\GO\Calendar\Model\Event::model()->addListener('delete', 'GO\Netspresso\NetspressoModule', 'delete');

		// Every controller that is derived from GO_Base_Controller_AbstractModelController 
		// has a "submit", "load", "display" and "delete" event. 
		// They are all called with these parameters: $controller, $response,$model,$params,$modifiedAttributes
		// attaching to a controller works a bit different
		//$c = new GO_Calendar_Controller_Event();
		//$c->addListener('submit',  'GO\Netspresso\NetspressoModule', 'submit');
		//$c->addListener('load',    'GO\Netspresso\NetspressoModule', 'load');
		//$c->addListener('store',   'GO\Netspresso\NetspressoModule', 'store');
		//$c->addListener('display', 'GO\Netspresso\NetspressoModule', 'display');
	}


	public static function save(&$model, $wasNew){
		//do something with the GO_Calendar_Model_Event module here
// 		\GO::debug("Netspresso: saving an event !");
//
// 		\GO::debug("Netspresso: export => " . var_export(get_object_vars($model), true));	
// 		$myClassReflection = new ReflectionClass(get_class($model));
//      $secret = $myClassReflection->getProperty('_attributes');
//      $secret->setAccessible(true);	
// 		\GO::debug("Netspresso: export => " . var_export($secret->getValue($model), true));

		if (! $model->isResource()) {
// 			\GO::debug("Netspresso: It's not an resource event !");
			return;
		}

		if ($model->calendar->id !== \GO\Netspresso\Model\NetspressoConfig::getResourceId()) {
// 			\GO::debug("Netspresso: Calendar Name => " . $model->calendar->name);
// 			\GO::debug("Netspresso: It's not an nÉTSpresso resource event !");
			return;
		}
		
//		if ($model->calendar->name !== 'nÉTSpresso') {
// 			\GO::debug("Netspresso: Calendar Name => " . $model->calendar->name);
// 			\GO::debug("Netspresso: It's not an nÉTSpresso resource event !");
//			return;
//		}
		
 		if ($model->status !== 'NEEDS-ACTION') {
// 			\GO::debug("Netspresso: Status => " . $model->status);
// 			\GO::debug("Netspresso: This resource event doesn't need action!");
			return;
		}

		// If resource is nÉTSpresso, automatically set it to CONFIRMED. 
//  		$model->status = 'CONFIRMED';   // \GO\Calendar\Model\Event::STATUS_CONFIRMED
//  		$model->save(true);

		// nothing else to do
		return;

	}

	public static function delete(&$model){
		//do something with the GO_Calendar_Model_Event module here
		//\GO::debug("Netspresso: deleting an event !");
	}

	public static function submit(&$controller, &$response, &$model, &$params, $modifiedAttributes){
		//do something with the GO_Calendar_Controller_Event module here
		//\GO::debug("Netspresso: submiting an event !");
	}

	public static function load(&$controller, &$response, &$model, &$params){
		//do something with the GO_Calendar_Controller_Event module here
		//\GO::debug("Netspresso: loading an event !");
	}

	public static function store(&$controller, &$response, &$model, &$params){
		//do something with the GO_Calendar_Controller_Event module here
		//\GO::debug("Netspresso: storing an event !");
	}

	public static function display(&$controller, &$response, &$model){
		//do something with the GO_Calendar_Controller_Event module here
		//\GO::debug("Netspresso: displaying an event !");
	}

}

?>

