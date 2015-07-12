<tr id="newWorkflowStepDiv_<?php echo$i; ?>">
	<td class="wf_data_td _hidden">
		<input type="text" name="workflow_step_name_<?php echo $i; ?>" class="pad5" style="width: 100%;" />
	</td>
	<td class="wf_data_td _hidden">
		<input type="text" name="workflow_step_parent_<?php echo $i; ?>" class="pad5" style="width: 100%;" />
	</td>
	<td style="text-align: center;" class="_hidden">
		<input type="checkbox" name="workflow_step_omitprograms_<?php echo $i; ?>" />
	</td>
	<td style="text-align: center;" class="_hidden">
		<input type="checkbox" name="workflow_step_multiple_<?php echo $i; ?>" />
	</td>
	<td class="_hidden">
		<input type="text" class="wf_restriction_select" name="workflow_step_restrictions_<?php echo $i; ?>" style="width: 100%;" />
	</td>
	<td class="_hidden">
		<button type="button" class="btn btn-danger btn-xs float_right wf_delete_btn" data-deleted="zero">
			<span class="glyphicon glyphicon-remove"></span>&nbsp;<span class="exq_delete_btn_text">Delete</span>
		</button>
	</td>
</tr>
<script type="text/javascript">
$('#newWorkflowStepDiv_<?php echo $i; ?>').find('button.wf_delete_btn').first().click(function(){
    if($(this).data('deleted') == 'zero'){
        $(this).children('span.exq_delete_btn_text').first().text('Really Delete?');
        $(this).data('deleted', 'once');
    }else if($(this).data('deleted') == 'once'){
        var $tr = $(this).parents('tr').first();
        $tr.children().slideUp(150 ,function(){
            $tr.remove();
        });
        $('#numWorkflowSteps').val(parseInt($('#numWorkflowSteps').val()) - 1);
    }
});
var restrictionOptions = new Array();
var restrictions = $.parseJSON($('#restrictionsJson').val());
for(var i = 0, j = restrictions.length; i < j; i++){
    for(var k = 0, l = restrictions[i].options.length; k < l; k++){
        restrictionOptions.push({option: restrictions[i].name + ':' + restrictions[i].options[k]});
    }
}

$('#newWorkflowStepDiv_<?php echo $i; ?>').find('input.wf_restriction_select').first().selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    create: false,
    maxItems: null,
    valueField: 'option',
    labelField: 'option',
    options: restrictionOptions
});
$('#newWorkflowStepDiv_<?php echo $i; ?>').find('input[type="checkbox"]').altCheckbox({sizeClass : ''});
</script>
