<?php

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && $ChangeAccount) { ?>
	<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
		<tr>
			<td>Your account could not be changed due to the following problems:
				<p style="color: red"><i>
						<?php echo $fail; ?>
					</i></p>
			</td>
		</tr>
	</table>
<?php } ?>
<!-- ################################# -->

<!-- Show messages.                    -->
<?php
if (isset($message_account) && $ChangeAccount) {
	echo '<br />'
		. '<h3>' . $message_account . '</h3>';
}
?>
<!-- ################################# -->

<h5>Form Notes</h5>
<ul style="width: 90%;">
	<li>Required Fields: If the form is submitted and any required fields are in error, the fields in error will be
		indicated in red.
	</li>
	<li>For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</li>
	<li>For questions regarding the data itself, or to request changes to read-only fields, contact <a
				href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.
	</li>
</ul>

<div class="mt-10"></div>

<form action="body.php<?php if ($EditingMemberProfile) { echo "?ID=" . $ID_Personnel; }?>" method="post" enctype="multipart/form-data">

	<fieldset class="group" id="anchor-account">
		<legend>Account</legend>

		<div class="input" style="border-top: none;">
			<label for="e-Mail" id="eMail_Button" style="padding-top: 0">e-Mail <img src="../include/info.PNG"
																											 height="16"> <span
						class="<?php if (empty($eMail)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						} ?>">REQUIRED</span></label>
			<div id="eMail_Dialog" title="e-Mail Address">
				<p>The e-mail address is both your login account, and the primary way that USA Rugby administrators and
					coaches contact you.</p>
			</div>
			<input type="email" size="40" name="eMail"
					 id="e-Mail" <?php recallText((empty($eMail) ? "" : $eMail), "yes"); ?>/>
		</div>

		<div class="input">
			<label for="Password">Change Password</label>
			<div class="rightcolumn">
				<div class="row">
					<fieldset class="field">
						<legend>Current Password</legend>
						<input name="currentPassword" type="password" size="25" id="Password"/>
					</fieldset>
				</div>
				<div class="row">
					<fieldset class="field">
						<legend>New Password</legend>
						<input name="newPassword1" type="password" size="25" id="Password"/>
					</fieldset>
					<fieldset class="field" style="margin-right: .5em;">
						<legend>Re-enter New Password</legend>
						<input name="newPassword2" type="password" size="25" id="Password"/>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="input" style="position: relative">
			<input name="change_password" type="hidden" value="true"/>
			<input type="submit" value="CHANGE" class="submit buy">
			<?php
			if ($ChangeAccountSuccess) {
				?>
				<div id="PasswordChanged_Dialog" title="Password Changed">
					<p>Password was successfully changed.</p>
				</div>
				<?php
			}
			?>
		</div>
	</fieldset>
</form>