<?php
/**
 * Created by PhpStorm.
 * User: aewerdt
 * Date: 1/19/2017
 * Time: 9:42 AM
 */

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && ($RegistrationSubmitted2 || $NewClubMembership)): ?>
	<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
		<tr>
			<td>Your request could not be processed due to the following problems:
				<p style="color: red"><i>
						<?php echo $fail; ?>
					</i></p>
			</td>
		</tr>
	</table>
<?php endif; ?>
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
	<li>Only clubs that have successfully registered are listed. Talk to your head coach if you don't see your club listed.</li>
	<li>Select the 'At Large' club if you don't currently belong to a club.</li>
</ul>

<div class="mt-10"></div>

<form id="MemberForm" action="body.php<?php if ($EditingMemberProfile) { echo "?ID=" . $ID_Personnel; }?>" method="post" enctype="multipart/form-data">

	<div class="groupheader">Registration Types</div>
	<div class="groupbody">
		
		<?php
		if ($ActiveClubMembershipCount > 0) {
			echo "
					<div class='input' style='border-top: none;'>
						<label class='top'>Your Club Membership(s)&nbsp;<span
									style='font-weight: 100'>&nbsp;(Please select the circle to identify your Primary Club)</span></label>
						<div style='max-height: 800px; overflow-y: auto;'> <!-- scrollbar --> ";
			
			
			$ActiveMembershipRoles = array();
			foreach ($ActiveMembership_records as $ActiveMembership_record) {
				if ($ActiveMembership_record->getField('Invitation_flag') != "1") {
					$ActiveMembership_RecordID = $ActiveMembership_record->getRecordID();
					$ActiveMembership_ID_Club = $ActiveMembership_record->getField('ID_Club');
					$ActiveMembership_PrimaryFlag = $ActiveMembership_record->getField('Primary_flag');
					$ActiveMembership_PrimaryHistory .= $ActiveMembership_PrimaryFlag == "1" ? $ActiveMembership_RecordID : "";
					$ActiveMembership[$ActiveMembership_RecordID]['Role'] = $ActiveMembership_record->getField('Role');
					$ActiveMembership_Role = $ActiveMembership_record->getField('Role');
					$ActiveMembershipRoles[$ActiveMembership_Role][] .= $ActiveMembership[$ActiveMembership_RecordID]['Role']; // needed for logic dependant on doing something once per role type
					$ActiveMembership[$ActiveMembership_RecordID]['StartDate'] = $ActiveMembership_record->getField('StartDate');
					$ActiveMembership[$ActiveMembership_RecordID]['EndDate'] = $ActiveMembership_record->getField('EndDate');
					$ActiveMembership_ClubName = $ActiveMembership_record->getField('c_ClubName');
					$ActiveMembership_PaymentDate = $ActiveMembership_record->getField('Personnel__ClubMembership__Payment.USARMembership::paymentDate');
					
					echo "<div class='row-divider row-divider-color' style='margin-left: 3em;'>

							<div class='row'>
							<fieldset class='field'>
							<legend>Primary</legend>
								<input name='ActiveMembership_UpdatePrimary' type='radio' value='" . $ActiveMembership_RecordID . "'
								 title='Select the one record that is your Primary Club' " . ($ActiveMembership_PrimaryFlag == 1 ? "checked='checked'" : "") . " />
							</fieldset>
							
							<fieldset class='field' style='margin-right: .5em;'>
							<legend>Name</legend>
								" . $ActiveMembership_ClubName . "
							</fieldset>
							</div>
							
							<div class='row'>
							<fieldset class='field' style='width: 8em'>
							<legend>Role</legend>
								" . $ActiveMembership[$ActiveMembership_RecordID]['Role'] . "
							</fieldset>
							
							<fieldset class='field'>
							<legend>Start Date</legend>";
								if ($ActiveMembership[$ActiveMembership_RecordID]['StartDate'] == "") {
									echo "<input class='alpha50 Date-80-1' type='text' name='ActiveMembership_Update[" . $ActiveMembership_RecordID . "]['StartDate']' title='The Date You Joined this Club' value='' />";
								} else {
									echo $ActiveMembership[$ActiveMembership_RecordID]['StartDate'];
								}

									echo "
							</fieldset>
							
							<fieldset class='field' style='margin-right: .5em;'>
							<legend id='EndDateLegend-" . $ActiveMembership_ID_Club . "' class='hidden'>End Date</legend>
								<input id='EndDate-" . $ActiveMembership_ID_Club . "' class='alpha50 Date-80-1 hidden' type='text' name='ActiveMembership_Update[" . $ActiveMembership_RecordID . "][EndDate]' title='The Date You Left this Club' value='" . $ActiveMembership[$ActiveMembership_RecordID]['EndDate'] . "' />
							</fieldset>
							<button id='" . $ActiveMembership_ID_Club . "' class='btn btn-primary RemoveMembership_Button' style='margin: 2px 0 2px 2em; vertical-align: middle;' type='button'>Remove Membership</button>
							<button id='renew-" . $ActiveMembership_ID_Club . "' class='btn btn-primary RenewMembership_Button' style='margin: 2px 0 2px 2em; vertical-align: middle;' type='button'>Renew</button>
							<input type='hidden' name='ActiveMembership_Remove[" . $ActiveMembership_RecordID . "]' id='RemoveMembership-" . $ActiveMembership_ID_Club . "'/>
							<input type='hidden' name='ActiveMembership_Renew[" . $ActiveMembership_RecordID . "]' id='RenewMembership-" . $ActiveMembership_ID_Club . "'/>
							</div>
							
							</div>";
				}
			}
			echo "
						</div> <!-- /scrollbar -->
					</div> <!-- /input -->";
		}
		
		$_SESSION['ActiveMembership_Original'] = isset($ActiveMembership) ? $ActiveMembership : "";
		$_SESSION['ActiveMembershipPrimaryHistory_Original'] = isset($ActiveMembership_PrimaryHistory) ? $ActiveMembership_PrimaryHistory : "";
		?>
		
