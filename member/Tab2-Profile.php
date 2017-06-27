<?php
/**
 * Created by PhpStorm.
 * User: aewerdt
 * Date: 1/19/2017
 * Time: 9:46 AM
 */

//<!-- Show messages.                    -->
if (isset($message_profile)) {
	echo '<h3>' . $message_profile . '</h3>';
} else {
	?>

	<div class="row-divider"
		<?php if ($ProfileStatus == "orange") {
			echo 'style="border:2px solid darkorange; background-color: rgba(255,165,0,.5)"';
		} elseif ($ProfileStatus == "red" || $ProfileStatus == "black") {
			echo 'style="border:2px solid red; background-color: rgba(255,99,71,.5)"';
		} else {
			echo 'style="border:2px solid green; background-color: rgba(144,238,144,.5)"';
		}
		?> >
		<div class="title small icon black entypo-back-in-time" style="color: black">
			<?php if ($ProfileStatus == "black") {
				echo 'Please update your profile before accessing other tabs.';
			} else { ?>
				You last updated your profile <span
						style="font-style: italic; font-weight: bold"><?php echo $DaysSinceSuccessfulProfileVerification; ?></span> days ago.
				<?php if ($ProfileStatus == "orange") {
					echo '<br/>Please review and update any missing data.';
				} elseif ($ProfileStatus == "red") {
					echo '<br/>Your profile is out of date. Please review and update your data before continuing.';
				}
			}
			?>
		</div>
	</div>

<?php } ?>
<!-- ################################# -->


<div class="mt-10"></div>

<!-- Add table to display any error messages from submitted form. -->
<?php if (!empty($fail) && !empty($_POST['submitted-profile'])) { ?>
	<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
		<tr>
			<td>Your Profile could not be updated due to the following problems:
				<p style="color: red"><i>
						<?php echo $fail; ?>
					</i></p>
			</td>
		</tr>
	</table>
<?php } ?>
<!-- ################################# -->

<h5>Form Notes</h5>
<ul style="width: 90%;">
	<li>Required Fields: If the form is submitted and any required fields are in error, the fields in error will be
		indicated in red.
	</li>
	<li>Date Fields: All dates must be entered in the mm/dd/yyyy or yyyy-mm-dd format.</li>
	<li>For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</li>
	<li>For questions regarding the data itself, or to request changes to read-only fields, contact <a
				href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.
	</li>
</ul>

<div class="mt-10"></div>

