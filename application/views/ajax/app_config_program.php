<tr class="pp_data_tr" id="<?php echo $step_name; ?>_ajax_tr_<?php echo $i; ?>">
	<td class="pp_data_td _hidden">
		<input type="text" class="pp_prog_name_read pad5" style="width: 100%;" name="<?php echo $step_name; ?>_prog_name_read_<?php echo $i; ?>" />
	</td>
	<td class="pp_data_td _hidden">
		<input type="text" class="pp_prog_name_read pad5" style="width: 100%;" name="<?php echo $step_name; ?>_prog_name_<?php echo $i; ?>" />
	</td>
	<td class="_hidden">
		<input type="text" class="pp_restriction_select" name="<?php echo $step_name; ?>_prog_restriction_flags_<?php echo $i; ?>" style="width: 100%;" />
	</td>
	<td class="_hidden">
		<div class="float_right">
			<button type="button" class="btn btn-danger btn-xs float_right pp_delete_btn" data-deleted="zero">
				<span class="glyphicon glyphicon-remove"></span>&nbsp;<span class="pp_delete_btn_text">Delete</span>
			</button>
		</div>
	</td>
</tr>
<tr class="pp_params_tr">
	<td colspan="4" style="border-top: 0;" class="_hidden">
		<span><strong>Parameters:</strong>&nbsp;</span>
		<input type="text" class="params_select" name="<?php echo $step_name; ?>_params_for_prog_<?php echo $i; ?>" style="width: 100%;" />
	</td>
</tr>
<script type="text/javascript">
var $ajaxTr = $('#<?php echo $step_name; ?>_ajax_tr_<?php echo $i; ?>');

var restrictionOptions = new Array();
var restrictions = $.parseJSON($('#restrictionsJson').val());
for(var i = 0, j = restrictions.length; i < j; i++){
    for(var k = 0, l = restrictions[i].options.length; k < l; k++){
        restrictionOptions.push({option: restrictions[i].name + ':' + restrictions[i].options[k]});
    }
}

$ajaxTr.find('.pp_restriction_select').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    create: false,
    maxItems: null,
    valueField: 'option',
    labelField: 'option',
    options: restrictionOptions
});

$ajaxTr.nextAll('tr.pp_params_tr').first().find('.params_select').selectize({
    plugins: ['remove_button','restore_on_backspace'],
    delimiter: ',',
    create: true,
    persist: false,
    maxItems: null,
    valueField: 'param',
    labelField: 'param'
});

$ajaxTr.find('.pp_delete_btn').click(function(){
    if($(this).data('deleted') == 'zero'){
        $(this).children('span.pp_delete_btn_text').first().text('Really Delete?');
        $(this).data('deleted', 'once');
    }else if($(this).data('deleted') == 'once'){
        var $tr1 = $(this).parents('tr.pp_data_tr').first();
        var $tr2 = $tr1.nextAll('tr').first();
        $tr2.children().slideUp(150 ,function(){
            $tr1.children().slideUp(150, function(){
            	var $numProgs = $tr1.parents('div.pp_data').first().find('input.pp_num_progs').first();
                $numProgs.val(parseInt($numProgs.val()) - 1);
                /* Remove both rows, which visually appear as a single row */
                $tr1.remove();
                $tr2.remove();
                /* Decrement number of programs */
               
            });
        });
    }
});
</script>
