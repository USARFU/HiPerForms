<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>USA Rugby HiPer Database - Confirm Account</title>
</head>

<?php
include_once 'header.php';
$passwordSubmitted = false;

if (isset($_GET['ID'])) {
	$ID = fix_string($_GET['ID']);
} else if (isset($_POST['ID'])) {
	$ID = fix_string($_POST['ID']);
	$passwordSubmitted = $_POST['passwordSubmitted'];
} else {
	$message = "No ID provided.";
}

## Try to find a match. Create account and e-mail password if match found #################
$passwordRequest = false;
if (empty($message)) {
	$request = $fm->newFindCommand('NewAccount');
	$request->addFindCriterion('ID', '==' . $ID);
	$result = $request->execute();
	$record = $result->getFirstRecord();
	$email = $record->getField('eMail');
	
	if (FileMaker::isError($result)) {
		$message = "The ID provided is no longer valid.<br />";
	} else if ($passwordSubmitted == "true") {
		$password1 = strtoupper(md5($_POST['password1']));
		$password2 = strtoupper(md5($_POST['password2']));
		if ($password1 != $password2) {
			$fail = "Passwords do not match.<br />";
			$passwordRequest = true;
		} else if (strlen($_POST['password1']) < 8) {
			$fail = "Password is not long enough.<br />";
			$passwordRequest = true;
		} else {
			$param = $ID . "|" . $password1;
			$newPerformScript = $fm->newPerformScriptCommand('NewAccount', 'ActivateAccount2', $param);
			$scriptResult = $newPerformScript->execute();
			if (FileMaker::isError($scriptResult)) {
				echo "<p>An error occured while activating your account</p>"
					. "<p>Error Code 501: " . $scriptResult->getMessage() . "</p>";
				echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information for review.</p>";
				die();
			} else {
				//Log into system
				$request = $fm->newFindCommand('Member-Header');
				$request->addFindCriterion('eMail', '==' . $email);
				$request->addFindCriterion('z_Exclusive_TicketBuyerDB', '=');
				$result = $request->execute();
				if (FileMaker::isError($result)) {
					header("location: login.php");
					die();
				} else {
					$record = $result->getFirstRecord();
				}
				session_start();
				$recordID = $record->getRecordId();
				$_SESSION['RecordID'] = $recordID;
				$_SESSION['timeout'] = time();
				header("location: body.php");
			}
		}
	} else {
		$passwordRequest = true;
	}
}
################################################################################
?>

<body>
<div id="container">

	<!-- Show message. ################### -->
	<?php
	if (!empty($message)) {
		echo '<br />'
			. '<h3>' . $message . '</h3></div></body></html>';
		die();
	}
	?>
	<!-- ################################# -->
	
	<?php
	if ($passwordRequest) {
		?>
		<div style="text-align: center">
			<img src="../include/USAR-logo.png" alt="logo"/>
			<div class="header">
				<h1 class="narrow">Create your HiPer account - final step for <?php echo $email; ?></h1>
			</div>
		</div>
	<section id="tabbed">
	<div class="wrapper shadow">
		<!-- Add table to display any error messages with submitted form. -->
		<?php if (!empty($fail)): ?>
			<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
				<tr>
					<td>Could not create account because:
						<p style="color: red"><i>
								<?php echo $fail; ?>
							</i></p>
					</td>
				</tr>
			</table>
		<?php endif; ?>
		
			<form action="ConfirmAccount.php" method="post">
				<div class="mt-15"></div>

				<div class="login">
					<form action="?" method="post">
						<fieldset class="group">
							<legend> Set Your Password</legend>
							<fieldset class="field">
								<legend>Password (8 characters minimum)</legend>
								<input type="password" class="text" size="40" name="password1" id="Password" title="Password"/>
							</fieldset>
							<fieldset class="field">
								<legend>Re-enter Your Password</legend>
								<input type="password" class="text" size="40" name="password2" id="Password" title="Password"/>
							</fieldset>
						</fieldset>

						<div style="position: relative;">
							<input type="submit" value="SUBMIT" class="submit buy" id="Login_Button">
						</div>
				</div>

				<input type="hidden" name="passwordSubmitted" value="true"/>
				<input type="hidden" name="ID" value="<?php echo $ID; ?>"/>
			</form>

	</div>
	</section>
		<?php
	}
	?>

</body>
</html>