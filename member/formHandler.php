<?php

$UpdateSchool = (isset($_GET['UpdateSchool']) && $_GET['UpdateSchool'] == 1 ? true : false);

//## Grab/refresh data; pre-form handling ##//
$stateValues = $layout_Header->getValueListTwoFields('State-Territory');
$countryValues = $layout_Header->getValueListTwoFields('Countries');

//
// registration1 form
$RegistrationSubmitted1 = isset($_POST['submitted-registration1']) ? $_POST['submitted-registration1'] : "";
if ($RegistrationSubmitted1) {
	include 'GetFMData/Registration1.php';
	
	## Retrieve POSTed data ############################
	$firstName = (isset ($_POST ['firstName']) ? fix_string($_POST ['firstName']) : "");
	$middleName = (isset ($_POST ['middleName']) ? fix_string($_POST ['middleName']) : "");
	$lastName = (isset ($_POST ['lastName']) ? fix_string($_POST ['lastName']) : "");
	$nickName = (isset ($_POST ['nickName']) ? fix_string($_POST ['nickName']) : "");
	$nickName = $nickName == $firstName ? "" : $nickName;
	$DOB = "";
	$DOBsave = "";
	if (isset($_POST['DOB'])) {
		if (validate_date($_POST['DOB']) || validate_date_filemaker($_POST['DOB'])) {
			$DOBold = new DateTime($_POST['DOB']);
			$DOB = $DOBold->format('m/d/Y');
			$DOBsave = $DOBold->format('Y-m-d');
		} else {
			$DOBsave = $_POST['DOB'];
		}
	}
	$gender = (isset ($_POST['gender']) ? fix_string($_POST['gender']) : "");
	$ethnicity = (isset($_POST['ethnicity']) ? fix_string($_POST['ethnicity']) : "");
	$homeAddress1 = (isset ($_POST['homeAddress1']) ? fix_string($_POST['homeAddress1']) : "");
	$homeAddress2 = (isset ($_POST['homeAddress2']) ? fix_string($_POST['homeAddress2']) : "");
	$City = (isset ($_POST['City']) ? fix_string($_POST['City']) : "");
	$State = (isset ($_POST['State']) ? fix_string($_POST['State']) : "");
	$zipCode = (isset ($_POST['zipCode']) ? fix_string($_POST['zipCode']) : "");
	$Country = (isset ($_POST['Country']) ? fix_string($_POST['Country']) : "");
	$Citizen1 = (isset ($_POST['Citizen1']) ? fix_string($_POST['Citizen1']) : "");
	$Citizen2 = (isset ($_POST['Citizen2']) ? fix_string($_POST['Citizen2']) : "");
	$MatchJerseySize = (isset ($_POST['MatchJerseySize']) ? fix_string($_POST['MatchJerseySize']) : "");
	$MatchShortsSize = (isset ($_POST['MatchShortsSize']) ? fix_string($_POST['MatchShortsSize']) : "");
	$PrimaryPhoneNumber = (isset ($_POST['PrimaryPhoneNumber']) ? fix_string($_POST['PrimaryPhoneNumber']) : "");
	$PrimaryPhoneText_flag = isset ($_POST['PrimaryPhoneText_flag']) ? 1 : "";
	
	## Fail Tests #######################################
	$fail .= validate_empty_field($firstName, "First Name");
	$fail .= validate_empty_field($lastName, "Last Name");
	$fail .= validate_DOB($DOB);
	$fail .= validate_empty_field($gender, "Gender");
	$fail .= validate_empty_field($ethnicity, "Race / Ethnicity");
	$fail .= validate_empty_field($homeAddress1, "Home Address: Street 1");
	$fail .= validate_empty_field($City, "Home Address: City");
	$fail .= validate_zip($zipCode);
	$fail .= validate_empty_field($Country, "Country");
	$fail .= validate_empty_field($Citizen1, "Country of Citizenship 1");
	$fail .= validate_empty_field($MatchJerseySize, "Match Jersey Size");
	$fail .= validate_empty_field($MatchShortsSize, "Match Shorts Size");
	$fail .= validate_empty_field($PrimaryPhoneNumber, "Primary Phone Number");
	
	## Write Data to Database ############################
	$edit = $fm->newEditCommand('Member-Registration1-Demographic', $record_Registration1->getRecordId());
	$edit->setField('firstName', $firstName);
	$edit->setField('middleName', $middleName);
	$edit->setField('lastName', $lastName);
	$edit->setField('nickName', $nickName);
	$edit->setField('DOB', $DOB);
	$edit->setField('gender', $gender);
	$edit->setField('RaceEthnicity', $ethnicity);
	$edit->setField('homeAddress1', $homeAddress1);
	$edit->setField('homeAddress2', $homeAddress2);
	$edit->setField('City', $City);
	$edit->setField('State', $State);
	$edit->setField('zipCode', $zipCode);
	$edit->setField('Country', $Country);
	$edit->setField('Citizen1', $Citizen1);
	$edit->setField('Citizen2', $Citizen2);
	$edit->setField('MatchJerseySize', $MatchJerseySize);
	$edit->setField('MatchShortsSize', $MatchShortsSize);
	$edit->setField('PrimaryPhoneNumber', $PrimaryPhoneNumber);
	$edit->setField('PrimaryPhoneText_flag', $PrimaryPhoneText_flag);
	$edit->setField('z_ModifiedByID', $eMail);
	$edit->setField('z_ModifiedByName', 'web');
	
	// Commit Personnel Record:
	$result = $edit->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
			. "<p>Error Code 201: " . $result->getMessage() . "</p>";
		die();
	}
	
	// Either finish loading form with error messages, or go to next step
	if (!empty($fail)) {
		echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
		//Refresh record so that field variables have updated values
		$request_Registration1 = $fm->newFindCommand('Member-Registration1-Demographic');
		$request_Registration1->addFindCriterion('RecordID', '==' . $recordID);
		$result_Registration1 = $request_Registration1->execute();
		if (FileMaker::isError($result_Registration1)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 202: " . $result_Registration1->getMessage() . "</p>";
			die();
		}
		$records_Registration1 = $result_Registration1->getRecords();
		$record_Registration1 = $result_Registration1->getFirstRecord();
	} else {
		$RegistrationStage = 2;
	}
} elseif ($RegistrationStage == 1) {
	include 'GetFMData/Registration1.php';
	
	$firstName = $record_Registration1->getField('firstName');
	$middleName = $record_Registration1->getField('middleName');
	$lastName = $record_Registration1->getField('lastName');
	$nickName = $record_Registration1->getField('nickName');
	$DOB_original = $record_Registration1->getField('DOB');
	$DOB_original_test = explode('/', $DOB_original);
	if (count($DOB_original_test) == 3) {
		if (checkdate($DOB_original_test[0], $DOB_original_test[1], $DOB_original_test[2]) == TRUE) {
			$DOB = new DateTime($DOB_original);
			$DOBsave = $DOB->format('Y-m-d');
		}
	} else {
		$DOB = "";
	}
	$gender = $record_Registration1->getField('gender');
	$ethnicity = $record_Registration1->getField('RaceEthnicity');
	$homeAddress1 = $record_Registration1->getField('homeAddress1');
	$homeAddress2 = $record_Registration1->getField('homeAddress2');
	$City = $record_Registration1->getField('City');
	$State = $record_Registration1->getField('State');
	$zipCode = $record_Registration1->getField('zipCode');
	$Country = $record_Registration1->getField('Country');
	$Citizen1 = $record_Registration1->getField('Citizen1');
	$Citizen2 = $record_Registration1->getField('Citizen2');
	$MatchJerseySize = $record_Registration1->getField('MatchJerseySize');
	$MatchShortsSize = $record_Registration1->getField('MatchShortsSize');
	$PrimaryPhoneNumber = $record_Registration1->getField('PrimaryPhoneNumber');
	$PrimaryPhoneText_flag = $record_Registration1->getField('PrimaryPhoneText_flag');
}
//---- / registration1 form submitted

