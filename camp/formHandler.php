<?php
//################## INITIAL LOAD TESTING #################//
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

$activeTab = (isset($_GET['activeTab']) ? $_GET['activeTab'] : 1 );

$IDType = (isset($_GET['IDType']) && $_GET['IDType'] == 'Camp' ? 'Camp' : "");
$IDType = (isset($_GET['IDType']) && $_GET['IDType'] == 'Document' ? 'Document' : $IDType);

if ($IDType == 'Camp') { //Get data based on Camp ID; for preview purposes
	$layout =& $fm->getLayout('Camp Header');
	$request = $fm->newFindCommand('Camp Header');
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
	$layout =& $fm->getLayout('Camp Invite');
	$request = $fm->newFindCommand('Camp Invite');
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
	$campRequest = $fm->newFindCommand('Camp Header');
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
	
}
//################## END INITIAL LOAD TESTING #################//

//## Header Data:
$customLogo = urlencode($campRecord->getField('WebFormLogo'));
$venueName = $campRecord->getField('c_Venue');
$campName = $campRecord->getField('Name');
$dateStarted = $campRecord->getField('StartDate');
$fee = $campRecord->getField('Fee');
$pageHeader = $campRecord->getField('WebFormInviteTitle');

//
// Tab 1: Invite form submitted
if (isset($_POST['submitted-invite'])) {
	$activeTab = 1;
	
}
//---- END Invite form submited

//
// Tab 2: Profile form submitted
if (isset($_POST['submitted-profile'])) {

}
//---- END Profile form submitted

//
// Tab 3: Payment

//---- END Payment form submitted

//
// Tab 4: Travel form submitted

//---- END Travel form submitted


/*
 * The following can only be done once everything else is loaded
 */

if ($activeTab == 2) {
	if (!empty($StatePlayingIn) && !empty($CurrentSchoolGradeLevel)) {
		$CompoundSchoolRequest =& $fm->newCompoundFindCommand('School_1_12');
		$SchoolRequest1 =& $fm->newFindRequest('School_1_12');
		$SchoolRequest2 =& $fm->newFindRequest('School_1_12');
		$SchoolRequest3 =& $fm->newFindRequest('School_1_12');
		$SchoolRequest1->addFindCriterion('State', '==' . $StatePlayingIn);
		$SchoolRequest2->addFindCriterion('GradeLow', '>' . ($CurrentSchoolGradeLevel + 1));
		$SchoolRequest2->setOmit(true);
		$SchoolRequest3->addFindCriterion('GradeHigh', '<' . ($CurrentSchoolGradeLevel - 1));
		$SchoolRequest3->setOmit(true);
		$CompoundSchoolRequest->add(1, $SchoolRequest1);
		$CompoundSchoolRequest->add(2, $SchoolRequest2);
		$CompoundSchoolRequest->add(3, $SchoolRequest3);
		$SchoolResult = $CompoundSchoolRequest->execute();
		if (FileMaker::isError($SchoolResult)) {
			$fail .= "There was an error retrieving the school records. <br />";
			$SchoolValues = "";
		} else {
			$SchoolRecords = $SchoolResult->getRecords();
			foreach ($SchoolRecords as $value) {
				$SchoolName[] = $value->getField('c_SchoolNameLocation');
				$SchoolID[] = $value->getField('ID');
			}
			$SchoolValues = array_combine($SchoolID, $SchoolName);
			asort($SchoolValues);
		}
	}
	if (!$U18 && $IsPlayer) {
		$CollegeValues = $layout_Tab2Profile->getValueListTwoFields('PHPCollege');
		asort($CollegeValues);
		$MilitaryBranchValues = $layout_Tab2Profile->getValueListTwoFields('Military Branch');
		$MilitaryComponentValues = $layout_Tab2Profile->getValueListTwoFields('Military Component');
	}
}

//Refresh value upon profile submission:
if (isset($_POST['submitted-profile'])) {
		$result = $request->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: Your form could not be loaded. Your link ID is invalid. Check the link that was e-mailed you, and try again.</p>"
			. "<p>Error Code 001: " . $result->getMessage() . "</p>";
		echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
		die();
	}
	$records = $result->getRecords();
	$record = $result->getFirstRecord();
}

$DaysSinceSuccessfulProfileVerification = $record->getField('Camp__CampPersonnel__Personnel::z_DaysSinceSuccessfulProfileUpdate');
if ($DaysSinceSuccessfulProfileVerification == "") {
	$ProfileStatus = "black";
} elseif ($DaysSinceSuccessfulProfileVerification < 61) {
	$ProfileStatus = "green";
} elseif ($DaysSinceSuccessfulProfileVerification > 60 && $DaysSinceSuccessfulProfileVerification < 181) {
	$ProfileStatus = "orange";
} elseif ($DaysSinceSuccessfulProfileVerification > 180) {
	$ProfileStatus = "red";
} else {
	$ProfileStatus = "black";
}