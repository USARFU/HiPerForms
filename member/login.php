<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>USA Rugby HiPer Database - Login</title>
</head>

<?php
include_once 'header.php';
$stateValues = $layout_Header->getValueListTwoFields('State-Territory');

//
//Do this if loading the page after clicking 'Logout'
if (isset($_GET['Logout'])) {
	if ($_GET['Logout'] == True) {
		unset($_SESSION['id']);
		header("location: login.php");
	}
}
//----

//
//The t-1 'Login' form is submitted
if (isset($_POST['submitted-login'])) {
	if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] == FALSE) {
		$fail .= "reCAPTCHA verification failed. <br />";
	} else { // Don't connect to database if recaptcha fails
		if (!empty($_POST['eMail'])) {
			$eMail = fix_string($_POST['eMail']);
			$request = $fm->newFindCommand('Member-Header');
			$request->addFindCriterion('eMail', '==' . $eMail);
			$request->addFindCriterion('z_Exclusive_TicketBuyerDB', '=');
			$result = $request->execute();
			if (FileMaker::isError($result)) {
				$record = "";
//			$error = $result->getMessage();
			} else {
				$record = $result->getFirstRecord();
				$Password_MD5 = $record->getField('Personnel2::Password_MD5');
			}
			if (empty($record)) {
				$fail .= "That e-mail address does not match any account. <br />";
			} elseif (empty($Password_MD5)) {
				$fail .= "That e-mail address matches an account, but the password for it hasn't been set yet. Select <i>Request New Password</i> to have a new one e-mailed you. <br />";
			}
		} elseif (isset($_POST['submitted'])) {
			$fail .= "The e-Mail Address is missing.";
		}
		if (isset($_POST['password'])) {
			$pwdMD5 = strtoupper(md5($_POST['password']));
			if ($pwdMD5 != $Password_MD5 && empty($fail)) {
				$fail .= "The password is incorrect. <br />";
			}
		}
	}
	
	if (empty($fail)) {
		session_start();
		$recordID = $record->getRecordId();
		$_SESSION['RecordID'] = $recordID;
		$_SESSION['timeout'] = time();
		header("location: body.php");
	}
}
//----

