<form id="swift_seq_form" action="<?php echo site_url('generate_json/process_form'); ?>" method="post">
<div class="container">
	<div class="row">
		<h1>Generate Workflow Configuration</h1>
	<div class="col-md-10 col-md-offset-1">
		<!--------------------------------------------------------->
		<!----------------- Begin Application --------------------->
		<!--------------------------------------------------------->
		<div id="infoGathering" class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Gathered Information</h3>
			</div>
				<ul class="list-group">
					<?php foreach($info_gathering as $info): ?>
						<li class="list-group-item">
							<span class="text-primary">
								<strong><?php echo $info['content']; ?></strong>
							</span>
							<span class="text-info">
								&nbsp;:&nbsp;<?php echo $info['option']; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
		</div>
		<div id="specify" class="panel panel-primary bottom_padding">
			<div class="panel-heading">
				<h3 class="panel-title">I would like to specify:</h3>
			</div>
			<div class="panel-body">
			<?php foreach($checkboxes as $key => $content):
				if($key == 'top'): 
					foreach($content as $name): 
						$step_name = $name;
						$step_name_readable = str_replace('_', ' ', $step_name);?>
						<label for="specify_<?php echo $step_name; ?>">
							<li class="list-group-item">
						<input type="checkbox" id="specify_<?php echo $step_name; ?>" name="specify_<?php echo $step_name; ?>" class="specify_checkbox<?php if(in_array($step_name, $required_steps)) echo ' required' ?>">
						&nbsp;<?php echo $step_name_readable; ?></li></label>
					<?php endforeach; ?>
				<?php else:
					$parent_name = $key;
					$parent_name_readable = str_replace('_', ' ', $key); ?>
					<label for="specify_<?php echo $parent_name; ?>"><li class="list-group-item">
					<input type="checkbox" id="specify_<?php echo $parent_name; ?>" name="specify_<?php echo $parent_name; ?>" class="specify_checkbox_parent">
					&nbsp;<?php echo $parent_name_readable; ?></li></label>
					<div class="indent _hidden">
					<?php foreach($content as $name):
						$step_name = $name;
						$step_name_readable = str_replace('_', ' ', $step_name);?>
						<label for="specify_<?php echo $step_name; ?>"><li class="list-group-item">
						<input type="checkbox" id="specify_<?php echo $step_name; ?>" name="specify_<?php echo $step_name; ?>" class="specify_checkbox indent<?php if(in_array($step_name, $required_steps)) echo ' required' ?>">
						&nbsp;<?php echo $step_name_readable; ?></li></label>
					<?php endforeach; ?>
					</div>
				<?php endif;
			endforeach;?>
			</div>
		</div>
		<?php foreach($workflow_steps as $step): $step_name_readable = str_replace('_', ' ', $step['name']); ?>
		<div id="<?php echo $step['name']; ?>" class="display_box bottom_padding _hidden">
			<span class="display_box_header bottom_padding"><?php echo $step_name_readable; ?></span>
			<!-- Put (?) icon if this workflow step has no programs -->
			<?php if($step['omitPrograms'] == 'true'): if(!empty($step['docUrl'])):?>
			<span data-docurl="<?php echo $step['docUrl']; ?>" class="anchorDocUrl">
				<!--<input type="hidden" data-docurl="<?php echo $step['docUrl']; ?>" />-->
				<img src="<?php echo base_url('includes/help.png'); ?>" class="help_icon" style="vertical-align: -3px !important">
			</span>
			<?php endif; endif;?>
			<input type="hidden" name="<?php echo $step['name']; ?>_num_progs" value="<?php echo $step['omitPrograms'] == 'false' ? '1' : '0'; ?>" />
			<div id="<?php echo $step['name']; ?>ProgramWrapper">
				<!-- Begin workflow step program specification box -->
				<div id="<?php echo $step['name']; ?>_prog_0" class="display_box bottom_padding display_box_prog_params" data-omitprograms="<?php echo $step['omitPrograms']; ?>" data-prognum="0">
					<input type="hidden" class="default_params_lookup" value="<?php echo $step['name']; ?>" />
					<?php if($step['omitPrograms'] == 'false'): ?>
					<span class="display_box_subheader">Program</span>
					<select id="<?php echo $step['name']; ?>_prog_select_0" name="<?php echo $step['name']; ?>_prog_select_0" class="prog_select width40" title placeholder="Select a program...">
						<!-- The $default_doc_url is grabbed as the first docUrl available from the
							list of programs. This is really dirty, but it's a hack for now -->
						<?php $default_doc_url = FALSE;
							foreach($programs[$step['name']] as $prog): 
								if($default_doc_url === FALSE){$default_doc_url = $prog['docUrl'];}?>
							<option value="<?php echo $prog['name']; ?>"><?php echo str_replace('_', ' ', $prog['nameRead']); ?></option>
						<?php endforeach; ?>
					</select>
					<?php if(!empty($prog['docUrl'])): ?>
					<!--<input type="hidden" data-docurl="<?php echo $step['docUrl']; ?>" class="anchorDocUrl" />-->
					<span data-docurl="<?php echo $default_doc_url; ?>" class="anchorDocUrl">
						<img src="<?php echo base_url('includes/help.png'); ?>" class="help_icon">
					</span>
					<?php endif; ?>
					<br/>
					<?php endif;?>
					<?php $default_walltime = '24:00:00';
						if($step['omitPrograms'] == 'true'){
							if(!empty($step['defaultWalltime'])) $default_walltime = $step['defaultWalltime'];
						}else{
							if(!empty($prog['defaultWalltime'])) $default_walltime = $prog['defaultWalltime'];
						}
					?>
					<label for="<?php echo $step['name']; ?>_walltime_0">Walltime:&nbsp;&nbsp;</label>
					<input type="text" id="<?php echo $step['name']; ?>_walltime_0" name="<?php echo $step['name']; ?>_walltime_0" class="inputWalltime" value="<?php echo $default_walltime; ?>"/>
					
					<span class="display_box_subheader">Parameters</span>
					
					<div class="prog_params_select_wrapper_div" data-workflowstep="<?php echo $step['name']; ?>" data-prognum="0">
						<input type="hidden" class="prog_params_select_num" value="1" />
						<div class="prog_params_select_div">
							<!-- id is "<step_name>_prog_params_<"select"|"val">_<prog_id>_<param_id>" -->
							<select id="<?php echo $step['name']; ?>_prog_params_select_0_0" name="<?php echo $step['name']; ?>_prog_params_select_0_0" class="prog_params_select width40" placeholder="Search for or select a parameter..."></select>
							<input type="text" id="<?php echo $step['name']; ?>_prog_params_val_0_0" name="<?php echo $step['name']; ?>_prog_params_val_0_0" class="prog_params_val inputProgParams width40"/>
						</div>
					</div>
				</div>
				<!-- End workflow step program specification box -->
			</div>
			<?php if($step['multiplePrograms'] == 'true'): ?>
				<button class="btn btn-primary addButton" type="button">
					<span class="glyphicon glyphicon-plus"></span>&nbsp;Add Program
				</button>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
		<br/>
		<div class="float_right">
			<label for="user_filename">Filename for download:&nbsp;</label><input type="text" id="user_filename" name="user_filename" class="pad5 bottom_padding"/>
			<br/>
			<button class="btn btn-primary float_right" id="form_submit">
				<span class="glyphicon glyphicon-download-alt"></span>&nbsp;Generate JSON
			</button>
		</div>
		<a href="<?php echo $back_button_url; ?>" class="btn btn-primary btn-lg" role="button">
			<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Back
		</a>
		<!------------------------------------------------------------------->
		<!------------------- End Application ------------------------------->
		<!------------------------------------------------------------------->
	</div>
	</div>
</div>
<input type="hidden" id="site_url" value="<?php echo $site_url; ?>" />
<?php foreach($restriction_flags as $rf_key => $rf): ?>
	<input type="hidden" name="<?php echo $rf_key; ?>" value="<?php echo $rf; ?>" />
<?php endforeach; ?>
<input type="hidden" name="workflow_steps" value="<?php foreach($workflow_steps as $step) echo $step['name'] . ' '; ?>" />
<input type="hidden" name="restriction_flags" value="<?php foreach($restriction_flags as $rf_key => $rf) echo $rf_key . ' '; ?>" />
<input type="hidden" id="restriction_flags_json" value='<?php echo json_encode($restriction_flags); ?>' />
</form>
