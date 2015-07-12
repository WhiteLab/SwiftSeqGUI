<div id="newQuestionDiv_<?php echo $i; ?>" class="panel panel-primary bottom_padding _hidden">
	<div class="panel-heading">
		<h3 class="panel-title" style="display: inline-block;">
			<input type="text" name="new_question_content_<?php echo $i; ?>" class="pad5 toggle_tooltip" style="color: #333; width: 250%;" 
				data-toggle="tooltip" data-placement="right" title="Enter question content here" />
		</h3>
		<button type="button" class="btn btn-danger btn-xs float_right exq_delete_btn" data-deleted="zero">
			<span class="glyphicon glyphicon-remove"></span>&nbsp;<span class="exq_delete_btn_text">Delete</span>
		</button>
	</div>
	<div class="panel-body">
		<span><strong>Programmatic Name:</strong>&nbsp;<input type="text" name="new_question_name_<?php echo $i; ?>" class="pad5 width30 bottom_padding" /></span><br/>
		<span><strong>Options:</strong></span>
		<div class="exq_options" style="display: inline;">
			<input type="text" name="new_question_options_<?php echo $i; ?>" class="pad5 width50 new_question_options" />
		</div>
	</div>
	<script type="text/javascript">
	var $newQuestionDiv = $('#newQuestionDiv_<?php echo $i; ?>');
	$newQuestionDiv.find('button.exq_delete_btn').first().click(function(){
		if($(this).data('deleted') == 'zero'){
            $(this).children('span.exq_delete_btn_text').first().text('Really Delete?');
            $(this).data('deleted', 'once');
        }else if($(this).data('deleted') == 'once'){
              $('#newQuestionDiv_<?php echo $i; ?>').slideUp(150, function(){
	    	     $(this).remove();
	    	     $('#numNewQuestions').val(parseInt($('#numNewQuestions').val()) - 1);
	    	});
       }
	});
	$newQuestionDiv.find('input.toggle_tooltip').tooltip();
	$newQuestionDiv.find('div.panel-body').first().find('input.new_question_options').first().selectize({
	    plugins: ['remove_button'],
	    delimiter: ';',
	    create: true,
	    persist: false,
	    maxItems: null,
	    valueField: 'option',
	    labelField: 'option'
	});
	</script>
</div>