//
//The t-2 'New Account' form is submitted
if (isset($_POST['submitted-create'])) {
	$activeTab = 2;
	$failNewAccount = "";
	
	if (isset($_POST['firstName'])) {
		$firstName = fix_string($_POST['firstName']);
		$failNewAccount .= validate_empty_field($firstName, "First Name");
	}
	if (isset($_POST['lastName'])) {
		$lastName = fix_string($_POST['lastName']);
		$failNewAccount .= validate_empty_field($lastName, "Last Name");
	}
	$DOB = "";
	$DOBsave = "";
	if (isset($_POST['DOB'])) {
		if (validate_date($_POST['DOB']) || validate_date_filemaker($_POST['DOB'])) {
			$DOBold = new DateTime($_POST['DOB']);
			$DOB = $DOBold->format('m/d/Y');
			$DOBsave = $DOBold->format('Y-m-d');
		} else {
			$failNewAccount .= validate_DOB($DOB);
			$DOBsave = $_POST['DOB'];
		}
	}
	if (isset($_POST['eMail'])) {
		$eMail = fix_string($_POST['eMail']);
		$failNewAccount .= validate_eMail($eMail);
	}
	if (isset ($_POST['gender'])) {
		$gender = fix_string($_POST['gender']);
		$failNewAccount .= validate_empty_field($gender, "Gender");
	}
	
	if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] == FALSE) {
		$failNewAccount .= "reCAPTCHA verification failed. <br />";
	}
	
	if (empty($failNewAccount)) {
		## Check to see if any duplicates already exist in the database #############
		$eMailrequest = $fm->newFindCommand('Member-Header');
		$eMailrequest->addFindCriterion('eMail', '==' . $eMail);
		$eMailrequest->addFindCriterion('z_Exclusive_TicketBuyerDB', '=');
		$eMailresult = $eMailrequest->execute();
		if (!FileMaker::isError($eMailresult)) {
			$failNewAccount .= "An account with that e-mail address already exists. Select the Request New Password option instead. <br />";
		} else {
			$request = $fm->newFindCommand('Member-Registration1-Demographic');
			$request->addFindCriterion('DOB', '==' . $DOB);
			$request->addFindCriterion('firstName', '==' . $firstName);
			$request->addFindCriterion('lastName', '==' . $lastName);
			$request->addFindCriterion('c_eMailValidator', '==' . 1);
			$request->addFindCriterion('z_Exclusive_TicketBuyerDB', '=');
			$result = $request->execute();
			if (!FileMaker::isError($result)) {
				$failNewAccount .= "An account with the same Name and Date of Birth already exists. Select the Forgot Account E-Mail option to look up your account. <br />";
			}
		}
		#############################################################################
	}
	if (empty($failNewAccount)) {
		## Check to see if an Exclusive record exists that needs to be converted ########
		$ExclusiveTBDBrequest = $fm->newFindCommand('Member-Header');
		$ExclusiveTBDBrequest->addFindCriterion('eMail', '==' . $eMail);
		$ExclusiveTBDBrequest->addFindCriterion('z_Exclusive_TicketBuyerDB', '=' . 1);
		$ExclusiveTBDBresult = $ExclusiveTBDBrequest->execute();
		
		if (!FileMaker::isError($ExclusiveTBDBresult)) {
			$z_Exclusive_TicketBuyerDB = 1;
		} else {
			$z_Exclusive_TicketBuyerDB = "";
		}
		
		## Create New Account and e-mail out confirmation link ##########################
		$newPersonnelRecord = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'DOB' => $DOB,
			'gender' => $gender,
			'eMail' => $eMail,
			'z_Exclusive_TicketBuyerDB' => $z_Exclusive_TicketBuyerDB,
		);
		$newRecordRequest =& $fm->newAddCommand('NewAccount', $newPersonnelRecord);
		$result = $newRecordRequest->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: Your account could not be created.</p>"
				. "<p>Error Code 001: " . $result->getMessage() . "</p>";
			echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review the problem.</p>";
			exit;
		} else {
			$newRecord = current($result->getRecords());
			$ID_Account = $newRecord->getField('ID');
		}
		
		//			NEED TO UPDATE SCRIPT'S MESSAGE THAT IS EMAILED OUT	//
		$newPerformScript = $fm->newPerformScriptCommand('NewAccount', 'ConfirmationLink', $ID_Account);
		$scriptResult = $newPerformScript->execute();
		if (FileMaker::isError($scriptResult)) {
			echo "<p>Error: Could not send you the confirmation e-mail.</p>"
				. "<p>Error Code 003: " . $scriptResult->getMessage() . "</p>";
			echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review the problem.</p>";
			die();
		} else {
			$message2 = "Your account has been created. A confirmation link has been e-mailed to the address provided. You must click this link within 48 hours to activate your account.";
		}
		
		#############################################################################
	} elseif (!empty($failNewAccount)) {
		//## Red Field Borders on required fields that failed
		echo '
		<style type="text/css">
			.missing {
			border: 2px solid red
			}
		</style>';
	}
}
//---- End 'New Account' form is submitted

//
//The t-3 'New Password' form is submitted
if (isset($_POST['submitted-password'])) {
	$activeTab = 3;
	
	if (isset($_POST['eMail_password'])) {
		$eMail_password = fix_string($_POST['eMail_password']);
		$fail .= validate_eMail($eMail_password);
	}
	if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] == FALSE) {
		$fail .= "reCAPTCHA verification failed. <br />";
	}

