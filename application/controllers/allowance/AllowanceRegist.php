<?php

/**
 * AllowanceRegistController
 * @author takanori_gozu
 *
 */
class AllowanceRegist extends MY_Controller {
	
	public function regist_input() {
		
		$this->load->model('allowance/AllowanceRegistModel', 'model');
		$this->load->library('dao/EmployeeDao');
		$this->load->library('dao/QualificationDao');
		
		$this->set('employee_map', $this->model->get_employee_map());
		$this->set('qualification_map', $this->model->get_qualification_map());
		
		$this->set('action', 'regist');
		$this->set('class_path', 'allowance/Allowance');
		
		$this->view('allowance/allowance_input');
	}
	
	/**
	 * 新規登録
	 */
	public function regist() {
		
		$this->load->model('allowance/AllowanceRegistModel', 'model');
		$this->load->library('dao/QualificationDao');
		$this->load->library('dao/QualificationAllowanceDao');
		
		$input = $this->get_attribute();
		
		$msgs = $this->model->validation($input);
		
		if ($msgs != null) {
			$this->set_err_info($msgs);
			$this->load->library('dao/EmployeeDao');
			$this->set('employee_map', $this->model->get_employee_map());
			$this->set('qualification_map', $this->model->get_qualification_map());
			$this->view('allowance/allowance_input');
			return;
		}
		
		$this->model->db_regist($input);
		
		$this->return_list($this->get('class_path'));
	}
}
?>