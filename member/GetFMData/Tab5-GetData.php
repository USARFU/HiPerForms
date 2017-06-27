<?php
$layout_Tab5MyClubs =& $fm->getLayout('Member-Tab5-Manage');

$request_Tab5MyClubs = $fm->newFindCommand('Member-Tab5-Manage');
if ($EditingMemberProfile) {
	$request_Tab5MyClubs->addFindCriterion('ID', '==' . $ID_Personnel);
} else {
	$request_Tab5MyClubs->addFindCriterion('RecordID', '==' . $recordID);
}
$result_Tab5MyClubs = $request_Tab5MyClubs->execute();
if (FileMaker::isError($result_Tab5MyClubs)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 301: " . $result_Tab5MyClubs->getMessage() . "</p>";
	die();
}
$records_Tab5MyClubs = $result_Tab5MyClubs->getRecords();
$record_Tab5MyClubs = $result_Tab5MyClubs->getFirstRecord();

// Club Viewer //
$related_ClubAccess = $record_Tab5MyClubs->getRelatedSet('Personnel__Club.WebAccess');
if (FileMaker::isError($related_ClubAccess)) {
	$related_ClubAccess_count = 0;
} else {
	$related_ClubAccess_count = count($related_ClubAccess);
}

// Camp Viewer //
$related_CampAccess = $record_Tab5MyClubs->getRelatedSet('Camp.WebAccess');
if (FileMaker::isError($related_CampAccess)) {
	$related_CampAccess_count = 0;
} else {
	$related_CampAccess_count = count($related_CampAccess);
}