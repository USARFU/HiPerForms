<?php
/**
 * Created by PhpStorm.
 * User: aewerdt
 * Date: 1/19/2017
 * Time: 10:31 AM
 */

// <!-- Show messages.                    -->
if (isset($message_members)) {
	echo '<br />'
		. '<h3>' . $message_members . '</h3>';
}
?>
<!-- ################################# -->

<h5>Notes</h5>
<ul>
	<li>For questions regarding the data itself, or to request changes to read-only fields, contact the Head Coach.</li>
	<li>Need access to a club or camp not listed below? Access privileges must be given through HiPer. Either contact the head coach, or <a
				href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.</li>
</ul>

<div class="mt-10"></div>

<?php
if ($related_ClubAccess_count > 0) {
	echo "
		<fieldset class='group'>
		<legend>&nbsp;Clubs&nbsp;</legend>
		<div class='aacell aacellheader' style='width: 49%; padding-left: 14px'>Club Name</div>
		<div class='aacell aacellheader' style='width: 32%'>Head Coach</div>
		<div class='aacell aacellheader' style='width: 15%'>Active Members</div>
		<form name='form-ClubAccess' id='form-ClubAccess' action='body.php' method='post'>
		";
	
	foreach ($related_ClubAccess as $ClubAccess_record) {
		
		$ClubAccess_ID = $ClubAccess_record->getField('Personnel__Club.WebAccess::ID');
		$ClubAccess_name = str_replace(chr(10), "<br />", $ClubAccess_record->getField('Personnel__Club.WebAccess::c_clubNameLong'));
		$ClubAccess_HeadCoach = $ClubAccess_record->getField('Personnel__Club.WebAccess::c_HeadCoachName') == "" ? "-" : $ClubAccess_record->getField('Personnel__Club.WebAccess::c_HeadCoachName');
		$ClubAccess_ActiveMembers = $ClubAccess_record->getField('Personnel__Club.WebAccess__ClubMembership.active::sum_count') == "" ? "0" : $ClubAccess_record->getField('Personnel__Club.WebAccess__ClubMembership.active::sum_count');
		
		echo "
			<div class='row-divider row-divider-color hover' id='" . $ClubAccess_ID . "' onClick='OpenClub(this.id);'>
			
					<div class='aacell' style='width: 49%'>
						" . $ClubAccess_name . "
					</div>
					<div class='aacell' style='width: 32%'>
						" . $ClubAccess_HeadCoach . "
					</div>
					<div class='aacell' style='width: 15%'>
						" . $ClubAccess_ActiveMembers . "
					</div>
				</div>
			
			";
	}
	
	echo "
		<input type='hidden' name='submitted-ClubAccess' value='true'/>
		<input id='ClubAccess_ID' name='ClubAccess_ID' type='hidden' value=''/>
		</form></fieldset>";
}
?>

<?php
if ($related_CampAccess_count > 0) {
	echo "
		<fieldset class='group'>
		<legend>&nbsp;Camps&nbsp;</legend>
		<div class='aacell aacellheader' style='width: 49%; padding-left: 14px'>Camp Name & Venue</div>
		<div class='aacell aacellheader' style='width: 32%'>Head Coach</div>
		<div class='aacell aacellheader' style='width: 15%'>Dates</div>
		<form name='form-CampAccess' id='form-CampAccess' action='body.php' method='post'>
		";
	
	foreach ($related_CampAccess as $CampAccess_record) {
		
		$CampAccess_ID = $CampAccess_record->getField('Camp.WebAccess::ID');
		$CampAccess_Name = $CampAccess_record->getField('Camp.WebAccess::Name');
		$CampAccess_Gender = $CampAccess_record->getField('Camp.WebAccess::Gender');
		$CampAccess_Level = $CampAccess_record->getField('Camp.WebAccess::PlayerLevel');
		$CampAccess_Venue = $CampAccess_record->getField('Camp.WebAccess::c_Venue');
		$CalculatedCampName = $CampAccess_Name . (!empty($CampAccess_Gender) ? ", " . $CampAccess_Gender : "") . (!empty($CampAccess_Level) ? " (" . $CampAccess_Level . ")" : "") . (!empty($CampAccess_Venue) ? "<br />" . $CampAccess_Venue : "");
		$CampAccess_HeadCoach = $CampAccess_record->getField('Camp.WebAccess::c_HeadCoachName') == "" ? "-" : $CampAccess_record->getField('Camp.WebAccess::c_HeadCoachName');
		$CampAccess_Dates = $CampAccess_record->getField('Camp.WebAccess::StartDate') . " - " . $CampAccess_record->getField('Camp.WebAccess::EndDate');
		$CampEditors = $CampAccess_record->getField('Camp.WebAccess::c_WebAccessIDList');
		// Verify that $ID_Personnel is authorized to view the Camp
		$CampEditor = strpos($CampEditors, $ID_Personnel);
		
		if ($CampEditor !== false) {
			echo "
			<div class='row-divider row-divider-color hover' id='" . $CampAccess_ID . "' onClick='OpenCamp(this.id);'>
				<div class='aacell' style='width: 49%'>
					" . $CalculatedCampName . "
				</div>
				<div class='aacell' style='width: 32%'>
					" . $CampAccess_HeadCoach . "
				</div>
				<div class='aacell' style='width: 15%'>
					" . $CampAccess_Dates . "
				</div>
			</div>
			";
		}
	}
	
	echo "
		<input type='hidden' name='submitted-CampAccess' value='true'/>
		<input id='CampAccess_ID' name='CampAccess_ID' type='hidden' value=''/>
		</form>
		</fieldset>";
} else { echo 'Count: ' . $related_CampAccess_count; }
?>

<div id="Submit_Dialog_Club" title="Opening Club">
	<p>Please wait while the selected club is loaded. This can take up to a minute.</p>
</div>

<script>
	function OpenClub(clicked_id){
       document.getElementById('ClubAccess_ID').value = clicked_id;
       $("#Submit_Dialog_Club").dialog("open");
		 $('#form-ClubAccess').submit();
	}
	function OpenCamp(clicked_id){
       document.getElementById('CampAccess_ID').value = clicked_id;
       $("#Processing").dialog("open");
		 $('#form-CampAccess').submit();
	}
</script>