<!--		--><?php //if ($ActiveClubMembershipCount > 0) { ?>
<!--			<div class='input'>-->
<!--				<label-->
<!--						class='top --><?php //echo($RegistrationSubmitted2 && !$Renew_Admin && !$Renew_Coach && !$Renew_Medical && !$Renew_Player && !$Renew_Referee && !$NewClubMembership ? 'missing' : ''); ?><!--'>-->
<!--					Renewal Choices</label>-->
<!--				--><?php //if (count($ActiveMembershipRoles['Player']) > 0) { ?>
<!--					<div class='row-divider row-divider-color'>-->
<!--						<button id='renew-player' class='btn btn-primary' style='margin: 2px 1em 2px 1em; vertical-align: bottom; width: 20em;'-->
<!--								  type='button'>-->
<!--							Renew Player Membership-->
<!--						</button>-->
<!--						<div style="display: inline-block">-->
<!--							--><?php //echo "Last Renewal Date: " . $LastRegistrationDate_Player . $RegistrationStatus_Player . "<br />Renew to Activate Your Membership Until: " . $NextRegistrationDate_Player; ?>
<!--						</div>-->
<!--					</div>-->
<!--				--><?php //}
//				if (count($ActiveMembershipRoles['Coach']) > 0) { ?>
<!--					<div class='row-divider row-divider-color'>-->
<!--						<button id='renew-coach' class='btn btn-primary' style='margin: 2px 1em 2px 1em; vertical-align: bottom; width: 20em;'-->
<!--								  type='button'>-->
<!--							Renew Coach Membership-->
<!--						</button>-->
<!--						<div style="display: inline-block">-->
<!--							--><?php //echo "Last Renewal Date: " . $LastRegistrationDate_Coach . $RegistrationStatus_Coach . "<br />Renew to Activate Your Membership Until: " . $NextRegistrationDate_Coach; ?>
<!--						</div>-->
<!--					</div>-->
<!--				--><?php //}
//				if (count($ActiveMembershipRoles['Medical']) > 0) { ?>
<!--					<div class='row-divider row-divider-color'>-->
<!--						<button id='renew-medical' class='btn btn-primary' style='margin: 2px 1em 2px 1em; vertical-align: bottom; width: 20em;'-->
<!--								  type='button'>-->
<!--							Renew Medical Membership-->
<!--						</button>-->
<!--						<div style="display: inline-block">-->
<!--							--><?php //echo "Last Renewal Date: " . $LastRegistrationDate_Medical . $RegistrationStatus_Medical . "<br />Renew to Activate Your Membership Until: " . $NextRegistrationDate_Medical; ?>
<!--						</div>-->
<!--					</div>-->
<!--				--><?php //}
//				if (count($ActiveMembershipRoles['Referee']) > 0) { ?>
<!--					<div class='row-divider row-divider-color'>-->
<!--						<button id='renew-referee' class='btn btn-primary' style='margin: 2px 1em 2px 1em; vertical-align: bottom; width: 20em;'-->
<!--								  type='button'>-->
<!--							Renew Referee Membership-->
<!--						</button>-->
<!--						<div style="display: inline-block">-->
<!--							--><?php //echo "Last Renewal Date: " . $LastRegistrationDate_Referee . $RegistrationStatus_Referee . "<br />Renew to Activate Your Membership Until: " . $NextRegistrationDate_Referee; ?>
<!--						</div>-->
<!--					</div>-->
<!--				--><?php //}
//				if (count($ActiveMembershipRoles['Admin/Manager']) > 0) { ?>
<!--					<div class='row-divider row-divider-color'>-->
<!--						<button id='renew-admin' class='btn btn-primary' style='margin: 2px 1em 2px 1em; vertical-align: bottom; width: 20em;'-->
<!--								  type='button'>-->
<!--							Renew Admin/Manager Membership-->
<!--						</button>-->
<!--						<div style="display: inline-block">-->
<!--							--><?php //echo "Last Renewal Date: " . $LastRegistrationDate_Admin . $RegistrationStatus_Admin . "<br />Renew to Activate Your Membership Until: " . $NextRegistrationDate_Admin; ?>
<!--						</div>-->
<!--					</div>-->
<!--				--><?php //} ?>
<!--			</div>-->
<!--		--><?php //} ?>

		<div class="input" <?php if ($ActiveClubMembershipCount == 0) {
			echo "style='border-top: none'";
		} ?> >
			<label class="top">New Club Membership
				<?php if ($related_ClubMembership_count == 0) {
					echo "<span class='mandatoryFailed'>REQUIRED</span>";
				} ?>
			</label>

			<div <?php if ($ActiveClubMembershipCount == 0 && (empty($ID_Club) || empty($ClubRole) || empty($StartDate))) {
				echo "class='missing' style='padding: 8px;'";
			} ?>>

				<div style='margin-left: 3em;'>
					<fieldset class="field">
						<legend>Club Name*</legend>
						<div class="<?php echo(empty($ID_Club) && $NewClubMembership ? 'missing' : ''); ?>" style="display: inline-block;">
							<select name="ID_Club" size="1" class="primaryClub select2"
									  title="The Rugby Club you play for. If it isn't listed, select 000.">
								<option value="">&nbsp;</option>
								<?php
								foreach ($clubValues as $key => $clubValue) {
									echo "<option value='" . $key . "'" . ($ID_Club == $key ? "selected='selected'>" : ">") . $clubValue . "</option>";
								}
								?>
							</select>
						</div>
					</fieldset>
				</div>

				<div class="row" style='margin-left: 3em;'>
					<fieldset class="field">
						<legend>Club Role*</legend>
						<div class="<?php echo(empty($ClubRole) && $NewClubMembership ? 'missing' : ''); ?>" style="display: inline-block;">
							<select name="ClubRole" size="1" title="What is your role in this club?">
								<?php if ($U18) { ?>
									<option value="Player" selected="selected">Player</option>
								<?php } else {
									echo "<option value=''>&nbsp;</option>";
									foreach ($clubRoleValues as $clubRoleValue) {
										echo "<option value='" . $clubRoleValue . "'" . ($ClubRole == $clubRoleValue ? "selected='selected'>" : ">") . $clubRoleValue . "</option>";
									}
								}
								?>
							</select>
						</div>
					</fieldset>

					<fieldset class="field">
						<legend>Start Date*</legend>
						<input type="text" name="StartDate" title="Your Date of Birth"
							<?php if ((empty($StartDate) || $StartDate == date('m/d/Y')) && $NewClubMembership) {
								echo "class='Date-80-1 missing'";
							} else {
								echo "class='Date-80-1' value='" . $StartDatesave . "'";
							} ?>/>
					</fieldset>

					<fieldset class="field">
						<legend>Primary Rugby Club</legend>
						<input name="Primary_flag" type="checkbox" value="1" title="Check here if this is your primary club"/>
					</fieldset>

					<button id="Add_Membership" class='btn btn-primary' style='margin: 2px 0 2px 2em; vertical-align: bottom;' type='submit'
							  formaction='body.php?NewClubMembership=true<?php if ($EditingMemberProfile) { echo "&ID=" . $ID_Personnel; }?>'>Add
					</button>
				</div>

			</div>
		</div>

	</div>

	<input type="submit" name="Back" value="Back" class="submit buy Processing" style="margin-right: 1em;"/>
	<input type="submit" name="Next" value="Next" class="submit buy Processing"/>
	<input type="hidden" name="submitted-registration2" value="true"/>
