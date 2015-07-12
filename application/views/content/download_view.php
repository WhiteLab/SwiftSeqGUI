<div class="container">
	<div class="row">
		<h1>Thank you for downloading!</h1>
	</div>
	<div class="col-md-5 col-md-offset-1">
		<p>We can put something here</p>
		<p>Like links or something</p>
		<p>That picture over there doesn't have to stay either</p>
		<p>Eventually this could maybe display a human readable format of what was just output?</p>
	</div>
	<div class="col-md-6">
		<img src="<?php echo base_url('includes/logo_swiftseq.png'); ?>" class="swiftseq_logo_nonfront" />
	</div>
</div>

<form method="post" id="initDownload" action="<?php echo site_url('generate_json/init_download'); ?>">
	<!-- This value of this next line must be single quoted to preserve json output -->
	<input type="hidden" name="output_json" value='<?php echo $output; ?>' />
	<input type="hidden" name="user_filename" value="<?php echo $user_filename; ?>" />
</form>