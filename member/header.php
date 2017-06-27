<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	
	<link rel="stylesheet" type="text/css" href="../include/WebForms.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="../include/tabbed/tabbed.css" media="screen"/>

	<script src="../include/script/jquery/jquery.min.js"></script>

	<script type="text/javascript">
       <!--	JS for rendering multiple Captcha boxes on one webpage-->
       var verifyCallback = function (response) {
           alert(response);
       };
       var onloadCallback = function () {
           grecaptcha.render('grecaptcha1', {
               'sitekey': '6Ldk5gkTAAAAAMwOynBrb1G07eitD0O3M-zKd3R5',
               'theme': 'light'
           });
           grecaptcha.render('grecaptcha2', {
               'sitekey': '6Ldk5gkTAAAAAMwOynBrb1G07eitD0O3M-zKd3R5',
               'theme': 'light'
           });
           grecaptcha.render('grecaptcha3', {
               'sitekey': '6Ldk5gkTAAAAAMwOynBrb1G07eitD0O3M-zKd3R5',
               'theme': 'light'
           });
           grecaptcha.render('grecaptcha4', {
               'sitekey': '6Ldk5gkTAAAAAMwOynBrb1G07eitD0O3M-zKd3R5',
               'theme': 'light'
           });
       };
	</script>

	<!-- slim image cropper -->
	<link href="../include/script/slim/slim.css" rel="stylesheet"/>

	<!-- select2 js library for searchable drop down controls -->
	<link href="../include/script/select2/css/select2.min.css" rel="stylesheet"/>
	<script src="../include/script/select2/js/select2.min.js"></script>

	<link href="../include/bootstrap-modified.css" rel="stylesheet"/>

	<!-- jquery-ui for date picker & dialog -->
	<link href="../include/script/jquery-ui/jquery-ui.min.css" rel="stylesheet"/>
	<script src="../include/script/jquery-ui/jquery-ui.min.js"></script>

	<script src="../include/WebForms.js"></script>
	
	<?php
	date_default_timezone_set('America/New_York');
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include "$root/include/dbaccess-membership.php";
	include "$root/include/functions.php";
	include_once '../slim.php';
	
	$layout_Header =& $fm->getLayout('Member-Header');
	$fail = "";
	$message = "";
	$activeTab = 1;
	$EditingMemberProfile = false;
	?>
</head>

<!-- Profile Header -->

<body class="polaroid">

<!--Popover dialogs-->
<div id="Processing" title="Please Wait">
	<p>Please wait while your request is being processed.</p>
</div>
<div id="loading" title="Loading...">
</div>

<!-- slim image cropper -->
<script src="../include/script/slim/slim.kickstart.min.js"></script>
</body>
</html>