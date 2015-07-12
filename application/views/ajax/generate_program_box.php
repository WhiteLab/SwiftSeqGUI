<div id="<?php echo $step['name']; ?>_prog_<?php echo $i; ?>" class="display_box bottom_padding display_box_prog_params" data-omitprograms="<?php echo $step['omitPrograms']; ?>" data-prognum="<?php echo $i; ?>">
	<input type="hidden" class="default_params_lookup" value="<?php echo $step['name']; ?>" />
	<?php if($step['omitPrograms'] == 'false'): ?>
	<span class="display_box_subheader">Program</span>
	<select id="<?php echo $step['name']; ?>_prog_select_<?php echo $i; ?>" name="<?php echo $step['name']; ?>_prog_select_<?php echo $i; ?>" class="prog_select width40" title placeholder="Select a program...">
		<?php foreach($programs[$step['name']] as $prog): ?>
			<option value="<?php echo $prog['name']; ?>"><?php echo str_replace('_', ' ', $prog['nameRead']); ?></option>
		<?php endforeach; ?>
	</select>
	<img src="<?php echo base_url('includes/help.png'); ?>" class="help_icon">
	<br/>
	<?php endif; ?>
	<label for="<?php echo $step['name']; ?>_walltime_<?php echo $i; ?>">Walltime:&nbsp;&nbsp;</label>
	<input type="text" id="<?php echo $step['name']; ?>_walltime_<?php echo $i; ?>" name="<?php echo $step['name']; ?>_walltime_<?php echo $i; ?>" class="inputWalltime" value="100:00:00"/>
	<span class="display_box_subheader">Parameters</span>
	<div class="prog_params_select_wrapper_div" data-workflowstep="<?php echo $step['name']; ?>" data-prognum="<?php echo $i; ?>">
		<input type="hidden" class="prog_params_select_num" value="1" />
		<div class="prog_params_select_div">
			<select id="<?php echo $step['name']; ?>_prog_params_select_prog<?php echo $i; ?>_0" name="<?php echo $step['name']; ?>_prog_params_select_prog<?php echo $i; ?>_0" class="prog_params_select width40" placeholder="Search for or select a parameter..."></select>
			<input type="text" id="<?php echo $step['name']; ?>_prog_params_val_prog<?php echo $i; ?>_0" name="<?php echo $step['name']; ?>_prog_params_val_prog<?php echo $i; ?>_0" class="prog_params_val inputProgParams width40"/>
		</div>
	</div>
