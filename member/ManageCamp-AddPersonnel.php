<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>USA Rugby HiPer Database - Manage Camp</title>

	<!-- Error Codes 1000+ -->
	
	<?php
	include_once 'header.php';
	$stateValues = $layout_Header->getValueListTwoFields('State-Territory');
	
	$layout =& $fm->getLayout('Camp');
	
	## Exit to login screen if ID received from session is invalid #################
	## or if the session has timed out. ############################################
	session_start();
	if (empty($_SESSION['RecordID']) || $_SESSION['timeout'] + 60 * 60 * 4 < time()) {
		header("location: login.php");
		unset($_SESSION['id']);
		die();
	} else {
		$recordID = $_SESSION['RecordID'];
	}
	$_SESSION['timeout'] = time();
	################################################################################
	
	$ID_Camp = $_SESSION['CampAccess_ID'];
	$ID_Personnel = $_SESSION['ID_Personnel'];
	
	## Grab Camp's record ##########################################################
	$request = $fm->newFindCommand('Camp');
	$request->addFindCriterion('ID', '==' . $ID_Camp);
	$result = $request->execute();
	if (FileMaker::isError($result)) {
		echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
			. "<p>Error Code 1000: " . $result->getMessage() . "</p>";
		die();
	}
	$records = $result->getRecords();
	$record = $result->getFirstRecord();
	################################################################################
	
	## Get data from HiPer to display #############################
	$CampName = $record->getField('Name');
	$HeadCoach = $record->getField('c_HeadCoachName');
	$Venue = $record->getField('c_Venue');
	$PlayerLevel = $record->getField('PlayerLevel');
	$Gender = $record->getField('Gender');
	$StartDate = $record->getField('StartDate');
	$EndDate = $record->getField('EndDate');
	
	if (isset($_POST['submitted-AddPerson'])) {
		$Search_by_Name = isset($_POST['Search_by_Name']) ? fix_string($_POST['Search_by_Name']) : "";
		$Search_by_City = isset($_POST['Search_by_City']) ? fix_string($_POST['Search_by_City']) : "";
		$Search_by_State = isset($_POST['Search_by_State']) ? fix_string($_POST['Search_by_State']) : "";
		$Search_by_Gender = isset($_POST['Search_by_Gender']) ? fix_string($_POST['Search_by_Gender']) : "";
		
		if (strlen($Search_by_Name) < 3) {
			$message_PersonnelFind = "Name Search Criteria not valid";
		} else {
			//## Perform Find Request ##//
			$compoundPersonnelFindRequest =& $fm->newCompoundFindCommand('Personnel Search');
			$PersonnelFindRequest =& $fm->newFindRequest('Personnel Search');
			$PersonnelFindRequest->addFindCriterion('DOD', '=');
			$PersonnelFindRequest->addFindCriterion('c_ActiveClubRoles', 'Player');
			$PersonnelFindRequest->addFindCriterion('c_lastFirstNick', '*' . $Search_by_Name . '*');
			$PersonnelFindRequest->addFindCriterion('z_DaysSinceSuccessfulProfileUpdate', '*');
			$PersonnelFindRequest->addFindCriterion('z_DaysSinceSuccessfulProfileUpdate', '<180');
			if (!empty($Search_by_City)) {
				$PersonnelFindRequest->addFindCriterion('City', $Search_by_City);
			}
			if (!empty($Search_by_State)) {
				$PersonnelFindRequest->addFindCriterion('State', '==' . $Search_by_State);
			}
			if (!empty($Search_by_Gender)) {
				$PersonnelFindRequest->addFindCriterion('gender', $Search_by_Gender);
			}
			$compoundPersonnelFindRequest->add(1, $PersonnelFindRequest);
			$compoundPersonnelFindRequest->addSortRule('c_lastFirstNick', 1, FILEMAKER_SORT_ASCEND);
			$PersonnelFindResult = $compoundPersonnelFindRequest->execute();
			if (FileMaker::isError($PersonnelFindResult)) {
				$PersonnelFind_count = 0;
				$message_PersonnelFind = "No records found.";
			} else {
				$PersonnelFind_count = $PersonnelFindResult->getFoundSetCount();
				$PersonnelFind_records = $PersonnelFindResult->getRecords();
			}
		}
	}
	?>

</head>