//
// registration2 form
$RegistrationSubmitted2 = (isset($_POST['submitted-registration2']) && $_POST['submitted-registration2'] == "true") ? true : false;
$NewClubMembership = isset($_GET['NewClubMembership']) ? true : false;
if ($RegistrationSubmitted2 || $NewClubMembership) {
	include 'GetFMData/Registration2.php';
	## Retrieve POSTed data ############################
	//Existing membership
	$ActiveMembership_Original = isset($_SESSION['ActiveMembership_Original']) ? $_SESSION['ActiveMembership_Original'] : "";
	$ActiveMembership_Remove = isset($_POST['ActiveMembership_Remove']) ? $_POST['ActiveMembership_Remove'] : "";
	$ActiveMembership_Renew = isset($_POST['ActiveMembership_Renew']) ? $_POST['ActiveMembership_Renew'] : "";
	$ActiveMembership_Update = isset($_POST['ActiveMembership_Update']) ? $_POST['ActiveMembership_Update'] : "";
	$ActiveMembership_UpdatePrimary = isset($_POST['ActiveMembership_UpdatePrimary']) ? $_POST['ActiveMembership_UpdatePrimary'] : "";
	$ActiveMembershipPrimaryHistory_Original = isset($_SESSION['ActiveMembershipPrimaryHistory_Original']) ? $_SESSION['ActiveMembershipPrimaryHistory_Original'] : "";
	$ActiveMembership_changed = ($ActiveMembership_Update == $ActiveMembership_Original ? false : true);
	
	//New membership
	if ($NewClubMembership) {
		$ID_Club = (isset ($_POST['ID_Club']) ? fix_string($_POST['ID_Club']) : "");
		$ClubRole = isset($_POST['ClubRole']) ? fix_string($_POST['ClubRole']) : "";
		$Primary_flag = isset($_POST['Primary_flag']) ? fix_string($_POST['Primary_flag']) : "";
		$StartDate = "";
		$StartDatesave = "";
		if (isset($_POST['StartDate'])) {
			if (validate_date($_POST['StartDate']) || validate_date_filemaker($_POST['StartDate'])) {
				$StartDateold = new DateTime($_POST['StartDate']);
				$StartDate = $StartDateold->format('m/d/Y');
				$StartDatesave = $StartDateold->format('Y-m-d');
			} else {
				$StartDatesave = $_POST['StartDate'];
			}
		}
	}
	
	//## Update existing Membership changes
	if ($ActiveMembership_UpdatePrimary != $ActiveMembershipPrimaryHistory_Original) {
		//##If one of the diff history records changed their primary flag, set that one record and unset all others
		foreach ($related_ClubMembership as $ClubMembership_record) {
			if ($ClubMembership_record->getRecordId() == $ActiveMembership_UpdatePrimary) {
				$ClubMembership_edit = $fm->newEditCommand('Personnel__ClubMembership', $ClubMembership_record->getRecordId());
				$ClubMembership_edit->setField('Primary_flag', 1);
				$SetPrimary_result = $ClubMembership_edit->execute();
				if (FileMaker::isError($SetPrimary_result)) {
					echo "<p>Error: There was a problem setting a club's primary flag. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 203: " . $SetPrimary_result->getMessage() . "</p>";
					exit;
				}
			} else {
				$ClubMembership_edit = $fm->newEditCommand('Personnel__ClubMembership', $ClubMembership_record->getRecordId());
				$ClubMembership_edit->setField('Primary_flag', "");
				$ClearPrimary_result = $ClubMembership_edit->execute();
				if (FileMaker::isError($ClearPrimary_result)) {
					echo "<p>Error: There was a problem removing a club's primary flag. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 204: " . $ClearPrimary_result->getMessage() . "</p>";
					exit;
				}
			}
		}
	}
	if ($ActiveMembership_changed) {
		while ($a = current($ActiveMembership_Update) && $b = current($ActiveMembership_Original)) { //For each element in the array
			$update_RecordID = key($ActiveMembership_Update);
			$update_record = current($ActiveMembership_Update);
			$original_record = current($ActiveMembership_Original);
			$diff = array_diff_assoc($update_record, $original_record);
			if (!empty($diff)) { //Edit the record if there is a difference from the update
				$ClubMember_edit = $fm->newEditCommand('Personnel__ClubMembership', $update_RecordID);
				foreach ($diff as $ClubMember_field => $ClubMember_field_value) { //Update only the fields that were in the diff array as changes
					if ($ClubMember_field == "StartDate" || $ClubMember_field == "EndDate") {
						if (validate_date($ClubMember_field_value) == false && validate_date_filemaker($ClubMember_field_value) == false) {
							$fail .= "One of your club membership dates was invalid. Its value has been reverted. <br />";
						} else {
							$ClubMember_edit->setField($ClubMember_field, $ClubMember_field_value);
						}
					} else {
						$ClubMember_edit->setField($ClubMember_field, $ClubMember_field_value);
					}
					if ($ClubMember_field == "EndDate" && !empty($ClubMember_field_value)) {
						$ClubMember_edit->setField('Primary_flag', "");
					}
				}
				$ClubMember_edit->setField('z_ModifiedByID', $eMail);
				$ClubMember_edit->setField('z_ModifiedByName', 'web');
				$ClubMember_result = $ClubMember_edit->execute();
				if (FileMaker::isError($ClubMember_result) && !empty($ClubMember_result->code)) { //supress error is date is invalid, and no other changes were made to record
					echo "<p>Error: There was a problem updating your club membership history. If this continues, please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 205: (" . $ClubMember_result->code . ") " . $ClubMember_result->getMessage() . "</p>";
					die();
				}
				// Audit:
				$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
				$z_storeResults = $z_storeRequest->execute();
				$z_storeResult = $z_storeResults->getFirstRecord();
				$z_storeRecordID = $z_storeResult->getRecordId();
				$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
				$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
				$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'ClubMembership::RecordID|' . $update_RecordID . ';');
				$resultAudit = $editAudit->execute();
			}
			next($ActiveMembership_Update);
			next($ActiveMembership_Original);
		}
	}
	
	## Fail Tests #######################################
	if ($NewClubMembership) {
		$fail .= validate_empty_field($ID_Club, 'Club Name');
		$fail .= validate_empty_field($ClubRole, 'Club Role');
		$fail .= validate_empty_field($StartDate, 'Club Start Date');
	}
	
	if (empty($fail)) {
		## Add new membership to Database ############################
		if (!empty($ID_Club)) {
			$clubMembership_data = array(
				'ID_Personnel' => $ID_Personnel,
				'ID_Club' => $ID_Club,
				'Role' => $ClubRole,
				'Primary_flag' => $Primary_flag,
				'StartDate' => $StartDate,
				'z_ModifiedByID' => $eMail,
				'z_ModifiedByName' => 'web',
			);
			// If adding a new primary club, remove the primary club flag from existing related record
			if ($PrimaryClubMembershipCount > 0 && $Primary_flag == 1) {
				foreach ($PrimaryClubMembership_records as $PrimaryClubMembership_record) {
					$PrimaryClubMembership_edit = $fm->newEditCommand('Personnel__ClubMembership', $PrimaryClubMembership_record->getRecordId());
					$PrimaryClubMembership_edit->setField('Primary_flag', "");
					$ClearPrimary_result = $PrimaryClubMembership_edit->execute();
					if (FileMaker::isError($ClearPrimary_result)) {
						echo "<p>Error: There was a problem removing a club's primary flag. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 206: " . $ClearPrimary_result->getMessage() . "</p>";
						exit;
					}
				}
			}
			
			$newClubMembershipRequest =& $fm->newAddCommand('Personnel__ClubMembership', $clubMembership_data);
			$result = $newClubMembershipRequest->execute();
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem adding the new club memberships record. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 207: " . $result->getMessage() . "</p>";
				exit;
			}
			
			$ClubMembership_NewRecord = $result->getFirstRecord();
			$ClubMembership_RecordID = $ClubMembership_NewRecord->getRecordId();
			// Audit:
			$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
			$z_storeResults = $z_storeRequest->execute();
			$z_storeResult = $z_storeResults->getFirstRecord();
			$z_storeRecordID = $z_storeResult->getRecordId();
			$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
			$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
			$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'ClubMembership::RecordID|' . $ClubMembership_RecordID . ';');
			$resultAudit = $editAudit->execute();
			
			$ID_Club = "";
			$ClubRole = "";
			$StartDate = "";
			$Primary_flag = "";
		}
		
		## Remove membership from Database ############################
		// key = RecordID; value = ID_Club
		foreach ($ActiveMembership_Remove as $key => $value) {
			if (!empty($value)) {
				$ClubMember_edit = $fm->newEditCommand('Personnel__ClubMembership', $key);
				$ClubMember_edit->setField('Inactive_flag', 1);
				$ClubMember_edit->setField('z_ModifiedByID', $eMail);
				$ClubMember_edit->setField('z_ModifiedByName', 'web');
				$ClubMember_result = $ClubMember_edit->execute();
				if (FileMaker::isError($ClubMember_result) && !empty($ClubMember_result->code)) { //supress error is date is invalid, and no other changes were made to record
					echo "<p>Error: There was a problem updating your club membership history. If this continues, please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 208: (" . $ClubMember_result->code . ") " . $ClubMember_result->getMessage() . "</p>";
					die();
				}
				// Audit:
				$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
				$z_storeResults = $z_storeRequest->execute();
				$z_storeResult = $z_storeResults->getFirstRecord();
				$z_storeRecordID = $z_storeResult->getRecordId();
				$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
				$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
				$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'ClubMembership::RecordID|' . $key . ';');
				$resultAudit = $editAudit->execute();
			}
		}
		## Renew Memberships ############################
		// key = RecordID; value = ID_Club
		foreach ($ActiveMembership_Renew as $key => $value) {
			if (!empty($value)) {
				//Error Checking, and price compilation
				
			}
		}
	}
	
	// Fail if no renewal choice is selected
	if ($RegistrationSubmitted2 && !$NewClubMembership) {
		if (!$Renew_Admin && !$Renew_Coach && !$Renew_Medical && !$Renew_Player && !$Renew_Referee) {
			$fail .= "Please select at least one Renewal Choice. <br />";
		}
	}
	
	// Either finish loading form with error messages, or go to next step
	$RegistrationStage = 2;
	if (!empty($_POST['Back'])) {
		$RegistrationStage = 1;
	} elseif (!empty($fail)) {
		echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
	} elseif ($NewClubMembership) {
		$RegistrationStage = 2;
	} else {
		$RegistrationStage = 3;
		$_SESSION['Renew_Player'] = $Renew_Player;
		$_SESSION['Renew_Coach'] = $Renew_Coach;
		$_SESSION['Renew_Medical'] = $Renew_Medical;
		$_SESSION['Renew_Referee'] = $Renew_Referee;
		$_SESSION['Renew_Admin'] = $Renew_Admin;
		$Renewal_Types = array();
		$Renewal_Types['Player'] = $Renew_Player;
		$Renewal_Types['Coach'] = $Renew_Coach;
		$Renewal_Types['Medical'] = $Renew_Medical;
		$Renewal_Types['Referee'] = $Renew_Referee;
		$Renewal_Types['Admin'] = $Renew_Admin;
		$_SESSION['Renewal_Types'] = $Renewal_Types;
	}
} elseif ($RegistrationStage == 2) {
	include 'GetFMData/Registration2.php';
	
	if (FileMaker::isError($related_RegistrationPayments)) {
		$related_RegistrationPayments_count = 0;
	} else {
		$related_RegistrationPayments_count = count($related_RegistrationPayments);
		$LastRegistrationDate_Coach = $record_Registration2->getField('Personnel__Payment.USARCoach::sum_LastDatePaid');
		$LastRegistrationDate_Admin = $record_Registration2->getField('Personnel__Payment.USARAdmin::sum_LastDatePaid');
		$LastRegistrationDate_Referee = $record_Registration2->getField('Personnel__Payment.USARReferee::sum_LastDatePaid');
		$LastRegistrationDate_Player = $record_Registration2->getField('Personnel__Payment.USARPlayer::sum_LastDatePaid');
		$OneYearAgo = date('m/d/Y', strtotime("-1 year"));
		$EightMonthsAgo = date('m/d/Y', strtotime("-8 months"));
		
		// Calculate new anniversary date(s)
		if (validate_date_filemaker($LastRegistrationDate_Coach)) {
			if (date('m/d/Y', $LastRegistrationDate_Coach) > $OneYearAgo) {
				$LastRegistrationDate_Coach_array = explode('/', $LastRegistrationDate_Coach);
				$NextRegistrationDate_Coach = $LastRegistrationDate_Coach_array[0] . "/" . $LastRegistrationDate_Coach_array[1] . "/" . ($LastRegistrationDate_Coach_array[2] + 1);
				if (date('m/d/Y', $LastRegistrationDate_Coach) > $EightMonthsAgo) {
					$RegistrationColor_Coach = "green";
					$RegistrationStatus_Coach = "<br /><span style='color: green'>Your Registration Status is GOOD. There is no need to renew at this time.</span>";
				} else {
					$RegistrationColor_Coach = "orange";
					$RegistrationStatus_Coach = "<br /><span style='color: orange'>Your Renewal date is coming up. It is recommended that you renew at this time.</span>";
				}
			} else {
				$NextRegistrationDate_Coach = date('m/d/Y', strtotime("+1 year"));
				$RegistrationColor_Coach = "red";
				$RegistrationStatus_Coach = "<br /><span style='color: red'>Your Registration is EXPIRED. You must renew your registration to be involved with USA Rugby activities.</span>";
			}
		} else {
			$NextRegistrationDate_Coach = date('m/d/Y', strtotime("+1 year"));
			$RegistrationColor_Coach = "black";
		}
		if (validate_date_filemaker($LastRegistrationDate_Admin)) {
			if (date('m/d/Y', $LastRegistrationDate_Admin) > $OneYearAgo) {
				$LastRegistrationDate_Admin_array = explode('/', $LastRegistrationDate_Admin);
				$NextRegistrationDate_Admin = $LastRegistrationDate_Admin_array[0] . "/" . $LastRegistrationDate_Admin_array[1] . "/" . ($LastRegistrationDate_Admin_array[2] + 1);
				if (date('m/d/Y', $LastRegistrationDate_Admin) > $EightMonthsAgo) {
					$RegistrationColor_Admin = "green";
					$RegistrationStatus_Admin = "<br /><span style='color: green'>Your Registration Status is GOOD. There is no need to renew at this time.</span>";
				} else {
					$RegistrationColor_Admin = "orange";
					$RegistrationStatus_Admin = "<br /><span style='color: orange'>Your Renewal date is coming up. It is recommended that you renew at this time.</span>";
				}
			} else {
				$NextRegistrationDate_Admin = date('m/d/Y', strtotime("+1 year"));
				$RegistrationColor_Admin = "red";
				$RegistrationStatus_Admin = "<br /><span style='color: red'>Your Registration is EXPIRED. You must renew your registration to be involved with USA Rugby activities.</span>";
			}
		} else {
			$NextRegistrationDate_Admin = date('m/d/Y', strtotime("+1 year"));
			$RegistrationColor_Admin = "black";
		}
		if (validate_date_filemaker($LastRegistrationDate_Referee)) {
			if (date('m/d/Y', $LastRegistrationDate_Referee) > $OneYearAgo) {
				$LastRegistrationDate_Referee_array = explode('/', $LastRegistrationDate_Referee);
				$NextRegistrationDate_Referee = $LastRegistrationDate_Referee_array[0] . "/" . $LastRegistrationDate_Referee_array[1] . "/" . ($LastRegistrationDate_Referee_array[2] + 1);
				if (date('m/d/Y', $LastRegistrationDate_Referee) > $EightMonthsAgo) {
					$RegistrationColor_Referee = "green";
					$RegistrationStatus_Referee = "<br /><span style='color: green'>Your Registration Status is GOOD. There is no need to renew at this time.</span>";
				} else {
					$RegistrationColor_Referee = "orange";
					$RegistrationStatus_Referee = "<br /><span style='color: orange'>Your Renewal date is coming up. It is recommended that you renew at this time.</span>";
				}
			} else {
				$NextRegistrationDate_Referee = date('m/d/Y', strtotime("+1 year"));
				$RegistrationColor_Referee = "red";
				$RegistrationStatus_Referee = "<br /><span style='color: red'>Your Registration is EXPIRED. You must renew your registration to be involved with USA Rugby activities.</span>";
			}
		} else {
			$NextRegistrationDate_Referee = date('m/d/Y', strtotime("+1 year"));
			$RegistrationColor_Referee = "black";
		}
		if (validate_date_filemaker($LastRegistrationDate_Player)) {
			if (date('m/d/Y', $LastRegistrationDate_Player) > $OneYearAgo) {
				$LastRegistrationDate_Player_array = explode('/', $LastRegistrationDate_Player);
				$NextRegistrationDate_Player = $LastRegistrationDate_Player_array[0] . "/" . $LastRegistrationDate_Player_array[1] . "/" . ($LastRegistrationDate_Player_array[2] + 1);
				if (date('m/d/Y', $LastRegistrationDate_Player) > $EightMonthsAgo) {
					$RegistrationColor_Player = "green";
					$RegistrationStatus_Player = "<br /><span style='color: green'>Your Registration Status is GOOD. There is no need to renew at this time.</span>";
				} else {
					$RegistrationColor_Player = "orange";
					$RegistrationStatus_Player = "<br /><span style='color: orange'>Your Renewal date is coming up. It is recommended that you renew at this time.</span>";
				}
			} else {
				$NextRegistrationDate_Player = date('m/d/Y', strtotime("+1 year"));
				$RegistrationColor_Player = "red";
				$RegistrationStatus_Player = "<br /><span style='color: red'>Your Registration is EXPIRED. You must renew your registration to be involved with USA Rugby activities.</span>";
			}
		} else {
			$NextRegistrationDate_Player = date('m/d/Y', strtotime("+1 year"));
			$RegistrationColor_Player = "black";
		}
	}
}
//---- / registration2 form

