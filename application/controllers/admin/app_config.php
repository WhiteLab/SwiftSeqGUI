<?php
/**
 * Controller for configuration of the user-end UI. There are four steps in this part of the program:
 * 		1. Configuration of Information Gathering
 * 			- Admin will enter in the question displayed the to user, the answers available to the user, as well as
 * 			  the key that will specify this question in the output json. This information can be used both to restrict
 * 			  certain programs from appear at subsequent steps based on user answers and at runtime of the program
 * 			  reading in the json file.
 * 		2. Configuration of Workflow Steps
 * 			- Admin will specify workflow steps.
 * 			- Workflow steps will either be abstract, offering program selection for the user, or programs themselves, only
 * 			  offering parameter selection to the user for that workflow step. The latter case is considered a standalone
 * 			  program.
 * 			- If a workflow step is to be hidden from the user under a parent checkbox, the name of that parent should be
 * 			  specified at this step. Workflow steps with the same parent will be hidden under the same parent checkbox.
 * 			- Restrictions can be placed on entire workflow steps at this point. Restrictions are based on answers to
 * 			  questions at the information gathering step; if a restriction is specified for a workflow step, it will
 * 			  not be available when the user specifies that particular question-answer pair.
 * 		3. Configuration of Programs and Parameters
 * 			- Admin will specify programs for workflow steps which offer program selection, as well as parameters for all programs
 * 			  and workflow steps which require them.
 * 			- Program readable name is what will be displayed to the user; program name is the key that will specify this program
 * 			  in the output json. Restrictions work the same way as for workflow steps. Parameters are specified for each program
 * 			  or workflow step as required.
 *		4. Configuration Overview
 * 			- This step allows the admin to check over the changes made before committing the changes to the server.
 * 			- Once the changes are confirmed, the temporary files that store data between configuration steps are deleted.
 */
class App_config extends CI_Controller{
	public $strings;
	public $controller_name;
	
	/**
	 * Constructor, load models and populate class variables.
	 */
	public function __construct(){
		parent::__construct();
		
		$this -> load -> helper('file');
		$this -> load -> model('json_model');
		$this -> load -> model('strings_model');
		$this -> strings = $this -> strings_model -> get_strings();
		
		$this -> controller_name = $this -> router -> fetch_class();
	}
	
	/**
	 * Default function, point to default view() function.
	 */
	public function index(){$this->view_info_gathering();}
	
	/**
	 * Process input from information gathering configuration step.
	 */
	public function process_info_gathering(){
		$restrictionFlags = array();
		/* Process existing questions */
		for($i = 0, $total = $this -> input -> post('num_ex_questions'), $k = 0; $i < $total; $i++, $k++){
			while($this -> input -> post('ex_question_name_' . $k) === FALSE) $k++;
			$rf = array();
			$rf['name'] = $this -> input -> post('ex_question_name_' . $k);
			$rf['content'] = $this -> input -> post('ex_question_content_' . $k);
			$rf['options'] = explode(';', str_replace(' ', '_', $this -> input -> post('ex_question_options_' . $k)));
			array_push($restrictionFlags, $rf);
		}
		
		/* Process new questions, if any */
		for($i = 0, $total = $this -> input -> post('num_new_questions'), $k = 0; $i < $total; $i++, $k++){
			while($this -> input -> post('new_question_name_' . $k) === FALSE) $k++;
			$rf = array();
			$rf['name'] = $this -> input -> post('new_question_name_' . $k);
			$rf['content'] = $this -> input -> post('new_question_content_' . $k);
			$rf['options'] = explode(';', str_replace(' ', '_', $this -> input -> post('new_question_options_' . $k)));
			array_push($restrictionFlags, $rf);
		}
		
		/* Encode data into json format, write it out to the temporary directory */
		$data = json_encode($restrictionFlags);
		write_file('./application/tmp/info_gathering_app_config_tmp.json', $data);
		
		/* Logically I should remove any existing restriction flags that have just been deleted.
		 * The way I'm passing the possible values into Selectize (jQuery plugin), though, it won't allow selection
		 * of restriction flags that don't exist. Even though I may be attempting to assign
		 * values that no longer exist, Selectize will ignore them.
		 */
		
		/* View next step */
		$this -> view_workflow_steps();
	}

