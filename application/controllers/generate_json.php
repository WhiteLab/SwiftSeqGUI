<?php
/**
 * Controller for generation of the automatic download. This controller is called immediately after user completes
 * the steps in the Generate controller.
 * 
 * The form information from the Generate controller is sent to this controller and processed into the final output
 * json file. This json file is stored as a single string and passed to the view, where it is stored in a hidden
 * input form element. An AJAX call is made as soon as the page is loaded, where the entire json string is sent
 * as post data; the download is then initiated.
 * 
 * There is probably a better way to do this, but I could not find/think of one. Feel free to implement a better
 * system if you know of one, and let me know how you did it: djf604@truman.edu
 */
class Generate_json extends CI_Controller{
	public $strings;
	
	public function __construct(){
		parent::__construct();
		
		$this -> load -> model('strings_model');
		$this -> strings = $this -> strings_model -> get_strings();
	}
	
	
	/**
	 * AJAX call, returns the json file as a download, containing the user specification to run SwiftSeq.
	 */
	public function init_download(){
		$output_json = $this -> input -> post('output_json');
		$user_filename = $this -> input -> post('user_filename');
		if(substr($user_filename, -5) === '.json')
			$user_filename = substr($user_filename, 0, -5);
		header('Content-Type: application/json');
		header('Content-Disposition: attachment; filename="' . $user_filename . '.json"');
		echo $output_json;
		exit(0);
	}
	
	public function process_form(){
		$output_json = array();
		
		//var_dump($this -> input -> post());exit;
		
		/* Get answers to information gathering */
		$restriction_flags = explode(' ', trim($this -> input -> post('restriction_flags')));
		foreach($restriction_flags as $rf){
			$output_json[$rf] = $this -> input -> post($rf);
		}
		
		/* Get all workflow steps */
		$workflow_steps = explode(' ', trim($this -> input -> post('workflow_steps')));
		
		/* Put program and parameters for each workflow step into output json */
		foreach($workflow_steps as $step){
			$output_json[$step] = array();
			
			/* If the user did not specify a specific workflow step, make it the string "default" */
			if($this -> input -> post('specify_' . $step) == FALSE){
				$output_json[$step] = 'default';
				continue;
			}
			
			$total = $this -> input -> post($step . '_num_progs');
			if($total != '0'){
				/* If user did specify workflow step, get all programs */
				for($i = 0, $k = 0; $i < $total; $i++, $k++){
					while($this -> input -> post($step . '_prog_select_' . $k) === FALSE) $k++;
					$prog_name = $this -> input -> post($step . '_prog_select_' . $k);
					$output_json[$step][$prog_name] = array();
					
					/* Get walltime for this program */
					if($this -> input -> post($step . '_walltime_' . $k) !== FALSE)
						$output_json[$step][$prog_name]['walltime'] = $this -> input -> post($step . '_walltime_' . $k);
					else
						$output_json[$step][$prog_name]['walltime'] = '100:00:00';
					
					/* Get this particular program's parameters */
					for($j = 0; $this -> input -> post($step . '_prog_params_select_' . $k . '_' . $j) !== ""; $j++){
						$param_name = $this -> input -> post($step . '_prog_params_select_' . $k . '_' . $j);
						$param_val = $this -> input -> post($step . '_prog_params_val_' . $k . '_' . $j);
						$output_json[$step][$prog_name]['params'][$param_name] = $param_val;
					}
				}
			}else{
				/* Get walltime for this program */
					if($this -> input -> post($step . '_walltime_0') !== FALSE)
						$output_json[$step]['walltime'] = $this -> input -> post($step . '_walltime_0');
					else
						$output_json[$step]['walltime'] = '100:00:00';
				
				for($j = 0; $this -> input -> post($step . '_prog_params_select_0_' . $j) !== ""; $j++){
					$param_name = $this -> input -> post($step . '_prog_params_select_0_' . $j);
					$param_val = $this -> input -> post($step . '_prog_params_val_0_' . $j);
					$output_json[$step]['params'][$param_name] = $param_val;
				}
			}
			
			
			/* If the workflow step was given a program, put the program and parameters into output json */
			/*$output_json[$step]['program'] = $this -> input -> post($step . '_prog_select');
			if($output_json[$step]['program'] === FALSE){
				unset($output_json[$step]['program']);
			}
			$output_json[$step]['params'] = array();*/
			
			/* Put parameters and respective values into output json */
			/*for($i = 0; $this -> input -> post($step . '_prog_params_select_' . $i) !== ""; $i++){
				$param_name = $this -> input -> post($step . '_prog_params_select_' . $i);
				$param_val = $this -> input -> post($step . '_prog_params_val_' . $i);
				$output_json[$step]['params'][$param_name] = $param_val;
			}*/
		}
		/* If the user specified a filename, use it; otherwise, default to "SwiftSeq_config_<DATE>.json" */
		$user_filename = $this -> input -> post('user_filename');
		if($user_filename === FALSE || $user_filename === ''){
			$user_filename = 'SwiftSeq_config_' . date('dMY');
		}
		
		$this -> view($output_json, $user_filename);
	}

	/**
	 * View function, initiates download via AJAX after the page is loaded.
	 */
	public function view($output_json, $user_filename){
		$header_vars['page_title'] = 'Download SwiftSeq JSON Configuration File';
		$header_vars['js_init'] = 'initDownload';
		$content_vars['output'] = json_encode($output_json);
		$content_vars['user_filename'] = $user_filename;
		$this -> load -> view('template/header', $header_vars);
		$this -> load -> view('content/download_view', $content_vars);
		$this -> load -> view('template/footer');
	}
}
