<?php

/**
 * QualificationRegistController
 * @author takanori_gozu
 *
 */
class QualificationRegist extends MY_Controller {
	
	public function regist_input() {
		
		$this->load->model('qualification/QualificationRegistModel', 'model');
		
		$this->set('action', 'regist');
		$this->set('class_path', 'qualification/Qualification');
		$this->set('rank_map', $this->model->get_rank_map());
		
		$this->view('qualification/qualification_input');
	}
	
	/**
	 * 新規登録
	 */
	public function regist() {
		
		$this->load->model('qualification/QualificationRegistModel', 'model');
		$this->load->library('dao/QualificationDao');
		
		$input = $this->get_attribute();
		
		$msgs = $this->model->validation($input);
		
		if ($msgs != null) {
			$this->set_err_info($msgs);
			$this->set('rank_map', $this->model->get_rank_map());
			$this->view('qualification/qualification_input');
			return;
		}
		
		$this->model->db_regist($input);
		
		$this->return_list($this->get('class_path'));
	}
	
	public function modify_input($class_id, $rank) {
		
		$this->load->model('qualification/QualificationRegistModel', 'model');
		$this->load->library('dao/QualificationDao');
		
		$this->set_attribute($this->model->get_info($class_id, $rank));
		$this->set('old_rank', $rank);
		
		$this->set('action', 'modify');
		$this->set('class_path', 'qualification/Qualification');
		$this->set('rank_map', $this->model->get_rank_map());
		$this->set('delete_disable', '1');
		
		$this->view('qualification/qualification_input');
	}
	
	/**
	 * 更新
	 */
	public function modify() {
		
		$this->load->model('qualification/QualificationRegistModel', 'model');
		$this->load->library('dao/QualificationDao');
		
		$input = $this->get_attribute();
		
		$msgs = $this->model->validation($input);
		
		if ($msgs != null) {
			$this->set_err_info($msgs);
			$this->set('rank_map', $this->model->get_rank_map());
			$this->set('delete_disable', '1');
			$this->view('qualification/qualification_input');
			return;
		}
		
		$this->model->db_modify($input);
		
		$this->return_list($this->get('class_path'));
	}
}
?>