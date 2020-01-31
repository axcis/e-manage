<?php

/**
 * Modelのオリジナル継承クラス
 * DB関係の関数を簡素化(ラッピング)
 * joinに関しては、2テーブル間のみ
 * →3つ以上のjoinはfetch結果をマージして1つの配列にするようにすること
 *
 * @author takanori_gozu
 *
 */
class MY_Model extends CI_Model {
	
	private $_select;
	private $_order;
	private $_group;
	private $_having;
	private $_col_val;
	private $_join;
	private $_distinct;
	private $_table;
	
	const COMP_EQUAL = ' = ';
	const COMP_NOT_EQUAL = ' <> ';
	const COMP_GREATER_EQUAL = ' >= ';
	const COMP_GREATER_THAN = ' > ';
	const COMP_LESS_EQUAL = ' <= ';
	const COMP_LESS_THAN = ' < ';
	
	const WILD_CARD_BEFORE = 'before';
	const WILD_CARD_AFTER = 'after';
	const WILD_CARD_BOTH = 'both';
	
	const ORDER_ASC = 'asc';
	const ORDER_DESC = 'desc';
	
	const JOIN_LEFT = 'left';
	const JOIN_RIGHT = 'right';
	const JOIN_INNER = 'inner';
	const JOIN_OUTER = 'outer';
	
	/**
	 * コンストラクタ
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->init();
	}
	
	/**
	 * イニシャライズ
	 */
	public function init() {
		$this->_select = array();
		$this->_order = array();
		$this->_group = array();
		$this->_having = array();
		$this->_col_val = array();
		$this->_join = false;
		$this->_distinct = false;
		$this->_table = '';
	}
	
	/**
	 * テーブル名セット
	 */
	public function set_table($table) {
		$this->_table = $table;
	}
	
	/**
	 * add_select
	 */
	public function add_select($col, $table = '') {
		if ($table != '') {
			$col = $table. '.'. $col;
		}
		$this->_select[] = $col;
	}
	
	/**
	 * add_select_as
	 */
	public function add_select_as($col, $name) {
		$this->_select[] = $col. ' AS '. $name;
	}
	
	/**
	 * add_select_sum_as
	 */
	public function add_select_sum_as($col, $name = '') {
		if ($name == '') {
			$name = $col;
		}
		$this->add_select_as('SUM('.$col. ')', $name);
	}
	
	/**
	 * set_distinct
	 */
	public function set_distinct($flg = true) {
		$this->_distinct = $flg;
	}
	
	/**
	 * add_where
	 */
	public function add_where($col, $value, $comp = self::COMP_EQUAL) {
		if ($comp != self::COMP_EQUAL) {
			$col = $col .' '. $comp;
		}
		$this->db->where($col, $value);
	}
	
	/**
	 * add_where_or
	 */
	public function add_where_or($col, $value, $comp = self::COMP_EQUAL) {
		if ($comp != self::COMP_EQUAL) {
			$col = $col .' '. $comp;
		}
		$this->_db->or_where($col, $value);
	}
	
	/**
	 * add_where_in
	 */
	public function add_where_in($col, $values) {
		$arr = explode(',', $values);
		$this->db->where_in($col, $arr);
	}
	
	/**
	 * add_where_or_in
	 */
	public function add_where_or_in($col, $values) {
		$arr = explode(',', $values);
		$this->db->or_where_in($col, $arr);
	}
	
	/**
	 * add_where_not_in
	 */
	public function add_where_not_in($col, $values) {
		$arr = explode(',', $values);
		$this->db->where_not_in($col, $arr);
	}
	
	/**
	 * add_where_like
	 */
	public function add_where_like($col, $value, $match = self::WILD_CARD_BOTH) {
		$this->db->like($col, $value, $match);
	}
	
	/**
	 * add_where_not_like
	 */
	public function add_where_not_like($col, $value, $match = self::WILD_CARD_BOTH) {
		$this->db->not_like($col, $value, $match);
	}
	
	/**
	 * add_where_statement
	 */
	public function add_where_statement($where) {
		$this->db->where($where);
	}
	
	/**
	 * add_join
	 */
	public function add_join($table2, $col1, $col2, $join = self::JOIN_INNER) {
		$this->_join = true;
		$this->db->join("$table2", "$this->_table.$col1 = $table2.$col2", $join);
	}
	
	/**
	 * add_order
	 */
	public function add_order($col, $order = self::ORDER_ASC) {
		$this->_order[$col] = $order;
	}
	
	/**
	 * add_group
	 */
	public function add_group($col) {
		$this->_group[] = $col;
	}
	
	/**
	 * add_having
	 */
	public function add_having($col, $value, $comp = self::COMP_EQUAL) {
		if ($comp != self::COMP_EQUAL) {
			$col = $col .' '. $comp;
		}
		$this->_having[$col] = $value;
	}
	
	/**
	 * do_select
	 */
	public function do_select() {
		
		if ($this->_distinct) $this->db->distinct();
		
		//select
		$this->db->select($this->_select);
		
		//join
		if ($this->_join) {
			$this->db->from($this->_table);
		}
		
		//whereに関しては、関数内で書き込んでいるのでここでは不要
		
		//order by
		if (count($this->_order) > 0) {
			foreach ($this->_order as $key => $value) {
				$this->db->order_by($key, $value);
			}
		}
		
		//group by
		if (count($this->_group) > 0) {
			$this->db->group_by($this->_group);
		}
		
		//having
		if (count($this->_having) > 0) {
			foreach ($this->_having as $key => $value) {
				$this->db->having($key, $value);
			}
		}
		
		$query = '';
		
		if ($this->_join) {
			$query = $this->db->get();
		} else {
			$query = $this->db->get($this->_table);
		}
		
		$this->init();
		
		return $query->result('array');
	}
	
