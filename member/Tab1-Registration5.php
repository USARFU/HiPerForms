<?php

//<!-- Add table to display any error messages from submitted form. -->
if (!empty($fail) && ($RegistrationSubmitted5)): ?>
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

	<div class="groupheader">PAYMENT</div>
	<div class="groupbody">
		<br/>
		<?php
		foreach ($Renewal_Types as $type => $active) {
			if ($active) {
				?>
				<div class='row-divider row-divider-color'>
					<div class="input" style="border-top: none">
						<label style="width: 50%">Membership Type</label>
						<?php echo $type; ?>
					</div>
					<div class="input" style="border-top: none">
						<label style="width: 50%">Membership Price - USA Rugby</label>
						$29.00
					</div>
					<div class="input" style="border-top: none">
						<label style="width: 50%">Membership Price - Florida Youth Rugby</label>
						$20.00
					</div>
				</div>
			<?php } } ?>

			<div class='row-divider row-divider-color'>
				<div class="input" style="border-top: none">
					<label style="width: 50%" for="donation-amount">
						Would you also like to donate to USA Rugby?<br />
						<small>Your donation is a tax-deductible charitable contribution to the extent allowed by law.</small>
					</label>
					$<input id="donation-amount" type="number" name="Donation_amount" value="<?php recallText($Donation_amount, "no"); ?>" style="width: 6em;"/>
				</div>
			</div>

		<div class="input">
			<label style="width: 50%" for="fee-amount">
				Processing Fee:
			</label>
			$<div id="fee" style="display: inline"><?php echo $Fee; ?></div>
		</div>
			
		<div class="input">
			<label style="width: 50%" for="total-amount">
				Total:
			</label>
			$<div id="total" style="display: inline; font-weight: bolder"><?php echo $Total; ?></div>
		</div>

		<div class="subheader">Credit Card Information</div>
		<br/>

		<fieldset class="payment">
			<div>
				<label class="payment">Credit Card #</label>
				<input type="text" class="text" size="18" name="card_num"
						 title="Credit Card number, no spaces." <?php recallText((empty($card_num) ? "" : $card_num), "yes"); ?> />
			</div>
			<div>
				<label class="payment">Month</label>
				<select name="month" class="text" title="Month the credit card expires." <?php if (empty($month)) {
					$month_a = " ";
					echo 'class="missing"';
				} else {
					$month_a = $month;
				} ?> >
					<option value="">&nbsp;</option>
					<?php
					for ($i = 1; $i <= 12; $i++) {
						?>
						<option value="<?php echo $i; ?>" <?php if ($month_a == $i) {
							echo "selected=\"selected\"";
						} ?>><?php echo $i; ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div>
				<label class="payment">Year</label>
				<select name="year" class="text" title="Year the credit card expires." <?php if (empty($year)) {
					$year_a = " ";
					echo 'class="missing"';
				} else {
					$year_a = $year;
				} ?> >
					<option value="">&nbsp;</option>
					<?php
					$start_year = date("Y");
					$final_year = $start_year + 16;
					for ($i = $start_year; $i <= $final_year; $i++) {
						?>
						<option value="<?php echo $i; ?>" <?php if ($year_a == $i) {
							echo "selected=\"selected\"";
						} ?>><?php echo $i; ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div>
				<label class="payment">CCV</label>
				<input type="text" class="text" size="4" name="card_code"
						 title="Additional 3 or 4 digit code on the credit card." <?php recallText((empty($card_code) ? "" : $card_code), "yes"); ?> />
			</div>
		</fieldset>
		<fieldset class="payment">
			<div>
				<label class="payment">First Name</label>
				<input type="text" class="text" size="15" name="first_name"
						 title="First name of the credit card holder." <?php recallText((empty($first_name) ? "" : $first_name), "yes"); ?> />
			</div>
			<div>
				<label class="payment">Last Name</label>
				<input type="text" class="text" size="14" name="last_name"
						 title="Last name of the credit card holder." <?php recallText((empty($last_name) ? "" : $last_name), "yes"); ?> />
			</div>
		</fieldset>
		<fieldset class="payment">
			<div>
				<label class="payment">Address</label>
				<input type="text" class="text" size="26" name="address"
						 title="Billing street address of the credit card." <?php recallText((empty($address) ? "" : $address), "yes"); ?> />
			</div>
			<div>
				<label class="payment">City</label>
				<input type="text" class="text" size="20" name="city"
						 title="Billing city of the credit card." <?php recallText((empty($city) ? "" : $city), "yes"); ?> />
			</div>
		</fieldset>
		<fieldset class="payment">
			<div style="margin-right:2em">
				<label class="payment">State/Province</label>
				<select name="state" size="1" class="text"
						  title="Billing State or Province of the credit card." <?php if (empty($state)) {
					echo 'class="missing"';
				} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($stateValues as $value) {
						echo "<option value='" . $value . "'" . ($state == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
			<div>
				<label class="payment">Zip/Postal Code</label>
				<input type="text" class="text" size="9" name="zip"
						 title="Billing Postal Code of the credit card." <?php recallText((empty($zip) ? "" : $zip), "yes"); ?> />
			</div>
		</fieldset>
		<fieldset class="payment">
			<div>
				<label class="payment">E-mail Receipt To:</label>
				<input type="text" class="text" size="26" name="email"
						 title="The e-mail address you would like the receipt sent to." <?php recallText((empty($email) ? "" : $email), "no"); ?> />
			</div>
		</fieldset>

		<div class="row attention-red" style="text-align: center">
			<strong>All memberships are non-refundable and non-transferable.</strong>
		</div>

	</div>


	<input type="submit" name="Back" value="Back" class="submit buy Processing" style="margin-right: 1em;"/>
	<input type="submit" name="Next" value="Submit" class="submit buy Processing"/>
	<input type="hidden" name="submitted-registration5" value="true"/>

</form>

<script>
    $('#donation-amount').focusout(function () {
        var subtotal = <?php echo $Subtotal; ?>;
        subtotal = Number(subtotal);
        var donation = $(this).val();
        donation = Number(donation);
        var fee = (subtotal + donation) * .029 +.3;
        fee = fee.toFixed(2);
        var total = parseFloat(subtotal + donation + Number(fee)).toFixed(2);
        $('#fee').html(fee);
        $('#total').html(total);
    });
</script>