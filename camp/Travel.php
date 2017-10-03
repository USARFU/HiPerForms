<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Rugby Event Travel Info</title>

	<script src="../include/script/jquery/jquery.min.js"></script>

	<!-- select2 js library for searchable drop down controls -->
	<link href="../include/script/select2/css/select2.min.css" rel="stylesheet"/>
	<script src="../include/script/select2/js/select2.min.js"></script>

	<!-- jquery-ui for date picker -->
	<link href="../include/script/jquery-ui/jquery-ui.min.css" rel="stylesheet"/>
	<script src="../include/script/jquery-ui/jquery-ui.min.js"></script>

	<!-- Error Codes 261-263 -->
	<?php
	include_once "header.php";
	$fail = "";

	## Get variable data for form header ########################################
	$travelCutOff = $campRecord->getField('travelCutOff');
	$includeEmbark = $campRecord->getField('wf_travel_Embark');
	$includeReturn = $campRecord->getField('wf_travel_Return');
	$pageHeader = (empty($campRecord->getField('WebFormTravelTitle')) ? "USA Rugby Camp Travel Information" : $campRecord->getField('WebFormTravelTitle'));
	$AdminEmailTravel = $campRecord->getField('AdminEmailUponTravelUpdate_flag');
	#############################################################################

	if (empty($IDType)) {
		## Sanitize submitted form data before assigning to variable.
		## Variable data is used to put data back into field in case mandatory field requirements are not met.
		## Generally, variable and field names match the database field names.
		## Otherwise, variables are submitted to database.  #########################
		$embarkModeOfTransportation = (isset ($_POST ['embarkModeOfTransportation']) ? fix_string($_POST ['embarkModeOfTransportation']) : "");
		$ID_embarkDepartureAirport = (isset ($_POST ['ID_embarkDepartureAirport']) ? fix_string($_POST ['ID_embarkDepartureAirport']) : "");
		$embarkDepartureDate = "";
		$embarkDepartureDateSave = "";
		if (!empty($_POST['embarkDepartureDate'])) {
			if (validate_date($_POST['embarkDepartureDate']) || validate_date_filemaker($_POST['embarkDepartureDate'])) {
				$embarkDepartureDateOld = new DateTime($_POST['embarkDepartureDate']);
				$embarkDepartureDate = $embarkDepartureDateOld->format('m/d/Y');
				$embarkDepartureDateSave = $embarkDepartureDateOld->format('Y-m-d');
			} else {
				$fail .= "The Embark Departure Date is in the wrong format. <br />";
				$embarkDepartureDateSave = $_POST['embarkDepartureDate'];
			}
		}
		$embarkDepartureTime = (isset ($_POST ['embarkDepartureTime']) ? fix_string($_POST ['embarkDepartureTime']) : "");
		$embarkFlightNumber = (isset ($_POST ['embarkFlightNumber']) ? fix_string($_POST ['embarkFlightNumber']) : "");
		$embarkConfirmationNumber = (isset ($_POST ['embarkConfirmationNumber']) ? fix_string($_POST ['embarkConfirmationNumber']) : "");
		$ID_embarkArrivalAirport = (isset ($_POST ['ID_embarkArrivalAirport']) ? fix_string($_POST ['ID_embarkArrivalAirport']) : "");
		$embarkArrivalDate = "";
		$embarkArrivalDateSave = "";
		if (!empty($_POST['embarkArrivalDate'])) {
			if (validate_date($_POST['embarkArrivalDate']) || validate_date_filemaker($_POST['embarkArrivalDate'])) {
				$embarkArrivalDateOld = new DateTime($_POST['embarkArrivalDate']);
				$embarkArrivalDate = $embarkArrivalDateOld->format('m/d/Y');
				$embarkArrivalDateSave = $embarkArrivalDateOld->format('Y-m-d');
			} else {
				$fail .= "The Embark Arrival Date is in the wrong format. <br />";
				$embarkArrivalDateSave = $_POST['embarkArrivalDate'];
			}
		}
		$embarkArrivalTime = (isset ($_POST ['embarkArrivalTime']) ? fix_string($_POST ['embarkArrivalTime']) : "");

		$returnModeOfTransportation = (isset ($_POST ['returnModeOfTransportation']) ? fix_string($_POST ['returnModeOfTransportation']) : "");
		$ID_returnDepartureAirport = (isset ($_POST ['ID_returnDepartureAirport']) ? fix_string($_POST ['ID_returnDepartureAirport']) : "");
		$returnDepartureDate = "";
		$returnDepartureDateSave = "";
		if (!empty($_POST['returnDepartureDate'])) {
			if (validate_date($_POST['returnDepartureDate']) || validate_date_filemaker($_POST['returnDepartureDate'])) {
				$returnDepartureDateOld = new DateTime($_POST['returnDepartureDate']);
				$returnDepartureDate = $returnDepartureDateOld->format('m/d/Y');
				$returnDepartureDateSave = $returnDepartureDateOld->format('Y-m-d');
			} else {
				$fail .= "The Return Departure Date is in the wrong format. <br />";
				$returnDepartureDateSave = $_POST['returnDepartureDate'];
			}
		}
		$returnDepartureTime = (isset ($_POST ['returnDepartureTime']) ? fix_string($_POST ['returnDepartureTime']) : "");
		$returnFlightNumber = (isset ($_POST ['returnFlightNumber']) ? fix_string($_POST ['returnFlightNumber']) : "");
		$returnConfirmationNumber = (isset ($_POST ['returnConfirmationNumber']) ? fix_string($_POST ['returnConfirmationNumber']) : "");
		$ID_returnArrivalAirport = (isset ($_POST ['ID_returnArrivalAirport']) ? fix_string($_POST ['ID_returnArrivalAirport']) : "");
		$returnArrivalTime = (isset ($_POST ['returnArrivalTime']) ? fix_string($_POST ['returnArrivalTime']) : "");

		$travelComments = (isset ($_POST ['travelComments']) ? fix_string($_POST ['travelComments']) : "");
		$frequentFlyerInfo = (isset ($_POST ['frequentFlyerInfo']) ? fix_string($_POST ['frequentFlyerInfo']) : "");

		## Check that EventPersonnel ID is received #################################
		if (isset($_POST['ID'])) {
			$ID = $_POST['ID'];
		} else {
			if (isset($_GET['ID'])) {
				$ID = fix_string($_GET['ID']);
			} else {
				echo '<p style="color: red"><i>User\'s Event ID is missing from link. Verify the link you were e-mailed and try again.</i></p>';
				die();
			}
		}
		#############################################################################

		## Verify received EventPersonnel ID ########################################
		$request = $fm->newFindCommand('PHP-EventInvite');
		$request->addFindCriterion('ID', '==' . $ID);
		$result = $request->execute();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (FileMaker::isError($result)) {
			/** @noinspection PhpUndefinedMethodInspection */
			echo "<p>Error: There was a problem processing your information. Your link ID is invalid. Check the link that was e-mailed you, and try again.</p>"
				. "<p>Error Code 261: " . $result->getMessage() . "</p>";
			/** @noinspection PhpUndefinedMethodInspection */
			echo "<p>Error: " . $result->getMessage() . "</p>";
			echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
			die();
		}
		#############################################################################

		## Verify that event link hasn't expired ####################################
		$CutOffCompare_a = new DateTime($travelCutOff);
		$CutOffCompare = $CutOffCompare_a->format('Y-m-d');
		$today = date('Y-m-d');
		if ($CutOffCompare < $today || empty($travelCutOff)) {
			$message = "This link has expired. You are past this event's cut off date.";
		}
		#############################################################################

		## Get related personnel record #############################################
		$related_personnel_records = $record->getRelatedSet('EventPersonnel__Personnel');
		$related_personnel = $related_personnel_records[0];
		$related_personnel_ID = $related_personnel->getRecordId();
		#############################################################################
	}

	if (isset($_POST['respondent_exists'])) { // Form has been submitted

		## Check that mandatory fields contain data, and return appropriate
		## error messages for each missing field data. ########################
		if ($includeEmbark == "Mandatory") {
			$fail .= validate_empty_field($embarkModeOfTransportation, "Embark: Method of Travel");
			if ($embarkModeOfTransportation == "Flying") {
				$fail .= validate_empty_field($ID_embarkDepartureAirport, "Embark: Departure Airport");
				$fail .= validate_embarkDepartureDate($embarkDepartureDate);
				$fail .= validate_empty_field($embarkDepartureTime, "Embark: Departure Time");
				$fail .= validate_empty_field($embarkFlightNumber, "Embark: Flight Number");
				$fail .= validate_empty_field($ID_embarkArrivalAirport, "Embark: Arrival Airport");
				$fail .= validate_empty_field($embarkArrivalTime, "Embark: Arrival Time");
			}
		}

		if ($includeReturn == "Mandatory") {
			$fail .= validate_empty_field($returnModeOfTransportation, "Return: Method of Travel");
			if ($returnModeOfTransportation == "Flying") {
				$fail .= validate_empty_field($ID_returnDepartureAirport, "Return: Departure Airport");
				$fail .= validate_returnDepartureDate($returnDepartureDate);
				$fail .= validate_empty_field($returnDepartureTime, "Return: Departure Time");
				$fail .= validate_empty_field($returnFlightNumber, "Return: Flight Number");
				$fail .= validate_empty_field($ID_returnArrivalAirport, "Return: Arrival Airport");
				$fail .= validate_empty_field($returnArrivalTime, "Return: Arrival Time");
			}
		}

		if (empty($fail)) { // Submit data to database
			## Begin - Update related Personnel record ####
			$editPersonnel = $fm->NewEditCommand('PHP-TravelForm', $related_personnel_ID);
			$editPersonnel->setField('travelComments', $travelComments);
			$editPersonnel->setField('frequentFlyerInfo', $frequentFlyerInfo);

			$resultPersonnel = $editPersonnel->execute();
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			if (FileMaker::isError($resultPersonnel)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 262: " . $resultPersonnel->getMessage() . "</p>";
				die();
			}
			## End - Update related Personnel record ######

			## Begin - Update EventPersonnel record ####
			$editEventPersonnel = $fm->NewEditCommand('PHP-EventInvite', $recordID);
			$editEventPersonnel->setField('embarkModeOfTransportation', $embarkModeOfTransportation);
			$editEventPersonnel->setField('ID_embarkDepartureAirport', $ID_embarkDepartureAirport);
			$editEventPersonnel->setField('embarkDepartureDate', $embarkDepartureDate);
			$editEventPersonnel->setField('embarkDepartureTime', $embarkDepartureTime);
			$editEventPersonnel->setField('embarkFlightNumber', $embarkFlightNumber);
			$editEventPersonnel->setField('embarkConfirmationNumber', $embarkConfirmationNumber);
			$editEventPersonnel->setField('ID_embarkArrivalAirport', $ID_embarkArrivalAirport);
			$editEventPersonnel->setField('embarkArrivalDate', $embarkArrivalDate);
			$editEventPersonnel->setField('embarkArrivalTime', $embarkArrivalTime);
			$editEventPersonnel->setField('returnModeOfTransportation', $returnModeOfTransportation);
			$editEventPersonnel->setField('ID_returnDepartureAirport', $ID_returnDepartureAirport);
			$editEventPersonnel->setField('returnDepartureDate', $returnDepartureDate);
			$editEventPersonnel->setField('returnDepartureTime', $returnDepartureTime);
			$editEventPersonnel->setField('returnFlightNumber', $returnFlightNumber);
			$editEventPersonnel->setField('returnConfirmationNumber', $returnConfirmationNumber);
			$editEventPersonnel->setField('ID_returnArrivalAirport', $ID_returnArrivalAirport);
			$editEventPersonnel->setField('returnArrivalTime', $returnArrivalTime);

			$resultEventPersonnel = $editEventPersonnel->execute();
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			if (FileMaker::isError($resultEventPersonnel)) {
				if	($resultEventPersonnel->code == 501) {
					$fail .= "An invalid value was entered in one of the time fields. <br />";
				} else {
					echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 263: " . $resultEventPersonnel->getMessage() . " (" . $resultEventPersonnel->code . ")" . "</p>";
					die();
				}
			}
			## End - Update EventPersonnel record ######
			
			// E-mail camp admin of change, if enabled
			if ($AdminEmailTravel == 1){
				$ID_EventPersonnel = $record->getField('ID');
				$params = "Travel|" . $ID_EventPersonnel;
				$newPerformScript = $fm->newPerformScriptCommand('PHP-EventInvite', 'eMail Camp Admin Player Update', $params);
				$scriptResult = $newPerformScript->execute();
				if (FileMaker::isError($scriptResult)) {
					echo "<p>Error: " . $scriptResult->getMessage() . "</p>";
//					die();
				}
			}

			$message = "Thank You. Your Travel Information has been Updated.";
		} else {
			//## Red Field Borders on required fields that failed
			echo '
		<style type="text/css">
			.missing {
			border: 2px solid red
			}
		</style>';
		}

	} elseif (empty($IDType)) { // Form has been freshly loaded

		// ## Only get existing database data is flight dates later than today ######
		$embarkModeOfTransportation = $record->getField('embarkModeOfTransportation');
		$embarkDepartureDate_original = $record->getField('embarkDepartureDate');
		$embarkDepartureDate_original_test = explode('/', $embarkDepartureDate_original);
		if (count($embarkDepartureDate_original_test) == 3) {
			if (checkdate($embarkDepartureDate_original_test[0], $embarkDepartureDate_original_test[1], $embarkDepartureDate_original_test[2]) == TRUE) {
				$embarkDepartureDate_a = new DateTime($embarkDepartureDate_original);
				$embarkDepartureDateSave = $embarkDepartureDate_a->format('Y-m-d');
				$embarkDepartureDate = $embarkDepartureDate_a->format('m/d/Y');
			}
		} else {
			$embarkDepartureDate = "";
			$embarkDepartureDateSave = "";
		}

		$ID_embarkDepartureAirport = $record->getField('ID_embarkDepartureAirport');
		$embarkDepartureTime = $record->getField('embarkDepartureTime');
		$embarkFlightNumber = $record->getField('embarkFlightNumber');
		$embarkConfirmationNumber = $record->getField('embarkConfirmationNumber');
		$ID_embarkArrivalAirport = $record->getField('ID_embarkArrivalAirport');
		$embarkArrivalDate_original = $record->getField('embarkArrivalDate');
		$embarkArrivalDate_original_test = explode('/', $embarkArrivalDate_original);
		if (count($embarkArrivalDate_original_test) == 3) {
			if (checkdate($embarkArrivalDate_original_test[0], $embarkArrivalDate_original_test[1], $embarkArrivalDate_original_test[2]) == TRUE) {
				$embarkArrivalDate_a = new DateTime($embarkArrivalDate_original);
				$embarkArrivalDateSave = $embarkArrivalDate_a->format('Y-m-d');
				$embarkArrivalDate = $embarkArrivalDate_a->format('m/d/Y');
			}
		} else {
			$embarkArrivalDate = "";
			$embarkArrivalDateSave = "";
		}
		$embarkArrivalTime = $record->getField('embarkArrivalTime');

		$returnModeOfTransportation = $record->getField('returnModeOfTransportation');
		$returnDepartureDate_original = $record->getField('returnDepartureDate');
		$returnDepartureDate_original_test = explode('/', $returnDepartureDate_original);
		if (count($returnDepartureDate_original_test) == 3) {
			if (checkdate($returnDepartureDate_original_test[0], $returnDepartureDate_original_test[1], $returnDepartureDate_original_test[2]) == TRUE) {
				$returnDepartureDate_a = new DateTime($returnDepartureDate_original);
				$returnDepartureDateSave = $returnDepartureDate_a->format('Y-m-d');
				$returnDepartureDate = $returnDepartureDate_a->format('m/d/Y');
			}
		} else {
			$returnDepartureDate = "";
			$returnDepartureDateSave = "";
		}

		$ID_returnDepartureAirport = $record->getField('ID_returnDepartureAirport');
		$returnDepartureTime = $record->getField('returnDepartureTime');
		$returnFlightNumber = $record->getField('returnFlightNumber');
		$returnConfirmationNumber = $record->getField('returnConfirmationNumber');
		$ID_returnArrivalAirport = $record->getField('ID_returnArrivalAirport');
		$returnArrivalTime = $record->getField('returnArrivalTime');
		##########################################################################

		$frequentFlyerInfo = $related_personnel->getField('EventPersonnel__Personnel::frequentFlyerInfo');
		$travelComments = $related_personnel->getField('EventPersonnel__Personnel::travelComments');
	}
	?>