<!--	<input type="hidden" name="register-as-player" id="register-as-player" value="false"/>-->
<!--	<input type="hidden" name="register-as-coach" id="register-as-coach" value="false"/>-->
<!--	<input type="hidden" name="register-as-medical" id="register-as-medical" value="false"/>-->
<!--	<input type="hidden" name="register-as-referee" id="register-as-referee" value="false"/>-->
<!--	<input type="hidden" name="register-as-admin" id="register-as-admin" value="false"/>-->

</form>

<div id="Add_Membership_Dialog" title="Adding Membership">Please wait while a new membership record is created.</div>

<script>
    $('.RemoveMembership_Button').click(function () {
        var ID_Club_Remove;
        var button_text = $(this).html();
        var d = new Date();
        var today = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
        ID_Club_Remove = this.id;
        $(this).toggleClass('entypo-cancel');
        if ($(this).is('.entypo-cancel')) {
            $(this).html('&nbsp;&nbsp;' + button_text);
            $(this).css('background-color', 'darkred');
            $("#RemoveMembership-" + ID_Club_Remove).val(ID_Club_Remove);
            if ($("#EndDate-" + ID_Club_Remove).val() == "") {
                $("#EndDate-" + ID_Club_Remove).val(today);
            }
            //Reset Renew button/value back to default
            $('#renew-' + ID_Club_Remove).css('background-color', '');
            $('#renew-' + ID_Club_Remove).removeClass('entypo-check');
            $("#RemoveMembership-" + ID_Club_Remove).val("");
        } else {
            $(this).html(button_text.replace(/&nbsp;/g, ""));
            $(this).css('background-color', '');
            $("#RemoveMembership-" + ID_Club_Remove).val("");
            $("#EndDate-" + ID_Club_Remove).val("");
        }
        $('#EndDate-' + ID_Club_Remove).toggleClass('hidden');
        $('#EndDateLegend-' + ID_Club_Remove).toggleClass('hidden');
    });
    $('.RenewMembership_Button').click(function () {
        var ID_Club_Renew;
        var button_text = $(this).html();
        ID_Club_Renew = this.id;
        ID_Club_Renew = ID_Club_Renew.slice(6);
        $(this).toggleClass('entypo-check');
        if ($(this).is('.entypo-check')) {
            $(this).html('&nbsp;&nbsp;' + button_text);
            $(this).css('background-color', 'green');
            $("#RenewMembership-" + ID_Club_Renew).val(ID_Club_Renew);
            //Reset Remove button/value back to default
            $('#' + ID_Club_Renew).css('background-color', '');
            $('#' + ID_Club_Renew).removeClass('entypo-cancel');
            $("#RemoveMembership-" + ID_Club_Renew).val("");
            $("#EndDate-" + ID_Club_Renew).val("");
            $('#EndDate-' + ID_Club_Renew).addClass('hidden');
            $('#EndDateLegend-' + ID_Club_Renew).addClass('hidden');
        } else {
            $(this).html(button_text.replace(/&nbsp;/g, ""));
            $(this).css('background-color', '');
            $("#RemoveMembership-" + ID_Club_Renew).val("");
        }
    });
