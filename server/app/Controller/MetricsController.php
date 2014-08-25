<?php
App::uses('AppController', 'Controller');
/**
 * Metrics Controller
 *
 * @property Metric $Metric
 * @property PaginatorComponent $Paginator
 */
class MetricsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Metric->recursive = 0;
		$this->set('metrics', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Metric->exists($id)) {
			throw new NotFoundException(__('Invalid metric'));
		}
		$options = array('conditions' => array('Metric.' . $this->Metric->primaryKey => $id));
		$this->set('metric', $this->Metric->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Metric->create();
			if ($this->Metric->save($this->request->data)) {
				return $this->flash(__('The metric has been saved.'), array('action' => 'index'));
			}
		}
		$types = $this->Metric->Type->find('list');
		$sources = $this->Metric->Source->find('list');
		$this->set(compact('types', 'sources'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Metric->exists($id)) {
			throw new NotFoundException(__('Invalid metric'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Metric->save($this->request->data)) {
				return $this->flash(__('The metric has been saved.'), array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('Metric.' . $this->Metric->primaryKey => $id));
			$this->request->data = $this->Metric->find('first', $options);
		}
		$types = $this->Metric->Type->find('list');
		$sources = $this->Metric->Source->find('list');
		$this->set(compact('types', 'sources'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Metric->id = $id;
		if (!$this->Metric->exists()) {
			throw new NotFoundException(__('Invalid metric'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Metric->delete()) {
			return $this->flash(__('The metric has been deleted.'), array('action' => 'index'));
		} else {
			return $this->flash(__('The metric could not be deleted. Please, try again.'), array('action' => 'index'));
		}
	}
}
