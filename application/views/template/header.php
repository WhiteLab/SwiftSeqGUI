<!DOCTYPE html>
<html>
<head>
	<title><?php echo $page_title; ?></title>
	<script src="//code.jquery.com/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" type="text/javascript"></script>
	<!--<script src="<?php echo base_url('js/jquery-ui-1.10.4.custom.js'); ?>" type="text/javascript"></script>-->
	<script src="<?php echo base_url('js/jquery.alt-checkbox.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('js/icheck.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('js/selectize.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('js/custombox.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('js/include.js'); ?>" type="text/javascript"></script>
	<?php if(isset($js_init)): ?>
		<script type="text/javascript">
			$(document).ready(<?php echo $js_init; ?>);
		</script>
	<?php endif; ?>
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/jquery-ui-1.10.4.custom.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/jquery.alt-checkbox.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/jquery.alt-checkbox.icon-font.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/aero.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/selectize.bootstrap3.css'); ?>">
	<!--<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/bootstrap.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/bootstrap-theme.min.css'); ?>">-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/layout.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('layout/custombox.css'); ?>">
	
	<link rel="shortcut icon" href="<?php echo base_url('includes/favicon.ico'); ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo base_url('includes/favicon.ico'); ?>" type="image/x-icon">
</head>
<body>
	<div class="navbar swiftseq_navbar" style="z-index: 1;">
      <div class="col-md-10 col-md-offset-1">
      	<ul class="nav navbar-nav">
        	<li><a href="<?php echo site_url('generate'); ?>">Generate Workflow</a></li>
        	<li><a href="<?php echo site_url('www/view/upload_temp'); ?>">Add Custom Program</a></li>
        	<li><a href="<?php echo site_url('www/view/prebuilt_workflows'); ?>">Pre-built Workflows</a></li>
        	<li><a href="<?php echo site_url('www/view/download_ref_files'); ?>">Download Reference Files</a></li>
        	<li><a href="#">Download SwiftSeq</a></li>
        	<li><a href="https://bitbucket.org/swiftseq/swiftseq/wiki/Home">SwiftSeq Wiki</a></li>
        </ul>
        <!--<ul class="nav navbar-nav navbar-right">
        	<li class="dropdown">
        		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Other Tools&nbsp;<span class="caret"></span></a>
        		<ul class="dropdown-menu" role="menu">
        			<li><a href="<?php echo site_url('admin/app_config'); ?>">App Administration</a></li>
        		</ul>
        	</li>
      	</ul>-->
      </div>
    </div>
	<div class="container-fluid">
		<div class="row" style="background: #707070;">
			<div class="col-md-8 col-md-offset-2" style="overflow: hidden;">
				<a href="http://dmel.uchicago.edu/swiftseq/">
					<img src="<?php echo base_url('includes/logo_swiftseq.png'); ?>" class="swiftseq_logo_nonfront" />
				</a>
			</div>
		</div>
	</div>
	