</head>

<body>
<!-- Banner and Error Messages                                     -->
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
			<td><?php echo $travelCutOff; ?></td>
		</tr>
		<tr>
			<td>Venue:</td>
			<td><?php echo $venueName; ?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
</div>
<!-- Show messages instead of form. -->
<?php
if (isset($message)) {
	echo '<br />'
		. '<h3 style="text-align: center">' . $message . '</h3></body></html>';
	die();
}
?>
<!-- Add table to display any error messages with submitted form. -->
<?php
if (!empty($fail)) {
	echo '<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
               <tr><td>Sorry, the following errors were found in your form: 
                  <p style="color: red"><i>' . $fail . '</i></p>
               </td></tr>
            </table>';
}
?>
</div> <!-- Ends <div style="text-align: center"> from header.php -->

<!-- Get Drop-Down List values                                         -->
<?php
$airportValues = $layout->getValueListTwoFields('PHPAirportNameCode');
asort($airportValues);
if ($playerLevel == "High School" || $playerLevel == "HSAA") {
	$methodOfTravelValues = $layout->getValueListTwoFields('Travel Method - HS');
} else {
	$methodOfTravelValues = $layout->getValueListTwoFields('Travel Method');
}
?>

<p>Required Fields: If you submit this form and a required field is missing, the form will reload, and indicate which
	field was missed.</p>