	/**
	 * Process input from workflow steps configuration step.
	 */
	public function process_workflow_steps(){
		$workflow_steps = array();
		for($i = 0, $total = $this -> input -> post('num_workflow_steps'), $k = 0; $i < $total; $i++, $k++){
			while($this -> input -> post('workflow_step_name_' . $k) === FALSE) $k++;
			$step = array();
			/* Get name and parent */
			$step['name'] = str_replace(' ', '_', $this -> input -> post('workflow_step_name_' . $k));
			$step['parent'] = str_replace(' ', '_', $this -> input -> post('workflow_step_parent_' . $k));
			if($step['parent'] === '') $step['parent'] = 'false';
			
			/* Get omitPrograms */
			if($this -> input -> post('workflow_step_omitprograms_' . $k) !== FALSE){
				$step['omitPrograms'] = 'true';
			}else{
				$step['omitPrograms'] = 'false';
			}

			/* Get multiple programs */
			if($this -> input -> post('workflow_step_multiple_' . $k) !== FALSE){
				$step['multiplePrograms'] = 'true';
			}else{
				$step['multiplePrograms'] = 'false';
			}
			
			/* Get required workflow steps */
			if($this -> input -> post('workflow_step_required_' . $k) !== FALSE){
				$step['stepRequired'] = 'true';
			}else{
				$step['stepRequired'] = 'false';
			}
			
			/* Get array hierarchy for restrictionFlags */
			if($this -> input -> post('workflow_step_restrictions_' . $k) === ''){
				$step['restrictionFlags'] = 'none';
			}else{
				$step['restrictionFlags'] = array();
				$rfs = explode(',', $this -> input -> post('workflow_step_restrictions_' . $k));
				foreach($rfs as $rf){
					$rf_parts = explode(':', $rf);
					if(!isset($step['restrictionFlags'][$rf_parts[0]])){
						$step['restrictionFlags'][$rf_parts[0]] = array();
					}
					array_push($step['restrictionFlags'][$rf_parts[0]], $rf_parts[1]);
				}
			}

			/* Push step onto output array */
			array_push($workflow_steps, $step);
		}
		
		/* Encode data into json format, write it out to the temporary directory */
		$data = json_encode($workflow_steps);
		write_file('./application/tmp/workflow_steps_app_config_tmp.json', $data);
		
		/* View next step */
		$this -> view_progs_and_params();
	}

	/**
	 * Process input from programs and parameters configuration step.
	 */
	public function process_progs_and_params(){
		// TODO I feel like this input needs to be sanitized somehow? It's not going into a database
		// so maybe not, but I need to consider this further.
		$programs = array();
		$parameters = array();
		
		$workflow_steps = explode(' ', trim($this -> input -> post('workflow_steps')));
		foreach($workflow_steps as $step_name){
			/* Get programs and parameters for workflow steps that have programs */
			$programs[$step_name] = array();
			for($i = 0, $total = $this -> input -> post($step_name . '_num_progs'), $k = 0; $i < $total; $i++, $k++){
				while($this -> input -> post($step_name . '_prog_name_read_' . $k) === FALSE) $k++;
				$prog = array();
				$prog['name'] = $this -> input -> post($step_name . '_prog_name_' . $k);
				$prog['nameRead'] = str_replace(' ', '_', $this -> input -> post($step_name . '_prog_name_read_' . $k));
				
				/* Get array hierarchy for restrictionFlags */
				if($this -> input -> post($step_name . '_prog_restrictions_' . $k) === ''){
					$prog['restrictionFlags'] = 'none';
				}else{
					$prog['restrictionFlags'] = array();
					$rfs = explode(',', $this -> input -> post($step_name . '_prog_restrictions_' . $k));
					foreach($rfs as $rf){
						$rf_parts = explode(':', $rf);
						if(isset($rf_parts[0]) and isset($rf_parts[1])){
							if(!isset($prog['restrictionFlags'][$rf_parts[0]]))
								$prog['restrictionFlags'][$rf_parts[0]] = array();
							array_push($prog['restrictionFlags'][$rf_parts[0]], $rf_parts[1]);
						}
					}
				}
				
				/* Get documentation url */
				$prog['docUrl'] = $this -> input -> post($step_name . '_doc_url_' . $k);
				
				/* Get default walltime */
				$prog['defaultWalltime'] = $this -> input -> post($step_name . '_default_walltime_' . $k);
				
				/* Get program description */
				$prog['description'] = $this -> input -> post($step_name . '_desc_for_prog_' . $k);
				
				/* Push program onto output array */
				array_push($programs[$step_name], $prog);
				
				/* Push parameters onto output array */
				$parameters[$prog['name']] = explode(',', $this -> input -> post($step_name . '_params_for_prog_' . $k));
			}
			
			/* If the workflow step does not have program, get parameters */
			if($this -> input -> post($step_name . '_params') !== FALSE){
				$parameters[$step_name] = explode(',', $this -> input -> post($step_name . '_params'));
				
				/* Get documentation url and default walltime*/
				$workflow_steps_temp_json = json_decode(read_file('./application/tmp/workflow_steps_app_config_tmp.json'), TRUE);
				foreach($workflow_steps_temp_json as $i => $step_temp){
					if($step_temp['name'] == $step_name){
						$workflow_steps_temp_json[$i]['docUrl'] = $this -> input -> post($step_name . '_doc_url');
						$workflow_steps_temp_json[$i]['defaultWalltime'] = $this -> input -> post($step_name . '_default_walltime');
						$workflow_steps_temp_json[$i]['description'] = $this -> input -> post($step_name . '_desc_for_prog');
						break;
					}
				}
				
				/*Write out edited workflow_steps temporary file */
				$data = json_encode($workflow_steps_temp_json);
				write_file('./application/tmp/workflow_steps_app_config_tmp.json', $data);
			}
		}
		
		//var_dump($programs);exit;
		/* Encode data into json format, write it out to the temporary directory */
		$data_programs = json_encode($programs);
		write_file('./application/tmp/programs_app_config_tmp.json', $data_programs);
		$data_parameters = json_encode($parameters);
		write_file('./application/tmp/parameters_app_config_tmp.json', $data_parameters);
		
		/* View next step */
		$this -> view_confirmation_step();
	}

