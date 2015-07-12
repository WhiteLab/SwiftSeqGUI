/* TODO Apply $<name> convention to variable names to follow industry standard best practices
 * 
 * TODO Generate custom program config as separate tool, upload into json generator
 * For upload thing, we might be able to store the information in a cookie
 * Which means we would need to load cookie data every time
 * 
 * TODO Maybe some 'other' steps aren't applicable for certain runs or something. Maybe hook them up with restriction flags
 * 
 * TODO Still have to do the admin ui section, right now all the default informtion is coming from hard coded arrays
 * TODO The file download is sloppy, but it may be the only way to do it
 * TODO We decided to go with the simpler option and just persist uploaded config for the length of the session.
 * 
 * 
 * 
 * 
 * TODO Major rehaul of the Generate JSON Config page. I want to break it up into two steps over two separate pages. The
 * first page will just be the information gathering questions, and the second page will be the program and parameters
 * specification page. To do this transfer or data across pages, I may be able to just send it as post data. If not, I could
 * store a temporary file in the server, which will be linked to temporary user id code in a session cookie. The idea behind
 * this is that it will be way easier to do workflow step and program restrictions if I can process with php rather than
 * trying to do everything with AJAX, as elegant as AJAX is. Side note: AJAX will still need to be used to retrieve parameters,
 * but that shouldn't be much of a problem.
 * 
 * 
 * TODO Clearing parameters on user side programs
 * TODO First page on app config use plugin for separation
 */

/**
 * Slides down alert box with given divName.
 */
function showAlert(divName){
    $alert = $(divName);
    if($alert.data('shown') == 'true') return;
    $alert.slideDown(150);
    $alert.data('shown', 'true');
}

/**
 * Forces the download of SwiftSeq config json file to begin. Called once the page it loaded.
 */
function initDownload(){
    $('#initDownload').submit();
}/* End initDownload() */

/* ========================================================================================================
 * ========================================================================================================
 * Application configuration initialization functions
 * For the application administrator to configure defaults for the UI
 * ========================================================================================================
 * ========================================================================================================
 */

/**
 * Initialization of dynamic elements on the information gathering application configuration step.
 */
