<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Support the USA Rugby U20 team</title>
	<link rel="stylesheet" type="text/css" href="include/WebForms.css" media="screen"/>

	<script src="include/script/jquery/jquery.min.js"></script>

	<!-- select2 js library for searchable drop down controls -->
	<link href="include/script/select2/css/select2.min.css" rel="stylesheet"/>
	<script src="include/script/select2/js/select2.min.js"></script>

	<link href="include/bootstrap-modified.css" rel="stylesheet"/>

	<!-- Error Codes 261-75. -->
	<!--Submit data if criteria is met-->
	<?php
	date_default_timezone_set('America/New_York');
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include "$root/include/dbaccess-membership.php";
	include "$root/include/functions.php";
	
	require 'Authorize.net-1.9.0/vendor/autoload.php';
	require_once 'Authorize.net-1.9.0/phpunit_config.php';
	
	$layout =& $fm->getLayout('SupportUSAU20s');
	
	## Redirect to HTTPS if necessary ###########################################
	if (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") {
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}
	#############################################################################
	
	$fail = "";
	
	$card_num = (isset ($_POST['card_num']) ? fix_string($_POST['card_num']) : "");
	
	$exp_date = "";
	$month = (isset ($_POST['month']) ? fix_string($_POST['month']) : "");
	$year = (isset ($_POST['year']) ? fix_string($_POST['year']) : "");
	
	if (!empty($month) && !empty($year)) {
		$exp_date_a = sprintf("%04d-%02d", $year, $month);
		$exp_date = fix_string($exp_date_a);
	}
	
	$ID_Adoptee = (isset ($_POST['ID_Adoptee']) ? fix_string($_POST['ID_Adoptee']) : "");
	$ID_Adoptee = ($ID_Adoptee == "Greatest Need" ? "" : $ID_Adoptee); //blank out ID_Personnel if Greatest Need is selected
	$AdopteeName = (isset ($_POST['AdopteeName']) ? fix_string($_POST['AdopteeName']) : "");
	
	$PaymentSelector = (isset ($_POST['PaymentSelector']) ? fix_string($_POST['PaymentSelector']) : "CreditCard");
	
	$card_code = (isset ($_POST['card_code']) ? fix_string($_POST['card_code']) : "");
	$first_name = (isset ($_POST['first_name']) ? fix_string($_POST['first_name']) : "");
	$last_name = (isset ($_POST['last_name']) ? fix_string($_POST['last_name']) : "");
	$address = (isset ($_POST['address']) ? fix_string($_POST['address']) : "");
	$city = (isset ($_POST['city']) ? fix_string($_POST['city']) : "");
	$state = (isset ($_POST['state']) ? fix_string($_POST['state']) : "");
	$zip = (isset ($_POST['zip']) ? fix_string($_POST['zip']) : "");
	$email = (isset ($_POST['email']) ? fix_string($_POST['email']) : "");
	$CheckNbr = isset ($_POST['CheckNbr']) ? fix_string($_POST['CheckNbr']) : "";
	$amount = isset ($_POST['Amount']) ? fix_string($_POST['Amount']) : "";
	
	$BudgetClass = "MJAA - Canadian Tour";
	$ID_Club = "70B49A41-A06E-CF40-A320-620FFA9D8D76"; //Associate payment to the USA U20s, Men club
	
	if ($PaymentSelector == "CreditCard") {
		$description = $BudgetClass . ", for " . $AdopteeName;
	} elseif ($PaymentSelector == "Check") {
		$description = $BudgetClass . " Check #" . $CheckNbr . ", for " . $AdopteeName;
	}
	
	$description .= " by sponsor: " . $first_name . " " . $last_name . " (" . $email . ")\n";
	$description .= $address . ", " . $city . ", " . $state . "  " . $zip;
	
	// Check for CC error //
	if (isset($_GET['response_reason_text'])) {
		$fail .= htmlentities($_GET['response_reason_text']);
	}
	if (isset($_GET['error'])) {
		$fail .= htmlentities($_GET['error']);
	}
	
	if (isset($_POST['respondent_exists'])) {
		
		if ($PaymentSelector == "CreditCard") {
			$fail .= validate_cardType($card_num);
			$fail .= validate_cardExp($month, $year);
			$fail .= validate_CCV($card_num, $card_code);
			$fail .= validate_empty_field($first_name, "First Name");
			$fail .= validate_empty_field($last_name, "Last Name");
			$fail .= validate_empty_field($address, "Street");
			$fail .= validate_empty_field($city, "City");
			$fail .= validate_empty_field($state, "State");
			$fail .= validate_zip($zip);
		}
		
		$fail .= validate_empty_field($amount, "Amount");
		
		if (empty($fail)) {
			
			######################################################################################
			
			if ($PaymentSelector == "CreditCard") {
				// Process the transaction using the AIM API
				$transaction = new AuthorizeNetAIM;
				$transaction->setFields(
					array(
						"card_num" => $card_num,
						"exp_date" => $exp_date,
						"card_code" => $card_code,
						
						"amount" => $amount,
						"description" => $BudgetClass,
						
						"first_name" => $first_name,
						"last_name" => $last_name,
						"address" => $address,
						"city" => $city,
						"state" => $state,
						"zip" => $zip,
						"email" => $email
						// Additional fields can be added here as outlined in the AIM integration
						// guide at: http://developer.authorize.net
					));
				
				$response = $transaction->authorizeAndCapture();
				if ($response->approved) {
					// Transaction approved. Collect pertinent transaction information for saving in the database.
					$transaction_id = $response->transaction_id;
					$authorization_code = $response->authorization_code;
					$avs_response = $response->avs_response;
					$cavv_response = $response->cavv_response;
					$acct = $response->account_number;
					
					// Put everything in a database for later review and order processing //
					$payment_data = array(
						'ID_Personnel' => $ID_Adoptee,
						'ID_Club' => $ID_Club,
//						'ID_Relationship' => $ID_Relationship,
						'transactionDescription' => $description,
						'paymentType' => 'Scholarship',
						'paymentAmount' => $amount,
						'CCProcessor' => 'Authorize.Net',
						'transactionID' => $transaction_id,
						'authorizationCode' => $authorization_code,
						'PaymentCategory' => 'Sponsorship',
						'accountNbr' => $acct,
					);
					
					$newPaymentRequest =& $fm->newAddCommand('SupportUSAU20s', $payment_data);
					$result = $newPaymentRequest->execute();
					if (FileMaker::isError($result)) {
						echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 261: " . $result->getMessage() . "</p>";
						exit;
					}
					
					// Once we're finished let's redirect the user to a receipt page
					$message = "<p>Thank You. Your Payment for $" . $amount . " has Been Received.<br/>Transaction ID: " . $transaction_id . "<br/>Credit Card: " . $acct . "</p>";
					$message .= "<p>Want to sponsor someone else? <a href='SupportUSAU20s.php'>Click here</a> to reload the form.</p>";
				} else if ($response->declined) {
					// Transaction declined. Set our error message.
					$error = 'Your credit card was declined by your bank. Please try another form of payment.';
					header('Location: SupportUSAU20s.php?error=' . $error);
					exit();
				} else {
					// And error has occurred. Set our error message.
					$error = 'We encountered an error while processing your payment. Your credit card was not charged. Please try again.<br />Error:' .
						header('Location: SupportUSAU20s.php?response_reason_text=' . $response->response_reason_text);
					
					exit();
				}
			} else { //End $PaymentSelector == "CreditCard"; handle check instead:
				$payment_data = array(
					'ID_Personnel' => $ID_Adoptee,
					'ID_Club' => $ID_Club,
//					'ID_Relationship' => $ID_Relationship,
					'transactionDescription' => $description,
					'paymentType' => 'Scholarship',
					'paymentAmount' => $amount,
					'PaymentCategory' => 'Sponsorship',
				);
				
				$newPaymentRequest =& $fm->newAddCommand('SupportUSAU20s', $payment_data);
				$result = $newPaymentRequest->execute();
				if (FileMaker::isError($result)) {
					echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 262: " . $result->getMessage() . "</p>";
					exit;
				}
				
				$message = "Thank You for your Sponsorship.<br/>";
				$message .= "<p>Make a check out to \"USA Rugby Trust\" for $" . $amount . ".</p>
				<p>In the For/Memo field, write in the name of the rugger you wish to sponsor (" . $AdopteeName . ").</p>
				<p>Mail To:<br/>
					Support USA U20s<br/>
					USA Rugby Trust<br/>
					2655 Crescent Dr., Unit A<br/>
					Lafayette, CO 80026
				</p>";
				$message .= "<p>Want to sponsor someone else? <a href='SupportUSAU20s.php'>Click here</a> to reload the form.</p>";
			}
			
		} else {
			//## Red Field Borders on required fields that failed
			echo '
		<style type="text/css">
			.missing {
			border: 2px solid red
			}
		</style>';
		}
		
	}
	
	## Get Drop Down List Values ###################################################
	$AdopteeValues = $layout->getValueListTwoFields('USAU20Men');
	asort($AdopteeValues);
	$stateValues = $layout->getValueListTwoFields('State-Territory');
	################################################################################
	
	?>