<body>
<div id="container">

	<div style="text-align: center">
		<img src="../include/USAR-logo.png" alt="logo"/>
		<div class="header background">
			<h1>Camp Manager</h1>
			<table class="tableHeaderTwo">
				<tr>
					<td style="width: 18%">Camp:</td>
					<td style="width: 39%"><?php echo $CampName; ?></td>
					<td style="width: 18%">Dates of Event:</td>
					<td style="width: 25%"><?php echo $StartDate . " - " . $EndDate; ?></td>
				</tr>
				<tr>
					<td>Head Coach:</td>
					<td><?php echo $HeadCoach; ?></td>
					<td>Gender:</td>
					<td><?php echo $Gender; ?></td>
				</tr>
				<tr>
					<td>Venue:</td>
					<td><?php echo $Venue; ?></td>
					<td>Level:</td>
					<td><?php echo $PlayerLevel; ?></td>
				</tr>
			</table>
		</div>
	</div>

	<div style="position: relative">
		<div style="position: absolute; top: -30px; right: 6px; font-size: 125%">
			<a href="ManageCamp.php"><span style="color: lightblue">Cancel</span></a>
		</div>
	</div>

	<!-- Add table to display any error messages from submitted form. -->
	<?php if (!empty($fail) && !empty($_POST['edit-camp-submitted'])) { ?>
		<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
			<tr>
				<td>The update failed due to the following problems:
					<p style="color: red"><i>
							<?php echo $fail; ?>
						</i></p>
				</td>
			</tr>
		</table>
	<?php } ?>
	<!-- ################################# -->

	<form action="ManageCamp-AddPersonnel.php" method="post">

		<fieldset class="group aashadow">
			<legend>&nbsp;Add a Player&nbsp;</legend>


			<fieldset class="group" style="width: 50%">
				<legend>&nbsp;Search Criteria&nbsp;</legend>

				<div class="input" style="border-top: none;">
					<label for="Search_by_Name">Name<br /><small>(3 or more letters required)</small></label>
					<input name="Search_by_Name" type="text" id="Search_by_Name"
							 size="24" <?php recallText((empty($Search_by_Name) ? "" : $Search_by_Name), "no"); ?> />
				</div>

				<div class="input">
					<label for="Search_by_City">City <small>(optional)</small></label>
					<input name="Search_by_City" type="text" id="Search_by_City"
							 size="24" <?php recallText((empty($Search_by_City) ? "" : $Search_by_City), "no"); ?> />
				</div>

				<div class="input">
					<label for="Search_by_State">State <small>(optional)</small></label>
					<select name="Search_by_State" size="1" id="Search_by_State" title="State or Canadian Province">
						<option value=""></option>
						<?php
						foreach ($stateValues as $value) {
							echo "<option value='" . $value . "' " . ($Search_by_State == $value ? "selected='selected'>" : ">") . $value . "</option>";
						}
						?>
					</select>
				</div>

				<div class="input">
					<label for="Search_by_Gender">Gender <small>(optional)</small></label>
					<select name="Search_by_Gender" size="1" id="Search_by_Gender" title="Gender">
						<option value=""></option>
						<option value="Female" <?php if ($Search_by_Gender == 'Female') { echo " selected='selected'"; } ?> >Female</option>
						<option value="Male" <?php if ($Search_by_Gender == 'Male') { echo " selected='selected'"; } ?> >Male</option>
					</select>
				</div>

			</fieldset>

			<input type="submit" name="SEARCH" value="SEARCH" class="submit buy" id="Search_Button"/>
			
			<?php
			if ($PersonnelFind_count > 0) {
				echo "
					<fieldset class='group'>
					<legend>&nbsp;Records Found: " . $PersonnelFind_count . "&nbsp;</legend>
					<div class='aacell aacellheader' style='width: 23%; padding-left: 14px'>Name</div>
					<div class='aacell aacellheader' style='width: 20%'>City, State</div>
					<div class='aacell aacellheader' style='width: 8%'>Gender</div>
					<div class='aacell aacellheader' style='width: 5%'>Age</div>
					<div class='aacell aacellheader' style='width: 26%'>Contact</div>
					<div class='aacell aacellheader' style='width: 13%'>&nbsp;</div>
					<form name='form-ClubAccess' id='form-ClubAccess' action='body.php' method='post'>
				";
				
				foreach ($PersonnelFind_records as $PersonnelFind_record) {
					
					$PersonnelFind_ID = $PersonnelFind_record->getField('ID');
					$PersonnelFind_Name = $PersonnelFind_record->getField('c_lastFirstNick') == "" ? "-" : $PersonnelFind_record->getField('c_lastFirstNick');
					$PersonnelFind_Location = $PersonnelFind_record->getField('City') == "" ? "-" : $PersonnelFind_record->getField('City');
					$PersonnelFind_Location .= "<br />";
					$PersonnelFind_Location .= $PersonnelFind_record->getField('State') == "" ? "-" : $PersonnelFind_record->getField('State');
					$PersonnelFind_Gender = $PersonnelFind_record->getField('gender') == "" ? "-" : $PersonnelFind_record->getField('gender');
					$PersonnelFind_Age = $PersonnelFind_record->getField('Age') == "" ? "-" : $PersonnelFind_record->getField('Age');
					$PersonnelFind_Contact = $PersonnelFind_record->getField('eMail') == "" ? "-" : $PersonnelFind_record->getField('eMail');
					$PersonnelFind_Contact .= "<br />";
					$PersonnelFind_Contact .= $PersonnelFind_record->getField('PrimaryPhoneNumber') == "" ? "-" : $PersonnelFind_record->getField('PrimaryPhoneNumber');
					$PersonnelFind_DaysSinceProfileUpdate = $PersonnelFind_record->getField('z_DaysSinceSuccessfulProfileUpdate');
					
						echo "
						<div class='row-divider row-divider-color'>
					
							<div class='aacell' style='width: 23%'>
								<p>" . $PersonnelFind_Name . "</p>
							</div>
							<div class='aacell' style='width: 20%'>
								<p>" . $PersonnelFind_Location . "</p>
							</div>
							<div class='aacell' style='width: 8%'>
								<p>" . $PersonnelFind_Gender . "</p>
							</div>
							<div class='aacell' style='width: 5%'>
								<p>" . $PersonnelFind_Age . "</p>
							</div>
							<div class='aacell' style='width: 26%'>
								<p>" . $PersonnelFind_Contact . "</p>
							</div>
							<div class='aacell' style='width: 13%'>
								<button class='btn btn-primary' style='margin: 8px 0 2px 2em;' type='submit'
									  formaction='ManageCamp.php?AddID=" . $PersonnelFind_ID . "' >Add
								</button>
							</div>
						</div>
			
					";
				}
				
				echo "
		</fieldset>";
			} else {
				echo '<h3>' . $message_PersonnelFind . '</h3>';
			}
			?>
		</fieldset>

		<input type="hidden" name="submitted-AddPerson" value="true"/>
	</form>
</div>
</body>
</html>