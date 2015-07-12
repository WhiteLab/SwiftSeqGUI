<form id="app_config_form_1" method="post" action="<?php echo site_url('admin/app_config/process_info_gathering'); ?>">
<div class="container">
	<div class="row">
		<h1>Create/Change Application Defaults</h1><br/>
		<div class="col-md-10 col-md-offset-1">
			<div class="display_box bottom_padding">
				<span class="display_box_header bottom_padding">Information Gathering</span>
				<div class="display_box bottom_padding">
					<span class="display_box_subheader">Existing Questions</span>
					<?php if(empty($defaults_rf)) echo '[None]'; else foreach($defaults_rf as $i => $rf): ?>
					<div class="panel panel-primary top_padding" style="margin-bottom: 0;" data-i="<?php echo $i; ?>">
						<div class="panel-heading">
							<h3 class="panel-title exq_content" style="display: inline-block;"><?php echo $rf['content']; ?></h3>
							<button type="button" class="btn btn-danger btn-xs float_right exq_delete_btn" data-deleted="zero">
								<span class="glyphicon glyphicon-remove"></span>&nbsp;<span class="exq_delete_btn_text">Delete</span>
							</button>
							<button type="button" class="btn btn-primary btn-xs float_right right-pad5 exq_edit_btn" data-edited="false">
								<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
							</button>
						</div>
						<div class="panel-body">
							<span><strong>Programmatic Name:</strong>&nbsp;<span class="exq_name"><?php echo $rf['name']; ?></span></span><br/>
							<span><strong>Options:</strong></span>
							<div class="exq_options" style="display: inline;">
								<ul class="list-group indent top_padding" style="margin-bottom: 0;">
									<?php foreach($rf['options'] as $option): ?>
									<li class="list-group-item"><?php echo str_replace('_', ' ', $option); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<div class="panel-hidden">
							<input type="hidden" name="ex_question_content_<?php echo $i; ?>" value="<?php echo $rf['content']; ?>" />
							<input type="hidden" name="ex_question_name_<?php echo $i; ?>" value="<?php echo $rf['name']; ?>" />
							<input type="hidden" name="ex_question_options_<?php echo $i; ?>" value="<?php echo implode(';', $rf['options']); ?>" />
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<div id="questionsContainer"></div>
				<button type="button" id="addQuestionBtn" class="btn btn-primary">
					<span class="glyphicon glyphicon-plus"></span>&nbsp;Add a Question
				</button>
			</div>
			<button class="btn btn-primary btn-lg float_right">
				Next&nbsp;<span class="glyphicon glyphicon-arrow-right"></span>
			</button>
		</div>
	</div>
</div>
<input type="hidden" id="numNewQuestionsi" value="0" />
<input type="hidden" id="numNewQuestions" name="num_new_questions" value="0" />
<input type="hidden" id="numExQuestions" name="num_ex_questions" value="<?php echo count($defaults_rf); ?>" />
</form>
<input type="hidden" id="site_url" value="<?php echo $site_url; ?>" />
