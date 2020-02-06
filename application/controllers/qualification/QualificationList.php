<?php

/**
 * QualificationListController
 * @author takanori_gozu
 *
 */
class QualificationList extends MY_Controller {
	
	/**
	 * Index
	 */
	public function index() {
		
		$this->load->model('qualification/QualificationListModel', 'model');
		$this->load->library('dao/QualificationDao');
		
		$this->set('list', $this->model->get_list());
		$this->set('list_col', $this->model->get_list_col());
		$this->set('link', $this->model->get_link());
		
		$this->set('class_key', 'qualification');
		$this->set('class_path', 'qualification/Qualification');
		
		$this->view('qualification/qualification_list');
	}
	
	/**
	 * 検索
	 */
	public function search() {
		
		$this->load->model('qualification/QualificationListModel', 'model');
		$this->load->library('dao/QualificationDao');
		
		$search = $this->get_attribute();
		
		$this->set('list', $this->model->get_list($search));
		$this->set('list_col', $this->model->get_list_col());
		$this->set('link', $this->model->get_link());
		
		$this->set('class_key', 'qualification');
		$this->set('class_path', 'qualification/Qualification');
		
		$this->view('qualification/qualification_list');
	}
}
?>