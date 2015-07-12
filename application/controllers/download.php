<?php
class download extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		$this -> load -> helper('file');
	}
	
	public function download_prebuilt_workflow($filename){
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$json_content = read_file('./includes/prebuilt_workflows/' . $filename);
		echo $json_content;
		exit(0);
	}
}
