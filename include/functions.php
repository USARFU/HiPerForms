<?php

// Required Field functions //

function validate_empty_field($field, $name)
{
	if ($field == "") {
		return "The '" . $name . "' field is missing a value.<br />";
	} else {
		return "";
	}
}

function validate_DOB($field)
{
	if ($field == "" || $field == date('m/d/Y')) {
		return "A Valid DOB (Date of Birth) was not entered.<br />";
	} else {
		if (validate_date_filemaker($field) == TRUE) {
			$date_array = explode('/', $field);
			$year = $date_array[2];
			$thisYear = date('Y');
			if ($thisYear - $year < 6) {
				return "You must be at least 6 years old to be in the HiPer database. <br />";
			} else {
				return "";
			}
		} else {
			return "The DOB must be entered in the mm/dd/yyyy format. <br />";
		}
	}
}

function validate_PassportExpiration($field) //$field = date('Y-m-d')
{
	$expire = strtotime("+6 months");
	if ($expire > strtotime($field)) {
		return "Warning: Your passport expires within 6 months. You may not be allowed reentry into the country. <br />";
	} else {
		return "";
	}
}

function validate_date($field)
{
	$field_test = explode('-', $field);
	if (count($field_test) == 3) {
		if (checkdate($field_test[1], $field_test[2], $field_test[0]) == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
}

function validate_date_filemaker($field)
{
	$field_test = explode('/', $field);
	if (count($field_test) == 3) {
		if (checkdate($field_test[0], $field_test[1], $field_test[2]) == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
}

function validate_image($field)
{
	if ($_FILES[$field]['error'] != UPLOAD_ERR_OK) {
		return "Either no photo was submitted, or its filesize exceeded 2MB.<br />";
	} else {
		return "";
	}
}

function validate_heightMeters($field)
{
	if ($field == "") {
		return "";
	}
	else if ($field < 1 || $field > 3 || !is_numeric($field)) {
		return "A valid number was not entered for Height(meters).<br />";
	} else {
		return "";
	}
}

function validate_heightFeet($field)
{
	if ($field == "") {
		return "";
	}
	else if ($field < 4 || $field > 7 || !is_numeric($field)) {
		return "A valid number was not entered for Height(feet).<br />";
	} else {
		return "";
	}
}

function validate_heightInches($field)
{
	if ($field == "") {
		return "";
	}
	else if ($field == "0") {
		return "";
	} else if ($field < 1 || $field > 11 || !is_numeric($field)) {
		return "A valid number was not entered for Height(inches).<br />";
	} else {
		return "";
	}
}

function validate_weight($field)
{
	if ($field == "") {
		return "";
	} else if ($field < 20 || $field > 600 || !is_numeric($field)) {
		return "A valid number was not entered for Weight.<br />";
	} else {
		return "";
	}
}

function validate_clothingSizes($field)
{
	if ($field == "") {
		return "You haven't specified all clothing sizes.<br />";
	} else {
		return "";
	}
}

function validate_zip($field)
{
	if ($field == "") {
		return "No Home Address: Zip was entered.<br />";
	} elseif (strlen($field) < 5) {
		return "Zip/Postal Code not long enough.<br />";
	} else {
		return "";
	}
}

function validate_eMail($field)
{
	if ($field == "") {
		return "No E-Mail was entered.<br />";
	} elseif (!filter_var($field, FILTER_VALIDATE_EMAIL)) {
		return "E-mail entered was invalid. <br />";
	} else {
		return "";
	}
}

function validate_allergiesConditions($field)
{
	if ($field == "") {
		return "Please indicate whether you have allergies, and if yes<br />";
	} else {
		return "";
	}
}

function validate_allergiesConditionsDescr($field, $parent)
{
	if ($field == "" && $parent != "No") {
		return "Please describe your allergies.<br />";
	} else {
		return "";
	}
}

function validate_medications($field)
{
	if ($field == "") {
		return "Please indicate whether you have medications, and if yes<br />";
	} else {
		return "";
	}
}

function validate_medicationsDescr($field, $parent)
{
	if ($field == "" && $parent != "No") {
		return "Please describe your medications.<br />";
	} else {
		return "";
	}
}

function validate_Membership($field)
{
	if ($field == "") {
		return "Must have a Membership ID from USA Rugby.<br />";
	}
	else if ($field < 10000 || $field > 9999999 || !is_numeric($field)) {
		return "The Member ID you entered was not a valid number.<br />";
	} else {
		return "";
	}
}

function validate_waiver($field)
{
	if ($field != 1) {
		return "Mark the checkbox to acknowledge that you accept responsibility for the information provided.<br />";
	} else {
		return "";
	}
}

## Validate CC Payment fields ##################################################
function validate_cardType($card_number)
{
	// Get the first digit
	$firstnumber = substr($card_number, 0, 1);
	// Make sure it is the correct amount of digits. Account for dashes being present.
	switch ($firstnumber) {
		case 3:
			if (!preg_match('/^3\d{3}[ \-]?\d{6}[ \-]?\d{5}$/', $card_number)) {
				return 'This is not a valid American Express card number. <br />';
			}
			break;
		case 4:
			if (!preg_match('/^4\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $card_number)) {
				return 'This is not a valid Visa card number. <br />';
			}
			break;
		case 5:
			if (!preg_match('/^5\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $card_number)) {
				return 'This is not a valid MasterCard card number. <br />';
			}
			break;
		case 6:
			if (!preg_match('/^6011[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $card_number)) {
				return 'This is not a valid Discover card number. <br />';
			}
			break;
		default:
			return 'This is not a valid credit card number. <br />';
	}
	validate_cardNumber($card_number);
}

function validate_cardNumber($number)
{
	/* Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
	 * This code has been released into the public domain, however please      *
	 * give credit to the original author where possible.                      */
	if ($number == "") {
		return "Credit Card Number must be entered. <br />";
	} else {
		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number = preg_replace('/\D/', '', $number);
		
		// Set the string length and parity
		$number_length = strlen($number);
		$parity = $number_length % 2;
		
		// Loop through each digit and do the maths
		$total = 0;
		for ($i = 0; $i < $number_length; $i++) {
			$digit = $number[$i];
			// Multiply alternate digits by two
			if ($i % 2 == $parity) {
				$digit *= 2;
				// If the sum is two digits, add them together (in effect)
				if ($digit > 9) {
					$digit -= 9;
				}
			}
			// Total up the digits
			$total += $digit;
		}
		
		// If the total mod 10 equals 0, the number is valid
		//return ($total % 10 == 0) ? TRUE : FALSE;
		return ($total % 10 == 0) ? "" : "Credit Card Number entered is invalid. <br />";
	}
}

function validate_cardExp($month, $year)
{
	if (!preg_match('/^\d{1,2}$/', $month)) {
		return "The month isn't a one or two digit number. <br />";
	} else if (!preg_match('/^\d{4}$/', $year)) {
		return "The year isn't four digits long. <br />";
	} else if ($year < date("Y")) {
		return "The card is already expired. <br />";
	} else if ($month < date("m") && $year == date("Y")) {
		return "The card is already expired. <br />";
	}
	return "";
}

function validate_CCV($cardNumber, $cvv)
{
	// Get the first number of the credit card so we know how many digits to look for
	$firstnumber = (int)substr($cardNumber, 0, 1);
	if ($firstnumber === 3) {
		if (!preg_match("/^\d{4}$/", $cvv)) {
			// The credit card is an American Express card but does not have a four digit CVV code
			return "The credit card is an American Express card but does not have a four digit CVV code. <br />";
		}
	} else if (!preg_match("/^\d{3}$/", $cvv)) {
		// The credit card is a Visa, MasterCard, or Discover Card card but does not have a three digit CVV code
		return "The credit card is a Visa, MasterCard, or Discover Card card but does not have a three digit CVV code. <br />";
	}
	return "";
}

################################################################################

## Validate Travel Form ########################################################
function validate_embarkDepartureDate($field)
{
	if ($field == "" || $field == date('m/d/Y')) {
		return "A valid Embark Departure Date was not entered.<br />";
	} else {
		return "";
	}
}

function validate_returnDepartureDate($field)
{
	if ($field == "" || $field == date('m/d/Y')) {
		return "A valid Return Departure Date was not entered.<br />";
	} else {
		return "";
	}
}

################################################################################

## Validate Registration Form fields ###########################################
function validate_StatePlayingIn($field)
{
	if ($field == "") {
		return "The State You Play In is a mandatory field. <br />";
	} else {
		return "";
	}
}

function validate_CurrentSchoolGradeLevel($field)
{
	if ($field == "") {
		return "Your School Grade Level is missing a value. <br />";
	} elseif ($field < 4 || $field > 12) {
		return "Youth Rugby Registration through this system is only open to grades 4-12 at this time. <br />";
	} else {
		return "";
	}
}

function validate_school_1_12($field)
{
	if ($field == "") {
		return "Select the school you are attending. <br />";
	} else {
		return "";
	}
}

################################################################################

// Text cleanup
function fix_string($string)
{
	return trim(strip_tags($string));
}

// Recall previously entered value on form reload
function recallText($fieldName, $required)
{
	if ($fieldName == "skip") {
		return;
	}
	if (!empty($fieldName) || $fieldName == "0") {
		echo 'value="' . $fieldName . '"';
	} elseif (empty($fieldName) && $required == "yes") {
		echo 'class="missing"';
	}
}

// Clean up old temp image filespace
function cleanPictures()
{
	$probabilityDivisor = 10; // 1/10 chance of running
	$uploadTTLSecs = 60 * 60 * 2; // age time of 2 hours required
	
	if (mt_rand(1, $probabilityDivisor) / $probabilityDivisor == 1) {
		foreach (glob('../tmp/*') as $file) {
			// Iterate files matching the uniqids you generate
			if (time() - filemtime($file) >= $uploadTTLSecs) {
				// If file is older than $uploadTTLSecs, delete it
				unlink($file);
			}
		}
	}
}

?>    