	/**
	 * do_selectの1件のみの取得Ver
	 */
	public function do_select_info() {
		$ret = $this->do_select();
		if ($ret == null) return null;
		return $ret[0];
	}
	
	/**
	 * 件数取得
	 */
	public function do_count() {
		$this->add_select_as('count(*)', 'cnt');
		$ret = $this->do_select();
		return $ret[0]['cnt'];
	}
	
	/**
	 * add_col_val
	 */
	public function add_col_val($col, $val) {
		$this->_col_val[$col] = $val;
	}
	
	/**
	 * add_col_val_str
	 */
	public function add_col_val_str($col, $val) {
		$this->_col_val[$col] = "'". $val. "'";
	}
	
	/**
	 * add_limit
	 */
	public function add_limit($count) {
		$this->db->limit($count);
	}
	
	/**
	 * do_insertとほぼ同じ
	 * これは結果行を取得する場合や、DB以外の
	 * トランザクションで使用する
	 */
	public function do_insert_get_rows() {
		
		$sql = 'INSERT INTO '. $this->_table. '(';
		$i = 0;
		
		foreach ($this->_col_val as $key => $value) {
			if ($i > 0) {
				$sql .= ', ';
			}
			$sql .= $key;
			$i++;
		}
		
		$sql .= ') VALUES (';
		$i = 0;
		
		foreach ($this->_col_val as $key => $value) {
			if ($i > 0) {
				$sql .= ', ';
			}
			$sql .= $value;
			$i++;
		}
		
		$sql .= ');';
		
		//Insert実行
		$this->db->query($sql);
		$rows = $this->get_rows();
		$this->init();
		
		//結果行数を取得
		return $rows;
	}
	
	/**
	 * do_insert
	 */
	public function do_insert() {
		$this->db->insert($this->_table, $this->_col_val);
		$this->init();
	}
	
	/**
	 * Insert実行後、IDを取得する
	 */
	public function do_insert_get_id() {
		$this->db->insert($this->_table, $this->_col_val);
		$id = $this->db->insert_id();
		$this->init();
		return $id;
	}
	
	/**
	 * 一括Insert
	 */
	public function do_bulk_insert($data) {
		$this->db->insert_batch($this->_table, $data);
		$this->init();
	}
	
	/**
	 * do_update
	 */
	public function do_update() {
		$this->db->update($this->_table, $this->_col_val);
		$this->init();
	}
	
	/**
	 * do_delete
	 */
	public function do_delete() {
		$this->db->delete($this->_table);
		$this->init();
	}
	
	/**
	 * get_rows
	 */
	public function get_rows() {
		return $this->db->affected_rows();
	}
	
	//トランザクション
	public function db_trans_start() {
		$this->db->trans_start();
	}
	
	//ロールバック
	public function db_trans_rollback() {
		$this->db->trans_rollback();
	}
	
	//コミット
	public function db_trans_commit() {
		$this->db->trans_commit();
	}
	
	//set_session
	public function set_session($key, $value) {
		$this->session->set_userdata($key, $value);
	}
	
	//session一括設定
	public function set_session_attribute($sessions) {
		foreach ($sessions as $key => $value) {
			$this->set_session($key, $value);
		}
	}
	
	//get_session
	public function get_session($key) {
		return $this->session->userdata($key);
	}
	
	//session一括取得
	public function get_session_attribute() {
		return $this->session->all_userdata();
	}
	
	//session削除
	public function del_session($key) {
		$this->session->unset_userdata($key);
	}
	
	//設定ファイルを取得
	public function get_conf($key) {
		return $this->config->item($key);
	}
	
	/**
	 * listをkey=>valueのmapに変換
	 */
	public function key_value_map($list, $key = 'id', $value = 'name') {
		
		$map = array();
		
		foreach ($list as $info) {
			$map_key = $info[$key];
			$map_value = $info[$value];
			$map[$map_key] = $map_value;
		}
		
		return $map;
	}
	
	/**
	 * 二次元配列から1つのカラムを,で連結して返す
	 */
	public function list_to_string($list, $col = 'id') {
		
		$string = '';
		
		foreach($list as $row) {
			if ($string != '') $string .= ',';
			$string .= $row[$col];
		}
		
		return $string;
	}
	
	/**
	 * 二次元配列から1つのカラムを一次元配列にして返す
	 */
	public function list_to_array($list, $col = 'id') {
		
		$arr = array();
		
		foreach ($list as $row) {
			$arr[] = $row[$col];
		}
		
		return $arr;
	}
	
	/**
	 * 二次元配列から１つのカラムをKeyにしたmapにして返す
	 */
	public function list_to_map($list, $col = 'id') {
		
		$map = array();
		
		foreach ($list as $row) {
			$key = $row[$col];
			$map[$key] = $row;
		}
		
		return $map;
	}
	
	/**
	 * 検索をDaoに追加
	 */
	public function set_search($search, $col, $search_name, $comp = self::COMP_EQUAL) {
		if (isset($search[$search_name]) && $search[$search_name] != '') {
			$this->add_where($col, $search[$search_name], $comp);
		}
	}
	
	/**
	 * 検索をDaoに追加(Like検索)
	 */
	public function set_search_like($search, $col, $search_name, $match = self::WILD_CARD_BOTH) {
		if (isset($search[$search_name]) && $search[$search_name] != '') {
			$this->add_where_like($col, $search[$search_name], $match);
		}
	}
}
?>