<?php

//<!-- Show messages.                    -->
if (isset($message_clubmembership)) {
	echo '<h3>' . $message_clubmembership . '</h3>';
} else {
	?>

	<div class="row-divider"
		<?php if ($ClubmembershipStatus == "orange") {
			echo 'style="border:2px solid darkorange; background-color: rgba(255,165,0,.5)"';
		} elseif ($ClubmembershipStatus == "red" || $ClubmembershipStatus == "black") {
			echo 'style="border:2px solid red; background-color: rgba(255,99,71,.5)"';
		} else {
			echo 'style="border:2px solid green; background-color: rgba(144,238,144,.5)"';
		}
		?> >
		<div class="title small icon black entypo-back-in-time" style="color: black">
			<?php if ($ClubmembershipStatus == "black") {
				echo 'You need at least one active club membership role to access the other areas of this site. ' ;
			} else { ?>
				You last verified your club membership <span
						style="font-style: italic; font-weight: bold"><?php echo $MonthsSinceModifiedClubMembership; ?></span> months ago.
				<?php if ($ClubmembershipStatus == "orange") {
					echo '<br/>Please review and update any missing data.';
				} elseif ($ClubmembershipStatus == "red") {
					echo '<br/>Your club membership hasn\'t been updated in over a year. Please review and if necessary update your data before continuing.';
				}
			}
			?>
		</div>
	</div>

<?php } ?>
<!-- ################################# -->


<div class="mt-10"></div>