	/**
	 * Aggragate data from all steps, write them out to the default configuration json file. 
	 * Then delete generated temporary data files.
	 */
	public function process_final(){
		$restriction_flags = $this -> get_info_gathering();
		$workflow_steps = $this -> get_workflow_steps();
		$programs = $this -> get_programs();
		$parameters = $this -> get_parameters();
		
		$defaults_data = array(
			'restrictionFlags' => $restriction_flags,
			'steps' => $workflow_steps
		);
		
		write_file('./config/defaults.json', json_encode($defaults_data));
		write_file('./config/programs.json', json_encode($programs));
		write_file('./config/parameters.json', json_encode($parameters));
		
		delete_files('./application/tmp/');
		
		/* View app config confirmation */
		$this -> view_completion_step();
	}
	
	/*****************************************************************************/
	/**************************** AJAX Calls *************************************/
	/*****************************************************************************/
	
	/**
	 * AJAX call, gets new question configuration box.
	 */
	public function get_new_question_box($i){
		$content_vars['i'] = $i;
		$this -> load -> view('ajax/app_config_question', $content_vars);
	}
	
	/**
	 * AJAX call, gets new workflow step configuration row.
	 */
	public function get_new_workflow_step($i){
		$content_vars['i'] = $i;
		$this -> load -> view('ajax/app_config_workflow', $content_vars);
	}
	
	/**
	 * AJAX call, gets new program configuration box.
	 */
	public function get_new_program($i, $step_name){
		$content_vars['i'] = $i;
		$content_vars['step_name'] = $step_name;
		$this -> load -> view('ajax/app_config_program', $content_vars);
	}
	
	/*****************************************************************************/
	/**************************** JSON Parsing ***********************************/
	/*****************************************************************************/
	
	/**
	 * Retrieves defaults from default configuration json file.
	 */
	public function get_current_defaults($partial = FALSE){
		$defaults = $this -> json_model -> get_defaults_array($partial);
		return $defaults;
	}
	
	/**
	 * Attempts to get information gathering configuration from temporary file. If it does not exist,
	 * falls back on the current defauls configuration json file.
	 */
	public function get_info_gathering(){
		$defaults_ig = read_file('./application/tmp/info_gathering_app_config_tmp.json');
		if($defaults_ig === FALSE)
			return $this -> json_model -> get_defaults_array('restrictionFlags');
		return json_decode($defaults_ig, TRUE);
	}
	
