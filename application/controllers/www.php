<?php
class www extends CI_Controller{
	public $strings;
	
	public function __construct(){
		parent::__construct();
		
		$this -> load -> model('strings_model');
		$this -> strings = $this -> strings_model -> get_strings();
	}
	
	public function index(){$this -> view('home');}
	
	public function view($page){
		$header_vars['page_title'] = $this -> strings['header_strings'][$page . '_title'];
		if(isset($this -> strings['header_strings'][$page . '_js_init']))
			$header_vars['js_init'] = $this -> strings['header_strings'][$page . '_js_init'];
		
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('www/' . $page);
		$this -> load -> view('template/footer');
	}
}
