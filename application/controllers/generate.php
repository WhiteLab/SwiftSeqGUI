<?php
/**
 * Controller for the user-end UI. There are two steps in this part of the program:
 * 		1. Information Gathering
 * 			- User will answer questions, one answer per question (radio boxes)
 * 			- Based on user answers, some workflow steps or programs may be unselectable at the next step
 * 		2. Program Specification
 * 			- For each workflow step that can have a variable program, user will be able to select from a pre-defined
 * 			  list, or select his or her own uploaded program
 * 			- For each workflow step that does not have a variable program, as well as for programs themselves, user
 * 			  will select a variable number of parameters from a pre-defined list or specify his or her own, and specify
 * 			  their values
 * 			- User will also specify the filename for download, format "<user_defined_filename>.json"; if not filename is
 * 			  specified by the user, it will default to "SwiftSeq_config_<DATE>.json"
 */
class Generate extends CI_Controller{
	public $controller_name;
	
	/**
	 * Constructor, load models and populate global variables.
	 */
	public function __construct(){
		parent::__construct();
		
		$this -> load -> model('json_model');
		$this -> load -> model('strings_model');
		$this -> strings = $this -> strings_model -> get_strings();
		
		$this -> controller_name = $this -> router -> fetch_class();
	}
	
	/**
	 * Default function, points to default view() function.
	 */
	public function index(){$this->view_step1();}
	
	/**
	 * Process user input from information gathering step.
	 */
	public function process_step1(){
		$restriction_flags_names = explode(' ', trim($this -> input -> post('restriction_flags')));
		$restriction_flags = array();
		$info_gathering = array();
		foreach($restriction_flags_names as $rf_name){
			$restriction_flags[$rf_name] = $this -> input -> post($rf_name);
			$info_gathering[$rf_name]['content'] = $this -> input -> post($rf_name . '_content');
			$info_gathering[$rf_name]['option'] = str_replace('_', ' ', $this -> input -> post($rf_name));
		}
		
		/* Remove workflow steps that are restricted by information gathering */
		$workflow_steps = $this -> json_model -> get_defaults_array('steps');
		foreach($workflow_steps as $step_i => $step){
			if($step['restrictionFlags'] === 'none') continue;
			foreach($restriction_flags as $rf_key => $rf_val){
				if(!array_key_exists($rf_key, $step['restrictionFlags'])) continue;
				foreach($step['restrictionFlags'][$rf_key] as $step_rf_val){
					if($step_rf_val === $rf_val){
						unset($workflow_steps[$step_i]);
					}
				}
			}
		}
		
		/* Remove programs that are restricted by information gathering */
		$programs = $this -> json_model -> get_programs_for_workflow_step_array();
		foreach($workflow_steps as $step){
			foreach($programs[$step['name']] as $program_i => $program){
				if(empty($program) || $program['restrictionFlags'] === 'none') continue;
				foreach($restriction_flags as $rf_key => $rf_val){
					if(!array_key_exists($rf_key, $program['restrictionFlags'])) continue;
					foreach($program['restrictionFlags'][$rf_key] as $step_rf_val){
						if($step_rf_val === $rf_val){
							unset($programs[$step['name']][$program_i]);
						}
					}
				}
			}
		}
		
		/* Flag required workflow steps */
		$required_steps = array();
		foreach($workflow_steps as $step){
			if($step['stepRequired'] == 'true'){
				array_push($required_steps, $step['name']);
			}
		}
		
		$this -> view_step2($workflow_steps, $restriction_flags, $info_gathering, $programs, $required_steps);
	}
	
	/**
	 * AJAX call, returns json-formatted array of parameters for a specific program through
	 * the json_model model object.
	 * 
	 * @param String $prog_name Name of the program to get parameters for.
	 * @return String Json-formatted array of parameters for a specific program.
	 */
	public function get_parameters_for_program($prog_name){
		echo $this -> json_model -> get_parameters_for_program_json($prog_name);
		exit(0);
	}
	
	public function get_programs_for_workflow_step($workflow_step = 'all'){
		echo $this -> json_model -> get_programs_for_workflow_step_json($workflow_step);
		exit(0);
	}
	