//    $('#renew-player').click(function () {
//        var button_text = $(this).html();
//        $(this).toggleClass('entypo-check');
//        if ($(this).is('.entypo-check')) {
//            $(this).html('&nbsp;&nbsp;' + button_text);
//            $(this).css('background-color', 'green');
//            $('#register-as-player').val('true');
//        } else {
//            $(this).html(button_text.replace(/&nbsp;/g, ""));
//            $(this).css('background-color', '');
//            $('#register-as-player').val('false');
//        }
//    });
//    $('#renew-coach').click(function () {
//        var button_text = $(this).html();
//        $(this).toggleClass('entypo-check');
//        if ($(this).is('.entypo-check')) {
//            $(this).html('&nbsp;&nbsp;' + button_text);
//            $(this).css('background-color', 'green');
//            $('#register-as-coach').val('true');
//        } else {
//            $(this).html(button_text.replace(/&nbsp;/g, ""));
//            $(this).css('background-color', '');
//            $('#register-as-coach').val('false');
//        }
//    });
//    $('#renew-medical').click(function () {
//        var button_text = $(this).html();
//        $(this).toggleClass('entypo-check');
//        if ($(this).is('.entypo-check')) {
//            $(this).html('&nbsp;&nbsp;' + button_text);
//            $(this).css('background-color', 'green');
//            $('#register-as-medical').val('true');
//        } else {
//            $(this).html(button_text.replace(/&nbsp;/g, ""));
//            $(this).css('background-color', '');
//            $('#register-as-medical').val('false');
//        }
//    });
//    $('#renew-referee').click(function () {
//        var button_text = $(this).html();
//        $(this).toggleClass('entypo-check');
//        if ($(this).is('.entypo-check')) {
//            $(this).html('&nbsp;&nbsp;' + button_text);
//            $(this).css('background-color', 'green');
//            $('#register-as-referee').val('true');
//        } else {
//            $(this).html(button_text.replace(/&nbsp;/g, ""));
//            $(this).css('background-color', '');
//            $('#register-as-referee').val('false');
//        }
//    });
//    $('#renew-admin').click(function () {
//        var button_text = $(this).html();
//        $(this).toggleClass('entypo-check');
//        if ($(this).is('.entypo-check')) {
//            $(this).html('&nbsp;&nbsp;' + button_text);
//            $(this).css('background-color', 'green');
//            $('#register-as-admin').val('true');
//        } else {
//            $(this).html(button_text.replace(/&nbsp;/g, ""));
//            $(this).css('background-color', '');
//            $('#register-as-admin').val('false');
//        }
//    });
</script>