<?php
$layout_Tab3History =& $fm->getLayout('Member-Tab3-History');

$request_Tab3History = $fm->newFindCommand('Member-Tab3-History');
if ($EditingMemberProfile) {
	$request_Tab3History->addFindCriterion('ID', '==' . $ID_Personnel);
} else {
	$request_Tab3History->addFindCriterion('RecordID', '==' . $recordID);
}
$result_Tab3History = $request_Tab3History->execute();
if (FileMaker::isError($result_Tab3History)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 301: " . $result_Tab3History->getMessage() . "</p>";
	die();
}
$records_Tab3History = $result_Tab3History->getRecords();
$record_Tab3History = $result_Tab3History->getFirstRecord();

// Club Membership //
$related_ClubMembership = $record_Tab3History->getRelatedSet('Personnel__ClubMembership');
if (FileMaker::isError($related_ClubMembership)) {
	$related_ClubMembership_count = 0;
} else {
	$related_ClubMembership_count = count($related_ClubMembership);
}

// Measurements //
$related_measurements = $record_Tab3History->getRelatedSet('Personnel__Measurements');
if (FileMaker::isError($related_measurements)) {
	$related_measurements_count = 0;
} else {
	$related_measurements_count = count($related_measurements);
}

// Attributes //
$related_attributes = $record_Tab3History->getRelatedSet('Personnel__Attributes');
if (FileMaker::isError($related_attributes)) {
	$related_attributes_count = 0;
} else {
	$related_attributes_count = count($related_attributes);
}

// Performance //
$related_performances = $record_Tab3History->getRelatedSet('Personnel__Performance');
if (FileMaker::isError($related_performances)) {
	$related_performances_count = 0;
} else {
	$related_performances_count = count($related_performances);
}

// Camp //
$related_camps = $record_Tab3History->getRelatedSet('Personnel__CampPersonnel');
if (FileMaker::isError($related_camps)) {
	$related_camps_count = 0;
} else {
	$related_camps_count = count($related_camps);
}