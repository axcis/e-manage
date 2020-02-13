<?php

/**
 * TopPageModel
 * @author takanori_gozu
 *
 */
class TopPageModel extends MY_Model {
	
	/**
	 * 年月のマッピング(前半年と後1年)
	 */
	public function get_month_map() {
		
		$map = array();
		$month = date('Y-m');
		
		//前半年
		for ($i = 6; $i > 0; $i--) {
			$target = strtotime("-$i month", strtotime($month));
			$map[date('Y-m', $target)] = date('Y年n月', $target);
		}
		
		//当月
		$map[$month] = date('Y年n月', strtotime($month));
		
		//後1年
		for ($i = 0; $i <= 12; $i++) {
			$target = strtotime("+$i month", strtotime($month));
			$map[date('Y-m', $target)] = date('Y年n月', $target);
		}
		
		return $map;
	}
	
	/**
	 * 社員のマッピング
	 */
	public function get_employee_map() {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_ID);
		$this->add_select(EmployeeDao::COL_NAME);
		
		return $this->key_value_map($this->do_select());
	}
	
	/**
	 * 有休付与マスタ
	 */
	private function get_vacation_master_map() {
		
		$this->set_table(VacationMasterDao::TABLE_NAME);
		
		$this->add_select(VacationMasterDao::COL_COUNT_KEY);
		$this->add_select(VacationMasterDao::COL_GIVE_COUNT);
		
		$list = $this->do_select();
		
		return $this->key_value_map($list, VacationMasterDao::COL_COUNT_KEY, VacationMasterDao::COL_GIVE_COUNT);
	}
	
	/**
	 * 資格マスタ
	 */
	private function get_qualification_map() {
		
		$this->set_table(QualificationDao::TABLE_NAME);
		
		$this->add_select_as("concat(class_id, '-', rank)", 'qualification_id');
		$this->add_select(QualificationDao::COL_NAME);
		$this->add_select(QualificationDao::COL_ALLOWANCE_PRICE);
		
		$list = $this->do_select();
		
		return $this->list_to_map($list, 'qualification_id');
	}
	
	/**
	 * 入社社員情報
	 */
	public function get_hire_list($month) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_NAME);
		$this->add_select(EmployeeDao::COL_HIRE_DATE);
		
		$this->add_where_like(EmployeeDao::COL_HIRE_DATE, $month, self::WILD_CARD_AFTER);
		
		return $this->do_select();
	}
	
	/**
	 * 入社半年経過社員情報(入社祝い金)
	 */
	public function get_half_year_employee_list($month) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_NAME);
		$this->add_select(EmployeeDao::COL_HALF_HIRE_DATE);
		
		$this->add_where_like(EmployeeDao::COL_HALF_HIRE_DATE, $month, self::WILD_CARD_AFTER);
		
		return $this->do_select();
	}
	
	/**
	 * 入社一年経過社員情報(入社祝い金)
	 */
	public function get_one_year_employee_list($month) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_NAME);
		$this->add_select(EmployeeDao::COL_ONE_YEAR_HIRE_DATE);
		
		$this->add_where_like(EmployeeDao::COL_ONE_YEAR_HIRE_DATE, $month, self::WILD_CARD_AFTER);
		
		return $this->do_select();
	}
	
	/**
	 * 退職社員情報
	 */
	public function get_retirement_list($month) {
		
		$this->set_table(EmployeeDao::TABLE_NAME);
		
		$this->add_select(EmployeeDao::COL_NAME);
		$this->add_select(EmployeeDao::COL_RETIREMENT_DATE);
		
		$this->add_where(EmployeeDao::COL_RETIREMENT, '1');
		$this->add_where_like(EmployeeDao::COL_RETIREMENT_DATE, $month, self::WILD_CARD_AFTER);
		
		return $this->do_select();
	}
	
	/**
	 * 契約社員登用情報
	 */
	public function get_contract_list($employee_map, $month) {
		
		$this->set_table(EmployeeStatusDao::TABLE_NAME);
		
		$this->add_select(EmployeeStatusDao::COL_EMPLOYEE_ID);
		$this->add_select(EmployeeStatusDao::COL_CONTRACT_DATE);
		
		$this->add_where_like(EmployeeStatusDao::COL_CONTRACT_DATE, $month, self::WILD_CARD_AFTER);
		
		$list = $this->do_select();
		
		foreach ($list as &$row) {
			$row[EmployeeStatusDao::COL_EMPLOYEE_ID] = $employee_map[$row[EmployeeStatusDao::COL_EMPLOYEE_ID]];
		}
		
		return $list;
	}
	
	/**
	* 正社員登用情報
	*/
	public function get_regular_list($employee_map, $month) {
		
		$this->set_table(EmployeeStatusDao::TABLE_NAME);
		
		$this->add_select(EmployeeStatusDao::COL_EMPLOYEE_ID);
		$this->add_select(EmployeeStatusDao::COL_REGULAR_DATE);
		
		$this->add_where_like(EmployeeStatusDao::COL_REGULAR_DATE, $month, self::WILD_CARD_AFTER);
		
		$list = $this->do_select();
		
		foreach ($list as &$row) {
			$row[EmployeeStatusDao::COL_EMPLOYEE_ID] = $employee_map[$row[EmployeeStatusDao::COL_EMPLOYEE_ID]];
		}
		
		return $list;
	}
	
	/**
	 * 有休付与情報
	 */
	public function get_vacation_date_list($employee_map, $month) {
		
		$this->set_table(VacationDateDao::TABLE_NAME);
		
		$this->add_select(VacationDateDao::COL_EMPLOYEE_ID);
		$this->add_select(VacationDateDao::COL_GIVE_DATE);
		$this->add_select(VacationDateDao::COL_WORK_YEAR);
		
		$this->add_where_like(VacationDateDao::COL_GIVE_DATE, $month, self::WILD_CARD_AFTER);
		
		$list = $this->do_select();
		
		$vacation_maseter_map = $this->get_vacation_master_map();
		
		foreach ($list as &$row) {
			$row[VacationDateDao::COL_EMPLOYEE_ID] = $employee_map[$row[VacationDateDao::COL_EMPLOYEE_ID]];
			$row[VacationDateDao::COL_WORK_YEAR] = $vacation_maseter_map[$row[VacationDateDao::COL_WORK_YEAR]];
		}
		
		return $list;
	}
	
	/**
	 * 資格手当申請情報
	 */
	public function get_allowance_list($employee_map, $month) {
		
		$this->set_table(QualificationAllowanceDao::TABLE_NAME);
		
		$this->add_select(QualificationAllowanceDao::COL_EMPLOYEE_ID);
		$this->add_select_as("concat(class_id, '-', rank)", 'qualification_id');
		$this->add_select_as("''", 'qualification_name');
		
		$this->add_where(QualificationAllowanceDao::COL_START_YM, $month);
		
		$list = $this->do_select();
		
		//資格マスタ情報
		$qualification_map = $this->get_qualification_map();
		
		foreach ($list as &$row) {
			$row[QualificationAllowanceDao::COL_EMPLOYEE_ID] = $employee_map[$row[QualificationAllowanceDao::COL_EMPLOYEE_ID]];
			$row['qualification_name'] = $qualification_map[$row['qualification_id']]['name'];
			$row['allowance_price'] = $qualification_map[$row['qualification_id']]['allowance_price'];
		}
		
		return $list;
	}
	
	/**
	 * 入社情報項目名
	 */
	public function get_hire_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '入社日');
		
		return $list_col;
	}
	
	/**
	 * 退職情報項目名
	 */
	public function get_retirement_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '退職日');
		
		return $list_col;
	}
	
	/**
	 * 入社半年経過社員情報項目名
	 */
	public function get_halr_year_employee_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '入社半年後日付');
		
		return $list_col;
	}
	
	/**
	 * 入社一年経過社員情報項目名
	 */
	public function get_one_year_employee_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '入社一年後日付');
		
		return $list_col;
	}
	
	/**
	 * 契約社員情報項目名
	 */
	public function get_contract_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '契約社員登用日');
		
		return $list_col;
	}
	
	/**
	 * 正社員情報項目名
	 */
	public function get_regular_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '正社員登用日');
		
		return $list_col;
	}
	
	/**
	 * 有休付与情報項目名
	 */
	public function get_vacation_date_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '有休付与日');
		$list_col[] = array('width' => 150, 'value' => '付与日数');
		
		return $list_col;
	}
	
	/**
	 * 資格手当情報項目名
	 */
	public function get_allowance_list_col() {
		
		$list_col = array();
		
		$list_col[] = array('width' => 150, 'value' => '社員名');
		$list_col[] = array('width' => 150, 'value' => '資格名');
		$list_col[] = array('width' => 100, 'value' => '手当金額');
		
		return $list_col;
	}
}
?>