<p>Date Fields: All dates must be entered in the mm/dd/yyyy or yyyy-mm-dd format.</p>
<p>For web form tech support, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</p>

<form action="Travel.php" method="post">

	<?php if ($includeEmbark != "Hidden") { ?>
		<fieldset class="group">
			<legend>Embark</legend>

			<div class="input" style="border-top: none;">
				<label for="EmbarkModeOfTransportation">Method of Travel
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<select name="embarkModeOfTransportation" size="1" id="EmbarkModeOfTransportation"
						  title="How you will get to the event."
					<?php
					$embarkModeOfTransportation_a = " ";
					if (empty($embarkModeOfTransportation) && $includeEmbark == "Mandatory") {
						echo 'class="missing"';
					} else {
						$embarkModeOfTransportation_a = $embarkModeOfTransportation;
					} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($methodOfTravelValues as $value) {
						echo "<option value=\"" . $value . "\"" . ($embarkModeOfTransportation_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>

			<div style="padding: .1em 0 .5em 0"><span style="font-style: italic;">If 'Flying', fill out the following flight fields:</span>
			</div>

			<div class="input">
				<label for="EmbarkDepartureAirport">Departure Airport
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(document).ready(function () {
						$(".embarkDeparture").select2();
					});
				</script>
				<div
					<?php
					$ID_embarkDepartureAirport_a = " ";
					if (empty($ID_embarkDepartureAirport) && $embarkModeOfTransportation == "Flying" && $includeEmbark == "Mandatory") {
						echo 'class="missing"';
					} else {
						$ID_embarkDepartureAirport_a = $ID_embarkDepartureAirport;
					}
					?>
				>
					<select name="ID_embarkDepartureAirport" size="1" class="embarkDeparture" id="EmbarkDepartureAirport"
							  title="The departure airport, going to the event.">
						<option value="">&nbsp;</option>
						<?php
						foreach ($airportValues as $key => $airportValue) {
							echo "<option value=\"" . $key . "\"" . ($ID_embarkDepartureAirport_a == $key ? "selected=\"selected\">" : ">") . $airportValue . "</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="input">
				<label for="embarkDepartureDate">Date of Departure
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(function () {
						$("#embarkDepartureDate").datepicker({
							changeMonth: true,
							changeYear: true
						});
					});
				</script>
				<input name="embarkDepartureDate" type="text" id="embarkDepartureDate"
						 title="The date you are leaving for the event."
					<?php
					if ((empty($embarkDepartureDate) || $embarkDepartureDate == date('m/d/Y')) && $embarkModeOfTransportation == "Flying" && $includeEmbark == "Mandatory") {
						echo 'class="missing"';
					} else {
						echo 'value="' . $embarkDepartureDateSave . '"';
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="EmbarkDepartureTime">Time of Departure (hh:mm)
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="embarkDepartureTime" type="time" id="EmbarkDepartureTime"
						 title="The time you are leaving for the event."
					<?php if ($embarkModeOfTransportation == "Flying") {
						recallText((empty($embarkDepartureTime) ? "" : $embarkDepartureTime), ($includeEmbark == "Mandatory" ? "yes" : "no"));
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="EmbarkFlightNumber">Flight Number
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="embarkFlightNumber" type="text" id="EmbarkFlightNumber"
						 title="The flight number, going to the event."
					<?php
					if ($embarkModeOfTransportation == "Flying") {
						recallText((empty($embarkFlightNumber) ? "" : $embarkFlightNumber), ($includeEmbark == "Mandatory" ? "yes" : "no"));
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="EmbarkConfirmation">Confirmation Number</label>
				<input name="embarkConfirmationNumber" type="text" id="EmbarkConfirmation"
						 title="Your flight's confirmation number, going to the event."
					<?php
					if ($embarkModeOfTransportation == "Flying") {
						recallText((empty($embarkConfirmationNumber) ? "" : $embarkConfirmationNumber), "no");
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="EmbarkArrivalAirport">Arrival Airport
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(document).ready(function () {
						$(".embarkArrival").select2();
					});
				</script>
				<div
					<?php
					$ID_embarkArrivalAirport_a = " ";
					if (empty($ID_embarkArrivalAirport) && $embarkModeOfTransportation == "Flying" && $includeEmbark == "Mandatory") {
						echo 'class="missing"';
					} else {
						$ID_embarkArrivalAirport_a = $ID_embarkArrivalAirport;
					}
					?>
				>
					<select name="ID_embarkArrivalAirport" size="1" id="EmbarkArrivalAirport" class="embarkArrival"
							  title="The arrival airport, going to the event.">
						<option value="">&nbsp;</option>
						<?php
						foreach ($airportValues as $key => $airportValue) {
							echo "<option value=\"" . $key . "\"" . ($ID_embarkArrivalAirport_a == $key ? "selected=\"selected\">" : ">") . $airportValue . "</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="input">
				<label for="embarkArrivalDate">Date of Arrival
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(function () {
						$("#embarkArrivalDate").datepicker({
							changeMonth: true,
							changeYear: true
						});
					});
				</script>
				<input name="embarkArrivalDate" type="text" id="embarkArrivalDate"
						 title="The date you are arriving at the event."
					<?php
					if ((empty($embarkArrivalDate) || $embarkArrivalDate == date('m/d/Y')) && $embarkModeOfTransportation == "Flying" && $includeEmbark == "Mandatory") {
						echo 'class="missing"';
					} else {
						echo 'value="' . $embarkArrivalDateSave . '"';
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="EmbarkArrivalTime">Time of Arrival (hh:mm)
					<?php if ($includeEmbark == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="embarkArrivalTime" type="time" id="EmbarkArrivalTime"
						 title="The time your flight arrives at the airport."
					<?php
					if ($embarkModeOfTransportation == "Flying") {
						recallText((empty($embarkArrivalTime) ? "" : $embarkArrivalTime), ($includeEmbark == "Mandatory" ? "yes" : "no"));
					}
					?>
				/>
			</div>
		</fieldset>
	<?php } ?>

	<?php if ($includeReturn != "Hidden") { ?>
		<fieldset class="group">
			<legend>Return</legend>

			<div class="input" style="border-top: none;">
				<label for="ReturnModeOfTransportation">Method of Travel
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<select name="returnModeOfTransportation" size="1" id="ReturnModeOfTransportation"
						  title="How you will get to the event."
					<?php
					$returnModeOfTransportation_a = " ";
					if (empty($returnModeOfTransportation) && $includeReturn == "Mandatory") {
						echo 'class="missing"';
					} else {
						$returnModeOfTransportation_a = $returnModeOfTransportation;
					} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($methodOfTravelValues as $value) {
						echo "<option value=\"" . $value . "\"" . ($returnModeOfTransportation_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>

			<div style="padding: .1em 0 .5em 0"><span style="font-style: italic;">If 'Flying', fill out the following flight fields:</span>
			</div>

			<div class="input">
				<label for="ReturnDepartureAirport">Departure Airport
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(document).ready(function () {
						$(".returnDeparture").select2();
					});
				</script>
				<div
					<?php
					$ID_returnDepartureAirport_a = " ";
					if (empty($ID_returnDepartureAirport) && $returnModeOfTransportation == "Flying" && $includeReturn == "Mandatory") {
						echo 'class="missing"';
					} else {
						$ID_returnDepartureAirport_a = $ID_returnDepartureAirport;
					}
					?>
				>
					<select name="ID_returnDepartureAirport" size="1" class="returnDeparture" id="ReturnDepartureAirport"
							  title="The departure airport, going to the event.">
						<option value="">&nbsp;</option>
						<?php
						foreach ($airportValues as $key => $airportValue) {
							echo "<option value=\"" . $key . "\"" . ($ID_returnDepartureAirport_a == $key ? "selected=\"selected\">" : ">") . $airportValue . "</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="input">
				<label for="returnDepartureDate">Date of Departure
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(function () {
						$("#returnDepartureDate").datepicker({
							changeMonth: true,
							changeYear: true
						});
					});
				</script>
				<input name="returnDepartureDate" type="text" id="returnDepartureDate"
						 title="The date you are leaving for the event."
					<?php
					if ((empty($returnDepartureDate) || $returnDepartureDate == date('m/d/Y')) && $returnModeOfTransportation == "Flying" && $includeReturn == "Mandatory") {
						echo 'class="missing"';
					} else {
						echo 'value="' . $returnDepartureDateSave . '"';
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="ReturnDepartureTime">Time of Departure (hh:mm)
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="returnDepartureTime" type="time" id="ReturnDepartureTime"
						 title="The time you are leaving for the event."
					<?php if ($returnModeOfTransportation == "Flying") {
						recallText((empty($returnDepartureTime) ? "" : $returnDepartureTime), ($includeReturn == "Mandatory" ? "yes" : "no"));
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="ReturnFlightNumber">Flight Number
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="returnFlightNumber" type="text" id="ReturnFlightNumber"
						 title="The flight number, going to the event."
					<?php
					if ($returnModeOfTransportation == "Flying") {
						recallText((empty($returnFlightNumber) ? "" : $returnFlightNumber), ($includeReturn == "Mandatory" ? "yes" : "no"));
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="ReturnConfirmation">Confirmation Number</label>
				<input name="returnConfirmationNumber" type="text" id="ReturnConfirmation"
						 title="Your flight's confirmation number, going to the event."
					<?php
					if ($returnModeOfTransportation == "Flying") {
						recallText((empty($returnConfirmationNumber) ? "" : $returnConfirmationNumber), "no");
					}
					?>
				/>
			</div>

			<div class="input">
				<label for="ReturnArrivalAirport">Arrival Airport
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<script>
					$(document).ready(function () {
						$(".returnArrival").select2();
					});
				</script>
				<div
					<?php
					$ID_returnArrivalAirport_a = " ";
					if (empty($ID_returnArrivalAirport) && $returnModeOfTransportation == "Flying" && $includeReturn == "Mandatory") {
						echo 'class="missing"';
					} else {
						$ID_returnArrivalAirport_a = $ID_returnArrivalAirport;
					}
					?>
				>
					<select name="ID_returnArrivalAirport" size="1" id="ReturnArrivalAirport" class="returnArrival"
							  title="The arrival airport, going to the event.">
						<option value="">&nbsp;</option>
						<?php
						foreach ($airportValues as $key => $airportValue) {
							echo "<option value=\"" . $key . "\"" . ($ID_returnArrivalAirport_a == $key ? "selected=\"selected\">" : ">") . $airportValue . "</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="input">
				<label for="ReturnArrivalTime">Time of Arrival (hh:mm)
					<?php if ($includeReturn == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="returnArrivalTime" type="time" id="ReturnArrivalTime"
						 title="The time your flight arrives at the airport."
					<?php
					if ($returnModeOfTransportation == "Flying") {
						recallText((empty($returnArrivalTime) ? "" : $returnArrivalTime), ($includeReturn == "Mandatory" ? "yes" : "no"));
					}
					?>
				/>
			</div>
		</fieldset>
	<?php } ?>

	<fieldset class="group">
		<legend>Travel Comments</legend>

		<div class="input" style="border-top: none;">
			<label for="FrequentFlyer">Frequent Flyer Information</label>
			<input name="frequentFlyerInfo" type="text" size="70" id="FrequentFlyer"
					 title="Your frequent flyer information."
				<?php
				recallText((empty($frequentFlyerInfo) ? "" : $frequentFlyerInfo), "no");
				?>
			/>
		</div>

		<div class="input">
			<label for="TravelComments">Travel Comments</label>
			<input name="travelComments" type="text" size="70" id="TravelComments" title="Additional travel comments."
				<?php
				recallText((empty($travelComments) ? "" : $travelComments), "no");
				?>
			/>
		</div>
	</fieldset>

	<?php
	if (empty($IDType)) {
		## Begin HTML block for EventPersonnel IDs ##################################################################
		?>

		<p>
			<input type="submit" name="submit" class="submit" value="Submit"/>
		</p>

		</div> <!-- Container div that does 90% centered margin -->

		<?php
	}
	## End HTML block for EventPersonnel IDs ##########################################################################
	?>

	<input name="respondent_exists" type="hidden" value="true"/>
	<input name="ID" type="hidden" value="<?php echo $ID; ?>"/>
</form>
</body>
</html>