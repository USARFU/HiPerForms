<?php

if ($related_measurements_count > 0) {
	echo "
		<div class='input' style='border-top: none;'>
		<label class='top'>Existing Measurement Records</label>
		<div style='max-height: 11em; overflow-y: auto;'> <!-- scrollbar -->
		";
	
	foreach ($related_measurements as $related_measurement) {
		echo "<div class='row row-divider row-divider-color' style='margin-left: 3em;'>
			<fieldset class='field' style='width: 14%'>
			<legend>Date Measured</legend>
				" . (empty($related_measurement->getField('Personnel__Measurements::dateMeasured')) ? '-' : $related_measurement->getField('Personnel__Measurements::dateMeasured')) . "
			</fieldset>
			<fieldset class='field' style='width: 14%'>
			<legend>Height&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</legend>
				" . (empty($related_measurement->getField('Personnel__Measurements::c_height')) ? '-' : $related_measurement->getField('Personnel__Measurements::c_height') . '/' . $related_measurement->getField('Personnel__Measurements::heightMeters') . 'm') . "
			</fieldset>
			<fieldset class='field' style='width: 14%'>
			<legend>Weight&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</legend>
				" . (empty($related_measurement->getField('Personnel__Measurements::Weight_lb')) ? '-' : $related_measurement->getField('Personnel__Measurements::Weight_lb') . 'lb/' . $related_measurement->getField('Personnel__Measurements::Weight_kg') . 'kg') . "
			</fieldset>
			<fieldset class='field' style='width: 14%'>
			<legend>Wingspan</legend>
				" . (empty($related_measurement->getField('Personnel__Measurements::Wingspan_in')) ? '-' : $related_measurement->getField('Personnel__Measurements::Wingspan_in') . 'in/' . $related_measurement->getField('Personnel__Measurements::Wingspan_m') . 'm') . "
			</fieldset>
			<fieldset class='field' style='width: 14%'>
			<legend>Handspan</legend>
				" . (empty($related_measurement->getField('Personnel__Measurements::Handspan_in')) ? '-' : $related_measurement->getField('Personnel__Measurements::Handspan_in') . 'in/' . $related_measurement->getField('Personnel__Measurements::Handspan_cm') . 'cm') . "
			</fieldset>
			<fieldset class='field' style='margin-right: .5em; width: 14%'>
			<legend>Standing Reach</legend>
				" . (empty($related_measurement->getField('Personnel__Measurements::StandingReach_in')) ? '-' : $related_measurement->getField('Personnel__Measurements::StandingReach_in') . 'in/' . $related_measurement->getField('Personnel__Measurements::StandingReach_m') . 'm') . "
			</fieldset>
			</div>";
	}
	
	echo "
		</div></div>
		";
}

if ($related_attributes_count > 0) {
	echo "
		<div class='input' style='border-top: none;'>
		<label class='top'>Attribute History</label>
		<div style='max-height: 11em; overflow-y: auto;'> <!-- scrollbar -->
		";
	
	foreach ($related_attributes as $related_attribute) {
		$attribute_date = $related_attribute->getField('Personnel__Attributes::dateEvaluated');
		$attribute_type = $related_attribute->getField('Personnel__Attributes::Attribute');
		$attribute_note = $related_attribute->getField('Personnel__Attributes::Notes');
		
		if (!empty($attribute_date) && !empty($attribute_type)) {
			echo "<div class='row-divider row-divider-color' style='margin-left: 3em;'>
				<div class='row'>
				<fieldset class='field'>
				<legend>Date Evaluated</legend>
					" . $attribute_date . "
				</fieldset>
				<fieldset class='field' style='width: 12em;'>
				<legend>Attribute</legend>
					" . $attribute_type . "
				</fieldset>
				<fieldset class='field'>
				<legend>Score</legend>
					" . (empty($related_attribute->getField('Personnel__Attributes::Level')) ? '-' : $related_attribute->getField('Personnel__Attributes::Level')) . "
				</fieldset>
				<fieldset class='field' style='width: 10em;'>
				<legend>Evaluator</legend>
					" . (empty($related_attribute->getField('Personnel__Attributes::c_EvaluatorName')) ? '-' : $related_attribute->getField('Personnel__Attributes::c_EvaluatorName')) . "
				</fieldset>
				<fieldset class='field' style='margin-right: .5em;'>
				<legend>Event / Camp</legend>
					" . (empty($related_attribute->getField('Personnel__Attributes::c_EventName')) ? '-' : $related_attribute->getField('Personnel__Attributes::c_EventName')) . "
				</fieldset>
				</div>";
			if (!empty($attribute_note)) echo "
				<div class='row'>
				<fieldset class='field' style='margin-right: .5em;'>
				<legend>Notes</legend>
					" . $attribute_note . "
				</fieldset>
				</div>";
			echo "</div>";
		}
	}
	echo "</div> <!-- /scrollbar -->
		</div>";
}