//
// registration3 form
$RegistrationSubmitted3 = isset($_POST['submitted-registration3']) ? $_POST['submitted-registration3'] : false;
$BackgroundScreeningSubmitted = isset($_POST['process-background-screening']) ? $_POST['process-background-screening'] : false;

if ($RegistrationSubmitted3) {
	if ($BackgroundScreeningSubmitted) {
		//## Retrieve data
		$SSNa = isset($_POST['SSNa']) ? $_POST['SSNa'] : "";
		$SSNb = isset($_POST['SSNb']) ? $_POST['SSNb'] : "";
		$SSNc = isset($_POST['SSNc']) ? $_POST['SSNc'] : "";
		$BackgroundWaiver = $_POST['waiver_background_check'] == "1" ? true : false;
		
		//## Validate data
		if (!preg_match('/^[0-9]{3}$/', $SSNa) || !preg_match('/^[0-9]{2}$/', $SSNb) || !preg_match('/^[0-9]{4}$/', $SSNc)) {
			$fail .= "The Social Security number is invalid <br />";
		}
		if (!$BackgroundWaiver) {
			$fail .= "You must check the box stating that you have read and understand the background screening agreement. <br />";
		}
	}
	
	// Either finish loading form with error messages, or go to next step
	$RegistrationStage = 3;
	if (!empty($_POST['Back'])) {
		$RegistrationStage = 2;
		include 'GetFMData/Registration2.php';
	} elseif (!empty($fail)) {
		echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
	} else {
		$RegistrationStage = 4;
	}
} elseif ($RegistrationStage == 3) {
	$Renew_Player = $_SESSION['Renew_Player'];
	$Renew_Coach = $_SESSION['Renew_Coach'];
	$Renew_Medical = $_SESSION['Renew_Medical'];
	$Renew_Referee = $_SESSION['Renew_Referee'];
	$Renew_Admin = $_SESSION['Renew_Admin'];
	if ($Renew_Referee || $Renew_Admin || $Renew_Coach || $Renew_Medical) {
		// Background check
		$Background_Check_Needed = true;
	} else {
		$Background_Check_Needed = false;
	}
}
//---- / registration3 form

//
// registration4 form submitted
$RegistrationSubmitted4 = isset($_POST['submitted-registration4']) ? $_POST['submitted-registration4'] : false;

if ($RegistrationSubmitted4) {
	//## Retrieve data
	$waiver_release_liability = isset($_POST['waiver_release_liability']) ? $_POST['waiver_release_liability'] : "";
	$waiver_rules = isset($_POST['waiver_rules']) ? $_POST['waiver_rules'] : "";
	if ($U18) {
		$waiver_release_liability_parent = isset($_POST['waiver_release_liability_parent']) ? $_POST['waiver_release_liability_parent'] : "";
		$waiver_rules_parent = isset($_POST['waiver_rules_parent']) ? $_POST['waiver_rules_parent'] : "";
	}
	
	//## Data validation
	$fail .= empty($waiver_release_liability) ? "You did not agree to the Release of Liability <br />" : "";
	$fail .= empty($waiver_rules) ? "You did not agree to the Rugby Rules <br />" : "";
	if ($U18) {
		$fail .= empty($waiver_release_liability_parent) ? "Your parent/guardian did not agree to the Release of Liability <br />" : "";
		$fail .= empty($waiver_rules_parent) ? "Your parent/guardian did not agree to the Rugby Rules <br />" : "";
	}
	
	// Either finish loading form with error messages, or go to next step
	$RegistrationStage = 4;
	if (!empty($_POST['Back'])) {
		$RegistrationStage = 3;
	} elseif (!empty($fail)) {
		echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
	} else {
		$RegistrationStage = 5;
	}
}
//---- / registration4 form submitted

//
// registration5 form submitted
$RegistrationSubmitted5 = isset($_POST['submitted-registration5']) ? $_POST['submitted-registration5'] : false;

if ($RegistrationSubmitted5) {
	//## Retrieve data
	$Donation_amount = isset($_POST['Donation_amount']) ? $_POST['Donation_amount'] : 0;
	
	//## Data validation
	
	// Either finish loading form with error messages, or go to next step
	$RegistrationStage = 5;
	if (!empty($_POST['Back'])) {
		$RegistrationStage = 4;
	} elseif (!empty($fail)) {
		echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
	} else {
		$RegistrationStage = 6;
	}
} elseif ($RegistrationStage == 5) {
	$USARugbyFee = 29;
	$UnionFee = 20;
	$Renewal_Types = $_SESSION['Renewal_Types'];
	foreach ($Renewal_Types as $type => $active) {
		if ($active) {
			$Subtotal += $USARugbyFee + $UnionFee;
		}
	}
	$Fee = (round(($Subtotal + $Donation_amount) * .029, 2)) + .3;
	$Total = number_format((float)$Subtotal + $Donation_amount + $Fee, 2, '.', ',');
}
//---- / registration4 form submitted

//
// Tab 1: ClubMembership NoRegistration form submitted
if (!$RegistrationActivate) {
	if (isset($_POST['submitted-clubmembership'])) {
		$activeTab = 1;
		include 'GetFMData/Registration1.php';
		include 'GetFMData/Registration2.php';
		
		## Retrieve POSTed data ############################
		$ID_Club = (isset ($_POST['ID_Club']) ? fix_string($_POST['ID_Club']) : "");
		$ClubRole = isset($_POST['ClubRole']) ? fix_string($_POST['ClubRole']) : "";
		$Primary_flag = isset($_POST['Primary_flag']) ? fix_string($_POST['Primary_flag']) : "";
		$StartDate = "";
		$OtherClub = isset($_POST['OtherClub']) ? fix_string($_POST['OtherClub']) : "";
		$DoNotBelongToAClub_flag = ($OtherClub == 'NoClub' ? 1 : "");
		$UnlistedClub_flag = ($OtherClub == 'UnlistedClub' ? 1 : "");
		$UnlistedClub_Name = isset($_POST['UnlistedClub_Name']) ? fix_string($_POST['UnlistedClub_Name']) : "";
		$UnlistedClub_City = isset($_POST['UnlistedClub_City']) ? fix_string($_POST['UnlistedClub_City']) : "";
		$UnlistedClub_State = isset($_POST['UnlistedClub_State']) ? fix_string($_POST['UnlistedClub_State']) : "";
		$UnlistedClub_Role = isset($_POST['UnlistedClub_Role']) ? fix_string($_POST['UnlistedClub_Role']) : "";
		$UnlistedClub_StartDate = isset($_POST['UnlistedClub_StartDate']) ? fix_string($_POST['UnlistedClub_StartDate']) : "";
		$NoClub_Role = isset($_POST['NoClub_Role']) ? fix_string($_POST['NoClub_Role']) : "";
		$StartDatesave = "";
		if (isset($_POST['StartDate'])) {
			if (validate_date($_POST['StartDate']) || validate_date_filemaker($_POST['StartDate'])) {
				$StartDateold = new DateTime($_POST['StartDate']);
				$StartDate = $StartDateold->format('m/d/Y');
				$StartDatesave = $StartDateold->format('Y-m-d');
			} else {
				$StartDatesave = $_POST['StartDate'];
			}
		}
		$UnlistedClub_StartDatesave = "";
		if (isset($_POST['UnlistedClub_StartDate'])) {
			if (validate_date($_POST['UnlistedClub_StartDate']) || validate_date_filemaker($_POST['UnlistedClub_StartDate'])) {
				$UnlistedClub_StartDateold = new DateTime($_POST['UnlistedClub_StartDate']);
				$UnlistedClub_StartDate = $UnlistedClub_StartDateold->format('m/d/Y');
				$UnlistedClub_StartDatesave = $UnlistedClub_StartDateold->format('Y-m-d');
			} else {
				$UnlistedClub_StartDatesave = $_POST['UnlistedClub_StartDate'];
			}
		}
		$ClubMemberHistory_Original = isset($_SESSION['ClubMemberHistory_Original']) ? $_SESSION['ClubMemberHistory_Original'] : "";
		$ClubMemberHistory_Update = isset($_POST['ClubMembershipHistory_Update']) ? $_POST['ClubMembershipHistory_Update'] : "";
		$ClubMemberHistory_Update_Primary = isset($_POST['ClubMembershipHistory_UpdatePrimary']) ? $_POST['ClubMembershipHistory_UpdatePrimary'] : "";
		$ClubMemberHistory_Original_Primary = isset($_SESSION['ClubMemberPrimaryHistory_Original']) ? $_SESSION['ClubMemberPrimaryHistory_Original'] : "";
		$ClubMemberHistory_changed = ($ClubMemberHistory_Update == $ClubMemberHistory_Original ? false : true);
		
		//## Apply any Club Membership history changes
		$SkipMembershipTest = true;
		if ($ClubMemberHistory_Update_Primary != $ClubMemberHistory_Original_Primary) {
			$SkipMembershipTest = true;
			//##If one of the diff history records changed their primary flag, set that one record and unset all others
			foreach ($related_ClubMembership as $ClubMembership_record) {
				if ($ClubMembership_record->getRecordId() == $ClubMemberHistory_Update_Primary) {
					$ClubMembership_edit = $fm->newEditCommand('Personnel__ClubMembership', $ClubMembership_record->getRecordId());
					$ClubMembership_edit->setField('Primary_flag', 1);
					$SetPrimary_result = $ClubMembership_edit->execute();
					if (FileMaker::isError($SetPrimary_result)) {
						echo "<p>Error: There was a problem setting a club's primary flag. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 209: " . $SetPrimary_result->getMessage() . "</p>";
						exit;
					}
				} else {
					$ClubMembership_edit = $fm->newEditCommand('Personnel__ClubMembership', $ClubMembership_record->getRecordId());
					$ClubMembership_edit->setField('Primary_flag', "");
					$ClearPrimary_result = $ClubMembership_edit->execute();
					if (FileMaker::isError($ClearPrimary_result)) {
						echo "<p>Error: There was a problem removing a club's primary flag. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 210: " . $ClearPrimary_result->getMessage() . "</p>";
						exit;
					}
				}
			}
		}
		if ($ClubMemberHistory_changed) {
			while ($a = current($ClubMemberHistory_Update) && $b = current($ClubMemberHistory_Original)) { //For each element in the array
				$update_RecordID = key($ClubMemberHistory_Update);
				$update_record = current($ClubMemberHistory_Update);
				$original_record = current($ClubMemberHistory_Original);
				$diff = array_diff_assoc($update_record, $original_record);
				if (!empty($diff)) { //Edit the record if there is a difference from the update
					$ClubMember_edit = $fm->newEditCommand('Personnel__ClubMembership', $update_RecordID);
					foreach ($diff as $ClubMember_field => $ClubMember_field_value) { //Update only the fields that were in the diff array as changes
						if ($ClubMember_field == "StartDate" || $ClubMember_field == "EndDate") {
							if (validate_date($ClubMember_field_value) == false && validate_date_filemaker($ClubMember_field_value) == false) {
								$fail .= "One of your club membership dates was invalid. Its value has been reverted. <br />";
							} else {
								$ClubMember_edit->setField($ClubMember_field, $ClubMember_field_value);
							}
						} else {
							$ClubMember_edit->setField($ClubMember_field, $ClubMember_field_value);
						}
						if ($ClubMember_field == "EndDate" && !empty($ClubMember_field_value)) {
							$ClubMember_edit->setField('Primary_flag', "");
						}
					}
					$ClubMember_edit->setField('z_ModifiedByID', $eMail);
					$ClubMember_edit->setField('z_ModifiedByName', 'web');
					$ClubMember_result = $ClubMember_edit->execute();
					if (FileMaker::isError($ClubMember_result) && !empty($ClubMember_result->code)) { //supress error is date is invalid, and no other changes were made to record
						echo "<p>Error: There was a problem updating your club membership history. If this continues, please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 211: (" . $ClubMember_result->code . ") " . $ClubMember_result->getMessage() . "</p>";
						die();
					}
					// Audit:
					$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
					$z_storeResults = $z_storeRequest->execute();
					$z_storeResult = $z_storeResults->getFirstRecord();
					$z_storeRecordID = $z_storeResult->getRecordId();
					$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
					$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
					$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'ClubMembership::RecordID|' . $update_RecordID . ';');
					$resultAudit = $editAudit->execute();
				}
				next($ClubMemberHistory_Update);
				next($ClubMemberHistory_Original);
			}
		}
		
		## Fail Tests #######################################
		if (!empty($UnlistedClub_flag) && (empty($UnlistedClub_Name) || empty($UnlistedClub_City) || empty($UnlistedClub_Role))) {
			$fail .= "An unlisted club needs a name, city, and role.";
		} elseif ($ActiveClubMembershipCount == 0 && ((empty($ID_Club)) && (empty($UnlistedClub_flag) && empty($DoNotBelongToAClub_flag))) && !$SkipMembershipTest) {
			$fail .= "You must specify at least one club membership.";
		}
		
		if ($DoNotBelongToAClub_flag == 1 && empty($NoClub_Role)){
			$fail .= "You must specify your role.";
		}
		
		if ($UnlistedClub_flag != 1 && $DoNotBelongToAClub_flag != 1 && (!empty($ID_Club) && (empty($ClubRole) || empty($StartDate)) )) {
			$fail .= validate_empty_field($ClubRole, 'Club Role');
			$fail .= validate_empty_field($StartDate, 'Club Start Date');
		}
		
		## Write Data to Database ############################
		if ($UnlistedClub_flag == 1 && empty($fail)) {
			$edit = $fm->newEditCommand('Member-Registration1-Demographic', $record_Registration1->getRecordId());
			$edit->setField('unlistedClubName', $UnlistedClub_Name);
			$edit->setField('unlistedClubCity', $UnlistedClub_City);
			$edit->setField('unlistedClubState', $UnlistedClub_State);
			$edit->setField('z_ModifiedByID', $eMail);
			$edit->setField('z_ModifiedByName', 'web');
			
			$result = $edit->execute();
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 212: " . $result->getMessage() . "</p>";
				die();
			}
			$Primary_flag = 1;
		}
		
		// Club Membership //
		if ((!empty($ID_Club) || $UnlistedClub_flag == 1 || $DoNotBelongToAClub_flag == 1) && empty($fail)) {
			$ID_Club = $UnlistedClub_flag == 1 ? "NotInList" : $ID_Club;
			if ($UnlistedClub_flag == 1){
				$CalculatedRole = $UnlistedClub_Role;
				$CalculatedStartDate = $UnlistedClub_StartDate;
			} elseif ($DoNotBelongToAClub_flag == 1) {
				$CalculatedRole = $NoClub_Role;
				$CalculatedStartDate = $today;
			} else {
				$CalculatedRole = $ClubRole;
				$CalculatedStartDate = $StartDate;
			}
			$clubMembership_data = array(
				'ID_Personnel' => $ID_Personnel,
				'ID_Club' => $ID_Club,
				'Role' => $CalculatedRole,
				'Primary_flag' => $Primary_flag,
				'StartDate' => $CalculatedStartDate,
				'DoNotBelongToAClub_flag' => $DoNotBelongToAClub_flag,
				'UnlistedClub_flag' => $UnlistedClub_flag,
				'z_ModifiedByID' => $eMail,
				'z_ModifiedByName' => 'web',
			);
			// If adding a new primary club, remove the primary club flag from existing related record
			if ($PrimaryClubMembershipCount > 0 && $Primary_flag == 1) {
				foreach ($PrimaryClubMembership_records as $PrimaryClubMembership_record) {
					$PrimaryClubMembership_edit = $fm->newEditCommand('Personnel__ClubMembership', $PrimaryClubMembership_record->getRecordId());
					$PrimaryClubMembership_edit->setField('Primary_flag', "");
					$ClearPrimary_result = $PrimaryClubMembership_edit->execute();
					if (FileMaker::isError($ClearPrimary_result)) {
						echo "<p>Error: There was a problem removing a club's primary flag. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 213: " . $ClearPrimary_result->getMessage() . "</p>";
						exit;
					}
				}
			}
			
			$newClubMembershipRequest =& $fm->newAddCommand('Personnel__ClubMembership', $clubMembership_data);
			$result = $newClubMembershipRequest->execute();
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem adding the new club memberships record. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 214: " . $result->getMessage() . "</p>";
				exit;
			}
			
			$ClubMembership_NewRecord = $result->getFirstRecord();
			$ClubMembership_RecordID = $ClubMembership_NewRecord->getRecordId();
			// Audit:
			$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
			$z_storeResults = $z_storeRequest->execute();
			$z_storeResult = $z_storeResults->getFirstRecord();
			$z_storeRecordID = $z_storeResult->getRecordId();
			$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
			$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
			$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'ClubMembership::RecordID|' . $ClubMembership_RecordID . ';');
			$resultAudit = $editAudit->execute();
			
			$ID_Club = "";
			$ClubRole = "";
			$StartDate = "";
			$Primary_flag = "";
		}
		
		if (!empty($fail)) {
			echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
			
		} elseif (!$RegistrationActivate && empty($fail) ) {
			//Refresh record so that field variables have updated values
//			include 'GetFMData/Registration2.php';
			//Go to next tab automatically
			$EditingID = $EditingMemberProfile == True ? "&ID=" . $ID_Personnel : "";
			header('location:body.php?activeTab=2' . $EditingID);
		}
	} elseif ($activeTab == 1) {
		include 'GetFMData/Registration1.php';
		include 'GetFMData/Registration2.php';
	}
}
//---- / ClubMembership NoRegistration form submitted

