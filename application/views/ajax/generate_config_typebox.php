<div id="type_<?php echo $i; ?>" class="display_box bottom_padding" style="display: none;>
	<span id="type_<?php echo $i; ?>_name_header" class="display_box_header bottom_padding">
		<input type="text" id="type_<?php echo $i; ?>_name" name="type_<?php echo $i; ?>_name" value="<enter workflow step name>" />
	</span>
	<div id="type_<?php echo $i; ?>_prog" class="display_box bottom_padding">
		<span id="type_<?php echo $i; ?>_prog_name_header" class="display_box_header">
			<input type="text" id="type_<?php echo $i; ?>_prog_name" name="type_<?php echo $i; ?>_prog_name" value="<enter program name>" />
		</span>
		<div id="type_<?php echo $i; ?>_prog_params" class="display_box top_padding">
			<span class="display_box_header">Parameters</span>
			<table width="100%">
				<tr>
					<td><select id="type_<?php echo $i; ?>_prog_params" name="type_<?php echo $i; ?>_prog_params">
					</select></td>
					<td>
						<input type="text" size="50"/>
					</td>
				</tr>
			</table><br/>
			<button id="addParam">Add Parameter</button>
			<button id="addParamBulk">Add Bulk Parameters</button>
		</div>
	</div>
</div>