if ($related_performances_count > 0) {
	echo "
		<div class='input' style='border-top: none;'>
		<label class='top'>Athletic Test History</label>
		<div style='max-height: 11em; overflow-y: auto;'> <!-- scrollbar -->
		";
	
	foreach ($related_performances as $related_performance) {
		$performance_date = $related_performance->getField('Personnel__Performance::Date');
		$performance_type = $related_performance->getField('Personnel__Performance::testType');
		$performance_note = $related_performance->getField('Personnel__Performance::Notes');
		
		if (!empty($performance_date) && !empty($performance_type)) {
			switch ($performance_type) {
				case "Vertical Jump":
					$performance_legend = "Inches";
					break;
				case "Medicine Ball Throw":
					$performance_legend = "Meters Thrown";
					break;
				case "10 Meter Dash":
				case "40 Meter Dash":
				case "40 Yard Dash":
				case "40 Meter with Ball":
				case "Illinois Agility":
				case "T Test":
				case "T Test with Ball":
				case "Planks":
					$performance_legend = "Seconds";
					break;
				case "Bench Press 225 Count":
				case "Chin-Ups":
				case "Push-Up Count (90 sec.)":
				case "Yo Yo":
					$performance_legend = "Count";
					break;
				case "Bench Press Max Weight":
					$performance_legend = "Weight (lbs.)";
					break;
			}
			switch ($performance_type) {
				case "Vertical Jump":
					$performance_score = $related_performance->getField('Personnel__Performance::VerticalJumpInches');
					break;
				case "Medicine Ball Throw":
				case "10 Meter Dash":
				case "40 Meter Dash":
				case "40 Yard Dash":
				case "40 Meter with Ball":
				case "Illinois Agility":
				case "T Test":
				case "T Test with Ball":
					$performance_score = $related_performance->getField('Personnel__Performance::avg_Timer1');
					break;
				case "Bench Press 225 Count":
				case "Bench Press Max Weight":
				case "Chin-Ups":
				case "Planks":
				case "Push-Up Count (90 sec.)":
					$performance_score = $related_performance->getField('Personnel__Performance::fullCount');
					break;
				case "Yo Yo":
					$performance_score = $related_performance->getField('Personnel__Performance::oneTimer1');
					break;
			}
		}
		
		if (!empty($performance_score)) {
			
			echo "<div class='row-divider row-divider-color' style='margin-left: 3em;'>
				<div class='row'>
				<fieldset class='field'>
				<legend>Date Evaluated</legend>
					" . $performance_date . "
				</fieldset>
				<fieldset class='field' style='width: 12em;'>
				<legend>Test Type</legend>
					" . $performance_type . "
				</fieldset>
				<fieldset class='field' style='width: 8em;'>
				<legend>" . $performance_legend . "</legend>
					" . $performance_score . "
				</fieldset>
				<fieldset class='field' style='width: 10em;'>
				<legend>Evaluator</legend>
					" . (empty($related_performance->getField('Personnel__Performance::c_EvaluatorName')) ? '-' : $related_performance->getField('Personnel__Performance::c_EvaluatorName')) . "
				</fieldset>
				<fieldset class='field' style='margin-right: .5em;'>
				<legend>Event / Camp</legend>
					" . (empty($related_performance->getField('Personnel__Performance::c_EventName')) ? '-' : $related_performance->getField('Personnel__Performance::c_EventName')) . "
				</fieldset>
				</div>";
			if (!empty($performance_note)) echo "
				<div class='row'>
				<fieldset class='field' style='margin-right: .5em;'>
				<legend>Notes</legend>
					" . $performance_note . "
				</fieldset>
				</div>";
			echo "</div>";
		}
	}
	
	echo "</div> <!-- /scrollbar -->
		</div>";
}