//
// Tab 2: Profile form submitted
if (isset($_POST['submitted-profile'])) {
	if (!$RegistrationActivate) {
		include 'GetFMData/Registration1.php';
		include 'GetFMData/Registration2.php';
		
		## Retrieve POSTed data ############################
		$firstName = (isset ($_POST ['firstName']) ? fix_string($_POST ['firstName']) : "");
		$middleName = (isset ($_POST ['middleName']) ? fix_string($_POST ['middleName']) : "");
		$lastName = (isset ($_POST ['lastName']) ? fix_string($_POST ['lastName']) : "");
		$nickName = (isset ($_POST ['nickName']) ? fix_string($_POST ['nickName']) : "");
		$nickName = $nickName == $firstName ? "" : $nickName;
		$DOB = "";
		$DOBsave = "";
		if (isset($_POST['DOB'])) {
			if (validate_date($_POST['DOB']) || validate_date_filemaker($_POST['DOB'])) {
				$DOBold = new DateTime($_POST['DOB']);
				$DOB = $DOBold->format('m/d/Y');
				$DOBsave = $DOBold->format('Y-m-d');
			} else {
				$DOBsave = $_POST['DOB'];
			}
		}
		$gender = (isset ($_POST['gender']) ? fix_string($_POST['gender']) : "");
		$ethnicity = (isset($_POST['ethnicity']) ? fix_string($_POST['ethnicity']) : "");
		$homeAddress1 = (isset ($_POST['homeAddress1']) ? fix_string($_POST['homeAddress1']) : "");
		$homeAddress2 = (isset ($_POST['homeAddress2']) ? fix_string($_POST['homeAddress2']) : "");
		$City = (isset ($_POST['City']) ? fix_string($_POST['City']) : "");
		$State = (isset ($_POST['State']) ? fix_string($_POST['State']) : "");
		$zipCode = (isset ($_POST['zipCode']) ? fix_string($_POST['zipCode']) : "");
		$Country = (isset ($_POST['Country']) ? fix_string($_POST['Country']) : "");
		$PrimaryPhoneNumber = (isset ($_POST['PrimaryPhoneNumber']) ? fix_string($_POST['PrimaryPhoneNumber']) : "");
		$PrimaryPhoneText_flag = isset ($_POST['PrimaryPhoneText_flag']) ? 1 : "";
		$MembershipID = (isset ($_POST['MembershipID']) ? fix_string($_POST['MembershipID']) : "");
		
		## Fail Tests #######################################
		$fail .= validate_empty_field($firstName, "First Name");
		$fail .= validate_empty_field($lastName, "Last Name");
		$fail .= validate_DOB($DOB);
		$fail .= validate_empty_field($gender, "Gender");
		$fail .= validate_empty_field($ethnicity, "Race / Ethnicity");
		$fail .= validate_empty_field($homeAddress1, "Home Address: Street 1");
		$fail .= validate_empty_field($City, "Home Address: City");
		$fail .= validate_zip($zipCode);
		$fail .= validate_empty_field($Country, "Country");
		$fail .= validate_empty_field($PrimaryPhoneNumber, "Primary Phone Number");
		$fail_MembershipID = validate_Membership($MembershipID);
		if (!empty($fail_MembershipID)) {
			$fail .= $fail_MembershipID;
			$MembershipID = "";
		}
		
		## Write Data to Database ############################
		$edit = $fm->newEditCommand('Member-Registration1-Demographic', $record_Registration1->getRecordId());
		$edit->setField('firstName', $firstName);
		$edit->setField('middleName', $middleName);
		$edit->setField('lastName', $lastName);
		$edit->setField('nickName', $nickName);
		$edit->setField('DOB', $DOB);
		$edit->setField('gender', $gender);
		$edit->setField('RaceEthnicity', $ethnicity);
		$edit->setField('homeAddress1', $homeAddress1);
		$edit->setField('homeAddress2', $homeAddress2);
		$edit->setField('City', $City);
		$edit->setField('State', $State);
		$edit->setField('zipCode', $zipCode);
		$edit->setField('Country', $Country);
		$edit->setField('PrimaryPhoneNumber', $PrimaryPhoneNumber);
		$edit->setField('PrimaryPhoneText_flag', $PrimaryPhoneText_flag);
		$edit->setField('MembershipID', $MembershipID);
		
		$edit->setField('z_ModifiedByID', $eMail);
		$edit->setField('z_ModifiedByName', 'web');
		
		$result = $edit->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 215: " . $result->getMessage() . "</p>";
			die();
		}
		
	}
	
	include 'GetFMData/Tab2-GetData.php';
	$activeTab = 2;
	
	$Birthplace_City = (isset($_POST['BirthplaceCity']) ? fix_string($_POST['BirthplaceCity']) : "");
	$Birthplace_State = (isset($_POST['BirthplaceState']) ? fix_string($_POST['BirthplaceState']) : "");
	$Birthplace_Country = (isset($_POST['BirthplaceCountry']) ? fix_string($_POST['BirthplaceCountry']) : "");
	##// Replace \n\r with just \n for FileMaker
	$Bio = (isset ($_POST['Bio']) ? str_replace("\r", "", $_POST['Bio']) : "");
	
	if (!$IsPlayer || !$U18) {
		// A Youth Player doesn't need these fields, as their Guardian 1 fields double for them
		$emergencyContactFirstName = (isset ($_POST['emergencyContactFirstName']) ? fix_string($_POST['emergencyContactFirstName']) : "");
		$emergencyContactLastName = (isset ($_POST['emergencyContactLastName']) ? fix_string($_POST['emergencyContactLastName']) : "");
		$emergencyContactNumber = (isset ($_POST['emergencyContactNumber']) ? fix_string($_POST['emergencyContactNumber']) : "");
		$emergencyContactRelationship = (isset ($_POST['emergencyContactRelationship']) ? fix_string($_POST['emergencyContactRelationship']) : "");
	}
	$spouseName = (isset ($_POST['spouseName']) ? fix_string($_POST['spouseName']) : "");
	$spouseEmail = (isset ($_POST['spouseEmail']) ? fix_string($_POST['spouseEmail']) : "");
	$spouseCell = (isset ($_POST['spouseCell']) ? fix_string($_POST['spouseCell']) : "");
	$Guardian1Type = (isset ($_POST['Guardian1Type']) ? fix_string($_POST['Guardian1Type']) : "");
	$Guardian1FirstName = (isset ($_POST['Guardian1FirstName']) ? fix_string($_POST['Guardian1FirstName']) : "");
	$Guardian1LastName = (isset ($_POST['Guardian1LastName']) ? fix_string($_POST['Guardian1LastName']) : "");
	$Guardian1eMail = (isset ($_POST['Guardian1eMail']) ? fix_string($_POST['Guardian1eMail']) : "");
	$Guardian1Cell = (isset ($_POST['Guardian1Cell']) ? fix_string($_POST['Guardian1Cell']) : "");
	$Guardian2Type = (isset ($_POST['Guardian2Type']) ? fix_string($_POST['Guardian2Type']) : "");
	$Guardian2FirstName = (isset ($_POST['Guardian2FirstName']) ? fix_string($_POST['Guardian2FirstName']) : "");
	$Guardian2LastName = (isset ($_POST['Guardian2LastName']) ? fix_string($_POST['Guardian2LastName']) : "");
	$Guardian2eMail = (isset ($_POST['Guardian2eMail']) ? fix_string($_POST['Guardian2eMail']) : "");
	$Guardian2Cell = (isset ($_POST['Guardian2Cell']) ? fix_string($_POST['Guardian2Cell']) : "");
	$Guardian3Type = (isset ($_POST['Guardian3Type']) ? fix_string($_POST['Guardian3Type']) : "");
	$Guardian3FirstName = (isset ($_POST['Guardian3FirstName']) ? fix_string($_POST['Guardian3FirstName']) : "");
	$Guardian3LastName = (isset ($_POST['Guardian3LastName']) ? fix_string($_POST['Guardian3LastName']) : "");
	$Guardian3eMail = (isset ($_POST['Guardian3eMail']) ? fix_string($_POST['Guardian3eMail']) : "");
	$Guardian3Cell = (isset ($_POST['Guardian3Cell']) ? fix_string($_POST['Guardian3Cell']) : "");
	$Guardian4Type = (isset ($_POST['Guardian4Type']) ? fix_string($_POST['Guardian4Type']) : "");
	$Guardian4FirstName = (isset ($_POST['Guardian4FirstName']) ? fix_string($_POST['Guardian4FirstName']) : "");
	$Guardian4LastName = (isset ($_POST['Guardian4LastName']) ? fix_string($_POST['Guardian4LastName']) : "");
	$Guardian4eMail = (isset ($_POST['Guardian4eMail']) ? fix_string($_POST['Guardian4eMail']) : "");
	$Guardian4Cell = (isset ($_POST['Guardian4Cell']) ? fix_string($_POST['Guardian4Cell']) : "");
	$referenceType1 = (isset ($_POST['referenceType1']) ? fix_string($_POST['referenceType1']) : "");
	$referenceFirstName1 = (isset ($_POST['referenceFirstName1']) ? fix_string($_POST['referenceFirstName1']) : "");
	$referenceLastName1 = (isset ($_POST['referenceLastName1']) ? fix_string($_POST['referenceLastName1']) : "");
	$referencePhone1 = (isset ($_POST['referencePhone1']) ? fix_string($_POST['referencePhone1']) : "");
	$referenceEmail1 = (isset ($_POST['referenceEmail1']) ? fix_string($_POST['referenceEmail1']) : "");
	$referenceType2 = (isset ($_POST['referenceType2']) ? fix_string($_POST['referenceType2']) : "");
	$referenceFirstName2 = (isset ($_POST['referenceFirstName2']) ? fix_string($_POST['referenceFirstName2']) : "");
	$referenceLastName2 = (isset ($_POST['referenceLastName2']) ? fix_string($_POST['referenceLastName2']) : "");
	$referencePhone2 = (isset ($_POST['referencePhone2']) ? fix_string($_POST['referencePhone2']) : "");
	$referenceEmail2 = (isset ($_POST['referenceEmail2']) ? fix_string($_POST['referenceEmail2']) : "");
	$referenceType3 = (isset ($_POST['referenceType3']) ? fix_string($_POST['referenceType3']) : "");
	$referenceFirstName3 = (isset ($_POST['referenceFirstName3']) ? fix_string($_POST['referenceFirstName3']) : "");
	$referenceLastName3 = (isset ($_POST['referenceLastName3']) ? fix_string($_POST['referenceLastName3']) : "");
	$referencePhone3 = (isset ($_POST['referencePhone3']) ? fix_string($_POST['referencePhone3']) : "");
	$referenceEmail3 = (isset ($_POST['referenceEmail3']) ? fix_string($_POST['referenceEmail3']) : "");
	
	$yearStartedPlaying = (isset ($_POST['yearStartedPlaying']) ? fix_string($_POST['yearStartedPlaying']) : "");
	$dominantHand = (isset ($_POST['dominantHand']) ? fix_string($_POST['dominantHand']) : "");
	$dominantFoot = (isset ($_POST['dominantFoot']) ? fix_string($_POST['dominantFoot']) : "");
	$primary15sPosition = (isset ($_POST['primary15sPosition']) ? fix_string($_POST['primary15sPosition']) : "");
	$secondary15sPosition = (isset ($_POST['secondary15sPosition']) ? fix_string($_POST['secondary15sPosition']) : "");
	$primary7sPosition = (isset ($_POST['primary7sPosition']) ? fix_string($_POST['primary7sPosition']) : "");
	$secondary7sPosition = (isset ($_POST['secondary7sPosition']) ? fix_string($_POST['secondary7sPosition']) : "");
	$HighlightVideoLink = (isset ($_POST['HighlightVideoLink']) ? fix_string($_POST['HighlightVideoLink']) : "");
	$FullMatchLink1 = (isset ($_POST['FullMatchLink1']) ? fix_string($_POST['FullMatchLink1']) : "");
	$FullMatchLink2 = (isset ($_POST['FullMatchLink2']) ? fix_string($_POST['FullMatchLink2']) : "");
	$FullMatchLink3 = (isset ($_POST['FullMatchLink3']) ? fix_string($_POST['FullMatchLink3']) : "");
	
	$MatchJerseySize = (isset ($_POST['MatchJerseySize']) ? fix_string($_POST['MatchJerseySize']) : "");
	$MatchShortsSize = (isset ($_POST['MatchShortsSize']) ? fix_string($_POST['MatchShortsSize']) : "");
	$tShirtSize = (isset ($_POST['tShirtSize']) ? fix_string($_POST['tShirtSize']) : "");
	$poloSize = (isset ($_POST['poloSize']) ? fix_string($_POST['poloSize']) : "");
	$shortsSize = (isset ($_POST['shortsSize']) ? fix_string($_POST['shortsSize']) : "");
	$trackSuitBottomSize = (isset ($_POST['trackSuitBottomSize']) ? fix_string($_POST['trackSuitBottomSize']) : "");
	$trackSuitTopSize = (isset ($_POST['trackSuitTopSize']) ? fix_string($_POST['trackSuitTopSize']) : "");
	$shoeSize = (isset ($_POST['shoeSize']) ? fix_string($_POST['shoeSize']) : "");
	
	$heightFeet = isset($_POST['heightFeet']) ? fix_string($_POST['heightFeet']) : "";
	$heightInches = isset($_POST['heightInches']) ? fix_string($_POST['heightInches']) : "";
	$heightMeters = isset($_POST['heightMeters']) ? fix_string($_POST['heightMeters']) : "";
	$Height_UM = isset($_POST['Height_UM']) ? fix_string($_POST['Height_UM']) : "ft";
	$Weight = isset($_POST['Weight']) ? fix_string($_POST['Weight']) : "";
	$Weight_UM = isset($_POST['Weight_UM']) ? fix_string($_POST['Weight_UM']) : "lb";
	$Wingspan = isset($_POST['Wingspan']) ? fix_string($_POST['Wingspan']) : "";
	$Wingspan_UM = isset($_POST['Wingspan_UM']) ? fix_string($_POST['Wingspan_UM']) : "in";
	$Handspan = isset($_POST['Handspan']) ? fix_string($_POST['Handspan']) : "";
	$Handspan_UM = isset($_POST['Handspan_UM']) ? fix_string($_POST['Handspan_UM']) : "in";
	$StandingReach = isset($_POST['StandingReach']) ? fix_string($_POST['StandingReach']) : "";
	$StandingReach_UM = isset($_POST['StandingReachUM']) ? fix_string($_POST['StandingReach_UM']) : "in";
	
	$healthInsuranceCompany = isset ($_POST['healthInsuranceCompany']) ? fix_string($_POST['healthInsuranceCompany']) : "";
	$healthPlanID = isset ($_POST['healthPlanID']) ? fix_string($_POST['healthPlanID']) : "";
	$NoInsurance = isset ($_POST['NoInsurance']) ? "1" : "";
	$allergiesConditions = isset ($_POST['allergiesConditions']) ? fix_string($_POST['allergiesConditions']) : "";
	$allergiesConditionsDescr = isset ($_POST['allergiesConditionsDescr']) ? fix_string($_POST['allergiesConditionsDescr']) : "";
	$medications = isset ($_POST['medications']) ? fix_string($_POST['medications']) : "";
	$medicationsDescr = isset ($_POST['medicationsDescr']) ? fix_string($_POST['medicationsDescr']) : "";
	$TakingBannedSubstance = isset ($_POST['TakingBannedSubstance']) ? fix_string($_POST['TakingBannedSubstance']) : "";
	$BannedSubstanceViaPrescription = isset ($_POST['BannedSubstanceViaPrescription']) ? "1" : "";
	$BannedSubstanceDescription = isset ($_POST['BannedSubstanceDescription']) ? fix_string($_POST['BannedSubstanceDescription']) : "";
	
	$passportHolder = (isset ($_POST['passportHolder']) ? fix_string($_POST['passportHolder']) : "");
	$passportNumber = (isset ($_POST['passportNumber']) ? fix_string($_POST['passportNumber']) : "");
	$nameOnPassport = (isset ($_POST['nameOnPassport']) ? fix_string($_POST['nameOnPassport']) : "");
	$passportExpiration = "";
	$passportExpirationsave = "";
	if (isset($_POST['passportExpiration'])) {
		if (validate_date($_POST['passportExpiration']) || validate_date_filemaker($_POST['passportExpiration'])) {
			$passportExpirationold = new DateTime($_POST['passportExpiration']);
			$passportExpiration = $passportExpirationold->format('m/d/Y');
			$passportExpirationsave = $passportExpirationold->format('Y-m-d');
		} elseif (empty($_POST['passportExpiration'])) {
			
		} else {
			$fail .= "The Passport Expiration Date is in the wrong format. <br />";
			$passportExpirationsave = $_POST['passportExpiration'];
		}
	}
	$VisaDateIssued = "";
	$VisaDateIssued_save = "";
	if (isset($_POST['VisaDateIssued'])) {
		if (validate_date($_POST['VisaDateIssued']) || validate_date_filemaker($_POST['VisaDateIssued'])) {
			$VisaDateIssued_old = new DateTime($_POST['VisaDateIssued']);
			$VisaDateIssued = $VisaDateIssued_old->format('m/d/Y');
			$VisaDateIssued_save = $VisaDateIssued_old->format('Y-m-d');
		} elseif (empty($_POST['VisaDateIssued'])) {
			
		} else {
			$fail .= "The Visa Date Issued value is in the wrong format. <br />";
			$VisaDateIssued_save = $_POST['VisaDateIssued'];
		}
	}
	$passportIssuingCountry = (isset ($_POST['passportIssuingCountry']) ? fix_string($_POST['passportIssuingCountry']) : "");
	$Citizen1 = (isset ($_POST['Citizen1']) ? fix_string($_POST['Citizen1']) : "");
	$Citizen2 = (isset ($_POST['Citizen2']) ? fix_string($_POST['Citizen2']) : "");
	
	$images_passport = Slim::getImages('slim_passport');
	$image_passport = $images_passport[0];
	$name_passport = $image_passport['output']['name'];
	$data_passport = $image_passport['output']['data'];
	
	// store the passport file
	if (!empty($name_passport)) {
		$file_passport = Slim::saveFile($data_passport, $name_passport, '../tmp/');
		if (!empty($file_passport['name'])) {
			$PassportCropPath = "https://hiperforms.com/tmp/" . $file_passport['name'];
		}
	}
	
	$images_other = Slim::getImages('slim_other');
	$image_other = $images_other[0];
	$name_other = $image_other['output']['name'];
	$data_other = $image_other['output']['data'];
	
	// store the other file
	if (!empty($name_other)) {
		$file_other = Slim::saveFile($data_other, $name_other, '../tmp/');
		if (!empty($file_other['name'])) {
			$OtherTravelCropPath = "https://hiperforms.com/tmp/" . $file_other['name'];
		}
	}
	
	$ID_primaryAirport = (isset ($_POST['ID_primaryAirport']) ? fix_string($_POST['ID_primaryAirport']) : "");
	$ID_secondaryAirport = (isset ($_POST['ID_secondaryAirport']) ? fix_string($_POST['ID_secondaryAirport']) : "");
	$travelComments = (isset ($_POST['travelComments']) ? fix_string($_POST['travelComments']) : "");
	$frequentFlyerInfo = (isset ($_POST['frequentFlyerInfo']) ? fix_string($_POST['frequentFlyerInfo']) : "");
	
	$StatePlayingIn = isset ($_POST['StatePlayingIn']) ? $_POST['StatePlayingIn'] : "";
	$CurrentSchoolGradeLevel = isset($_POST['CurrentSchoolGradeLevel']) ? fix_string($_POST['CurrentSchoolGradeLevel']) : 12;
	$ID_School = isset($_POST['ID_School']) ? fix_string($_POST['ID_School']) : "";
	$HighSchoolGraduationYear = isset($_POST['HighSchoolGraduationYear']) ? fix_string($_POST['HighSchoolGraduationYear']) : "";
	$ID_School_College = isset($_POST['ID_School_College']) ? fix_string($_POST['ID_School_College']) : "";
	$graduationCollegeYear = isset($_POST['graduationCollegeYear']) ? fix_string($_POST['graduationCollegeYear']) : "";
	$ACTScore = isset($_POST['ACTScore']) ? fix_string($_POST['ACTScore']) : "";
	$SATScore = isset($_POST['SATScore']) ? fix_string($_POST['SATScore']) : "";
	$GPA = isset($_POST['GPA']) ? fix_string($_POST['GPA']) : "";
	$PotentialCollegeMajor = isset($_POST['PotentialCollegeMajor']) ? fix_string($_POST['PotentialCollegeMajor']) : "";
	$currentlyMilitary = isset($_POST['currentlyMilitary']) ? fix_string($_POST['currentlyMilitary']) : "";
	$militaryBranch = isset($_POST['militaryBranch']) ? fix_string($_POST['militaryBranch']) : "";
	$militaryComponent = isset($_POST['militaryComponent']) ? fix_string($_POST['militaryComponent']) : "";
	
	$waiver = isset($_POST['waiver']) ? "1" : "";
	################################################################################
	
	
	if (!$UpdateSchool) {
		## Addition Fail tests ######################################################
		
		$images_face = Slim::getImages('slim_face');
		$image_face = $images_face[0];
		$name_face = $image_face['output']['name'];
		$data_face = $image_face['output']['data'];
		
		if (empty($Photo64) && empty($name_face)) {
			$fail .= "Your Face Photo is required.";
		}
		
		// store the file
		if (!empty($name_face)) {
			$file_face = Slim::saveFile($data_face, $name_face, '../tmp/');
			if (!empty($file_face['name'])) {
				$FacePhotoCropPath = "https://hiperforms.com/tmp/" . $file_face['name'];
			}
		}
		
		if (!$IsCoach && !$IsManager && !$U18) {
			$fail .= validate_empty_field($emergencyContactFirstName, "Emergency Contact: First Name");
			$fail .= validate_empty_field($emergencyContactLastName, "Emergency Contact: Last Name");
			$fail .= validate_empty_field($emergencyContactNumber, "Emergency Contact: Phone Number");
			$fail .= validate_empty_field($emergencyContactRelationship, "Emergency Contact: Relationship");
		}
		if ($U18 && $IsPlayer) {
			$fail .= validate_empty_field($Guardian1Type, "Parent / Guardian 1: Type");
			$fail .= validate_empty_field($Guardian1FirstName, "Parent / Guardian 1: First Name");
			$fail .= validate_empty_field($Guardian1LastName, "Parent / Guardian 1: Last Name");
			$fail .= validate_empty_field($Guardian1Cell, "Parent / Guardian 1: Phone");
			$fail .= validate_empty_field($Guardian1eMail, "Parent / Guardian 1: E-Mail");
		}
		
		$images_DOB = Slim::getImages('slim_DOB');
		$image_DOB = $images_DOB[0];
		$name_DOB = $image_DOB['output']['name'];
		$data_DOB = $image_DOB['output']['data'];
		
//		if (empty($ProofOfDOB64) && empty($name_DOB) && $IsPlayer) {
//			$fail .= "Your Proof of DOB is required.";
//		}
		
		// store the file
		if (!empty($name_DOB)) {
			$file_DOB = Slim::saveFile($data_DOB, $name_DOB, '../tmp/');
			if (!empty($file_DOB['name'])) {
				$ProofOfDOBCropPath = "https://hiperforms.com/tmp/" . $file_DOB['name'];
			}
		}
		
		$images_school = Slim::getImages('slim_school');
		$image_school = $images_school[0];
		$name_school = $image_school['output']['name'];
		$data_school = $image_school['output']['data'];
		
		if (empty($ProofOfSchool64) && empty($name_school) && $IsPlayer && $U18) {
			$fail .= "Your Proof of School attendance is required. <br />";
		}

		// store the file
		if (!empty($name_school)) {
			$file_school = Slim::saveFile($data_school, $name_school, '../tmp/');
			if (!empty($file_school['name'])) {
				$ProofOfSchoolCropPath = "https://hiperforms.com/tmp/" . $file_school['name'];
			}
		}
		
		// Do numeric validations, and if failed reset the variable so it doesn't fail FileMaker's validation //
		if ($Height_UM == "m") {
			$fail_Meters = validate_heightMeters($heightMeters);
			if (!empty($fail_heightMeters)) {
				$fail .= $fail_heightMeters;
				$heightMeters = "";
			}
		} else {
			$fail_heightFeet = validate_heightFeet($heightFeet);
			if (!empty($fail_heightFeet)) {
				$fail .= $fail_heightFeet;
				$heightFeet = "";
			}
			$fail_heightInches = validate_heightInches($heightInches);
			if (!empty($fail_heightInches)) {
				$fail .= $fail_heightInches;
				$heightInches = "";
			}
		}
		
		$images_insurance = Slim::getImages('slim_insurance');
		$image_insurance = $images_insurance[0];
		$name_insurance = $image_insurance['output']['name'];
		$data_insurance = $image_insurance['output']['data'];
		
		// Don't make Health Insurance mandatory until USA Rugby Insurance requires it
//		if ($NoInsurance != "1" && $IsPlayer) {
//			$fail .= validate_empty_field($healthInsuranceCompany, "Health Insurance Company");
//			$fail .= validate_empty_field($healthPlanID, "Health Plan ID");
//		}
		
		// store the insurance card file
		if (!empty($name_insurance)) {
			$file_insurance = Slim::saveFile($data_insurance, $name_insurance, '../tmp/');
			if (!empty($file_insurance['name'])) {
				$InsuranceCardCropPath = "https://hiperforms.com/tmp/" . $file_insurance['name'];
			}
		}
		
		if ($IsPlayer) {
//			$fail .= validate_allergiesConditions($allergiesConditions);
//			$fail .= validate_allergiesConditionsDescr($allergiesConditionsDescr, $allergiesConditions);
//			$fail .= validate_medications($medications);
//			$fail .= validate_medicationsDescr($medicationsDescr, $medications);
			$fail .= validate_empty_field($TakingBannedSubstance, "Are You Taking Banned Substances (Yes/No)");
			if ($TakingBannedSubstance == 'Yes') {
				$fail .= validate_empty_field($BannedSubstanceDescription, "Banned Substance Description");
			}
		}
		
		if ($U19) {
			$fail .= validate_empty_field($CurrentSchoolGradeLevel, "Grade Level");
		}
		
		$fail .= validate_waiver($waiver);
		
		#############################################################################
		
		## Write Data to Database ############################
		$edit = $fm->newEditCommand('Member-Tab2-Profile', $record_Tab2Profile->getRecordId());
		$edit->setField('Birthplace_City', $Birthplace_City);
		$edit->setField('Birthplace_State', $Birthplace_State);
		$edit->setField('Birthplace_Country', $Birthplace_Country);
		$edit->setField('Bio', $Bio);
		
		if (!$U18) {
			$edit->setField('emergencyContactFirstName', $emergencyContactFirstName);
			$edit->setField('emergencyContactLastName', $emergencyContactLastName);
			$edit->setField('emergencyContactNumber', $emergencyContactNumber);
			$edit->setField('emergencyContactRelationship', $emergencyContactRelationship);
		}
		if ($U18 && $IsPlayer) {
			$edit->setField('emergencyContactFirstName', $Guardian1FirstName);
			$edit->setField('emergencyContactLastName', $Guardian1LastName);
			$edit->setField('emergencyContactNumber', $Guardian1Cell);
			$edit->setField('emergencyContactRelationship', $Guardian1Type);
			$edit->setField('Guardian1Type', $Guardian1Type);
			$edit->setField('Guardian1FirstName', $Guardian1FirstName);
			$edit->setField('Guardian1LastName', $Guardian1LastName);
			$edit->setField('Guardian1Cell', $Guardian1Cell);
			$edit->setField('Guardian1eMail', $Guardian1eMail);
			$edit->setField('Guardian2Type', $Guardian2Type);
			$edit->setField('Guardian2FirstName', $Guardian2FirstName);
			$edit->setField('Guardian2LastName', $Guardian2LastName);
			$edit->setField('Guardian2Cell', $Guardian2Cell);
			$edit->setField('Guardian2eMail', $Guardian2eMail);
			$edit->setField('Guardian3Type', $Guardian3Type);
			$edit->setField('Guardian3FirstName', $Guardian3FirstName);
			$edit->setField('Guardian3LastName', $Guardian3LastName);
			$edit->setField('Guardian3Cell', $Guardian3Cell);
			$edit->setField('Guardian3eMail', $Guardian3eMail);
			$edit->setField('Guardian4Type', $Guardian4Type);
			$edit->setField('Guardian4FirstName', $Guardian4FirstName);
			$edit->setField('Guardian4LastName', $Guardian4LastName);
			$edit->setField('Guardian4Cell', $Guardian4Cell);
			$edit->setField('Guardian4eMail', $Guardian4eMail);
			$edit->setField('referenceType1', $referenceType1);
			$edit->setField('referenceFirstName1', $referenceFirstName1);
			$edit->setField('referenceLastName1', $referenceLastName1);
			$edit->setField('referencePhone1', $referencePhone1);
			$edit->setField('referenceEmail1', $referenceEmail1);
			$edit->setField('referenceType2', $referenceType2);
			$edit->setField('referenceFirstName2', $referenceFirstName2);
			$edit->setField('referenceLastName2', $referenceLastName2);
			$edit->setField('referencePhone2', $referencePhone2);
			$edit->setField('referenceEmail2', $referenceEmail2);
			$edit->setField('referenceType3', $referenceType3);
			$edit->setField('referenceFirstName3', $referenceFirstName3);
			$edit->setField('referenceLastName3', $referenceLastName3);
			$edit->setField('referencePhone3', $referencePhone3);
			$edit->setField('referenceEmail3', $referenceEmail3);
		} else {
			$edit->setField('spouseName', $spouseName);
			$edit->setField('spouseEmail', $spouseEmail);
			$edit->setField('spouseCell', $spouseCell);
		}

//		$edit->setField('MembershipID', $MembershipID);
		if ($IsPlayer) {
			$edit->setField('yearStartedPlaying', $yearStartedPlaying);
			$edit->setField('dominantHand', $dominantHand);
			$edit->setField('dominantFoot', $dominantFoot);
			$edit->setField('primary15sPosition', $primary15sPosition);
			$edit->setField('secondary15sPosition', $secondary15sPosition);
			$edit->setField('primary7sPosition', $primary7sPosition);
			$edit->setField('secondary7sPosition', $secondary7sPosition);
			$edit->setField('HighlightVideoLink', $HighlightVideoLink);
			$edit->setField('FullMatchLink1', $FullMatchLink1);
			$edit->setField('FullMatchLink2', $FullMatchLink2);
			$edit->setField('FullMatchLink3', $FullMatchLink3);
		}
		$edit->setField('MatchJerseySize', $MatchJerseySize);
		$edit->setField('MatchShortsSize', $MatchShortsSize);
		$edit->setField('tShirtSize', $tShirtSize);
		$edit->setField('poloSize', $poloSize);
		$edit->setField('shortsSize', $shortsSize);
		$edit->setField('trackSuitBottomSize', $trackSuitBottomSize);
		$edit->setField('trackSuitTopSize', $trackSuitTopSize);
		$edit->setField('shoeSize', $shoeSize);
		
		## Copy new image URL to record, and run script to update picture   ###
		## Only if an image was uploaded ######################################
		if (!empty($FacePhotoCropPath)) {
			$edit->setField('photoURL', $FacePhotoCropPath);
		}
		if (!empty($ProofOfSchoolCropPath)) {
			$edit->setField('ProofOfSchoolURL', $ProofOfSchoolCropPath);
		}
		if (!empty($ProofOfDOBCropPath)) {
			$edit->setField('ProofOfDOBURL', $ProofOfDOBCropPath);
		}
		if (!empty($InsuranceCardCropPath)) {
			$edit->setField('InsuranceCardURL', $InsuranceCardCropPath);
		}
		if (!empty($PassportCropPath)) {
			$edit->setField('PassportURL', $PassportCropPath);
		}
		if (!empty($OtherTravelCropPath)) {
			$edit->setField('OtherTravelURL', $OtherTravelCropPath);
		}
		##########################################################################
		
		$edit->setField('NoInsurance', $NoInsurance);
		$edit->setField('healthInsuranceCompany', $healthInsuranceCompany);
		$edit->setField('healthPlanID', $healthPlanID);
		if ($IsPlayer) {
			$edit->setField('allergiesConditions', $allergiesConditions);
			$edit->setField('allergiesConditionsDescr', $allergiesConditionsDescr);
			$edit->setField('medications', $medications);
			$edit->setField('medicationsDescr', $medicationsDescr);
			$edit->setField('TakingBannedSubstance', $TakingBannedSubstance);
			$edit->setField('BannedSubstanceViaPrescription', $BannedSubstanceViaPrescription);
			$edit->setField('BannedSubstanceDescription', $BannedSubstanceDescription);
		}
		
		$edit->setField('passportHolder', $passportHolder);
		$edit->setField('passportNumber', $passportNumber);
		$edit->setField('nameOnPassport', $nameOnPassport);
		$edit->setField('passportExpiration', $passportExpiration);
		$edit->setField('VisaDateIssued', $VisaDateIssued);
		$edit->setField('passportIssuingCountry', $passportIssuingCountry);
		$edit->setField('Citizen1', $Citizen1);
		$edit->setField('Citizen2', $Citizen2);
		$edit->setField('ID_primaryAirport', $ID_primaryAirport);
		$edit->setField('ID_secondaryAirport', $ID_secondaryAirport);
		$edit->setField('travelComments', $travelComments);
		$edit->setField('frequentFlyerInfo', $frequentFlyerInfo);
		
		if ($IsPlayer) {
			$edit->setField('StatePlayingIn', $StatePlayingIn);
			$edit->setField('CurrentSchoolGradeLevel', $CurrentSchoolGradeLevel);
			if (!empty($ID_School)) {
				$edit->setField('ID_School_1_12', $ID_School);
			}
			if (!empty($HighSchoolGraduationYear)) {
				$edit->setField('HighSchoolGraduationYear', $HighSchoolGraduationYear);
			}
		}
		if ($IsPlayer && $U19) {
			$edit->setField('ACTScore', $ACTScore);
			$edit->setField('SATScore', $SATScore);
			$edit->setField('GPA', $GPA);
			$edit->setField('PotentialCollegeMajor', $PotentialCollegeMajor);
		}
		if (!$U18) {
			$edit->setField('ID_School_College', $ID_School_College);
			$edit->setField('graduationCollegeYear', $graduationCollegeYear);
			$edit->setField('currentlyMilitary', $currentlyMilitary);
			$edit->setField('militaryBranch', $militaryBranch);
			$edit->setField('militaryComponent', $militaryComponent);
		}
		
		if (empty($fail)) {
			$edit->setField('z_SuccessfulProfileUpdatedDate', $today);
		}
		$edit->setField('z_ModifiedByID', $eMail);
		$edit->setField('z_ModifiedByName', 'web');
		
		// Commit Personnel Record:
		$result = $edit->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 216: " . $result->getMessage() . "</p>";
			die();
		}
		
		if (!empty($FacePhotoCropPath) || !empty($ProofOfSchoolCropPath) || !empty($ProofOfDOBCropPath) || !empty($InsuranceCardCropPath) || !empty($PassportCropPath) || !empty($OtherTravelCropPath)) {
			// Run script to put image URLs into their container fields //
			$newPerformScript = $fm->newPerformScriptCommand('Member-Tab2-Profile', 'Personnel Images URL to container', $ID_Personnel);
			$scriptResult = $newPerformScript->execute();
			if (FileMaker::isError($scriptResult)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 217: " . $scriptResult->getMessage() . "</p>";
				die();
			}
		}
		cleanPictures();
		
		// Audit:
		$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
		$z_storeResults = $z_storeRequest->execute();
		$z_storeResult = $z_storeResults->getFirstRecord();
		$z_storeRecordID = $z_storeResult->getRecordId();
		$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
		$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
		$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'Personnel::RecordID|' . $recordID . ';');
		$resultAudit = $editAudit->execute();
		
		//## Create related records ##//
		
		// Measurements //
		if (!empty($heightFeet) || !empty($heightInches) || !empty($heightMeters) || !empty($Weight) || !empty($Wingspan) || !empty($Handspan) || !empty($StandingReach)) {
			$heightFeet = $Height_UM != "m" ? $heightFeet : intval($heightMeters * 3.28084);
			$heightInches = $Height_UM != "m" ? $heightInches : round(($heightMeters * 3.28084 * 12) % 12, 1);
			$heightMeters = $Height_UM == "m" ? $heightMeters : round(($heightFeet + ($heightInches / 12)) * .3048, 2);
			$measurement_data = array(
				'ID_Personnel' => $ID_Personnel,
				'heightFeet' => $heightFeet,
				'heightInches' => $heightInches,
				'heightMeters' => $heightMeters,
				$Weight_UM == "kg" ? 'Weight_kg' : 'Weight_lb' => $Weight,
				$Wingspan_UM == "m" ? 'Wingspan_m' : 'Wingspan_in' => $Wingspan,
				$Handspan_UM == "cm" ? 'Handspan_cm' : 'Handspan_in' => $Handspan,
				$StandingReach_UM == "m" ? 'StandingReach_m' : 'StandingReach_in' => $StandingReach,
				'z_ModifiedByID' => $eMail,
				'z_ModifiedByName' => 'web',
			);
			$newMeasurementRequest =& $fm->newAddCommand('Personnel__Measurements', $measurement_data);
			$result = $newMeasurementRequest->execute();
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem adding the new measurements record. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 218: " . $result->getMessage() . "</p>";
				exit;
			}
			$Measurement_NewRecord = $result->getFirstRecord();
			$Measurement_RecordID = $Measurement_NewRecord->getRecordId();
			// Audit:
			$z_storeRequest = $fm->newFindCommand('ScheduleAudit');
			$z_storeResults = $z_storeRequest->execute();
			$z_storeResult = $z_storeResults->getFirstRecord();
			$z_storeRecordID = $z_storeResult->getRecordId();
			$z_ScheduledRecordsToAudit = $z_storeResult->getField('z_ScheduledRecordsToAudit');
			$editAudit = $fm->NewEditCommand('ScheduleAudit', $z_storeRecordID);
			$editAudit->setField('z_ScheduledRecordsToAudit', $z_ScheduledRecordsToAudit . 'Measurements::RecordID|' . $Measurement_RecordID . ';');
			$resultAudit = $editAudit->execute();
			unset($heightFeet);
			unset($heightInches);
			unset($heightMeters);
			unset($Weight);
			unset($Wingspan);
			unset($Handspan);
			unset($StandingReach);
		}
		
		// Refresh data //
		include 'GetFMData/Tab2-GetData.php';
		#############################################################################
		
		if (!empty($fail)) {
			echo '
				<style type="text/css">
				.missing {
					border: 2px solid red
				}
				</style>';
			if (!$RegistrationActivate) {
				//Refresh record so that field variables have updated values
				include 'GetFMData/Registration1.php';
			}
		} else {
			// Need something here for after the Profile tab is sucessfully applied:
			$message_profile = "Thank You. Your profile has been successfully updated.";
		}
		
	}
} elseif ($activeTab == 2) {
	if (!$RegistrationActivate) {
		include 'GetFMData/Registration1.php';
		include 'GetFMData/Registration2.php';
		
		$firstName = $record_Registration1->getField('firstName');
		$middleName = $record_Registration1->getField('middleName');
		$lastName = $record_Registration1->getField('lastName');
		$nickName = $record_Registration1->getField('nickName');
		$DOB_original = $record_Registration1->getField('DOB');
		$DOB_original_test = explode('/', $DOB_original);
		if (count($DOB_original_test) == 3) {
			if (checkdate($DOB_original_test[0], $DOB_original_test[1], $DOB_original_test[2]) == TRUE) {
				$DOB = new DateTime($DOB_original);
				$DOBsave = $DOB->format('Y-m-d');
			}
		} else {
			$DOB = "";
		}
		$gender = $record_Registration1->getField('gender');
		$ethnicity = $record_Registration1->getField('RaceEthnicity');
		$homeAddress1 = $record_Registration1->getField('homeAddress1');
		$homeAddress2 = $record_Registration1->getField('homeAddress2');
		$City = $record_Registration1->getField('City');
		$State = $record_Registration1->getField('State');
		$zipCode = $record_Registration1->getField('zipCode');
		$Country = $record_Registration1->getField('Country');
		$Citizen1 = $record_Registration1->getField('Citizen1');
		$Citizen2 = $record_Registration1->getField('Citizen2');
		$MatchJerseySize = $record_Registration1->getField('MatchJerseySize');
		$MatchShortsSize = $record_Registration1->getField('MatchShortsSize');
		$PrimaryPhoneNumber = $record_Registration1->getField('PrimaryPhoneNumber');
		$PrimaryPhoneText_flag = $record_Registration1->getField('PrimaryPhoneText_flag');
	}
	include 'GetFMData/Tab2-GetData.php';
	$Height_UM = "";
	$Weight_UM = "";
	$Wingspan_UM = "";
	$Handspan_UM = "";
	$StandingReach_UM = "";
	$waiver = "";
	
	## Get data from HiPer to display #############################
	$Birthplace_City = $record_Tab2Profile->getField('Birthplace_City');
	$Birthplace_State = $record_Tab2Profile->getField('Birthplace_State');
	$Birthplace_Country = $record_Tab2Profile->getField('Birthplace_Country');
	$Bio = $record_Tab2Profile->getField('Bio');
	
	$emergencyContactFirstName = $record_Tab2Profile->getField('emergencyContactFirstName');
	$emergencyContactLastName = $record_Tab2Profile->getField('emergencyContactLastName');
	$emergencyContactNumber = $record_Tab2Profile->getField('emergencyContactNumber');
	$emergencyContactRelationship = $record_Tab2Profile->getField('emergencyContactRelationship');
	if ($U18 && $IsPlayer) {
		$Guardian1Type = $record_Tab2Profile->getField('Guardian1Type');
		$Guardian1FirstName = $record_Tab2Profile->getField('Guardian1FirstName');
		$Guardian1LastName = $record_Tab2Profile->getField('Guardian1LastName');
		$Guardian1Cell = $record_Tab2Profile->getField('Guardian1Cell');
		$Guardian1eMail = $record_Tab2Profile->getField('Guardian1eMail');
		$Guardian2Type = $record_Tab2Profile->getField('Guardian2Type');
		$Guardian2FirstName = $record_Tab2Profile->getField('Guardian2FirstName');
		$Guardian2LastName = $record_Tab2Profile->getField('Guardian2LastName');
		$Guardian2Cell = $record_Tab2Profile->getField('Guardian2Cell');
		$Guardian2eMail = $record_Tab2Profile->getField('Guardian2eMail');
		$Guardian3Type = $record_Tab2Profile->getField('Guardian3Type');
		$Guardian3FirstName = $record_Tab2Profile->getField('Guardian3FirstName');
		$Guardian3LastName = $record_Tab2Profile->getField('Guardian3LastName');
		$Guardian3Cell = $record_Tab2Profile->getField('Guardian3Cell');
		$Guardian3eMail = $record_Tab2Profile->getField('Guardian3eMail');
		$Guardian4Type = $record_Tab2Profile->getField('Guardian4Type');
		$Guardian4FirstName = $record_Tab2Profile->getField('Guardian4FirstName');
		$Guardian4LastName = $record_Tab2Profile->getField('Guardian4LastName');
		$Guardian4Cell = $record_Tab2Profile->getField('Guardian4Cell');
		$Guardian4eMail = $record_Tab2Profile->getField('Guardian4eMail');
		$referenceType1 = $record_Tab2Profile->getField('referenceType1');
		$referenceFirstName1 = $record_Tab2Profile->getField('referenceFirstName1');
		$referenceLastName1 = $record_Tab2Profile->getField('referenceLastName1');
		$referencePhone1 = $record_Tab2Profile->getField('referencePhone1');
		$referenceEmail1 = $record_Tab2Profile->getField('referenceEmail1');
		$referenceType2 = $record_Tab2Profile->getField('referenceType2');
		$referenceFirstName2 = $record_Tab2Profile->getField('referenceFirstName2');
		$referenceLastName2 = $record_Tab2Profile->getField('referenceLastName2');
		$referencePhone2 = $record_Tab2Profile->getField('referencePhone2');
		$referenceEmail2 = $record_Tab2Profile->getField('referenceEmail2');
		$referenceType3 = $record_Tab2Profile->getField('referenceType3');
		$referenceFirstName3 = $record_Tab2Profile->getField('referenceFirstName3');
		$referenceLastName3 = $record_Tab2Profile->getField('referenceLastName3');
		$referencePhone3 = $record_Tab2Profile->getField('referencePhone3');
		$referenceEmail3 = $record_Tab2Profile->getField('referenceEmail3');
	} else {
		$spouseName = $record_Tab2Profile->getField('spouseName');
		$spouseEmail = $record_Tab2Profile->getField('spouseEmail');
		$spouseCell = $record_Tab2Profile->getField('spouseCell');
	}
	
	$MembershipID = $record_Tab2Profile->getField('MembershipID');
	if ($IsPlayer) {
		$yearStartedPlaying = $record_Tab2Profile->getField('yearStartedPlaying');
		$dominantHand = $record_Tab2Profile->getField('dominantHand');
		$dominantFoot = $record_Tab2Profile->getField('dominantFoot');
		$primary15sPosition = $record_Tab2Profile->getField('primary15sPosition');
		$secondary15sPosition = $record_Tab2Profile->getField('secondary15sPosition');
		$primary7sPosition = $record_Tab2Profile->getField('primary7sPosition');
		$secondary7sPosition = $record_Tab2Profile->getField('secondary7sPosition');
		$HighlightVideoLink = $record_Tab2Profile->getField('HighlightVideoLink');
		$FullMatchLink1 = $record_Tab2Profile->getField('FullMatchLink1');
		$FullMatchLink2 = $record_Tab2Profile->getField('FullMatchLink2');
		$FullMatchLink3 = $record_Tab2Profile->getField('FullMatchLink3');
	}
	$MatchJerseySize = $record_Tab2Profile->getField('MatchJerseySize');
	$MatchShortsSize = $record_Tab2Profile->getField('MatchShortsSize');
	$tShirtSize = $record_Tab2Profile->getField('tShirtSize');
	$poloSize = $record_Tab2Profile->getField('poloSize');
	$shortsSize = $record_Tab2Profile->getField('shortsSize');
	$trackSuitBottomSize = $record_Tab2Profile->getField('trackSuitBottomSize');
	$trackSuitTopSize = $record_Tab2Profile->getField('trackSuitTopSize');
	$shoeSize = $record_Tab2Profile->getField('shoeSize');
	
	$NoInsurance = $record_Tab2Profile->getField('NoInsurance');
	$healthInsuranceCompany = $record_Tab2Profile->getField('healthInsuranceCompany');
	$healthPlanID = $record_Tab2Profile->getField('healthPlanID');
	if (empty($healthInsuranceCompany) && empty($healthPlanID) && empty($NoInsurance)) {
		$NoInsurance = 1;
	}
	if ($IsPlayer) {
		$allergiesConditions = $record_Tab2Profile->getField('allergiesConditions');
		$allergiesConditionsDescr = $record_Tab2Profile->getField('allergiesConditionsDescr');
		$medications = $record_Tab2Profile->getField('medications');
		$medicationsDescr = $record_Tab2Profile->getField('medicationsDescr');
		$TakingBannedSubstance = $record_Tab2Profile->getField('TakingBannedSubstance');
		$BannedSubstanceViaPrescription = $record_Tab2Profile->getField('BannedSubstanceViaPrescription');
		$BannedSubstanceDescription = $record_Tab2Profile->getField('BannedSubstanceDescription');
	}
	
	$passportHolder = $record_Tab2Profile->getField('passportHolder');
	$passportNumber = $record_Tab2Profile->getField('passportNumber');
	$nameOnPassport = $record_Tab2Profile->getField('nameOnPassport');
	$passportExpiration_original = $record_Tab2Profile->getField('passportExpiration');
	if (!empty($passportExpiration_original)) {
		$passportExpiration = new DateTime($passportExpiration_original);
		$passportExpirationsave = $passportExpiration->format('Y-m-d');
	} else {
		$passportExpirationsave = "";
	}
	$VisaDateIssued_original = $record_Tab2Profile->getField('VisaDateIssued');
	if (!empty($VisaDateIssued_original)) {
		$VisaDateIssued = new DateTime($VisaDateIssued_original);
		$VisaDateIssued_save = $VisaDateIssued->format('Y-m-d');
	} else {
		$VisaDateIssued_save = "";
	}
	$passportIssuingCountry = $record_Tab2Profile->getField('passportIssuingCountry');
	$Citizen1 = $record_Tab2Profile->getField('Citizen1');
	$Citizen2 = $record_Tab2Profile->getField('Citizen2');
	$ID_primaryAirport = $record_Tab2Profile->getField('ID_primaryAirport');
	$ID_secondaryAirport = $record_Tab2Profile->getField('ID_secondaryAirport');
	$frequentFlyerInfo = $record_Tab2Profile->getField('frequentFlyerInfo');
	$travelComments = $record_Tab2Profile->getField('travelComments');
	
	if ($IsPlayer) {
		$StatePlayingIn = $record_Tab2Profile->getField('StatePlayingIn');
		$CurrentSchoolGradeLevel = ($U19 ? $record_Tab2Profile->getField('CurrentSchoolGradeLevel') : 12);
		$ID_School = $record_Tab2Profile->getField('ID_School_1_12');
		$HighSchoolGraduationYear = $record_Tab2Profile->getField('HighSchoolGraduationYear');
	}
	if ($IsPlayer && $U19) {
		$ACTScore = $record_Tab2Profile->getField('ACTScore');
		$SATScore = $record_Tab2Profile->getField('SATScore');
		$GPA = $record_Tab2Profile->getField('GPA');
		$PotentialCollegeMajor = $record_Tab2Profile->getField('PotentialCollegeMajor');
	}
	if (!$U18) {
		$ID_School_College = $record_Tab2Profile->getField('ID_School_College');
		$graduationCollegeYear = $record_Tab2Profile->getField('graduationCollegeYear');
		$currentlyMilitary = $record_Tab2Profile->getField('currentlyMilitary');
		$militaryBranch = $record_Tab2Profile->getField('militaryBranch');
		$militaryComponent = $record_Tab2Profile->getField('militaryComponent');
	}
}

