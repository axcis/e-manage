<?php

/**
 * QualificationListModel
 * @author takanori_gozu
 *
 */
class QualificationListModel extends MY_Model {
	
	/**
	 * 一覧
	 */
	public function get_list($search = null) {
		
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_select(QualificationDao::COL_CLASS_ID);
		$this->add_select(QualificationDao::COL_RANK);
		$this->add_select(QualificationDao::COL_NAME);
		$this->add_select(QualificationDao::COL_ALLOWANCE_PRICE);
		
		//検索
		if ($search != null) {
			$this->set_search_like($search, QualificationDao::COL_NAME, 'search_name');
		}
		
		$this->add_order(QualificationDao::COL_CLASS_ID);
		$this->add_order(QualificationDao::COL_RANK);
		
		return $this->do_select();
	}
	
	/**
	 * 項目名
	 */
	public function get_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 70, 'value' => ''); //編集
		$list_col[] = array('width' => 70, 'value' => '分類ID');
		$list_col[] = array('width' => 70, 'value' => 'ランク');
		$list_col[] = array('width' => 300, 'value' => '資格名');
		$list_col[] = array('width' => 150, 'value' => '手当');
		
		return $list_col;
	}
	
	/**
	 * リンク
	 */
	public function get_link() {
		
		$link = array();
		
		$link[] = array('url' => 'qualification/QualificationRegist/regist_input', 'class' => 'fa fa-pencil', 'value' => '登録');
		
		return $link;
	}
}
?>