if ($related_camps_count > 0) {
	echo "
		<div class='input' style='border-top: none;'>
		<label class='top'>Camp History</label>
		<div style='max-height: 12em; overflow-y: auto;'> <!-- scrollbar -->
		";
	
	foreach ($related_camps as $related_camp) {
		$camp_name = $related_camp->getField('Personnel__CampPersonnel::c_EventName');
		$camp_venue = $related_camp->getField('Personnel__CampPersonnel::c_Venue');
		$camp_startDate = $related_camp->getField('Personnel__CampPersonnel::c_CampStartDate');
		$camp_endDate = $related_camp->getField('Personnel__CampPersonnel::c_CampEndDate');
		$camp_inviteStatus = $related_camp->getField('Personnel__CampPersonnel::inviteStatus');
		$camp_reasonForNotAttending = $related_camp->getField('Personnel__CampPersonnel::reasonForNotAttending');
		
		echo "<div class='row-divider row-divider-color' style='margin-left: 3em;'>
			<div class='row'>
			<fieldset class='field'>
			<legend>Camp Name</legend>
				" . $camp_name . "
			</fieldset>
			<fieldset class='field' style='margin-right: .5em;'>
			<legend>Venue</legend>
				" . (empty($camp_venue) ? '-' : $camp_venue) . "
			</fieldset>
			</div>
			
			<div class='row'>
			<fieldset class='field'>
			<legend>Invite Status</legend>
				" . (empty($camp_inviteStatus) ? '-' : $camp_inviteStatus) . "
			</fieldset>
			<fieldset class='field'>
			<legend>Start Date</legend>
				" . (empty($camp_startDate) ? '-' : $camp_startDate) . "
			</fieldset>
			<fieldset class='field'>
			<legend>End Date</legend>
				" . (empty($camp_endDate) ? '-' : $camp_endDate) . "
			</fieldset>
			<fieldset class='field' style='margin-right: .5em;'>";
		if ($camp_inviteStatus == 'Declined') {
			echo "
				<legend>Reason For Not Attending</legend>
					" . (empty($camp_reasonForNotAttending) ? '-' : $camp_reasonForNotAttending) . "
				</fieldset>";
		}
		
		echo "</div></div>";
	}
	
	echo "</div> <!-- /scrollbar -->
		</div>";
}

if ($related_ClubMembership_count > 0) {
	echo "
		<div class='input' style='border-top: none;'>
		<label class='top'>Club History</label>
		<div style='max-height: 600px; overflow-y: auto;'> <!-- scrollbar -->
		";
	
	foreach ($related_ClubMembership as $ClubMembership_record) {
			$ClubHistory_ClubName = $ClubMembership_record->getField('Personnel__ClubMembership::c_ClubName');
			$ClubHistory_ClubRole = $ClubMembership_record->getField('Personnel__ClubMembership::Role');
			$ClubHistory_StartDate = $ClubMembership_record->getField('Personnel__ClubMembership::StartDate');
			$ClubHistory_EndDate = $ClubMembership_record->getField('Personnel__ClubMembership::EndDate');
			
			echo "<div class='row-divider row-divider-color' style='margin-left: 3em;'>

				<div class='row'>
					<fieldset class='field' style='margin-right: .5em;'>
					<legend>Name</legend>
						" . $ClubHistory_ClubName . "
					</fieldset>
				</div>
				
				<div class='row'>
					<fieldset class='field' style='width: 10em'>
					<legend>Role</legend>
					" . $ClubHistory_ClubRole . "
					</fieldset>
					<fieldset class='field'>
					<legend>Start Date</legend>
					" . $ClubHistory_StartDate . "
					</fieldset>
					<fieldset class='field'>
					<legend>End Date</legend>
					" . $ClubHistory_EndDate . "
					</fieldset>
				</div>
			</div>";
		
	}
		echo "</div> <!-- /scrollbar -->
		</div>";
}