## Try to find a match, and e-mail new password if match found #################
	if (empty($fail)) {
		$request = $fm->newFindCommand('Member-Header');
		$request->addFindCriterion('eMail', '==' . $eMail_password);
		$request->addFindCriterion('z_Exclusive_TicketBuyerDB', '=');
		$result = $request->execute();
		
		if (FileMaker::isError($result)) {
			$fail .= "That e-mail is not associated with any account. <br />";
			$fail .= $result->getMessage();
		} else {
			$record = $result->getFirstRecord();
			$ID = $record->getField('ID');
//			NEED TO UPDATE SCRIPT'S MESSAGE THAT IS EMAILED OUT	//
			$newPerformScript = $fm->newPerformScriptCommand('Member-Header', 'eMail new Password (ID)', $ID);
			$scriptResult = $newPerformScript->execute();
			if (FileMaker::isError($scriptResult)) {
				$fail .= $scriptResult->getMessage() . "<br />";
			} else {
				$message3 = "An e-mail has been sent to the address given with the new password.";
			}
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
################################################################################
}
//---- End 'New Password' form submitted

//
//The t-4 'Forgot e-mail' form submitted
if (isset($_POST['submitted-email'])) {
	$activeTab = 4;
	
	if (isset($_POST['firstName'])) {
		$firstName = fix_string($_POST['firstName']);
		$fail .= validate_empty_field($firstName, "First Name");
	}
	if (isset($_POST['lastName'])) {
		$lastName = fix_string($_POST['lastName']);
		$fail .= validate_empty_field($lastName, "Last Name");
	}
	if (isset($_POST['DOB'])) {
		if (validate_date($_POST['DOB']) || validate_date_filemaker($_POST['DOB'])) {
			$DOBold = new DateTime($_POST['DOB']);
			$DOB = $DOBold->format('m/d/Y');
			$DOBsave = $DOBold->format('Y-m-d');
		} else {
			$fail .= "The DOB is in the wrong format. <br />";
			$DOBsave = $_POST['DOB'];
		}
	}
	if (isset ($_POST['zipCode'])) {
		$zipCode = fix_string($_POST['zipCode']);
	}
	if (isset ($_POST['Cell'])) {
		$Cell = fix_string($_POST['Cell']);
	}
	if (empty($zipCode) && empty($Cell)) {
		$fail .= "Either your Zip Code or Cell number must be entered. <br />";
	}
	if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] == FALSE) {
		$fail .= "reCAPTCHA verification failed. <br />";
	}
	
	if (empty($fail)) {
		## Try to find a match, and return e-mail if match found ####################
		if (isset($zipCode) || isset($Cell)) {
			$request = $fm->newFindCommand('Member-Registration1-Demographic');
			$request->addFindCriterion('firstName', '==' . $firstName);
			$request->addFindCriterion('lastName', '==' . $lastName);
			$request->addFindCriterion('DOB', '==' . $DOB);
			$request->addFindCriterion('z_Exclusive_TicketBuyerDB', '=');
			if (!empty($zipCode)) {
				$request->addFindCriterion('zipCode', '==' . $zipCode);
			}
			if (!empty($Cell)) {
				$request->addFindCriterion('c_PrimaryPhoneNumberDigits', '==' . $Cell);
			}
			$result = $request->execute();
			
			if (FileMaker::isError($result)) {
				$fail .= "No Match was Found. <br />";
			} else {
				$record = $result->getFirstRecord();
				$eMail = $record->getField('eMail');
				$message4 = "Record Found. E-mail address is " . $eMail;
			}
		}
		#############################################################################
	}
	if (empty($fail)) {
		//## Red Field Borders on required fields that failed
		echo '
		<style type="text/css">
			.missing {
			border: 2px solid red
			}
		</style>';
	}
}
//---- End 'Forgot e-mail' form submitted

//
// Tab 6: Find a Club form submitted
$FindAClub = (isset($_POST['submitted-ClubSearch']) ? True : False);

