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
	$Camp_RecordID = $record->getRecordId();
	################################################################################
	
	## Security ####################################################################
	$related_WebAccessPersonnel = $record->getRelatedSet('Camp__WebAccessPersonnel');
	if (FileMaker::isError($related_WebAccessPersonnel)) {
		$related_WebAccessPersonnel_count = 0;
	} else {
		$related_WebAccessPersonnel_count = count($related_WebAccessPersonnel);
	}
	
	if ($related_WebAccessPersonnel_count > 0) {
		$EditCampArray = array();
		foreach ($related_WebAccessPersonnel as $item) {
			array_push($EditCampArray, $item->getField('Camp__WebAccessPersonnel::ID_Personnel'));
		}
	}
	
	// Die if $ID_Personnel not found in any array
	$ViewCamp = in_array($ID_Personnel, $EditCampArray);
	if (!$ViewCamp) {
		echo "You are not allowed to view this camp.<br />";
		die();
	}
	# /Security #####################################################################
	
	// Add Personnel if ID passed from ManageCamp-AddPersonnel.php
	$AddID = isset($_GET['AddID']) ? fix_string($_GET['AddID']) : "";
	if (!empty($AddID)) {
		$request_AddPersonnel = $fm->newFindCommand('Personnel Search');
		$request_AddPersonnel->addFindCriterion('ID', '==' . $AddID);
		$result_AddPersonnel = $request_AddPersonnel->execute();
		if (FileMaker::isError($result_AddPersonnel)) {
			echo "<p>Error: There was a problem finding the record to add.</p>"
				. "<p>Error Code 1001: " . $result_AddPersonnel->getMessage() . "</p>";
			die();
		} else {
			$RegistrationScript_param = $AddID . "\n" . $ID_Camp . "\nManageCamp";
			$newPerformScript = $fm->newPerformScriptCommand('Member-Tab4-Enrollment', 'Camp Registration', $RegistrationScript_param);
			$scriptResult = $newPerformScript->execute();
			if (FileMaker::isError($scriptResult)) {
				echo "<p>Error: There was a problem executing the script to add the person.</p>"
					. "<p>Error Code 1002: " . $scriptResult->getMessage() . "</p>";
				die();
			} else {
			echo "Script executed<br />AddID: " . $AddID . "<br />ID_Camp: " . $ID_Camp;
				$update_Camp = True;
			}
		}
	}
	
	//
	// Update Members form submitted
	if (isset($_POST['update-members'])) {
		
		$CampMember_Original = isset($_SESSION['CampMember_Original']) ? $_SESSION['CampMember_Original'] : "";
		$CampMember_Update = isset($_POST['CampMember_Update']) ? $_POST['CampMember_Update'] : "";
		$CampMember_changed = ($CampMember_Update == $CampMember_Original ? false : true);
		$CampMember_Remove = isset($_POST['CampMember_Remove']) ? $_POST['CampMember_Remove'] : "";
		
		if ($CampMember_changed) {
			$eMail = $_SESSION['eMail'];
			while ($a = current($CampMember_Update) && $b = current($CampMember_Original)) { //For each element in the array
				$update_RecordID = key($CampMember_Update);
				$update_record = current($CampMember_Update);
				$original_record = current($CampMember_Original);
				$diff = array_diff_assoc($update_record, $original_record);
				if (!empty($diff)) { //Edit the record if there is a difference from the update
					$CampMember_edit = $fm->newEditCommand('Camp__Squad__SquadPersonnel', $update_RecordID);
					foreach ($diff as $CampMember_field => $CampMember_field_value) { //Update only the fields that were in the diff array as changes
						$CampMember_edit->setField($CampMember_field, $CampMember_field_value);
					}
					$CampMember_edit->setField('z_ModifiedByID', $eMail);
					$CampMember_edit->setField('z_ModifiedByName', 'web');
					$CampMember_result = $CampMember_edit->execute();
					if (FileMaker::isError($CampMember_result) && !empty($CampMember_result->code)) { //supress error is date is invalid, and no other changes were made to record
						echo "<p>Error: There was a problem updating your Camp membership history. If this continues, please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
							. "<p>Error Code 1001: (" . $CampMember_result->code . ") " . $CampMember_result->getMessage() . "</p>";
						die();
					}
				}
				next($CampMember_Update);
				next($CampMember_Original);
			}
			$update_Camp = true;
		}
		
		## Remove players from camp ############################
		// key = RecordID; value = ID_Player
		foreach ($CampMember_Remove as $key => $value) {
			if (!empty($value)) {
				$CampMember_delete_request = $fm->newFindCommand('Camp__CampPersonnel');
				$CampMember_delete_request->addFindCriterion('ID_Event', $ID_Camp );
				$CampMember_delete_request->addFindCriterion('ID_Personnel', $value );
				$CampMember_delete_result = $CampMember_delete_request->execute();
				if (FileMaker::isError($CampMember_delete_result)) {
					echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 1002: " . $CampMember_delete_result->getMessage() . "</p>" . "RecordID= " . $key;
					die();
				}
				$CampMember_delete_record = $CampMember_delete_result->getFirstRecord();
				$CampMember_delete = $CampMember_delete_record->delete();
				if (FileMaker::isError($CampMember_delete)) {
					echo "<p>Error: There was a problem removing the player from this camp. If this continues, please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 1003: (" . $CampMember_delete->code . ") " . $CampMember_delete->getMessage() . "</p>";
					die();
				}
				
				$update_Camp = true;
			}
		}
		
	}
	
	## Get data from HiPer to display #############################
	if ($update_Camp) {
		$request = $fm->newFindCommand('Camp');
		$request->addFindCriterion('ID', '==' . $ID_Camp);
		$result = $request->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 1004: " . $result->getMessage() . "</p>";
			die();
		}
		$records = $result->getRecords();
		$record = $result->getFirstRecord();
	}
	
	$CampName = $record->getField('Name');
	$HeadCoach = $record->getField('c_HeadCoachName');
	$Venue = $record->getField('c_Venue');
	$PlayerLevel = $record->getField('PlayerLevel');
	$Gender = $record->getField('Gender');
	$StartDate = $record->getField('StartDate');
	$EndDate = $record->getField('EndDate');
	
	
	## Get Related SquadPersonnel Records #############################
	$related_SquadPersonnel = $record->getRelatedSet('Camp__Squad__SquadPersonnel');
	if (FileMaker::isError($related_SquadPersonnel)) {
		$related_SquadPersonnel_count = 0;
	} else {
		$related_SquadPersonnel_count = count($related_SquadPersonnel);
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
			<a href="body.php?activeTab=5"><span style="color: lightblue">Back to Your Profile</span></a>
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

	<div style="text-align: center">
		<div style="display: inline-block; margin-top: .5em" class="btn btn-primary RemoveMembership_Button">
			<a href="ManageCamp-AddPersonnel.php"><span style="color: lightblue">Add Player</span></a>
		</div>
	</div>
	
	<form id='form-CampMembers' action='ManageCamp.php' method='post'>
		<fieldset class='group aashadow'>
			<legend>&nbspMembers&nbsp<?php echo $message; ?></legend>
			
			<?php
			if ($related_SquadPersonnel_count > 0) {
				
				foreach ($related_SquadPersonnel as $SquadPersonnel_record) {
					
					$Member_RecordID = $SquadPersonnel_record->getRecordID();
					$Member_ID_Personnel = $SquadPersonnel_record->getField('Camp__Squad__SquadPersonnel::ID_Player');
					$Member_Name = $SquadPersonnel_record->getField('Camp__Squad__SquadPersonnel::c_playerLastFirstNick');
					$CampMember[$Member_RecordID]['JerseyNbr'] = $SquadPersonnel_record->getField('Camp__Squad__SquadPersonnel::JerseyNbr');
					
					if (!empty($Member_ID_Personnel)) {
						echo "
						<div class='row-divider row-divider-color'>
							<fieldset class='field' style='width: 40%;'>
								<legend>Player Name</legend>
									" . $Member_Name . "
							</fieldset>
							<fieldset class='field' style='width: 24%;'>
								<legend>Jersey Number</legend>
								<input class='alpha50' name='CampMember_Update[" . $Member_RecordID . "][JerseyNbr]' type='text' size='6'";
								recallText((empty($CampMember[$Member_RecordID]['JerseyNbr']) ? '' : $CampMember[$Member_RecordID]['JerseyNbr']), 'no');
						echo "	/>
							</fieldset>
							<fieldset class='field' style='width: auto'>
								<button id='" . $Member_ID_Personnel . "' class='btn btn-primary RemoveMembership_Button' style='margin: 2px 0 2px 2em; vertical-align: middle;' type='button'>Remove Player</button>
								<input type='hidden' name='CampMember_Remove[" . $Member_RecordID . "]' id='RemoveMembership-" . $Member_ID_Personnel . "'/>
							</fieldset>
						</div>";
					}
				}
			}
			?>

		</fieldset>

		<input type='hidden' name='update-members' value='true'/>
		<!-- The original values array lets us only update field changes upon form submission.
			  Great when only updating 1 field out of 300 potential fields -->
		<?php $_SESSION['CampMember_Original'] = isset($CampMember) ? $CampMember : ""; ?>

		<input type='submit' name='APPLY' value='Update' class='submit buy' id='Submit_Members_Button'/>

		<div id='Submit_Members_Dialog' title='Updating Players'>
			<p>Please wait while the player records are updated. This can take up to a minute.</p>
		</div>

	</form>

	<script>
       $(document).ready(function () {
           //
           <!-- Submit popover -->
           $('#Submit_Members_Button').click(function () {
               $("#Submit_Members_Dialog").dialog("open");
           });
       });

       //
       <!-- Dialog popovers -->
       $(function () {
           $("#Submit_Members_Dialog").dialog({
               autoOpen: false,
               show: {
                   effect: "blind",
                   duration: 1000
               },
               modal: true
           });
       });

       $('.RemoveMembership_Button').click(function () {
           var ID_Club_Remove;
           var button_text = $(this).html();
           var d = new Date();
           var today = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
           ID_Club_Remove = this.id;
           $(this).toggleClass('entypo-cancel');
           if ($(this).is('.entypo-cancel')) {
               $(this).html('&nbsp;&nbsp;' + button_text);
               $(this).css('background-color', 'darkred');
               $("#RemoveMembership-" + ID_Club_Remove).val(ID_Club_Remove);
               if ($("#EndDate-" + ID_Club_Remove).val() === "") {
                   $("#EndDate-" + ID_Club_Remove).val(today);
               }
           } else {
               $(this).html(button_text.replace(/&nbsp;/g, ""));
               $(this).css('background-color', '');
               $("#RemoveMembership-" + ID_Club_Remove).val("");
               $("#EndDate-" + ID_Club_Remove).val("");
           }
       });
	</script>
</div>
</body>
</html>