//
// Tab 3: History
if ($activeTab == 3) {
	include 'GetFMData/Tab3-GetData.php';
}

//
// Tab 4: Camp Enrollment form submitted
$EnrollCamp = (isset($_POST['submitted-camp']) ? True : False);
if (isset($_GET['VenueSearch'])) {
	$VenueSearch = $_GET['VenueSearch'] == 1 ? true : false;
} else {
	$VenueSearch = "";
}

if ($EnrollCamp && !$VenueSearch) {
	include 'GetFMData/Tab4-GetData.php';
	$activeTab = 4;
	$InterestedInRegistration = isset($_POST['InterestedInRegistration']) ? $_POST['InterestedInRegistration'] : "";
	$InterestedInRegistrationRole = isset($_POST['InterestedInRegistrationRole']) ? $_POST['InterestedInRegistrationRole'] : "";
	$InterestedInRegistration_string = "";
	$InterestedInRegistrationRole_string = "";
	
	foreach ($InterestedInRegistration as $key => $value) {
		$InterestedInRegistration_string .= $value . "|";
	}
	foreach ($InterestedInRegistrationRole as $key => $value) {
		if (!empty($value)) {
			$InterestedInRegistrationRole_string .= $value . "|";
		}
	}
	
	$InterestedInRegistration_string = rtrim($InterestedInRegistration_string, "|");
	$InterestedInRegistrationRole_string = rtrim($InterestedInRegistrationRole_string, "|");
	$RegistrationScript_param = $ID_Personnel . "\n" . $InterestedInRegistration_string . "\n" . 0 . "\n" . $InterestedInRegistrationRole_string;
	$newPerformScript = $fm->newPerformScriptCommand('Member-Tab4-Enrollment', 'Camp Registration', $RegistrationScript_param);
	$scriptResult = $newPerformScript->execute();
	if (FileMaker::isError($scriptResult)) {
		echo "<p>Error: There was a problem processing your camp registration. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
			. "<p>Error Code 219: " . $scriptResult->getMessage() . "</p>";
		die();
	}
//	else {
//		echo $RegistrationScript_param;
//	}
	
	//Refresh record so that script results update
	include 'GetFMData/Tab4-GetData.php';
	$message_enrollment = "Thank You. Your Camp Enrollment selection(s) have been updated.";
} elseif ($activeTab == 4) {
	include 'GetFMData/Tab4-GetData.php';
	
	if ($VenueSearch && !empty($_POST['ID_Venue'])) {
		$ID_Venue = $_POST['ID_Venue'];
		$related_campRegistrations_filtered = array();
		$i = 0;
		foreach ($related_campRegistrations as $camp) {
			if ($camp->getField('Camp.OpenRegistration::ID_Venue') == $ID_Venue) {
				$related_campRegistrations_filtered[$i] = $camp;
				$i++;
			}
			$related_campRegistrations = $related_campRegistrations_filtered;
		}
	}
}
//---- / Camp Enrollment form submitted

