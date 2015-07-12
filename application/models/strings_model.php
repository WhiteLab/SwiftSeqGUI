<?php
class Strings_model extends CI_Model{
	public $strings;
	
	public function __construct(){
		parent::__construct();
		
		$this -> strings = array(
			'header_strings' => array(
				'page_title_generate' => 'Generate SwiftSeq Workflow',
				'home_title' => 'SwiftSeq Workflow',
				'aboutus_title' => 'SwiftSeq Workflow | About Us',
				'contactus_title' => 'SwiftSeq Workflow | Contact Us',
				'upload_temp_title' => 'Add Custom Program',
				'prebuilt_workflows_title' => 'Download Pre-built Workflows',
				'prebuilt_workflows_js_init' => 'initPrebuiltWorkflows',
				'download_ref_files_title' => 'Download Reference Files'
			),
			'footer_strings' => array(
				'swift_link_url' => 'http://swift-lang.org/main/',
				'uchicago_link_url' => 'http://www.uchicago.edu/',
				'igsb_link_url' => 'http://www.igsb.anl.gov/',
				'moreinfo1' => 'Contact Us',
				'moreinfo2' => 'Follow Us',
				'moreinfo3' => 'About Us',
				'moreinfo1_url' => site_url('www/view/contactus'),
				'moreinfo2_url' => 'https://twitter.com/JasonJPitt',
				'moreinfo3_url' => site_url('www/view/aboutus')
			),
			'generate_strings' => array(
				'step1' => array(
					'title_bar' => 'Generate SwiftSeq Custom Configuration File | Information Gathering',
					'page_title' => 'Generate SwiftSeq Custom Configuration File',
					'not_configured' => 'This application has not yet been configured.'
				),
				'step2' => array(
					'page_title' => ''
				)
			),
			'app_config_strings' => array(
				'information_gathering' => array(
					'title_bar' => 'Create/Change Application Defaults | Information Gathering',
					'page_title' => 'Create/Change Application Defaults',
					'wrapper_box_header' => 'Information Gathering',
					'existing_questions_box_header' => 'Existing Questions'
				)
			),
			'buttons' => array(
				'next_step' => 'Next',
				'last_step' => 'Back',
				'add_question' => 'Add a Question',
				
			),
			'empty_message' => '[None]'
		);
	}
	
	public function get_strings(){
		return $this -> strings;
	}
}
