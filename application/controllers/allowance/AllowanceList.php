<?php

/**
 * AllowanceListController
 * @author takanori_gozu
 *
 */
class AllowanceList extends MY_Controller {
	
	/**
	 * Index
	 */
	public function index() {
		
		$this->load->model('allowance/AllowanceListModel', 'model');
		$this->load->library('dao/EmployeeDao');
		$this->load->library('dao/QualificationDao');
		$this->load->library('dao/QualificationAllowanceDao');
		
		$this->set('list', $this->model->get_list());
		$this->set('list_col', $this->model->get_list_col());
		$this->set('link', $this->model->get_link());
		
		$this->set('employee_map', $this->model->get_employee_map());
		$this->set('qualification_map', $this->model->get_qualification_map());
		
		$this->set('class_key', 'allowance');
		$this->set('class_path', 'allowance/Allowance');
		
		$this->view('allowance/allowance_list');
	}
	
	/**
	 * 検索
	 */
	public function search() {
		
		$this->load->model('allowance/AllowanceListModel', 'model');
		$this->load->library('dao/EmployeeDao');
		$this->load->library('dao/QualificationDao');
		$this->load->library('dao/QualificationAllowanceDao');
		
		$search = $this->get_attribute();
		
		$this->set('list', $this->model->get_list($search));
		$this->set('list_col', $this->model->get_list_col());
		$this->set('link', $this->model->get_link());
		
		$this->set('employee_map', $this->model->get_employee_map());
		$this->set('qualification_map', $this->model->get_qualification_map());
		
		$this->set('class_key', 'allowance');
		$this->set('class_path', 'allowance/Allowance');
		
		$this->view('allowance/allowance_list');
	}
}
?>