<?php
App::uses('AppModel', 'Model');
/**
 * Metric Model
 *
 * @property Type $Type
 * @property Source $Source
 */
class Metric extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'path';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Type' => array(
			'className' => 'MetricsType',
			'foreignKey' => 'type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Source' => array(
			'className' => 'MetricsSource',
			'foreignKey' => 'source_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
