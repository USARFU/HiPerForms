<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>USA Rugby HiPer Database - Manage Club</title>

	<!-- Error Codes 341-34x -->
	
	<?php
	include "header.php";
	
	$layout =& $fm->getLayout('Club');
	$fail = "";
	
	## Exit to login screen if ID received from session is invalid #################
	## or if the session has timed out. ############################################
	session_start();
	if (empty($_SESSION['RecordID']) || $_SESSION['timeout'] + 60 * 60 * 4 < time()) {
		header("location: login.php");
		unset($_SESSION['id']);
		die();
	} else {
		$recordID = $_SESSION['RecordID'];
	}
	$_SESSION['timeout'] = time();
	################################################################################
	
	$ID_Club = $_SESSION['ClubAccess_ID'];
	$ID_Personnel = $_SESSION['ID_Personnel'];
	$PreferredName = $_SESSION['PreferredName'];
	
	## Grab Club's record ##########################################################
	$request = $fm->newFindCommand('Club');
	$request->addFindCriterion('ID', '==' . $ID_Club);
	$result = $request->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
			. "<p>Error Code 340: " . $result->getMessage() . "</p>";
		die();
	}
	$records = $result->getRecords();
	$record = $result->getFirstRecord();
	################################################################################
	
	## Security ####################################################################
	$EditClubArray = array($record->getField('ID_HeadCoach') => 1);
	$EditMembersArray = array($record->getField('ID_HeadCoach') => 1);
	
	$related_WebAccessPersonnel = $record->getRelatedSet('Club__WebAccessPersonnel');
	if (FileMaker::isError($related_WebAccessPersonnel)) {
		$related_WebAccessPersonnel_count = 0;
	} else {
		$related_WebAccessPersonnel_count = count($related_WebAccessPersonnel);
	}
	
	if ($related_WebAccessPersonnel_count > 0) {
		foreach ($related_WebAccessPersonnel as $item) {
			$EditClubArray[$item->getField('Club__WebAccessPersonnel::ID_Personnel')] = $item->getField('Club__WebAccessPersonnel::EditClub');
			$EditMembersArray[$item->getField('Club__WebAccessPersonnel::ID_Personnel')] = $item->getField('Club__WebAccessPersonnel::EditMembers');
		}
	}
	
	// Die if $ID_Personnel not found in any array
	$ViewClub = array_key_exists($ID_Personnel, $EditClubArray);
	if (!$ViewClub) {
		echo "You are not allowed to view this club.<br />";
		die();
	}
	
	$EditClub = array_filter($EditClubArray);
	$EditClub = array_key_exists($ID_Personnel, $EditClub);
	$EditMembers = array_filter($EditMembersArray);
	$EditMembers = array_key_exists($ID_Personnel, $EditMembers);
	# /Security #####################################################################
	
	
	$Nominate_player = isset($_POST['Nominate_player']) ? $_POST['Nominate_player'] : "";
	$Nominate_type = isset($_POST['Nominate_type']) ? $_POST['Nominate_type'] : "";
	$Nominate = (!empty($Nominate_player) && !empty($Nominate_type)) ? true : false;
	
	//
	// Grab Club Form Data
	if (isset($_POST['edit-club-submitted'])) {
		
		## Get submitted data ############################
		$images_logo = Slim::getImages('slim_logo');
		$image_logo = $images_logo[0];
		$name_logo = $image_logo['output']['name'];
		$data_logo = $image_logo['output']['data'];
		
		// store the logo file
		if (!empty($name_logo)) {
			$file_logo = Slim::saveFile($data_logo, $name_logo, '../tmp/');
			if (!empty($file_logo['name'])) {
				$LogoCropPath = "https://hiperforms.com/tmp/" . $file_logo['name'];
			}
		}
		
		$Level = isset($_POST['Level']) ? fix_string($_POST['Level']) : "";
		$TeamStatus = isset($_POST['TeamStatus']) ? fix_string($_POST['TeamStatus']) : "";
		$AgeGroup = isset($_POST['AgeGroup']) ? fix_string($_POST['AgeGroup']) : "";
		$Gender = isset($_POST['Gender']) ? fix_string($_POST['Gender']) : "";
		// Replace \n\r with just \n for FileMaker
		$Notes = isset($_POST['Notes']) ? fix_string(str_replace("\r", "", $_POST['Notes'])) : "";
		
		$Website = isset($_POST['Website']) ? fix_string($_POST['Website']) : "";
		$TwitterHandle = isset($_POST['TwitterHandle']) ? fix_string($_POST['TwitterHandle']) : "";
		$FacebookURL = isset($_POST['FacebookURL']) ? fix_string($_POST['FacebookURL']) : "";
		$FacebookName = isset($_POST['FacebookName']) ? fix_string($_POST['FacebookName']) : "";
		$City = isset($_POST['City']) ? fix_string($_POST['City']) : "";
		$State = isset($_POST['State']) ? fix_string($_POST['State']) : "";
		$Country = isset($_POST['Country']) ? fix_string($_POST['Country']) : "";
		$YearFounded = isset($_POST['YearFounded']) ? fix_string($_POST['YearFounded']) : "";
		$JerseyColorsHome = isset($_POST['JerseyColorsHome']) ? fix_string($_POST['JerseyColorsHome']) : "";
		$JerseyColorsAway = isset($_POST['JerseyColorsAway']) ? fix_string($_POST['JerseyColorsAway']) : "";
		$ShortsColor = isset($_POST['ShortsColor']) ? fix_string($_POST['ShortsColor']) : "";
		
		$MatchFieldName = isset($_POST['MatchFieldName']) ? fix_string($_POST['MatchFieldName']) : "";
		$MatchFieldAddress = isset($_POST['MatchFieldAddress']) ? fix_string($_POST['MatchFieldAddress']) : "";
		$MatchFieldCity = isset($_POST['MatchFieldCity']) ? fix_string($_POST['MatchFieldCity']) : "";
		$MatchFieldState = isset($_POST['MatchFieldState']) ? fix_string($_POST['MatchFieldState']) : "";
		$MatchFieldZip = isset($_POST['MatchFieldZip']) ? fix_string($_POST['MatchFieldZip']) : "";
		$MatchFieldNotes = isset($_POST['MatchFieldNotes']) ? fix_string(str_replace("\r", "", $_POST['MatchFieldNotes'])) : "";
		$Clubhouse = isset($_POST['Clubhouse']) ? fix_string($_POST['Clubhouse']) : "";
		$Shower = isset($_POST['Shower']) ? fix_string($_POST['Shower']) : "";
		$PracticeFieldName = isset($_POST['PracticeFieldName']) ? fix_string($_POST['PracticeFieldName']) : "";
		$PracticeFieldAddress = isset($_POST['PracticeFieldAddress']) ? fix_string($_POST['PracticeFieldAddress']) : "";
		$PracticeFieldCity = isset($_POST['PracticeFieldCity']) ? fix_string($_POST['PracticeFieldCity']) : "";
		$PracticeFieldState = isset($_POST['PracticeFieldState']) ? fix_string($_POST['PracticeFieldState']) : "";
		$PracticeFieldZip = isset($_POST['PracticeFieldZip']) ? fix_string($_POST['PracticeFieldZip']) : "";
		$PracticeFieldNotes = isset($_POST['PracticeFieldNotes']) ? fix_string(str_replace("\r", "", $_POST['PracticeFieldNotes'])) : "";
		
	} else {
		
		## Get data from HiPer to display #############################
		$NickName = $record->getField('Nickname');
		$Level = $record->getField('Level');
		$TeamStatus = $record->getField('TeamStatus');
		$AgeGroup = $record->getField('AgeGroup');
		$Gender = $record->getField('Gender');
		$Website = $record->getField('Website');
		$FacebookURL = $record->getField('FacebookURL');
		$FacebookName = $record->getField('FacebookName');
		$TwitterHandle = $record->getField('TwitterHandle');
		$City = $record->getField('City');
		$State = $record->getField('State');
		$Country = $record->getField('Country');
		$YearFounded = $record->getField('yearFounded');
		$JerseyColorsHome = $record->getField('jerseyColorsHome');
		$JerseyColorsAway = $record->getField('jerseyColorsAway');
		$ShortsColor = $record->getField('shortsColor');
		$Notes = $record->getField('Notes');
		
		$MatchFieldName = $record->getField('matchFieldName');
		$MatchFieldAddress = $record->getField('matchFieldAddress');
		$MatchFieldCity = $record->getField('matchFieldCity');
		$MatchFieldState = $record->getField('matchFieldState');
		$MatchFieldZip = $record->getField('matchFieldZip');
		$MatchFieldNotes = $record->getField('matchFieldNotes');
		$Clubhouse = $record->getField('Clubhouse');
		$Shower = $record->getField('Shower');
		$PracticeFieldName = $record->getField('practiceFieldName');
		$PracticeFieldAddress = $record->getField('practiceFieldAddress');
		$PracticeFieldCity = $record->getField('practiceFieldCity');
		$PracticeFieldState = $record->getField('practiceFieldState');
		$PracticeFieldZip = $record->getField('practiceFieldZip');
		$PracticeFieldNotes = $record->getField('practiceFieldNotes');
		
	}
	
	if (isset($_POST['edit-club-submitted'])) {
		
		## Data Validation ############################
		$fail .= validate_empty_field($Level);
		$fail .= validate_empty_field($Gender);
		$fail .= validate_empty_field($Country);
		
		## Write Data to Database ############################
		$edit = $fm->newEditCommand('Club', $record->getRecordId());
		$edit->setField('Level', $Level);
		if ($Level == "High School" || $Level == "College") {
			$edit->setField('TeamStatus', $TeamStatus);
		}
		$edit->setField('TeamStatus', $TeamStatus);
		$edit->setField('AgeGroup', $AgeGroup);
		$edit->setField('Gender', $Gender);
		$edit->setField('Notes', $Notes);
		
		$edit->setField('Website', $Website);
		$edit->setField('TwitterHandle', $TwitterHandle);
		$edit->setField('FacebookURL', $FacebookURL);
		$edit->setField('FacebookName', $FacebookName);
		$edit->setField('City', $City);
		$edit->setField('State', $State);
		$edit->setField('Country', $Country);
		$edit->setField('yearFounded', $YearFounded);
		$edit->setField('jerseyColorsHome', $JerseyColorsHome);
		$edit->setField('jerseyColorsAway', $JerseyColorsAway);
		$edit->setField('shortsColor', $ShortsColor);
		
		$edit->setField('matchFieldName', $MatchFieldName);
		$edit->setField('matchFieldAddress', $MatchFieldAddress);
		$edit->setField('matchFieldCity', $MatchFieldCity);
		$edit->setField('matchFieldState', $MatchFieldState);
		$edit->setField('matchFieldZip', $MatchFieldZip);
		$edit->setField('matchFieldNotes', $MatchFieldNotes);
		$edit->setField('Clubhouse', $Clubhouse);
		$edit->setField('Shower', $Shower);
		$edit->setField('practiceFieldName', $PracticeFieldName);
		$edit->setField('practiceFieldAddress', $PracticeFieldAddress);
		$edit->setField('practiceFieldCity', $PracticeFieldCity);
		$edit->setField('practiceFieldState', $PracticeFieldState);
		$edit->setField('practiceFieldZip', $PracticeFieldZip);
		$edit->setField('practiceFieldNotes', $PracticeFieldNotes);
		
		## Copy new image URL to record, and run script to update picture   ###
		## Only if an image was uploaded ######################################
		if (!empty($LogoCropPath)) {
			$edit->setField('Club2::LogoURL', $LogoCropPath);
		}
		
		// Commit Data:
		$result = $edit->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem updating your club. Please send a note to tech@hiperforms.com with the following information so they can review the record: </p>"
				. "<p>Error Code 341: " . $result->getMessage() . "</p>";
			die();
		}
		
		if (!empty($LogoCropPath)) {
			// Run script to put image URLs into their container fields //
			$newPerformScript = $fm->newPerformScriptCommand('Club2', 'Club Image URL to Container', $ID_Club);
			$scriptResult = $newPerformScript->execute();
			if (FileMaker::isError($scriptResult)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 342: " . $scriptResult->getMessage() . "</p>";
				die();
			}
		}
		cleanPictures();
		
		if (!empty($fail)) {
			echo '
			<style type="text/css">
			.missing {
				border: 2px solid red
			}
			</style>';
		}
		
		## Update Club's record ##########################################################
		$request = $fm->newFindCommand('Club');
		$request->addFindCriterion('ID', '==' . $ID_Club);
		$result = $request->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 343: " . $result->getMessage() . "</p>";
			die();
		}
		$records = $result->getRecords();
		$record = $result->getFirstRecord();
	}
	
	if ($Nominate) {
		
		$ScriptParam = $Nominate_player . "|" . $ID_Personnel . "|" . $Nominate_type . "|" . $Gender;
		$newPerformScript = $fm->newPerformScriptCommand('Personnel__ClubMembership', 'Nominate Player', $ScriptParam);
		$scriptResult = $newPerformScript->execute();
		if (FileMaker::isError($scriptResult)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 344: " . $scriptResult->getMessage() . "</p>";
			die();
		}
	}
	
	$update_Club = false;
	if (isset($_POST['update-members']) && !$Nominate) {
		
		$ClubMember_Original = isset($_SESSION['ClubMember_Original']) ? $_SESSION['ClubMember_Original'] : "";
		$ClubMember_Update = isset($_POST['ClubMember_Update']) ? $_POST['ClubMember_Update'] : "";
		$ClubMember_changed = ($ClubMember_Update == $ClubMember_Original ? false : true);
		
		if ($ClubMember_changed) {
			$eMail = $_SESSION['eMail'];
			while ($a = current($ClubMember_Update) && $b = current($ClubMember_Original)) { //For each element in the array
				$update_RecordID = key($ClubMember_Update);
				$update_record = current($ClubMember_Update);
				$original_record = current($ClubMember_Original);
				$diff = array_diff_assoc($update_record, $original_record);
				if (!empty($diff)) { //Edit the record if there is a difference from the update
					$ClubMember_edit = $fm->newEditCommand('Personnel__ClubMembership', $update_RecordID);
					foreach ($diff as $ClubMember_field => $ClubMember_field_value) { //Update only the fields that were in the diff array as changes
						if ($ClubMember_field == "StartDate" || $ClubMember_field == "EndDate") {
							if (validate_date($ClubMember_field_value) == false && validate_date_filemaker($ClubMember_field_value) == false && !empty($ClubMember_field_value)) {
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
							. "<p>Error Code 345: (" . $ClubMember_result->code . ") " . $ClubMember_result->getMessage() . "</p>";
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
				next($ClubMember_Update);
				next($ClubMember_Original);
			}
			$update_Club = true;
		}
		
	}
	
	// Always do these on each page reload //
	
	if ($update_Club) {
		$request = $fm->newFindCommand('Club');
		$request->addFindCriterion('ID', '==' . $ID_Club);
		$result = $request->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 346: " . $result->getMessage() . "</p>";
			die();
		}
		$records = $result->getRecords();
		$record = $result->getFirstRecord();
	}
	
	$ClubName = $record->getField('clubName');
	$HeadCoach = $record->getField('c_HeadCoachName');
	$Logo64 = $EditClub ? $record->getField('Club2::Logo64') : $record->getField('Club2::Logo_thumbnail64');
	
	## Get National Pool records to display if a player has already been nominated #############################
	if ($Level == "High School" && $TeamStatus == "JV") {
		$ID_PoolClub = $Gender == "Men" ? "DECD3F91-9881-6A42-81AA-8D7ADFC129F7" : "90D9B41C-7185-C344-A257-36E893CF35A4";
		$request = $fm->newFindCommand('Personnel__ClubMembership');
		$request->addFindCriterion('ID_Club', '==' . $ID_PoolClub);
		$request->addFindCriterion('Role', '==' . "Player");
		$request->addFindCriterion('Inactive_flag', '=');
		$result = $request->execute();
		if (FileMaker::isError($result) && FileMaker::isError($result) != 401) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 347: " . $result->getMessage() . "</p>";
			die();
		} elseif (!FileMaker::isError($result)) {
			$PoolMemberRecords = $result->getRecords();
		}
	} elseif ($Level == "High School") {
		$ID_PoolClub = $Gender == "Men" ? "0FE85A5D-BE04-4F0E-8F1E-E0FF9D2BCC2A" : "90D9B41C-7185-C344-A257-36E893CF35A4";
		$request = $fm->newFindCommand('Personnel__ClubMembership');
		$request->addFindCriterion('ID_Club', '==' . $ID_PoolClub);
		$request->addFindCriterion('Role', '==' . "Player");
		$request->addFindCriterion('Inactive_flag', '=');
		$result = $request->execute();
		if (FileMaker::isError($result) && FileMaker::isError($result) != 401) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 348: " . $result->getMessage() . "</p>";
			die();
		} elseif (!FileMaker::isError($result)) {
			$PoolMemberRecords = $result->getRecords();
		}
	}
	
	if ($Level == "College") {
		//CAA
		$ID_PoolClub = $Gender == "Men" ? "D9459E86-519B-D849-8E43-B952EE3B19AE" : "DC0B2DD5-6E2C-8640-87E4-DBC96F49E9AF";
		$request = $fm->newFindCommand('Personnel__ClubMembership');
		$request->addFindCriterion('ID_Club', '==' . $ID_PoolClub);
		$request->addFindCriterion('Role', '==' . "Player");
		$request->addFindCriterion('Inactive_flag', '=');
		$result = $request->execute();
		if (FileMaker::isError($result) && FileMaker::isError($result) != 401) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 349: " . $result->getMessage() . "</p>";
			die();
		} elseif (!FileMaker::isError($result)) {
			$PoolMemberRecords = $result->getRecords();
		}
		//JAA
		$ID_PoolClub = $Gender == "Men" ? "E0C796E1-DA60-FE4E-88FF-EFB8C222FA4E" : "C219B4DA-D007-9A44-B2DA-EFD6D934017C";
		$request = $fm->newFindCommand('Personnel__ClubMembership');
		$request->addFindCriterion('ID_Club', '==' . $ID_PoolClub);
		$request->addFindCriterion('Role', '==' . "Player");
		$request->addFindCriterion('Inactive_flag', '=');
		$result = $request->execute();
		if (FileMaker::isError($result) && FileMaker::isError($result) != 401) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 350: " . $result->getMessage() . "</p>";
			die();
		} elseif (!FileMaker::isError($result)) {
			$PoolMemberRecords2 = $result->getRecords();
		}
		
		$PoolMemberRecords = array_merge($PoolMemberRecords, $PoolMemberRecords2);
	}
	
	if ($Level == "Senior" || $Level == "Pro") {
		//JAA
		$ID_PoolClub = $Gender == "Men" ? "E0C796E1-DA60-FE4E-88FF-EFB8C222FA4E" : "C219B4DA-D007-9A44-B2DA-EFD6D934017C";
		$request = $fm->newFindCommand('Personnel__ClubMembership');
		$request->addFindCriterion('ID_Club', '==' . $ID_PoolClub);
		$request->addFindCriterion('Role', '==' . "Player");
		$request->addFindCriterion('Inactive_flag', '=');
		$result = $request->execute();
		if (FileMaker::isError($result) && FileMaker::isError($result) != 401) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 351: " . $result->getMessage() . "</p>";
			die();
		} elseif (!FileMaker::isError($result)) {
			$PoolMemberRecords = $result->getRecords();
		}
		//USA Selects
		$ID_PoolClub = $Gender == "Men" ? "B76C3721-5603-4167-BDCF-EB3505C163C5" : "";
		$request = $fm->newFindCommand('Personnel__ClubMembership');
		$request->addFindCriterion('ID_Club', '==' . $ID_PoolClub);
		$request->addFindCriterion('Role', '==' . "Player");
		$request->addFindCriterion('Inactive_flag', '=');
		$result = $request->execute();
		if (FileMaker::isError($result) && FileMaker::isError($result) != 401) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 352: " . $result->getMessage() . "</p>";
			die();
		}
		$PoolMemberRecords2 = $result->getRecords();

		$PoolMemberRecords = array_merge($PoolMemberRecords, $PoolMemberRecords2);
	}
	
	$PoolMembers = array();
	if (isset($PoolMemberRecords)) {
		foreach ($PoolMemberRecords as $PoolMemberRecord) {
			array_push($PoolMembers, $PoolMemberRecord->getField('ID_Personnel'));
		}
	}
	
	## Get Related ClubMembership Records #############################
	$related_ClubMembership = $record->getRelatedSet('ClubMembership.activeAndRecent');
	if (FileMaker::isError($related_ClubMembership)) {
		$related_ClubMembership_count = 0;
	} else {
		$related_ClubMembership_count = count($related_ClubMembership);
	}
	
	if ($EditClub) {
		//## Determine what to show in the Image editors ##//
		$LogoEditor = (empty($Logo64) ? "../include/MissingLogo.PNG" : $Logo64);
	}
	
	if ($EditClub) {
		## Get Drop Down List Values ###################################################
		$ClubLevelValues = $layout->getValueListTwoFields('Club Level');
		$AgeGroupValues = $layout->getValueListTwoFields('Age Group');
		$ClubGenderValues = $layout->getValueListTwoFields('Club Gender');
		$stateValues = $layout_Header->getValueListTwoFields('State-Territory');
		$countryValues = $layout_Header->getValueListTwoFields('Countries');
		$ClubRoleValues = $layout->getValueListTwoFields('Club Roles');
		################################################################################
	}
	
	?>
