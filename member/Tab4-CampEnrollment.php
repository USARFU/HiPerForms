<?php
/**
 * Created by PhpStorm.
 * User: aewerdt
 * Date: 1/19/2017
 * Time: 10:26 AM
 */

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && !empty($POST['submitted-camp'])): ?>
	<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
		<tr>
			<td>Enrollment was unsuccessful due to the following problems:
				<p style="color: red"><i>
						<?php echo $fail; ?>
					</i></p>
			</td>
		</tr>
	</table>
<?php endif; ?>
<!-- ################################# -->

<!-- Show messages.                    -->
<?php
if (isset($message_enrollment)) {
	echo '<br />'
		. '<h3>' . $message_enrollment . '</h3>';
	die();
}
?>
<!-- ################################# -->

<h5>Form Notes</h5>
<ul style="width: 90%;">
	<li>For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</li>
	<li>For questions regarding the data itself, or to request changes to read-only fields, contact <a
				href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.
	</li>
</ul>

<div class="mt-10"></div>

<form id="campForm" action="body.php<?php if ($EditingMemberProfile) {
	echo "?ID=" . $ID_Personnel;
} ?>" method="post">

	<fieldset class="input" style="border-top: none; position: relative">
		<label class="top" id="OpenRegistration_Button">Open Registrations <img src="../include/info.PNG" height="16"></label>
		
		<div id="OpenRegistration_Dialog" title="Open Registration">
			<p>This lists any camps/events that are open for registration.</p>
			<p>Indicating interest does not complete the registration process, it just adds you to the invite list. Once the head
				coach
				reviews the list and qualifies you, you will receive additional information on the camp.</p>
		</div>

		<div style="position: absolute; top: 0; right: 0">
			Venue Search:&nbsp;
			<select name="ID_Venue" size="1" title="Venue Search">
				<option value=""></option>
				<?php
				foreach ($Venues as $key => $venue_name) {
					echo "<option value='" . $key . "' " . ($ID_Venue == $key ? "selected='selected'>" : ">") . $venue_name . "</option>";
				}
				?>
			</select>
			<button id="VenueSearch" class='btn btn-primary' style='margin: 2px 0 2px 2em;' type='submit'
					  formaction='body.php?activeTab=4&VenueSearch=1'>Search
			</button>
		</div>
		<br />
		
		<?php
		if ($related_campRegistration_count > 0) {
			// Get list of history Camp Record IDs. If any matches an open registration camp, then its checkbox needs to be checked.
			if ($related_camps_count > 0) {
				foreach ($related_camps as $related_camp) {
					$CampHistoryRole = $related_camp->getField('Personnel__CampPersonnel::CampRole');
					$CampHistoryIDs[$CampHistoryRole] = $related_camp->getField('Personnel__CampPersonnel::ID_Event');
				}
			} else {
				$CampHistoryIDs[] = "";
			}
			
			foreach ($related_campRegistrations as $related_campRegistration) {
				$camp_RecordID = $related_campRegistration->getRecordID();
				$camp_ID = $related_campRegistration->getField('Camp.OpenRegistration::ID');
				$camp_name = $related_campRegistration->getField('Camp.OpenRegistration::Name');
				$camp_venue = $related_campRegistration->getField('Camp.OpenRegistration::c_Venue');
				$camp_startDate = $related_campRegistration->getField('Camp.OpenRegistration::StartDate');
				$camp_endDate = $related_campRegistration->getField('Camp.OpenRegistration::EndDate');
				$camp_expiration = $related_campRegistration->getField('Camp.OpenRegistration::OpenRegistrationExpiration');
				$camp_description = $related_campRegistration->getField('Camp.OpenRegistration::OpenRegistrationDescription');
				$camp_gender = $related_campRegistration->getField('Camp.OpenRegistration::Gender');
				$camp_ageBaseline = $related_campRegistration->getField('Camp.OpenRegistration::OpenRegistrationAgeBaseline');
				$camp_ageFrom = $related_campRegistration->getField('Camp.OpenRegistration::OpenRegistrationAgeFrom');
				$camp_ageTo = $related_campRegistration->getField('Camp.OpenRegistration::OpenRegistrationAgeTo');
				$camp_roles = explode("\n", $related_campRegistration->getField('Camp.OpenRegistration::OpenRegistrationRoles'));
				
				// For U19 players, hide "Varsity" camps for JV players, and hide "JV" camps for Varsity players
				$SkipAgeTest = false;
				$JV_valid_player = true;
				$Varsity_valid_player = true;
				$JV_valid_camp = strstr($camp_name, "JV");
				$Varsity_valid_camp = strstr($camp_name, "Varsity");
				if ($U19) {
					$JV_valid_player = (($CurrentSchoolGradeLevel < 11 && $CurrentSchoolGradeLevel > 7) ? true : false);
					$Varsity_valid_player = (($CurrentSchoolGradeLevel < 14 && $CurrentSchoolGradeLevel > 10) ? true : false);
				}
				if ((!$JV_valid_player && !$Varsity_valid_player) || !$U19) {
					if (!$JV_valid_camp && !$Varsity_valid_camp) {
						$SkipAgeTest = true;
					}
				}
				
				$camp_registeredStatus = (array_search($camp_ID, $CampHistoryIDs) !== false ? true : false);
				$camp_registeredRole = array_search($camp_ID, $CampHistoryIDs); //returns the key, which is the Role
				$SelectedCampRole = ($camp_registeredStatus == true ? $camp_registeredRole : $PrimaryClubRole); //Default non-registered camp role values to primary club role
				
				// Does one of the user's club roles match one of the allowed camp's open enrollment roles?
				$ActiveClubRoles_array = explode("\n", $ActiveClubRoles);
				$camp_role_valid = false;
				foreach ($ActiveClubRoles_array as $role) {
					$valid = array_search($role, $camp_roles);
					if ($valid !== false){
						$camp_role_valid = true;
					}
				}
				
				if ($camp_role_valid == true && $IsPlayer) {
					$camp_gender_valid = (($gender == "Female" && $camp_gender != "Men") || ($gender == "Male" && $camp_gender != "Women") ? true : false);
					if (empty($camp_ageFrom) && empty($camp_ageTo)) {
						$camp_age_valid = true;
					} elseif (!empty($camp_ageBaseline)) {
						$camp_ageBaseline_php = date_create($camp_ageBaseline);
						$age[$camp_RecordID] = date_diff($camp_ageBaseline_php, $DOB_php);
						$camp_age_valid = $age[$camp_RecordID]->y < $camp_ageFrom ? false : true;
						if ($camp_age_valid) {
							$camp_age_valid = $age[$camp_RecordID]->y > $camp_ageTo ? false : true;
						}
//						echo "age as of " . $camp_ageBaseline . ": " . $age[$camp_RecordID]->y;
					} else {
						$camp_age_valid = $Age < $camp_ageFrom ? false : true;
						if ($camp_age_valid) {
							$camp_age_valid = $Age > $camp_ageTo ? false : true;
						}
					}
				} elseif ($camp_role_valid == true && !$IsPlayer) {
					$camp_gender_valid = true;
					$camp_age_valid = true;
				}
				
				if ($camp_role_valid == true && $camp_gender_valid !== false && $camp_age_valid == true &&
					(($JV_valid_player && !$Varsity_valid_camp) || ($Varsity_valid_player && !$JV_valid_camp) || $SkipAgeTest)
				) {
					echo "<div class='row-divider row-divider-color' style='margin-left: 3em; overflow: auto;'>

						<div style='display: inline-block; float: left;'>
						
						<fieldset class='field' style='margin-right: 2px; padding-right: 0;'>
							<input ID='Interested[" . $camp_RecordID . "]' class='radio' name='InterestedInRegistration[" . $camp_RecordID . "]' type='checkbox' value='" . $camp_ID . "' title='Check here if you want to be added to the invite list for this camp'";
					if ($camp_registeredStatus) {
						echo " checked='checked'";
					}
					echo " />
							<label for='Interested[" . $camp_RecordID . "]' class='radio'>I am interested in attending this Camp as a</label>
						</fieldset>
						
						<select name='InterestedInRegistrationRole[" . $camp_RecordID . "]' size='1' title='Camp Role'>
							<option value=''></option>";
							foreach ($CampRole_values as $campRole_value) {
								echo "<option value='" . $campRole_value . "' " . ($campRole_value == $SelectedCampRole ? "selected='selected'>" : ">") . $campRole_value . "</option>";
							}
					echo "
						</select>
						
						</div>
						<div class='row' style='float: left;'>
						<fieldset class='field'>
						<legend>Camp Name</legend>
							" . $camp_name . "
						</fieldset>
						
						<fieldset class='field' style='margin-right: .5em;'>
						<legend>Venue</legend>
							" . (empty($camp_venue) ? '-' : $camp_venue) . "
						</fieldset>
						</div>
						
						<div class='row' style='float: left;'>
						<fieldset class='field'>
						<legend>Start Date</legend>
							" . (empty($camp_startDate) ? '-' : $camp_startDate) . "
						</fieldset>
						
						<fieldset class='field'>
						<legend>End Date</legend>
							" . (empty($camp_endDate) ? '-' : $camp_endDate) . "
						</fieldset>
						
						<fieldset class='field' style='margin-right: .5em;'>
						<legend>Registration Expiration</legend>
							" . (empty($camp_expiration) ? '-' : $camp_expiration) . "
						</fieldset>
						</div>
						
						<div class='row' style='clear: left'>
						<fieldset class='field' style='margin-right: .5em;'>
						<legend>Description</legend>
							" . (empty($camp_description) ? '-' : $camp_description) . "
						</fieldset>
						</div>
						
						</div>";
				}
			}
			
		} else {
			echo "<p>There are no camps that have open enrollment at this time.</p>";
		}
		?>

	</fieldset>
	<input type="submit" name="APPLY" value="APPLY" class="submit buy" id="Submit_Button"/>
	<input type="hidden" name="submitted-camp" value="true"/>
</form>


