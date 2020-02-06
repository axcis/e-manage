<?php

/**
 * EmployeeRegistController
 * @author takanori_gozu
 *
 */
class EmployeeRegist extends MY_Controller {
	
	public function regist_input() {
		
		$this->load->model('employee/EmployeeRegistModel', 'model');
		
		$this->set('action', 'regist');
		$this->set('class_path', 'employee/Employee');
		$this->set('trial_map', $this->model->get_trial_map());
		
		$this->view('employee/employee_input');
	}
	
	/**
	 * 新規登録
	 */
	public function regist() {
		
		$this->load->model('employee/EmployeeRegistModel', 'model');
		$this->load->library('dao/EmployeeDao');
		$this->load->library('dao/EmployeeStatusDao');
		$this->load->library('dao/VacationDateDao');
		
		$input = $this->get_attribute();
		
		$msgs = $this->model->validation($input);
		
		if ($msgs != null) {
			$this->set_err_info($msgs);
			$this->set('trial_map', $this->model->get_trial_map());
			$this->view('employee/employee_input');
			return;
		}
		
		$this->model->db_regist($input);
		
		$this->return_list($this->get('class_path'));
	}
	
	public function modify_input($id) {
		
		$this->load->model('employee/EmployeeRegistModel', 'model');
		$this->load->library('dao/EmployeeDao');
		
		$info = $this->model->get_info($id);
		
		$this->set_attribute($info);
		
		$this->set('action', 'modify');
		$this->set('class_path', 'employee/Employee');
		$this->set('delete_disable', '1');
		if ($info['retirement'] == '1') $this->set('retirement_checked', array(1));
		
		$this->view('employee/employee_input');
	}
	
	/**
	 * 更新
	 */
	public function modify() {
		
		$this->load->model('employee/EmployeeRegistModel', 'model');
		$this->load->library('dao/EmployeeDao');
		
		$input = $this->get_attribute();
		
		$msgs = $this->model->validation($input);
		
		if ($msgs != null) {
			$this->set_err_info($msgs);
			$this->set('delete_disable', '1');
			if ($input['retirement'] == '1') $this->set('retirement_checked', array(1));
			$this->view('employee/employee_input');
			return;
		}
		
		$this->model->db_modify($input);
		
		$this->return_list($this->get('class_path'));
	}
}
?>