//
// Tab 5: Manage form
$ClubAccess = (isset($_POST['submitted-ClubAccess']) ? True : False);
$CampAccess = (isset($_POST['submitted-CampAccess']) ? True : False);

if ($ClubAccess) {
	include 'GetFMData/Tab5-GetData.php';
	$activeTab = 5;
	$ClubAccess_ID = isset($_POST['ClubAccess_ID']) ? $_POST['ClubAccess_ID'] : "";
	if (!empty($ClubAccess_ID)) {
		$_SESSION['ClubAccess_ID'] = $ClubAccess_ID;
		$_SESSION['ID_Personnel'] = $ID_Personnel;
		$_SESSION['eMail'] = $eMail; // needed for z_modifiedBy
		$_SESSION['PreferredName'] = $PreferredName;
		header("location: ManageClub.php");
	}
} elseif ($activeTab == 5) {
	include 'GetFMData/Tab5-GetData.php';
}

if ($CampAccess) {
	include 'GetFMData/Tab5-GetData.php';
	$activeTab = 5;
	$CampAccess_ID = isset($_POST['CampAccess_ID']) ? fix_string($_POST['CampAccess_ID']) : "";
	if (!empty($CampAccess_ID)) {
		$_SESSION['CampAccess_ID'] = $CampAccess_ID;
		$_SESSION['ID_Personnel'] = $ID_Personnel;
		$_SESSION['eMail'] = $eMail; // needed for z_modifiedBy
		header("location: ManageCamp.php");
	}
} elseif ($activeTab == 5) {
	include 'GetFMData/Tab5-GetData.php';
}