	/**
	 * Attempts to get workflow steps configuration from temporary file. If it does not exist,
	 * falls back on the current defauls configuration json file.
	 */
	public function get_workflow_steps(){
		$defaults_steps = read_file('./application/tmp/workflow_steps_app_config_tmp.json');
		if($defaults_steps === FALSE)
			return $this -> json_model -> get_defaults_array('steps');
		return json_decode($defaults_steps, TRUE);
	}
	
	/**
	 * Attempts to get program configuration from temporary file. If it does not exist,
	 * falls back on the current defauls configuration json file.
	 */
	public function get_programs(){
		$defaults_programs = read_file('./application/tmp/programs_app_config_tmp.json');
		if($defaults_programs === FALSE)
			return $this -> json_model -> get_programs_for_workflow_step_array();
		return json_decode($defaults_programs, TRUE);
	}
	
	/**
	 * Attempts to get parameters configuration from temporary file. If it does not exist,
	 * falls back on the current defauls configuration json file.
	 */
	public function get_parameters(){
		$defaults_parameters = read_file('./application/tmp/parameters_app_config_tmp.json');
		if($defaults_parameters === FALSE)
			return $this -> json_model -> get_parameters_for_program_array();
		return json_decode($defaults_parameters, TRUE);
	}
	
	/*****************************************************************************/
	/**************************** View Methods ***********************************/
	/*****************************************************************************/
	
	/**
	 * View function for the information gathering configuration step.
	 */
	public function view_info_gathering(){
		$header_vars['page_title'] = 'Create/Change Application Defaults | Information Gathering';
		$header_vars['js_init'] = 'initAppConfigStep1';
		$content_vars['site_url'] = site_url();
		$content_vars['defaults_rf'] = $this -> get_info_gathering();
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/app_config_view_1', $content_vars);
		$this -> load -> view('template/footer');
	}
	
	/**
	 * View function for the workflow steps configuration step.
	 */
	public function view_workflow_steps(){
		$header_vars['page_title'] = 'Create/Change Application Defaults | Workflow Steps';
		$header_vars['js_init'] = 'initAppConfigStep2';
		$content_vars['site_url'] = site_url();
		$content_vars['workflow_steps'] = $this -> get_workflow_steps();
		$content_vars['restriction_flags'] = $this -> get_info_gathering();
		$content_vars['back_button_url'] = site_url('admin/' . $this -> controller_name . '/view_info_gathering');
		
		// TODO Add functionality to change order of workflow steps in checkbox sequence
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/app_config_view_2', $content_vars);
		$this -> load -> view('template/footer');
	}
	
	/**
	 * View function for the programs and parameters configuration step.
	 */
	public function view_progs_and_params(){
		$header_vars['page_title'] = 'Create/Change Application Defaults | Programs and Parameters';
		$header_vars['js_init'] = 'initAppConfigStep3';
		
		$content_vars['restriction_flags'] = $this -> get_info_gathering();
		$content_vars['workflow_steps'] = $this -> get_workflow_steps();
		$content_vars['programs'] = $this -> get_programs();
		$content_vars['params'] = $this -> get_parameters();
		$content_vars['site_url'] = site_url();
		$content_vars['back_button_url'] = site_url('admin/' . $this -> controller_name . '/view_workflow_steps');
		
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/app_config_view_3', $content_vars);
		$this -> load -> view('template/footer');
	}
	
	/**
	 * View function for the configuration overview confirmation step.
	 */
	public function view_confirmation_step(){
		$header_vars['page_title'] = 'Create/Change Application Defaults | Apply Defaults to Application';
		$header_vars['js_init'] = 'initAppConfigStep4';
		
		$content_vars['restriction_flags'] = $this -> get_info_gathering();
		$content_vars['workflow_steps'] = $this -> get_workflow_steps();
		$content_vars['programs'] = $this -> get_programs();
		$content_vars['params'] = $this -> get_parameters();
		$content_vars['site_url'] = site_url();
		$content_vars['back_button_url'] = site_url('admin/' . $this -> controller_name . '/view_progs_and_params');
		
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/app_config_view_4', $content_vars);
		$this -> load -> view('template/footer');
	}

	public function view_completion_step(){
		$header_vars['page_title'] = 'Create/Change Application Defaults | Complete';
		
		$content_vars['site_url'] = site_url();
		
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/app_config_view_final', $content_vars);
		$this -> load -> view('template/footer');
	}
}
