<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Rugby Camp Payment</title>

<!-- Error Codes 241-243 -->
<!--Submit data if criteria is met-->
<?php
include_once 'header.php';
require '../Authorize.net-1.9.0/vendor/autoload.php';
require_once '../Authorize.net-1.9.0/phpunit_config.php';

// Get Form options and data //
$pageHeader = (empty($campRecord->getField('WebFormPaymentTitle')) ? "USA Rugby Camp Payment" : $campRecord->getField('WebFormPaymentTitle'));
$budgetClass = $campRecord->getField('BudgetClass');
$description = $budgetClass . ": " . $campName . " " . $dateStarted;
$paymentCutOff = $campRecord->getField('paymentCutOff');
$couponCount = $campRecord->getField('c_CouponCount');
$partialPaymentFlag = $campRecord->getField('allowPartialPayment');
$AdminEmailPayment = $campRecord->getField('AdminEmailUponPayment_flag');

if (empty($IDType)) {
## Redirect to HTTPS if necessary ###########################################
if (empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") {
   header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
   exit();
}
#############################################################################

	$fail = "";
	$skip = "";

	$partialPaymentFlagIndividual = $record->getField('allowPartialPayment');
	$partialPaymentFlag = ($partialPaymentFlag == 1 or $partialPaymentFlagIndividual == 1 ? 1 : "");
	$ID_Personnel = $record->getField('ID_Personnel');

	$CutOffCompare_a = new DateTime($paymentCutOff);
	$CutOffCompare = $CutOffCompare_a->format('Y-m-d');
	$today = date('Y-m-d');

// Check that link isn't expired //
	if ($CutOffCompare < $today || empty($paymentCutOff)) {
		$message = "This link has expired. You are past this event's cut off date.";
	}

	$card_num = (isset ($_POST['card_num']) ? fix_string($_POST['card_num']) : "");

	$exp_date = "";
	$month = (isset ($_POST['month']) ? fix_string($_POST['month']) : "");
	$year = (isset ($_POST['year']) ? fix_string($_POST['year']) : "");

	if (!empty($month) && !empty($year)) {
		$exp_date_a = sprintf("%04d-%02d", $year, $month);
		$exp_date = fix_string($exp_date_a);
	}

	$card_code = (isset ($_POST['card_code']) ? fix_string($_POST['card_code']) : "");
	$first_name = (isset ($_POST['first_name']) ? fix_string($_POST['first_name']) : "");
	$last_name = (isset ($_POST['last_name']) ? fix_string($_POST['last_name']) : "");
	$address = (isset ($_POST['address']) ? fix_string($_POST['address']) : "");
	$city = (isset ($_POST['city']) ? fix_string($_POST['city']) : "");
	$state = (isset ($_POST['state']) ? fix_string($_POST['state']) : "");
	$zip = (isset ($_POST['zip']) ? fix_string($_POST['zip']) : "");
	$email = (isset ($_POST['email']) ? fix_string($_POST['email']) : "");
	$partialPayment = (isset ($_POST['partialPayment']) ? fix_string($_POST['partialPayment']) : "");
	$couponCode = (isset ($_POST['couponCode']) ? fix_string($_POST['couponCode']) : "");
	$discountedFee = (isset ($_POST['discountedFee']) ? fix_string($_POST['discountedFee']) : "");

// Check for CC error //
	if (isset($_GET['response_reason_text'])) {
		$fail .= htmlentities($_GET['response_reason_text']);
	}
	if (isset($_GET['error'])) {
		$fail .= htmlentities($_GET['error']);
	}
}

