<?php

/**
 * EmployeeListModel
 * @author takanori_gozu
 *
 */
class EmployeeListModel extends MY_Model {
	
	/**
	 * 一覧
	 */
	public function get_list($search = null) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_ID);
		$this->add_select(EmployeeDao::COL_NAME);
		$this->add_select(EmployeeDao::COL_HIRAGANA);
		$this->add_select(EmployeeDao::COL_RETIREMENT);
		
		//退職者データ表示制御
		if (!isset($search['retirement_show'])) {
			$this->add_where(EmployeeDao::COL_RETIREMENT, '0');
		}
		
		//検索
		if ($search != null) {
			$this->set_search_like($search, EmployeeDao::COL_NAME, 'search_name');
		}
		
		$list = $this->do_select();
		
		foreach ($list as &$row) {
			if ($row[EmployeeDao::COL_RETIREMENT] == '1') {
				$row[EmployeeDao::COL_RETIREMENT] = '済';
			} else {
				$row[EmployeeDao::COL_RETIREMENT] = '';
			}
		}
		
		return $list;
	}
	
	/**
	 * 項目名
	 */
	public function get_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 70, 'value' => ''); //編集
		$list_col[] = array('width' => 70, 'value' => 'ID');
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '社員名(かな)');
		$list_col[] = array('width' => 70, 'value' => '退職');
		
		return $list_col;
	}
	
	/**
	 * リンク
	 */
	public function get_link() {
		
		$link = array();
		
		$link[] = array('url' => 'employee/EmployeeRegist/regist_input', 'class' => 'fa fa-pencil', 'value' => '登録');
		
		return $link;
	}
}
?>