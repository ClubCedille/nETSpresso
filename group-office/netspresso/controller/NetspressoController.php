<?php

namespace GO\Netspresso\Controller;

require_once 'HTTP/Request2.php';

class NetspressoController extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO\Netspresso\Model\Netspresso';

// 	/**
// 	 * Tell the controller to change some column values
// 	 */
// 	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
// 		//$columnModel->formatColumn('user_id','$model->users->first_name');
// 		//$columnModel->formatColumn('calendar_id','$model->calendar->name');
// 		//$columnModel->formatColumn('resource_event_id','$model->resources->calendar_id');
// 		return parent::formatColumns($columnModel);
// 	}

// 	/**
// 	 * Display corrent value in combobox
// 	 */
// 	protected function remoteComboFields(){
// 		return array('party_id'=>'$model->party->name');
// 	}
// 	
// 	protected function afterDisplay(&$response, &$model, &$params) {
// 		$response['data']['write_permission'] = true;
// 		$response['data']['permission_level'] = GO_Base_Model_Acl::MANAGE_PERMISSION;
// 		$response['data']['partyName'] = $model->party->name;
// 		return parent::beforeDisplay($response, $model, $params);
// 	}

 	protected function actionConfiguration($params) {

GO::debug("NetspressoController::display function called !");

//return '{"date":"2014-08-25T18:45:22-0400","status":"Stand-By","temperature":"30"}';
//return "{\"success\":true,\"results\":[{\"date\":\"2014-08-25T18:45:22-0400\",\"status\":\"Stand-By\",\"temperature\":30}],\"total\":1}";
//return self::getStatus($params);

return array ('success' => 'true', 'results' => array('date' => '2014-08-25T18:45:22-0400', 'status' => 'Stand-By', 'temperature' => 30), 'total' => 1);

 	}


	private static function getStatus($params) {

		//GO::debug("::getStatus (" . var_export($data, true) . ")");

		// Set the destination URL
		$URL = 'http://netspresso.cedille.club/go/status.json?box=netspresso01';

		//create the http request object
		$request = new HTTP_Request2($URL, HTTP_Request2::METHOD_GET);

		//add the headers
		$headers = array('Content-Type' => 'application/json',
						 'X-Auth-Token' => 'tr9D96HJtlcH');
		$request->setHeader($headers);

		//add the json encoded data
		$request->setBody(json_encode($params));
		
		//send the http request
		try {
			$response = $request->send();
			
    		if (200 == $response->getStatus()) {
        		GO::debug("::getStatus response : " . $response->getBody());
        		return $response->getBody();

    		} else {    	
    			GO::debug("::getStatus Unexpected response: " . $response->getStatus() . ' ' . $response->getReasonPhrase());
    		}
		} catch (HTTP_Request2_Exception $e) {
    		GO::debug("::getStatus Error: " . $e->getMessage() );
		}
		
		//debug the result
		//GO::debug("::getStatus HTTP response status => " . $result->status() );
		//GO::debug("::getStatus HTTP request body => " . $result->getRequestBody() );

	}



}
