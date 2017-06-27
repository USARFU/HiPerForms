<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Rugby Event Attribute Ratings</title>
</head>

<body>
<!-- Error Codes 281-282 -->
<?php
include_once "header.php";

## Get variable data for form header ########################################
$AttributeResponseCutOff = $campRecord->getField('AttributeResponseCutOff');
#############################################################################

## Verify that event link hasn't expired ####################################
$CutOffCompare_a = new DateTime($AttributeResponseCutOff);
$CutOffCompare = $CutOffCompare_a->format('Y-m-d');
$today = date('Y-m-d');
if ($CutOffCompare < $today || empty($AttributeResponseCutOff)) {
	$message = "This link has expired. You are past this event's cut off date.";
}
#############################################################################

## Verify that attribute records exist ######################################
$related_attribute_records = $record->getRelatedSet('EventPersonnel__Attributes.all');
if (Filemaker::isError($related_attribute_records)) {
	$message = "The Coach has not entered in any Attribute ratings for you yet.";
}
#############################################################################

if (isset($_POST['respondent_exists'])) { // Form has been submitted

	## Begin - Update Attribute records ####
	foreach ($related_attribute_records as $attribute_record) {
		if (!empty ($_POST[$attribute_record->getRecordID()])) {
			// Replace \n\r with just \n for FileMaker
			$comment = str_replace("\r", "", $_POST[$attribute_record->getRecordID()]);
			$params = $attribute_record->getField('EventPersonnel__Attributes.all::ID') . "|" . fix_string($comment);
			$newPerformScript = $fm->newPerformScriptCommand('PHP-EventAttributeNotes', 'PHP Add Player Comment To Attribute Note', $params);
			$result = $newPerformScript->execute();
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 282: " . $result->getMessage() . "</p>";
				die();
			}
		}
	}
	## End - Update Attribute record ######

	$message = "Thank You. Your Attribute responses have been recorded.";

} else { // Form has been freshly loaded

}
?>

<!-- Banner and Error Messages                                     -->
<div class="header background">
	<h1>USA Rugby Event Attribute Ratings</h1>
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
			<td><?php echo $AttributeResponseCutOff; ?></td>
		</tr>
		<tr>
			<td>Venue:</td>
			<td><?php echo $venueName; ?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
</div>

<p>For web form tech support, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</p>

<!-- Show messages instead of form. -->
<?php
if (isset($message)) {
	echo '<br />'
		. '<h3 style="text-align: center">' . $message . '</h3></body></html>';
	die();
}
?>
</div> <!-- Ends <div style="text-align: center"> from header.php -->

<form action="AttributeFeedback.php" method="post" id="mainForm">

	<?php
	foreach ($related_attribute_records as $attribute_record) {
		if (!empty($attribute_record->getField('EventPersonnel__Attributes.all::Notes'))) {
			echo '<fieldset class="group"><legend>' . $attribute_record->getField('EventPersonnel__Attributes.all::Attribute') . '</legend>';
			echo '<div style="position: relative; width: 100%; margin: -20px auto 0 8px;">
						<div style="float: left; width: 25%;"><p style="font-weight: bold; margin-bottom: -1px;">Date:</p>' . $attribute_record->getField('EventPersonnel__Attributes.all::dateEvaluated') . '</div>
						<div style="float: left; width: 25%;"><p style="font-weight: bold; margin-bottom: -1px;">Evaluator:</p>' . $attribute_record->getField('EventPersonnel__Attributes.all::c_EvaluatorName') . '</div>
						<div style="float: left; width: 25%;"><p style="font-weight: bold; margin-bottom: -1px;">Rating:</p>&nbsp&nbsp&nbsp' . $attribute_record->getField('EventPersonnel__Attributes.all::Level') . '</div>
					</div>
					<div style="position: relative; top: 1em; width: auto; margin: 0 auto 10px 8px; clear: both;"><span style="font-weight: bold; margin-bottom: -1px;">Notes:</span><br /><span style="white-space: pre-wrap;">' .
						$attribute_record->getField('EventPersonnel__Attributes.all::Notes') . '</span></div>
					<br />';
			echo '<div style="position: relative; width: 96%; margin: 0 auto 0 8px;">
						<textarea style="width: 100%;" form="mainForm" rows="4" maxlength="500" placeholder="Enter your comments here." name="' . $attribute_record->getRecordID() . '"></textarea></div>';
			echo '</fieldset>';
		}
	}
	?>

	<p>
		<input type="submit" name="submit" class="submit" value="Submit"/>
	</p>

	<input name="respondent_exists" type="hidden" value="true"/>
	<input name="ID" type="hidden" value="<?php echo $ID; ?>"/>
</form>
</div> <!-- Container div that does 90% centered margin -->
</body>
</html>