</head>

<body class="polaroid">
<!-- Header -->
<div id="container">
	<div style="text-align: center">
		<img src="include/USAR-logo.png" alt="logo"/>
		<div class="header">
			<h1 class="narrow">Support the USA Rugby U20s</h1>
		</div>
	</div>

	<!-- Show messages instead of form. -->
	<?php
	if (isset($message)) {
		echo '<br />'
			. '<h3>' . $message . '</h3></div></div></body></html>';
		die();
	}
	?>
	<!-- Update CSS style and display error messages/notices if applicable. -->
	<?php
	if (isset($error)) {
		echo '<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
                  <tr><td> 
                     <p style="font-weight: bold">' . $error . '</p>
                  </td></tr>
               </table>';
	}
	if (!empty($fail)) {
		echo '
               <table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
                  <tr><td>Sorry, the following errors were found in your form: 
                     <p style="color: red"><i>' . $fail . '</i></p>
                  </td></tr>
               </table>';
	}
	?>

	<form action="SupportUSAU20s.php" method="post">
		<div id="container" class="aashadow" style="padding: 1em; margin-bottom: 2em">

			<h2>Player to Sponsor</h2>
			<fieldset class="payment">
				<div style="display: inline-block;"
					  class="<?php if (empty($ID_Adoptee)) {
						  $ID_Adoptee_a = " ";
						  echo 'missing';
					  } else {
						  $ID_Adoptee_a = $ID_Adoptee;
					  } ?>">
					<select name="ID_Adoptee" size="1" class="select2" id="Adoptee"
							  title="The Player you wish to sponsor."
							  onchange="document.getElementById('AdopteeName').value=this.options[this.selectedIndex].text">
						<option value=""></option>
						<option value="Greatest Need">Donation to the overall travel fund.</option>
						<option value="">-</option>
						<?php
						foreach ($AdopteeValues as $key => $AdopteeValue) {
							echo "<option value='" . $key . "' " . ($ID_Adoptee_a == $key ? "selected='selected'>" : ">") . $AdopteeValue . "</option>";
						}
						?>
					</select>
				</div>
			</fieldset>

			<h2>Payment</h2>
			<fieldset class="payment">
				<div class="row">
					<input name="PaymentSelector" type="radio" value="CreditCard" class="radio" id="CreditCardRadio"
							 title="Make a payment with a credit card."
						<?php echo($PaymentSelector == "CreditCard" ? "checked='checked'" : ""); ?>
					/>
					<label class="radio" for="CreditCardRadio">Credit Card</label>

					<input name="PaymentSelector" type="radio" value="Check" class="radio" id="CheckRadio"
							 title="Make a payment by mailing in a check."
						<?php echo($PaymentSelector == "Check" ? "checked='checked'" : ""); ?>
					/>
					<label class="radio" for="CheckRadio">Check</label>
				</div>
			</fieldset>

			<div id="CreditCardFields">
				<fieldset class="payment">
					<div style="clear: left">
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
							$state_a = " ";
							echo 'class="missing"';
						} else {
							$state_a = $state;
						} ?> >
							<option value="">&nbsp;</option>
							<?php
							foreach ($stateValues as $value) {
								echo "<option value='" . $value . "' " . ($state_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
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
			</div>

			<fieldset class="payment">
				<div id="CheckInfo">
					<p>Make a check out to "USA Rugby Trust".</p>
					<p>In the For/Memo field, write in the name of the rugger you wish to sponsor.</p>
					<p>Mail To:<br/>
						Support USA U20s<br/>
						USA Rugby Trust<br/>
						2655 Crescent Dr., Unit A<br/>
						Lafayette, CO 80026
					</p>

					<div>
						<label class="payment">Check #</label>
						<input type="text" class="text" size="9" name="CheckNbr"
								 title="Check Number" <?php recallText((empty($CheckNbr) ? "" : $CheckNbr), "no"); ?> />
					</div>
				</div>
			</fieldset>

			<fieldset class="payment">
				<div>
					<label class="payment">Amount:</label>
					<input type="number" class="text" title="Sponsorship Amount" name="Amount" style="width: 8em;"
						<?php recallText($amount, "yes"); ?>/>
					<br/>
					<i>Your donation will be matched dollar for dollar by an RBEA member.<br/>All donations are tax deductible and you will
						receive a tax receipt from USA Rugby Trust.</i>
				</div>
			</fieldset>

			<input name="respondent_exists" type="hidden" value="true"/>
			<input type="hidden" name="AdopteeName" id="AdopteeName" value=""/>

			<input type="submit" value="PAY" class="submit buy">
	</form>
</div>

<p style="width: 90%">For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</p>

<script>
    $(document).ready(function () {

        <!-- Searchable drop-down list -->
        $(".select2").select2();

        var PaymentSelector = $('input:radio[name=PaymentSelector]');
        var CreditCardFields = $('#CreditCardFields');
        var CheckInfo = $('#CheckInfo');

        PaymentSelector.change(function () { //when the rating changes
            var value = this.value;
            if (value === "Check") {
                CheckInfo.removeClass('hidden');
                CreditCardFields.addClass('hidden');
            } else {
                CheckInfo.addClass('hidden');
                CreditCardFields.removeClass('hidden');
            }
        });

        var CheckRadio = $('#CheckRadio');
        var CreditCardRadio = $('#CreditCardRadio');

        if ($(CheckRadio).is(':checked')) {
            CheckInfo.removeClass('hidden');
            CreditCardFields.addClass('hidden');
        } else {
            CheckInfo.addClass('hidden');
        }
        if ($(CreditCardRadio).is(':checked')) {
            CreditCardFields.removeClass('hidden');
        }

    });
</script>
</body>
</html>
