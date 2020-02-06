<?php

/**
 * AllowanceListModel
 * @author takanori_gozu
 *
 */
class AllowanceListModel extends AllowanceBaseModel {
	
	/**
	 * 一覧
	 */
	public function get_list($search = null) {
		
		$this->set_table(QualificationAllowanceDao::TABLE_NAME);
		
		$this->add_select(QualificationAllowanceDao::COL_EMPLOYEE_ID);
		$this->add_select_as("concat(class_id, '-', rank)", 'qualification_id');
		$this->add_select_as("''", 'qualification_name');
		$this->add_select(QualificationAllowanceDao::COL_REQUEST_DATE);
		
		if ($search != null) {
			$this->set_search($search, QualificationAllowanceDao::COL_EMPLOYEE_ID, 'search_employee_id');
			if (isset($search['qualification_id']) && $search['qualification_id'] != '') {
				//分割IDとランクでAND検索
				$arr = explode('-', $search['qualification_id']);
				$this->add_where(QualificationAllowanceDao::COL_CLASS_ID, $arr[0]);
				$this->add_where(QualificationAllowanceDao::COL_RANK, $arr[1]);
			}
		}
		
		$list = $this->do_select();
		
		//マージ
		$employee_map = $this->get_employee_map(false);
		$qualification_map = $this->get_qualification_map(false);
		
		foreach($list as &$row) {
			$row[QualificationAllowanceDao::COL_EMPLOYEE_ID] = $employee_map[$row[QualificationAllowanceDao::COL_EMPLOYEE_ID]];
			$row['qualification_name'] = $qualification_map[$row['qualification_id']];
		}
		
		return $list;
	}
	
	/**
	 * 項目名
	 */
	public function get_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '資格名');
		$list_col[] = array('width' => 150, 'value' => '申請日');
		
		return $list_col;
	}
	
	/**
	 * リンク
	 */
	public function get_link() {
		
		$link = array();
		
		$link[] = array('url' => 'allowance/AllowanceRegist/regist_input', 'class' => 'fa fa-pencil', 'value' => '登録');
		
		return $link;
	}
}
?>