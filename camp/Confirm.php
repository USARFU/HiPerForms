<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Rugby Camp Confirmation</title>

	<!-- Error Codes 201-202 -->

	<script src="../include/script/jquery/jquery.min.js"></script>
	<script src="../include/script/jsignature/jSignature.min.js"></script>
	<script src="../include/script/jsignature/jSignature.CompressorBase30.js"></script>
	<script src="../include/script/jsignature/jSignature.UndoButton.js"></script>

	<?php
	include_once "header.php";

	// Get form options and data //
	$pageHeader = (empty($campRecord->getField('WebFormInviteTitle')) ? "USA Rugby Camp Attendance Confirmation" : $campRecord->getField('WebFormInviteTitle'));
	$inviteCutOff = $campRecord->getField('inviteCutOff');
	$playerLevel = $campRecord->getField('playerLevel');
	$includeProfile = ($campRecord->getField('includeProfileForm') != 1 ? 0 : 1);
	$includeCCPayment = ($campRecord->getField('includeCCPaymentForm') != 1 ? 0 : 1);
	$SignatureOption = $campRecord->getField('SignatureFieldOption');
	$includeTravelMethod = $campRecord->getField('wf_invite_TravelMethod');
	$includeGrant = $campRecord->getField('wf_invite_Grant');
	$SubmitTitle = ($includeProfile == 1 || $includeCCPayment == 1 ? "Next" : "Submit");
	$AdminEmailInvite = $campRecord->getField('AdminEmailUponInviteChange_flag');

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
			
			// E-mail camp admin of change, if enabled
			if ($AdminEmailInvite == 1){
				$ID_EventPersonnel = $record->getField('ID');
				$params = "Invite|" . $ID_EventPersonnel;
				$newPerformScript = $fm->newPerformScriptCommand('PHP-EventInvite', 'eMail Camp Admin Player Update', $params);
				$scriptResult = $newPerformScript->execute();
				if (FileMaker::isError($scriptResult)) {
					echo "<p>Error: " . $scriptResult->getMessage() . "</p>";
//					die();
				}
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
</head>

<body>
<div class="header background">
	<h1><?php echo $pageHeader; ?></h1>
	<table class="tableHeaderTwo">
		<tr>
			<td style="width: 18%">Your Name:</td>
			<td style="width: 39%"><?php echo $name; ?></td>
			<td style="width: 18%">Date of Event:</td>
			<td style="width: 25%"><?php echo $dateStarted; ?></td>
		</tr>
		<tr>
			<td>Event Name:</td>
			<td><?php echo $campName; ?></td>
			<td>Cut-off Date:</td>
			<td><?php echo $inviteCutOff; ?></td>
		</tr>
		<tr>
			<td>Venue:</td>
			<td><?php echo $venueName; ?></td>
			<td>Event Fee:</td>
			<td>$<?php echo $fee; ?></td>
		</tr>
	</table>
</div>
<!-- Show messages instead of form. -->
<?php
if (isset($message)) {
	echo '<br />'
		. '<h3>' . $message . '</h3></div></div></body></html>';
	die();
}
?>
<!-- Add table to display any error messages with submitted form. -->
<?php
if (isset($fail)) {
	echo '<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
                     <tr><td>Sorry, the following errors were found in your form: 
                        <p style="color: red"><i>' . $fail . '</i></p>
                     </td></tr>
                 </table>';
}
?>
</div> <!-- Ends <div style="text-align: center"> from header.php -->

<form id="mainForm" action="Confirm.php" method="post">
	<fieldset class="group">
		<legend>Attendance</legend>

		<div class="input" style="border-top: none;">
			<label for="attendance">Will You Be Attending?*</label>
			<div class="rightcolumn <?php if (empty($inviteStatus)) {
				echo ' missing';
			} ?>">
				<input name="inviteStatus" type="radio" value="Yes" id="attendanceYes" class="radio"
						 title="Yes, you will be attending this event."
					<?php if (!empty($inviteStatus) and $inviteStatus == "Yes") {
						echo 'checked="checked"';
					} ?> />
				<label for="attendanceYes" class="radio">Yes</label>
				<input name="inviteStatus" type="radio" value="No" id="attendanceNo" class="radio"
						 title="No, you will not be attending this event."
					<?php if (!empty($inviteStatus) and $inviteStatus == "No") {
						echo 'checked="checked"';
					} ?> />
				<label for="attendanceNo" class="radio">No</label>
			</div>
			
		</div>

		<?php
		if ($includeTravelMethod != "Hidden") {
			?>
			<div class="input">
				<label for="TravelMethod">If 'Yes': Select Method of Travel
					<?php if ($includeTravelMethod == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<select name="methodOfTravel" size="1" id="TravelMethod"
						  title="How will you be getting to this event?" <?php
				if (empty($methodOfTravel)) {
					$methodOfTravel_a = " ";
				} else {
					$methodOfTravel_a = $methodOfTravel;
				}
				if (empty($methodOfTravel) and $inviteStatus == "Yes" and $includeTravelMethod == "Mandatory") {
					echo 'class="missing"';
				}
				?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($methodOfTravelValues as $value) {
						echo "<option value=\"" . $value . "\"" . ($methodOfTravel_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
			<?php
		}
		?>

		<?php
		if ($includeGrant != "Hidden") {
			?>
			<div class="input">
				<label for="Grant">If 'Yes': Applying for a Grant?</label>
				<input type='checkbox' class='checkbox' id="Grant" name='feePayMethod'
						 title="Check this to apply for a Grant/Scholarship."
						 value='Scholarship' <?php if ($feePayMethod == "Scholarship") {
					echo 'checked="checked"';
				} ?> />
			</div>
			<?php
		}
		?>

		<div class="input">
			<label for="Reason">If not attending the Event, state the reason.</label>
			<input name="reasonForNotAttending" type="text" size="74" id="Reason"
					 title="If you won't be able to attend this event, please give the coach a reason why."
				<?php
				if ($inviteStatus == "Yes") {
					recallText((empty($reasonForNotAttending) ? "" : $reasonForNotAttending), "no");
				}
				if ($inviteStatus == "No") {
					recallText((empty($reasonForNotAttending) ? "" : $reasonForNotAttending), "yes");
				}
				?> />
		</div>
	</fieldset>

	<!-- Show Signature Fields if valid -->
	<?php

	if ($SignatureOption == "All Players") {
		include("../include/parentSignatures.php");
	} else if ($SignatureOption == "U18 Players" && $U18AtStartOfEvent == 1) {
		include("../include/parentSignatures.php");
	} else {
		echo '
				<div ';
		if (empty($waiver)) {
			echo 'class="missing" style="padding: 1em"';
		}
		echo ' >
						<input type="checkbox" name="waiver" value="1" class="radio" id="waiver" ';
		if ($waiver == 1) {
			echo 'checked="checked"';
		}
		echo ' /><label for="waiver" class="radio">
							I accept responsibility that the information provided on this form is accurate.</label>
					</div>
					<hr />';
	}

	if (empty($IDType)) {
		## Begin HTML block for EventPersonnel IDs ##################################################################
		?>

		<p>
			<input name="respondent_exists" type="hidden" value="true"/>
			<input name="ID" type="hidden" value="<?php echo $ID; ?>"/>
			<input type="submit" name="submit" value="<?php echo $SubmitTitle; ?>" class="submit" onclick="setValue();"/>
		</p>

		</div> <!-- Container div that does 90% centered margin -->

		<?php
	}
	## End HTML block for EventPersonnel IDs ##########################################################################
	?>

	<!-- The B30 values are used to determine nullness, as the Base64 data still returns value with no signature -->
	<input id="signatureConsent" name="signatureConsent" type="hidden" value=""/>
	<input id="signatureConsentB30" name="signatureConsentB30" type="hidden" value=""/>
	<input id="signatureMedical" name="signatureMedical" type="hidden" value=""/>
	<input id="signatureMedicalB30" name="signatureMedicalB30" type="hidden" value=""/>
	<input id="sigConductPlayer" name="sigConductPlayer" type="hidden" value=""/>
	<input id="sigConductPlayerB30" name="sigConductPlayerB30" type="hidden" value=""/>
	<input id="sigConductParent" name="sigConductParent" type="hidden" value=""/>
	<input id="sigConductParentB30" name="sigConductParentB30" type="hidden" value=""/>
	<input id="sigMediaRelease" name="sigMediaRelease" type="hidden" value=""/>
	<input id="sigMediaReleaseB30" name="sigMediaReleaseB30" type="hidden" value=""/>

</form>

<script>
	function setValue() {
		$(document).ready(function () {
			var $sigdiv = $("#signature");
			var sigConsentData = $sigdiv.jSignature('getData');
			var sigConsentB30 = $sigdiv.jSignature('getData', 'base30');
			var sigConsentB301 = sigConsentB30[1];
			document.getElementById('signatureConsentB30').value = sigConsentB301;
			// Don't overwrite existing value with null signature
			if (sigConsentB301.length > 25) {
				document.getElementById('signatureConsent').value = sigConsentData;
			} else {
				// Pass on existing data is applicable
				var sigConsentPOST = <?php echo json_encode($_POST['signatureConsent']); ?>;
				if (sigConsentPOST.length > 25) {
					document.getElementById('signatureConsent').value = sigConsentPOST;
				}
			}

			var $sigdiv2 = $("#signature2");
			var sigMedicalData = $sigdiv2.jSignature('getData');
			var sigMedicalB30 = $sigdiv2.jSignature('getData', 'base30');
			var sigMedicalB301 = sigMedicalB30[1];
			document.getElementById('signatureMedicalB30').value = sigMedicalB301;
			if (sigMedicalB301.length > 25) {
				document.getElementById('signatureMedical').value = sigMedicalData;
			} else {
				// Pass on existing data is applicable
				var sigMedicalPOST = <?php echo json_encode($_POST['signatureMedical']); ?>;
				if (sigMedicalPOST.length > 25) {
					document.getElementById('signatureMedical').value = sigMedicalPOST;
				}
			}

			var $sigdiv3 = $("#signature3");
			var sigConductPlayerData = $sigdiv3.jSignature('getData');
			var sigConductPlayerB30 = $sigdiv3.jSignature('getData', 'base30');
			var sigConductPlayerB301 = sigConductPlayerB30[1];
			//console.log(sigConductPlayerB301);
			document.getElementById('sigConductPlayerB30').value = sigConductPlayerB301;
			if (sigConductPlayerB301.length > 25) {
				document.getElementById('sigConductPlayer').value = sigConductPlayerData;
				//console.log(sigConductPlayerData);
			} else {
				// Pass on existing data is applicable
				var sigConductPlayerPOST = <?php echo json_encode($_POST['sigConductPlayer']); ?>;
				if (sigConductPlayerPOST.length > 25) {
					document.getElementById('sigConductPlayer').value = sigConductPlayerPOST;
				}
			}

			var $sigdiv4 = $("#signature4");
			var sigConductParentData = $sigdiv4.jSignature('getData');
			var sigConductParentB30 = $sigdiv4.jSignature('getData', 'base30');
			var sigConductParentB301 = sigConductParentB30[1];
			document.getElementById('sigConductParentB30').value = sigConductParentB301;
			if (sigConductParentB301.length > 25) {
				document.getElementById('sigConductParent').value = sigConductParentData;
			} else {
				// Pass on existing data is applicable
				var sigConductParentPOST = <?php echo json_encode($_POST['sigConductParent']); ?>;
				if (sigConductParentPOST.length > 25) {
					document.getElementById('sigConductParent').value = sigConductParentPOST;
				}
			}

			var $sigdiv5 = $("#signature5");
			var sigMediaReleaseData = $sigdiv5.jSignature('getData');
			var sigMediaReleaseB30 = $sigdiv5.jSignature('getData', 'base30');
			var sigMediaReleaseB301 = sigMediaReleaseB30[1];
			document.getElementById('sigMediaReleaseB30').value = sigMediaReleaseB301;
			if (sigMediaReleaseB301.length > 25) {
				document.getElementById('sigMediaRelease').value = sigMediaReleaseData;
			} else {
				// Pass on existing data is applicable
				var sigMediaReleasePOST = <?php echo json_encode($_POST['sigMediaRelease']); ?>;
				if (sigMediaReleasePOST.length > 25) {
					document.getElementById('sigMediaRelease').value = sigMediaReleasePOST;
				}
			}
		});
	}
</script>

</body>
</html>