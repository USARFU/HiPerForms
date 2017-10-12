<?php
$layout_Tab2Profile =& $fm->getLayout('Member-Tab2-Profile');

$request_Tab2Profile = $fm->newFindCommand('Member-Tab2-Profile');
if ($EditingMemberProfile) {
	$request_Tab2Profile->addFindCriterion('ID', '==' . $ID_Personnel);
} else {
	$request_Tab2Profile->addFindCriterion('RecordID', '==' . $recordID);
}
$result_Tab2Profile = $request_Tab2Profile->execute();
if (FileMaker::isError($result_Tab2Profile)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 301: " . $result_Tab2Profile->getMessage() . "</p>";
	die();
}
$records_Tab2Profile = $result_Tab2Profile->getRecords();
$record_Tab2Profile = $result_Tab2Profile->getFirstRecord();

// Show latest measurement data in Profile
// Measurements //
$request_Measurement_latest = $fm->newFindCommand('Personnel__Measurements');
$request_Measurement_latest->addFindCriterion('ID_Personnel', '==' . $ID_Personnel);
$request_Measurement_latest->addSortRule('dateMeasured', 1, FILEMAKER_SORT_DESCEND);
$request_Measurement_latest->setRange(0,1);
$result_Measurement_latest = $request_Measurement_latest->execute();
if (FileMaker::isError($result_Measurement_latest)) {
	$record_Measurement_latest = false;
} else {
	$record_Measurement_latest = $result_Measurement_latest->getFirstRecord();
	$latest_Measurement_date = $record_Measurement_latest->getField('dateMeasured');
	$latest_Measurement_height = $record_Measurement_latest->getField('c_height');
	$latest_Measurement_height_m = $record_Measurement_latest->getField('heightMeters');
	$latest_Measurement_weight_lb = $record_Measurement_latest->getField('Weight_lb');
	$latest_Measurement_weight_kg = $record_Measurement_latest->getField('Weight_kg');
	$latest_Measurement_wingspan_in = $record_Measurement_latest->getField('Wingspan_in');
	$latest_Measurement_wingspan_m = $record_Measurement_latest->getField('Wingspan_m');
	$latest_Measurement_handspan_in = $record_Measurement_latest->getField('Handspan_in');
	$latest_Measurement_handspan_cm = $record_Measurement_latest->getField('Handspan_cm');
	$latest_Measurement_standingreach_in = $record_Measurement_latest->getField('StandingReach_in');
	$latest_Measurement_standingreach_m = $record_Measurement_latest->getField('StandingReach_m');
}

$clothingSizeValues = $layout_Tab2Profile->getValueListTwoFields('Size');
$relationshipValues = $layout_Tab2Profile->getValueListTwoFields('Relationship');
$guardianValues = $layout_Tab2Profile->getValueListTwoFields('Guardian Type');
// Should this be moved/copied to camp nomination?
$referenceTypeValues = $layout_Tab2Profile->getValueListTwoFields('Reference Type');
$fifteensValues = $layout_Tab2Profile->getValueListTwoFields('Position15s');
$sevensValues = $layout_Tab2Profile->getValueListTwoFields('Position7s');
$sportsValues = $layout_Tab2Profile->getValueListTwoFields('OtherSports');
$airportValues = $layout_Tab2Profile->getValueListTwoFields('Airport');
asort($airportValues);

$Photo64 = $record_Tab2Profile->getField('Personnel2::Photo64');
$ProofOfDOB64 = $record_Tab2Profile->getField('Personnel2::ProofOfDOB64');
$ProofOfSchool64 = $record_Tab2Profile->getField('Personnel2::ProofOfSchool64');
$Passport64 = $record_Tab2Profile->getField('Personnel2::Passport64');
$OtherTravel64 = $record_Tab2Profile->getField('Personnel2::OtherTravel64');
$InsuranceCard64 = $record_Tab2Profile->getField('Personnel2::InsuranceCard64');

//## Determine what to show in the Image editors ##//
$FacePhotoEditor = (empty($Photo64) ? "../include/MissingFacePhoto.PNG" : $Photo64);
$ProofOfDOBEditor = (empty($ProofOfDOB64) ? "../include/MissingDOB.PNG" : $ProofOfDOB64);
$PassportEditor = (empty($Passport64) ? "../include/MissingPassport.PNG" : $Passport64);
$OtherTravelEditor = (empty($OtherTravel64) ? "../include/MissingOtherTravel.PNG" : $OtherTravel64);
$InsuranceCardEditor = (empty($InsuranceCard64) ? "../include/MissingInsurance.PNG" : $InsuranceCard64);

if ($U19) {
	$ProofOfSchool64 = $record_Tab2Profile->getField('Personnel2::ProofOfSchool64');
	$ProofOfSchoolEditor = (empty($ProofOfSchool64) ? "../include/MissingSchool.PNG" : $ProofOfSchool64);
}

// Club Membership // Only for ProfileNoRegistration //
$related_ClubMembership = $record_Tab2Profile->getRelatedSet('Personnel__ClubMembership');
if (FileMaker::isError($related_ClubMembership)) {
	$related_ClubMembership_count = 0;
} else {
	$related_ClubMembership_count = count($related_ClubMembership);
}
// Delete the above for Registration-only profile //

// Other Sports related records //
$related_othersports = $record_Tab2Profile->getRelatedSet('Personnel__OtherSports');
if (FileMaker::isError($related_othersports)) {
	$related_othersports_count = 0;
} else {
	$related_othersports_count = count($related_othersports);
}