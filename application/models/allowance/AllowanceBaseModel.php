<?php

/**
 * AllowanceBaseModel
 * 資格手当申請の共通モデル
 * @author takanori_gozu
 *
 */
class AllowanceBaseModel extends MY_Model {
	
	/**
	 * 社員のマッピング
	 */
	public function get_employee_map($no_select_show = true) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_ID);
		$this->add_select(EmployeeDao::COL_NAME);
		
		$list = $this->do_select();
		
		$map = array();
		
		if ($no_select_show) $map[''] = '社員を選択';
		$map += $this->key_value_map($list);
		
		return $map;
	}
	
	/**
	 * 資格マスタのマッピング
	 */
	public function get_qualification_map($no_select_show = true) {
		
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_select_as("concat(class_id, '-', rank)", 'qualification_id');
		$this->add_select(QualificationDao::COL_NAME);
		
		$list = $this->do_select();
		
		$map = array();
		
		if ($no_select_show) $map[''] = '資格を選択';
		$map += $this->key_value_map($list, 'qualification_id');
		
		return $map;
	}
}
?>