//---- / Manage form

//
// Tab 7: Account form submitted
$ChangeAccount = (isset($_POST['change_password']) ? $_POST['change_password'] : False);
$ChangeAccountSuccess = false;

if ($ChangeAccount) {
	$activeTab = 7;
	if (isset($_POST['eMail'])) {
		$eMail = fix_string($_POST['eMail']);
		$fail .= validate_eMail($eMail);
	} else {
		$eMail = "";
	}
	if (!empty($_POST['currentPassword']) && !empty($_POST['newPassword1']) && !empty($_POST['newPassword2'])) {
		$pwdMD5 = strtoupper(md5($_POST['currentPassword']));
		$Password_MD5 = $record_Header->getField('Password_MD5');
		if ($pwdMD5 != $Password_MD5) {
			$fail .= "Password could not be changed: The Current Password entered does not match your existing password. <br />";
		} elseif ($_POST['newPassword1'] != $_POST['newPassword2']) {
			$fail .= "Password could not be changed: 'Re-enter New Password' does not match 'New Password'. <br />";
		} elseif (strlen($_POST['newPassword1']) < 6) {
			$fail .= "Password could not be changed: Password length must be at least 6 characters long. <br />";
		} else {
			$newPassword = strtoupper(md5($_POST['newPassword1']));
		}
	}
	
	if (empty($fail)) {
		$edit = $fm->newEditCommand('Member-Header', $record_Header->getRecordId());
		$edit->setField('eMail', $eMail);
		if (isset($pwdMD5)) {
			$edit->setField('Password_MD5', $newPassword);
		}
		
		$result = $edit->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 220: " . $result->getMessage() . "</p>";
			die();
		} else {
			$ChangeAccountSuccess = True;
		}
	}
	
}
//---- / Account form submitted

