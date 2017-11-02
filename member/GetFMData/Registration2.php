<?php
$layout_Registration2 =& $fm->getLayout('Member-Registration2-Membership');

$request_Registration2 = $fm->newFindCommand('Member-Registration2-Membership');
if ($EditingMemberProfile) {
	$request_Registration2->addFindCriterion('ID', '==' . $ID_Personnel);
} else {
	$request_Registration2->addFindCriterion('RecordID', '==' . $recordID);
}
$result_Registration2 = $request_Registration2->execute();
if (FileMaker::isError($result_Registration2)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 301: " . $result_Registration2->getMessage() . "</p>";
	die();
}
$records_Registration2 = $result_Registration2->getRecords();
$record_Registration2 = $result_Registration2->getFirstRecord();

// workaround FM update breaking relational filter of value lists
// make your own array instead of using getValueTwoFields
$requestClubVL = $fm->newFindCommand('Club Value List');
$requestClubVL->addFindCriterion('InvitationalFlag', '=');
$requestClubVL->addFindCriterion('ID', '*');

$resultClubVL = $requestClubVL->execute();
if (FileMaker::isError($resultClubVL)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 1301: " . $resultClubVL->getMessage() . "</p>";
	die();
}
$recordsClubVL = $resultClubVL->getRecords();

$clubValues = array();

foreach ($recordsClubVL as $recordClubVL) {
	$clubValues[$recordClubVL->getField('ID')] = $recordClubVL->getField('c_clubNameLong');
}

//$clubValues = $layout_Registration2->getValueListTwoFields('Club.NonInvitational');
asort($clubValues);

$clubRoleValues = $layout_Registration2->getValueListTwoFields('Role.Registration');

// Club Membership //
$related_ClubMembership = $record_Registration2->getRelatedSet('Personnel__ClubMembership');
if (FileMaker::isError($related_ClubMembership)) {
	$related_ClubMembership_count = 0;
} else {
	$related_ClubMembership_count = count($related_ClubMembership);
}

## Get Related Primary ClubMembership Record #############################
$compoundMembershipRequest =& $fm->newCompoundFindCommand('Personnel__ClubMembership');
$clubMembershipRequest =& $fm->newFindRequest('Personnel__ClubMembership');
$clubMembershipRequest->addFindCriterion('ID_Personnel', '==' . $ID_Personnel);
$clubMembershipRequest->addFindCriterion('Primary_flag', 1);
$compoundMembershipRequest->add(1, $clubMembershipRequest);
$PrimaryClubMembershipResult = $compoundMembershipRequest->execute();
if (FileMaker::isError($PrimaryClubMembershipResult)) {
	$PrimaryClubMembershipCount = 0;
} else {
	$PrimaryClubMembershipCount = $PrimaryClubMembershipResult->getFoundSetCount();
	$PrimaryClubMembership_records = $PrimaryClubMembershipResult->getRecords();
}