</div>
<script>
$('#<?php echo $step['name']; ?>_prog_<?php echo $i; ?>').each(function(){
    /* ############################################################################################
         * The change event handler for the parameter selection box cannot be an annonymous function
         * because it must be assigned recursively.
         * ############################################################################################
         */
        var progParamsSelectChangeHandler = function(){
            /* Get handler for the parameter select dropdown boxes wrapper div */
            var progParamsSelectWrapperDiv = $(this).parents('.prog_params_select_wrapper_div').first();
            var progParamsProgNum = progParamsSelectWrapperDiv.data('prognum');
            
            /* Check to see if a blank parameter select dropdown box already exists. If so, return */
            var blankSelectExists = false;
            progParamsSelectWrapperDiv.find('select.prog_params_select').each(function(){
               if($(this).val() == '') blankSelectExists = true;
            });
            if(blankSelectExists) return false;
            
            /* Calculate the new select dropdown box id and name, based on the most recent one */
            var progParamsSelectNum = parseInt(progParamsSelectWrapperDiv.children('.prog_params_select_num').val());
            var lastSelectInWrapper = progParamsSelectWrapperDiv.find('select.prog_params_select').last();
            var newSelectId = lastSelectInWrapper.attr('id').slice(0, -3) + progParamsProgNum + '_' + progParamsSelectNum;
            
            /* Calculate the new text input id and name, based on the most recent one */
            var lastInputInWrapper = progParamsSelectWrapperDiv.find('.prog_params_val').last();
            var newInputId = lastInputInWrapper.attr('id').slice(0, -3) + progParamsProgNum + '_' + progParamsSelectNum;
            
            /* Create new nodes, adding attributes and appending where necessary */
            var newProgParamsSelectDiv = $('<div>', {
                class: 'prog_params_select_div _hidden'
            }).appendTo(progParamsSelectWrapperDiv);
            var newProgParamsSelect = $('<select>', {
                id: newSelectId,
                name: newSelectId,
                class: 'prog_params_select width40',
                placeholder: 'Search for or select a parameter...'
            }).appendTo(newProgParamsSelectDiv);
            
            /* Apply selectize plugin to new nodes */
            newProgParamsSelect.selectize({
                create: true,
                valueField: 'id',
                labelField: 'id',
                searchField: 'id'
            });
            
            /* Grab the handle for the program currently selected. This unfortunately requires going all the way up to the
             * top level parent div, then back down to the prog_select select element.
             * 
             * Then get parameter values via AJAX.
             */
            var parametersLookup = $(this).parents('.display_box_prog_params').first().find('select.prog_select').first().val();
            if(parametersLookup == undefined){
                parametersLookup = $(this).parents('.display_box_prog_params').first().children('.default_params_lookup').val();
            }
            $.getJSON($('#site_url').val() + '/generate/get_parameters_for_program/' + parametersLookup, function(data){
                var paramOptions = [];
                $.each(data, function(key, val){
                   paramOptions.push({id: val});
                });
                newProgParamsSelect[0].selectize.addOption(paramOptions);
            });
            
            /* Attach event handler to new select dropdown box, then slidedown div */
            newProgParamsSelect.change(progParamsSelectChangeHandler);
            
            /* For whatever reason, a space is added between the dropdown box and the text box during
             * original generation, but must be added when a new node is created
             */
            $('<span>',{
                html: '&nbsp;'
            }).appendTo(newProgParamsSelectDiv);
            
            /* Create new input node */
            $('<input>', {
                id: newInputId,
                name: newInputId,
                type: 'text',
                class: 'inputProgParams width40'
            }).appendTo(newProgParamsSelectDiv);
            
            newProgParamsSelectDiv.slideDown();
            /* Increment select number to ensure unique id */
            progParamsSelectWrapperDiv.children('.prog_params_select_num').val(++progParamsSelectNum);
        };
        /* #######################################END########################################################
         * End definition of progParamsSelectChangeHandler function
         * #######################################END########################################################
         */
        
        /* Get needed DOM object handles */
        var progSelect = $(this).find('select.prog_select').first();
        var progParamsSelect = $(this).find('.prog_params_select');
        
        /* defaultParametersLookup will be the name of the workflow step. This is used to generate
         * the programs that are available for selection, or in the case where program selection isn't
         * available, it will point to the parameters
         */
        var defaultParametersLookup = $(this).children('.default_params_lookup').val();
        /* Set selectize styling for dropdown boxes */
        progSelect.selectize({
            create: false,
            valueField: 'name',
            labelField: 'nameRead',
            searchField: 'nameRead',
            sortField: 'text'
        });
        progParamsSelect.selectize({
            create: true,
            valueField: 'id',
            labelField: 'id',
            searchField: 'id'
        });
        
        /* Load parameters via AJAX for workflow steps that don't contain programs */
        if($(this).data('omitprograms')){
            $.getJSON($('#site_url').val() + '/generate/get_parameters_for_program/' + defaultParametersLookup, function(data){
                var paramOptions = [];
                $.each(data, function(key, val){
                   paramOptions.push({id: val});
                });
                progParamsSelect[0].selectize.addOption(paramOptions);
            });
        }
        
        /* ####################################################################
         * Event handler for when user changes program select dropdown box
         * #################################################################### 
         */
        progSelect.change(function(){
            var progSelect = $(this);
            /* Draw back in all the parameter boxes when the program changes */
            var progParamsSelectWrapperDiv = $(this).parents('.display_box_prog_params').first().children('.prog_params_select_wrapper_div');
            /* Make sure the slideUp animation is finished before reseting parameter select dropdown boxes */
            progParamsSelectWrapperDiv.slideUp(200, function(){
                /* Get default select id, then clear wrapper div */
                var workflowStep = progParamsSelectWrapperDiv.data('workflowstep');
                var progNum = progParamsSelectWrapperDiv.data('prognum');
                progParamsSelectWrapperDiv.empty();
                /* Create new elements, imitating default setup */
                $('<input>', {
                    type: 'hidden',
                    class: 'prog_params_select_num',
                    value: '1'
                }).appendTo(progParamsSelectWrapperDiv);
                var progParamsSelectDiv = $('<div>', {
                    class: 'prog_params_select_div' 
                }).appendTo(progParamsSelectWrapperDiv);
                var progParamsSelect = $('<select>', {
                    id: workflowStep + '_prog_params_select_' + progNum + '_0',
                    name: workflowStep + '_prog_params_select_' + progNum + '_0',
                    class: 'prog_params_select width40',
                    placeholder: 'Search for or select a parameter...'
                }).appendTo(progParamsSelectDiv);
                
                progParamsSelect.selectize({
                    create: true,
                    valueField: 'id',
                    labelField: 'id',
                    searchField: 'id'
                });
                /* Repopulate options, getting parameters for new program via AJAX */
                var parametersLookup = $(this).parents('.display_box_prog_params').first().find('select.prog_select').first().val();
                $.getJSON($('#site_url').val() + '/generate/get_parameters_for_program/' + parametersLookup, function(data){
                    var paramOptions = [];
                    $.each(data, function(key, val){
                       paramOptions.push({id: val});
                    });
                    progParamsSelect[0].selectize.addOption(paramOptions);
                });
                /* Attach change event handler */
                progParamsSelect.change(progParamsSelectChangeHandler);
                
                /* Space between parameter selection dropdown box and parameter selection text input */
                $('<span>',{
                    html: '&nbsp;'
                }).appendTo(progParamsSelectDiv);
                /* Parameter selection text input */
                $('<input>', {
                    id: workflowStep + '_prog_params_val_' + progNum + '_0',
                    name: workflowStep + '_prog_params_val_' + progNum + '_0',
                    type: 'text',
                    class: 'prog_params_val inputProgParams width40'
                }).appendTo(progParamsSelectDiv);
                
                progParamsSelectWrapperDiv.slideDown();
            });
        });
        /* #################################################################################################
         * End event handler for when user changes program select dropdown box
         * #################################################################################################
         */ 
         
         /* Attach change event handler to parameter select dropdown boxes */
        progParamsSelect.change(progParamsSelectChangeHandler);
        
        /* If the workflow step does contain programs, call the change event to load first set of parameters */
        if(!$(this).data('omitprograms'))
            progSelect.change();
            
        $('.help_icon').data({
            'toggle' : 'tooltip',
            'placement' : 'right',
            'title' : 'Click for program documentation'
        }).tooltip();
        
    });
</script>