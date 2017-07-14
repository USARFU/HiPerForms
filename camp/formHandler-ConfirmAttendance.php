<?php

// Don't load certain data checks if the Form Development Camp ID is used //
if (empty($IDType)) {
	
	## Check that link isn't expired ############################################
	$inviteCutOffCompare_a = new DateTime($inviteCutOff);
	$inviteCutOffCompare = $inviteCutOffCompare_a->format('Y-m-d');
	$today = date('Y-m-d');
	if ($inviteCutOffCompare < $today || $inviteCutOff == "") {
		$message = "This link has expired. You are past this event's cut off date.";
	} else {
		## Check that Invite Status is not Declined ##############################
		$inviteStatusDB = $record->getField('inviteStatus');
		if ($inviteStatusDB == "Declined") {
			$message = "Your link is no longer active. Please contact the organizer of the event to activate your link.";
		}
	}
	
	$U18AtStartOfEvent = ($record->getField('c_U18AtStartOfEvent') != 1 ? 0 : 1);
	
	## Grab submitted form data ##################################################
	$inviteStatus = (isset($_POST['inviteStatus']) ? fix_string($_POST['inviteStatus']) : "");
	$methodOfTravel = (isset($_POST['methodOfTravel']) ? fix_string($_POST['methodOfTravel']) : "");
	$feePayMethod = (isset($_POST['feePayMethod']) ? fix_string($_POST['feePayMethod']) : "");
	$reasonForNotAttending = (isset($_POST['reasonForNotAttending']) ? fix_string($_POST['reasonForNotAttending']) : "");
	
	// Need to get rid of the data:image/png,base64 header for FileMaker
	$signatureConsent = (isset ($_POST['signatureConsent']) ? substr($_POST['signatureConsent'], 22) : "");
	$signatureConsentLength = (isset ($_POST['signatureConsentB30']) ? strlen($_POST['signatureConsentB30']) : 0);
	$signatureMedical = (isset ($_POST['signatureMedical']) ? substr($_POST['signatureMedical'], 22) : "");
	$signatureMedicalLength = (isset ($_POST['signatureMedicalB30']) ? strlen($_POST['signatureMedicalB30']) : 0);
	$sigConductPlayer = (isset ($_POST['sigConductPlayer']) ? substr($_POST['sigConductPlayer'], 22) : "");
	$sigConductPlayerLength = (isset ($_POST['sigConductPlayerB30']) ? strlen($_POST['sigConductPlayerB30']) : 0);
	$sigConductParent = (isset ($_POST['sigConductParent']) ? substr($_POST['sigConductParent'], 22) : "");
	$sigConductParentLength = (isset ($_POST['sigConductParentB30']) ? strlen($_POST['sigConductParentB30']) : 0);
	$sigMediaRelease = (isset ($_POST['sigMediaRelease']) ? substr($_POST['sigMediaRelease'], 22) : "");
	$sigMediaReleaseLength = (isset ($_POST['sigMediaReleaseB30']) ? strlen($_POST['sigMediaReleaseB30']) : 0);
	
	if (empty($signatureConsent)) {
		$signatureConsent = $record->getField('ParentSignatureInformedConsent64');
	}
	if (empty($signatureMedical)) {
		$signatureMedical = $record->getField('ParentSignatureMedicalRelease64');
	}
	if (empty($sigConductPlayer)) {
		$sigConductPlayer = $record->getField('PlayerSigConduct64');
	}
	if (empty($sigConductParent)) {
		$sigConductParent = $record->getField('ParentSigConduct64');
	}
	if (empty($sigMediaRelease)) {
		$sigMediaRelease = $record->getField('ParentSigMediaRelease64');
	}
} else { // End if(empty($IDType))
	$signatureConsentLength = (isset ($_POST['signatureConsentB30']) ? strlen($_POST['signatureConsentB30']) : 0);
	$signatureMedicalLength = (isset ($_POST['signatureMedicalB30']) ? strlen($_POST['signatureMedicalB30']) : 0);
	$sigConductPlayerLength = (isset ($_POST['sigConductPlayerB30']) ? strlen($_POST['sigConductPlayerB30']) : 0);
	$sigConductParentLength = (isset ($_POST['sigConductParentB30']) ? strlen($_POST['sigConductParentB30']) : 0);
	$sigMediaReleaseLength = (isset ($_POST['sigMediaReleaseB30']) ? strlen($_POST['sigMediaReleaseB30']) : 0);
}

$waiver = (isset ($_POST['waiver']) ? fix_string($_POST['waiver']) : "");

