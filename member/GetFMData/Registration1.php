<?php
$layout_Registration1 =& $fm->getLayout('Member-Registration1-Demographic');

$request_Registration1 = $fm->newFindCommand('Member-Registration1-Demographic');
if ($EditingMemberProfile) {
	$request_Registration1->addFindCriterion('ID', '==' . $ID_Personnel);
} else {
	$request_Registration1->addFindCriterion('RecordID', '==' . $recordID);
}
$result_Registration1 = $request_Registration1->execute();
if (FileMaker::isError($result_Registration1)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 301: " . $result_Registration1->getMessage() . "</p>";
	die();
}
$records_Registration1 = $result_Registration1->getRecords();
$record_Registration1 = $result_Registration1->getFirstRecord();

$ethnicityValues = $layout_Registration1->getValueListTwoFields('RaceEthnicity');
$clothingSizeValues = $layout_Registration1->getValueListTwoFields('Size');