<form id="mainForm" action="body.php<?php if ($EditingMemberProfile) { echo "?ID=" . $ID_Personnel; }?>" method="post" enctype="multipart/form-data">

	<!-- Demographic                        -->
	<fieldset class="group" id="anchor-demographic">
		<legend>
			&nbsp;<a href="#anchor-demographic">Demographic</a><span class="legend-links">&nbsp;-&nbsp;
							<a href="#anchor-contacts">Contacts</a> - <a href="#anchor-rugby">Rugby</a> -
							<a href="#anchor-medical">Medical</a> - <a href="#anchor-travel">Travel</a> -
							<a href="#anchor-education">Education</a></span>&nbsp;
		</legend>

		<div class="input" style="border-top: none;">
			<label for="crop-avatar" id="FacePhoto_Button">Select the box to the right to upload a new photo. <img
						src="../include/info.PNG" height="16"> <span
						class="<?php if (empty($Photo64) && empty($FacePhotoCropPath)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						} ?>">REQUIRED</span></label>
			<div id="FacePhoto_Dialog" title="Face Photo" class="hidden">
				<p>This is a head-and-shoulders photo used for identification purposes by coaches and scouts.</p>
				<div>
					<label for="Good1">Good Example:</label>
					<img src="../include/GoodFacePhoto1.JPG" alt="Good Example" id="Good1">
				</div>
				<div>
					<label for="Bad1">Bad Example 1:</label>
					<img src="../include/BadFacePhoto1.JPG" alt="Bad Example" id="Bad1">
				</div>
				<div>
					<label for="Bad2">Bad Example 2:</label>
					<img src="../include/BadFacePhoto2.JPG" alt="Bad Example" id="Bad2">
				</div>
			</div>

			<div class="container rightcolumn" id="crop-avatar">

				<!-- Current avatar -->
				<div class="avatar-view" id="avatar-view" title="Change your face photo">
					<img src="<?php echo $FacePhotoEditor; ?>" alt="Face Photo">
				</div>

			</div>
		</div>

		<div class="input">
			<label for="Birthplace">Birthplace</label>
			<div class="rightcolumn">
				<input name="BirthplaceCity" type="text" size="20" id="Birthplace" placeholder="City" style="margin-right: 1em"
						 title="Your birthplace city. Used by announcers." <?php recallText((empty($Birthplace_City) ? "" : $Birthplace_City), "no"); ?> />
				<select name="BirthplaceState" size="1" id="Birthplace" style="margin-right: 1em"
						  title="State or Canadian Province of your birth.">
					<option value="" disabled selected>State</option>
					<?php
					foreach ($stateValues as $value) {
						echo "<option value='" . $value . "'" . ($Birthplace_State == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>

				<select name="BirthplaceCountry" size="1" id="Country" title="Country you were born in."
						  class="CountryBirthplace select2">
					<option value="" disabled selected>Country</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "'" . ($Birthplace_Country == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
		</div>

		<div class="input">
			<label for="Bio" id="Bio_Button">Rugby Career Bio <img src="../include/info.PNG" height="16"></label>
			<div id="Bio_Dialog" title="Bio">
				<p>Many of you will be participating in rugby matches which will be webcast or televised.
					Please write something short about your rugby history that would be helpful to the match announcers when they are
					discussing your play. Example:</p>
				<p style="font-style: italic">"Tom originally learned how to play rugby as a freshman at Princeton University in the
					late
					sixties.
					He founded the Princeton Athletic Club in 1974 and became its first captain.
					Tom later played for the Chicago Lions and then the USA Owls as an Old Boy in the Golden Oldies tournaments. Tom
					lives in
					Jupiter, FL with his wife Jane."</p>
			</div>
			<div class="rightcolumn">
					<textarea style="width: 99%;" form="mainForm" rows="5" maxlength="1500" name="Bio"
								 id="Bio"><?php echo $Bio; ?></textarea>
			</div>
		</div>
	</fieldset>
	<!-- ################################# -->

	<!-- Contacts                           -->
	<fieldset class="group" id="anchor-contacts">
		<legend>
			&nbsp;<span class="legend-links"><a href="#anchor-demographic">Demographic</a> - </span>
			<a href="#anchor-contacts">Contacts</a>
			<span class="legend-links">&nbsp;-&nbsp;<a href="#anchor-rugby">Rugby</a> -
			<a href="#anchor-medical">Medical</a> -
			<a href="#anchor-travel">Travel</a> -
			<a href="#anchor-education">Education</a></span>&nbsp;
		</legend>

		<div class="input" style="border-top: none;">
			<label for="emergency">Emergency Contact <span
						class="<?php if (empty($emergencyContactFirstName) || empty($emergencyContactLastName) || empty($emergencyContactNumber) || empty($emergencyContactRelationship)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						} ?>">REQUIRED</span></label>
			<div class="rightcolumn">
				<fieldset class="field">
					<legend>First Name</legend>
					<input name="emergencyContactFirstName" type="text" size="25" id="EmergencyContact"
							 title="First Name of your emergency contact."
						<?php recallText((empty($emergencyContactFirstName) ? "" : $emergencyContactFirstName), "yes"); ?> />
				</fieldset>
				<fieldset class="field" style="margin-right: .5em;">
					<legend>Last Name</legend>
					<input name="emergencyContactLastName" type="text" size="25"
							 title="Last Name of your emergency contact."
						<?php recallText((empty($emergencyContactLastName) ? "" : $emergencyContactLastName), "yes"); ?> />
				</fieldset>
				<div class="row">
					<fieldset class="field">
						<legend>Phone</legend>
						<input name="emergencyContactNumber" type="text" size="25"
								 title="Phone number of your emergency contact."
							<?php recallText((empty($emergencyContactNumber) ? "" : $emergencyContactNumber), "yes"); ?> />
					</fieldset>
					<fieldset class="field">
						<legend>Relationship</legend>
						<select name="emergencyContactRelationship" size="1"
								  title="Relationship to your emergency contact."
							<?php if (empty($emergencyContactRelationship)) {
								echo 'class="missing"';
							} ?> >
							<option value="">&nbsp;</option>
							<?php
							foreach ($relationshipValues as $value) {
								echo "<option value=\"" . $value . "\"" . ($emergencyContactRelationship == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
							}
							?>
						</select>
					</fieldset>
				</div>
			</div>
		</div>
		
		<?php if ($U18 && $IsPlayer) { ?>
			<div class="input">
				<label for="Parent">Parent / Guardian 1 <span
							class="<?php if (empty($Guardian1FirstName) || empty($Guardian1LastName) || empty($Guardian1Cell) || empty($Guardian1eMail) || empty($Guardian1Type)) {
								echo "mandatoryFailed";
							} else {
								echo "mandatory";
							} ?>">REQUIRED</span>
				</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Type</legend>
							<select name="Guardian1Type" size="1" id="Parent"
								<?php
								if (empty($Guardian1Type)) {
									echo 'class="missing"';
								}
								?>>
								<option value="">&nbsp;</option>
								<?php
								foreach ($guardianValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($Guardian1Type == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?>
							</select>
						</fieldset>
						<fieldset class="field">
							<legend>First</legend>
							<input name="Guardian1FirstName" type="text" size="16"
									 title="First name of your first guardian."
								<?php recallText((empty($Guardian1FirstName) ? "" : $Guardian1FirstName), "yes"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="Guardian1LastName" type="text" size="16"
									 title="Last name of your first guardian."
								<?php recallText((empty($Guardian1LastName) ? "" : $Guardian1LastName), "yes"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="Guardian1Cell" type="text" size="16"
									 title="Phone number of your first guardian."
								<?php recallText((empty($Guardian1Cell) ? "" : $Guardian1Cell), "yes"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="Guardian1eMail" type="text" size="32"
									 title="E-mail of your first guardian."
								<?php recallText((empty($Guardian1eMail) ? "" : $Guardian1eMail), "yes"); ?> />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Parent2">Parent / Guardian 2</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Type</legend>
							<select name="Guardian2Type" size="1" id="Parent2">
								<option value="">&nbsp;</option>
								<?php
								foreach ($guardianValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($Guardian2Type == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?>
							</select>
						</fieldset>
						<fieldset class="field">
							<legend>First</legend>
							<input name="Guardian2FirstName" type="text" size="16"
									 title="First name of your second guardian."
								<?php recallText((empty($Guardian2FirstName) ? "" : $Guardian2FirstName), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="Guardian2LastName" type="text" size="16"
									 title="Last name of your second guardian."
								<?php recallText((empty($Guardian2LastName) ? "" : $Guardian2LastName), "no"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="Guardian2Cell" type="text" size="16"
									 title="Phone number of your second guardian."
								<?php recallText((empty($Guardian2Cell) ? "" : $Guardian2Cell), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="Guardian2eMail" type="text" size="32"
									 title="E-mail of your second guardian."
								<?php recallText((empty($Guardian2eMail) ? "" : $Guardian2eMail), "no"); ?> />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Parent3">Parent / Guardian 3</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Type</legend>
							<select name="Guardian3Type" size="1" id="Parent3">
								<option value="">&nbsp;</option>
								<?php
								foreach ($guardianValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($Guardian3Type == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?>
							</select>
						</fieldset>
						<fieldset class="field">
							<legend>First</legend>
							<input name="Guardian3FirstName" type="text" size="16"
									 title="First name of your third guardian."
								<?php recallText((empty($Guardian3FirstName) ? "" : $Guardian3FirstName), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="Guardian3LastName" type="text" size="16"
									 title="Last name of your third guardian."
								<?php recallText((empty($Guardian3LastName) ? "" : $Guardian3LastName), "no"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="Guardian3Cell" type="text" size="16"
									 title="Phone number of your third guardian."
								<?php recallText((empty($Guardian3Cell) ? "" : $Guardian3Cell), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="Guardian3eMail" type="text" size="32"
									 title="E-mail of your third guardian."
								<?php recallText((empty($Guardian3eMail) ? "" : $Guardian3eMail), "no"); ?> />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Parent4">Parent / Guardian 4</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Type</legend>
							<select name="Guardian4Type" size="1" id="Parent4">
								<option value="">&nbsp;</option>
								<?php
								foreach ($guardianValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($Guardian4Type == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?>
							</select>
						</fieldset>
						<fieldset class="field">
							<legend>First</legend>
							<input name="Guardian4FirstName" type="text" size="16"
									 title="First Name of your fourth guardian."
								<?php recallText((empty($Guardian4FirstName) ? "" : $Guardian4FirstName), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="Guardian4LastName" type="text" size="16"
									 title="Last Name of your fourth guardian."
								<?php recallText((empty($Guardian4LastName) ? "" : $Guardian4LastName), "no"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="Guardian4Cell" type="text" size="16"
									 title="Phone number of your fourth guardian."
								<?php recallText((empty($Guardian4Cell) ? "" : $Guardian4Cell), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="Guardian4eMail" type="text" size="32"
									 title="E-Mail address of your fourth guardian."
								<?php recallText((empty($Guardian4eMail) ? "" : $Guardian4eMail), "no"); ?> />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Reference1">Reference 1</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Type</legend>
							<select name="referenceType1" size="1" title="Type or relation of reference">
								<option value=""></option>
								<?php
								foreach ($referenceTypeValues as $referenceTypeValue) {
									echo '<option value="' . $referenceTypeValue . '"' . ($referenceType1 == $referenceTypeValue ? 'selected="selected">' : '>') . $referenceTypeValue . '</option>';
								}
								?>
							</select>
						</fieldset>
						<fieldset class="field">
							<legend>First</legend>
							<input name="referenceFirstName1" type="text" size="16" id="Reference1"
									 title="First name of your first reference."
								<?php recallText((empty($referenceFirstName1) ? "" : $referenceFirstName1), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="referenceLastName1" type="text" size="16"
									 title="Last name of your first reference."
								<?php recallText((empty($referenceLastName1) ? "" : $referenceLastName1), "no"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="referencePhone1" type="text" size="16"
									 title="Phone number of your first reference."
								<?php recallText((empty($referencePhone1) ? "" : $referencePhone1), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="referenceEmail1" type="text" size="40"
									 title="E-mail of your first reference."
								<?php recallText((empty($referenceEmail1) ? "" : $referenceEmail1), "no"); ?> />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Reference2">Reference 2</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Type</legend>
							<select name="referenceType2" size="1" title="Type or relation of reference">
								<option value=""></option>
								<?php
								foreach ($referenceTypeValues as $referenceTypeValue) {
									echo '<option value="' . $referenceTypeValue . '"' . ($referenceType2 == $referenceTypeValue ? 'selected="selected">' : '>') . $referenceTypeValue . '</option>';
								}
								?>
							</select>
						</fieldset>
						<fieldset class="field">
							<legend>First</legend>
							<input name="referenceFirstName2" type="text" size="16" id="Reference2"
									 title="First name of your first reference."
								<?php recallText((empty($referenceFirstName2) ? "" : $referenceFirstName2), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="referenceLastName2" type="text" size="16"
									 title="Last name of your first reference."
								<?php recallText((empty($referenceLastName2) ? "" : $referenceLastName2), "no"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="referencePhone2" type="text" size="16"
									 title="Phone number of your first reference."
								<?php recallText((empty($referencePhone2) ? "" : $referencePhone2), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="referenceEmail2" type="text" size="40"
									 title="E-mail of your first reference."
								<?php recallText((empty($referenceEmail2) ? "" : $referenceEmail2), "no"); ?> />
						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Reference3">Reference 3</label>
				<div class="rightcolumn">
					<fieldset class="field">
						<legend>Type</legend>
						<select name="referenceType3" size="1" title="Type or relation of reference">
							<option value=""></option>
							<?php
							foreach ($referenceTypeValues as $referenceTypeValue) {
								echo '<option value="' . $referenceTypeValue . '"' . ($referenceType3 == $referenceTypeValue ? 'selected="selected">' : '>') . $referenceTypeValue . '</option>';
							}
							?>
						</select>
					</fieldset>
					<div class="row">
						<fieldset class="field">
							<legend>First</legend>
							<input name="referenceFirstName3" type="text" size="16" id="Reference3"
									 title="First name of your first reference."
								<?php recallText((empty($referenceFirstName3) ? "" : $referenceFirstName3), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Last</legend>
							<input name="referenceLastName3" type="text" size="16"
									 title="Last name of your first reference."
								<?php recallText((empty($referenceLastName3) ? "" : $referenceLastName3), "no"); ?> />
						</fieldset>
					</div>
					<div class="row">
						<fieldset class="field">
							<legend>Phone</legend>
							<input name="referencePhone3" type="text" size="16"
									 title="Phone number of your first reference."
								<?php recallText((empty($referencePhone3) ? "" : $referencePhone3), "no"); ?> />
						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>E-Mail</legend>
							<input name="referenceEmail3" type="text" size="40"
									 title="E-mail of your first reference."
								<?php recallText((empty($referenceEmail3) ? "" : $referenceEmail3), "no"); ?> />
						</fieldset>
					</div>
				</div>
			</div>
		
		<?php } else { ?>

			<div class="input">
				<label for="Spouse">Spouse / Partner</label>
				<div class="rightcolumn">
					<input name="spouseName" type="text" size="16" id="Spouse" placeholder="Name"
							 style="margin-right: 2em"
						<?php recallText((empty($spouseName) ? "" : $spouseName), "no"); ?> />
					<input name="spouseCell" type="tel" size="16" placeholder="Cell Number" style="margin-right: 2em"
						<?php recallText((empty($spouseCell) ? "" : $spouseCell), "no"); ?> />
					<input name="spouseEmail" type="email" size="40" placeholder="E-Mail"
						<?php recallText((empty($spouseEmail) ? "" : $spouseEmail), "no"); ?> />
				</div>
			</div>
		<?php } ?>

	</fieldset>

	<!-- Rugby                           -->
	<fieldset class="group" id="anchor-rugby">
		<legend>
		&nbsp;	&nbsp;<span class="legend-links"><a href="#anchor-demographic">Demographic</a> -
			<a href="#anchor-contacts">Contacts</a>&nbsp;-&nbsp;</span>
			<a href="#anchor-rugby">Rugby</a><span class="legend-links">&nbsp;-&nbsp;
			<a href="#anchor-medical">Medical</a> -
			<a href="#anchor-travel">Travel</a> -
			<a href="#anchor-education">Education</a></span>&nbsp;
		</legend>

		<div class="input" style="border-top: none;">
			<label for="Kit">Clothing Sizes</label>
			<div class="rightcolumn">
				<fieldset class="field" style="width: 8em;">
					<legend>Match Jersey</legend>
					<select name="MatchJerseySize" size="1"
							  id="Kit">
						<option value="">&nbsp;</option>
						<?php
						foreach ($clothingSizeValues as $value) {
							echo "<option value=\"" . $value . "\"" . ($MatchJerseySize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
						}
						?>
					</select>
				</fieldset>
				<fieldset class="field" style="width: 10em;">
					<legend>Match Shorts</legend>
					<select name="MatchShortsSize" size="1"
							  id="Kit">
						<option value="">&nbsp;</option>
						<?php
						foreach ($clothingSizeValues as $value) {
							echo "<option value=\"" . $value . "\"" . ($MatchShortsSize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
						}
						?>
					</select>
				</fieldset>
				<fieldset class="field" style="width: 8em;">
					<legend>T-Shirt</legend>
					<select name="tShirtSize" size="1"
							  id="Kit">
						<option value="">&nbsp;</option>
						<?php
						foreach ($clothingSizeValues as $value) {
							echo "<option value=\"" . $value . "\"" . ($tShirtSize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
						}
						?>
					</select>
				</fieldset>
				<fieldset class="field" style="margin-right: .5em;">
					<legend>Polo</legend>
					<select name="poloSize" size="1"
							  id="Kit">
						<option value="">&nbsp;</option>
						<?php
						foreach ($clothingSizeValues as $value) {
							echo "<option value=\"" . $value . "\"" . ($poloSize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
						}
						?>
					</select>
				</fieldset>
				<div class="row">
					<fieldset class="field" style="width: 8em;">
						<legend>Gym Shorts</legend>
						<select name="shortsSize" size="1"
								  id="Kit">
							<option value="">&nbsp;</option>
							<?php
							foreach ($clothingSizeValues as $value) {
								echo "<option value=\"" . $value . "\"" . ($shortsSize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
							}
							?>
						</select>
					</fieldset>
					<fieldset class="field" style="width: 10em;">
						<legend>Track Suit Bottom</legend>
						<select name="trackSuitBottomSize" size="1"
								  id="Kit">
							<option value="">&nbsp;</option>
							<?php
							foreach ($clothingSizeValues as $value) {
								echo "<option value=\"" . $value . "\"" . ($trackSuitBottomSize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
							}
							?>
						</select>
					</fieldset>
					<fieldset class="field" style="width: 8em;">
						<legend>Track Suit Top</legend>
						<select name="trackSuitTopSize" size="1"
								  id="Kit">
							<option value="">&nbsp;</option>
							<?php
							foreach ($clothingSizeValues as $value) {
								echo "<option value=\"" . $value . "\"" . ($trackSuitTopSize == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
							}
							?>
						</select>
					</fieldset>
					<fieldset class="field" style="margin-right: .5em;">
						<legend>Shoes</legend>
						<input name="shoeSize" size="4" type="text"
								 title="Shoe Size" <?php recallText($shoeSize, "no"); ?> />
					</fieldset>
				</div>
			</div>
		</div>
		
		<?php
		if ($IsPlayer) {
			?>

			<div class="input">
				<label for="YearStarted">Year Started Playing Rugby</label>
				<input name="yearStartedPlaying" type="text" size="6" id="YearStarted"
						 title="The year you started playing rugby." <?php recallText((empty($yearStartedPlaying) ? "" : $yearStartedPlaying), "no"); ?> />
			</div>

			<div class="input">
				<label for="DominantHand">Dominant Hand</label>
				<select name="dominantHand" size="1" id="DominantHand" title="Your dominant hand.">
					<option value="">&nbsp;</option>
					<option value="Left" <?php if (!empty($dominantHand) and $dominantHand == "Left") {
						echo 'selected="selected"';
					} ?> >Left
					</option>
					<option value="Right" <?php if (!empty($dominantHand) and $dominantHand == "Right") {
						echo 'selected="selected"';
					} ?> >Right
					</option>
					<option value="Both" <?php if (!empty($dominantHand) and $dominantHand == "Both") {
						echo 'selected="selected"';
					} ?> >Both
					</option>
				</select>
			</div>

			<div class="input">
				<label for="DominantFoot">Dominant Foot</label>
				<select name="dominantFoot" size="1" id="DominantFoot" title="Your dominant foot.">
					<option value="">&nbsp;</option>
					<option value="Left" <?php if (!empty($dominantFoot) and $dominantFoot == "Left") {
						echo 'selected="selected"';
					} ?> >Left
					</option>
					<option value="Right" <?php if (!empty($dominantFoot) and $dominantFoot == "Right") {
						echo 'selected="selected"';
					} ?> >Right
					</option>
					<option value="Both" <?php if (!empty($dominantFoot) and $dominantFoot == "Both") {
						echo 'selected="selected"';
					} ?> >Both
					</option>
				</select>
			</div>

			<div class="input">
				<label for="15sPosition">15s Position</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Primary</legend>

							<select name="primary15sPosition" size="1" id="15sPosition">
								<option value="">&nbsp;</option>
								<?php
								foreach ($fifteensValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($primary15sPosition == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?></select>

						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Secondary</legend>

							<select name="secondary15sPosition" size="1" id="15sPosition">
								<option value="">&nbsp;</option>
								<?php
								foreach ($fifteensValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($secondary15sPosition == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?></select>

						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="7sPosition">7s Position</label>
				<div class="rightcolumn">
					<div class="row">
						<fieldset class="field">
							<legend>Primary</legend>

							<select name="primary7sPosition" size="1" id="7sPosition">
								<option value="">&nbsp;</option>
								<?php
								foreach ($sevensValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($primary7sPosition == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?></select>

						</fieldset>
						<fieldset class="field" style="margin-right: .5em;">
							<legend>Secondary</legend>

							<select name="secondary7sPosition" size="1" id="7sPosition">
								<option value="">&nbsp;</option>
								<?php
								foreach ($sevensValues as $value) {
									echo "<option value=\"" . $value . "\"" . ($secondary7sPosition == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
								}
								?></select>

						</fieldset>
					</div>
				</div>
			</div>

			<div class="input">
				<label for="Video1">Highlight Video Link</label>
				<input name="HighlightVideoLink" type="text" size="70" id="Video1"
					<?php recallText($HighlightVideoLink, "no"); ?> />
			</div>

			<div class="input">
				<label for="Video2">Full Match Video Link 1</label>
				<input name="FullMatchLink1" type="text" size="70" id="Video2"
					<?php recallText($FullMatchLink1, "no"); ?> />
			</div>

			<div class="input">
				<label for="Video3">Full Match Video Link 2</label>
				<input name="FullMatchLink2" type="text" size="70" id="Video3"
					<?php recallText($FullMatchLink2, "no"); ?> />
			</div>

			<div class="input">
				<label for="Video4">Full Match Video Link 3</label>
				<input name="FullMatchLink3" type="text" size="70" id="Video4"
					<?php recallText($FullMatchLink3, "no"); ?> />
			</div>
			
			<?php
			} // End Player fields
		?>
		
		<?php
		if (!$IsManager) {
			?>

			<div class="input">
				<label for="crop-ProofOfDOB">Select the box to the right to attach Proof of Date of Birth
					<small>(<i>Birth Certificate or Gov. Issued ID</i>)</small>
					<?php
					if ($IsPlayer) {
						echo "<span class='";
						if (empty($ProofOfDOB64) && empty($ProofOfDOBCropPath)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						}
						echo "'>REQUIRED</span>";
					}
					?>
				</label>

				<div class="container rightcolumn" id="crop-ProofOfDOB">

					<!-- Current image -->
					<div class="avatar-view" id="dob-view" title="Change the attached image">
						<img src="<?php echo $ProofOfDOBEditor; ?>" alt="Proof of DOB">
					</div>

				</div>
			</div>
			
			<?php
		}
		?>
		
		<?php
		if ($U19 && !$IsManager) { //## Proof of School Enrollment
			?>
			<div class="input">
				<label for="crop-ProofOfSchool">Select the box to the right to attach Proof of School Enrollment</label>

				<div class="container rightcolumn" id="crop-ProofOfSchool">

					<!-- Current image -->
					<div class="avatar-view" id="school-view" title="Change the attached image">
						<img src="<?php echo $ProofOfSchoolEditor; ?>" alt="Proof of School Enrollment">
					</div>

				</div>
			</div>
		
		<?php } //## End Proof of Enrollment ?>
		
		<?php
		if ($IsPlayer) {
			?>

			<div class="input">
				<label>Measurements</label>
				<div class="rightcolumn">
					<label class="top">Add a measurement</label>

					<div class="row">
						<fieldset class="field" style="width: 14em;">
							<legend>Height</legend>
							<input name="heightFeet" type="text" id="HeightFeet" placeholder="Feet"
									 size="3" <?php recallText((empty($heightFeet) ? "" : $heightFeet), "no"); ?> />
							<input name="heightInches" type="text" id="HeightInches" placeholder="Inches"
									 size="3" <?php recallText((isset($heightInches) ? $heightInches : ""), "no"); ?> />
							<input name="heightMeters" type="text" id="HeightMeters" placeholder="Meters"
									 size="3" <?php recallText((isset($heightMeters) ? $heightMeters : ""), "no"); ?> />
							<select name="Height_UM" size="1" id="Height_UM" title="Height Unit of Measurement">
								<option value="ft" <?php if ($Height_UM != "m") {
									echo 'selected="selected"';
								} ?>>ft
								</option>
								<option value="m" <?php if ($Height_UM == "m") {
									echo 'selected="selected"';
								} ?>>m
								</option>
							</select>
						</fieldset>

						<fieldset class="field" style="width: 9em; margin-right: .5em;">
							<legend>Weight</legend>
							<input name="Weight" id="Weight" type="text" title="Your weight."
									 size="3" <?php recallText((empty($Weight) ? "" : $Weight), "no"); ?> />
							<select name="Weight_UM" id="Weight_UM" size="1" title="Weight Unit of Measurement">
								<option value="lb" <?php if ($Weight_UM != "kg") {
									echo 'selected="selected"';
								} ?>>lb
								</option>
								<option value="kg" <?php if ($Weight_UM == "kg") {
									echo 'selected="selected"';
								} ?>>kg
								</option>
							</select>
						</fieldset>
					</div>

					<div class="row">
						<fieldset class="field" style="width: 9em;">
							<legend>Wingspan</legend>
							<input name="Wingspan" type="text" id="Wingspan" title="Wingspan"
									 size="3" <?php recallText((empty($Wingspan) ? "" : $Wingspan), "no"); ?> />
							<select name="Wingspan_UM" id="Wingspan_UM" size="1" title="Wingspan Unit of Measurement">
								<option value="in" <?php if ($Wingspan_UM != "m") {
									echo 'selected="selected"';
								} ?>>in
								</option>
								<option value="m" <?php if ($Wingspan_UM == "m") {
									echo 'selected="selected"';
								} ?>>m
								</option>
							</select>
						</fieldset>

						<fieldset class="field" style="width: 9em;">
							<legend>Handspan</legend>
							<input name="Handspan" type="text" id="Handspan" title="Handspan"
									 size="3" <?php recallText((isset($Handspan) ? $Handspan : ""), "no"); ?> />
							<select name="Handspan_UM" id="Handspan_UM" size="1" title="Handspan Unit of Measurement">
								<option value="in" <?php if ($Handspan_UM != "cm") {
									echo 'selected="selected"';
								} ?>>in
								</option>
								<option value="cm" <?php if ($Handspan_UM == "cm") {
									echo 'selected="selected"';
								} ?>>cm
								</option>
							</select>
						</fieldset>

						<fieldset class="field" style="width: 9em; margin-right: .5em;">
							<legend>Standing Reach</legend>
							<input name="StandingReach" type="text" id="StandingReach" title="Standing Reach"
									 size="3" <?php recallText((empty($StandingReach) ? "" : $StandingReach), "no"); ?> />
							<select name="StandingReach_UM" id="StandingReach_UM" size="1" title="StandingReach Unit of Measurement">
								<option value="in" <?php if ($StandingReach_UM != "m") {
									echo 'selected="selected"';
								} ?>>in
								</option>
								<option value="m" <?php if ($StandingReach_UM == "m") {
									echo 'selected="selected"';
								} ?>>m
								</option>
							</select>
						</fieldset>
					</div>

				</div>
			</div>
		
		<?php
		if ($record_Measurement_latest) {
			?>

			<div class="rightcolumn">
				<label class="top">Latest Measurement <small>(For full list, select the 'History' tab.)</small>
				</label>
				<?php
				echo $latest_Measurement_date . "&nbsp;&nbsp;-&nbsp;&nbsp;Height: " . $latest_Measurement_height . "/" . $latest_Measurement_height_m . "m &nbsp; Weight: " .
					$latest_Measurement_weight_lb . "lb/" . $latest_Measurement_weight_kg . "kg &nbsp;";
				if ($latest_Measurement_wingspan_in) {
					echo " Wingspan: " . $latest_Measurement_wingspan_in . "in/" . $latest_Measurement_wingspan_m . "m &nbsp;";
				}
				if ($latest_Measurement_handspan_in) {
					echo " Handspan: " . $latest_Measurement_handspan_in . "in/" . $latest_Measurement_handspan_cm . "cm &nbsp;";
				}
				if ($latest_Measurement_standingreach_in) {
					echo " Standing Reach: " . $latest_Measurement_standingreach_in . "in/" . $latest_Measurement_standingreach_m . "m;";
				}
				?>
			</div>
		
		<?php }
		} ## /Primary Role display of Measurements, Attributes, and Performance ?>

	</fieldset>
	<!-- ####### /Rugby Fieldset ########################## -->

	<!-- Medical                                              -->
	<fieldset class="group" id="anchor-medical">
		<legend>
			&nbsp;<span class="legend-links">&nbsp;
				<a href="#anchor-demographic">Demographic</a> -
				<a href="#anchor-contacts">Contacts</a>&nbsp;-&nbsp;
				<a href="#anchor-rugby">Rugby</a>&nbsp;-&nbsp;</span>
				<a href="#anchor-medical">Medical</a>
				<span class="legend-links">&nbsp;-&nbsp;<a href="#anchor-travel">Travel</a> -
				<a href="#anchor-education">Education</a></span>&nbsp;
		</legend>

		<div class="input" style="border-top: none;">
			<label for="Insurance">Health Insurance
				<?php
				if ($IsPlayer) {
					echo "<span class='";
					if (empty($NoInsurance) && (empty($healthInsuranceCompany) || empty($healthPlanID))) {
						echo "mandatoryFailed";
					} else {
						echo "mandatory";
					}
					echo "'>REQUIRED</span>";
				}
				?>
			</label>
			<div class="rightcolumn">

				<fieldset class="field HealthInsuranceFields">
					<legend>Health Insurance Company</legend>
					<input name="healthInsuranceCompany" type="text" id="Insurance"
							 size="40" <?php recallText((empty($healthInsuranceCompany) ? "" : $healthInsuranceCompany), ($NoInsurance == "1" || !$IsPlayer ? "no" : "yes")); ?> />
				</fieldset>

				<fieldset class="field HealthInsuranceFields">
					<legend>Health Plan ID</legend>
					<input name="healthPlanID" type="text" title="Health Plan ID"
							 size="16" <?php recallText((empty($healthPlanID) ? "" : $healthPlanID), ($NoInsurance == "1" || !$IsPlayer ? "no" : "yes")); ?> />
				</fieldset>

				<fieldset class="field">
					<legend>No Insurance</legend>
					<input name="NoInsurance" type="checkbox" value="1" title="I do not have any health insurance." id="NoInsurance"
						<?php if ($NoInsurance == 1) {
							echo " checked='checked'";
						} ?> />
				</fieldset>

			</div>
		</div>

		<div class="input">
			<label for="crop-InsuranceCard">Select the box to the right to attach a scan of your Insurance Card<br/>
				<span style="font-style: italic; font-size: small">Required for International Travel</span>
			</label>

			<div class="container rightcolumn" id="crop-InsuranceCard">

				<!-- Current avatar -->
				<div class="avatar-view" id="insurance-view" title="Change your Insurance Card photo">
					<img src="<?php echo $InsuranceCardEditor; ?>" alt="Insurance Card Photo">
				</div>

			</div>
		</div>
		
		<?php if ($IsPlayer) { ?>

			<div class="input">
				<label class="top" for="Conditions">Do you have any allergies, dietary restrictions, chronic illnesses, or medical
					conditions? If yes, please describe.
					<span
							class="<?php if (empty($allergiesConditions) || ($allergiesConditions == "Yes" && empty($allergiesConditionsDescr))) {
								echo "mandatoryFailed";
							} else {
								echo "mandatory";
							} ?>">REQUIRED</span>
				</label>

				<div <?php if (empty($allergiesConditions)) {
					echo 'class="missing"';
				} ?> >
					<input name="allergiesConditions" type="radio" id="ConditionsYes" class="radio"
							 value="Yes" <?php if ($allergiesConditions == "Yes") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="ConditionsYes">Yes</label>
					<input name="allergiesConditions" class="radio" type="radio" id="ConditionsNo"
							 value="No" <?php if ($allergiesConditions == "No") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="ConditionsNo">No</label>
				</div>

				<input name="allergiesConditionsDescr" type="text" size="70" id="Conditions"
					<?php
					if ($allergiesConditions == "Yes") {
						recallText((empty($allergiesConditionsDescr) ? "" : $allergiesConditionsDescr), "yes");
					} elseif ($allergiesConditions == "No") {
						recallText((empty($allergiesConditionsDescr) ? "" : $allergiesConditionsDescr), "no");
					}
					?> />
			</div>

			<div class="input">
				<label class="top" for="Medications">Are you prescribed any medication? If yes, please explain any instructions.
					<span class="<?php if (empty($medications) || ($medications == "Yes" && empty($medicationsDescr))) {
						echo "mandatoryFailed";
					} else {
						echo "mandatory";
					} ?>">REQUIRED</span>
				</label>

				<div <?php if (empty($medications)) {
					echo 'class="missing"';
				} ?> >
					<input name="medications" type="radio" id="MedicationsYes" class="radio"
							 value="Yes" <?php if ($medications == "Yes") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="MedicationsYes">Yes</label>
					<input name="medications" class="radio" type="radio" id="MedicationsNo"
							 value="No" <?php if ($medications == "No") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="MedicationsNo">No</label>
				</div>

				<input name="medicationsDescr" type="text" size="70" id="Medications"
					<?php
					if ($medications == "Yes") {
						recallText((empty($medicationsDescr) ? "" : $medicationsDescr), "yes");
					} elseif ($medications == "No") {
						recallText((empty($medicationsDescr) ? "" : $medicationsDescr), "no");
					}
					?> />
			</div>

			<div class="input">
				<label id="BannedSubstances_Button">Banned Substances <img src="../include/info.PNG" height="16"></label>

				<div id="BannedSubstances_Dialog" title="Banned Substances">
					<p>More information on banned substances can be found <a
								href=http://rugbyready.worldrugby.org/index.php?section=62_3&language=en">here</a>.</p>
				</div>

				<div class="rightcolumn">
					<input name="TakingBannedSubstance" type="checkbox" value="1" id="BannedSubstance"
							 class="radio" <?php if ($TakingBannedSubstance == 1) {
						echo "checked='checked'";
					} ?> />
					<label for="BannedSubstance" class="radio">Taking Banned Substance</label>

					<input name="BannedSubstanceViaPrescription" type="checkbox" value="1" id="BannedSubstanceViaPrescription"
							 class="radio" <?php if ($BannedSubstanceViaPrescription == 1) {
						echo "checked='checked'";
					} ?> />
					<label for="BannedSubstanceViaPrescription" class="radio">Via Prescription</label>

					<div class="row">
						<fieldset class="field">
							<legend>Banned Substance Description</legend>
							<input name="BannedSubstanceDescription" type="text" size="60" title="Banned Substance Description"
								<?php recallText($BannedSubstanceDescription, "no") ?> />
						</fieldset>
					</div>
				</div>
			</div>
		
		<?php } ?>

	</fieldset>
	<!-- ####### /Medical Fieldset ################ -->

	<!-- Travel                                              -->
	<fieldset class="group" id="anchor-travel">
		<legend>&nbsp;
			&nbsp;<span class="legend-links">
				<a href="#anchor-demographic">Demographic</a> -
				<a href="#anchor-contacts">Contacts</a>&nbsp;-&nbsp;
				<a href="#anchor-rugby">Rugby</a>&nbsp;-&nbsp;
				<a href="#anchor-medical">Medical</a>&nbsp;-&nbsp;</span>
				<a href="#anchor-travel">Travel</a>
				<span class="legend-links">&nbsp;-&nbsp;<a href="#anchor-education">Education</a></span>&nbsp;
		</legend>

		<div class="input" style="border-top: none;">
			<label for="ValidPassport" style="margin-right: 1em;">Valid Passport with a Minimum of 6 months before
				expiration?</label>
			<div class="rightcolumn">
				<input name="passportHolder" type="radio" value="Yes" id="ValidPassportYes" class="radio"
						 title="Yes" <?php if ($passportHolder == "Yes") {
					echo 'checked="checked"';
				} ?> />
				<label class="radio" for="ValidPassportYes">Yes</label>
				<input name="passportHolder" type="radio" value="No" id="ValidPassportNo" class="radio"
						 title="No" <?php if ($passportHolder == "No") {
					echo 'checked="checked"';
				} ?> />
				<label class="radio" for="ValidPassportNo">No</label>
			</div>
		</div>

		<div class="row" style="border: 1px dotted #1b6d85; padding: 4px;" id="PassportFields">

			<div class="input" style="border-top: none;">
				<label for="PassportNumber">Passport Number</label>
				<input name="passportNumber" type="text" size="16" title="Your passport number." id="PassportNumber"
					<?php recallText((empty($passportNumber) ? "" : $passportNumber), "no"); ?> />
			</div>

			<div class="input">
				<label for="PassportName">Name on Passport</label>
				<input name="nameOnPassport" type="text" size="30" title="Your name as printed in your passport." id="PassportName"
					<?php recallText((empty($nameOnPassport) ? "" : $nameOnPassport), "no"); ?> />
			</div>

			<div class="input">
				<label for="passportDate">Passport Expiration</label>
				<script>
                $(function () {
                    $("#passportDate").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-10:+20"
                    });
                });
				</script>
				<input type="text" name="passportExpiration" id="passportDate" title="The date your passport expires."
					<?php if (empty($passportExpiration) || $passportExpiration == date('m/d/Y')) {
					} else {
						echo 'value="' . $passportExpirationsave . '"';
					} ?> />
			</div>

			<div class="input">
				<label for="PassportCountry">Issuing Country</label>

				<select name="passportIssuingCountry" size="1" id="PassportCountry"
						  class="CountryIssuing select2">
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "'" . ($passportIssuingCountry == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>

			<div class="input">
				<label for="Citizen1">Country of Citizenship 1</label>

				<select name="Citizen1" size="1" id="Citizen1" class="CountryCitizen1 select2">
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "'" . ($Citizen1 == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>

			<div class="input">
				<label for="Citizen2">Country of Citizenship 2</label>

				<select name="Citizen2" size="1" id="Citizen2" class="Citizen2 select2">
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "'" . ($Citizen2 == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>

			<div class="input">
				<label for="crop-Passport">Select the box to the right to attach a scan of your Passport<br/>
					<span style="font-style: italic; font-size: small">Required for International Travel</span>
				</label>

				<div class="container rightcolumn" id="crop-Passport">

					<!-- Current avatar -->
					<div class="avatar-view" id="passport-view" title="Change your Passport image">
						<img src="<?php echo $PassportEditor; ?>" alt="Passport Image">
					</div>

				</div>
			</div>

			<div class="input">
				<label for="crop-OtherTravel">Other Travel Documentation<br/>
					<span style="font-style: italic; font-size: small">e.g., Visa or Green Card</span>
				</label>

				<div class="container rightcolumn" id="crop-OtherTravel">

					<!-- Current avatar -->
					<div class="avatar-view" id="othertravel-view" title="Change the image">
						<img src="<?php echo $OtherTravelEditor; ?>" alt="Other Travel Image">
					</div>

				</div>
			</div>

			<div class="input" style="border-top: none;">
				<label for="VisaDateIssued">Date Issued</label>
				<script>
                $(function () {
                    $("#VisaDateIssued").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-30:+1"
                    });
                });
				</script>
				<input type="text" name="VisaDateIssued" id="VisaDateIssued" title="The date your Visa was issued."
					<?php
					echo 'value="' . $VisaDateIssued_save . '"';
					?> />
			</div>

		</div>

		<div class="input">
			<label for="airport">Primary Airport</label>
			<select name="ID_primaryAirport" size="1" id="airport" class="airport select2">
				<option value="">&nbsp;</option>
				<?php
				foreach ($airportValues as $key => $airportValue) {
					echo "<option value='" . $key . "'" . ($ID_primaryAirport == $key ? "selected='selected'>" : ">") . $airportValue . "</option>";
				}
				?>
			</select>
		</div>

		<div class="input">
			<label for="airport2">Secondary Airport</label>
			<select name="ID_secondaryAirport" size="1" id="airport2" class="airport2 select2">
				<option value="">&nbsp;</option>
				<?php
				foreach ($airportValues as $key => $airportValue) {
					echo "<option value='" . $key . "'" . ($ID_secondaryAirport == $key ? "selected='selected'>" : ">") . $airportValue . "</option>";
				}
				?>
			</select>
		</div>

		<div class="input">
			<label for="FrequentFlyer">Frequent Flyer Information</label>
			<input name="frequentFlyerInfo" type="text" id="FrequentFlyer" size="70"
				<?php recallText((empty($frequentFlyerInfo) ? "" : $frequentFlyerInfo), "no"); ?> />
		</div>

		<div class="input">
			<label for="TravelComments">Travel Comments</label>
			<input name="travelComments" type="text" size="70" id="TravelComments"
				<?php recallText((empty($travelComments) ? "" : $travelComments), "no"); ?> />
		</div>


	</fieldset>
	<!-- ####### /Travel Fieldset ################ -->
	
	<?php if ($IsPlayer) { ?>

		<!-- Education                                              -->
		<fieldset class="group" id="anchor-education">
			<legend>&nbsp;
				<span class="legend-links">
					<a href="#anchor-demographic">Demographic</a> -
					<a href="#anchor-contacts">Contacts</a>&nbsp;-&nbsp;
					<a href="#anchor-rugby">Rugby</a>&nbsp;-&nbsp;
					<a href="#anchor-medical">Medical</a>&nbsp;-&nbsp;
					<a href="#anchor-travel">Travel</a>&nbsp;-&nbsp;</span>
				<a href="#anchor-education">Education</a>&nbsp;
			</legend>
			
			<?php if ($IsPlayer) { ?>

				<div class="input" style="border-top: none;">
					<label>School Search</label>

					<div class="rightcolumn">
						<fieldset class="field">
							<legend>State</legend>
							<select name="StatePlayingIn" id="StatePlayingIn" size="1"
									  title="State or Canadian Province of your high school">
								<option value="" disabled selected>State</option>
								<?php
								foreach ($stateValues as $value) {
									echo "<option value='" . $value . "'" . ($StatePlayingIn == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						</fieldset>
						
						<?php if ($U19) { ?>
							<fieldset class="field">
								<legend><?php echo $season ?> Grade Level</legend>
								<select name="CurrentSchoolGradeLevel" id="GradeLevel" size="1"
										  title="The school grade level you are or will be at.">
									<option value=""></option>
									<?php
									for ($i = 3; $i < 13; $i++) {
										echo "<option value='" . $i . "'" . ($CurrentSchoolGradeLevel == $i ? "selected='selected'" : "") . ">" . $i . "</option>";
									}
									?>
								</select>
							</fieldset>
						<?php } ?>

						<button id="UpdateSchoolButton" class='btn btn-primary hidden' style='margin: 2px 0 2px 2em;' type='submit'
								  formaction='body.php?UpdateSchool=1#anchor-education'>Search
						</button>
					</div>
				</div>

				<div id="HighSchoolFields">
					<div class="input" style="border-top: none;">
						<label>School Name</label>
						<select class="select2" name="ID_School" size="1" title="School you are attending">
							<option value=""></option>
							<?php
							foreach ($SchoolValues as $key => $SchoolValue) {
								echo "<option value='" . $key . "'" . ($ID_School == $key ? "selected='selected'>" : ">") . $SchoolValue . "</option>";
							}
							?>
						</select>
					</div>
					
					<?php if (!$U18) { ?>
						<div class="input" style="border-top: none;">
							<label for="HSGraduationYear">High School Graduation Year</label>
							<input type="text" size="6" name="HighSchoolGraduationYear"
									 id="HSGraduationYear" <?php if (!empty($HighSchoolGraduationYear)) {
								echo "value='" . $HighSchoolGraduationYear . "'";
							} ?> />
						</div>
					<?php } ?>

				</div>
			
			<?php }
			if (!$U18) { //show college & military fields ?>

				<div class="input">
					<label for="CollegeName">College</label>
					<select id="CollegeName" class="select2" name="ID_School_College" size="1" title="College you are attending">
						<option value=""></option>
						<?php
						foreach ($CollegeValues as $key => $CollegeValue) {
							echo "<option value='" . $key . "'" . ($ID_School_College == $key ? "selected='selected'>" : ">") . $CollegeValue . "</option>";
						}
						?>
					</select>
				</div>

				<div class="input" style="border-top: none;">
					<label for="CollegeGraduationYear">College Graduation Year</label>
					<input id="CollegeGraduationYear" name="graduationCollegeYear" type="text"
							 size="6" <?php if (!empty($graduationCollegeYear)) {
						echo "value='" . $graduationCollegeYear . "'";
					} ?> />
				</div>

				<div class="input">
					<label>Currently Military?</label>
					<input name="currentlyMilitary" type="radio" value="Yes" class="radio" id="CurrentlyMilitaryYes"
							 title="Yes" <?php if ($currentlyMilitary == "Yes") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="CurrentlyMilitaryYes">Yes</label>

					<input name="currentlyMilitary" type="radio" value="No" id="CurrentlyMilitaryNo" class="radio"
							 title="No" <?php if ($currentlyMilitary == "No") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="CurrentlyMilitaryNo">No</label>
				</div>

				<div class="input">
					<label for="MilitaryBranch">Military Branch</label>
					<select name="militaryBranch" id="MilitaryBranch" size="1">
						<option value=""></option>
						<?php
						foreach ($MilitaryBranchValues as $MilitaryBranchValue) {
							echo "<option value='" . $MilitaryBranchValue . "'" . ($militaryBranch == $MilitaryBranchValue ? "selected='selected'>" : ">") . $MilitaryBranchValue . "</option>";
						}
						?>
					</select>
				</div>

				<div class="input">
					<label for="MilitaryComponent">Military Component</label>
					<select name="militaryComponent" id="MilitaryComponent" size="1">
						<option value=""></option>
						<?php
						foreach ($MilitaryComponentValues as $MilitaryComponentValue) {
							echo "<option value='" . $MilitaryComponentValue . "'" . ($militaryComponent == $MilitaryComponentValue ? "selected='selected'>" : ">") . $MilitaryComponentValue . "</option>";
						}
						?>
					</select>
				</div>
			
			<?php } ?>
		</fieldset>
		<!-- ####### /Education Fieldset ################ -->
	
	<?php } ?>


	<div id="anchor-education" <?php
	if (empty($waiver)) {
		echo 'class="missing" style="padding: 1em"';
	} ?> >
		<input id="Waiver" type="checkbox" name="waiver" value="1" class="radio" <?php
		if ($waiver == 1) {
			echo 'checked="checked"';
		} ?>/>
		<label for="Waiver" class="radio">I accept responsibility that the information provided on this form is accurate.</label>
	</div>

	<input type="submit" name="APPLY" value="APPLY" class="submit buy" id="Submit_Button"/>
	<input type="hidden" name="submitted-profile" value="true"/>

	<input id="CroppedFace" name="CroppedFace" type="hidden" value=""/>
	<input id="CroppedSchool" name="CroppedSchool" type="hidden" value=""/>
	<input id="CroppedDOB" name="CroppedDOB" type="hidden" value=""/>
	<input id="CroppedInsuranceCard" name="CroppedInsuranceCard" type="hidden" value=""/>
	<input id="CroppedPassport" name="CroppedPassport" type="hidden" value=""/>
	<input id="CroppedOtherTravel" name="CroppedOtherTravel" type="hidden" value=""/>

	<div id="Submit_Dialog" title="Updating Profile">
		<p>Please wait while your profile is updated. This can take up to a minute.</p>
	</div>
</form>