function initAppConfigStep1(){
    /*
     * AJAX call to generate new information gathering configuration box.
     */
    $('#addQuestionBtn').click(function(){
        $.get($('#site_url').val() + '/admin/app_config/get_new_question_box/' + $('#numNewQuestionsi').val(), function(data){
            var $question = $(data);
            $('#questionsContainer').append($question);
            $question.slideDown(150);
            $('#numNewQuestions').val(parseInt($('#numNewQuestions').val()) + 1);
            $('#numNewQuestionsi').val(parseInt($('#numNewQuestionsi').val()) + 1);
        });
    });
    
    /*
     * Click event handler for each edit button on existing question information gathering.
     * Replaces relevant text nodes with form input nodes, populating with text node content.
     */
    $('.exq_edit_btn').click(function(){
        /* If the edit button has already been clicked once, exit this function */
        if($(this).data('edited') == 'true') return false;
        $(this).data('edited', 'true');
        /* Get needed DOM object handles */
        var $panel = $(this).parents('div.panel').first();
        var $panelBody = $panel.children('div.panel-body').first();
        var $contentNode = $panel.find('h3.exq_content').first();
        var $nameNode = $panelBody.find('span.exq_name').first();
        var $optionsListNode = $panelBody.find('div.exq_options').first();
        
        /* Get the number id of the panel this has been called on */
        var i = $panel.data('i');
        /*
         * Protype object; other input nodes are created by cloning this object, then adding on unique attributes.
         */
        var $inputNodeProto = $('<input>', {
            type : 'text',
            class : 'pad5'
        });
        
        /*
         * Replace question text node with input node.
         */
        var contentText = $contentNode.text();
        $contentNode.empty();
        $inputNodeProto.clone().attr({
            value : contentText,
            name : 'ex_question_content_' + i,
            style : 'color: #333; width: 250%;'
        }).appendTo($contentNode);
        
        /*
         * Replace programmatic name text node with input node.
         */
        var nameText = $nameNode.text().trim();
        $nameNode.empty();
        $inputNodeProto.clone().attr({
            value : nameText,
            name : 'ex_question_name_' + i
        }).addClass('width30 bottom_padding').appendTo($nameNode);
        
        /*
         * Put each question answer option into an array...
         */
        var optionsText = new Array();
        $optionsListNode.find('li.list-group-item').each(function(){
            optionsText.push($(this).text().trim());
        });
        optionsText = optionsText.join(';');
        
        /*
         * ...then add them as options to a Selectize input node.
         * Also add Bootstrap tooltip to indicate backspace function on Selectize input node.
         */
        $optionsListNode.empty();
        $inputNodeProto.clone().attr({
            value : optionsText,
            name : 'ex_question_options_' + i
        }).addClass('width50')
        .appendTo($optionsListNode)
        .selectize({
            plugins: ['remove_button','restore_on_backspace'],
            delimiter: ';',
            create: true,
            persist: false,
            maxItems: null,
            valueField: 'option',
            labelField: 'option'
        })
        .next('div.selectize-control')
        .attr('title', 'Backspace item to edit')
        .data({
            'toggle' : 'tooltip',
            'placement' : 'right'
        }).tooltip();
        
        /*
         * Clear the div containing hidden input nodes. The values in the text nodes are placed inside
         * hidden input nodes by default so they are submitted in the form, but once this panel converts
         * it's data to input nodes, the hidden input nodes become redundant.
         */
        $panel.children('div.panel-hidden').first().empty();
    });
    
    
    /*
     * Click event handler for each delete button on information gathering.
     * Removes the panel from view and decrements count for number of panels.
     */
    $('.exq_delete_btn').click(function(){
        /* On the first click of delete button, display confirmation message */
        if($(this).data('deleted') == 'zero'){
            $(this).children('span.exq_delete_btn_text').first().text('Really Delete?');
            $(this).data('deleted', 'once');
        /* If clicked a second time, really delete the panel */
        }else if($(this).data('deleted') == 'once'){
            var $panel = $(this).parents('div.panel').first();
            /* Slide the panel up, then remove from DOM */
            $panel.slideUp(150 ,function(){
                /* If the last panel on the page is being deleted, insert a text node displaying '[None]' */
                if($panel.siblings('div.panel').length == 0){
                    $('<span>').addClass('indent').text('[None]').appendTo($panel.parent('div.display_box').first());
                }
                $panel.remove();
            });
            /* Decrement count for number of panels */
            $('#numExQuestions').val(parseInt($('#numExQuestions').val()) - 1);
        }
    });
}/* End initAppConfigStep1() */

/**
 * Initialization of dynamic elements on the workflow step application configuration step.
 */