if (isset($_POST['respondent_exists']) && empty($IDType)) { // EventPersonnel with a submitted form
	$fail = validate_empty_field($inviteStatus, "Will You Be Attending?");
	if ($inviteStatus == "Yes" && $includeTravelMethod == "Mandatory") {
		$fail .= validate_empty_field($methodOfTravel, "Method of Travel");
	}
	if ($inviteStatus == "No") {
		$fail .= validate_empty_field($reasonForNotAttending, "Reason for Not Attending");
	}
	if ($inviteStatus == "Yes" && ($SignatureOption == "All Players" || ($SignatureOption == "U18 Players" && $U18AtStartOfEvent == 1))) {
		if ($signatureConsentLength < 26 && empty($signatureConsent)) {
			$fail .= "Parental Signature of Consent is missing. <br />";
		}
		if ($signatureMedicalLength < 26 && empty($signatureMedical)) {
			$fail .= "Parental Signature for Medical Release is missing. <br />";
		}
		if ($sigConductPlayerLength < 26 && empty($sigConductPlayer)) {
			$fail .= "Player Signature for Code of Conduct is missing. <br />";
		}
		if ($sigConductParentLength < 26 && empty($sigConductParent)) {
			$fail .= "Parent Signature for Code of Conduct is missing. <br />";
		}
		if ($sigMediaReleaseLength < 26 && empty($sigMediaRelease)) {
			$fail .= "Parent Signature for Media Release is missing. <br />";
		}
		
		$fail .= validate_waiver($waiver);
	} elseif (empty($SignatureOption)) {
		$fail .= validate_waiver($waiver);
	}
	
	if (empty($fail)) {
		if ($inviteStatus == "Yes") {
			$inviteStatus = "Accepted";
		} else {
			$inviteStatus = "Declined";
		}
		$edit = $fm->newEditCommand('PHP-EventInvite', $record->getRecordId());
		$edit->setField('inviteStatus', $inviteStatus);
		$edit->setField('methodOfTravel', $methodOfTravel);
		$edit->setField('reasonForNotAttending', $reasonForNotAttending);
		$edit->setField('feePayMethod', $feePayMethod);
		if ($SignatureOption == "All Players" || ($SignatureOption == "U18 Players" && $U18AtStartOfEvent == 1)) {
			$edit->setField('ParentSignatureInformedConsent64', $signatureConsent);
			$edit->setField('ParentSignatureMedicalRelease64', $signatureMedical);
			$edit->setField('PlayerSigConduct64', $sigConductPlayer);
			$edit->setField('ParentSigConduct64', $sigConductParent);
			$edit->setField('ParentSigMediaRelease64', $sigMediaRelease);
		}
		$result = $edit->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 202: " . $result->getMessage() . "</p>";
			die();
		}
		
		// Either go to CC Payment Form, or say Thank You //
		if ($includeProfile == 1 && $inviteStatus == "Accepted") {
			header("Location: Profile.php?ID=$ID");
			exit();
		} else if ($includeCCPayment == 1 && $inviteStatus == "Accepted") {
			header("Location: Payment.php?ID=$ID");
			exit();
		} else {
			$message = "Thank You. Your Registration Status has been Updated. <br />";
		}
		
	} else {
		//## Red Field Borders on required fields that failed
		echo '
		<style type="text/css">
			.missing {
			border: 2px solid red
			}
		</style>';
	}
} elseif (empty($IDType)) { // EventPersonnel ID, when the form first loads
	$inviteStatus = $record->getField('inviteStatus');
	if ($inviteStatus == "Accepted") {
		$inviteStatus = "Yes";
	}
	if ($inviteStatus == "Declined") {
		$inviteStatus = "No";
	}
	$methodOfTravel = $record->getField('methodOfTravel');
	$reasonForNotAttending = $record->getField('reasonForNotAttending');
	$feePayMethod = $record->getField('feePayMethod');
} else { // Camp form editor
	$inviteStatus = "";
	$methodOfTravel = "";
	$reasonForNotAttending = "";
	$feePayMethod = "";
}
?>

<!-- Get Drop-Down List values -->
<?php
if ($includeTravelMethod != "Hidden") {
	if ($playerLevel == "High School" || $playerLevel == "HSAA") {
		$methodOfTravelValues = $layout->getValueListTwoFields('Travel Method - HS');
	} else {
		$methodOfTravelValues = $layout->getValueListTwoFields('Travel Method');
	}
}
?>
<!-- ##################### -->