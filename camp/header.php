<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="../include/WebForms.css" media="screen"/>
</head>

<body>
<!-- Error code 001 -->

<?php
date_default_timezone_set('America/New_York');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include "$root/include/dbaccess.php";
include "$root/include/functions.php";

// Check that EventPersonnel ID is received //
if (isset($_POST['ID'])) {
	$ID = $_POST['ID'];
} else {
	if (isset($_GET['ID'])) {
		$ID = fix_string($_GET['ID']);
	} else {
		echo '<p style="color: red"><i>Your personalized Event ID is missing from the link. Verify the link that was sent you and try again.</i></p>';
		die();
	}
}

$IDType = (isset($_GET['IDType']) && $_GET['IDType'] == 'Camp' ? 'Camp' : "");
$IDType = (isset($_GET['IDType']) && $_GET['IDType'] == 'Document' ? 'Document' : $IDType);

if ($IDType == 'Camp') { //Get data based on Camp ID; for preview purposes
	$layout =& $fm->getLayout('PHP-Camp');
	$request = $fm->newFindCommand('PHP-Camp');
	$request->addFindCriterion('ID', '==' . $ID);
	$result = $request->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: Your form could not be loaded. Your link ID is invalid. Check the link that was e-mailed you, and try again.</p>"
			. "<p>Error Code 000: " . $result->getMessage() . "</p>";
		echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
		die();
	}
	$records = $result->getRecords();
	$campRecord = $result->getFirstRecord();

	$name = "HiPer Test User";

} elseif ($IDType == 'Document') {
	$layout =& $fm->getLayout('PHP-CampDocument');
	$request = $fm->newFindCommand('PHP-CampDocument');
	$request->addFindCriterion('ID', '==' . $ID);
	$result = $request->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: The Document could not be found. Your link ID is. Check the link that was e-mailed you, and try again.</p>"
			. "<p>Error Code 003: " . $result->getMessage() . "</p>";
		echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
		die();
	}
	$records = $result->getRecords();
	$documentRecord = $result->getFirstRecord();
	return;

} else { //Get data based on EventPersonnel ID
	$layout =& $fm->getLayout('PHP-EventInvite');
	$request = $fm->newFindCommand('PHP-EventInvite');
	$request->addFindCriterion('ID', '==' . $ID);
	$result = $request->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: Your form could not be loaded. Your link ID is invalid. Check the link that was e-mailed you, and try again.</p>"
			. "<p>Error Code 001: " . $result->getMessage() . "</p>";
		echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
		die();
	}
	$records = $result->getRecords();
	$record = $result->getFirstRecord();

	// ID is valid, get Personnel information for the header //
	$recordID = $record->getRecordId();
	$ID_Camp = $record->getField('ID_Event');
	$name = $record->getField('c_lastFirst_lookup');

	// Get data based on Camp ID //
	$campRequest = $fm->newFindCommand('PHP-Camp');
	$campRequest->addFindCriterion('ID', '==' . $ID_Camp);
	$campResult = $campRequest->execute();
	if (FileMaker::isError($campResult)) {
		echo "<p>Error: Your form could not be loaded. The Camp ID is invalid. Check the link that was e-mailed you, and try again.</p>"
			. "<p>Error Code 002: " . $campResult->getMessage() . "</p>";
		echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
		die();
	}
	$campRecords = $campResult->getRecords();
	$campRecord = $campResult->getFirstRecord();

	echo '<div id="container">';

}

$customLogo = urlencode( $campRecord->getField('WebFormLogo') );
$venueName = $campRecord->getField('c_Venue');
$campName = $campRecord->getField('Name');
$dateStarted = $campRecord->getField('StartDate');
$fee = $campRecord->getField('Fee');

?>

	<div style="text-align: center">
		<?php
		if (empty($customLogo)){
			echo '<img src="../include/USAR-logo.png" alt="logo"/>';
		} else {
			echo '<img src="../include/ContainerBridge.php?path=' . $customLogo . '" alt="logo" />';
		}
		?>

</body>
</html>