// Apply Coupon Code //
if (isset($_POST['couponCode'])) {
	$couponRequest = $fm->newFindCommand('PHP-EventCoupons');
	$couponRequest->addFindCriterion('ID_Event', '==' . $ID_Camp);
	$couponRequest->addFindCriterion('couponCode', '==' . $couponCode);
	$couponResult = $couponRequest->execute();
	if (FileMaker::isError($couponResult)) {
		$couponNote = "NOTICE: Coupon Code $couponCode is not valid.";
	} else {
		$couponRecord = $couponResult->getFirstRecord();
		$discount = $couponRecord->getField('percentageDiscount');
		$discountedFee = round((1 - ($discount / 100)) * $fee, 2);
		$couponNote = "NOTICE: Coupon Code $couponCode has been applied.";
		if ($discount == 100) {
			$payment_data = array(
				'ID_Personnel' => $ID_Personnel,
				'ID_Event' => $ID_Camp,
				'transactionDescription' => $description,
				'paymentType' => 'Scholarship',
				'paymentAmount' => 0,
				'couponCode' => $couponCode,
			);

			$newPaymentRequest =& $fm->newAddCommand('PHP-PaymentRelated', $payment_data);
			$result = $newPaymentRequest->execute();
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 242: " . $result->getMessage() . "</p>";
				exit;
			}
			$message = "Thank You. Your Payment Has Been Received.";
			$skip = 1;
		}
	}
}