if ($FindAClub) {
	$activeTab = 6;
	$message_ClubFind = "";
	
	$Search_by_City = isset($_POST['Search_by_City']) ? fix_string($_POST['Search_by_City']) : "";
	$Search_by_State = isset($_POST['Search_by_State']) ? fix_string($_POST['Search_by_State']) : "";
	$Search_by_Name = isset($_POST['Search_by_Name']) ? fix_string($_POST['Search_by_Name']) : "";
	
	if (!empty($Search_by_City) || !empty($Search_by_State) || !empty($Search_by_Name)) {
		//## Perform Find Request ##//
		$compoundClubFindRequest =& $fm->newCompoundFindCommand('Club Search');
		$ClubFindRequest =& $fm->newFindRequest('Club Search');
		$ClubFindRequest->addFindCriterion('InvitationalFlag', '=');
		if (!empty($Search_by_City)) {
			$ClubFindRequest->addFindCriterion('City', $Search_by_City);
		}
		if (!empty($Search_by_State)) {
			$ClubFindRequest->addFindCriterion('State', '==' . $Search_by_State);
		}
		if (!empty($Search_by_Name)) {
			$ClubFindRequest->addFindCriterion('c_clubNameLong', $Search_by_Name);
		}
		$compoundClubFindRequest->add(1, $ClubFindRequest);
		$compoundClubFindRequest->addSortRule('c_clubNameLong', 1, FILEMAKER_SORT_ASCEND);
		$ClubFindResult = $compoundClubFindRequest->execute();
		if (FileMaker::isError($ClubFindResult)) {
			$ClubFind_count = 0;
		} else {
			$ClubFind_count = $ClubFindResult->getFoundSetCount();
			$ClubFind_records = $ClubFindResult->getRecords();
		}
	} else {
		$message_ClubFind = "At Least One Search Criteria Must Be Entered";
	}
} else {
	$ClubFind_count = "";
	$message_ClubFind = "";
}
//---- / Club Access form submitted

?>

