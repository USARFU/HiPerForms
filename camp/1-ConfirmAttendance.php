<!-- Error Codes 201-202 -->

<script src="/include/script/jsignature/jSignature.min.js"></script>
<script src="/include/script/jsignature/jSignature.CompressorBase30.js"></script>
<script src="/include/script/jsignature/jSignature.UndoButton.js"></script>

<?php
// Get form options and data //
$inviteCutOff = $campRecord->getField('inviteCutOff');
$doSignatureFields = ($campRecord->getField('SignatureFields_flag' == 1) ? True : False);
$doBorderCrossingSignatureField = ($campRecord->getField('BorderCrossingSignatureField_flag' == 1) ? True : False);
$includeTravelMethod = $campRecord->getField('wf_invite_TravelMethod');
$includeGrant = $campRecord->getField('wf_invite_Grant');
?>

<div class="header background">
	<?php echo "This tab expires on $inviteCutOff"; ?>
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

<form id="mainForm" action="formHandler-ConfirmAttendance.php" method="post">
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
		
		<div class="hidden" id="attendanceYesDiv">
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
				if (empty($methodOfTravel) and $inviteStatus == "Yes" and $includeTravelMethod == "Mandatory") {
					echo 'class="missing"';
				}
				?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($methodOfTravelValues as $value) {
						echo "<option value='" . $value . "' " . ($methodOfTravel == $value ? "selected='selected'>" : ">") . $value . "</option>";
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
		</div>

		<div class="input hidden" id="attendanceNoDiv">
			<label for="Reason">If not attending the Event, state the reason.</label>
			<input name="reasonForNotAttending" type="text" size="70" id="Reason"
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