	public function get_info_box($workflow_step, $program){
		$progs = $this -> json_model -> get_programs_for_workflow_step_array($workflow_step);
		foreach($progs as $prog){
			if($prog['name'] === $program){
				$content['docUrl'] = $prog['docUrl'];
				$content['description'] = $prog['description'];
			}
		}
		$this -> load -> view('ajax/generate_info_box', $content);
		//exit(0);
	}
	
	/**
	 * AJAX call, returns another program selection box for a specific workflow step
	 */
	public function get_program_box($step_name, $i){
		$workflow_steps = $this -> json_model -> get_defaults_array('steps');
		//$content_vars['programs'] = $this -> json_model -> get_programs_for_workflow_step_array();
		//$content_vars['programs'] = $this -> global_program;
		
		$restriction_flags = json_decode($this -> input -> post('programs'));
		$programs = $this -> json_model -> get_programs_for_workflow_step_array();
		foreach($workflow_steps as $step){
			foreach($programs[$step['name']] as $program_i => $program){
				if(empty($program) || $program['restrictionFlags'] === 'none') continue;
				foreach($restriction_flags as $rf_key => $rf_val){
					if(!array_key_exists($rf_key, $program['restrictionFlags'])) continue;
					foreach($program['restrictionFlags'][$rf_key] as $step_rf_val){
						if($step_rf_val === $rf_val){
							unset($programs[$step['name']][$program_i]);
						}
					}
				}
			}
		}
		
		$content_vars['programs'] = $programs;
		$content_vars['i'] = $i;
		foreach($workflow_steps as $steps){
			if($steps['name'] == $step_name){
				$content_vars['step'] = $steps;
				break;
			}
		}
		$this -> load -> view('ajax/generate_program_box', $content_vars);
	}
	
	/**
	 * Formats data from $workflow_steps in an array that denotes checkbox hierarchy, then
	 * passed to the view where it is interpreted.
	 * 
	 * @param array $workflow_steps Array of workflow steps, pulled directory from the app config json.
	 * @return array An array that is a format easily interpreted by the view.
	 */
	public function get_checkbox_array($workflow_steps){
		/* Create an empty array, add an element for top level checkboxes */
		$checkboxes = array(
			'top' => array()
		);
		/* Loop through workflow steps, adding them to either top level, or below their parent*/
		foreach($workflow_steps as $step){
			if($step['parent'] == 'false'){
				array_push($checkboxes['top'], $step['name']);
			}else{
				$parent = $step['parent'];
				if(!isset($checkboxes[$parent])){
					$checkboxes[$parent] = array();
				}
				array_push($checkboxes[$parent], $step['name']);
			}
		}
		return $checkboxes;
	}
	
	/**
	 * View function for the information gathering step.
	 */
	public function view_step1(){
		$header_vars['page_title'] = 'Generate SwiftSeq JSON Configuration File';
		$header_vars['js_init'] = 'initGenerateStep1';
		$content_vars['site_url'] = site_url();
		$content_vars['defaults'] = json_decode($this -> json_model -> get_defaults_json(), TRUE);
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/generate_view_1', $content_vars);
		$this -> load -> view('template/footer');
	}
	
	/**
	 * View function for the program specification step.
	 */
	public function view_step2($workflow_steps, $restriction_flags, $info_gathering, $programs, $required_steps){
		$header_vars['page_title'] = 'Generate SwiftSeq JSON Configuration File';
		$header_vars['js_init'] = 'initGenerateStep2';
		$content_vars['workflow_steps'] = $workflow_steps;
		$content_vars['restriction_flags'] = $restriction_flags;
		$content_vars['info_gathering'] = $info_gathering; /* To display answers to questions the user just answered */
		$content_vars['programs'] = $programs;
		$content_vars['checkboxes'] = $this -> get_checkbox_array($workflow_steps);
		$content_vars['required_steps'] = $required_steps;
		$content_vars['site_url'] = site_url();
		$content_vars['back_button_url'] = site_url($this -> controller_name . '/view_step1');
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/generate_view_2', $content_vars);
		$this -> load -> view('template/footer');
	}
}
