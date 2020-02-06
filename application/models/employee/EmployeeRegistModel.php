<?php

/**
 * EmployeeRegistModel
 * @author takanori_gozu
 *
 */
class EmployeeRegistModel extends MY_Model {
	
	/**
	 * 試用期間
	 */
	public function get_trial_map() {
		
		$map = array();
		
		$map['2'] = '2ヶ月';
		$map['1'] = '1ヶ月';
		$map['0'] = 'なし';
		
		return $map;
	}
	
	/**
	 * 詳細
	 */
	public function get_info($id) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_ID);
		$this->add_select(EmployeeDao::COL_NAME);
		$this->add_select(EmployeeDao::COL_HIRAGANA);
		$this->add_select(EmployeeDao::COL_HIRE_DATE);
		$this->add_select(EmployeeDao::COL_RETIREMENT);
		$this->add_select(EmployeeDao::COL_RETIREMENT_DATE);
		
		$this->add_where(EmployeeDao::COL_ID, $id);
		
		return $this->do_select_info();
	}
	
	/**
	 * バリデーション
	 */
	public function validation($input) {
		
		$msgs = array();
		
		$name = $input['name'];
		$hiragana = $input['hiragana'];
		$hire_date = $input['hire_date'];
		$retirement = isset($input['retirement']) ? $input['retirement'] : '';
		
		//未入力・未選択チェック
		if (trim($name) == '') $msgs[] = $this->lang->line('err_required', array($this->lang->line('name')));
		if (trim($hiragana) == '') $msgs[] = $this->lang->line('err_required', array($this->lang->line('hiragana')));
		if (trim($hire_date) == '') $msgs[] = $this->lang->line('err_not_select', array($this->lang->line('hire_date')));
		
		//文字列長チェック
		if (mb_strlen(trim($name)) > 50) $msgs[] = $this->lang->line('err_max_length', array($this->lang->line('name'), 50));
		if (mb_strlen(trim($hiragana)) > 50) $msgs[] = $this->lang->line('err_max_length', array($this->lang->line('hiragana'), 50));
		
		if ($msgs != null) return $msgs;
		
		//退職済みの場合、退職日未選択はエラー
		if ($input['action'] == 'modify' && $retirement == '1') {
			if ($input['retirement_date'] == '') $msgs[] = $this->lang->line('err_not_select', array($this->lang->line('retirement_date')));
		}
		
		return $msgs;
	}
	
	/**
	 * 新規登録
	 */
	public function db_regist($input) {
		
		//トランザクション
		$this->db_trans_start();
		
		//社員情報の新規登録
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_col_val(EmployeeDao::COL_NAME, $input['name']);
		$this->add_col_val(EmployeeDao::COL_HIRAGANA, $input['hiragana']);
		$this->add_col_val(EmployeeDao::COL_HIRE_DATE, $input['hire_date']);
		$this->add_col_val(EmployeeDao::COL_RETIREMENT, '0');
		
		$employee_id = $this->do_insert_get_id();
		
		//雇用形態情報の新規登録
		$contract_date = $this->get_add_date($input['hire_date'], $input['trial']);
		$regular_date = $this->get_add_date($contract_date, 6); //正社員は6ヶ月後で固定
		
		$this->set_table(EmployeeStatusDao::TABLE_NAME);
		
		$this->add_col_val(EmployeeStatusDao::COL_EMPLOYEE_ID, $employee_id);
		$this->add_col_val(EmployeeStatusDao::COL_CONTRACT_DATE, $contract_date);
		$this->add_col_val(EmployeeStatusDao::COL_REGULAR_DATE, $regular_date);
		
		$this->do_insert();
		
		//初回有休付与情報の新規登録
		$give_date = $input['give_date'] != '' ? $input['give_date'] : $this->get_add_date($input['hire_date'], 6);
		$this->set_table(VacationDateDao::TABLE_NAME);
		
		$this->add_col_val(VacationDateDao::COL_EMPLOYEE_ID, $employee_id);
		$this->add_col_val(VacationDateDao::COL_GIVE_DATE, $give_date);
		$this->add_col_val(VacationDateDao::COL_WORK_YEAR, 0);
		
		$this->do_insert();
		
		$this->db_trans_commit();
	}
	
	/**
	 * 加算後の日付を取得
	 */
	private function get_add_date($target_date, $add) {
		
		// 年月日別に分離
		$year = date('Y', strtotime($target_date));
		$month = date('n', strtotime($target_date));
		$day = date('j', strtotime($target_date));
		
		$month += $add;
		
		// 年を跨ぐ場合
		if ($month > 12) {
			$year++;
			$month -= 12;
		}
		
		// 算出結果の日付を返す
		if (checkdate($month, $day, $year)) {
			return date('Y-m-d', strtotime(sprintf('%04d-%02d-%02d', $year, $month, $day)));
		}
		
		// 2月31日などになった場合、月末の日付を返す
		return date('Y-m-d', strtotime(sprintf('%04d-%02d-01 -1 day', $year, ($month+1))));
	}
	
	/**
	 * 更新
	 */
	public function db_modify($input) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_col_val(EmployeeDao::COL_NAME, $input['name']);
		$this->add_col_val(EmployeeDao::COL_HIRAGANA, $input['hiragana']);
		$this->add_col_val(EmployeeDao::COL_HIRE_DATE, $input['hire_date']);
		
		if (isset($input['retirement'])) {
			$this->add_col_val(EmployeeDao::COL_RETIREMENT, '1');
			$this->add_col_val(EmployeeDao::COL_RETIREMENT_DATE, $input['retirement_date']);
		} else {
			$this->add_col_val(EmployeeDao::COL_RETIREMENT, '0');
			$this->add_col_val(EmployeeDao::COL_RETIREMENT_DATE, null);
		}
		
		$this->add_where(EmployeeDao::COL_ID, $input['id']);
		
		$this->do_update();
	}
}
?>