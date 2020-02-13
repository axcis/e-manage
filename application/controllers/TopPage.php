<?php

/**
 * TopPageController
 * @author takanori_gozu
 *
 */
class TopPage extends MY_Controller {
	
	/**
	 * Index
	 */
	public function index() {
		
		$this->load->model('top/TopPageModel', 'model');
		$this->load->library('dao/EmployeeDao');
		$this->load->library('dao/EmployeeStatusDao');
		$this->load->library('dao/QualificationDao');
		$this->load->library('dao/QualificationAllowanceDao');
		$this->load->library('dao/VacationDateDao');
		$this->load->library('dao/VacationMasterDao');
		
		$this->set('class_key', 'top');
		
		$month = date('Y-m');
		$employee_map = $this->model->get_employee_map();
		
		//各List取得
		$this->set('hire_list', $this->model->get_hire_list($month));
		$this->set('retirement_list', $this->model->get_retirement_list($month));
		$this->set('halr_year_employee_list', $this->model->get_half_year_employee_list($month));
		$this->set('one_year_employee_list', $this->model->get_one_year_employee_list($month));
		$this->set('contract_list', $this->model->get_contract_list($employee_map, $month));
		$this->set('regular_list', $this->model->get_regular_list($employee_map, $month));
		$this->set('vacation_date_list', $this->model->get_vacation_date_list($employee_map, $month));
		$this->set('allowance_list', $this->model->get_allowance_list($employee_map, $month));
		
		//各項目
		$this->set('hire_list_col', $this->model->get_hire_list_col());
		$this->set('retirement_list_col', $this->model->get_retirement_list_col());
		$this->set('halr_year_employee_list_col', $this->model->get_halr_year_employee_list_col());
		$this->set('one_year_employee_list_col', $this->model->get_one_year_employee_list_col());
		$this->set('contract_list_col', $this->model->get_contract_list_col());
		$this->set('regular_list_col', $this->model->get_regular_list_col());
		$this->set('vacation_date_list_col', $this->model->get_vacation_date_list_col());
		$this->set('allowance_list_col', $this->model->get_allowance_list_col());
		
		$this->set('search_month', $month);
		$this->set('month_map', $this->model->get_month_map());
		
		$this->view('top/top_page');
	}
	
	/**
	 * 検索
	 */
	public function search() {
		
		$this->load->model('top/TopPageModel', 'model');
		$this->load->library('dao/EmployeeDao');
		$this->load->library('dao/EmployeeStatusDao');
		$this->load->library('dao/QualificationDao');
		$this->load->library('dao/QualificationAllowanceDao');
		$this->load->library('dao/VacationDateDao');
		$this->load->library('dao/VacationMasterDao');
		
		$this->set('class_key', 'top');
		
		$month = $this->get('search_month');
		$employee_map = $this->model->get_employee_map();
		
		//各List取得
		$this->set('hire_list', $this->model->get_hire_list($month));
		$this->set('retirement_list', $this->model->get_retirement_list($month));
		$this->set('halr_year_employee_list', $this->model->get_half_year_employee_list($month));
		$this->set('one_year_employee_list', $this->model->get_one_year_employee_list($month));
		$this->set('contract_list', $this->model->get_contract_list($employee_map, $month));
		$this->set('regular_list', $this->model->get_regular_list($employee_map, $month));
		$this->set('vacation_date_list', $this->model->get_vacation_date_list($employee_map, $month));
		$this->set('allowance_list', $this->model->get_allowance_list($employee_map, $month));
		
		//各項目
		$this->set('hire_list_col', $this->model->get_hire_list_col());
		$this->set('retirement_list_col', $this->model->get_retirement_list_col());
		$this->set('halr_year_employee_list_col', $this->model->get_halr_year_employee_list_col());
		$this->set('one_year_employee_list_col', $this->model->get_one_year_employee_list_col());
		$this->set('contract_list_col', $this->model->get_contract_list_col());
		$this->set('regular_list_col', $this->model->get_regular_list_col());
		$this->set('vacation_date_list_col', $this->model->get_vacation_date_list_col());
		$this->set('allowance_list_col', $this->model->get_allowance_list_col());
		
		$this->set('search_month', $month);
		$this->set('month_map', $this->model->get_month_map());
		
		$this->view('top/top_page');
	}
}
?>