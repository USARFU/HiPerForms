<?php
/**
 * Created by PhpStorm.
 * User: aewerdt
 * Date: 1/19/2017
 * Time: 9:42 AM
 */

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && $RegistrationSubmitted1) { ?>
	<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
		<tr>
			<td>Registration was unsuccessful due to the following problems:
				<p style="color: red"><i>
						<?php echo $fail; ?>
					</i></p>
			</td>
		</tr>
	</table>
<?php } ?>
<!-- ################################# -->

<div class="mt-10"></div>

<h5>Form Notes</h5>
<ul style="width: 90%;">
	<li>Required Fields: If the form is submitted and any required fields are in error, the fields in error will be
		indicated in red.
	</li>
	<li>Date Fields: All dates must be entered in the mm/dd/yyyy or yyyy-mm-dd format.</li>
	<li>For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</li>
	<li>For questions regarding the the registration process, contact <a
				href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.
	</li>
</ul>

<div class="mt-10"></div>

<form id="MemberForm" action="body.php<?php if ($EditingMemberProfile) { echo "?ID=" . $ID_Personnel; }?>" method="post" enctype="multipart/form-data">

	<div class="groupheader">Member Information</div>
	<div class="groupbody">
		<div class="input" style="border-top: none;">
			<label for="Name">Legal Name <span class="<?php if (empty($firstName) || empty($lastName)) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<div class="rightcolumn">
				<input name="firstName" type="text" size="20" placeholder="First" id="Name" style="margin-right: 2em"
						 title="Your first name (required)" <?php recallText((empty($firstName) ? "" : $firstName), "yes"); ?> />
				<input name="middleName" type="text" size="20" placeholder="Middle" id="Name" style="margin-right: 2em"
						 title="Your middle name (optional)" <?php recallText((empty($middleName) ? "" : $middleName), "no"); ?> />
				<input name="lastName" type="text" size="20" placeholder="Last" id="Name"
						 title="Your last name (required)" <?php recallText((empty($lastName) ? "" : $lastName), "yes"); ?> />
			</div>
		</div>

		<div class="input">
			<label for="NickName">Preferred First Name
				<small>(if different)</small>
			</label>
			<input name="nickName" type="text" size="20" id="NickName"
					 title="The name you prefer to be called." <?php recallText((empty($nickName) ? "" : $nickName), "no"); ?> />
		</div>

		<div class="input">
			<label for="DOBDate">DOB <span class="<?php if (empty($DOB) || $DOB == date('m/d/Y')) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<input type="text" name="DOB" id="DOBDate" title="Your Date of Birth"
				<?php if (empty($DOB) || $DOB == date('m/d/Y')) {
					echo 'class="missing"';
				} else {
					echo 'value="' . $DOBsave . '"';
				} ?>/>
		</div>

		<div class="input">
			<label>Gender <span class="<?php if (empty($gender)) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<div class="rightcolumn <?php if (empty($gender)) {
				echo ' missing';
			} ?>">
				<input name="gender" type="radio" value="Male" id="GenderMale" class="radio"
						 title="Male" <?php if (!empty($gender) and $gender == "Male") {
					echo 'checked="checked"';
				} ?> />
				<label for="GenderMale" class="radio">Male</label>
				<input name="gender" type="radio" value="Female" id="GenderFemale" class="radio"
						 title="Female" <?php if (!empty($gender) and $gender == "Female") {
					echo 'checked="checked"';
				} ?> />
				<label for="GenderFemale" class="radio">Female</label>
			</div>
		</div>

		<div class="input">
			<label for="Race">Race / Ethnicity <span class="<?php if (empty($ethnicity)) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<select name="ethnicity" size="1" id="Race" <?php if (empty($ethnicity)) {
				$ethnicity_a = " ";
				echo 'class="missing"';
			} else {
				$ethnicity_a = $ethnicity;
			} ?> >
				<option value="">&nbsp;</option>
				<?php
				foreach ($ethnicityValues as $value) {
					echo "<option value='" . $value . "'" . ($ethnicity_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
				}
				?>
			</select>
		</div>

		<div class="input">
			<label for="Address">Home Address <span
						class="<?php if (empty($homeAddress1) || empty($City) || empty($zipCode)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						} ?>">REQUIRED</span></label>
			<div class="rightcolumn">
				<input name="homeAddress1" type="text" id="Address" placeholder="Street 1"
						 size="40" <?php recallText((empty($homeAddress1) ? "" : $homeAddress1), "yes"); ?> />
				<br/>
				<input name="homeAddress2" type="text" id="Address" placeholder="Street 2"
						 size="40" <?php recallText((empty($homeAddress2) ? "" : $homeAddress2), "no"); ?> />
				<br/>
				<input name="City" type="text" id="Address" placeholder="City" style="margin-right: 1em"
						 size="30" <?php recallText((empty($City) ? "" : $City), "yes"); ?> />
				<select name="State" size="1" id="Address" title="State or Canadian Province" style="margin-right: 1em">
					<option value="" disabled selected>State</option>
					<?php
					foreach ($stateValues as $value) {
						echo "<option value='" . $value . "'" . ($State == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
				<input name="zipCode" type="text" id="Address" placeholder="Postal Code"
						 size="10" <?php recallText((empty($zipCode) ? "" : $zipCode), "yes"); ?> />
			</div>
		</div>

		<div class="input" style="border-top: none">
			<label for="Country">Country <span class="<?php if (empty($Country)) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<div class="<?php echo(empty($Country) ? 'missing' : ''); ?>" style="display: inline-block;">
				<select name="Country" size="1" id="Country" class="CountryAddress select2">
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "'" . ($Country == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
		</div>

		<div class="input">
			<label for="Citizen1">Country of Citizenship 1 <span class="<?php if (empty($Citizen1)) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<div class="<?php echo(empty($Citizen1) ? 'missing' : ''); ?>" style="display: inline-block;">
				<select name="Citizen1" size="1" id="Citizen1" class="CountryCitizen1 select2">
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "'" . ($Citizen1 == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
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
			<label for="Kit">Clothing Sizes <span class="<?php if (empty($MatchJerseySize) || empty($MatchShortsSize)) {
					echo "mandatoryFailed";
				} else {
					echo "mandatory";
				} ?>">REQUIRED</span></label>
			<div class="rightcolumn">
				<fieldset class="field" style="width: 8em;">
					<legend>Match Jersey</legend>
					<div class="<?php echo(empty($MatchJerseySize) ? 'missing' : ''); ?>" style="display: inline-block;">
						<select name="MatchJerseySize" size="1"
								  id="Kit">
							<option value="">&nbsp;</option>
							<?php
							foreach ($clothingSizeValues as $value) {
								echo "<option value='" . $value . "'" . ($MatchJerseySize == $value ? "selected='selected'>" : ">") . $value . "</option>";
							}
							?>
						</select>
					</div>
				</fieldset>
				<fieldset class="field" style="width: 9em;">
					<legend>Match Shorts</legend>
					<div class="<?php echo(empty($MatchShortsSize) ? 'missing' : ''); ?>" style="display: inline-block;">
						<select name="MatchShortsSize" size="1"
								  id="Kit">
							<option value="">&nbsp;</option>
							<?php
							foreach ($clothingSizeValues as $value) {
								echo "<option value='" . $value . "'" . ($MatchShortsSize == $value ? "selected='selected'>" : ">") . $value . "</option>";
							}
							?>
						</select>
					</div>
				</fieldset>
			</div>
		</div>

		<div class="input">
			<label for="PrimaryPhoneNumber" id="Phone_Button">Primary Phone Number<br/>
				<small><i>(Cell Preferred)</i></small>
				<img src="../include/info.PNG" height="16"> <span
						class="<?php if (empty($PrimaryPhoneNumber)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						} ?>">REQUIRED</span></label>

			<div id="Phone_Dialog" title="Phone Numbers">
				<p>Event and Camp administrators and coaches may use HiPer to send out notifications via text message.
					Therefore, the most important phone number is one that can receive text messages.</p>
				<p>Phone numbers are formatted after being submitted, so they can be entered as 5555555555.</p>
			</div>

			<div class="rightcolumn">
				<fieldset class="field">
					<input name="PrimaryPhoneNumber" type="text" size="25" id="PrimaryPhoneNumber" placeholder="Phone Number"
						<?php recallText((empty($PrimaryPhoneNumber) ? "" : $PrimaryPhoneNumber), "yes"); ?>
					/>
				</fieldset>
				<fieldset class="field">
					<input name="PrimaryPhoneText_flag" type="checkbox" value="1" id="PrimaryPhoneText" class="radio"
						<?php if ($PrimaryPhoneText_flag == 1) {
							echo " checked='checked'";
						} ?> />
					<label for="PrimaryPhoneText" class="radio">This Phone is capable of receiving text messages.</label>
				</fieldset>
			</div>
		</div>

	</div>

	<input type="submit" name="Next" value="Next" class="submit buy Processing"/>
	<input type="hidden" name="submitted-registration1" value="true"/>

</form>