<body>
<div id="container">

	<div style="text-align: center">
		<img src="../include/USAR-logo.png" alt="logo"/>
		<div class="header">
			<h1 class="narrow">Welcome to the USA Rugby High Performance web portal</h1>
		</div>
	</div>

	<section id="tabbed">
		<!-- First tab input and label -->
		<input id="t-1" name="tabbed-tabs" type="radio" <?php if ($activeTab == 1) {
			echo 'checked="checked"';
		} ?> class="radiotab"/>
		<label for="t-1" class="tabs shadow entypo-user">Login</label>
		<!-- Second tab input and label -->
		<input id="t-2" name="tabbed-tabs" type="radio" <?php if ($activeTab == 2) {
			echo 'checked="checked"';
		} ?> class="radiotab"/>
		<label for="t-2" class="tabs shadow entypo-user-add">New Account</label>
		<!-- Third tab input and label -->
		<input id="t-3" name="tabbed-tabs" type="radio" <?php if ($activeTab == 3) {
			echo 'checked="checked"';
		} ?> class="radiotab"/>
		<label for="t-3" class="tabs shadow entypo-dot-3">Request New Password</label>
		<!-- Fourth tab input and label -->
		<input id="t-4" name="tabbed-tabs" type="radio" <?php if ($activeTab == 4) {
			echo 'checked="checked"';
		} ?> class="radiotab"/>
		<label for="t-4" class="tabs shadow entypo-folder">Forgot Account e-Mail</label>
		<!-- Sixth tab input and label (FIND A CLUB) -->
		<input id="t-6" name="tabbed-tabs" type="radio" <?php if ($activeTab == 6) {
			echo 'checked="checked"';
		} ?> class="radiotab"/>
		<label for="t-6" class="tabs shadow entypo-search">Find a Club</label>
		<!-- Tabs wrapper -->
		<div class="wrapper shadow">

			<!-- Tab 1 content -->
			<div class="tab-1">
				<?php if ($message == "") { ?>
					<p>
						Welcome! Once logged in, you will be able to register with USA Rugby, update your High Performance (HiPer) profile, view
						historical data that
						has been collected, and (optionally) sign up for camps with open registration.
					</p>
					<p>
						If you received an e-mail requesting you to update your profile here, but have never logged in before, simply select
						the "Request New Password" option above to get the password sent to your e-mail address.
					</p>
				<?php } ?>
				<!-- Add table to display any error messages with submitted form. -->
				<?php if (!empty($fail)): ?>
					<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
						<tr>
							<td>Could not login because:
								<p style="color: red"><i>
										<?php echo $fail; ?>
									</i></p>
							</td>
						</tr>
					</table>
				<?php endif; ?>

				<!-- Show messages.                    -->
				<?php
				if (isset($message)) {
					echo '<br />'
						. '<h3>' . $message . '</h3>';
				}
				?>
				<!-- ################################# -->

				<div class="mt-15"></div>

				<div class="login">
					<form action="?" method="post">
						<fieldset class="group">
							<legend>Login</legend>
							<fieldset class="field">
								<legend>e-Mail Address</legend>
								<input type="email" class="text" size="40" name="eMail" id="e-Mail" title="e-mail address"/>
							</fieldset>
							<fieldset class="field">
								<legend>Password</legend>
								<input type="password" class="text" size="40" name="password" id="Password" title="Password"/>
							</fieldset>
						</fieldset>

						<div id="grecaptcha1"></div>

						<div style="position: relative;">
							<input type="submit" value="LOGIN" class="submit buy" id="Login_Button">
						</div>
						<input type="hidden" value="true" name="submitted-login">
					</form>
				</div>
			</div>
			<!-- / Tab 1 content -->

			<!-- Tab 2 content -->
			<div class="tab-2">
				<!-- Add table to display any error messages with submitted form. -->
				<?php if (!empty($failNewAccount)): ?>
					<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
						<tr>
							<td>Could not create a new account because:
								<p style="color: red"><i>
										<?php echo $failNewAccount; ?>
									</i></p>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				<!-- ################################# -->

				<!-- Show messages instead of form.    -->
				<?php
				if (isset($message2)) {
					echo '<br />'
						. '<h3>' . $message2 . '</h3>';
					$firstName = "";
					$lastName = "";
					$gender = "";
					$DOB = "";
					$eMail = "";
				} else {
					?>

					<div class="login">
						<form action="?" method="post">
							<fieldset class="group">
								<legend>Create New HiPer Account</legend>
								<fieldset class="field">
									<legend>First Name*</legend>
									<input type="text" class="text" size="20" name="firstName" title="First Name"
											 id="FirstName" <?php recallText((empty($firstName) ? "" : $firstName), "yes"); ?>/>
								</fieldset>
								<fieldset class="field">
									<legend>Last Name*</legend>
									<input type="text" class="text" size="20" name="lastName" title="Last Name"
											 id="LastName" <?php recallText((empty($lastName) ? "" : $lastName), "yes"); ?>/>
								</fieldset>
								<fieldset class="radio">
									<legend>Gender*</legend>
									<div <?php if (empty($gender)) {
										echo 'class="missing"';
									} ?> >
										<input name="gender" type="radio" value="Male" id="Male" class="radio"
												 title="Male" <?php if (!empty($gender) and $gender == "Male") {
											echo 'checked="checked"';
										} ?> />
										<label class="radio" for="Male">Male</label>
										<input name="gender" type="radio" value="Female" id="Female" class="radio"
												 title="Female" <?php if (!empty($gender) and $gender == "Female") {
											echo 'checked="checked"';
										} ?> />
										<label class="radio" for="Female">Female</label>
									</div>
								</fieldset>
								<fieldset class="field">
									<legend>Date of Birth* (mm/dd/yyyy)</legend>
									<input type="text" name="DOB" class="text" id="DOBDate" title="Your Date of Birth"
											 style="border-radius:5px; box-shadow:inset 0 5px 5px #eee; border: 1px solid #bfbab4;"
										<?php if (empty($DOB) || $DOB == date('m/d/Y')) {
											echo 'class="missing"';
										} else {
											echo 'value="' . $DOBsave . '"';
										} ?>/>
								</fieldset>
								<fieldset class="field">
									<legend>e-Mail (This will be your account name)*</legend>
									<input name="eMail" type="email" class="text" size="40" title="e-mail"
											 id="eMail" <?php recallText((empty($eMail) ? "" : $eMail), "yes"); ?> />
								</fieldset>
							</fieldset>

							<div id="grecaptcha2"></div>

							<div style="position: relative">
								<input type="submit" value="Submit" class="submit buy"/>
							</div>
							<input type="hidden" name="submitted-create" value="true"/>
						</form>
					</div>
				
				<?php } ?>

			</div>
			<!-- / Tab 2 content -->

			<!-- Tab 3 content -->
			<div class="tab-3">
				<!-- Add table to display any error messages with submitted form. -->
				<?php if (!empty($fail)): ?>
					<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
						<tr>
							<td>Could not reset the password because:
								<p style="color: red"><i>
										<?php echo $fail; ?>
									</i></p>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				<!-- ################################# -->

				<!-- Show messages instead of form.    -->
				<?php
				if (isset($message3)) {
					echo '<br />'
						. '<h3>' . $message3 . '</h3>';
				}
				?>
				<!-- ################################# -->

				<div class="login">
					<form action="login.php" method="post">
						<fieldset class="group">
							<legend>Reset Password</legend>
							<fieldset class="field">
								<legend>Account's e-Mail Address</legend>
								<input type="text" class="text" size="40" name="eMail_password" id="eMail_password"
										 title="Account's e-mail address"/>
							</fieldset>
						</fieldset>

						<div id="grecaptcha3"></div>

						<div style="position: relative">
							<input type="submit" value="Submit" class="submit buy"/>
						</div>
						<input type="hidden" name="submitted-password" value="true"/>
					</form>
				</div>
			</div>
			<!-- / Tab 3 content -->

			<!-- Tab 4 content -->
			<div class="tab-4">
				<!-- Add table to display any error messages with submitted form. -->
				<?php if (!empty($fail)): ?>
					<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
						<tr>
							<td>Could not look up the e-mail because:
								<p style="color: red"><i>
										<?php echo $fail; ?>
									</i></p>
							</td>
						</tr>
					</table>
				<?php endif; ?>

				<!-- Show messages instead of form.    -->
				<?php
				if (isset($message4)) {
					echo '<br />'
						. '<h3>' . $message4 . '</h3>';
				}
				?>
				<!-- ################################# -->

				<div class="login">
					<form action="login.php" method="post">
						<fieldset class="group">
							<legend>These Fields are Required</legend>
							<fieldset class="field">
								<legend>First Name</legend>
								<input type="text" class="text" size="20" name="firstName" id="FirstName" title="First Name"/>
							</fieldset>
							<fieldset class="field">
								<legend>Last Name</legend>
								<input type="text" class="text" size="20" name="lastName" id="LastName" title="Last Name"/>
							</fieldset>
							<fieldset class="field">
								<legend>Date of Birth</legend>
								<input type="date" name="DOB" id="DOB" class="text" title="Date of Birth"/>
							</fieldset>
						</fieldset>

						<fieldset class="group">
							<legend>One of these Fields are Required</legend>
							<fieldset class="field">
								<legend>Zip/Postal Code</legend>
								<input name="zipCode" type="text" class="text" size="10" id="zip" title="Zip or Postal Code"/>
							</fieldset>
							<fieldset class="field">
								<legend>Cell Phone (0000000000)</legend>
								<input name="Cell" type="text" class="text" size="16" id="Cell" title="Primary phone number"/>
							</fieldset>
						</fieldset>

						<div id="grecaptcha4"></div>

						<div style="position: relative">
							<input type="submit" value="Search" class="submit buy"/>
						</div>
						<input type="hidden" name="submitted-email" value="true"/>
					</form>
				</div>

			</div>
			<!-- / Tab 4 content -->

			<!-- Tab 6 content (Find a Club) -->
			<div class="tab-6">
				<form action="login.php" method="post">
					<div class="login">
						<p>
							IMPORTANT: This club search is only for club data entered into the High Performance database. For offical USA Rugby
							club membership rosters, go <a href="https://www.usarugby.org/membership-resources/public-rosters/">here</a>.
						</p>

						<fieldset class="group" id="anchor-account">
							<legend>&nbsp;Specify At Least One Search Criteria&nbsp;</legend>

							<div class="input" style="border-top: none;">
								<label for="Search_by_City">City</label>
								<input name="Search_by_City" type="text" id="Search_by_City"
										 size="24" <?php recallText((empty($Search_by_City) ? "" : $Search_by_City), "no"); ?> />
							</div>

							<div class="input">
								<label for="Search_by_State">State</label>
								<select name="Search_by_State" size="1" id="Search_by_State" title="State or Canadian Province"
										  style="margin-right: 8em">
									<option value=""></option>
									<?php
									foreach ($stateValues as $value) {
										echo "<option value='" . $value . "' " . ($Search_by_State == $value ? "selected='selected'>" : ">") . $value . "</option>";
									}
									?>
								</select>
							</div>

							<div class="input">
								<label for="Search_by_Name">Club Name</label>
								<input name="Search_by_Name" type="text" id="Search_by_Name"
										 size="24" <?php recallText((empty($Search_by_Name) ? "" : $Search_by_Name), "no"); ?> />
							</div>

						</fieldset>

						<input type="submit" name="SEARCH" value="SEARCH" class="submit buy" id="Search_Button"/>

					</div>
					<?php
					if ($ClubFind_count > 0) {
						echo "
		<fieldset class='group'>
		<legend>&nbsp;Clubs Found: " . $ClubFind_count . "&nbsp;</legend>
		<div class='aacell aacellheader' style='width: 38%; padding-left: 14px'>Club</div>
		<div class='aacell aacellheader' style='width: 30%'>Club Contact</div>
		<div class='aacell aacellheader' style='width: 30%'>&nbsp;</div>
		<form name='form-ClubAccess' id='form-ClubAccess' action='body.php' method='post'>
		";
						
						foreach ($ClubFind_records as $ClubFind_record) {
							
							$Club_name = str_replace(chr(10), "<br />", $ClubFind_record->getField('c_clubNameLong'));
							$Club_Contact = str_replace(chr(10), "<br />", $ClubFind_record->getField('c_ClubContact'));
							$Club_HeadCoach = $ClubFind_record->getField('c_HeadCoachName') == "" ? "-" : $ClubFind_record->getField('c_HeadCoachName');
							$Club_Website = $ClubFind_record->getField('Website') == "" ? "-" : $ClubFind_record->getField('Website');
							$Club_Facebook = $ClubFind_record->getField('FacebookURL') == "" ? "-" : $ClubFind_record->getField('FacebookURL');
							$Club_Twitter = $ClubFind_record->getField('TwitterHandle') == "" ? "-" : $ClubFind_record->getField('TwitterHandle');
							
							echo "
			<div class='row-divider row-divider-color'>
			
					<div class='aacell' style='width: 38%'>
						<p>" . $Club_name . "</p>
					</div>
					<div class='aacell' style='width: 30%'>
						<p>" . $Club_Contact . "</p>
					</div>
					<div class='aacell' style='width: 30%'>
						<p>Head Coach: " . $Club_HeadCoach . "</p>
						<p><a href='" . $Club_Website . "'>Home Page</a></p><p><a href='" . $Club_Facebook . "'>Facebook Page</a></p>
					</div>
				</div>
			
			";
						}
						
						echo "
		</fieldset>";
					} else {
						echo '<h3>' . $message_ClubFind . '</h3>';
					}
					?>

					<input type="hidden" name="submitted-ClubSearch" value="true"/>
				</form>
			</div>
			<!-- / Tab 6 content (My Clubs)-->

		</div>
		<!-- / Tabs wrapper -->
	</section>

</div>

<!--Manual Recaptcha rendering of multiple elements (defined in header)-->
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
		  async defer>
</script>

</body>
</html>