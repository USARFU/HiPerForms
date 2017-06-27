<form action="login.php" method="post">

	<fieldset class="group" id="anchor-account">
		<legend>&nbsp;Specify At Least One Search Criteria&nbsp;</legend>

		<div class="input" style="border-top: none;">
			<label for="Search_by_City">City</label>
			<input name="Search_by_City" type="text" id="Search_by_City" size="24" <?php recallText((empty($Search_by_City) ? "" : $Search_by_City), "no"); ?> />
		</div>
		
		<div class="input">
			<label for="Search_by_State">State</label>
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
			<label for="Search_by_Name">Club Name</label>
			<input name="Search_by_Name" type="text" id="Search_by_Name" size="24" <?php recallText((empty($Search_by_Name) ? "" : $Search_by_Name), "no"); ?> />
		</div>

	</fieldset>

	<input type="submit" name="SEARCH" value="SEARCH" class="submit buy" id="Search_Button"/>
	
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
						<p><a href='" . $Club_Website . "'><div class='entypo-globe'></div></a></p>
						<p><a href='" . $Club_Website . "'>" . $Club_Website . "</a></p><p><a href='" . $Club_Facebook . "'>" . $Club_Facebook . "</a></p>
					</div>
					<div class='row'>
						<p>" . "Club Status: " . "</p>
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