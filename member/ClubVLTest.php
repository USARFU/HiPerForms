<?php

date_default_timezone_set('America/New_York');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include "$root/include/dbaccess-membership.php";
include "$root/include/functions.php";

$requestClubVL = $fm->newFindCommand('Club Value List');
$requestClubVL->addFindCriterion('InvitationalFlag', '=');
$requestClubVL->addFindCriterion('ID', '*');

$resultClubVL = $requestClubVL->execute();
if (FileMaker::isError($resultClubVL)) {
	echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
		. "<p>Error Code 1301: " . $resultClubVL->getMessage() . "</p>";
	die();
}
$recordsClubVL = $resultClubVL->getRecords();

$clubValues = array();

foreach ($recordsClubVL as $recordClubVL) {
	$clubValues[$recordClubVL->getField('ID')] = $recordClubVL->getField('c_clubNameLong');
}

asort($clubValues);

//var_dump($clubValues);

?>

<html>
<head>
	<meta charset="UTF-8">

	<link rel="stylesheet" type="text/css" href="../include/WebForms.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="../include/tabbed/tabbed.css" media="screen"/>

	<script src="../include/script/jquery/jquery.min.js"></script>

	<!-- slim image cropper -->
	<link href="../include/script/slim/slim.css" rel="stylesheet"/>

	<!-- select2 js library for searchable drop down controls -->
	<link href="../include/script/select2/css/select2.min.css" rel="stylesheet"/>
	<script src="../include/script/select2/js/select2.min.js"></script>

	<link href="../include/bootstrap-modified.css" rel="stylesheet"/>
</head>

<body>
<label for="PrimaryClub">Primary Club*</label>
<div id="YesClubFields" class="rightcolumn">
	<select name="ID_Club" size="1" class="select2" id="PrimaryClub"
			  title="The Primary Club you play for.">
		<option value="">&nbsp;</option>
		<?php
		foreach ($clubValues as $key => $clubValue) {
			echo "<option value='" . $key . "' " . ($ID_Club == $key ? "selected='selected'>" : ">") . $clubValue . "</option>";
		}
		?>
	</select>
</div>

<script>
    $(document).ready(function () {

//
        <!-- Searchable drop-down list -->
        $(".select2").select2();
    )
    }
</script>
</body>
</html>