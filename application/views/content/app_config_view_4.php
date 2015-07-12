<form id="app_config_form_4" method="post" action="<?php echo site_url('admin/app_config/process_final'); ?>">
<div class="container">
	<div class="row">
		<h1>Create/Change Application Defaults</h1><br/>
		<div class="col-md-10 col-md-offset-1">
			<div class="display_box bottom_padding">
				<span class="display_box_header bottom_padding">Review New Application Defaults</span>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Information Gathering</h3>
					</div>
					<div class="panel-body">
						<span><strong>Information Gathering:</strong></span><br/>
						<pre><?php print_r($restriction_flags); ?></pre>
					</div>
				</div>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Workflow Steps</h3>
					</div>
					<div class="panel-body">
						<span><strong>Workflow Steps:</strong></span><br/>
						<pre><?php print_r($workflow_steps); ?></pre>
					</div>
				</div>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Programs and Parameters</h3>
					</div>
					<div class="panel-body">
						<span><strong>Programs:</strong></span><br/>
						<pre><?php print_r($programs); ?></pre>
						<span><strong>Parameters:</strong></span><br/>
						<pre><?php print_r($params); ?></pre>
					</div>
				</div>
			</div>
			<a href="<?php echo $back_button_url; ?>" class="btn btn-primary btn-lg" role="button">
				<span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Back
			</a>
			<button class="btn btn-success btn-lg float_right">
				<span class="glyphicon glyphicon-ok"></span>&nbsp;Apply
			</button>
		</div>
	</div>
</div>
<input type="hidden" name="workflow_steps" value="<?php foreach($workflow_steps as $step) echo $step['name'] . ' '; ?>" />
</form>
<input type="hidden" id="site_url" value="<?php echo $site_url; ?>" />
<input type="hidden" id="restrictionsJson" value='<?php echo json_encode($restriction_flags); ?>' />