function initAppConfigStep2(){
    /*
     * Load the workflow step restriction options from a json formatted string in a hidden input node.
     * The json string is loaded into the input node by php proessing of the previous step, given the
     * option to select even knew restriction options.
     */
    var restrictionOptions = new Array();
    var restrictions = $.parseJSON($('#restrictionsJson').val());
    for(var i = 0, j = restrictions.length; i < j; i++){
        for(var k = 0, l = restrictions[i].options.length; k < l; k++){
            restrictionOptions.push({option: restrictions[i].name + ':' + restrictions[i].options[k]});
        }
    }
    
    /*
     * Apply selectize to workflow restriction input node.
     */
    $('.wf_restriction_select').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        create: false,
        maxItems: null,
        valueField: 'option',
        labelField: 'option',
        options: restrictionOptions
    });
    
    /*
     * Click event handler for each delete button on workflow steps.
     * Removes the row from view and decrements count for number of rows.
     */
    $('.wf_delete_btn').click(function(){
        /* On the first click of delete button, display confirmation message */
        if($(this).data('deleted') == 'zero'){
            $(this).children('span.exq_delete_btn_text').first().text('Really Delete?');
            $(this).data('deleted', 'once');
        /* If clicked a second time, really delete the row */
        }else if($(this).data('deleted') == 'once'){
            /* Get DOM handler for parent <tr> element */
            var $tr = $(this).parents('tr').first();
            $tr.children().slideUp(150 ,function(){
                $tr.remove();
            });
            /* Decrement count for number of panels */
            $('#numWorkflowSteps').val(parseInt($('#numWorkflowSteps').val()) - 1);
        }
    });
    
    /*
     * Click event handler for each edit button on existing workflow steps.
     * Replaces relevant text nodes with form input nodes, populating with text node content.
     */
    $('.wf_edit_btn').click(function(){
        /* Get DOM handler for parent <tr> element */
        var $tr = $(this).parents('tr').first();
        $tr.children('.wf_data_td').each(function(){
            /* Replace text nodes with input nodes */
            $(this).children('span').empty();
            $(this).children('input').attr({
                'type' : 'text',
                'style' : 'width: 100%'
            }).addClass('pad5');
        });
        /* Show information to edit workflow steps */
        showAlert('#workflowAlert');
    });
    
    /* Surely this is a way to condense this code!!! */
    $('.wf_add_btn_in').click(function(){
        var $panel = $(this).parents('div.panel').first();
        var $tableBody = $panel.find('tbody').first();
        $.get($('#site_url').val() + '/admin/app_config/get_new_workflow_step/' + $('#numWorkflowStepsi').val(), function(data){
            var $newStep = $(data);
            $tableBody.append($newStep);
            $newStep.children('td').slideDown(150, function(){
                showAlert('#workflowAlert');
            });
            $('#numWorkflowStepsi').val(parseInt($('#numWorkflowStepsi').val()) + 1);
            $('#numWorkflowSteps').val(parseInt($('#numWorkflowSteps').val()) + 1);
        });
    });
    // The only different between this and the above function is the first line in the anonymous function
    $('.wf_add_btn_out').click(function(){
        var $panel = $(this).siblings('div.panel').first();
        var $tableBody = $panel.find('tbody').first();
        $.get($('#site_url').val() + '/admin/app_config/get_new_workflow_step/' + $('#numWorkflowStepsi').val(), function(data){
            var $newStep = $(data);
            $tableBody.append($newStep);
            $newStep.children('td').slideDown(150, function(){
                showAlert('#workflowAlert');
            });
            $('#numWorkflowStepsi').val(parseInt($('#numWorkflowStepsi').val()) + 1);
            $('#numWorkflowSteps').val(parseInt($('#numWorkflowSteps').val()) + 1);
        });
    });
    
    /* Apply altCheckbox style to checkboxes */
    $('input[type="checkbox"]').altCheckbox({sizeClass : ''});
}/* End initAppConfigStep2() */

/**
 * Initialization of dynamic elements on the programs and parameters application configuration step.
 */
