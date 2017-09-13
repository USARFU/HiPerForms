<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="../include/hiperforms.css" media="screen"/>

	<!-- Error code 001 -->
	
	<?php
	date_default_timezone_set('America/New_York');
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include "$root/include/dbaccess-membership.php";
	include "$root/include/functions.php";
	include_once "formHandler.php";
	?>

	<script src="/include/script/jquery/jquery.min.js"></script>
	<script src="/include/script/jquery-ui/jquery-ui.min.js"></script>
	<script src="/include/script/select2/js/select2.min.js"></script>
	<script src="/include/WebForms.js"></script>
	
</head>

<body id="body-tabbed">

<div id="site-content">

	<div class="header">
		<?php
		if (empty($customLogo)) {
			echo '<img src="/include/USAR-logo.png" alt="logo" style="max-height: 100px; max-width: 100px;"/>';
		} else {
			echo '<img src="/include/ContainerBridge.php?path=' . $customLogo . '" alt="logo" />';
		}
		?>
		
		<div class="content-by-logo">
			<div class="row">
				<div class="cell w-15 cell-label">
					Your Name:
				</div>
				<div class="cell w-32">
					<?php echo $name; ?>
				</div>
				<div class="cell w-15 cell-label">
				
				</div>
				<div class="cell w-32 cell-last-child">
				
				</div>
			</div>
			<div class="row">
				<div class="cell w-15 cell-label">
					Camp/Event:
				</div>
				<div class="cell w-32">
					<?php echo $campName; ?>
				</div>
				<div class="cell w-15 cell-label">
					Start Date:
				</div>
				<div class="cell w-32 cell-last-child">
					<?php echo $dateStarted; ?>
				</div>
			</div>
			<div class="row">
				<div class="cell w-15 cell-label">
					Venue:
				</div>
				<div class="cell w-32">
					<?php echo $venueName; ?>
				</div>
				<div class="cell w-15 cell-label">
					Event Fee:
				</div>
				<div class="cell w-32 cell-last-child">
					<?php echo $fee; ?>
				</div>
			</div>
		</div>
	</div>

	<section id="tabbed">

		<input id="t-1" name="tabbed-tabs" type="radio" onclick="location.href='camp-body.php?activeTab=1'" <?php if ($activeTab == 1) {
			echo 'checked="checked"';
		} ?> class="radiotab loading"/>
		<label for="t-1" class="tabs shadow entypo-user-add">Attendance</label>

		<!-- Second tab input and label (PROFILE) -->
		<input id="t-2" name="tabbed-tabs" type="radio" onclick="location.href='camp-body.php?activeTab=2'" <?php if ($activeTab == 2) {
			echo 'checked="checked"';
		} ?> class="radiotab loading"/>
		<label for="t-2" class="tabs shadow entypo-newspaper">Profile</label>

		<!-- If > 180 days since last profile update, disable the following tabs: -->
		<?php
		if (($ProfileStatus == 'red' || $ProfileStatus == 'black')) { ?>
			<input id="t-3-disabled" name="tabbed-tabs" type="radio" class="radiotab"
					 title="Update your profile before accessing this tab."/>
			<label class="tabs shadow entypo-credit-card" style="color: gray">Payment</label>
			<input id="t-4-disabled" name="tabbed-tabs" type="radio" class="radiotab"
					 title="Update your profile before accessing this tab."/>
			<label class="tabs shadow entypo-flight" style="color: gray">Travel</label>
		<?php } else { ?>
			<!-- Third tab input and label (PAYMENT) -->
			<input id="t-3" name="tabbed-tabs" type="radio" onclick="location.href='camp-body.php?activeTab=3'" <?php if ($activeTab == 3) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-3" class="tabs shadow entypo-credit-card">Payment</label>
			<!-- Fourth tab input and label (TRAVEL) -->
			<input id="t-4" name="tabbed-tabs" type="radio" onclick="location.href='camp-body.php?activeTab=4'" <?php if ($activeTab == 4) {
				echo 'checked="checked"';
			} ?> class="radiotab loading"/>
			<label for="t-4" class="tabs shadow entypo-flight">Travel</label>
		
		<?php } ?>

		<!-- Tabs wrapper -->
		<div class="wrapper shadow">

			<!-- Tab 1 content (Invite) -->
			<div class="tab-1">
				
				<?php if ($activeTab == 1) {
					include_once '1-ConfirmAttendance.php';
				} ?>

			</div>
			<!-- / Tab 1 content (Invite)-->

			<!-- Tab 2 content (Profile) -->
			<div class="tab-2">
				
				<?php if ($activeTab == 2) {
					include_once '';
				} ?>

			</div>
			<!-- / Tab 2 content (Profile)-->

			<!-- Tab 3 content (Payment) -->
			<div class="tab-3">

				<div class="mt-10"></div>
				
				<?php if ($activeTab == 3) {
					include_once '';
				} ?>

			</div>
			<!-- / Tab 3 content (Payment)-->

			<!-- Tab 4 content (Travel) -->
			<div class="tab-4">
				
				<?php if ($activeTab == 4) {
					include_once '';
				} ?>

			</div>
			<!-- / Tab 4 content (Travel)-->

		</div>
		<!-- / Tabs wrapper -->
	</section>

</div>

</body>
</html>