<!-- Add table to display any error messages from submitted form. -->
<?php if (!empty($fail) && !empty($_POST['submitted-clubmembership'])) { ?>
	<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
		<tr>
			<td>Your club membership could not be updated due to the following problems:
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
	<li>Fields and features of the other tabs are dependant on your club roles, so make sure to add all club memberships that currently apply to you.</li>
	<li>At least one club affiliation must be added before accessing the Camp Enrollment tab.</li>
</ul>

<div class="mt-10"></div>

<form id="mainForm" action="body.php<?php if ($EditingMemberProfile) {
	echo "?ID=" . $ID_Personnel;
} ?>" method="post" enctype="multipart/form-data">

	<!-- Rugby                           -->
	<fieldset class="group">

		<div class="input" style="border-top: none">
			<label class="top">Club Membership History <span
						style="font-weight: 100">(Please select the circle to identify your Primary Club)<br/>If you are no longer with a club, please enter the date that you stopped being a member.</span></label>
			<div style="max-height: 560px; overflow-y: auto;"> <!-- scrollbar -->
				
				<?php
				if ($related_ClubMembership_count > 0) {
					$ClubMemberPrimaryHistory = "";
					
					foreach ($related_ClubMembership as $ClubMembership_record) {
						$InvitationFlag = ($ClubMembership_record->getField('Personnel__ClubMembership::Invitation_flag') == "1" ? true : false);
						$InactiveFlag = ($ClubMembership_record->getField('Personnel__ClubMembership::Inactive_flag') == 1 ? 1 : 0);
						$ClubMembership_RecordID = $ClubMembership_record->getRecordID();
						$related_PrimaryFlag = $ClubMembership_record->getField('Personnel__ClubMembership::Primary_flag');
						$ClubMemberPrimaryHistory .= $related_PrimaryFlag == "1" ? $ClubMembership_RecordID : "";
						if (!$InvitationFlag && !$InactiveFlag) {
							$ClubMemberHistory[$ClubMembership_RecordID]['Role'] = $ClubMembership_record->getField('Personnel__ClubMembership::Role');
							$ClubMemberHistory[$ClubMembership_RecordID]['StartDate'] = $ClubMembership_record->getField('Personnel__ClubMembership::StartDate');
							$ClubMemberHistory[$ClubMembership_RecordID]['EndDate'] = $ClubMembership_record->getField('Personnel__ClubMembership::EndDate');
							$related_ClubName = $ClubMembership_record->getField('Personnel__ClubMembership::c_ClubName');
							
							echo "<div class='row-divider row-divider-color'>

						<div class='row'>
						<fieldset class='field'>
						<legend>Primary</legend>
							<input name='ClubMembershipHistory_UpdatePrimary' type='radio' value='" . $ClubMembership_RecordID . "'
							 title='Select the one record that is your Primary Club' " . ($related_PrimaryFlag == 1 ? "checked='checked'" : "") . " />
						</fieldset>
						
						<fieldset class='field' style='margin-right: .5em;'>
						<legend>Name</legend>
							" . $related_ClubName . "
						</fieldset>
						</div>
						
						<div class='row'>
						<fieldset class='field'>
						<legend>Role</legend>
							<select class='alpha50' name='ClubMembershipHistory_Update[" . $ClubMembership_RecordID . "][Role]' size='1' title='What is your role in this club?'>";
							foreach ($clubRoleValues as $clubRoleValue) {
								echo "<option value='" . $clubRoleValue . "' " . ($ClubMemberHistory[$ClubMembership_RecordID]['Role'] == $clubRoleValue ? " selected='selected'>" : ">") . $clubRoleValue . "</option>";
							}
							echo "
						</select>
						</fieldset>
						
						<fieldset class='field'>
						<legend>Start Date</legend>
							<input class='alpha50 Date-80-1' type='text' name='ClubMembershipHistory_Update[" . $ClubMembership_RecordID . "][StartDate]' title='The Date You Joined this Club' value='" . $ClubMemberHistory[$ClubMembership_RecordID]['StartDate'] . "' />
						</fieldset>
						
						<fieldset class='field' style='margin-right: .5em;'>
						<legend>End Date</legend>
							<input class='alpha50 Date-80-1' type='text' name='ClubMembershipHistory_Update[" . $ClubMembership_RecordID . "][EndDate]' title='The Date You Left this Club' value='" . $ClubMemberHistory[$ClubMembership_RecordID]['EndDate'] . "' />
						</fieldset>
						
						</div></div>";
						} elseif ($InvitationFlag && !$InactiveFlag) {
							$related_ClubRole = $ClubMembership_record->getField('Personnel__ClubMembership::Role');
							$related_ClubStartDate = $ClubMembership_record->getField('Personnel__ClubMembership::StartDate');
							$related_ClubName = $ClubMembership_record->getField('Personnel__ClubMembership::c_ClubName');
							
							echo "<div class='row-divider row-divider-color'>

						<div class='row'>
						
						<fieldset class='field'>
						<legend>Primary</legend>
							<input name='ClubMembershipHistory_UpdatePrimary' type='radio' value='" . $ClubMembership_RecordID . "'
							 title='Select the one record that is your Primary Club' " . ($related_PrimaryFlag == 1 ? "checked='checked'" : "") . " />
						</fieldset>
						
						<fieldset class='field' style='margin-right: .5em;'>
						<legend>Name</legend>
							" . $related_ClubName . "
						</fieldset>
						
						</div>
						
						<div class='row'>
						
						<fieldset class='field'>
						<legend style='width: 8.5em'>Role</legend>
							" . $related_ClubRole . "
						</select>
						</fieldset>
						
						<fieldset class='field'>
						<legend>Start Date</legend>
							" . $related_ClubStartDate . "
						</fieldset>
						
						</div></div>";
						}
					}
				}
				$_SESSION['ClubMemberHistory_Original'] = isset($ClubMemberHistory) ? $ClubMemberHistory : "";
				$_SESSION['ClubMemberPrimaryHistory_Original'] = isset($ClubMemberPrimaryHistory) ? $ClubMemberPrimaryHistory : "";
				
				?>
			</div> <!-- /scrollbar -->
		</div>

		<div class="input">
			<label class="top">New Club Membership
				<?php if ($related_ClubMembership_count == 0) {
					echo "<span class='mandatoryFailed'>REQUIRED</span>";
				} ?>
			</label>

			<div <?php if ($ActiveClubMembershipCount == 0 && (empty($ID_Club) || empty($ClubRole) || empty($StartDate)) && empty($UnlistedClub_flag) && empty($DoNotBelongToAClub_flag)) {
				echo "class='missing' style='padding: 8px;'";
			} ?>>

				<div id="YesClubFields">
					<div class="row">
						<fieldset class="field">
							<legend>Club Name</legend>
							<?php if (empty($ID_Club)) {
								$ID_Club_a = " ";
							} else {
								$ID_Club_a = $ID_Club;
							} ?>
							<select name="ID_Club" size="1" class="primaryClub select2"
									  title="The Rugby Club you play for. If it isn't listed, select 000.">
								<option value="">&nbsp;</option>
								<?php
								foreach ($clubValues as $key => $clubValue) {
									echo "<option value='" . $key . "' " . ($ID_Club_a == $key ? "selected='selected'>" : ">") . $clubValue . "</option>";
								}
								?>
							</select>
						</fieldset>
					</div>

					<div class="row">
						<fieldset class="field">
							<legend>Club Role</legend>
							<select name="ClubRole" size="1" title="What is your role in this club?">
								<option value="">&nbsp;</option>
								<?php
								foreach ($clubRoleValues as $clubRoleValue) {
									echo "<option value=\"" . $clubRoleValue . "\" " . ($ClubRole == $clubRoleValue ? "selected=\"selected\">" : ">") . $clubRoleValue . "</option>";
								}
								?>
							</select>
						</fieldset>

						<fieldset class="field">
							<legend>Start Date</legend>
							<input type="text" name="StartDate" class="Date-80-1" title="Your Date of Birth"
								<?php if (empty($StartDate) || $StartDate == date('m/d/Y')) {
								} else {
									echo 'value="' . $StartDatesave . '"';
								} ?>/>
						</fieldset>

						<fieldset class="field">
							<legend>Primary Rugby Club</legend>
							<input name="Primary_flag" type="checkbox" value="1" title="Check here if this is your primary club"/>
						</fieldset>
					</div>
				</div>

				<div class="row" style="margin-top: 1em;">
					<input name="OtherClub" type="radio" value="UnlistedClub" class="radio" id="UnlistedClubRadio"
							 title="Select this if your club is not in the drop-down list."/>
					<label class="radio" for="UnlistedClubRadio">My Club Is Not Listed</label>
					<input name="OtherClub" type="radio" value="NoClub" class="radio" id="NoClubRadio"
							 title="Select this if you do not belong to a club at the time."/>
					<label class="radio" for="NoClubRadio">I Do Not Currently Belong To A Club</label>
					<div class="hidden" id="YesClub" style="display: inline-block;">
						<input name="OtherClub" type="radio" value="YesClub"
								 class="radio" id="YesClubRadio"
								 title="View the Club list"/>
						<label class="radio" for="YesClubRadio">View the Club List</label></div>
				</div>

				<div class="row hidden" style="border: 1px dotted #1b6d85; padding: 4px;" id="UnlistedClubFields">
					<fieldset class="field">
						<legend>Unlisted Club Name</legend>
						<input name="UnlistedClub_Name" type="text" title="Unlisted Club Name"
								 size="50" <?php recallText((empty($UnlistedClub_Name) ? "" : $UnlistedClub_Name), "no"); ?> />
					</fieldset>
					<fieldset class="field">
						<legend>Club City</legend>
						<input name="UnlistedClub_City" type="text" title="Unlisted Club City"
								 size="24" <?php recallText((empty($UnlistedClub_City) ? "" : $UnlistedClub_City), "no"); ?> />
					</fieldset>
					<fieldset class="field">
						<legend>Club State / Country</legend>
						<input name="UnlistedClub_State" type="text" title="Unlisted Club State or Country"
								 size="24" <?php recallText((empty($UnlistedClub_State) ? "" : $UnlistedClub_State), "no"); ?> />
					</fieldset>

					<div class="row">
						<fieldset class="field">
							<legend>Club Role</legend>
							<select name="UnlistedClub_Role" size="1" title="What is your role in this club?">
								<option value="">&nbsp;</option>
								<?php
								foreach ($clubRoleValues as $clubRoleValue) {
									echo "<option value=\"" . $clubRoleValue . "\" " . ($UnlistedClub_Role == $clubRoleValue ? "selected=\"selected\">" : ">") . $clubRoleValue . "</option>";
								}
								?>
							</select>
						</fieldset>

						<fieldset class="field">
							<legend>Start Date</legend>
							<input type="text" name="UnlistedClub_StartDate" class="Date-80-1" title="Your Date of Birth"
								<?php if (empty($UnlistedClub_StartDate) || $UnlistedClub_StartDate == date('m/d/Y')) {
								} else {
									echo 'value="' . $UnlistedClub_StartDatesave . '"';
								} ?>/>
						</fieldset>
						
					</div>
				</div>

				<div class="row hidden" style="border: 1px dotted #1b6d85; padding: 4px;" id="NoClubFields">
					<fieldset class="field">
						<legend>Rugby Role</legend>
						<select name="NoClub_Role" size="1" title="What is your role within rugby?">
							<option value="">&nbsp;</option>
							<?php
							foreach ($clubRoleValues as $clubRoleValue) {
								echo "<option value=\"" . $clubRoleValue . "\" " . ($NoClub_Role == $clubRoleValue ? "selected=\"selected\">" : ">") . $clubRoleValue . "</option>";
							}
							?>
						</select>
					</fieldset>
				</div>
				
			</div>
		</div>

	</fieldset>
	<!-- ####### /Rugby Fieldset ########################## -->
	
	<input type="submit" name="APPLY" value="APPLY" class="submit buy" id="Submit_Button"/>
	<input type="hidden" name="submitted-clubmembership" value="true"/>

	<div id="Submit_Dialog" title="Updating Club Membership">
		<p>Please wait while your club membership is updated. This can take up to a minute.</p>
	</div>
</form>

<script>
    $(document).ready(function () {

        <!-- Conditional Hidden fields -->
        var OtherClub = $('input:radio[name=OtherClub]');
        var YesClubFields = $('#YesClubFields');
        var UnlistedClubFields = $('#UnlistedClubFields');
        var NoClubFields = $('#NoClubFields');
        var YesClub = $('#YesClub');

        OtherClub.change(function () { //when the rating changes
            var value = this.value;
            if (value === "UnlistedClub") {
                UnlistedClubFields.removeClass('hidden');
                YesClub.removeClass('hidden');
                YesClubFields.addClass('hidden');
            } else {
                UnlistedClubFields.addClass('hidden');
            }
            if (value === "NoClub") {
                NoClubFields.removeClass('hidden');
                YesClub.removeClass('hidden');
                YesClubFields.addClass('hidden');
            } else {
                NoClubFields.addClass('hidden');
            }
            if (value === "YesClub") {
                YesClub.addClass('hidden');
                YesClubFields.removeClass('hidden');
            }
        });

    });
</script>