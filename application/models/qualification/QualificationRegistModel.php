<?php

/**
 * QualificationRegistModel
 * @author takanori_gozu
 *
 */
class QualificationRegistModel extends MY_Model {
	
	/**
	 * 資格ランク
	 */
	public function get_rank_map() {
		
		$map = array();
		
		//TODO: とりあえず3まで用意
		$map[0] = '0';
		$map[1] = '1';
		$map[2] = '2';
		$map[3] = '3';
		
		return $map;
	}
	
	/**
	 * 詳細
	 */
	public function get_info($class_id, $rank) {
		
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_where(QualificationDao::COL_CLASS_ID, $class_id);
		$this->add_where(QualificationDao::COL_RANK, $rank);
		
		return $this->do_select_info();
	}
	
	/**
	 * バリデーション
	 */
	public function validation($input) {
		
		$msgs = array();
		
		$action = $input['action'];
		$class_id = $input['class_id'];
		$rank = $input['rank'];
		if ($action == 'modify') {
			$old_rank = $input['old_rank'];
		}
		$name = $input['name'];
		$allowance_price = $input['allowance_price'];
		
		//未入力・未選択チェック
		if (trim($class_id) == '') $msgs[] = $this->lang->line('err_required', array($this->lang->line('class_id')));
		if (trim($name) == '') $msgs[] = $this->lang->line('err_required', array($this->lang->line('qualification_name')));
		if (trim($allowance_price) == '') $msgs[] = $this->lang->line('err_required', array($this->lang->line('allowance_price')));
		
		//文字列長チェック
		if (mb_strlen(trim($class_id)) != 2 || !preg_match("/^[A-Z]+$/", $class_id)) $msgs[] = '分類IDは半角英字(大文字)2文字で入力してください'	;
		if (mb_strlen(trim($name)) > 50) $msgs[] = $this->lang->line('err_max_length', array($this->lang->line('name'), 50));
		
		if ($msgs != null) return $msgs;
		
		//登録済ID・ランクのチェック
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_where(QualificationDao::COL_CLASS_ID, $class_id);
		if ($action == 'regist') {
			$this->add_where(QualificationDao::COL_RANK, $rank);
		} else {
			$this->add_where(QualificationDao::COL_RANK, $old_rank, self::COMP_NOT_EQUAL);
			$this->add_where(QualificationDao::COL_RANK, $rank);
		}
		$count = $this->do_count();
		
		if ($count > 0) $msgs[] = '入力された分類ID・ランクの資格は登録済みです。';
		
		return $msgs;
	}
	
	/**
	 * 新規登録
	 */
	public function db_regist($input) {
		
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_col_val(QualificationDao::COL_CLASS_ID, $input['class_id']);
		$this->add_col_val(QualificationDao::COL_RANK, $input['rank']);
		$this->add_col_val(QualificationDao::COL_NAME, $input['name']);
		$this->add_col_val(QualificationDao::COL_ALLOWANCE_PRICE, $input['allowance_price']);
		
		$this->do_insert();
	}
	
	/**
	 * 更新
	 */
	public function db_modify($input) {
		
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_col_val(QualificationDao::COL_RANK, $input['rank']);
		$this->add_col_val(QualificationDao::COL_NAME, $input['name']);
		$this->add_col_val(QualificationDao::COL_ALLOWANCE_PRICE, $input['allowance_price']);
		
		$this->add_where(QualificationDao::COL_CLASS_ID, $input['class_id']);
		$this->add_where(QualificationDao::COL_RANK, $input['old_rank']);
		
		$this->do_update();
	}
}
?>