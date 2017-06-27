<?php
$layout_Tab4CampEnrollment =& $fm->getLayout('Member-Tab4-Enrollment');

$request_Tab4CampEnrollment = $fm->newFindCommand('Member-Tab4-Enrollment');
if ($EditingMemberProfile) {
	$request_Tab4CampEnrollment->addFindCriterion('ID', '==' . $ID_Personnel);
} else {
	$request_Tab4CampEnrollment->addFindCriterion('RecordID', '==' . $recordID);
}
$result_Tab4CampEnrollment = $request_Tab4CampEnrollment->execute();
if (FileMaker::isError($result_Tab4CampEnrollment)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 301: " . $result_Tab4CampEnrollment->getMessage() . "</p>";
	die();
}
$records_Tab4CampEnrollment = $result_Tab4CampEnrollment->getRecords();
$record_Tab4CampEnrollment = $result_Tab4CampEnrollment->getFirstRecord();

// Camp Registrations //
$related_campRegistrations = $record_Tab4CampEnrollment->getRelatedSet('Camp.OpenRegistration');
if (FileMaker::isError($related_campRegistrations)) {
	$related_campRegistration_count = 0;
} else {
	$related_campRegistration_count = count($related_campRegistrations);
}

// Venue List from Camps //
if ($related_campRegistration_count > 0) {
	$Venues = array();
	
	foreach ($related_campRegistrations as $related_campRegistration) {
		$camp_venue_ID = $related_campRegistration->getField('Camp.OpenRegistration::ID_Venue');
		$camp_venue = $related_campRegistration->getField('Camp.OpenRegistration::c_Venue');
		$Venues[$camp_venue_ID] = $camp_venue;
	}
}

// Get Age to Determine Open Registration validity //
$CurrentSchoolGradeLevel = $record_Tab4CampEnrollment->getField('CurrentSchoolGradeLevel');
$Age = $record_Tab4CampEnrollment->getField('Age');
$DOB_php = date_create($record_Tab4CampEnrollment->getField('DOB'));

// Camp //
$related_camps = $record_Tab4CampEnrollment->getRelatedSet('Personnel__CampPersonnel');
if (FileMaker::isError($related_camps)) {
	$related_camps_count = 0;
} else {
	$related_camps_count = count($related_camps);
}

$CampRole_values = $layout_Tab4CampEnrollment->getValueListTwoFields('Primary Role');
$PrimaryClubRole = $record_Tab4CampEnrollment->getField('c_PrimaryClubRole');
