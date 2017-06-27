<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>USA Rugby Member Profile</title>

	<!-- Error Codes 100+ -->
	
	<?php
	include_once 'header.php';
	
	$RegistrationStage = 1;
	
	## Exit to login screen if ID received from session is invalid #################
	## or if the session has timed out. ############################################
	session_start();
	if (empty($_SESSION['RecordID']) || $_SESSION['timeout'] + 60 * 60 * 4 < time()) {
		header("location: login.php");
		unset($_SESSION['RecordID']);
		die();
	} else {
		$recordID = $_SESSION['RecordID'];
	}
	$_SESSION['timeout'] = time();
	################################################################################
	
	//## Determine Current Season ##//
	$thisYear = date('Y');
	$thisMonth = date('m');
	$thisDay = date('d');
	
	if ($thisMonth < 6 || ($thisMonth == 6 && $thisDay < 30)) {
		$season = ($thisYear - 1) . '/' . $thisYear;
	} else {
		$season = $thisYear . '/' . ($thisYear + 1);
	}
	
	$today = date('m/d/Y');
	
	## Grab Account's Personnel record #############################################
	
	//## If provided, grab Club Member's ID
	$ID_Personnel = isset($_GET['ID']) ? fix_string($_GET['ID']) : "";
	$request_Header = $fm->newFindCommand('Member-Header');
	if (empty($ID_Personnel)) {
		$request_Header->addFindCriterion('RecordID', '==' . $recordID);
		$EditingMemberProfile = false;
	} else {
		$request_Header->addFindCriterion('ID', '==' . $ID_Personnel);
		$EditingMemberProfile = true;
	}
	$result_Header = $request_Header->execute();
	if (FileMaker::isError($result_Header)) {
		echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
			. "<p>Error Code 100: " . $result->getMessage() . "</p>";
		die();
	}
	$records_Header = $result_Header->getRecords();
	$record_Header = $result_Header->getFirstRecord();
	
	if ($EditingMemberProfile) {
		// Security: die if selected Club ID is not one of member's clubs
		$ActiveClubMembershipIDs = $record_Header->getField('c_ActiveClubMembershipIDs');
		$ID_Club = $_SESSION['ClubAccess_ID'];
		if (strstr($ActiveClubMembershipIDs, $ID_Club) == false) {
			echo "You are not allowed to view the selected profile.";
			die();
		}
	}
	
	$ID_Personnel = $record_Header->getField('ID');
	$U18 = ($record_Header->getField('Age') < 18 ? true : false);
	$U19 = ($record_Header->getField('Age') < 19 ? true : false);
//	$PrimaryRole = $record_Header->getField('PrimaryRole');
	$ActiveClubRoles = $record_Header->getField('c_ActiveClubRoles');
	$IsPlayer = (strpos($ActiveClubRoles, "Player") === false ? false : true);
	$IsManager = (strpos($ActiveClubRoles, "Manager") === false ? false : true);
	$IsCoach = (strpos($ActiveClubRoles, "Coach") === false ? false : true);
	$PreferredName = $record_Header->getField('c_PreferredName');
	$eMail = $record_Header->getField('eMail'); // Need for z_ModifiedBy field
	
	$RegistrationActivate = ($record_Header->getField('z_AccessWebFormRegistration') == 1 ? true : false);
	if (isset($_GET['activeTab'])) {
		$activeTab = $_GET['activeTab'];
	}
	################################################################################
	
	include 'formHandler.php';
	
	?>
</head>

