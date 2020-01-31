<?php

/**
 * LoginModel
 * @author takanori_gozu
 *
 */
class LoginModel extends MY_Model {
	
	/**
	 * コンストラクタ
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * ログイン認証チェック
	 */
	public function login_check($input) {
		
		$msg = array();
		$login_id = $input['login_id'];
		$password = $input['password'];
		
		if ($login_id == '') {
			$msg[] = $this->lang->line('err_required', array($this->lang->line('login_id')));
		}
		
		if ($password == '') {
			$msg[] = $this->lang->line('err_required', array($this->lang->line('password')));
		}
		
		if ($msg != null) return $msg;
		
		//認証チェック(ID・パスは固定)
		if ($login_id != $this->lang->line('auth_login_id') ||
			$password != $this->lang->line('auth_password')) {
			$msg[] = $this->lang->line('err_not_match', array($this->lang->line('login_id'), $this->lang->line('password')));
			return $msg;
		}
		
		//認証フラグのみ、Sessionにセット
		$this->set_session('is_login', '1');
		
		return null;
	}
}
?>