/*
 * The following can only be done once everything else is loaded
 */

// Club Membership //
## Get Active ClubMembership count #############################
$compoundMembershipRequest =& $fm->newCompoundFindCommand('Personnel__ClubMembership');
$clubMembershipRequest =& $fm->newFindRequest('Personnel__ClubMembership');
$clubMembershipRequest2 =& $fm->newFindRequest('Personnel__ClubMembership');
$clubMembershipRequest->addFindCriterion('ID_Personnel', '==' . $ID_Personnel);
$clubMembershipRequest2->addFindCriterion('Inactive_flag', 1);
$clubMembershipRequest2->setOmit(true);
$compoundMembershipRequest->add(1, $clubMembershipRequest);
$compoundMembershipRequest->add(2, $clubMembershipRequest2);
$ActiveClubMembershipResult = $compoundMembershipRequest->execute();
if (FileMaker::isError($ActiveClubMembershipResult)) {
	$ActiveClubMembershipCount = 0;
} else {
	$ActiveClubMembershipCount = $ActiveClubMembershipResult->getFoundSetCount();
	$ActiveMembership_records = $ActiveClubMembershipResult->getRecords();
}

if (!$RegistrationActivate) {
	$LastModifiedClubMembership = date_create($record_Header->getField('Personnel__ClubMembership.sortedByModifiedDate::z_modifiedTimeStamp'));
	$now = date_create($today);
	$MonthsSinceModifiedClubMembership = intval(date_diff($LastModifiedClubMembership, $now)->m);
	
	if ($ActiveClubMembershipCount == 0) {
		$ClubmembershipStatus = "black";
	} elseif ($MonthsSinceModifiedClubMembership === "") {
		$ClubmembershipStatus = "black";
	} elseif ($MonthsSinceModifiedClubMembership < 9) {
		$ClubmembershipStatus = "green";
	} elseif ($MonthsSinceModifiedClubMembership > 8 && $MonthsSinceModifiedClubMembership < 13) {
		$ClubmembershipStatus = "orange";
	} elseif ($MonthsSinceModifiedClubMembership > 12) {
		$ClubmembershipStatus = "red";
	} else {
		$ClubmembershipStatus = "black";
	}
}

//Refresh value upon profile submission:
if (isset($_POST['submitted-profile'])) {
	$result_Header = $request_Header->execute();
	if (FileMaker::isError($result_Header)) {
		echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
			. "<p>Error Code 100: " . $result->getMessage() . "</p>";
		die();
	}
	$records_Header = $result_Header->getRecords();
	$record_Header = $result_Header->getFirstRecord();
}

$DaysSinceSuccessfulProfileVerification = $record_Header->getField('z_DaysSinceSuccessfulProfileUpdate');
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