</head>

<body class="polaroid">
<div id="container">


	<div class="header" style="text-align: center; position: relative; height: 100px">
		<div style="position: absolute; left: 1em">
			<img src="../include/USAR-logo.png" alt="logo"/>
		</div>
		<h1 class="narrow">Club Management for <?php echo $ClubName; ?></h1>
<!--		<h2 class="narrow">Script Param: --><?php //echo $ScriptParam; ?><!--</h2>-->
	</div>

	<div style="position: relative">
		<div style="position: absolute; top: -28px; right: 8px; font-size: 125%">
			<a href="body.php?activeTab=5"><span style="color: dimgray">Back to Your Profile</span></a>
		</div>
	</div>

	<!-- Add table to display any error messages from submitted form. -->
	<?php if (!empty($fail) && (!empty($_POST['edit-club-submitted']) || !empty($_POST['update-members']))) { ?>
		<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
			<tr>
				<td>The update failed due to the following problems:
					<p style="color: red"><i>
							<?php echo $fail; ?>
						</i></p>
				</td>
			</tr>
		</table>
	<?php } ?>
	<!-- ################################# -->

	<div class="input">
		<h5>Notes</h5>
		<ul style="width: 90%;">
			<li>Date Fields: All dates must be entered in the mm/dd/yyyy or yyyy-mm-dd format.</li>
			<li>For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</li>
			<li>For questions regarding the data itself, or to request changes to read-only fields, contact <a
						href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.
			</li>
			<li>Members Listed: All Active, and all Inactive with an End Date up to 3 month old.</li>
			<li>Your Priviledges for this record: <em><b>Club/Membership:</b> <?php echo $EditClub ? 'Edit' : 'View Only'; ?>, <b>Member
						Profiles:</b> <?php echo $EditMembers ? 'Edit' : 'View Only'; ?></em></li>
			<li>To Nominate a player for a national level pool, the player must be active, and all mandatory club fields filled in.</li>
		</ul>
	</div>

	<div id="tabbed">

		<!-- Column 1 (Club info) -->
		<div class="cell w-49 table">
			<form id="edit-club" action="ManageClub.php" method="post" enctype="multipart/form-data">
				<fieldset class='group aashadow' style="margin-top: 0">
					<legend>&nbspClub Details&nbsp</legend>

					<div class="input" style="border-top: none;">
						<label class="w-12" for="Name">Club Name</label>
						<p>
							<?php echo $ClubName; ?>
						</p>
					</div>

					<div class="input">
						<label class="w-12" for="NickName">Nickname</label>
						<?php if ($EditClub) { ?>
							<input name="NickName" type="text" size="20" id="NickName"
									 title="Team Nickname" <?php recallText((empty($NickName) ? "" : $NickName), "no"); ?> />
						<?php } else {
							echo "<p>" . $NickName . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="slim-Logo">Logo</label>
						<?php if ($EditClub) { ?>

							<div class="imgpreview">

								<div class="slim"
									  id="slim-Logo"
									  data-instant-edit="true"
									  data-download="true"
									  data-fetcher="../fetch.php">
									<input type="file" name="slim_logo[]"/>
									<img src="<?php echo $LogoEditor; ?>" alt="">
								</div>
								<div class="row">
									<span style="font-style: italic;">Select the image above, or drag an image over, to upload a new image.</span>
								</div>

							</div>
							
						<?php } elseif (!empty($Logo64)) {
							echo "
						<div style='display: inline-block; width: 100px; height: 80px;  background-size: contain; background-repeat: no-repeat; background-image:url(" . $Logo64 . ")'></div>
					";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="Level">Level
							<span class="<?php if (empty($Level)) {
								echo "mandatoryFailed";
							} else {
								echo "mandatory";
							} ?>">REQUIRED</span>
						</label>
						<?php if ($EditClub) { ?>
							<select name="Level" size="1" id="Level" <?php if (empty($Level)) {
								echo 'class="missing"';
							} ?> >
								<option value="">&nbsp;</option>
								<?php
								foreach ($ClubLevelValues as $value) {
									echo "<option value='" . $value . "' " . ($Level == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						<?php } else {
							echo "<p>" . $Level . "</p>";
						} ?>
					</div>
					
					<?php if ($Level == "High School" || $Level == "College") { ?>

						<div class="input">
							<label class="w-12" for="TeamStatus">Player Level</label>
							<?php if ($EditClub) { ?>
								<select name="TeamStatus" size="1" id="TeamStatus">
									<option value="">&nbsp;</option>
									<option value="JV" <?php if ($TeamStatus == "JV") {
										echo 'selected="selected"';
									} ?> >JV
									</option>
									<option value="Varsity" <?php if ($TeamStatus == "Varsity") {
										echo 'selected="selected"';
									} ?> >Varsity
									</option>
								</select>
							<?php } else {
								echo "<p>" . $TeamStatus . "</p>";
							} ?>
						</div>
					
					<?php } ?>

					<div class="input">
						<label class="w-12" for="AgeGroup">Age Group</label>
						<?php if ($EditClub) { ?>
							<select name="AgeGroup" size="1" id="AgeGroup">
								<option value="">&nbsp;</option>
								<?php
								foreach ($AgeGroupValues as $value) {
									echo "<option value='" . $value . "' " . ($AgeGroup == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						<?php } else {
							echo "<p>" . $AgeGroup . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="Gender">Gender
							<span class="<?php if (empty($Gender)) {
								echo "mandatoryFailed";
							} else {
								echo "mandatory";
							} ?>">REQUIRED</span>
						</label>
						<?php if ($EditClub) { ?>
							<select name="Gender" size="1" id="Gender" <?php if (empty($Gender)) {
								echo 'class="missing"';
							} ?> >
								<option value="">&nbsp;</option>
								<?php
								foreach ($ClubGenderValues as $value) {
									echo "<option value='" . $value . "' " . ($Gender == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						<?php } else {
							echo "<p>" . $Gender . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="HeadCoach">Head Coach</label>
						<p>
							<?php echo $HeadCoach; ?>
						</p>
					</div>

					<div class="input">
						<label class="w-12" for="Notes">Notes</label>
						<?php if ($EditClub) { ?>
							<textarea style="width: 100%;" form="edit-club" rows="4" maxlength="500"
										 placeholder="Enter notes about this club here." name="Notes"><?php echo $Notes; ?></textarea>
						<?php } else {
							echo "<p>" . $Notes . "</p>";
						} ?>
					</div>

				</fieldset>

				<fieldset class='group aashadow'>
					<legend>&nbspLocation&nbsp</legend>

					<div class="input" style="border-top: none;">
						<label class="w-12" for="YearFounded">Year Founded</label>
						<?php if ($EditClub) { ?>
							<input name="YearFounded" type="text" size="20" id="YearFounded"
									 title="Year Founded" <?php recallText((empty($YearFounded) ? "" : $YearFounded), "no"); ?> />
						<?php } else {
							echo "<p>" . $YearFounded . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="City">City</label>
						<?php if ($EditClub) { ?>
							<input name="City" type="text" size="20" id="City"
									 title="Club City" <?php recallText((empty($City) ? "" : $City), "no"); ?> />
						<?php } else {
							echo "<p>" . $City . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="State">State</label>
						<?php if ($EditClub) { ?>
							<select name="State" size="1" id="State">
								<option value="">&nbsp;</option>
								<?php
								foreach ($stateValues as $value) {
									echo "<option value='" . $value . "' " . ($State == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						<?php } else {
							echo "<p>" . $State . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="Country">Country
							<span class="<?php if (empty($Country)) {
								echo "mandatoryFailed";
							} else {
								echo "mandatory";
							} ?>">REQUIRED</span>
						</label>
						<?php if ($EditClub) { ?>
							<select name="Country" size="1" id="Country" <?php if (empty($Country)) {
								echo 'class="missing"';
							} ?> >
								<option value="">&nbsp;</option>
								<?php
								foreach ($countryValues as $value) {
									echo "<option value='" . $value . "' " . ($Country == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						<?php } else {
							echo "<p>" . $Country . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="JerseyColorsHome">Home Jersey Colors</label>
						<?php if ($EditClub) { ?>
							<input name="JerseyColorsHome" type="text" size="20" id="JerseyColorsHome"
									 title="Home Jersey Colors" <?php recallText((empty($JerseyColorsHome) ? "" : $JerseyColorsHome), "no"); ?> />
						<?php } else {
							echo "<p>" . $JerseyColorsHome . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="JerseyColorsAway">Away Jersey Colors</label>
						<?php if ($EditClub) { ?>
							<input name="JerseyColorsAway" type="text" size="20" id="JerseyColorsAway"
									 title="Away Jersey Colors" <?php recallText((empty($JerseyColorsAway) ? "" : $JerseyColorsAway), "no"); ?> />
						<?php } else {
							echo "<p>" . $JerseyColorsAway . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="ShortsColor">Shorts Color</label>
						<?php if ($EditClub) { ?>
							<input name="ShortsColor" type="text" size="20" id="ShortsColor"
									 title="Shorts Color" <?php recallText((empty($ShortsColor) ? "" : $ShortsColor), "no"); ?> />
						<?php } else {
							echo "<p>" . $ShortsColor . "</p>";
						} ?>
					</div>

				</fieldset>

				<fieldset class='group aashadow'>
					<legend>&nbspWeb&nbsp</legend>

					<div class="input" style="border-top: none;">
						<label class="w-12" for="Website">Website</label>
						<?php if ($EditClub) { ?>
							<input name="Website" type="text" size="36" id="Website"
									 title="Website" <?php recallText((empty($Website) ? "" : $Website), "no"); ?> />
						<?php } else {
							echo "<p><a href='" . $Website . "'>" . $Website . "</a></p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="TwitterHandle">Twitter Handle</label>
						<?php if ($EditClub) { ?>
							<input name="TwitterHandle" type="text" size="36" id="TwitterHandle"
									 title="Twitter Handle" <?php recallText((empty($TwitterHandle) ? "" : $TwitterHandle), "no"); ?> />
						<?php } else {
							echo "<p>" . $TwitterHandle . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="FacebookURL">Facebook URL</label>
						<?php if ($EditClub) { ?>
							<input name="FacebookURL" type="text" size="36" id="FacebookURL"
									 title="Facebook URL" <?php recallText((empty($FacebookURL) ? "" : $FacebookURL), "no"); ?> />
						<?php } else {
							echo "<p><a href='" . $FacebookURL . "'>" . $FacebookURL . "</a></p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="FacebookName">FacebookName</label>
						<?php if ($EditClub) { ?>
							<input name="FacebookName" type="text" size="36" id="FacebookName"
									 title="Facebook Name" <?php recallText((empty($FacebookName) ? "" : $FacebookName), "no"); ?> />
						<?php } else {
							echo "<p>" . $FacebookName . "</p>";
						} ?>
					</div>

				</fieldset>

				<fieldset class='group aashadow'>
					<legend>&nbspMatch Field&nbsp</legend>

					<div class="input" style="border-top: none;">
						<label class="w-12" for="MatchFieldName">Field Name</label>
						<?php if ($EditClub) { ?>
							<input name="MatchFieldName" type="text" size="36" id="MatchFieldName"
									 title="Match Field Name" <?php recallText((empty($MatchFieldName) ? "" : $MatchFieldName), "no"); ?> />
						<?php } else {
							echo "<p>" . $MatchFieldName . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="MatchFieldAddress">Street Address</label>
						<?php if ($EditClub) { ?>
							<input name="MatchFieldAddress" type="text" size="36" id="MatchFieldAddress"
									 title="Match Field Address" <?php recallText((empty($MatchFieldAddress) ? "" : $MatchFieldAddress), "no"); ?> />
						<?php } else {
							echo "<p>" . $MatchFieldAddress . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="MatchFieldCity">City</label>
						<?php if ($EditClub) { ?>
							<input name="MatchFieldCity" type="text" size="36" id="MatchFieldCity"
									 title="Match Field City" <?php recallText((empty($MatchFieldCity) ? "" : $MatchFieldCity), "no"); ?> />
						<?php } else {
							echo "<p>" . $MatchFieldCity . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12">State / Zip</label>
						<div style="margin-left: 12em">
							<?php if ($EditClub) { ?>
								<select name="MatchFieldState" size="1" id="MatchFieldState" title="Match Field State" style="margin-right: 1em">
									<option value="">&nbsp;</option>
									<?php
									foreach ($stateValues as $value) {
										echo "<option value='" . $value . "' " . ($MatchFieldState == $value ? "selected='selected'>" : ">") . $value . "</option>";
									}
									?>
								</select>
								<input name="MatchFieldZip" type="text" size="16" id="MatchFieldZip"
										 title="Match Field Zip" <?php recallText((empty($MatchFieldZip) ? "" : $MatchFieldZip), "no"); ?> />
							<?php } else {
								echo "<p>" . $MatchFieldState . "<pre>&#9</pre>" . $MatchFieldZip . "</p>";
							} ?>
						</div>
					</div>

					<div class="input">
						<label class="w-12" for="Clubhouse">Clubhouse</label>
						<?php if ($EditClub) { ?>
							<select name="Clubhouse" size="1" id="Clubhouse">
								<option value="">&nbsp;</option>
								<option value="Yes">Yes</option>
								<option value="No">No</option>
							</select>
						<?php } else {
							echo "<p>" . $Clubhouse . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="Shower">Shower</label>
						<?php if ($EditClub) { ?>
							<select name="Shower" size="1" id="Shower">
								<option value="">&nbsp;</option>
								<option value="Yes">Yes</option>
								<option value="No">No</option>
							</select>
						<?php } else {
							echo "<p>" . $Shower . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="MatchFieldNotes">Match Field Notes</label>
						<?php if ($EditClub) { ?>
							<textarea style="width: 100%;" form="edit-club" rows="3" maxlength="500"
										 placeholder="Match Field Notes" name="MatchFieldNotes"><?php echo $MatchFieldNotes; ?></textarea>
						<?php } else {
							echo "<p>" . $MatchFieldNotes . "</p>";
						} ?>
					</div>

				</fieldset>

				<fieldset class='group aashadow'>
					<legend>&nbspPractice Field&nbsp</legend>

					<div class="input" style="border-top: none;">
						<label class="w-12" for="PracticeFieldName">Field Name</label>
						<?php if ($EditClub) { ?>
							<input name="PracticeFieldName" type="text" size="36" id="PracticeFieldName"
									 title="Practice Field Name" <?php recallText((empty($PracticeFieldName) ? "" : $PracticeFieldName), "no"); ?> />
						<?php } else {
							echo "<p>" . $PracticeFieldName . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="PracticeFieldAddress">Street Address</label>
						<?php if ($EditClub) { ?>
							<input name="PracticeFieldAddress" type="text" size="36" id="PracticeFieldAddress"
									 title="Practice Field Address" <?php recallText((empty($PracticeFieldAddress) ? "" : $PracticeFieldAddress), "no"); ?> />
						<?php } else {
							echo "<p>" . $PracticeFieldAddress . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12" for="PracticeFieldCity">City</label>
						<?php if ($EditClub) { ?>
							<input name="PracticeFieldCity" type="text" size="36" id="PracticeFieldCity"
									 title="Practice Field City" <?php recallText((empty($PracticeFieldCity) ? "" : $PracticeFieldCity), "no"); ?> />
						<?php } else {
							echo "<p>" . $PracticeFieldCity . "</p>";
						} ?>
					</div>

					<div class="input">
						<label class="w-12">State / Zip</label>
						<div style="margin-left: 12em">
							<?php if ($EditClub) { ?>
								<select name="PracticeFieldState" size="1" id="PracticeFieldState" title="Practice Field State"
										  style="margin-right: 1em">
									<option value="">&nbsp;</option>
									<?php
									foreach ($stateValues as $value) {
										echo "<option value='" . $value . "' " . ($PracticeFieldState == $value ? "selected='selected'>" : ">") . $value . "</option>";
									}
									?>
								</select>
								<input name="PracticeFieldZip" type="text" size="16" id="PracticeFieldZip"
										 title="Practice Field Zip" <?php recallText((empty($PracticeFieldZip) ? "" : $PracticeFieldZip), "no"); ?> />
							<?php } else {
								echo "<p>" . $PracticeFieldState . "<pre>&#9</pre>" . $PracticeFieldZip . "</p>";
							} ?>
						</div>
					</div>

					<div class="input">
						<label class="w-12" for="PracticeFieldNotes">Practice Field Notes</label>
						<?php if ($EditClub) { ?>
							<textarea style="width: 100%;" form="edit-club" rows="3" maxlength="500"
										 placeholder="Practice Field Notes" name="PracticeFieldNotes"><?php echo $PracticeFieldNotes; ?></textarea>
						<?php } else {
							echo "<p>" . $PracticeFieldNotes . "</p>";
						} ?>
					</div>

				</fieldset>

				<input type="submit" name="APPLY" value="Update Club" class="submit buy" id="Submit_Button"/>
				<input type="hidden" name="edit-club-submitted" value="true"/>

				<input id="CroppedLogo" name="CroppedLogo" type="hidden" value=""/>

			</form>
		</div>
		<!-- / Column 1 content (Club info)-->

		<!-- Column 2 (Members) -->
		<div class="cell w-49">
			<form id='form-ClubMembers' action='ManageClub.php' method='post'>
				<fieldset class='group aashadow' style="margin-top: 0">
					<legend>&nbspMembers&nbsp<?php echo $message; ?></legend>
					
					<?php
					if ($related_ClubMembership_count > 0) {
						
						foreach ($related_ClubMembership as $ClubMembership_record) {
							
							$Member_RecordID = $ClubMembership_record->getRecordID();
							$Member_ID_Personnel = $ClubMembership_record->getField('ClubMembership.activeAndRecent::ID_Personnel');
							$Member_Name = $ClubMembership_record->getField('ClubMembership.activeAndRecent::c_PersonnelNameLong') == "" ? "-" : $ClubMembership_record->getField('ClubMembership.activeAndRecent::c_PersonnelNameLong');
							$ClubMember[$Member_RecordID]['Inactive_flag'] = $ClubMembership_record->getField('ClubMembership.activeAndRecent::Inactive_flag');
							$ClubMember[$Member_RecordID]['Role'] = $ClubMembership_record->getField('ClubMembership.activeAndRecent::Role');
							$ClubMember[$Member_RecordID]['StartDate'] = $ClubMembership_record->getField('ClubMembership.activeAndRecent::StartDate');
							$ClubMember[$Member_RecordID]['EndDate'] = $ClubMembership_record->getField('ClubMembership.activeAndRecent::EndDate');
							$InPool = in_array($Member_ID_Personnel, $PoolMembers);
							
							echo "
							<div class='row-divider row-divider-color'>
								<div class='row'>
									<fieldset class='field' style='width: 63%;'>
									<legend>Member Name</legend>
										" . $Member_Name . "
									</fieldset>
									<fieldset class='field' style='width: 28%;'>
									<legend>Inactive</legend>";
							if ($EditClub) {
								echo "
									<input class='alpha50' name='ClubMember_Update[" . $Member_RecordID . "][Inactive_flag]' type='radio' value='0'
							 		" . ($ClubMember[$Member_RecordID]['Inactive_flag'] != 1 ? "checked='checked'" : "") . ($ClubMember[$Member_RecordID]['Inactive_flag'] != 1 ? "checked='checked'" : "") . " />
							 		No
									<input class='alpha50' name='ClubMember_Update[" . $Member_RecordID . "][Inactive_flag]' type='radio' value='1'
							 		title='Select this if the member is no longer active under their selected Role' " . ($ClubMember[$Member_RecordID]['Inactive_flag'] == 1 ? "checked='checked'" : "") . " />
							 		Yes";
							} else {
								echo "<p>" . ($ClubMember[$Member_RecordID]['Inactive_flag'] == 1 ? "Yes" : "No") . "</p>";
							}
							echo "
									</fieldset>
								</div>
								
								<div class='row'>
									<fieldset class='field' style='width: 32%;'>
									<legend>Club Role</legend>";
							if ($EditClub) {
								echo "
											<select class='alpha50' name='ClubMember_Update[" . $Member_RecordID . "][Role]' size='1' title='What is your role in this club?'>";
								foreach ($ClubRoleValues as $value) {
									echo "<option value='" . $value . "' " . ($ClubMember[$Member_RecordID]['Role'] == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
							} else {
								echo "<p>" . $ClubMember[$Member_RecordID]['Role'] . "</p>";
							}
							echo "
										</select>
									</fieldset>
									<fieldset class='field' style='width: 27%;'>
									<legend>Start Date</legend>";
							if ($EditClub) {
								echo "
											<input class='alpha50 Date-80-1' type='text' size='12' name='ClubMember_Update[" . $Member_RecordID . "][StartDate]' title='The Date You Joined this Club' value='" . $ClubMember[$Member_RecordID]['StartDate'] . "' />";
							} else {
								echo "<p>" . $ClubMember[$Member_RecordID]['StartDate'] . "</p>";
							}
							echo "
									</fieldset>
									<fieldset class='field' style='width: 27%;'>
									<legend>End Date</legend>";
							if ($EditClub) {
								echo "
											<input class='alpha50 Date-80-1' type='text' size='12' name='ClubMember_Update[" . $Member_RecordID . "][EndDate]' title='The Date You Left this Club' value='" . $ClubMember[$Member_RecordID]['EndDate'] . "' />";
							} else {
								echo "<p>" . $ClubMember[$Member_RecordID]['EndDate'] . "</p>";
							}
							echo "
									</fieldset>
								</div>";
							
							if ($ClubMember[$Member_RecordID]['Inactive_flag'] != 1) {
								echo "
								<div class='row'>";
								if ($EditMembers) {
									echo "
									<button type='submit' formaction='body.php?ID=" . $Member_ID_Personnel . "' class='btn btn-primary entypo-user View_Member_Button'>&nbsp;View</button>";
								}
								if ($ClubMember[$Member_RecordID]['Role'] == "Player" && ($Level == "High School" || $Level == "College" || $Level == "Senior" || $Level == "Pro") && !empty($Gender)) {
									if ($InPool) {
										echo "
									<button type='button' class='btn btn-primary entypo-check Nominated_Button' style='background-color: green'>&nbsp;Nominated</button>";
									} else {
										echo "
									<button type='button' class='btn btn-primary entypo-thumbs-up Nominate_Button' name='" . $Member_Name . "' id='" . $Member_ID_Personnel . "'>&nbsp;Nominate</button>";
									}
								}
								echo "</div>";
							}
							
							echo "
								<div id='dialog-" . $Member_ID_Personnel . "' class='Nominate_Dialog' title=''>
									<p id='Nominate_Header-" . $Member_ID_Personnel . "'></p>
									<button name='" . $Member_ID_Personnel . "' type='button' class='btn btn-primary' id='NominateButton1-" . $Member_ID_Personnel . "'></button>
									<button name='" . $Member_ID_Personnel . "' type='button' class='btn btn-primary hidden'
											  id='NominateButton2-" . $Member_ID_Personnel . "'></button>
								</div>
								
							<script>
                         var ID_Player = '" . $Member_ID_Personnel . "';
                         $('#dialog-' + ID_Player).dialog({
                             autoOpen: false,
                             show: {
                                 effect: 'blind',
                                 duration: 1000
                             },
                             modal: true,
                             width: 500
                         });
                         
                         $('#NominateButton1-' + ID_Player).click(function () {
                         	var ID_Player = '" . $Member_ID_Personnel . "';
									var type = $('#NominateButton1-' + ID_Player).text();
									document.getElementById('Nominate_player').value = this.name;
									document.getElementById('Nominate_type').value = type;
									$('form#form-ClubMembers').submit();
								});
                         $('#NominateButton2-' + ID_Player).click(function () {
                         	var ID_Player = '" . $Member_ID_Personnel . "';
									var type = $('#NominateButton2-' + ID_Player).text();
									document.getElementById('Nominate_player').value = this.name;
									document.getElementById('Nominate_type').value = type;
									$('form#form-ClubMembers').submit();
								});
							</script>
					
							</div>
							";
							
						}
						
					}
					?>


				</fieldset>

				<input type='hidden' name='update-members' value='true'/>
				<input type='hidden' name='Nominate_player' id="Nominate_player" value=""/>
				<input type='hidden' name='Nominate_type' id="Nominate_type" value=""/>
				<!-- The original values array lets us only update field changes upon form submission.
					  Great when only updating 1 field out of 300 potential fields -->
				<?php $_SESSION['ClubMember_Original'] = isset($ClubMember) ? $ClubMember : ""; ?>

				<input type='submit' name='APPLY' value='Update Members' class='submit buy' id='Submit_Members_Button'/>

			</form>
			<?php
			//			echo '$ClubMember_Original: <br />';
			//			print_r($ClubMember_Original);
			//			echo '<br /><br />$ClubMember_Update: <br />';
			//			print_r($ClubMember_Update);
			?>
		</div>
		<!-- / Column 2 content (Members)-->

	</div>
	<!-- / wrapper -->

	<script>
       $(document).ready(function () {

           //
           <!-- Submit popover -->
           $('#Submit_Button').click(function () {
               $("#Submit_Dialog").dialog("open");
           });
           $('#Submit_Members_Button').click(function () {
               $("#Submit_Members_Dialog").dialog("open");
           });
           $('.View_Member_Button').click(function () {
               $("#View_Member_Dialog").dialog("open");
           });

           // Nominating players to national pools
           $('.Nominate_Button').click(function () {
               var title = "Nomination for " + this.name;
               var buttonTitle1;
               var buttonTitle2;
               var ID_Player = this.id;
               var Nominator = "<?php echo $PreferredName; ?>";
               var Level = "<?php echo $Level; ?>";
               var PlayerLevel = "<?php echo $TeamStatus; ?>";
               //## Nomination Type
               var NominationHeader = "I, " + Nominator + ", nominate " + this.name + " for:";
               switch (Level) {
                   case "High School":
                       if (PlayerLevel === "JV") {
                           buttonTitle1 = "High School All Americans, JV";
                       } else {
                           buttonTitle1 = "High School All Americans";
                       }
                       break;
                   case "College":
                       buttonTitle1 = "Junior All Americans";
                       buttonTitle2 = "Collegiate All Americans";
                       break;
                   case "Senior":
                       buttonTitle1 = "Junior All Americans";
                       buttonTitle2 = "USA Selects";
                       break;
                   case "Pro":
                       buttonTitle1 = "Junior All Americans";
                       buttonTitle2 = "USA Selects";
                       break;
               }
               $("#Nominate_Header-" + ID_Player).text(NominationHeader);
               $("#dialog-" + ID_Player).dialog("open");
               $("#dialog-" + ID_Player).dialog("option", "title", title);
               $("#NominateButton1-" + ID_Player).text(buttonTitle1);
               if (buttonTitle2) {
                   $("#NominateButton2-" + ID_Player).text(buttonTitle2);
                   $("#NominateButton2-" + ID_Player).removeClass('hidden');
               }
           });

       });

       //
       <!-- Datepickers -->
       $(function () {
           $(".Date-80-1").datepicker({
               changeMonth: true,
               changeYear: true,
               yearRange: "-80:+1"
           });
       });

       //
       <!-- Dialog popovers -->
       $(function () {
           $("#Submit_Dialog").dialog({
               autoOpen: false,
               show: {
                   effect: "blind",
                   duration: 1000
               },
               modal: true
           });
           $("#Submit_Members_Dialog").dialog({
               autoOpen: false,
               show: {
                   effect: "blind",
                   duration: 1000
               },
               modal: true
           });
           $("#View_Member_Dialog").dialog({
               autoOpen: false,
               show: {
                   effect: "blind",
                   duration: 1000
               },
               modal: true
           });
       });
	</script>


	<div id="Submit_Dialog" title="Updating Club">
		<p>Please wait while the club's record is updated. This can take up to a minute.</p>
	</div>

	<div id="Submit_Members_Dialog" title="Updating Members">
		<p>Please wait while the membership records are updated. This can take up to a minute.</p>
	</div>

	<div id='View_Member_Dialog' title='Opening Member Profile'>
		<p>Please wait while the selected profile is opened.</p>
	</div>
	
</body>
</html>