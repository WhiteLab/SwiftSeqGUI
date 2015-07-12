<form id="app_config_form_3" method="post" action="<?php echo site_url('admin/app_config/process_progs_and_params'); ?>">
<div class="container">
	<div class="row">
		<h1>Create/Change Application Defaults</h1><br/>
		<div class="col-md-10 col-md-offset-1">
			<div class="display_box bottom_padding">
				<span class="display_box_header bottom_padding">Programs and Parameters</span>
				<?php if(empty($workflow_steps)): ?>
					<span><strong>You should probably add some workflow steps.</strong></span>
				<?php else:
				foreach($workflow_steps as $step):
					$omit_prog = $step['omitPrograms'] === 'true' ? TRUE : FALSE; ?>
				<div class="panel panel-primary bottom_padding pp_data">
					<?php if(!$omit_prog): ?>
					<input type="hidden" name="<?php echo $step['name']; ?>_num_progs" class="pp_num_progs" value="<?php echo isset($programs[$step['name']]) ? count($programs[$step['name']]) : '0'; ?>" />
					<input type="hidden" class="pp_num_progs_i" value="<?php echo isset($programs[$step['name']]) ? count($programs[$step['name']]) : '0'; ?>" />
					<input type="hidden" class="pp_workflow_step" value="<?php echo $step['name']; ?>" />
					<?php endif; ?>
					<div class="panel-heading">
						<h3 class="panel-title exq_content" style="display: inline-block;">
							<?php echo str_replace('_', ' ', $step['name']); ?>
						</h3>
						<?php if(!$omit_prog): ?>
						<button type="button" class="btn btn-primary btn-xs float_right pp_add_prog_btn">
							<span class="glyphicon glyphicon-plus"></span>&nbsp;Add Program
						</button>
						<?php endif; ?>
					</div>
					<table class="table prog_table">
						<?php if(!$omit_prog): ?>
						<thead>
							<th class="col-md-3">Readable Name</th>
							<th class="col-md-2">Program Name</th>
							<th class="col-md-5">Restrictions</th>
							<th class="col-md-2"></th>
						</thead>
						<?php endif; ?>
						<tbody>
						<!-- If not omit program: -->
							<?php if(!$omit_prog): if(isset($programs[$step['name']]))
							foreach($programs[$step['name']] as $program_i => $program):
							if(empty($program)) continue;
							$existing_rf = '';
							if($program['restrictionFlags'] !== 'none')
							foreach($program['restrictionFlags'] as $rf_key => $rf_vals)
							foreach($rf_vals as $rf_val){
								$existing_rf .= $rf_key . ':' . $rf_val . ',';
							} $existing_rf = substr($existing_rf, 0, -1); ?>
							<tr class="pp_data_tr">
								<td class="pp_data_td">
									<span><h4><strong><?php echo str_replace('_', ' ', $program['nameRead']); ?></strong></h4></span>
									<input type="hidden" class="pp_prog_name_read" name="<?php echo $step['name']; ?>_prog_name_read_<?php echo $program_i; ?>" value="<?php echo $program['nameRead']; ?>" />
								</td>
								<td class="pp_data_td">
									<span><?php echo $program['name']; ?></span>
									<input type="hidden" class="pp_prog_name_read" name="<?php echo $step['name']; ?>_prog_name_<?php echo $program_i; ?>" value="<?php echo $program['name']; ?>" />
								</td>
								<td>
									<input type="text" class="pp_restriction_select" name="<?php echo $step['name']; ?>_prog_restrictions_<?php echo $program_i; ?>" style="width: 100%;" value="<?php echo $existing_rf; ?>" />
								</td>
								<td>
									<div class="float_right">
										<button type="button" class="btn btn-danger btn-xs float_right pp_delete_btn" data-deleted="zero">
											<span class="glyphicon glyphicon-remove"></span>&nbsp;<span class="pp_delete_btn_text">Delete</span>
										</button>
										<button type="button" class="btn btn-primary btn-xs float_right right-pad5 pp_edit_btn" data-edited="false">
											<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
										</button>
									</div>
									<input type="hidden" class="pp_data_n" value="<?php echo count($programs[$step['name']]); ?>" />
								</td>
							</tr>
							<!-- Row: documentation url and default walltime-->
							<tr>
								<td colspan="2" style="border:none;">
									<label for="<?php echo $step['name']; ?>_doc_url"><strong>Documentation URL:</strong>&nbsp;</label>
									<input type="text" name="<?php echo $step['name']; ?>_doc_url_<?php echo $program_i; ?>" class="pad5" value="<?php if(isset($program['docUrl']))echo $program['docUrl']; ?>">
								</td>
								<td colspan="2" style="border:none;">
									<label for="<?php echo $step['name']; ?>_default_walltime"><strong>Default walltime:</strong>&nbsp;</label>
									<input type="text" name="<?php echo $step['name']; ?>_default_walltime_<?php echo $program_i; ?>" class="pad5" value="<?php if(isset($program['defaultWalltime']))echo $program['defaultWalltime']; ?>">
								</td>
							</tr>
							<!-- Row: program description -->
							<tr>
								<td colspan="4" style="border-top:0">
									<span><strong>Program description:</strong>&nbsp;</span><br/>
									<textarea rows="6" name="<?php echo $step['name']; ?>_desc_for_prog_<?php echo $program_i; ?>" style="width:100%" ><?php if(isset($program['description']))echo $program['description']; ?></textarea>
								</td>
							</tr>
							<!-- Row: parameters -->
							<tr>
								<td colspan="4" style="border-top: 0;">
									<span><strong>Parameters:</strong>&nbsp;</span>
									<!-- TODO This probably doesn't need to be a key value pair, just a list of strings -->
									<?php $existing_params = '';
										if(isset($params[$program['name']]))
										foreach($params[$program['name']] as $param_val)
											$existing_params .= $param_val . ',';
										$existing_params = substr($existing_params, 0, -1); ?>
									<input type="text" class="params_select" name="<?php echo $step['name']; ?>_params_for_prog_<?php echo $program_i; ?>" style="width: 100%;" value="<?php echo $existing_params; ?>"/>
								</td>
							</tr>
							<?php endforeach; ?>
							<!-- If omit program: -->
							<?php else: ?>
							<tr>
								<td colspan="2" style="border:none;">
									<label for="<?php echo $step['name']; ?>_doc_url"><strong>Documentation URL:</strong>&nbsp;</label>
									<input type="text" name="<?php echo $step['name']; ?>_doc_url" class="pad5" value="<?php if(isset($step['docUrl']))echo $step['docUrl']; ?>">
								</td>
								<td colspan="2" style="border:none;">
									<label for="<?php echo $step['name']; ?>_default_walltime"><strong>Default walltime:</strong>&nbsp;</label>
									<input type="text" name="<?php echo $step['name']; ?>_default_walltime" class="pad5" value="<?php if(isset($step['defaultWalltime']))echo $step['defaultWalltime']; ?>">
								</td>
							</tr>
							<tr>
								<td colspan="4" style="border-top:0">
									<span><strong>Program description:</strong>&nbsp;</span><br/>
									<textarea rows="6" name="<?php echo $step['name']; ?>_desc_for_prog" style="width:100%" ><?php if(isset($program['description']))echo $program['description']; ?></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="4" style="border-top: 0;">
									<span><strong>Parameters:</strong>&nbsp;</span>
									<?php $existing_params = '';
										if(isset($params[$step['name']]))
										foreach($params[$step['name']] as $param_val)
											$existing_params .= $param_val . ',';
										$existing_params = substr($existing_params, 0, -1); ?>
									<input type="text" class="params_select" name="<?php echo $step['name']; ?>_params" style="width: 100%;" value="<?php echo $existing_params; ?>"/>
								</td>
							</tr>	
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php endforeach; endif;?>
			</div>
			<a href="<?php echo $back_button_url; ?>" class="btn btn-primary btn-lg" role="button">
				<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Back
			</a>
			<button class="btn btn-primary btn-lg float_right">
				Next&nbsp;<span class="glyphicon glyphicon-arrow-right"></span>
			</button>
		</div>
	</div>
</div>
<input type="hidden" name="workflow_steps" value="<?php foreach($workflow_steps as $step) echo $step['name'] . ' '; ?>" />
</form>
<input type="hidden" id="site_url" value="<?php echo $site_url; ?>" />
<input type="hidden" id="restrictionsJson" value='<?php echo json_encode($restriction_flags); ?>' />