function initAppConfigStep3(){
    /*
     * Load the workflow step restriction options from a json formatted string in a hidden input node.
     * The restriction options are the same as in the previous step.
     */
    var restrictionOptions = new Array();
    var restrictions = $.parseJSON($('#restrictionsJson').val());
    for(var i = 0, j = restrictions.length; i < j; i++){
        for(var k = 0, l = restrictions[i].options.length; k < l; k++){
            restrictionOptions.push({option: restrictions[i].name + ':' + restrictions[i].options[k]});
        }
    }
    
    /*
     * Apply Selectize to program restriction input node.
     */
    $('.pp_restriction_select').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        create: false,
        maxItems: null,
        valueField: 'option',
        labelField: 'option',
        options: restrictionOptions
    });
    
    /*
     * Apply Selectize to parameter selection input node.
     */
    $('.params_select').selectize({
        plugins: ['remove_button'],
        delimiter: ',',
        create: true,
        persist: false,
        maxItems: null,
        valueField: 'param',
        labelField: 'param'
    });
    
    /*
     * Click event handler for each delete button on program rows.
     * Removes the row from view and decrements count for number of rows for that panel.
     */
    $('.pp_delete_btn').click(function(){
        /* On the first click of delete button, display confirmation message */
        if($(this).data('deleted') == 'zero'){
            $(this).children('span.pp_delete_btn_text').first().text('Really Delete?');
            $(this).data('deleted', 'once');
        /* If clicked a second time, really delete the row */
        }else if($(this).data('deleted') == 'once'){
            /*
             * Get DOM handlers for both parent <tr> elements.
             * The program rows and parameters are in techincally in two separate rows,
             * so both <tr> elements need to be hidden.
             */ 
            var $tr1 = $(this).parents('tr.pp_data_tr').first();
            var $tr2 = $tr1.nextAll('tr').first();
            $tr2.children().slideUp(150 ,function(){
                $tr1.children().slideUp(150, function(){
                    /* Decrement number of programs */
                    var $numProgs = $tr1.parents('div.pp_data').first().find('input.pp_num_progs').first();
                    $numProgs.val(parseInt($numProgs.val()) - 1);
                    /* Remove both rows, which visually appear as a single row */
                    $tr1.remove();
                    $tr2.remove();
                });
            });
        }
    });
    
    /*
     * Click event handler for each edit button on existing programs.
     * Replaces relevant text nodes with form input nodes, populating with text node content.
     */
    $('.pp_edit_btn').click(function(){
        var $tr = $(this).parents('tr.pp_data_tr').first();
        $tr.children('.pp_data_td').each(function(){
            $(this).children('span').empty();
            $(this).children('input').attr({
                'type' : 'text',
                'style' : 'width: 100%'
            }).addClass('pad5');
        });
    });
    
    /*
     * AJAX call to generate new program row.
     */
    $('.pp_add_prog_btn').click(function(){
        /* Get needed DOM event handlers */
        var $panel = $(this).parents('div.pp_data').first();
        var $table = $panel.find('table.prog_table').first();
        var $numProgs_i = $panel.find('input.pp_num_progs_i').first();
        var $numProgs = $panel.find('input.pp_num_progs').first();
        var $stepName = $panel.find('input.pp_workflow_step').first();
        /* AJAX call */
        $.get($('#site_url').val() + '/admin/app_config/get_new_program/' + $numProgs_i.val() + '/' + $stepName.val(), function(data){
            /* Get DOM event handlers in returned AJAX data */
            var $newProgramData = $(data);
            var $newProgramTr1 = $newProgramData.filter('tr.pp_data_tr');
            var $newProgramTr2 = $newProgramData.filter('tr.pp_params_tr');
            var $newProgramScript = $newProgramData.filter('script');
            /* Append elements to table */
            $table.children('tbody').first().append($newProgramTr1);
            $table.children('tbody').first().append($newProgramTr2);
            $newProgramScript.appendTo('body');
            $newProgramTr1.children('td').slideDown(150, function(){
                $newProgramTr2.children('td').slideDown(150);
            });
            
            
            /* Increase count for number of programs in panel */
            $numProgs_i.val(parseInt($numProgs_i.val()) + 1);
            $numProgs.val(parseInt($numProgs.val()) + 1);
        });
    });
}/* End initAppConfigStep3() */

/* ========================================================================================================
 * ========================================================================================================
 * UI initialization functions
 * For the external user to generate SwiftSeq run configuration json files
 * ========================================================================================================
 * ========================================================================================================
 */

/**
 * Initialization of dynamic elements on the information gathering UI step.
 */
function initGenerateStep1(){
    /* Apply iCheck styling to radio buttons */
    $('input[type="radio"]').iCheck({radioClass: 'iradio_minimal-aero'}).on('ifClicked', function(){
        var $moreQuestions = $(this).parents('div.restriction_flag_div').first().nextAll('div.restriction_flag_div');
        /* If there is another question to follow, slide it down */
        if($moreQuestions.length > 0){
            $moreQuestions.first().slideDown(150);
        /* If this is the last question, enable the next step button */
        }else{
            $('.gen1_next').removeAttr('disabled');
        }
    });
}/* End initGenerateStep1() */


/**
 * Initialization of dynamic elements on the programs and parameters UI step.
 */