if ($skip != 1 && empty($IDType)) {
	// Check if Payment Record already exists //
	$paymentRequest = $fm->newFindCommand('PHP-PaymentRelated');
	$paymentRequest->addFindCriterion('ID_Personnel', '==' . $ID_Personnel);
	$paymentRequest->addFindCriterion('ID_Event', '==' . $ID_Camp);
	$paymentResult = $paymentRequest->execute();

	if (FileMaker::isError($paymentResult)) {
	} else {
		$related_payment_records = $record->getRelatedSet('EventPersonnel__Payment');
		$related_payment = $related_payment_records[0];
		$related_payment_amount = $related_payment->getField('EventPersonnel__Payment::sum_paymentAmount');
		$error = 'NOTICE: Payment(s) have already been made for your account for this event in the amount of $' . $related_payment_amount;
	}

	if (isset($_POST['respondent_exists'])) {
		$fail = validate_cardType($card_num);
		$fail .= validate_cardExp($month, $year);
		$fail .= validate_CCV($card_num, $card_code);
		$fail .= validate_empty_field($first_name, "First Name");
		$fail .= validate_empty_field($last_name, "Last Name");
		$fail .= validate_empty_field($address, "Street");
		$fail .= validate_empty_field($city, "City");
		$fail .= validate_empty_field($state, "State");
		$fail .= validate_zip($zip);
		if (!empty($partialPayment) && $partialPayment < 5) {
			$fail .= "Credit Card payments less than $5 are not allowed.";
		}

		if (empty($fail)) {
			// Process the transaction using the AIM API
			if (!empty($discountedFee)) {
				$amount2 = $discountedFee;
			} else {
				$amount2 = $fee;
			}
			if (!empty($partialPayment)) {
				$amount2 = $partialPayment;
			}
			$transaction = new AuthorizeNetAIM;
			$transaction->setFields(
				array(
					"card_num" => $card_num,
					"exp_date" => $exp_date,
					"card_code" => $card_code,

					"amount" => $amount2,
					"description" => $description,

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
					'ID_Personnel' => $ID_Personnel,
					'ID_Event' => $ID_Camp,
					'transactionDescription' => $description,
					'paymentType' => 'Credit Card',
					'paymentAmount' => $amount2,
					'CCProcessor' => 'Authorize.Net',
					'transactionID' => $transaction_id,
					'authorizationCode' => $authorization_code,
					'couponCode' => $couponCode,
					'PaymentCategory' => 'Event Fee',
					'accountNbr' => $acct,
				);

				$newPaymentRequest =& $fm->newAddCommand('PHP-PaymentRelated', $payment_data);
				$result = $newPaymentRequest->execute();
				if (FileMaker::isError($result)) {
					echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 243: " . $result->getMessage() . "</p>";
					exit;
				}

				// Once we're finished let's redirect the user to a receipt page
				$message = "Thank You. Your Payment Has Been Received.";
				
				// E-mail camp admin of payment, if enabled
				if ($AdminEmailPayment == 1){
					$ID_EventPersonnel = $record->getField('ID');
					$params = "Payment|" . $ID_EventPersonnel . "|$amount2";
					$newPerformScript = $fm->newPerformScriptCommand('PHP-EventInvite', 'eMail Camp Admin Player Update', $params);
					$scriptResult = $newPerformScript->execute();
					if (FileMaker::isError($scriptResult)) {
						echo "<p>Error: " . $scriptResult->getMessage() . "</p>";
//					die();
					}
				}
				
			} else if ($response->declined) {
				// Transaction declined. Set our error message.
				$error = 'Your credit card was declined by your bank. Please try another form of payment.';
				header('Location: Payment.php?ID=' . $ID . '&error=' . $error);
				exit();
			} else {
				// And error has occurred. Set our error message.
				$error = 'We encountered an error while processing your payment. Your credit card was not charged. Please try again.';
				header('Location: Payment.php?ID=' . $ID . '&response_reason_text=' . $response->response_reason_text);

				exit();
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

	} else {
		// Populate variables from related Personnel table //
		$related_personnel_records = $record->getRelatedSet('EventPersonnel__Personnel');
		$related_personnel = $related_personnel_records[0];
		$related_personnel_ID = $related_personnel->getRecordId();
		$email = $related_personnel->getField('EventPersonnel__Personnel::eMail');
	}
}
?>
	</head>

<body>
<!-- Header -->
<div class="header background">
	<h1><?php echo $pageHeader; ?></h1>
	<table class="tableHeaderTwo">
		<tr>
			<td style="width: 15%">Your Name:</td>
			<td style="width: 35%"><?php echo $name; ?></td>
			<td style="width: 15%">Date of Event:</td>
			<td style="width: 35%"><?php echo $dateStarted; ?></td>
		</tr>
		<tr>
			<td>Event Name:</td>
			<td><?php echo $campName; ?></td>
			<td>Cut-off Date:</td>
			<td><?php echo $paymentCutOff; ?></td>
		</tr>
		<tr>
			<td>Venue:</td>
			<td><?php echo $venueName; ?></td>
			<td>Event Fee:</td>
			<td>$<?php echo $fee; ?></td>
		</tr>
		<?php if (!empty($discountedFee)) {
			echo '
               <tr>
                  <td></td>
                  <td></td>
                  <td>Discounted Fee:</td>
                  <td>$' . $discountedFee . '</td>
               </tr>
               ';
		} ?>
	</table>
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
if (isset($couponNote)) {
	echo '<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
                  <tr><td> 
                     <p style="font-weight: bold">' . $couponNote . '</p>
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
</div> <!-- Ends <div style="text-align: center"> from header.php -->

<?php if ($couponCount > 0) { ?>
	<form action="Payment.php" method="post">
		<h2>Coupon Code</h2>
		<fieldset class="payment">
			<div>
				<label class="payment">Coupon Code</label>
				<input type="text" class="text" size="15" name="couponCode"
						 title="If you have received a coupon for this event, enter and apply it now before continuing."/>
			</div>
		</fieldset>
		<input name="coupon_exists" type="hidden" value="true"/>
		<input name="ID" type="hidden" value="<?php echo $ID; ?>"/>
		<input type="submit" value="APPLY" class="submit buy">
	</form>
<?php } ?>

<form action="Payment.php" method="post">
	<h2>Credit Card Payment</h2>
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
				$state_a = " ";
				echo 'class="missing"';
			} else {
				$state_a = $state;
			} ?> >
				<option value="">&nbsp;</option>
				<?php
				$stateValues = $layout->getValueListTwoFields('State');
				foreach ($stateValues as $value) {
					echo "<option value=\"" . $value . "\"" . ($state_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
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

	<?php
	if ($partialPaymentFlag == 1) {
		echo '
            <fieldset class="payment">
               <div>
                  <label class="payment">Amount:</label>
                  <input type="number" class="text" name="partialPayment" style="width: 8em;" ';
		recallText((empty($partialPayment) ? "" : $partialPayment), "no");
		echo ' />
               </div>
            </fieldset>';
	}
	?>

	<input name="respondent_exists" type="hidden" value="true"/>
	<input name="ID" type="hidden" value="<?php echo $ID; ?>"/>
	<?php
	if (isset($discountedFee)) {
		echo '<input name="discountedFee" type="hidden" value="';
		echo $discountedFee;
		echo '" />';
	}
	if (isset($couponCode)) {
		echo '<input name="couponCode" type="hidden" value="';
		echo $couponCode;
		echo '" />';
	}
	?>
	<input type="submit" value="PAY" class="submit buy">
</form>
</div> <!-- Container div that does 90% centered margin -->
</body>
</html>
