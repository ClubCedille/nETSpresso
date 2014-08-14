<?php
class GO_Netspresso_Controller_Netspresso extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Netspresso_Model_Netspresso';

	/**
	 * Tell the controller to change some column values
	 */
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		//$columnModel->formatColumn('user_id','$model->users->first_name');
		//$columnModel->formatColumn('calendar_id','$model->calendar->name');
		//$columnModel->formatColumn('resource_event_id','$model->resources->calendar_id');
		return parent::formatColumns($columnModel);
	}

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

}