function initGenerateStep2(){
    
    
    /*
     * For the checkboxes to specify which elements of the workflow
     * are to be specified by the user
     */
    $('.specify_checkbox').each(function(){
       var assocDivStr = $(this).attr('id');
       var assocDiv = $('#' + assocDivStr.substring(8));
       $(this).altCheckbox({sizeClass: '', customClass: 'alt_' + assocDivStr});
       $('.alt_' + assocDivStr).click(function(){
          if($(this).hasClass('checked')){
               assocDiv.slideDown(150);
           }else{
               assocDiv.slideUp(150);
           }
       });
    });
    /* Special checkbox that just opens up other checkboxes below it */
    $('.specify_checkbox_parent').each(function(){
        var parentName = $(this).attr('id').substring(8);
        $(this).altCheckbox({sizeClass: '', customClass: 'alt_' + parentName});
        $('.alt_' + parentName).click(function(){
            var childrenCheckboxesDiv = $(this).parents('label.alt-checkbox-label').first().nextAll('div').first();
            if($(this).hasClass('checked')){
                childrenCheckboxesDiv.slideDown(150);
            }else{
                childrenCheckboxesDiv.slideUp(150);
                /* Uncheck all children checkboxes and slideup associated divs */
                childrenCheckboxesDiv.find('a.alt-checkbox').removeClass('checked');
                childrenCheckboxesDiv.find('input[type="checkbox"]').each(function(){
                    var assocDivStr = $(this).attr('id');
                    $('#' + assocDivStr.substring(8)).slideUp(150);
                });
            }
        });
    });
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /* ===============================================================================================
     * Configuration for each workflow step. This function sets up the AJAX for each workflow step,
     * calling parameters based on the selected program.
     * =============================================================================================== 
     */
    $('.display_box_prog_params').each(function(){
        /* Begin AJAX portion */
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
                
                /* Set default walltime and new docUrl*/
                var walltime = progSelect.nextAll('input.inputWalltime').first();
                var docUrl = progSelect.nextAll('.anchorDocUrl').first();
                $.getJSON($('#site_url').val() + '/generate/get_programs_for_workflow_step/' + workflowStep, function(data){
                var prog_i;
                for(var i = 0; i < data.length; i++){
                    if(data[i].name == parametersLookup){
                        prog_i = i;
                        break;
                    }
                }
                var defaultWalltime = data[prog_i].defaultWalltime;
                if(defaultWalltime == '') defaultWalltime = '24:00:00';
                walltime.val(defaultWalltime);
                
                docUrl.data('docurl', data[prog_i].docUrl);
                //docUrl.attr('href', data[prog_i].docUrl);
                
            });
                
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
    /* End AJAX portion */
   
    $('.anchorDocUrl').each(function(){
        	$(this).click(function(){
        		var workflowStep = $(this).parents('.display_box_prog_params').first().children('.prog_params_select_wrapper_div').data('workflowstep');
        		var program = $(this).parents('.display_box_prog_params').first().find('select.prog_select').first().val();
        		Custombox.open({
                	target: $('#site_url').val() + '/generate/get_info_box/' + workflowStep + '/' + program,
                	effect: 'fadein'
            	});
         });
        });
        
    $('.addButton').click(function(){
        var $workflowStepWrapperDiv = $(this).parents('div').first();
        var workflowStepName = $workflowStepWrapperDiv.attr('id');
        var $numProgsHiddenInput = $workflowStepWrapperDiv.children('input').first();
        $.post($('#site_url').val() + '/generate/get_program_box/' + workflowStepName + '/' + $numProgsHiddenInput.val(),{
            programs: $('#restriction_flags_json').val()
        }, function(data){
            var $new = $(data);
            $new.appendTo($workflowStepWrapperDiv.children('div').first());
        });
        $numProgsHiddenInput.val(parseInt($numProgsHiddenInput.val()) + 1);
    });
    
    /* Disabled checkboxes for required workflow steps */
    $('input.required').each(function(){
        $(this).click();
        $(this).prevAll('a.alt-checkbox').first().unbind();
    });
}/* End initGenerateStep2() */
///////////////////////////////////////////////////////////////////////////////////////////////

function initPrebuiltWorkflows(){
    $('.dwnWorkflow').click(function(){
        $('<form>', {
            method : 'post',
            action : $('#site_url').val() + '/download/download_prebuilt_workflow/' + $(this).data('filename')
        }).submit();
    });
}
