<form id="swift_seq_form" action="<?php echo site_url('generate/process_step1'); ?>" method="post">
<div class="container">
	<div class="row">
		<h1>Generate Workflow Configuration</h1>
	<div class="col-md-10 col-md-offset-1">
		<?php if(!empty($defaults)): ?>
		<!--------------------------------------------------------->
		<!----------------- Begin Application --------------------->
		<!--------------------------------------------------------->
		<?php foreach($defaults['restrictionFlags'] as $i => $restriction_flag): ?>
		<div id="<?php echo $restriction_flag['name'] . '_div'; ?>" class="restriction_flag_div<?php if($i>0) echo ' _hidden'; ?> panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $restriction_flag['content']; ?></h3>
			</div>
			<div class="panel-body">
				<ul style="margin-bottom: 0; padding-left: 0;">
					<?php foreach($restriction_flag['options'] as $j => $option):
						$option_val = str_replace(' ', '_', $option); ?>
						<label for="<?php echo $restriction_flag['name'] . '_' . $j; ?>">
						<li class="list-group-item">
							<input type="radio" id="<?php echo $restriction_flag['name'] . '_' . $j; ?>" name="<?php echo $restriction_flag['name']; ?>" value="<?php echo $option_val; ?>" />
							&nbsp;<?php echo str_replace('_', ' ', $option); ?>
						</li>
						</label>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="panel-hidden">
				<input type="hidden" name="<?php echo $restriction_flag['name']; ?>_content" value="<?php echo $restriction_flag['content']; ?>" />
			</div>
		</div>
		<?php endforeach; ?>
		<!------------------------------------------------------------------->
		<!------------------- End Application ------------------------------->
		<!------------------------------------------------------------------->
		<button class="btn btn-primary btn-lg float_right gen1_next" disabled="disabled">
			Next&nbsp;<span class="glyphicon glyphicon-arrow-right"></span>
		</button>
		<?php else: ?>
		<div class="alert alert-warning">
			<span class="glyphicon glyphicon-wrench"></span>
			<span class="text-warning">This application has not yet been configured.</span>
		</div>
		<?php endif; ?>
	</div>
	</div>
</div>
<input type="hidden" id="site_url" value="<?php echo $site_url; ?>" />
<?php if(!empty($defaults)): ?>
<input type="hidden" name="workflow_steps" value="<?php foreach($defaults['steps'] as $default) echo $default['name'] . ' '; ?>" />
<input type="hidden" name="restriction_flags" value="<?php foreach($defaults['restrictionFlags'] as $rf) echo $rf['name'] . ' '; ?>" />
<?php endif; ?>
</form>
