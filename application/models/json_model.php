<?php
class Json_model extends CI_Model{
	public function __construct(){
		parent::__construct();
		
		$this -> load -> helper('file');
	}
	
	/*
	 * Reads defaults.json from the server and returns it as a raw string
	 */
	private function get_defaults(){
		$defaults = read_file('./config/defaults.json');
		/*$defaults = array(
			'restrictionFlags' => array(
				0 => array(
					'name' => 'type_data',
					'content' => 'What type of data is this?',
					'options' => array('Tumor_Normal_Pair', 'Germline')
				),
				1 => array(
					'name' => 'type_run',
					'content' => 'What type of run is this?',
					'options' => array('Processing', 'Genotyping', 'Processing_and_Genotyping')
				),
			),
			'steps' => array(
				0 => array(
					'name' => 'Genotyper',
					'parent' => 'false',
					'omitPrograms' => 'false',
					'restrictionFlags' => array(
						'type_run' => array('Processing')
					)
				),
				1 => array(
					'name' => 'Aligner',
					'parent' => 'false',
					'omitPrograms' => 'false',
					'restrictionFlags' => array(
						'type_run' => array('Genotyping')
					)
				),
				2 => array(
					'name' => 'Mark_Duplicates',
					'parent' => 'Other',
					'omitPrograms' => 'true',
					'restrictionFlags' => array(
						'type_run' => array('Genotyping')
					)
				),
				3 => array(
					'name' => 'Indel_Realignment',
					'parent' => 'Other',
					'omitPrograms' => 'true',
					'restrictionFlags' => array(
						'type_run' => array('Genotyping')
					)
				),
				4 => array(
					'name' => 'Base_Quality_Score_Recalibration',
					'parent' => 'Other',
					'omitPrograms' => 'true',
					'restrictionFlags' => array(
						'type_run' => array('Genotyping')
					)
				),
				5 => array(
					'name' => 'SNPeff',
					'parent' => 'Other',
					'omitPrograms' => 'true',
					'restrictionFlags' => array(
						'type_run' => array('Processing')
					)
				)
				// restrictionFlags can be string 'none' if no restrictions
			)
		);*/
		if($defaults !== FALSE)
			return $defaults;
		return '[]';
	}
	
	/*
	 * Reads parameters.json from the server and returns it as a raw string
	 */
	private function get_parameters(){
		$params = read_file('./config/parameters.json');
		/*$params = array(
			'genotyper1-1.3.4' => array(
				'in_file',
				'out_file',
				'n_runs',
				'no-mixed',
				'no-discordant',
				'no-coverage-search',
				'in_file1',
				'out_file1',
				'n_runs1',
				'no-mixed1',
				'no-discordant1',
				'no-coverage-search1'
			),
			'genotyper2-4.2.1a' => array(
				'param1',
				'threads',
				'output'
			),
			'SNPeff' => array(
				'one',
				'two',
				'three',
				'four'
			)
		);*/
		if($params !== FALSE)
			return $params;
		return '[]';
	}
	
	/*
	 * Reads programs.json from the server and returns it as a raw string
	 */
	private function get_programs(){
		$programs = read_file('./config/programs.json');
		/*$programs = array(
			'Genotyper' => array(
				0 => array(
					'name' => 'genotyper1-1.3.4',
					'nameRead' => 'Genotyper 1 ver: 1.3.4', //This is unnecessary, since it's always the same as the name, look into removing it
					'restrictionFlags' => 'none'
				),
				1 => array(
					'name' => 'genotyper2-4.2.1a',
					'nameRead' => 'Genotyper 2 ver: 4.2.1a',
					'restrictionFlags' => array(
						'type_data' => array('Tumor_Normal_Pair')
					)
				),
				2 => array(
					'name' => 'tumor_genotyper7.2',
					'nameRead' => 'Tumor Genotyper 7.2',
					'restrictionFlags' => 'none'
				)
			),
			'Aligner' => array(
				0 => array(
					'name' => 'tophat2-2.2.1',
					'nameRead' => 'TopHat 2 ver: 2.2.1',
					'restrictionFlags' => 'none'
				),
				1 => array(
					'name' => 'STAR-1.4',
					'nameRead' => 'STAR Aligner ver: 1.4',
					'restrictionFlags' => 'none'
				)
			),
			'Mark_Duplicates' => array(),
			'Indel_Realignment' => array(),
			'Base_Quality_Score_Recalibration' => array(),
			'SNPeff' => array()
		);*/
		if($programs !== FALSE)
			return $programs;
		return '[]';
	}
	
	public function get_defaults_array($partial = FALSE){
		$defaults = json_decode($this -> get_defaults(), TRUE);
		if($partial !== FALSE){
			if(!isset($defaults[$partial]))
				return array();
			return $defaults[$partial];
		}
		return $defaults;
	}
	
	public function get_defaults_json($partial = FALSE){
		return json_encode($this -> get_defaults_array($partial));
	}
	
	public function get_parameters_for_program_array($program = 'all'){
		$params = json_decode($this -> get_parameters(), TRUE);
		if($program == 'all')
			return $params;
		if(!isset($params[urldecode($program)]))
			return array();
		return $params[urldecode($program)];
	}
	
	public function get_parameters_for_program_json($program = 'all'){
		return json_encode($this -> get_parameters_for_program_array($program));
	}
	
	public function get_programs_for_workflow_step_array($workflow_step = 'all'){
		$programs = json_decode($this -> get_programs(), TRUE);
		if($workflow_step == 'all')
			return $programs;
		if(!isset($programs[urldecode($workflow_step)]))
			return array();
		return $programs[urldecode($workflow_step)];
	}
	
	public function get_programs_for_workflow_step_json($workflow_step = 'all'){
		return json_encode($this -> get_programs_for_workflow_step_array($workflow_step));
	}
	/* For future use */
	public function from_file(){
		return read_file('./config/defaults.json');
	}
}
