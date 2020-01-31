<?php

/**
 * TopPageController
 * @author takanori_gozu
 *
 */
class TopPage extends MY_Controller {
	
	/**
	 * Index
	 */
	public function index() {
		
		$this->set('class_key', 'top');
		
		//TODO
		
		$this->set('no_search', '1');
		
		$this->view('top/top_page');
	}
}
?>