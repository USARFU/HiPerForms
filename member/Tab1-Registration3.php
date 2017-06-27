<?php

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && ($RegistrationSubmitted3)): ?>
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

<div class="mt-10"></div>

<form action="body.php<?php if ($EditingMemberProfile) { echo "?ID=" . $ID_Personnel; }?>" method="post" enctype="multipart/form-data">

	<div class="groupheader">Background Screening Authorization Form</div>
	<div class="groupbody">
		<?php if (!$Background_Check_Needed) { ?>
			<div class="subheader">No Background Screening needed at this time</div>
		<?php } else { ?>
			<div style="display: inline-block">
				<label class="aaclose">Applicant's Name:</label>
				<?php echo $firstName . " " . $lastName; ?>
			</div>
			<div style="display: inline-block; margin-left: 14em;">
				<label class="aaclose">Date of Birth:</label>
				<?php echo $DOB->format('d/m/Y'); ?>
			</div>

			<div class="row">
				<div>
					<label class="top">Applicant's Present Address:</label>
					<?php echo $homeAddress1 . "<br />"; ?>
					<?php if (!empty($homeAddress2)) {
						echo $homeAddress2 . "<br />";
					} ?>
					<?php echo $City . ", " . $State . "&nbsp;&nbsp;" . $zipCode; ?>
				</div>
			</div>

			<div class="row attention-red" style="text-align: center">
				If you are not a U.S. citizen and do not have a valid social security number, you must contact USA Rugby to complete this
				process.
			</div>

			<div class="input" style="border-top: none">
				<label class="w-12">Social Security Number:</label>
				<input type="number" size="3" name="SSNa" title="First three digits of your social security number."
						 style="width: 4em"/>&nbsp;-&nbsp;
				<input type="number" size="2" name="SSNb" title="Second two digits of your social security number."
						 style="width: 3em"/>&nbsp;-&nbsp;
				<input type="number" size="4" name="SSNc" title="Last three digits of your social security number."
						 style="width: 4em"/>
			</div>

			<div class="subheader">BACKGROUND SCREENING RELEASE</div>
			<br/>
			<p>
				I, <strong><?php echo $firstName . " " . $lastName; ?></strong>, authorize and give consent for the above named organization to
				obtain
				information regarding myself. This includes the following: <b>Social Security Number Verification, Criminal background
					records/information, Drivers license check and Addresses</b>.
			</p>
			<p>
				I authorize this information to be obtained either in writing, electronic transmission or via telephone in connection with my
				employment and/or volunteer application. Such information will be held in confidence in accordance with the organization's
				guidelines. Further, I understand that it is the policy of this organization that any member who participates with youth members
				and/or USA Rugby Sanctioned Events in any capacity, including supervisory personnel, club directors, team representatives,
				coaches, chaperones and trainers shall submit to a background screen immediately upon application.
			</p>
			<p>
				USA Rugby may obtain information about you from a third party consumer reporting agency for background screening purposes. Thus,
				you may be the subject of a "consumer report" which may include information about your character, general reputation, personal
				characteristics, and/or mode of living. These reports may contain information regarding your criminal history, social security
				verification, motor vehicle records ("driving records"), verification of your education or employment history, or other
				background checks.
			</p>
			<p>
				You have the right, upon written request made within a reasonable time, to request whether a consumer report has been run about
				you and to request a copy of your report. These searches will be conducted by SSCI, 1853 Piedmont Road, Suite 100, Marietta, GA
				30066, 866-996-7412, www.ssci2000.com. The scope of this disclosure is all-encompassing, however, allowing USA Rugby to obtain
				from any outside organization all manner of consumer reports throughout the course of your participation to the extent permitted
				by law.
			</p>
			<p>
				Name: <strong><?php echo $firstName . " " . $lastName; ?></strong>&nbsp;&nbsp;Date: <strong><?php echo $today; ?></strong>
			</p>

			<div class="subheader">DISQUALIFIERS</div>
			<br/>
			<p>
				I understand that disqualification from all junior events and/or activities will result if I have been found guilty, pled
				guilty; or pled nolo contendere for criminal convictions for ALL sex offenses, Murder, and Homicide regardless of time limit;
				Felony Violence and Felony Drug offenses in the past 10 years; any misdemeanor violence offences in the past 7 years; any
				multiple misdemeanor drug and alcohol offenses within the past 7 years; or any other crimes against children.
			</p>
			<p>
				Any criminal conviction, finding of guilt, guilty plea or plea of nolo contendere for an offense listed above that occurs after
				the initial background screen has been completed will require the applicant to resubmit for a Background Screen clearance before
				further participating in junior events and/or activities. Falsification of any information on any registration application or
				this form is grounds for membership revocation or denial of membership.
			</p>
			<p>
				A conviction or falsification of information that results in revocation or denial of my registration forfeits all fees paid with
				my registration application.
			</p>
			<p>
				Name: <strong><?php echo $firstName . " " . $lastName; ?></strong>&nbsp;&nbsp;Date: <strong><?php echo $today; ?></strong>
			</p>

			<div class="row attention-red">
				<label class="radio" style="display: block; float: right">
				<input type="checkbox" value="1" name="waiver_background_check" class="radio" style="display: block; float: left"/>
					I have read and understand this agreement in its entirety. I unconditionally
					accept the terms and conditions herein, including but not limited to the authorization and disqualifier language specified in
					the agreement.
				</label>
			</div>
		<?php } ?>
	</div>


	<input type="submit" name="Back" value="Back" class="submit buy Processing" style="margin-right: 1em;"/>
	<?php if (!$Background_Check_Needed) { ?>
		<input type="submit" name="Next" value="Next" class="submit buy Processing"/>
	<?php } else { ?>
		<input type="submit" name="Next" value="Process Background Screening" class="submit buy Processing"/>
		<input type="hidden" name="process-background-screening" value="true"/>
	<?php } ?>
	<input type="hidden" name="submitted-registration3" value="true"/>

</form>