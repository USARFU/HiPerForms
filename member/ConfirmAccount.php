<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>USA Rugby HiPer Database - Confirm Account</title>
</head>

<?php
$header2 = " - Confirm Account";
include_once 'header.php';

if (isset($_GET['ID'])) {
	$ID = fix_string($_GET['ID']);
} else {
	$message = "No ID provided.";
}

## Try to find a match. Create account and e-mail password if match found #################
if (empty($message)) {
	$request = $fm->newFindCommand('NewAccount');
	$request->addFindCriterion('ID', '==' . $ID);
	$result = $request->execute();
	
	if (FileMaker::isError($result)) {
		$message = "The ID provided is no longer valid.<br />";
	} else {
		$record = $result->getFirstRecord();
		$newPerformScript = $fm->newPerformScriptCommand('NewAccount', 'ActivateAccount', $ID);
		$scriptResult = $newPerformScript->execute();
		if (FileMaker::isError($scriptResult)) {
			$message = $scriptResult->getMessage() . "<br />";
		} else {
			$message = "Activation Successful. An e-mail has been sent with your temporary password.";
		}
	}
}
################################################################################
?>

<body>
<div id="container">
	
	<!-- Show message. ################### -->
	<?php
	if (isset($message)) {
		echo '<br />'
			. '<h3>' . $message . '</h3></div></body></html>';
		die();
	}
	?>
	<!-- ################################# -->

</body>
</html>