<body>
<div id="container">

	<div class="header" style="text-align: center; position: relative; height: 100px">
		<div style="position: absolute; left: 1em">
			<img src="../include/USAR-logo.png" alt="logo"/>
		</div>
		<h1 class="narrow">USA Rugby Profile for <?php echo $PreferredName; ?></h1>
		<h2 class="narrow">Active Club Role(s): <?php echo str_replace("\n", ", ", $ActiveClubRoles); ?></h2>
	</div>

	<div style="position: relative">
		<div style="position: absolute; top: -28px; right: 8px; font-size: 125%">
			<?php if ($EditingMemberProfile) { ?>
				<a href="ManageClub.php"><span style="color: dimgray">Close</span></a>
			<?php } else { ?>
				<a href="login.php?Logout=True"><span style="color: dimgray">Logout</span></a>
			<?php } ?>
		</div>
	</div>

	<section id="tabbed">
		
		<?php if ($RegistrationActivate) { ?>

			<!-- First tab input and label (REGISTRATION) -->
			<input id="t-1" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=1<?php if ($EditingMemberProfile) {
				echo "&ID=" . $ID_Personnel;
			} ?>'" <?php if ($activeTab == 1) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-1" class="tabs shadow entypo-vcard">Registration</label>
		
		<?php } else { ?>

			<input id="t-1" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=1<?php if ($EditingMemberProfile) {
				echo "&ID=" . $ID_Personnel;
			} ?>'" <?php if ($activeTab == 1) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-1" class="tabs shadow entypo-vcard">Club Membership</label>
		
		<?php }
		
		if (!$RegistrationActivate && ($ClubmembershipStatus == 'red' || $ClubmembershipStatus == 'black')) {
			//Disable Profile tab
			?>

			<input id="t-2-disabled" name="tabbed-tabs" type="radio" class="radiotab"
					 title="Update your profile before accessing this tab."/>
			<label class="tabs shadow entypo-newspaper" style="color: gray">Profile</label>
		
		<?php } else { ?>

			<!-- Second tab input and label (PROFILE) -->
			<input id="t-2" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=2<?php if ($EditingMemberProfile) {
				echo "&ID=" . $ID_Personnel;
			} ?>'" <?php if ($activeTab == 2) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-2" class="tabs shadow entypo-newspaper">Profile</label>

			<!-- If > 180 days since last profile update, disable the following tabs: -->
			<?php
		}
		if (($ProfileStatus == 'red' || $ProfileStatus == 'black') || (!$RegistrationActivate && ($ClubmembershipStatus == 'red' || $ClubmembershipStatus == 'black'))) { ?>
			<input id="t-3-disabled" name="tabbed-tabs" type="radio" class="radiotab"
					 title="Update your profile before accessing this tab."/>
			<label class="tabs shadow entypo-folder" style="color: gray">History</label>
			<input id="t-4-disabled" name="tabbed-tabs" type="radio" class="radiotab"
					 title="Update your profile before accessing this tab."/>
			<label class="tabs shadow entypo-megaphone" style="color: gray">Camp Enrollment</label>
			<?php if (!$IsPlayer) { ?>
				<input id="t-5-disabled" name="tabbed-tabs" type="radio" class="radiotab"
						 title="Update your profile before accessing this tab."/>
				<label class="tabs shadow entypo-archive" style="color: gray">Manage</label>
				<input id="t-6-disabled" name="tabbed-tabs" type="radio" class="radiotab"
						 title="Update your profile before accessing this tab."/>
				<label class="tabs shadow entypo-feather" style="color: gray">Eagle Files</label>
			<?php } ?>
		<?php } else { ?>
			<!-- /disable tabs if > 180 since profile update -->
			<!-- Third tab input and label (HISTORY) -->
			<input id="t-3" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=3<?php if ($EditingMemberProfile) {
				echo "&ID=" . $ID_Personnel;
			} ?>'" <?php if ($activeTab == 3) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-3" class="tabs shadow entypo-folder">History</label>
			<!-- Fourth tab input and label (CAMP ENROLLMENT) -->
			<input id="t-4" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=4<?php if ($EditingMemberProfile) {
				echo "&ID=" . $ID_Personnel;
			} ?>'" <?php if ($activeTab == 4) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-4" class="tabs shadow entypo-megaphone">Camp Enrollment</label>
			<!-- Fifth tab input and label (MANAGE) -->
			<?php if ($IsCoach || $IsManager) { ?>
				<input id="t-5" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=5<?php if ($EditingMemberProfile) {
					echo "&ID=" . $ID_Personnel;
				} ?>'" <?php if ($activeTab == 5) {
					echo 'checked="checked"';
				} ?> class="radiotab loading"/>
				<label for="t-5" class="tabs shadow entypo-archive">Manage</label>
				<input id="t-6" name="tabbed-tabs" type="radio" onclick="window.open('https://eaglefiles.org/technical-guide?39RldjfzO8owtCykO4RB','_blank');" class="radiotab"/>
				<label for="t-6" class="tabs shadow entypo-feather">Eagle Files</label>
			<?php } ?>
		
		<?php } ?>
		<!-- Seventh tab input and label (ACCOUNT) -->
		<input id="t-7" name="tabbed-tabs" type="radio" onclick="location.href='body.php?activeTab=7<?php if ($EditingMemberProfile) {
			echo "&ID=" . $ID_Personnel;
		} ?>'" <?php if ($activeTab == 7) {
			echo 'checked="checked"';
		} ?> class="radiotab loading"/>
		<label for="t-7" class="tabs shadow entypo-user">Account</label>
		<!-- Tabs wrapper -->
		<div class="wrapper shadow">
			
			<?php if ($RegistrationActivate) { ?>
				<!-- Tab 1 content (Registration) -->
				<div class="tab-1">
					
					<?php
					if ($activeTab == 1) {
						switch ($RegistrationStage) {
							case 2;
								include_once 'Tab1-Registration2.php';
								break;
							case 3;
								include_once 'Tab1-Registration3.php';
								break;
							case 4;
								include_once 'Tab1-Registration4.php';
								break;
							case 5;
								include_once 'Tab1-Registration5.php';
								break;
							default;
								include_once 'Tab1-Registration.php';
						}
					}
					?>

				</div>
			
			<?php } else { ?>

				<div class="tab-1">
					
					<?php if ($activeTab == 1) {
						include_once 'Tab1-ClubMembershipNoRegistration.php';
					} ?>

				</div>
			
			<?php } ?>
			<!-- / Tab 1 content (Registration)-->

			<!-- Tab 2 content (Profile) -->
			<div class="tab-2">
				
				<?php if ($activeTab == 2) {
					if ($RegistrationActivate) {
						include_once 'Tab2-Profile.php';
					} else {
						include_once 'Tab2-ProfileNoRegistration.php';
					}
				} ?>

			</div>
			<!-- / Tab 2 content (Profile)-->

			<!-- Tab 3 content (History) -->
			<div class="tab-3">

				<div class="mt-10"></div>
				
				<?php if ($activeTab == 3) {
					include_once 'Tab3-History.php';
				} ?>

			</div>
			<!-- / Tab 3 content (History)-->

			<!-- Tab 4 content (Enrollment) -->
			<div class="tab-4">
				
				<?php if ($activeTab == 4) {
					include_once 'Tab4-CampEnrollment.php';
				} ?>

			</div>
			<!-- / Tab 4 content (Enrollment)-->

			<!-- Tab 5 content (My Clubs) -->
			<?php if ($IsCoach || $IsManager) { ?>
				<div class="tab-5">
					
					<?php if ($activeTab == 5) {
						include_once 'Tab5-Manage.php';
					} ?>

				</div>
			<?php } ?>
			<!-- / Tab 5 content (My Clubs)-->

			<!-- Tab 6 content (Find a Club) -->
			<?php if (!$EditingMemberProfile) { ?>
				<div class="tab-6">
					
					<?php if ($activeTab == 6) {
						include_once 'Tab6-FindAClub.php';
					} ?>

				</div>
			<?php } ?>
			<!-- / Tab 6 content (My Clubs)-->

			<!-- Tab 7 content (Account) -->
			<div class="tab-7">
				
				<?php if ($activeTab == 7) {
					include_once 'Tab7-Account.php';
				} ?>

			</div>
			<!-- / Tab 7 content (Account)-->

		</div>
		<!-- / Tabs wrapper -->
	</section>

</div>

</body>
</html>