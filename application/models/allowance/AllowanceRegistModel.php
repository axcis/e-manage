<?php

/**
 * AllowanceRegistModel
 * @author takanori_gozu
 *
 */
class AllowanceRegistModel extends AllowanceBaseModel {
	
	/**
	 * バリデーション
	 */
	public function validation($input) {
		
		$msgs = array();
		
		$employee_id = $input['employee_id'];
		$qualification_id = $input['qualification_id'];
		$request_date = $input['request_date'];
		
		//未選択チェック
		if (trim($employee_id) == '') $msgs[] = $this->lang->line('err_not_select', array($this->lang->line('employee_name')));
		if (trim($qualification_id) == '') $msgs[] = $this->lang->line('err_not_select', array($this->lang->line('qualification_name')));
		if (trim($request_date) == '') $msgs[] = $this->lang->line('err_not_select', array($this->lang->line('request_date')));
		
		return $msgs;
	}
	
	/**
	 * 新規登録
	 */
	public function db_regist($input) {
		
		$employee_id = $input['employee_id'];
		$request_date = $input['request_date'];
		
		//資格情報を分割
		$qualification_arr = explode('-', $input['qualification_id']);
		$class_id = $qualification_arr[0];
		$rank = $qualification_arr[1];
		
		$start_ym = $this->get_start_ym($request_date);
		
		switch ($rank) {
			case 0:
				//そのまま1件だけ登録
				$this->one_insert($employee_id, $class_id, $rank, $request_date, $start_ym);
				break;
			default:
				//下位ランクの登録有無チェック
				if ($this->check_lower_rank($employee_id, $class_id, $rank)) {
					//下位資格登録済→上位資格1件だけ登録する
					$this->one_insert($employee_id, $class_id, $rank, $request_date, $start_ym);
				} else {
					//下位資格も併せて登録
					$this->multi_insert($employee_id, $class_id, $rank, $request_date, $start_ym);
				}
				break;
		}
		
	}
	
	/**
	 * 1件だけ登録
	 */
	private function one_insert($employee_id, $class_id, $rank, $request_date, $start_ym) {
		
		$this->set_table(QualificationAllowanceDao::TABLE_NAME);
		
		$this->add_col_val(QualificationAllowanceDao::COL_EMPLOYEE_ID, $employee_id);
		$this->add_col_val(QualificationAllowanceDao::COL_CLASS_ID, $class_id);
		$this->add_col_val(QualificationAllowanceDao::COL_RANK, $rank);
		$this->add_col_val(QualificationAllowanceDao::COL_REQUEST_DATE, $request_date);
		$this->add_col_val(QualificationAllowanceDao::COL_START_YM, $start_ym);
		
		$this->do_insert();
	}
	
	/**
	 * 下位資格の登録有無のチェック
	 */
	private function check_lower_rank($employee_id, $class_id, $rank) {
		
		$this->set_table(QualificationAllowanceDao::TABLE_NAME);
		
		$this->add_where(QualificationAllowanceDao::COL_EMPLOYEE_ID, $employee_id);
		$this->add_where(QualificationAllowanceDao::COL_CLASS_ID, $class_id);
		$this->add_where(QualificationAllowanceDao::COL_RANK, $rank, self::COMP_LESS_THAN);
		
		$count = $this->do_count();
		
		return $count == $rank;
	}
	
	/**
	 * 下位資格も含めた複数件登録
	 */
	private function multi_insert($employee_id, $class_id, $rank, $request_date, $start_ym) {
		
		//資格マスタから同一分類の資格を取得
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_select(QualificationDao::COL_CLASS_ID);
		$this->add_select(QualificationDao::COL_RANK);
		$this->add_where(QualificationDao::COL_CLASS_ID, $class_id);
		$this->add_where(QualificationDao::COL_RANK, $rank, self::COMP_LESS_EQUAL);
		
		$list = $this->do_select();
		$map = array();
		
		foreach ($list as $row) {
			//登録済みかチェックしてなければ、Insertのマップへ情報をセット
			$this->set_table(QualificationAllowanceDao::TABLE_NAME);
			$this->add_where(QualificationAllowanceDao::COL_EMPLOYEE_ID, $employee_id);
			$this->add_where(QualificationAllowanceDao::COL_CLASS_ID, $row[QualificationDao::COL_CLASS_ID]);
			$this->add_where(QualificationAllowanceDao::COL_RANK, $row[QualificationDao::COL_RANK]);
			$count = $this->do_count();
			if ($count == 0) {
				$map[] = array(
						QualificationAllowanceDao::COL_EMPLOYEE_ID => $employee_id,
						QualificationAllowanceDao::COL_CLASS_ID => $row[QualificationDao::COL_CLASS_ID],
						QualificationAllowanceDao::COL_RANK => $row[QualificationDao::COL_RANK],
						QualificationAllowanceDao::COL_REQUEST_DATE => $request_date,
						QualificationAllowanceDao::COL_START_YM => $start_ym
				);
			}
		}
		//Insert実行
		$this->set_table(QualificationAllowanceDao::TABLE_NAME);
		$this->do_bulk_insert($map);
	}
	
	/**
	 * 支給開始年月
	 */
	private function get_start_ym($date) {
		
		// 年月日別に分離
		$year = date('Y', strtotime($date));
		$month = date('n', strtotime($date));
		$day = date('j', strtotime($date));
		
		$month += 2;
		
		// 年を跨ぐ場合
		if ($month > 12) {
			$year++;
			$month -= 12;
		}
		
		// 算出結果の日付を返す
		if (checkdate($month, $day, $year)) {
			return date('Y-m', strtotime(sprintf('%04d-%02d-%02d', $year, $month, $day)));
		}
		
		// 2月31日などになった場合、月末の日付を返す
		return date('Y-m', strtotime(sprintf('%04d-%02d-01 -1 day', $year, ($month+1))));
	}
}
?>