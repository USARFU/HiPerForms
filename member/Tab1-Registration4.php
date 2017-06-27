<?php

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && ($RegistrationSubmitted4)): ?>
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

	<div class="groupheader">USA RUGBY RELEASE OF LIABILITY</div>
	<div class="groupbody">
		<br/>
		<p>
			<strong>I ACKNOWLEDGE THAT BY SIGNING THIS DOCUMENT, I AM AGREEING TO RELEASE THE RELEASED PARTIES FROM LIABILITY. I HAVE THEREFORE
				BEEN ADVISED TO READ THIS DOCUMENT CAREFULLY BEFORE SIGNING IT.</strong>
		</p>
		<p>
			This Participation Agreement and Waiver and Release of Liability is entered into by the undersigned "Participant" in favor of USA
			Rugby, its member unions, clubs, organizations, affiliates, partners, sponsors, vendors, directors, officers, employees,
			volunteers, members, agents, contractors, contracted entities and facilities and the owners and lessors thereof, (hereinafter
			referred to as "USA Rugby" or collectively as the "Released Parties").
		</p>
		<p>
			I understand that participation in USA Rugby activities is a privilege but not a right. In consideration for the privilege of
			participation in USA Rugby activities, I and my Parent/Guardian, if applicable, acknowledge and agree as follows:
		</p>
		<ol>
			<li>
				Participation in the activities of USA Rugby, including but not limited to warm-up, training, practice, games, clinics, travel,
				and social events (referred to herein as the "Activities"), includes participation in a full-contact sport, requires good health
				and fitness and can be <strong>HAZARDOUS AND PRESENT A DANGER TO ME</strong>. I believe I am qualified to participate in the
				Activities, and if at any time I believe the conditions to be unsafe, I will immediately discontinue further participation in
				the Activities
			</li>
			<li>
				Participation in Activities exposes me to <strong>RISKS OF SERIOUS BODILY INJURY, INCLUDING PERMANENT DISABILITY, PARALYSIS AND
					DEATH</strong>. Risks may arise out of contact and/or participation with other participants, spectators, equipment, field,
				facility and/or fixed objects; falls, collisions, rough play, and other mishaps; exposure to adverse weather conditions and/or
				high altitude; flaws and defects in equipment and facilities; irregular field conditions; and negligent field maintenance,
				negligent officiating, negligent coaching and negligent participation. Risks may be caused by my own actions, or inaction, the
				actions or inaction of others participants, the condition of the facilities in which the Activities take place, and/or <strong>THE
					NEGLIGENCE OF THE "RELEASED PARTIES."</strong> There may be other risks and social and economic losses either not known to me
				or not readily foreseeable at this time.
			</li>
			<li>
				<span style="text-decoration: underline">Assumption of the Risks</span>. <strong>I CONSENT TO PARTICIPATION IN THE ACTIVITIES
					AND FULLY ACCEPT AND ASSUME ALL SUCH RISKS AND ALL RESPONSIBILITY FOR LOSSES, COSTS, AND DAMAGES</strong> incurred as a
				result of such participation.
			</li>
			<li>
				<span style="text-decoration: underline">Waiver and Release of Liability</span>. In consideration for the privilege of my
				participation in the Activities, I hereby <strong>RELEASE, DISCHARGE, COVENANT NOT TO SUE, AND AGREE TO INDEMNIFY AND SAVE AND
					HOLD
					HARMLESS RELEASED PARTIES</strong> from any and all liability, demands, losses, medical expenses, lost opportunities, damages
				or
				attorneys fees and costs stemming from any or all claims for negligence, expressed or implied warranty, contribution, and
				indemnity, and/or claims of negligent rescue operations, first aid, and emergency care, to the broadest extent permitted by
				applicable law, including C.R.S. � 13-22-107 if I am a Minor, suffered by me and incurred on my account with respect to my
				personal injury and other injury or harm, disability, and/or death, or property damage, arising directly or indirectly from my
				participation in Activities, as caused or alleged to be caused in whole or in part by the Released Parties or any of them, and
				further agree that if, despite this Release, I or any other person makes a claim on my behalf against any of the Released
				Parties, unless, and to the extent, prohibited by law, <strong>I AND MY PARENT/GUARDIAN, IF APPLICABLE, WILL INDEMNIFY, SAVE AND
					HOLD
					HARMLESS EACH OF THE RELEASED PARTIES FROM ANY LIABILITY, LITIGATION EXPENSES, ATTORNEY FEES, LOSSES, DAMAGES OR COSTS ANY
					MAY
					INCUR AS THE RESULT OF ANY SUCH CLAIM, WHETHER ASSERTED BY ME, MY PARENT/GUARDIAN, IF APPLICABLE, OR ANOTHER PERSON</strong>.
			</li>
			<li>
				<span style="text-decoration: underline">Governing Law, Venue and Jurisdiction</span>: I understand and agree that this document
				is intended to be as broad and inclusive as permitted under applicable law and shall be governed by Colorado law. In the event
				of a dispute, the exclusive venue and jurisdiction for any lawsuit arising out of such dispute shall be the state court of
				Boulder County, or the federal courts located in Denver, Colorado.
			</li>
			<li>
				<span style="text-decoration: underline">Severability</span>: If any provision of this document is determined to be invalid for
				any reason, such invalidity shall not affect the validity of any of the other provisions, which other provisions shall remain in
				full force and effect as if this document had been executed with the invalid provision eliminated.
			</li>
		</ol>

		<p><strong>
				I HEREBY CERTIFY THAT I HAVE COMPLETELY READ AND UNDERSTAND THIS AGREEMENT AND ITS TERMS. PRIOR TO SIGNING THIS AGREEMENT, I
				HAVE HAD THE OPPORTUNITY TO ASK ANY QUESTIONS ABOUT THIS AGREEMENT. I AM AWARE, BY SIGNING THIS AGREEMENT, THAT I ASSUME ALL
				RISKS AND WAIVE AND RELEASE CERTAIN RIGHTS THAT I AND EACH OF MY HEIRS, NEXT OF KIN, FAMILY, RELATIVES, GUARDIANS, CONSERVATORS,
				EXECUTORS, ADMINISTRATORS, TRUSTEES AND ASSIGNS MAY HAVE AGAINST RELEASED PARTIES. THIS RELEASE SHALL BE EFFECTIVE AND BINDING
				UPON ME. I FURTHER REPRESENT THAT I AM AT LEAST 18 YEARS OF AGE OR, IF I AM UNDER THE AGE OF 18, THAT MY PARENT OR GUARDIAN HAS
				SIGNED THIS FORM IN THE "CONSENT" SECTION BELOW.
			</strong></p>

		<div class="row attention-red" style="text-align: center;">
			<input id="waiver-release-liability" type="checkbox" value="1" name="waiver_release_liability" class="radio"/>
			<label for="waiver-release-liability" class="radio" style="vertical-align: bottom">
				I Agree
			</label>
		</div>
		
		<?php if ($U18) { ?>
			<div class="subheader">CONSENT OF PARENT/GUARDIAN FOR PARTICIPANTS UNDER 18</div>
			<br/>
			<p><strong>
					I REPRESENT THAT I AM THE PARENT/GUARDIAN OF THE UNDERSIGNED PARTICIPANT, WHO IS UNDER 18 YEARS OF AGE. I HAVE READ THE ABOVE
					RELEASE AND AM FULLY FAMILIAR WITH THE CONTENTS THEREOF. IN CONSIDERATION FOR ALLOWING MY CHILD/WARD TO PARTICIPATE IN
					ACTIVITIES, I HEREBY CONSENT TO THE FOREGOING ON BEHALF OF MY CHILD/WARD AND AGREE THAT THIS RELEASE SHALL BE BINDING UPON
					ME, MY CHILD/WARD, HEIRS, LEGAL REPRESENTATIVES AND ASSIGNS.
				</strong></p>
			<p><strong>Name of Minor: </strong> <?php echo $firstName . " " . $lastName; ?></p>

			<div class="row attention-red" style="text-align: center">
				<label for="waiver-release-liability-parent" class="aaclose" style="vertical-align: middle; float: none">
					Parent/Guardian Name:
				</label>
				<input id="waiver-release-liability-parent" type="text" name="waiver_release_liability_parent" size="24"/>
			</div>
		<?php } ?>

		<div class="subheader">USA Rugby Rules Acknowledgement</div>
		<br/>
		<ol>
			<li>
				I understand and agree to abide by all World Rugby Board, USA Rugby, territorial and local area union rules and regulations,
				including the arbitration procedures therein, for any dispute regarding my eligibility or right to participate in, USA
				Rugby-sponsored and USA Rugby-sanctioned activities and events, as set forth in the Bylaws of USA Rugby, as they are amended on
				a periodic basis, which I understand are available on the USA Rugby website (<a
						href="http://www.usarugby.org">www.usarugby.org</a>).
			</li>
			<li>
				I affirm that I am not suspended or banned from play or participation by any club, local area union, territorial union, or
				national union, and I authorize USA Rugby to verify my citizenship status with the appropriate governmental agencies.
			</li>
			<li>
				I am aware that USA Rugby has the right to revoke my membership registration and therefore my eligibility to play or coach, in
				the event of any violation of the aforementioned statement.
			</li>
		</ol>
		<p><strong>
				I HAVE READ THIS ACKNOWLEDGMENT AND FULLY UNDERSTAND ITS TERMS, AND UNDERSTAND THAT I HAVE GIVEN UP SUBSTANTIAL RIGHTS BY
				SIGNING IT. IN CONSIDERATION FOR THE PRIVILEGE OF PARTICIPATION IN USA RUGBY ACTIVITIES, I FURTHER REPRESENT THAT I AM AT LEAST
				18 YEARS OF AGE OR, IF I AM UNDER THE AGE OF 18, THAT MY PARENT/ GUARDIAN HAS SIGNED THIS FORM IN THE SECTION BELOW.
			</strong></p>

		<div class="row attention-red" style="text-align: center">
			<input id="waiver-rules" type="checkbox" value="1" name="waiver_rules" class="radio"/>
			<label for="waiver-rules" class="radio" style="vertical-align: bottom">
				I Agree
			</label>
		</div>
		
		<?php if ($U18) { ?>
			<div class="subheader">CONSENT OF PARENT/GUARDIAN FOR PARTICIPANTS UNDER 18</div>
			<br/>
			<p><strong>
					I REPRESENT THAT I AM THE PARENT/GUARDIAN OF THE UNDERSIGNED PARTICIPANT, WHO IS UNDER 18 YEARS OF AGE. I SIGN THIS DOCUMENT
					VOLUNTARILY AND WITH FULL UNDERSTANDING OF ITS TERMS AND LEGAL SIGNIFICANCE. I ATTEST THAT, IF I AM THE SOLE PARENT/GUARDIAN
					SIGNING BELOW, MY SIGNATURE IS SUFFICIENT TO CONSENT TO THE PARTICIPATION OF THE MINOR IN THE ACTIVITIES AND TO ENTER INTO
					THIS AGREEMENT ON BEHALF OF THE MINOR.
				</strong></p>
			<p><strong>Name of Minor: </strong> <?php echo $firstName . " " . $lastName; ?></p>

			<div class="row attention-red" style="text-align: center">
				<label for="waiver-rules-parent" class="aaclose" style="vertical-align: middle; float: none">
					Parent/Guardian Name:
				</label>
				<input id="waiver-rules-parent" type="text" name="waiver_rules_parent" size="24"/>
			</div>
		<?php } ?>
	</div>


	<input type="submit" name="Back" value="Back" class="submit buy Processing" style="margin-right: 1em;"/>
	<input type="submit" name="Next" value="Next" class="submit buy Processing"/>
	<input type="hidden" name="submitted-registration4" value="true"/>

</form>