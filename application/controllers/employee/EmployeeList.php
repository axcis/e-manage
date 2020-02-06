<?php

/**
 * EmployeeListController
 * @author takanori_gozu
 *
 */
class EmployeeList extends MY_Controller {
	
	/**
	 * Index
	 */
	public function index() {
		
		$this->load->model('employee/EmployeeListModel', 'model');
		$this->load->library('dao/EmployeeDao');
		
		$this->set('list', $this->model->get_list());
		$this->set('list_col', $this->model->get_list_col());
		$this->set('link', $this->model->get_link());
		
		$this->set('class_key', 'employee');
		$this->set('class_path', 'employee/Employee');
		
		$this->view('employee/employee_list');
	}
	
	/**
	 * 検索
	 */
	public function search() {
		
		$this->load->model('employee/EmployeeListModel', 'model');
		$this->load->library('dao/EmployeeDao');
		
		$search = $this->get_attribute();
		
		$this->set('list', $this->model->get_list($search));
		$this->set('list_col', $this->model->get_list_col());
		$this->set('link', $this->model->get_link());
		
		//チェックボックス
		if (isset($search['retirement_show']) && $search['retirement_show'] != '') {
			$this->set('retirement_show_checked', array(1));
		}
		
		$this->set('class_key', 'employee');
		$this->set('class_path', 'employee/Employee');
		
		$this->view('employee/employee_list');
	}
}
?>