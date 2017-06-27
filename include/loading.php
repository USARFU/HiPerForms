<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Please wait while we load the form.</title>

	<!-- Loading message -->
	<link href="../scripts/please-wait/please-wait.css" rel="stylesheet" />
	<script src="../scripts/please-wait/please-wait.min.js"></script>

	<style type="text/css">
		.spinner {
			margin: 100px auto;
			width: 80px;
			height: 80px;
			text-align: center;
			font-size: 10px;
		}

		.spinner > div {
			background-color: #ccebff;
			height: 100%;
			width: 6px;
			display: inline-block;

			-webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
			animation: sk-stretchdelay 1.2s infinite ease-in-out;
		}

		.spinner .rect2 {
			-webkit-animation-delay: -1.1s;
			animation-delay: -1.1s;
		}

		.spinner .rect3 {
			-webkit-animation-delay: -1.0s;
			animation-delay: -1.0s;
		}

		.spinner .rect4 {
			-webkit-animation-delay: -0.9s;
			animation-delay: -0.9s;
		}

		.spinner .rect5 {
			-webkit-animation-delay: -0.8s;
			animation-delay: -0.8s;
		}

		@-webkit-keyframes sk-stretchdelay {
			0%, 40%, 100% { -webkit-transform: scaleY(0.4) }
			20% { -webkit-transform: scaleY(1.0) }
		}

		@keyframes sk-stretchdelay {
			0%, 40%, 100% {
				transform: scaleY(0.4);
				-webkit-transform: scaleY(0.4);
			}  20% {
					transform: scaleY(1.0);
					-webkit-transform: scaleY(1.0);
				}
		}
	</style>
	
	<!-- Examples of Calling this page:
	$mensURL = "location.href='includes/loading.php?Page=" . urlencode("RugbyInterest.php?S=Male") . "'";
	header("location: includes/loading.php?Page=AddRugbyPlayer-RCT-Form.php"); -->
</head>

<body>
<?php
include_once '../functions.php';
session_start();
if (!empty($_GET['Page'])) {
	$page = urldecode($_GET['Page']);
	$page = fix_string($_GET['Page']);
}
?>
<script>
	var page = '../' + <?php echo json_encode($page); ?>;
	var loading_screen = pleaseWait({
		logo: "../2014-USA-Rugby-Logo-Web.png",
		backgroundColor: '#468bb9',
		loadingHtml: "<p style='font-size: 2em; font-style: italic'>Please wait while your form loads.</p><div class='spinner'><div class='rect1'></div><div class='rect2'></div><div class='rect3'></div><div class='rect4'></div><div class='rect5'></div></div>"
	});
	location.replace(page);
</script>
</body>
</html>