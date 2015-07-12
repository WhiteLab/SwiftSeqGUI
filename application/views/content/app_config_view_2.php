<form action="<?php echo site_url('admin/app_config/process_workflow_steps'); ?>" method="post">
<div class="container">
	<div class="row">
		<h1>Create/Change Application Defaults</h1><br/>
		<div class="col-md-10 col-md-offset-1">
			<div class="display_box bottom_padding">
				<span class="display_box_header bottom_padding">Workflow Steps</span>
				<div class="panel panel-primary top_padding">
					<div class="panel-heading">
						<h3 class="panel-title exq_content" style="display: inline-block;">Existing Workflow Steps</h3>
						<button type="button" class="btn btn-primary btn-xs float_right wf_add_btn_in">
							<span class="glyphicon glyphicon-plus"></span>&nbsp;Add Workflow Step
						</button>
					</div>
					<div class="panel-body">
						<div class="alert alert-info bottom_padding" role="alert">
							<span class="text-info">
								<strong>Workflow steps</strong> with parents are initially hidden, visible only when a checkbox label with
								the parent is checked. Selecting a <strong>restriction</strong> for a <strong>workflow step</strong> will
								hide it from the user when the condition is met during information gathering. A program marked as <strong>standalone </strong>
								will not allow for program selection because the <strong>workflow step</strong> is assumed to be a fixed program of itself. Parameter selection
								will still be available.
							</span>
						</div>
						<div id="workflowAlert" class="alert alert-info _hidden" style="margin-bottom: 0;" role="alert" data-shown="false">
							<span class="text-info">
								To specify a <strong>workflow step</strong> as top-level, type <kbd>false</kbd> in the field for parent, or leave it blank.
								Otherwise, parent names must exactly match among children.
							</span>
						</div>
					</div>
					<table class="table table-hover">
						<thead>
						<tr>
							<th class="col-md-2">Workflow Step</th>
							<th class="col-md-1">Parent</th>
							<th class="col-md-1">Standalone</th>
							<th class="col-md-1">Multiple</th>
							<th class="col-md-1">Required</th>
							<th class="col-md-4">Restrictions</th>
							<th class="col-md-2"></th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($workflow_steps as $i => $step):
							$existing_rf = '';
							if($step['restrictionFlags'] !== 'none')
							foreach($step['restrictionFlags'] as $rf_key => $rf_vals)
							foreach($rf_vals as $rf_val){
								$existing_rf .= $rf_key . ':' . $rf_val . ',';
							} $existing_rf = substr($existing_rf, 0, -1); ?>
						<tr>
							<td class="wf_data_td">
								<span><?php echo str_replace('_', ' ', $step['name']); ?></span>
								<input type="hidden" name="workflow_step_name_<?php echo $i; ?>" value="<?php echo str_replace('_', ' ', $step['name']); ?>" />
							</td>
							<td class="wf_data_td">
								<span><?php echo $step['parent'] == 'false' ? '[None]' : str_replace('_', ' ', $step['parent']); ?></span>
								<input type="hidden" name="workflow_step_parent_<?php echo $i; ?>" value="<?php echo str_replace('_', ' ', $step['parent']); ?>" />
							</td>
							<td style="text-align: center;">
								<input type="checkbox" name="workflow_step_omitprograms_<?php echo $i; ?>" <?php if($step['omitPrograms'] === 'true') echo 'checked="checked"'; ?> />
							</td>
							<td style="text-align: center;">
								<input type="checkbox" name="workflow_step_multiple_<?php echo $i; ?>" <?php if($step['multiplePrograms'] === 'true') echo 'checked="checked"'; ?> />
							</td>
							<td style="text-align: center;">
								<input type="checkbox" name="workflow_step_required_<?php echo $i; ?>" <?php if($step['stepRequired'] === 'true') echo 'checked="checked"'; ?> />
							</td>
							<td>
								<input type="text" class="wf_restriction_select" name="workflow_step_restrictions_<?php echo $i; ?>" style="width: 100%;" value="<?php echo $existing_rf; ?>" />
							</td>
							<td>
								<div class="float_right">
								<button type="button" class="btn btn-danger btn-xs float_right wf_delete_btn" data-deleted="zero">
									<span class="glyphicon glyphicon-remove"></span>&nbsp;<span class="exq_delete_btn_text">Delete</span>
								</button>
								<button type="button" class="btn btn-primary btn-xs float_right right-pad5 wf_edit_btn" data-edited="false">
									<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
								</button>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<button type="button" class="btn btn-primary wf_add_btn_out">
					<span class="glyphicon glyphicon-plus"></span>&nbsp;Add Workflow Step
				</button>
			</div>
			<a href="<?php echo $back_button_url; ?>" class="btn btn-primary btn-lg" role="button">
				<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Back
			</a>
			<button class="btn btn-primary btn-lg float_right">
				Next&nbsp;<span class="glyphicon glyphicon-arrow-right"></span>
			</button>
		</div>
	</div>
	<input type="hidden" id="numWorkflowStepsi" value="<?php echo count($workflow_steps); ?>" />
	<input type="hidden" id="numWorkflowSteps" name="num_workflow_steps" value="<?php echo count($workflow_steps); ?>" />
	<input type="hidden" id="restrictionsJson" value='<?php echo json_encode($restriction_flags); ?>' />
</div>
</form>
<input type="hidden" id="site_url" value="<?php echo $site_url; ?>" />
