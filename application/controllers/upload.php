<?php
/**
 * Receives an uploaded file, making sure it is in json or plain text format.
 */
class upload extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Receives and processes the uploaded file
	 */
	public function process_upload(){
		$customprog = 'customprog';
		$maxfiesize = 1000000;
		
		try{
			/* Stop processing if file is undefined, multiple files, or a $_FILES corruption attack */
			if(!isset($_FILES[$customprog]['error']) || is_array($_FILES[$customprog]['error']))
				throw new RuntimeException('Invalid parameters.');
			
			/* Check for errors */
			switch($_FILES[$customprog]['error']){
				case UPLOAD_ERR_OK;
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown error(s).');
			}
			
			/* Check filesize */
			if($_FILES[$customprog]['size'] > $maxfiesize)
				throw new RuntimeException('Exceeded filesize limit.');
			
			/* Check MIME value to ensure this is a .json file */
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			var_dump($finfo -> file($_FILES[$customprog]['tmp_name']));
			if(FALSE === array_search($finfo -> file($_FILES[$customprog]['tmp_name']), array('json' => 'application/json', 'text' => 'text/plain'), TRUE))
				throw new RuntimeException('Invalid file format.');
			
			$this -> load -> helper('file');
			
			$json_data = read_file($_FILES[$customprog]['tmp_name']);
			
			var_dump(json_decode($json_data, TRUE));
			exit;
		}catch(RuntimeException $e){
			echo $e -> getMessage();
		}
	}
	
	public function index(){$this->view();}
	
	public function view(){
		$header_vars['page_title'] = 'Upoad Custom Program';
		$header_vars['js_init'] = 'initUpload';
		$content_vars['site_url'] = site_url();
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/upload_view', $content_vars);
		$this -> load -> view('template/footer');
	}
}
