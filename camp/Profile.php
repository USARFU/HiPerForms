<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Rugby Camp Confirmation</title>

	<script src="../include/script/jquery/jquery.min.js"></script>

	<!-- select2 js library for searchable drop down controls -->
	<link href="../include/script/select2/css/select2.min.css" rel="stylesheet"/>
	<script src="../include/script/select2/js/select2.min.js"></script>

	<!-- jquery-ui for date picker -->
	<link href="../include/script/jquery-ui/jquery-ui.min.css" rel="stylesheet"/>
	<script src="../include/script/jquery-ui/jquery-ui.min.js"></script>

	<!-- jSignature js library -->
	<script src="../include/script/jsignature/jSignature.min.js"></script>
	<script src="../include/script/jsignature/jSignature.CompressorBase30.js"></script>
	<script src="../include/script/jsignature/jSignature.UndoButton.js"></script>

	<!-- slim image cropper -->
	<link href="../include/script/slim/slim.css" rel="stylesheet"/>

	<!-- Error Codes 221-227 -->

	<!--Submit data if criteria is met-->
	<?php
	include_once 'header.php';
	include_once '../slim.php'; //Image cropper
	
	$fail = "";
	
	//## Determine Current Season ##//
	$thisYear = date('Y');
	$thisMonth = date('m');
	$thisDay = date('d');
	if ($thisMonth < 6 || ($thisMonth == 6 && $thisDay < 15)) {
		$season = ($thisYear - 1) . '/' . $thisYear;
	} else {
		$season = $thisYear . '/' . ($thisYear + 1);
	}
	//---------------//
	
	// Get form options and data //
	$includeProfile = ($campRecord->getField('includeProfileForm') != 1 ? 0 : 1);
	$includeCCPayment = ($campRecord->getField('includeCCPaymentForm') != 1 ? 0 : 1);
	$pageHeader = (empty($campRecord->getField('WebFormProfileTitle')) ? "USA Rugby Personnel Profile Update" : $campRecord->getField('WebFormProfileTitle'));
	$inviteCutOff = $campRecord->getField('inviteCutOff');
	$playerLevel = $campRecord->getField('PlayerLevel');
	$SignatureOption = $campRecord->getField('SignatureFieldOption');
	$includeFacePhoto = $campRecord->getField('wf_profile_FacePhoto');
	$includeDominantHandFoot = $campRecord->getField('wf_profile_DominantHandFoot');
	$includeHeightWeight = $campRecord->getField('wf_profile_HeightWeight');
	$includeKit = $campRecord->getField('wf_profile_Kit');
	$includeGradeLevel = $campRecord->getField('wf_profile_GradeLevel');
	$includeCellNumber = $campRecord->getField('wf_profile_CellNumber');
	$includeInsurance = $campRecord->getField('wf_profile_Insurance');
	$includeConditions = $campRecord->getField('wf_profile_Conditions');
	$includeMedications = $campRecord->getField('wf_profile_Medications');
	$includeMembershipID = $campRecord->getField('wf_profile_MembershipID');
	$includeNationalEligible = $campRecord->getField('wf_profile_NationalEligible');
	$includePositionFields = $campRecord->getField('wf_profile_Positions');
	$includeStartedPlayingFields = $campRecord->getField('wf_profile_StartedPlaying');
	$includeOtherSportsFields = $campRecord->getField('wf_profile_OtherSports');
	$includePassportFields = $campRecord->getField('wf_profile_Passport');
	$includeAirTravel = $campRecord->getField('wf_profile_AirTravel');
	$includePartner = $campRecord->getField('wf_profile_Partner');
	$includeParent = $campRecord->getField('wf_profile_Parent');
	$includeEducationFields = $campRecord->getField('wf_profile_Education');
	$includeReferenceFields = $campRecord->getField('wf_profile_References');
	$SubmitTitle = ($includeCCPayment == 1 ? "Next" : "Submit");
	
	// Don't load certain data checks if the Form Development Camp ID is used (HiPer preview)//
	if (empty($IDType)) {
		## Check that link isn't expired #########################################
		$inviteCutOffCompare_a = new DateTime($inviteCutOff);
		$inviteCutOffCompare = $inviteCutOffCompare_a->format('Y-m-d');
		$today = date('Y-m-d');
		if ($inviteCutOffCompare < $today || $inviteCutOff == "") {
			$message = "This link has expired. You are past this event's cut off date.";
		} else {
			## Check that Invite Status is not Declined ###########################
			$inviteStatusDB = $record->getField('inviteStatus');
			if ($inviteStatusDB == "Declined") {
				$message = "Your link is no longer active. Please contact the organizer of the event to activate your link.";
			}
		}
		
		$U18AtStartOfEvent = ($record->getField('c_U18AtStartOfEvent') != 1 ? false : true);
		$inviteStatus = $record->getField('inviteStatus');
		$CampRole = $record->getField('CampRole');
		$TakingBannedSubstance = $record->getField('TakingBannedSubstance');
		$BannedSubstanceViaPrescription = $record->getField('BannedSubstanceViaPrescription');
		$BannedSubstanceDescription = $record->getField('BannedSubstanceDescription');
		$UpdateSchool = (isset($_GET['UpdateSchool']) && $_GET['UpdateSchool'] == 1 ? true : false);
		
		## Grab submitted form data ##################################################
		$firstName = (isset ($_POST ['firstName']) ? fix_string($_POST ['firstName']) : "");
		$middleName = (isset ($_POST ['middleName']) ? fix_string($_POST ['middleName']) : "");
		$lastName = (isset ($_POST ['lastName']) ? fix_string($_POST ['lastName']) : "");
		$nickName = (isset ($_POST ['nickName']) ? fix_string($_POST ['nickName']) : "");
		$nickName = $nickName == $firstName ? "" : $nickName;
		$DOB = "";
		$DOBsave = "";
		if (isset($_POST['DOB'])) {
			if (validate_date($_POST['DOB']) || validate_date_filemaker($_POST['DOB'])) {
				$DOBold = new DateTime($_POST['DOB']);
				$DOB = $DOBold->format('m/d/Y');
				$DOBsave = $DOBold->format('Y-m-d');
			} else {
				$DOBsave = $_POST['DOB'];
			}
		}
		$gender = (isset ($_POST['gender']) ? fix_string($_POST['gender']) : "");
		$dominantHand = (isset ($_POST['dominantHand']) ? fix_string($_POST['dominantHand']) : "");
		$dominantFoot = (isset ($_POST['dominantFoot']) ? fix_string($_POST['dominantFoot']) : "");
		$heightFeet = "";
		if (isset ($_POST['heightFeet'])) {
			$new = $_POST['heightFeet'];
			$heightFeetOriginal = $_POST['heightFeetOriginal'];
			if ($new == $heightFeetOriginal) {
				$heightFeet = fix_string($_POST['heightFeet']);
			} else {
				$heightFeet_new = fix_string($_POST['heightFeet']);
				$heightFeet = $heightFeet_new;
			}
		}
		$heightInches = "";
		if (isset ($_POST['heightInches'])) {
			$new = $_POST['heightInches'];
			$heightInchesOriginal = $_POST['heightInchesOriginal'];
			if (($_POST['heightInches']) == "0") {
				$heightInches_new = "0";
				$heightInches = $heightInches_new;
			} else if ($new == $heightInchesOriginal) {
				$heightInches = fix_string($_POST['heightInches']);
			} else {
				$heightInches_new = fix_string($_POST['heightInches']);
				$heightInches = $heightInches_new;
			}
		}
		$heightMeters = "";
		if (isset ($_POST['heightMeters'])) {
			$new = $_POST['heightMeters'];
			$heightMetersOriginal = $_POST['heightMetersOriginal'];
			if (($_POST['heightMeters']) == "0") {
				$heightMeters_new = "0";
				$heightMeters = $heightMeters_new;
			} else if ($new == $heightMetersOriginal) {
				$heightMeters = fix_string($_POST['heightMeters']);
			} else {
				$heightMeters_new = fix_string($_POST['heightMeters']);
				$heightMeters = $heightMeters_new;
			}
		}
		$weight = "";
		if (isset ($_POST['weight'])) {
			$new = $_POST['weight'];
			$weightOriginal = $_POST['weightOriginal'];
			if ($new == $weightOriginal) {
				$weight = fix_string($_POST['weight']);
			} else {
				$weight_new = fix_string($_POST['weight']);
				$weight = $weight_new;
			}
		}
		$weight_UM = isset($_POST['weight_UM']) ? fix_string($_POST['weight_UM']) : "lb";
		$height_UM = isset($_POST['height_UM']) ? fix_string($_POST['height_UM']) : "ft";
		$MatchJerseySize = (isset ($_POST['MatchJerseySize']) ? fix_string($_POST['MatchJerseySize']) : "");
		$MatchShortsSize = (isset ($_POST['MatchShortsSize']) ? fix_string($_POST['MatchShortsSize']) : "");
		$tShirtSize = (isset ($_POST['tShirtSize']) ? fix_string($_POST['tShirtSize']) : "");
		$poloSize = (isset ($_POST['poloSize']) ? fix_string($_POST['poloSize']) : "");
		$shortsSize = (isset ($_POST['shortsSize']) ? fix_string($_POST['shortsSize']) : "");
		$trackSuitBottomSize = (isset ($_POST['trackSuitBottomSize']) ? fix_string($_POST['trackSuitBottomSize']) : "");
		$trackSuitTopSize = (isset ($_POST['trackSuitTopSize']) ? fix_string($_POST['trackSuitTopSize']) : "");
		$homeAddress1 = (isset ($_POST['homeAddress1']) ? fix_string($_POST['homeAddress1']) : "");
		$homeAddress2 = (isset ($_POST['homeAddress2']) ? fix_string($_POST['homeAddress2']) : "");
		$City = (isset ($_POST['City']) ? fix_string($_POST['City']) : "");
		$State = (isset ($_POST['State']) ? fix_string($_POST['State']) : "");
		$zipCode = (isset ($_POST['zipCode']) ? fix_string($_POST['zipCode']) : "");
		$Country = (isset ($_POST['Country']) ? fix_string($_POST['Country']) : "");
		$eMail = (isset ($_POST['eMail']) ? fix_string($_POST['eMail']) : "");
		$PrimaryPhoneNumber = (isset ($_POST['PrimaryPhoneNumber']) ? fix_string($_POST['PrimaryPhoneNumber']) : "");
		$PrimaryPhoneText_flag = isset ($_POST['PrimaryPhoneText_flag']) ? 1 : "";
		$healthInsuranceCompany = (isset ($_POST['healthInsuranceCompany']) ? fix_string($_POST['healthInsuranceCompany']) : "");
		$healthPlanID = (isset ($_POST['healthPlanID']) ? fix_string($_POST['healthPlanID']) : "");
		
		$images_insurance = Slim::getImages('slim_insurance');
		$image_insurance = $images_insurance[0];
		$name_insurance = $image_insurance['output']['name'];
		$data_insurance = $image_insurance['output']['data'];
		
		// store the insurance file
		if (!empty($name_insurance)) {
			$file_insurance = Slim::saveFile($data_insurance, $name_insurance, '../tmp/');
			if (!empty($file_insurance['name'])) {
				$InsuranceCardCropPath = "https://hiperforms.com/tmp/" . $file_insurance['name'];
			}
		}
		
		$allergiesConditions = (isset ($_POST['allergiesConditions']) ? fix_string($_POST['allergiesConditions']) : "");
		$allergiesConditionsDescr = (isset ($_POST['allergiesConditionsDescr']) ? fix_string($_POST['allergiesConditionsDescr']) : "");
		$medications = (isset ($_POST['medications']) ? fix_string($_POST['medications']) : "");
		$medicationsDescr = (isset ($_POST['medicationsDescr']) ? fix_string($_POST['medicationsDescr']) : "");
		$TakingBannedSubstance = isset ($_POST['TakingBannedSubstance']) ? fix_string($_POST['TakingBannedSubstance']) : $TakingBannedSubstance;
		$BannedSubstanceViaPrescription = isset ($_POST['BannedSubstanceViaPrescription']) ? "1" : "";
		$BannedSubstanceDescription = isset ($_POST['BannedSubstanceDescription']) ? fix_string($_POST['BannedSubstanceDescription']) : "";
		
		$MembershipID = (isset ($_POST['MembershipID']) ? $_POST['MembershipID'] : "");
		$ID_Club = (isset ($_POST['ID_Club']) ? fix_string($_POST['ID_Club']) : "");
		$OtherClub = isset($_POST['OtherClub']) ? fix_string($_POST['OtherClub']) : "";
		$DoNotBelongToAClub_flag = ($OtherClub == 'NoClub' ? 1 : "");
		$UnlistedClub_flag = ($OtherClub == 'UnlistedClub' ? 1 : "");
		$UnlistedClub_Name = isset($_POST['UnlistedClub_Name']) ? fix_string($_POST['UnlistedClub_Name']) : "";
		$UnlistedClub_City = isset($_POST['UnlistedClub_City']) ? fix_string($_POST['UnlistedClub_City']) : "";
		$UnlistedClub_State = isset($_POST['UnlistedClub_State']) ? fix_string($_POST['UnlistedClub_State']) : "";
		$nationalLevelEligible = (isset ($_POST['nationalLevelEligible']) ? fix_string($_POST['nationalLevelEligible']) : "");
		$nationalLevelEligibleExplain = (isset ($_POST['nationalLevelEligibleExplain']) ? fix_string($_POST['nationalLevelEligibleExplain']) : "");
		$yearStartedPlaying = (isset ($_POST['yearStartedPlaying']) ? fix_string($_POST['yearStartedPlaying']) : "");
		$monthStartedPlaying = (isset ($_POST['monthStartedPlaying']) ? fix_string($_POST['monthStartedPlaying']) : "");
		$primary15sPosition = (isset ($_POST['primary15sPosition']) ? fix_string($_POST['primary15sPosition']) : "");
		$primary7sPosition = (isset ($_POST['primary7sPosition']) ? fix_string($_POST['primary7sPosition']) : "");
		$HighlightVideoLink = (isset ($_POST['HighlightVideoLink']) ? fix_string($_POST['HighlightVideoLink']) : "");
		$FullMatchLink1 = (isset ($_POST['FullMatchLink1']) ? fix_string($_POST['FullMatchLink1']) : "");
		$FullMatchLink2 = (isset ($_POST['FullMatchLink2']) ? fix_string($_POST['FullMatchLink2']) : "");
		$FullMatchLink3 = (isset ($_POST['FullMatchLink3']) ? fix_string($_POST['FullMatchLink3']) : "");
		
		$OtherSport = isset($_POST['OtherSport']) ? fix_string($_POST['OtherSport']) : "";
		$OtherSportDateStart = "";
		$OtherSportDateStartsave = "";
		if (isset($_POST['OtherSportDateStart'])) {
			if (validate_date($_POST['OtherSportDateStart']) || validate_date_filemaker($_POST['OtherSportDateStart'])) {
				$OtherSportDateStartold = new DateTime($_POST['OtherSportDateStart']);
				$OtherSportDateStart = $OtherSportDateStartold->format('m/d/Y');
				$OtherSportDateStartsave = $OtherSportDateStartold->format('Y-m-d');
			} elseif (empty($_POST['OtherSportDateStart'])) {
			} else {
				$fail .= "The Other Sport Start Date is in the wrong format. <br />";
				$OtherSportDateStartsave = $_POST['OtherSportDateStart'];
			}
		}
		$OtherSportDateEnd = "";
		$OtherSportDateEndsave = "";
		if (isset($_POST['OtherSportDateEnd'])) {
			if (validate_date($_POST['OtherSportDateEnd']) || validate_date_filemaker($_POST['OtherSportDateEnd'])) {
				$OtherSportDateEndold = new DateTime($_POST['OtherSportDateEnd']);
				$OtherSportDateEnd = $OtherSportDateEndold->format('m/d/Y');
				$OtherSportDateEndsave = $OtherSportDateEndold->format('Y-m-d');
			} elseif (empty($_POST['OtherSportDateEnd'])) {
			
			} else {
				$fail .= "The Other Sport End Date is in the wrong format. <br />";
				$OtherSportDateEndsave = $_POST['OtherSportDateEnd'];
			}
		}
		$OtherSportDescription = isset($_POST['OtherSportDescription']) ? fix_string($_POST['OtherSportDescription']) : "";
		$OtherSport_Delete = isset($_POST['OtherSport_Delete']) ? $_POST['OtherSport_Delete'] : "";
		
		$passportHolder = (isset ($_POST['passportHolder']) ? fix_string($_POST['passportHolder']) : "");
		$passportNumber = (isset ($_POST['passportNumber']) ? fix_string($_POST['passportNumber']) : "");
		$nameOnPassport = (isset ($_POST['nameOnPassport']) ? fix_string($_POST['nameOnPassport']) : "");
		$passportExpiration = "";
		$passportExpirationsave = "";
		if (isset($_POST['passportExpiration'])) {
			if (validate_date($_POST['passportExpiration']) || validate_date_filemaker($_POST['passportExpiration'])) {
				$passportExpirationold = new DateTime($_POST['passportExpiration']);
				$passportExpiration = $passportExpirationold->format('m/d/Y');
				$passportExpirationsave = $passportExpirationold->format('Y-m-d');
			} elseif (empty($_POST['passportExpiration'])) {
				
			} else {
				$fail .= "The Passport Expiration Date is in the wrong format. <br />";
				$passportExpirationsave = $_POST['passportExpiration'];
			}
		}
		$VisaDateIssued = "";
		$VisaDateIssued_save = "";
		if (isset($_POST['VisaDateIssued'])) {
			if (validate_date($_POST['VisaDateIssued']) || validate_date_filemaker($_POST['VisaDateIssued'])) {
				$VisaDateIssued_old = new DateTime($_POST['VisaDateIssued']);
				$VisaDateIssued = $VisaDateIssued_old->format('m/d/Y');
				$VisaDateIssued_save = $VisaDateIssued_old->format('Y-m-d');
			} elseif (empty($_POST['VisaDateIssued'])) {
				
			} else {
				$fail .= "The Visa Date Issued value is in the wrong format. <br />";
				$VisaDateIssued_save = $_POST['VisaDateIssued'];
			}
		}
		$passportIssuingCountry = (isset ($_POST['passportIssuingCountry']) ? fix_string($_POST['passportIssuingCountry']) : "");
		$Citizen1 = (isset ($_POST['Citizen1']) ? fix_string($_POST['Citizen1']) : "");
		$Citizen2 = (isset ($_POST['Citizen2']) ? fix_string($_POST['Citizen2']) : "");
		
		$images_passport = Slim::getImages('slim_passport');
		$image_passport = $images_passport[0];
		$name_passport = $image_passport['output']['name'];
		$data_passport = $image_passport['output']['data'];
		
		// store the passport file
		if (!empty($name_passport)) {
			$file_passport = Slim::saveFile($data_passport, $name_passport, '../tmp/');
			if (!empty($file_passport['name'])) {
				$PassportCropPath = "https://hiperforms.com/tmp/" . $file_passport['name'];
			}
		}
		
		$images_other = Slim::getImages('slim_other');
		$image_other = $images_other[0];
		$name_other = $image_other['output']['name'];
		$data_other = $image_other['output']['data'];
		
		// store the other file
		if (!empty($name_other)) {
			$file_other = Slim::saveFile($data_other, $name_other, '../tmp/');
			if (!empty($file_other['name'])) {
				$OtherTravelCropPath = "https://hiperforms.com/tmp/" . $file_other['name'];
			}
		}
		
		$ID_primaryAirport = (isset ($_POST['ID_primaryAirport']) ? fix_string($_POST['ID_primaryAirport']) : "");
		$ID_secondaryAirport = (isset ($_POST['ID_secondaryAirport']) ? fix_string($_POST['ID_secondaryAirport']) : "");
		$travelComments = (isset ($_POST['travelComments']) ? fix_string($_POST['travelComments']) : "");
		$frequentFlyerInfo = (isset ($_POST['frequentFlyerInfo']) ? fix_string($_POST['frequentFlyerInfo']) : "");
		
		$spouseName = (isset ($_POST['spouseName']) ? fix_string($_POST['spouseName']) : "");
		$spouseEmail = (isset ($_POST['spouseEmail']) ? fix_string($_POST['spouseEmail']) : "");
		$spouseCell = (isset ($_POST['spouseCell']) ? fix_string($_POST['spouseCell']) : "");
		$Guardian1Type = (isset ($_POST['Guardian1Type']) ? fix_string($_POST['Guardian1Type']) : "");
		$Guardian1FirstName = (isset ($_POST['Guardian1FirstName']) ? fix_string($_POST['Guardian1FirstName']) : "");
		$Guardian1LastName = (isset ($_POST['Guardian1LastName']) ? fix_string($_POST['Guardian1LastName']) : "");
		$Guardian1eMail = (isset ($_POST['Guardian1eMail']) ? fix_string($_POST['Guardian1eMail']) : "");
		$Guardian1Cell = (isset ($_POST['Guardian1Cell']) ? fix_string($_POST['Guardian1Cell']) : "");
		$Guardian2Type = (isset ($_POST['Guardian2Type']) ? fix_string($_POST['Guardian2Type']) : "");
		$Guardian2FirstName = (isset ($_POST['Guardian2FirstName']) ? fix_string($_POST['Guardian2FirstName']) : "");
		$Guardian2LastName = (isset ($_POST['Guardian2LastName']) ? fix_string($_POST['Guardian2LastName']) : "");
		$Guardian2eMail = (isset ($_POST['Guardian2eMail']) ? fix_string($_POST['Guardian2eMail']) : "");
		$Guardian2Cell = (isset ($_POST['Guardian2Cell']) ? fix_string($_POST['Guardian2Cell']) : "");
		$Guardian3Type = (isset ($_POST['Guardian3Type']) ? fix_string($_POST['Guardian3Type']) : "");
		$Guardian3FirstName = (isset ($_POST['Guardian3FirstName']) ? fix_string($_POST['Guardian3FirstName']) : "");
		$Guardian3LastName = (isset ($_POST['Guardian3LastName']) ? fix_string($_POST['Guardian3LastName']) : "");
		$Guardian3eMail = (isset ($_POST['Guardian3eMail']) ? fix_string($_POST['Guardian3eMail']) : "");
		$Guardian3Cell = (isset ($_POST['Guardian3Cell']) ? fix_string($_POST['Guardian3Cell']) : "");
		$Guardian4Type = (isset ($_POST['Guardian4Type']) ? fix_string($_POST['Guardian4Type']) : "");
		$Guardian4FirstName = (isset ($_POST['Guardian4FirstName']) ? fix_string($_POST['Guardian4FirstName']) : "");
		$Guardian4LastName = (isset ($_POST['Guardian4LastName']) ? fix_string($_POST['Guardian4LastName']) : "");
		$Guardian4eMail = (isset ($_POST['Guardian4eMail']) ? fix_string($_POST['Guardian4eMail']) : "");
		$Guardian4Cell = (isset ($_POST['Guardian4Cell']) ? fix_string($_POST['Guardian4Cell']) : "");
		
		$emergencyContactFirstName = (isset ($_POST['emergencyContactFirstName']) ? fix_string($_POST['emergencyContactFirstName']) : "");
		$emergencyContactLastName = (isset ($_POST['emergencyContactLastName']) ? fix_string($_POST['emergencyContactLastName']) : "");
		$emergencyContactNumber = (isset ($_POST['emergencyContactNumber']) ? fix_string($_POST['emergencyContactNumber']) : "");
		$emergencyContactRelationship = (isset ($_POST['emergencyContactRelationship']) ? fix_string($_POST['emergencyContactRelationship']) : "");
		$CurrentSchoolGradeLevel = isset($_POST['CurrentSchoolGradeLevel']) ? fix_string($_POST['CurrentSchoolGradeLevel']) : 12;
		
		if ($includeEducationFields != "Hidden") {
			$StatePlayingIn = isset ($_POST['StatePlayingIn']) ? $_POST['StatePlayingIn'] : "";
			$ID_School = isset($_POST['ID_School']) ? fix_string($_POST['ID_School']) : "";
			$HighSchoolGraduationYear = isset($_POST['HighSchoolGraduationYear']) ? fix_string($_POST['HighSchoolGraduationYear']) : "";
			$ID_School_College = isset($_POST['ID_School_College']) ? fix_string($_POST['ID_School_College']) : "";
			$graduationCollegeYear = isset($_POST['graduationCollegeYear']) ? fix_string($_POST['graduationCollegeYear']) : "";
			$currentlyMilitary = isset($_POST['currentlyMilitary']) ? fix_string($_POST['currentlyMilitary']) : "";
			$militaryBranch = isset($_POST['militaryBranch']) ? fix_string($_POST['militaryBranch']) : "";
			$militaryComponent = isset($_POST['militaryComponent']) ? fix_string($_POST['militaryComponent']) : "";
		}
		
		if ($includeReferenceFields != "Hidden") {
			$referenceFirstName1 = (isset ($_POST['referenceFirstName1']) ? fix_string($_POST['referenceFirstName1']) : "");
			$referenceLastName1 = (isset ($_POST['referenceLastName1']) ? fix_string($_POST['referenceLastName1']) : "");
			$referencePhone1 = (isset ($_POST['referencePhone1']) ? fix_string($_POST['referencePhone1']) : "");
			$referenceEmail1 = (isset ($_POST['referenceEmail1']) ? fix_string($_POST['referenceEmail1']) : "");
			$referenceFirstName2 = (isset ($_POST['referenceFirstName2']) ? fix_string($_POST['referenceFirstName2']) : "");
			$referenceLastName2 = (isset ($_POST['referenceLastName2']) ? fix_string($_POST['referenceLastName2']) : "");
			$referencePhone2 = (isset ($_POST['referencePhone2']) ? fix_string($_POST['referencePhone2']) : "");
			$referenceEmail2 = (isset ($_POST['referenceEmail2']) ? fix_string($_POST['referenceEmail2']) : "");
			$referenceFirstName3 = (isset ($_POST['referenceFirstName3']) ? fix_string($_POST['referenceFirstName3']) : "");
			$referenceLastName3 = (isset ($_POST['referenceLastName3']) ? fix_string($_POST['referenceLastName3']) : "");
			$referencePhone3 = (isset ($_POST['referencePhone3']) ? fix_string($_POST['referencePhone3']) : "");
			$referenceEmail3 = (isset ($_POST['referenceEmail3']) ? fix_string($_POST['referenceEmail3']) : "");
		}
		
		// Need to get rid of the data:image/png,base64 header for FileMaker
		$signatureConsent = (isset ($_POST['signatureConsent']) ? substr($_POST['signatureConsent'], 22) : "");
		$signatureConsentLength = (isset ($_POST['signatureConsentB30']) ? strlen($_POST['signatureConsentB30']) : 0);
		$signatureMedical = (isset ($_POST['signatureMedical']) ? substr($_POST['signatureMedical'], 22) : "");
		$signatureMedicalLength = (isset ($_POST['signatureMedicalB30']) ? strlen($_POST['signatureMedicalB30']) : 0);
		$sigConductPlayer = (isset ($_POST['sigConductPlayer']) ? substr($_POST['sigConductPlayer'], 22) : "");
		$sigConductPlayerLength = (isset ($_POST['sigConductPlayerB30']) ? strlen($_POST['sigConductPlayerB30']) : 0);
		$sigConductParent = (isset ($_POST['sigConductParent']) ? substr($_POST['sigConductParent'], 22) : "");
		$sigConductParentLength = (isset ($_POST['sigConductParentB30']) ? strlen($_POST['sigConductParentB30']) : 0);
		$sigMediaRelease = (isset ($_POST['sigMediaRelease']) ? substr($_POST['sigMediaRelease'], 22) : "");
		$sigMediaReleaseLength = (isset ($_POST['sigMediaReleaseB30']) ? strlen($_POST['sigMediaReleaseB30']) : 0);
		
		if (empty($signatureConsent)) {
			$signatureConsent = $record->getField('ParentSignatureInformedConsent64');
		}
		if (empty($signatureMedical)) {
			$signatureMedical = $record->getField('ParentSignatureMedicalRelease64');
		}
		if (empty($sigConductPlayer)) {
			$sigConductPlayer = $record->getField('PlayerSigConduct64');
		}
		if (empty($sigConductParent)) {
			$sigConductParent = $record->getField('ParentSigConduct64');
		}
		if (empty($sigMediaRelease)) {
			$sigMediaRelease = $record->getField('ParentSigMediaRelease64');
		}
		
		## Get Related Personnel Record ##########################################
		$related_personnel_records = $record->getRelatedSet('EventPersonnel__Personnel');
		$related_personnel = $related_personnel_records[0];
		$related_personnel_ID = $related_personnel->getRecordId();
		$ID_Personnel = $related_personnel->getField('EventPersonnel__Personnel::ID');
		$U19 = ($related_personnel->getField('EventPersonnel__Personnel::Age') < 19 ? true : false);
		$Photo64 = $related_personnel->getField('EventPersonnel__Personnel2::Photo64');
		$ProofOfDOB64 = $related_personnel->getField('EventPersonnel__Personnel2::ProofOfDOB64');
		$Passport64 = $related_personnel->getField('EventPersonnel__Personnel2::Passport64');
		$OtherTravel64 = $related_personnel->getField('EventPersonnel__Personnel2::OtherTravel64');
		$InsuranceCard64 = $related_personnel->getField('EventPersonnel__Personnel2::InsuranceCard64');
		
		## Get Related Measurement Record ########################################
		$measurementRequest = $fm->newFindCommand('PHP-MeasurementsRelated');
		$measurementRequest->addFindCriterion('ID_Personnel', '==' . $ID_Personnel);
		$measurementResult = $measurementRequest->execute();
		if (FileMaker::isError($measurementResult)) {
			$countMeasurement = 0;
		} else {
			$countMeasurement = $measurementResult->getFoundSetCount();
			$related_measurement_records = $record->getRelatedSet('EventPersonnel__Personnel__Measurement');
			$related_measurement = $related_measurement_records[0];
		}
		
		## Get Related Other Sports Records ########################################
		$related_othersports = $record->getRelatedSet('EventPersonnel__OtherSports');
		if (FileMaker::isError($related_othersports)) {
			$related_othersports_count = 0;
		} else {
			$related_othersports_count = count($related_othersports);
		}
		
		## Get Related Primary ClubMembership Record #############################
		$compoundMembershipRequest =& $fm->newCompoundFindCommand('PHP-ClubMembershipRelated');
		$clubMembershipRequest =& $fm->newFindRequest('PHP-ClubMembershipRelated');
		$clubMembershipRequest2 =& $fm->newFindRequest('PHP-ClubMembershipRelated');
		$clubMembershipRequest->addFindCriterion('ID_Personnel', '==' . $ID_Personnel);
		$clubMembershipRequest->addFindCriterion('Primary_flag', 1);
		$clubMembershipRequest2->addFindCriterion('Inactive_flag', 1);
		$clubMembershipRequest2->setOmit(true);
		$compoundMembershipRequest->add(1, $clubMembershipRequest);
		$compoundMembershipRequest->add(2, $clubMembershipRequest2);
		$clubMembershipResult = $compoundMembershipRequest->execute();
		if (FileMaker::isError($clubMembershipResult)) {
			$clubMembershipCount = 0;
		} else {
			$clubMembershipCount = $clubMembershipResult->getFoundSetCount();
			$related_clubMembership_records = $record->getRelatedSet('EventPersonnel__ClubMembership.Primary');
			$related_clubMembership = $related_clubMembership_records[0];
			$related_clubMembership_ID = $related_clubMembership->getRecordId();
			$ID_Club_original = $related_clubMembership->getField('EventPersonnel__ClubMembership.Primary::ID_Club');
		}
		
	} else { // End if(empty($IDType))
		$signatureConsentLength = (isset ($_POST['signatureConsentB30']) ? strlen($_POST['signatureConsentB30']) : 0);
		$signatureMedicalLength = (isset ($_POST['signatureMedicalB30']) ? strlen($_POST['signatureMedicalB30']) : 0);
		$sigConductPlayerLength = (isset ($_POST['sigConductPlayerB30']) ? strlen($_POST['sigConductPlayerB30']) : 0);
		$sigConductParentLength = (isset ($_POST['sigConductParentB30']) ? strlen($_POST['sigConductParentB30']) : 0);
		$sigMediaReleaseLength = (isset ($_POST['sigMediaReleaseB30']) ? strlen($_POST['sigMediaReleaseB30']) : 0);
	}
	
	$waiver = (isset ($_POST['waiver']) ? fix_string($_POST['waiver']) : "");
	
	// Actions to do when form is submitted //
	if (isset($_POST['respondent_exists']) && empty($IDType) && !$UpdateSchool) { // EventPersonnel with a submitted form
		
		##    Mandatory Field Check      #########################################
		$fail .= validate_empty_field($firstName, "First Name");
		$fail .= validate_empty_field($lastName, "Last Name");
		$fail .= validate_DOB($DOB);
		$fail .= validate_empty_field($gender, "Gender");
		
		$images_face = Slim::getImages('slim_face');
		$image_face = $images_face[0];
		$name_face = $image_face['output']['name'];
		$data_face = $image_face['output']['data'];
		if (empty($Photo64) && empty($name_face) && $includeFacePhoto == "Mandatory") {
			$fail .= "Your Face Photo is required.";
		}
		// store the face photo file
		if (!empty($name_face)) {
			$file_face = Slim::saveFile($data_face, $name_face, '../tmp/');
			if (!empty($file_face['name'])) {
				$FacePhotoCropPath = "https://hiperforms.com/tmp/" . $file_face['name'];
			}
		}
		
		$images_DOB = Slim::getImages('slim_DOB');
		$image_DOB = $images_DOB[0];
		$name_DOB = $image_DOB['output']['name'];
		$data_DOB = $image_DOB['output']['data'];

//		if (empty($ProofOfDOB64) && empty($name_DOB) && $IsPlayer) {
//			$fail .= "Your Proof of DOB is required.";
//		}
		
		// store the DOB file
		if (!empty($name_DOB)) {
			$file_DOB = Slim::saveFile($data_DOB, $name_DOB, '../tmp/');
			if (!empty($file_DOB['name'])) {
				$ProofOfDOBCropPath = "https://hiperforms.com/tmp/" . $file_DOB['name'];
			}
		}
		
		if ($includeDominantHandFoot == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($dominantHand, "Dominant Hand");
		}
		if ($includeDominantHandFoot == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($dominantFoot, "Dominant Foot");
		}
		// Do numeric validations first, and if failed reset the variable so it doesn't fail FileMaker's validation //
		if ($height_UM == "m") {
			$fail_Meters = validate_heightMeters($heightMeters);
			if (!empty($fail_heightMeters)) {
				$fail .= $fail_heightMeters;
				$heightMeters = $heightMetersOriginal;
				unset($heightMeters_new);
			}
		} else {
			$fail_heightFeet = validate_heightFeet($heightFeet);
			if (!empty($fail_heightFeet)) {
				$fail .= $fail_heightFeet;
				$heightFeet = $heightFeetOriginal;
				unset($heightFeet_new);
			}
			$fail_heightInches = validate_heightInches($heightInches);
			if (!empty($fail_heightInches)) {
				$fail .= $fail_heightInches;
				$heightInches = $heightInchesOriginal;
				unset($heightInches_new);
			}
		}
		if ($includeHeightWeight == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_weight($weight);
		}
		if ((empty($MatchJerseySize) || empty($MatchShortsSize) || empty($tShirtSize) || empty($poloSize) || empty($shortsSize) || empty($trackSuitBottomSize) || empty($trackSuitTopSize))
			&& $includeKit == "Mandatory"
		) {
			$fail .= validate_clothingSizes("");
		}
		if ($includeGradeLevel == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($CurrentSchoolGradeLevel, "Grade Level");
		}
		$fail .= validate_empty_field($homeAddress1, "Home Address: Street 1");
		$fail .= validate_empty_field($City, "Home Address: City");
		$fail .= validate_empty_field($State, "Home Address: State");
		$fail .= validate_zip($zipCode);
		$fail .= validate_empty_field($Country, "Country");
		$fail .= validate_eMail($eMail);
		if ($includeCellNumber == "Mandatory") {
			$fail .= validate_empty_field($PrimaryPhoneNumber, "Primary Phone Number");
		}
		if ($includeInsurance == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($healthInsuranceCompany, "Health Insurance Company");
			$fail .= validate_empty_field($healthPlanID, "Health Plan ID");
			if (empty($InsuranceCard64) && empty($name_insurance)) {
				$fail .= "An image upload of your insurance card is missing.<br />";
			}
		}
		if ($includeConditions == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_allergiesConditions($allergiesConditions);
			$fail .= validate_allergiesConditionsDescr($allergiesConditionsDescr, $allergiesConditions);
		}
		if ($includeMedications == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_medications($medications);
			$fail .= validate_medicationsDescr($medicationsDescr, $medications);
			$fail .= validate_empty_field($TakingBannedSubstance, "Are You Taking Banned Substances (Yes/No)");
			if ($TakingBannedSubstance == 'Yes') {
				$fail .= validate_empty_field($BannedSubstanceDescription, "Banned Substance Description");
			}
		}
		
		if ($includeMembershipID == "Mandatory") {
			$fail_MembershipID .= validate_Membership($MembershipID);
			if (!empty($fail_MembershipID)) {
				$fail .= $fail_MembershipID;
				$MembershipID = "";
			} else {
				$fail .= validate_empty_field($MembershipID, "Membership ID");
			}
		}
		if (empty($DoNotBelongToAClub_flag) && empty($UnlistedClub_flag)) {
			$fail .= validate_empty_field($ID_Club, "Primary Club");
		}
		if (!empty($UnlistedClub_flag) && (empty($UnlistedClub_Name) || empty($UnlistedClub_City))) {
			$fail .= "An unlisted club needs a name and city. <br />";
		}
		if ($includeNationalEligible == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($nationalLevelEligible, "Are You Eligible for USA Rugby National Team?");
			if ($nationalLevelEligible != "Yes") {
				$fail .= validate_empty_field($nationalLevelEligibleExplain, "Are You Eligible for USA Rugby National Team? explanation");
			}
		}
		if ($includePositionFields == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($primary15sPosition, "Primary 15's Position");
			$fail .= validate_empty_field($primary7sPosition, "Primary 7's Position");
		}
		if ($includeStartedPlayingFields == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($yearStartedPlaying, "Year Started Playing Rugby");
		}
		if ($includePassportFields == "Mandatory") {
			$fail .= validate_empty_field($passportHolder, "Valid Passport");
			$fail .= validate_empty_field($passportNumber, "Passport Number");
			$fail .= validate_empty_field($nameOnPassport, "Name on Passport");
			if (empty($passportExpiration)) {
				$fail .= validate_empty_field($passportExpiration, "Passport Expiration");
			} else {
				$fail .= validate_PassportExpiration($passportExpirationsave);
			}
			$fail .= validate_empty_field($passportIssuingCountry, "Issuing Country");
			$fail .= validate_empty_field($Citizen1, "Country of Citizenship 1");
		}
		if ($includeAirTravel == "Mandatory") {
			$fail .= validate_empty_field($ID_primaryAirport, "Primary Airport");
		}
		
		$fail .= validate_empty_field($emergencyContactFirstName, "Emergency Contact: First Name");
		$fail .= validate_empty_field($emergencyContactLastName, "Emergency Contact: Last Name");
		$fail .= validate_empty_field($emergencyContactNumber, "Emergency Contact: Phone Number");
		$fail .= validate_empty_field($emergencyContactRelationship, "Emergency Contact: Relationship");
		
		if ($includeParent == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($Guardian1Type, "Parent / Guardian 1: Type");
			$fail .= validate_empty_field($Guardian1FirstName, "Parent / Guardian 1: First Name");
			$fail .= validate_empty_field($Guardian1LastName, "Parent / Guardian 1: Last Name");
			$fail .= validate_empty_field($Guardian1Cell, "Parent / Guardian 1: Phone");
			$fail .= validate_empty_field($Guardian1eMail, "Parent / Guardian 1: E-Mail");
		}
		
		if ($includeReferenceFields == "Mandatory" && $CampRole == "Player") {
			$fail .= validate_empty_field($referenceFirstName1, "Coach Reference: First Name");
			$fail .= validate_empty_field($referenceLastName1, "Coach Reference: Last Name");
			$fail .= validate_empty_field($referencePhone1, "Coach Reference: Phone");
			$fail .= validate_empty_field($referenceEmail1, "Coach Reference: E-Mail");
		}
		
		if ($SignatureOption == "All Players" || ($SignatureOption == "U18 Players" && $U18AtStartOfEvent)) {
			if ($signatureConsentLength < 26 && empty($signatureConsent)) {
				$fail .= "Parental Signature of Consent is missing. <br />";
			}
			if ($signatureMedicalLength < 26 && empty($signatureMedical)) {
				$fail .= "Parental Signature for Medical Release is missing. <br />";
			}
			if ($sigConductPlayerLength < 26 && empty($sigConductPlayer)) {
				$fail .= "Player Signature for Code of Conduct is missing. <br />";
			}
			if ($sigConductParentLength < 26 && empty($sigConductParent)) {
				$fail .= "Parent Signature for Code of Conduct is missing. <br />";
			}
			if ($sigMediaReleaseLength < 26 && empty($sigMediaRelease)) {
				$fail .= "Parent Signature for Media Release is missing. <br />";
			}
			
			$fail .= validate_waiver($waiver);
		} elseif (empty($SignatureOption)) {
			$fail .= validate_waiver($waiver);
		}
		##########################################################################
		
		// Apply all field changes to HiPer, and relay success and/or errors //
		$edit = $fm->newEditCommand('PHP-EventInvite', $record->getRecordId());
		
		if ($SignatureOption == "All Players" || ($SignatureOption == "U18 Players" && $U18AtStartOfEvent)) {
			$edit->setField('ParentSignatureInformedConsent64', $signatureConsent);
			$edit->setField('ParentSignatureMedicalRelease64', $signatureMedical);
			$edit->setField('PlayerSigConduct64', $sigConductPlayer);
			$edit->setField('ParentSigConduct64', $sigConductParent);
			$edit->setField('ParentSigMediaRelease64', $sigMediaRelease);
		}
		if ($CampRole == "Player") {
			$edit->setField('TakingBannedSubstance', $TakingBannedSubstance);
			$edit->setField('BannedSubstanceViaPrescription', $BannedSubstanceViaPrescription);
			$edit->setField('BannedSubstanceDescription', $BannedSubstanceDescription);
		}
		$result = $edit->execute();
		
		$UpdateEmail = $fm->NewEditCommand('PHP-PersonnelRelatedData', $related_personnel_ID);
		$UpdateEmail->setField('eMail', $eMail);
		$ResultUpdateEmail = $UpdateEmail->execute();
		
		if (FileMaker::isError($ResultUpdateEmail)) {
			if ($ResultUpdateEmail->code == 504) {
				$fail .= "E-mail address could not be updated, as another record in the database already uses this address. <br />";
			} else {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 221: " . $resultPersonnel->getMessage() . "</p>";
				die();
			}
		}
		
		$editPersonnel = $fm->NewEditCommand('PHP-PersonnelRelatedData', $related_personnel_ID);
		if ($CampRole == "Player") {
			$editPersonnel->setField('dominantHand', $dominantHand);
			$editPersonnel->setField('dominantFoot', $dominantFoot);
			$editPersonnel->setField('healthInsuranceCompany', $healthInsuranceCompany);
			$editPersonnel->setField('healthPlanID', $healthPlanID);
			$editPersonnel->setField('allergiesConditions', $allergiesConditions);
			$editPersonnel->setField('allergiesConditionsDescr', $allergiesConditionsDescr);
			$editPersonnel->setField('medications', $medications);
			$editPersonnel->setField('medicationsDescr', $medicationsDescr);
			$editPersonnel->setField('yearStartedPlaying', $yearStartedPlaying);
			$editPersonnel->setField('monthStartedPlaying', $monthStartedPlaying);
			$editPersonnel->setField('HighlightVideoLink', $HighlightVideoLink);
			$editPersonnel->setField('FullMatchLink1', $FullMatchLink1);
			$editPersonnel->setField('FullMatchLink2', $FullMatchLink2);
			$editPersonnel->setField('FullMatchLink3', $FullMatchLink3);
		}
		$editPersonnel->setField('firstName', $firstName);
		$editPersonnel->setField('middleName', $middleName);
		$editPersonnel->setField('lastName', $lastName);
		$editPersonnel->setField('nickName', $nickName);
		$editPersonnel->setField('DOB', $DOB);
		$editPersonnel->setField('gender', $gender);
		$editPersonnel->setField('MatchJerseySize', $MatchJerseySize);
		$editPersonnel->setField('MatchShortsSize', $MatchShortsSize);
		$editPersonnel->setField('tShirtSize', $tShirtSize);
		$editPersonnel->setField('poloSize', $poloSize);
		$editPersonnel->setField('shortsSize', $shortsSize);
		$editPersonnel->setField('trackSuitBottomSize', $trackSuitBottomSize);
		$editPersonnel->setField('trackSuitTopSize', $trackSuitTopSize);
		if ($includeEducationFields != "Hidden" && $CampRole == "Player") {
			$editPersonnel->setField('StatePlayingIn', $StatePlayingIn);
			$editPersonnel->setField('CurrentSchoolGradeLevel', $CurrentSchoolGradeLevel);
			$editPersonnel->setField('ID_School_1_12', $ID_School);
			$editPersonnel->setField('HighSchoolGraduationYear', $HighSchoolGraduationYear);
			if (!$U18AtStartOfEvent) {
				$editPersonnel->setField('ID_School_College', $ID_School_College);
				$editPersonnel->setField('graduationCollegeYear', $graduationCollegeYear);
				$editPersonnel->setField('currentlyMilitary', $currentlyMilitary);
				$editPersonnel->setField('militaryBranch', $militaryBranch);
				$editPersonnel->setField('militaryComponent', $militaryComponent);
			}
		}
		if ($includeReferenceFields != "Hidden" && $CampRole == "Player") {
			$editPersonnel->setField('referenceFirstName1', $referenceFirstName1);
			$editPersonnel->setField('referenceLastName1', $referenceLastName1);
			$editPersonnel->setField('referencePhone1', $referencePhone1);
			$editPersonnel->setField('referenceEmail1', $referenceEmail1);
			$editPersonnel->setField('referenceFirstName2', $referenceFirstName2);
			$editPersonnel->setField('referenceLastName2', $referenceLastName2);
			$editPersonnel->setField('referencePhone2', $referencePhone2);
			$editPersonnel->setField('referenceEmail2', $referenceEmail2);
			$editPersonnel->setField('referenceFirstName3', $referenceFirstName3);
			$editPersonnel->setField('referenceLastName3', $referenceLastName3);
			$editPersonnel->setField('referencePhone3', $referencePhone3);
			$editPersonnel->setField('referenceEmail3', $referenceEmail3);
		}
		
		## Update database with images uploaded to web server ###
		## Only if an image was uploaded ######################################
		if (!empty($FacePhotoCropPath)) {
			$editPersonnel->setField('EventPersonnel__Personnel2::PhotoURL', $FacePhotoCropPath);
		}
		if (!empty($ProofOfDOBCropPath)) {
			$editPersonnel->setField('EventPersonnel__Personnel2::ProofOfDOBURL', $ProofOfDOBCropPath);
		}
		if (!empty($InsuranceCardCropPath)) {
			$editPersonnel->setField('EventPersonnel__Personnel2::InsuranceCardURL', $InsuranceCardCropPath);
		}
		if (!empty($PassportCropPath)) {
			$editPersonnel->setField('EventPersonnel__Personnel2::PassportURL', $PassportCropPath);
		}
		if (!empty($OtherTravelCropPath)) {
			$editPersonnel->setField('EventPersonnel__Personnel2::OtherTravelURL', $OtherTravelCropPath);
		}
		#######################################################################
		
		$editPersonnel->setField('homeAddress1', $homeAddress1);
		$editPersonnel->setField('homeAddress2', $homeAddress2);
		$editPersonnel->setField('City', $City);
		$editPersonnel->setField('State', $State);
		$editPersonnel->setField('zipCode', $zipCode);
		$editPersonnel->setField('Country', $Country);
		$editPersonnel->setField('PrimaryPhoneNumber', $PrimaryPhoneNumber);
		$editPersonnel->setField('PrimaryPhoneText_flag', $PrimaryPhoneText_flag);
		$editPersonnel->setField('MembershipID', $MembershipID);
		$editPersonnel->setField('unlistedClubName', $UnlistedClub_Name);
		$editPersonnel->setField('unlistedClubCity', $UnlistedClub_City);
		$editPersonnel->setField('unlistedClubState', $UnlistedClub_State);
		if ($includeStartedPlayingFields != 'Hidden' && $CampRole == "Player") {
			$editPersonnel->setField('yearStartedPlaying', $yearStartedPlaying);
			$editPersonnel->setField('monthStartedPlaying', $monthStartedPlaying);
		}
		if ($includePositionFields != 'Hidden' && $CampRole == "Player") {
			$editPersonnel->setField('primary15sPosition', $primary15sPosition);
			$editPersonnel->setField('primary7sPosition', $primary7sPosition);
		}
		$editPersonnel->setField('passportHolder', $passportHolder);
		$editPersonnel->setField('passportNumber', $passportNumber);
		$editPersonnel->setField('nameOnPassport', $nameOnPassport);
		$editPersonnel->setField('passportExpiration', $passportExpiration);
		$editPersonnel->setField('VisaDateIssued', $VisaDateIssued);
		$editPersonnel->setField('passportIssuingCountry', $passportIssuingCountry);
		$editPersonnel->setField('Citizen1', $Citizen1);
		$editPersonnel->setField('Citizen2', $Citizen2);
		$editPersonnel->setField('ID_primaryAirport', $ID_primaryAirport);
		$editPersonnel->setField('ID_secondaryAirport', $ID_secondaryAirport);
		$editPersonnel->setField('travelComments', $travelComments);
		$editPersonnel->setField('frequentFlyerInfo', $frequentFlyerInfo);
		if ($playerLevel != 'High School' && $playerLevel != 'HSAA') {
			$editPersonnel->setField('spouseName', $spouseName);
			$editPersonnel->setField('spouseEmail', $spouseEmail);
			$editPersonnel->setField('spouseCell', $spouseCell);
		} elseif ($CampRole == "Player") {
			$editPersonnel->setField('CurrentSchoolGradeLevel', $CurrentSchoolGradeLevel);
			$editPersonnel->setField('Guardian1Type', $Guardian1Type);
			$editPersonnel->setField('Guardian1FirstName', $Guardian1FirstName);
			$editPersonnel->setField('Guardian1LastName', $Guardian1LastName);
			$editPersonnel->setField('Guardian1Cell', $Guardian1Cell);
			$editPersonnel->setField('Guardian1eMail', $Guardian1eMail);
			$editPersonnel->setField('Guardian2Type', $Guardian2Type);
			$editPersonnel->setField('Guardian2FirstName', $Guardian2FirstName);
			$editPersonnel->setField('Guardian2LastName', $Guardian2LastName);
			$editPersonnel->setField('Guardian2Cell', $Guardian2Cell);
			$editPersonnel->setField('Guardian2eMail', $Guardian2eMail);
			$editPersonnel->setField('Guardian3Type', $Guardian3Type);
			$editPersonnel->setField('Guardian3FirstName', $Guardian3FirstName);
			$editPersonnel->setField('Guardian3LastName', $Guardian3LastName);
			$editPersonnel->setField('Guardian3Cell', $Guardian3Cell);
			$editPersonnel->setField('Guardian3eMail', $Guardian3eMail);
			$editPersonnel->setField('Guardian4Type', $Guardian4Type);
			$editPersonnel->setField('Guardian4FirstName', $Guardian4FirstName);
			$editPersonnel->setField('Guardian4LastName', $Guardian4LastName);
			$editPersonnel->setField('Guardian4Cell', $Guardian4Cell);
			$editPersonnel->setField('Guardian4eMail', $Guardian4eMail);
		}
		$editPersonnel->setField('emergencyContactFirstName', $emergencyContactFirstName);
		$editPersonnel->setField('emergencyContactLastName', $emergencyContactLastName);
		$editPersonnel->setField('emergencyContactNumber', $emergencyContactNumber);
		$editPersonnel->setField('emergencyContactRelationship', $emergencyContactRelationship);
		if ($playerLevel == 'National' && $CampRole == "Player") {
			$editPersonnel->setField('nationalLevelEligible', $nationalLevelEligible);
			$editPersonnel->setField('nationalLevelEligibleExplain', $nationalLevelEligibleExplain);
		}
		
		$resultPersonnel = $editPersonnel->execute();
		
		if (FileMaker::isError($resultPersonnel)) {
			echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
				. "<p>Error Code 222: " . $resultPersonnel->getMessage() . "</p>";
			die();
		}
		
		$ID_Club = ($UnlistedClub_flag == 1 ? "NotInList" : $ID_Club);
		if ($ID_Club != $ID_Club_original) {
			if (isset($related_clubMembership_ID)) {
				$clubMembershipEdit = $fm->NewEditCommand('PHP-ClubMembershipRelated', $related_clubMembership_ID);
				$clubMembershipEdit->setField('Primary_flag', 0);
				$clubMembershipResult = $clubMembershipEdit->execute();
			}
			$clubMembership_data = array(
				'ID_Personnel' => $ID_Personnel,
				'ID_Club' => $ID_Club,
				'Primary_flag' => 1,
				'DoNotBelongToAClub_flag' => $DoNotBelongToAClub_flag,
				'UnlistedClub_flag' => $UnlistedClub_flag,
			);
			$clubMembershipNewRequest =& $fm->newAddCommand('PHP-ClubMembershipRelated', $clubMembership_data);
			$result = $clubMembershipNewRequest->execute();
			if (FileMaker::isError($result)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 223: " . $result->getMessage() . "</p>";
				exit;
			}
		}
		
		if ($CampRole == "Player") {
			if (isset($heightFeet_new) || isset($heightInches_new) || isset($heightMeters_new) || isset($weight_new)) {
				$heightFeet = $height_UM != "m" ? $heightFeet : intval($heightMeters * 3.28084);
				$heightInches = $height_UM != "m" ? $heightInches : round(($heightFeet * 12) % 12, 1);
				$heightMeters = $height_UM == "m" ? $heightMeters : round(($heightFeet + ($heightInches / 12)) * .3048, 2);
				
				$measurement_data = array(
					'ID_Personnel' => $ID_Personnel,
					'heightFeet' => $heightFeet,
					'heightInches' => $heightInches,
					'heightMeters' => $heightMeters,
					$weight_UM == "kg" ? 'Weight_kg' : 'Weight_lb' => $weight,
				);
				$newMeasurementRequest =& $fm->newAddCommand('PHP-MeasurementsRelated', $measurement_data);
				$result = $newMeasurementRequest->execute();
				if (FileMaker::isError($result)) {
					echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 224: " . $result->getMessage() . "</p>";
					exit;
				}
				
				// Update stored measurment data for camps/matches:
				$newPerformScript = $fm->newPerformScriptCommand('PHP-MeasurementsRelated', 'Trigger Measurement Field Update (ID_Personnel ; Action )', $ID_Personnel);
				$scriptResult = $newPerformScript->execute();
				
			}
			
			// Other Sports //
			if (!empty($OtherSport) && !empty($OtherSportDateStart)) {
				$OtherSport_data = array(
					'ID_Personnel' => $ID_Personnel,
					'Sport' => $OtherSport,
					'DateStarted' => $OtherSportDateStart,
					'DateEnded' => $OtherSportDateEnd,
					'Description' => $OtherSportDescription,
				);
				$newOtherSportRequest =& $fm->newAddCommand('PHP-OtherSportsRelated', $OtherSport_data);
				$result = $newOtherSportRequest->execute();
				if (FileMaker::isError($result)) {
					echo "<p>Error: There was a problem adding a new Other Sports record to your profile. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
						. "<p>Error Code 218: " . $result->getMessage() . "</p>";
					exit;
				}
				unset($OtherSport);
				unset($OtherSportDateStart);
				unset($OtherSportDateEnd);
			}
			
			foreach ($OtherSport_Delete as $key => $value) {
				if ($value == 1) {
					$RecordID = $key;
					$deleteOtherSport = $fm->NewDeleteCommand('PHP-OtherSportsRelated', $RecordID);
					$resultDelete = $deleteOtherSport->execute();
				}
			}
		}
		
		if (!empty($FacePhotoCropPath) || !empty($ProofOfDOBCropPath) || !empty($InsuranceCardCropPath) || !empty($PassportCropPath) || !empty($OtherTravelCropPath)) {
			// Run script to put image URLs into their container fields //
			$newPerformScript = $fm->newPerformScriptCommand('PHP-PersonnelRelatedData', 'PHP Player Update - URL to container', $ID_Personnel);
			$scriptResult = $newPerformScript->execute();
			if (FileMaker::isError($scriptResult)) {
				echo "<p>Error: There was a problem processing your information. Please send a note to tech@hiperforms.com with the following information so they can review your record: </p>"
					. "<p>Error Code 225: " . $scriptResult->getMessage() . "</p>";
				die();
			}
		}
		cleanPictures();
		
		// Refresh data //
		$result = $request->execute();
		if (FileMaker::isError($result)) {
			echo "<p>Error: Your form could not be loaded. Your link ID is invalid. Check the link that was e-mailed you, and try again.</p>"
				. "<p>Error Code 226: " . $result->getMessage() . "</p>";
			echo "<p>If problems continue, please send a note to tech@hiperforms.com with the above information so they can review your record.</p>";
			die();
		}
		$records = $result->getRecords();
		$record = $result->getFirstRecord();
		$related_personnel_records = $record->getRelatedSet('EventPersonnel__Personnel');
		$related_personnel = $related_personnel_records[0];
		
		## Get Related Other Sports Records ########################################
		$related_othersports = $record->getRelatedSet('EventPersonnel__OtherSports');
		if (FileMaker::isError($related_othersports)) {
			$related_othersports_count = 0;
		} else {
			$related_othersports_count = count($related_othersports);
		}
		
		if (!empty($fail)) {
			//## Red Field Borders on required fields that failed
			echo '
		<style type="text/css">
			.missing {
			border: 2px solid red
			}
		</style>';
		} else {
			// Either go to CC Payment Form, or say Thank You //
			if ($includeCCPayment == 1 && $inviteStatus == "Accepted") {
				header("Location: Payment.php?ID=$ID");
				exit();
			} else {
				$message = "Thank You. Your Profile has been Updated.";
			}
		}
		
	} elseif (empty($IDType) && !$UpdateSchool) { // EventPersonnel ID, when the form first loads
		
		## Get existing personnel HiPer data to display ##########################
		if ($CampRole == "Player") {
			$dominantHand = $related_personnel->getField('EventPersonnel__Personnel::dominantHand');
			$dominantFoot = $related_personnel->getField('EventPersonnel__Personnel::dominantFoot');
			$healthInsuranceCompany = $related_personnel->getField('EventPersonnel__Personnel::healthInsuranceCompany');
			$healthPlanID = $related_personnel->getField('EventPersonnel__Personnel::healthPlanID');
			$allergiesConditions = $related_personnel->getField('EventPersonnel__Personnel::allergiesConditions');
			$allergiesConditionsDescr = $related_personnel->getField('EventPersonnel__Personnel::allergiesConditionsDescr');
			$medications = $related_personnel->getField('EventPersonnel__Personnel::medications');
			$medicationsDescr = $related_personnel->getField('EventPersonnel__Personnel::medicationsDescr');
			$nationalLevelEligible = $related_personnel->getField('EventPersonnel__Personnel::nationalLevelEligible');
			$nationalLevelEligibleExplain = $related_personnel->getField('EventPersonnel__Personnel::nationalLevelEligibleExplain');
			$yearStartedPlaying = $related_personnel->getField('EventPersonnel__Personnel::yearStartedPlaying');
			$monthStartedPlaying = $related_personnel->getField('EventPersonnel__Personnel::monthStartedPlaying');
			$primary15sPosition = $related_personnel->getField('EventPersonnel__Personnel::primary15sPosition');
			$primary7sPosition = $related_personnel->getField('EventPersonnel__Personnel::primary7sPosition');
			$HighlightVideoLink = $related_personnel->getField('EventPersonnel__Personnel::HighlightVideoLink');
			$FullMatchLink1 = $related_personnel->getField('EventPersonnel__Personnel::FullMatchLink1');
			$FullMatchLink2 = $related_personnel->getField('EventPersonnel__Personnel::FullMatchLink2');
			$FullMatchLink3 = $related_personnel->getField('EventPersonnel__Personnel::FullMatchLink3');
			$Guardian1Type = $related_personnel->getField('EventPersonnel__Personnel::Guardian1Type');
			$Guardian1FirstName = $related_personnel->getField('EventPersonnel__Personnel::Guardian1FirstName');
			$Guardian1LastName = $related_personnel->getField('EventPersonnel__Personnel::Guardian1LastName');
			$Guardian1Cell = $related_personnel->getField('EventPersonnel__Personnel::Guardian1Cell');
			$Guardian1eMail = $related_personnel->getField('EventPersonnel__Personnel::Guardian1eMail');
			$Guardian2Type = $related_personnel->getField('EventPersonnel__Personnel::Guardian2Type');
			$Guardian2FirstName = $related_personnel->getField('EventPersonnel__Personnel::Guardian2FirstName');
			$Guardian2LastName = $related_personnel->getField('EventPersonnel__Personnel::Guardian2LastName');
			$Guardian2Cell = $related_personnel->getField('EventPersonnel__Personnel::Guardian2Cell');
			$Guardian2eMail = $related_personnel->getField('EventPersonnel__Personnel::Guardian2eMail');
			$Guardian3Type = $related_personnel->getField('EventPersonnel__Personnel::Guardian3Type');
			$Guardian3FirstName = $related_personnel->getField('EventPersonnel__Personnel::Guardian3FirstName');
			$Guardian3LastName = $related_personnel->getField('EventPersonnel__Personnel::Guardian3LastName');
			$Guardian3Cell = $related_personnel->getField('EventPersonnel__Personnel::Guardian3Cell');
			$Guardian3eMail = $related_personnel->getField('EventPersonnel__Personnel::Guardian3eMail');
			$Guardian4Type = $related_personnel->getField('EventPersonnel__Personnel::Guardian4Type');
			$Guardian4FirstName = $related_personnel->getField('EventPersonnel__Personnel::Guardian4FirstName');
			$Guardian4LastName = $related_personnel->getField('EventPersonnel__Personnel::Guardian4LastName');
			$Guardian4Cell = $related_personnel->getField('EventPersonnel__Personnel::Guardian4Cell');
			$Guardian4eMail = $related_personnel->getField('EventPersonnel__Personnel::Guardian4eMail');
		}
		$firstName = $related_personnel->getField('EventPersonnel__Personnel::firstName');
		$middleName = $related_personnel->getField('EventPersonnel__Personnel::middleName');
		$lastName = $related_personnel->getField('EventPersonnel__Personnel::lastName');
		$nickName = $related_personnel->getField('EventPersonnel__Personnel::nickName');
		$DOB_original = $related_personnel->getField('EventPersonnel__Personnel::DOB');
		$DOB_original_test = explode('/', $DOB_original);
		if (count($DOB_original_test) == 3) {
			if (checkdate($DOB_original_test[0], $DOB_original_test[1], $DOB_original_test[2]) == TRUE) {
				$DOB = new DateTime($DOB_original);
				$DOBsave = $DOB->format('Y-m-d');
			}
		} else {
			$DOB = "";
		}
		$gender = $related_personnel->getField('EventPersonnel__Personnel::gender');
		
		if ($countMeasurement > 0) {
			$heightFeet = $related_measurement->getField('EventPersonnel__Personnel__Measurement::heightFeet');
			$heightInches = $related_measurement->getField('EventPersonnel__Personnel__Measurement::heightInches');
			$heightMeters = round(($heightFeet + ($heightInches / 12)) * .3048, 2);
			$weight = $related_measurement->getField('EventPersonnel__Personnel__Measurement::Weight_lb');
			$heightFeetOriginal = $related_measurement->getField('EventPersonnel__Personnel__Measurement::heightFeet');
			$heightInchesOriginal = $related_measurement->getField('EventPersonnel__Personnel__Measurement::heightInches');
			$heightMetersOriginal = $related_measurement->getField('EventPersonnel__Personnel__Measurement::heightMeters');
			$weightOriginal = $related_measurement->getField('EventPersonnel__Personnel__Measurement::Weight_lb');
		}
		$MatchJerseySize = $related_personnel->getField('EventPersonnel__Personnel::MatchJerseySize');
		$MatchShortsSize = $related_personnel->getField('EventPersonnel__Personnel::MatchShortsSize');
		$tShirtSize = $related_personnel->getField('EventPersonnel__Personnel::tShirtSize');
		$poloSize = $related_personnel->getField('EventPersonnel__Personnel::poloSize');
		$shortsSize = $related_personnel->getField('EventPersonnel__Personnel::shortsSize');
		$trackSuitBottomSize = $related_personnel->getField('EventPersonnel__Personnel::trackSuitBottomSize');
		$trackSuitTopSize = $related_personnel->getField('EventPersonnel__Personnel::trackSuitTopSize');
		$CurrentSchoolGradeLevel = $related_personnel->getField('EventPersonnel__Personnel::CurrentSchoolGradeLevel');
		$homeAddress1 = $related_personnel->getField('EventPersonnel__Personnel::homeAddress1');
		$homeAddress2 = $related_personnel->getField('EventPersonnel__Personnel::homeAddress2');
		$City = $related_personnel->getField('EventPersonnel__Personnel::City');
		$State = $related_personnel->getField('EventPersonnel__Personnel::State');
		$zipCode = $related_personnel->getField('EventPersonnel__Personnel::zipCode');
		$Country = $related_personnel->getField('EventPersonnel__Personnel::Country');
		$eMail = $related_personnel->getField('EventPersonnel__Personnel::eMail');
		$PrimaryPhoneNumber = $related_personnel->getField('EventPersonnel__Personnel::PrimaryPhoneNumber');
		$PrimaryPhoneText_flag = $related_personnel->getField('EventPersonnel__Personnel::PrimaryPhoneText_flag');
		
		$MembershipID = $related_personnel->getField('EventPersonnel__Personnel::MembershipID');
		$UnlistedClub_Name = $related_personnel->getField('EventPersonnel__Personnel::unlistedClubName');
		$UnlistedClub_City = $related_personnel->getField('EventPersonnel__Personnel::unlistedClubCity');
		$UnlistedClub_State = $related_personnel->getField('EventPersonnel__Personnel::unlistedClubState');
		if ($clubMembershipCount > 0) {
			$ID_Club = $related_clubMembership->getField('EventPersonnel__ClubMembership.Primary::ID_Club');
			$DoNotBelongToAClub_flag = $related_clubMembership->getField('EventPersonnel__ClubMembership.Primary::DoNotBelongToAClub_flag');
			$UnlistedClub_flag = $related_clubMembership->getField('EventPersonnel__ClubMembership.Primary::UnlistedClub_flag');
		}
		
		$passportHolder = $related_personnel->getField('EventPersonnel__Personnel::passportHolder');
		$passportNumber = $related_personnel->getField('EventPersonnel__Personnel::passportNumber');
		$nameOnPassport = $related_personnel->getField('EventPersonnel__Personnel::nameOnPassport');
		$passportExpiration_original = $related_personnel->getField('EventPersonnel__Personnel::passportExpiration');
		if (!empty($passportExpiration_original)) {
			$passportExpiration = new DateTime($passportExpiration_original);
			$passportExpirationsave = $passportExpiration->format('Y-m-d');
		} else {
			$passportExpirationsave = "";
		}
		$VisaDateIssued_original = $related_personnel->getField('EventPersonnel__Personnel::VisaDateIssued');
		if (!empty($VisaDateIssued_original)) {
			$VisaDateIssued = new DateTime($VisaDateIssued_original);
			$VisaDateIssued_save = $VisaDateIssued->format('Y-m-d');
		} else {
			$VisaDateIssued_save = "";
		}
		$passportIssuingCountry = $related_personnel->getField('EventPersonnel__Personnel::passportIssuingCountry');
		$Citizen1 = $related_personnel->getField('EventPersonnel__Personnel::Citizen1');
		$Citizen2 = $related_personnel->getField('EventPersonnel__Personnel::Citizen2');
		$ID_primaryAirport = $related_personnel->getField('EventPersonnel__Personnel::ID_primaryAirport');
		$ID_secondaryAirport = $related_personnel->getField('EventPersonnel__Personnel::ID_secondaryAirport');
		$frequentFlyerInfo = $related_personnel->getField('EventPersonnel__Personnel::frequentFlyerInfo');
		$travelComments = $related_personnel->getField('EventPersonnel__Personnel::travelComments');
		$spouseName = $related_personnel->getField('EventPersonnel__Personnel::spouseName');
		$spouseEmail = $related_personnel->getField('EventPersonnel__Personnel::spouseEmail');
		$spouseCell = $related_personnel->getField('EventPersonnel__Personnel::spouseCell');
		
		$emergencyContactFirstName = $related_personnel->getField('EventPersonnel__Personnel::emergencyContactFirstName');
		$emergencyContactLastName = $related_personnel->getField('EventPersonnel__Personnel::emergencyContactLastName');
		$emergencyContactNumber = $related_personnel->getField('EventPersonnel__Personnel::emergencyContactNumber');
		$emergencyContactRelationship = $related_personnel->getField('EventPersonnel__Personnel::emergencyContactRelationship');
		
		if ($includeEducationFields != "Hidden" && $CampRole == "Player") {
			$StatePlayingIn = $related_personnel->getField('EventPersonnel__Personnel::StatePlayingIn');
			$CurrentSchoolGradeLevel = $related_personnel->getField('EventPersonnel__Personnel::CurrentSchoolGradeLevel');
			$ID_School = $related_personnel->getField('EventPersonnel__Personnel::ID_School_1_12');
			$HighSchoolGraduationYear = $related_personnel->getField('EventPersonnel__Personnel::HighSchoolGraduationYear');
			if (!$U18AtStartOfEvent) {
				$ID_School_College = $related_personnel->getField('EventPersonnel__Personnel::ID_SchoolCollege');
				$graduationCollegeYear = $related_personnel->getField('EventPersonnel__Personnel::graduationCollegeYear');
				$currentlyMilitary = $related_personnel->getField('EventPersonnel__Personnel::currentlyMilitary');
				$militaryBranch = $related_personnel->getField('EventPersonnel__Personnel::militaryBranch');
				$militaryComponent = $related_personnel->getField('EventPersonnel__Personnel::militaryComponent');
			}
		}
		
		if ($includeReferenceFields != "Hidden" && $CampRole == "Player") {
			$referenceFirstName1 = $related_personnel->getField('EventPersonnel__Personnel::referenceFirstName1');
			$referenceLastName1 = $related_personnel->getField('EventPersonnel__Personnel::referenceLastName1');
			$referencePhone1 = $related_personnel->getField('EventPersonnel__Personnel::referencePhone1');
			$referenceEmail1 = $related_personnel->getField('EventPersonnel__Personnel::referenceEmail1');
			$referenceFirstName2 = $related_personnel->getField('EventPersonnel__Personnel::referenceFirstName2');
			$referenceLastName2 = $related_personnel->getField('EventPersonnel__Personnel::referenceLastName2');
			$referencePhone2 = $related_personnel->getField('EventPersonnel__Personnel::referencePhone2');
			$referenceEmail2 = $related_personnel->getField('EventPersonnel__Personnel::referenceEmail2');
			$referenceFirstName3 = $related_personnel->getField('EventPersonnel__Personnel::referenceFirstName3');
			$referenceLastName3 = $related_personnel->getField('EventPersonnel__Personnel::referenceLastName3');
			$referencePhone3 = $related_personnel->getField('EventPersonnel__Personnel::referencePhone3');
			$referenceEmail3 = $related_personnel->getField('EventPersonnel__Personnel::referenceEmail3');
		}
		##########################################################################
	} elseif (!$UpdateSchool) { //Form first loads; Camp ID
		$gender = $campRecord->getField('Gender');
		$allergiesConditions = "";
		$medications = "";
		$nationalLevelEligible = "";
		$passportExpirationsave = "";
		$U18AtStartOfEvent = "";
	}
	
	//## Determine what to show in the Image editors ##//
	if (empty($IDType)) {
		$Photo64 = $related_personnel->getField('EventPersonnel__Personnel2::Photo64');
		$ProofOfDOB64 = $related_personnel->getField('EventPersonnel__Personnel2::ProofOfDOB64');
		$Passport64 = $related_personnel->getField('EventPersonnel__Personnel2::Passport64');
		$OtherTravel64 = $related_personnel->getField('EventPersonnel__Personnel2::OtherTravel64');
		$InsuranceCard64 = $related_personnel->getField('EventPersonnel__Personnel2::InsuranceCard64');
	}
	$FacePhotoEditor = (empty($Photo64) ? "../include/MissingFacePhoto.PNG" : $Photo64);
	$ProofOfDOBEditor = (empty($ProofOfDOB64) ? "../include/MissingDOB.PNG" : $ProofOfDOB64);
	$PassportEditor = (empty($Passport64) ? "../include/MissingPassport.PNG" : $Passport64);
	$OtherTravelEditor = (empty($OtherTravel64) ? "../include/MissingOtherTravel.PNG" : $OtherTravel64);
	$InsuranceCardEditor = (empty($InsuranceCard64) ? "../include/MissingInsurance.PNG" : $InsuranceCard64);
	
	//## Get Drop-Down List values ##//
	$clothingSizeValues = $layout->getValueListTwoFields('Size');
	$stateValues = $layout->getValueListTwoFields('State');
	$countryValues = $layout->getValueListTwoFields('World Countries');
	if ($playerLevel == "High School" || $playerLevel == "HSAA" || $playerLevel == "Youth" || $U18AtStartOfEvent) {
		if ($gender == "Female" || $gender == "Women") {
			$clubValues = $layout->getValueListTwoFields('PHPClubsYouthWomen');
		} else {
			$clubValues = $layout->getValueListTwoFields('PHPClubsYouthMen');
		}
	} else {
		if ($gender == "Female" || $gender == "Women") {
			$clubValues = $layout->getValueListTwoFields('PHPClubsNonYouthWomen');
		} else {
			$clubValues = $layout->getValueListTwoFields('PHPClubsNonYouthMen');
		}
	}
	asort($clubValues);
	$airportValues = $layout->getValueListTwoFields('PHPAirportNameCode');
	asort($airportValues);
	if (!empty($StatePlayingIn) && !empty($CurrentSchoolGradeLevel)) {
		$CompoundSchoolRequest =& $fm->newCompoundFindCommand('PHP-Schools1-12');
		$SchoolRequest1 =& $fm->newFindRequest('PHP-Schools1-12');
		$SchoolRequest2 =& $fm->newFindRequest('PHP-Schools1-12');
		$SchoolRequest3 =& $fm->newFindRequest('PHP-Schools1-12');
		$SchoolRequest1->addFindCriterion('State', '==' . $StatePlayingIn);
		$SchoolRequest2->addFindCriterion('GradeLow', '>' . ($CurrentSchoolGradeLevel + 1));
		$SchoolRequest2->setOmit(true);
		$SchoolRequest3->addFindCriterion('GradeHigh', '<' . ($CurrentSchoolGradeLevel - 1));
		$SchoolRequest3->setOmit(true);
		$CompoundSchoolRequest->add(1, $SchoolRequest1);
		$CompoundSchoolRequest->add(2, $SchoolRequest2);
		$CompoundSchoolRequest->add(3, $SchoolRequest3);
		$SchoolResult = $CompoundSchoolRequest->execute();
		if (FileMaker::isError($SchoolResult)) {
			$fail .= "There was an error retrieving the school records. <br />";
			$SchoolValues = "";
		} else {
			$SchoolRecords = $SchoolResult->getRecords();
			foreach ($SchoolRecords as $value) {
				$SchoolName[] = $value->getField('c_SchoolNameLocation');
				$SchoolID[] = $value->getField('ID');
			}
			$SchoolValues = array_combine($SchoolID, $SchoolName);
			asort($SchoolValues);
		}
	}
	if (!$U18AtStartOfEvent && $CampRole == "Player") {
		$CollegeValues = $layout->getValueListTwoFields('PHPCollege');
		asort($CollegeValues);
		$MilitaryBranchValues = $layout->getValueListTwoFields('Military Branch');
		$MilitaryComponentValues = $layout->getValueListTwoFields('Military Component');
	}
	$guardianValues = $layout->getValueListTwoFields('Parent Guardian Type');
	$relationshipValues = $layout->getValueListTwoFields('Relationship');
	$othersportsValues = $layout->getValueListTwoFields('Other Sport');
	?>

</head>

<body>
<div class="header background">
	<h1><?php echo $pageHeader; ?></h1>
	<table class="tableHeaderTwo">
		<tr>
			<td style="width: 18%">Your Name:</td>
			<td style="width: 39%"><?php echo $name; ?></td>
			<td style="width: 18%">Date of Event:</td>
			<td style="width: 25%"><?php echo $dateStarted; ?></td>
		</tr>
		<tr>
			<td>Event Name:</td>
			<td><?php echo $campName; ?></td>
			<td>Cut-off Date:</td>
			<td><?php echo $inviteCutOff; ?></td>
		</tr>
		<tr>
			<td>Venue:</td>
			<td><?php echo $venueName; ?></td>
			<td>Event Fee:</td>
			<td>$<?php echo $fee; ?></td>
		</tr>
	</table>
</div>
<!-- Show messages instead of form. -->
<?php
if (isset($message)) {
	echo '<br />'
		. '<h3>' . $message . '</h3></div></div></body></html>';
	die();
}
?>
<!-- Add table to display any error messages with submitted form. -->
<?php
if (!empty($fail) && isset($_POST['respondent_exists'])) {
	echo '<table style="width:100%; border:0; padding: 2px; background-color: #eeeeee">
                     <tr><td>Sorry, the following errors were found in your form: 
                        <p style="color: red"><i>' . $fail . '</i></p>
                     </td></tr>
                 </table>';
}
?>
</div> <!-- Ends <div style="text-align: center"> from header.php -->
<h5>Form Notes</h5>
<ul style="width: 90%;">
	<li>Required Fields: If the form is submitted and any required fields are in error, the fields in error will be
		indicated in red.
	</li>
	<li>Date Fields: All dates must be entered in the mm/dd/yyyy or yyyy-mm-dd format.</li>
	<li>For tech/web issues, please contact <a href="mailto:tech@hiperforms.com">tech@hiperforms.com</a>.</li>
	<li>For questions regarding the data itself, or to request changes to read-only fields, contact <a
				href="mailto:webform@hiperforms.com">webform@hiperforms.com</a>.
	</li>
</ul>

<form id="mainForm"  action="Profile.php" method="post" enctype="multipart/form-data">

	<fieldset class="group">
		<legend>Confirm and Update Your Information</legend>

		<div class="input" style="border-top: none;">
			<label for="Name">Legal Name*</label>
			<div class="rightcolumn">
				<input name="firstName" type="text" size="20" placeholder="First" id="Name"
						 title="Your first name (required)" <?php recallText((empty($firstName) ? "" : $firstName), "yes"); ?> />
				<input name="middleName" type="text" size="20" placeholder="Middle" id="Name"
						 title="Your middle name (optional)" <?php recallText((empty($middleName) ? "" : $middleName), "no"); ?> />
				<input name="lastName" type="text" size="20" placeholder="Last" id="Name"
						 title="Your last name (required)" <?php recallText((empty($lastName) ? "" : $lastName), "yes"); ?> />
			</div>
		</div>

		<div class="input">
			<label for="NickName">Preferred First Name
				<small>(if different)</small>
			</label>
			<input name="nickName" type="text" size="20" id="NickName"
					 title="The name you prefer to be called." <?php recallText((empty($nickName) ? "" : $nickName), "no"); ?> />
		</div>
		
		<?php
		if ($includeFacePhoto != "Hidden") {
			?>

			<div class="input">
				<label for="slim-FacePhoto" id="FacePhoto_Button">Face Photo <img
							src="../include/info.PNG" height="16">
					<?php
					if ($includeFacePhoto == "Mandatory") {
						echo "<span class='";
						if (empty($Photo64) && empty($FacePhotoCropPath)) {
							echo "mandatoryFailed";
						} else {
							echo "mandatory";
						}
						echo "'>REQUIRED</span>";
					}
					?>
				</label>

				<div id="FacePhoto_Dialog" title="Face Photo" class="hidden">
					<p>This is a head-and-shoulders photo used for identification purposes by coaches and scouts.</p>
					<div>
						<label for="Good1">Good Example:</label>
						<img src="../include/GoodFacePhoto1.JPG" alt="Good Example" id="Good1">
					</div>
					<div>
						<label for="Bad1">Bad Example 1:</label>
						<img src="../include/BadFacePhoto1.JPG" alt="Bad Example" id="Bad1">
					</div>
					<div>
						<label for="Bad2">Bad Example 2:</label>
						<img src="../include/BadFacePhoto2.JPG" alt="Bad Example" id="Bad2">
					</div>
				</div>

				<div class="rightcolumn imgpreview">

					<div class="slim"
						  id="slim-FacePhoto"
						  data-ratio="1:1"
						  data-instant-edit="true"
						  data-download="true"
						  data-fetcher="../fetch.php">
						<input type="file" name="slim_face[]"/>
						<img src="<?php echo $FacePhotoEditor; ?>" alt="">
					</div>
					<div class="row">
						<span style="font-style: italic;">Select the image above, or drag an image over, to upload a new image.</span>
					</div>

				</div>

			</div>
			
			<?php
		}
		?>

		<div class="input">
			<label for="DOBDate">DOB*</label>
			<script>
             $(function () {
                 $("#DOBDate").datepicker({
                     changeMonth: true,
                     changeYear: true,
                     yearRange: "-60:-4"
                 });
             });
			</script>
			<input type="text" name="DOB" id="DOBDate" title="Your Date of Birth"
				<?php if (empty($DOB) || $DOB == date('m/d/Y')) {
					echo 'class="missing"';
				} else {
					echo 'value="' . $DOBsave . '"';
				} ?>/>
		</div>
		
		<?php if ($CampRole != "Admin/Manager" && $CampRole != "Other") { ?>
			<div class="input">
				<label for="crop-ProofOfDOB">Proof of Date of Birth<br/>
					<span style="font-style: italic; font-size: small">(Birth Certificate or Gov. Issued ID)</span>
				</label>

				<div class="rightcolumn imgpreview">

					<div class="slim"
						  id="slim-ProofOfDOB"
						  data-instant-edit="true"
						  data-download="true"
						  data-fetcher="../fetch.php">
						<input type="file" name="slim_DOB[]"/>
						<img src="<?php echo $ProofOfDOBEditor; ?>" alt="">
					</div>
					<div class="row">
						<span style="font-style: italic;">Select the image above, or drag an image over, to upload a new image.</span>
					</div>
				</div>

			</div>
		<?php } ?>

		<div class="input">
			<label for="Gender">Gender*</label>
			<div class="rightcolumn <?php if (empty($gender)) {
				echo ' missing';
			} ?>">
				<input name="gender" type="radio" value="Male" id="GenderMale" class="radio"
						 title="Male" <?php if (!empty($gender) and $gender == "Male") {
					echo 'checked="checked"';
				} ?> />
				<label for="GenderMale" class="radio">Male</label>
				<input name="gender" type="radio" value="Female" id="GenderFemale" class="radio"
						 title="Female" <?php if (!empty($gender) and $gender == "Female") {
					echo 'checked="checked"';
				} ?> />
				<label for="GenderFemale" class="radio">Female</label>
			</div>
		</div>
		
		<?php
		if (($includeDominantHandFoot != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label for="DominantHand">Dominant Hand
					<?php if ($includeDominantHandFoot == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<select name="dominantHand" size="1" id="DominantHand" title="Your dominant hand."
					<?php if (empty($dominantHand) && $includeDominantHandFoot == "Mandatory") {
						echo 'class="missing"';
					} ?>>
					<option value="">&nbsp;</option>
					<option value="Left" <?php if (!empty($dominantHand) and $dominantHand == "Left") {
						echo 'selected="selected"';
					} ?> >Left
					</option>
					<option value="Right" <?php if (!empty($dominantHand) and $dominantHand == "Right") {
						echo 'selected="selected"';
					} ?> >Right
					</option>
					<option value="Both" <?php if (!empty($dominantHand) and $dominantHand == "Both") {
						echo 'selected="selected"';
					} ?> >Both
					</option>
				</select>
			</div>
			<div class="input">
				<label for="DominantFoot">Dominant Foot
					<?php if ($includeDominantHandFoot == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<select name="dominantFoot" size="1" id="DominantFoot" title="Your dominant foot."
					<?php if (empty($dominantFoot) && $includeDominantHandFoot == "Mandatory") {
						echo 'class="missing"';
					} ?>>
					<option value="">&nbsp;</option>
					<option value="Left" <?php if (!empty($dominantFoot) and $dominantFoot == "Left") {
						echo 'selected="selected"';
					} ?> >Left
					</option>
					<option value="Right" <?php if (!empty($dominantFoot) and $dominantFoot == "Right") {
						echo 'selected="selected"';
					} ?> >Right
					</option>
					<option value="Both" <?php if (!empty($dominantFoot) and $dominantFoot == "Both") {
						echo 'selected="selected"';
					} ?> >Both
					</option>
				</select>
			</div>
			<?php
		}
		?>
		
		<?php
		if (($includeHeightWeight != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label for="HeightFeet">Height
					<?php if ($includeHeightWeight == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="heightFeet" type="text" id="HeightFeet" placeholder="Feet" class="set-width-5"
					<?php recallText((empty($heightFeet) ? "" : $heightFeet), ($includeHeightWeight == "Mandatory" ? "yes" : "no")); ?> />
				<input name="heightInches" type="text" id="HeightInches" placeholder="Inches" class="set-width-5"
					<?php recallText((isset($heightInches) ? $heightInches : ""), ($includeHeightWeight == "Mandatory" ? "yes" : "no")); ?> />
				<input name="heightMeters" type="text" id="HeightMeters" placeholder="Meters" class="set-width-5"
					<?php recallText((isset($heightMeters) ? $heightMeters : ""), ($includeHeightWeight == "Mandatory" ? "yes" : "no")); ?> />
				<select name="height_UM" size="1" id="Height_UM" title="Height Unit of Measurement">
					<option value="ft" <?php if ($height_UM != "m") {
						echo 'selected="selected"';
					} ?>>ft
					</option>
					<option value="m" <?php if ($height_UM == "m") {
						echo 'selected="selected"';
					} ?>>m
					</option>
				</select>
			</div>

			<div class="input">
				<label for="Weight">Weight
					<?php if ($includeHeightWeight == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="weight" type="text" id="Weight" class="set-width-5"
					<?php recallText((empty($weight) ? "" : $weight), ($includeHeightWeight == "Mandatory" ? "yes" : "no")); ?> />
				<select name="weight_UM" size="1" id="Weight_UM" title="Weight Unit of Measurement">
					<option value="lb" <?php if ($weight_UM != "kg") {
						echo 'selected="selected"';
					} ?>>lb
					</option>
					<option value="kg" <?php if ($weight_UM == "kg") {
						echo 'selected="selected"';
					} ?>>kg
					</option>
				</select>
			</div>
			<?php
		}
		?>
		
		<?php
		if ($includeKit != "Hidden") {
			?>
			<div class="input">
				<label for="Kit">Clothing Sizes
					<?php if ($includeKit == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<div class="rightcolumn">
					<table style="width: 100%; max-width: 800px; padding: 0; margin: 0;">
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field" style="width: 6em;">
									<legend>Match Jersey</legend>
									<select name="MatchJerseySize" size="1"
											  id="Kit" <?php if (empty($MatchJerseySize) && $includeKit == "Mandatory") {
										$MatchJerseySize_a = " ";
										echo 'class="missing"';
									} else {
										$MatchJerseySize_a = $MatchJerseySize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($MatchJerseySize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field" style="width: 7em;">
									<legend>Match Shorts</legend>
									<select name="MatchShortsSize" size="1"
											  id="Kit" <?php if (empty($MatchShortsSize) && $includeKit == "Mandatory") {
										$MatchShortsSize_a = " ";
										echo 'class="missing"';
									} else {
										$MatchShortsSize_a = $MatchShortsSize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($MatchShortsSize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>T-Shirt</legend>
									<select name="tShirtSize" size="1" id="Kit" <?php if (empty($tShirtSize) && $includeKit == "Mandatory") {
										$tShirtSize_a = " ";
										echo 'class="missing"';
									} else {
										$tShirtSize_a = $tShirtSize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($tShirtSize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>Polo</legend>
									<select name="poloSize" size="1" id="Kit" <?php if (empty($poloSize) && $includeKit == "Mandatory") {
										$poloSize_a = " ";
										echo 'class="missing"';
									} else {
										$poloSize_a = $poloSize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($poloSize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
						</tr>
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Gym Shorts</legend>
									<select name="shortsSize" size="1" id="Kit" <?php if (empty($shortsSize) && $includeKit == "Mandatory") {
										$shortsSize_a = " ";
										echo 'class="missing"';
									} else {
										$shortsSize_a = $shortsSize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($shortsSize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td style="padding: 0;">
								<fieldset class="field" style="width: 8em;">
									<legend>Track Suit Bottom</legend>
									<select name="trackSuitBottomSize" size="1"
											  id="Kit" <?php if (empty($trackSuitBottomSize) && $includeKit == "Mandatory") {
										$trackSuitBottomSize_a = " ";
										echo 'class="missing"';
									} else {
										$trackSuitBottomSize_a = $trackSuitBottomSize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($trackSuitBottomSize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td style="padding: 0;">
								<fieldset class="field" style="width: 7em;">
									<legend>Track Suit Top</legend>
									<select name="trackSuitTopSize" size="1"
											  id="Kit" <?php if (empty($trackSuitTopSize) && $includeKit == "Mandatory") {
										$trackSuitTopSize_a = " ";
										echo 'class="missing"';
									} else {
										$trackSuitTopSize_a = $trackSuitTopSize;
									} ?> >
										<option value="">&nbsp;</option>
										<?php
										foreach ($clothingSizeValues as $value) {
											echo "<option value=\"" . $value . "\" " . ($trackSuitTopSize_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td style="width: auto;"></td>
						</tr>
					</table>
				</div>
			</div>
			<?php
		}
		?>
		
		<?php
		if (($includeGradeLevel != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label for="GradeLevel">
					<?php
					echo $season . " Grade Level";
					if ($includeGradeLevel == "Mandatory") {
						echo "*";
					}
					?>
				</label>
				<input name="CurrentSchoolGradeLevel" type="text" style="width: 4em;" id="GradeLevel"
					<?php recallText((empty($CurrentSchoolGradeLevel) ? "" : $CurrentSchoolGradeLevel), ($includeGradeLevel == "Mandatory" ? "yes" : "no")); ?> />
			</div>
			<?php
		}
		?>

		<div class="input">
			<label for="Address">Home Address*</label>
			<div class="rightcolumn">
				<input name="homeAddress1" type="text" id="Address" placeholder="Street 1"
						 size="40" <?php recallText((empty($homeAddress1) ? "" : $homeAddress1), "yes"); ?> />
				<br/>
				<input name="homeAddress2" type="text" id="Address" placeholder="Street 2"
						 size="40" <?php recallText((empty($homeAddress2) ? "" : $homeAddress2), "no"); ?> />
				<br/>
				<input name="City" type="text" id="Address" placeholder="City"
						 size="30" <?php recallText((empty($City) ? "" : $City), "yes"); ?> />
				<select name="State" size="1" id="Address" <?php if (empty($State)) {
					$State_a = " ";
					echo 'class="missing"';
				} else {
					$State_a = $State;
				} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($stateValues as $value) {
						echo "<option value='" . $value . "' " . ($State_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
					}
					?>
				</select>
				<input name="zipCode" type="text" id="Address" placeholder="Postal Code"
						 size="10" <?php recallText((empty($zipCode) ? "" : $zipCode), "yes"); ?> />
			</div>
		</div>

		<div class="input">
			<label for="Country">Country*</label>
			<select name="Country" size="1" id="Country" class="select2" <?php if (empty($Country)) {
				$Country_a = " ";
				echo 'class="missing"';
			} else {
				$Country_a = $Country;
			} ?> >
				<option value="">&nbsp;</option>
				<?php
				foreach ($countryValues as $value) {
					echo "<option value='" . $value . "' " . ($Country_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
				}
				?>
			</select>
		</div>

		<div class="input">
			<label for="eMail">E-Mail*</label>
			<input name="eMail" type="email" size="40" id="eMail"
					 title="E-mail address that USA Rugby can use to send you Invites." <?php recallText((empty($eMail) ? "" : $eMail), "yes"); ?> />
		</div>
		
		<?php
		if ($includeCellNumber != "Hidden") {
			?>
			<div class="input">
				<label for="PrimaryPhoneNumber" id="Phone_Button">Primary Phone Number<br/>
					<small><i>(Cell Preferred)</i></small>
					<img src="../include/info.PNG" height="16">
					<?php if ($includeCellNumber == "Mandatory") {
						echo "*";
					} ?></label>

				<div id="Phone_Dialog" title="Phone Numbers">
					<p>Event and Camp administrators and coaches may use HiPer to send out notifications via text message.
						Therefore, the most important phone number is one that can receive text messages.</p>
					<p>Phone numbers are formatted after being submitted, so they can be entered as 5555555555.</p>
				</div>

				<div class="rightcolumn">
					<fieldset class="field">
						<input name="PrimaryPhoneNumber" type="text" size="25" id="PrimaryPhoneNumber" placeholder="Phone Number"
							<?php recallText((empty($PrimaryPhoneNumber) ? "" : $PrimaryPhoneNumber), "yes"); ?>
						/>
					</fieldset>
					<fieldset class="field">
						<input name="PrimaryPhoneText_flag" type="checkbox" value="1" id="PrimaryPhoneText" class="radio"
							<?php if ($PrimaryPhoneText_flag == 1) {
								echo " checked='checked'";
							} ?> />
						<label for="PrimaryPhoneText" class="radio">This Phone is capable of receiving text messages.</label>
					</fieldset>
				</div>

			</div>
			<?php
		}
		?>
		
		<?php
		if (($includeInsurance != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label for="Insurance">Health Insurance Company
					<?php if ($includeInsurance == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="healthInsuranceCompany" type="text" id="Insurance"
						 size="40" <?php recallText((empty($healthInsuranceCompany) ? "" : $healthInsuranceCompany), ($includeInsurance == "Mandatory" ? "yes" : "no")); ?> />
			</div>
			<div class="input">
				<label for="Plan">Health Plan ID
					<?php if ($includeInsurance == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<input name="healthPlanID" type="text" id="Plan"
						 size="16" <?php recallText((empty($healthPlanID) ? "" : $healthPlanID), ($includeInsurance == "Mandatory" ? "yes" : "no")); ?> />
			</div>

			<div class="input">
				<label for="slim-InsuranceCard">Insurance Card<br/>
					<span style="font-style: italic; font-size: small">Required for International Travel</span>
				</label>

				<div class="rightcolumn imgpreview">

					<div class="slim"
						  id="slim-InsuranceCard"
						  data-instant-edit="true"
						  data-download="true"
						  data-fetcher="../fetch.php">
						<input type="file" name="slim_insurance[]"/>
						<img src="<?php echo $InsuranceCardEditor; ?>" alt="">
					</div>
					<div class="row">
						<span style="font-style: italic;">Select the image above, or drag an image over, to upload a new image.</span>
					</div>

				</div>
			</div>
			<?php
		}
		?>
		
		<?php
		if (($includeConditions != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label class="top" for="Conditions">Do you have any allergies, dietary restrictions, chronic illnesses, or medical conditions?
					If yes, please describe.
					<?php if ($includeConditions == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<div <?php if (empty($allergiesConditions) && $includeConditions == "Mandatory") {
					echo 'class="missing"';
				} ?> >
					<input name="allergiesConditions" type="radio" id="ConditionsYes" class="radio"
							 value="Yes" <?php if (!empty($allergiesConditions) && $allergiesConditions == "Yes") {
						echo 'checked="checked"';
					} ?> />
					<label for="ConditionsYes" class="radio">Yes</label>
					<input name="allergiesConditions" class="radio" type="radio" id="ConditionsNo" class="radio"
							 value="No" <?php if (!empty($allergiesConditions) && $allergiesConditions == "No") {
						echo 'checked="checked"';
					} ?> />
					<label for="ConditionsNo" class="radio">No</label>
				</div>
				<input name="allergiesConditionsDescr" type="text" size="70" id="Conditions"
					<?php
					if ($allergiesConditions == "Yes" && $includeConditions == "Mandatory") {
						recallText((empty($allergiesConditionsDescr) ? "" : $allergiesConditionsDescr), "yes");
					} elseif ($allergiesConditions == "No") {
						recallText((empty($allergiesConditionsDescr) ? "" : $allergiesConditionsDescr), "no");
					}
					?> />
			</div>
			<?php
		}
		?>
		
		<?php
		if (($includeMedications != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>

			<div class="input">
				<label class="top" for="Medications">Are you prescribed any medication? If yes, please explain any instructions.
					<?php if ($includeMedications == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<div <?php if (empty($medications) && $includeConditions == "Mandatory") {
					echo 'class="missing"';
				} ?> >
					<input name="medications" type="radio" id="MedicationsYes" value="Yes" class="radio"
						<?php if (!empty($medications) and $medications == "Yes") {
							echo 'checked="checked"';
						} ?> />
					<label for="MedicationsYes" class="radio">Yes</label>
					<input name="medications" class="radio" type="radio" id="MedicationsNo" value="No" class="radio"
						<?php if (!empty($medications) and $medications == "No") {
							echo 'checked="checked"';
						} ?> />
					<label for="MedicationsNo" class="radio">No</label>
				</div>
				<input name="medicationsDescr" type="text" size="70" id="Medications"
					<?php
					if ($medications == "Yes" && $includeMedications == "Mandatory") {
						recallText((empty($medicationsDescr) ? "" : $medicationsDescr), "yes");
					} elseif ($medications == "No") {
						recallText((empty($medicationsDescr) ? "" : $medicationsDescr), "no");
					}
					?> />
			</div>

			<div class="input">
				<label>
					Are you currently taking one of the <a
							href="https://www.wada-ama.org/sites/default/files/resources/files/2016-09-29_-_wada_prohibited_list_2017_eng_final.pdf"
							target="_blank">currently listed</a> banned substances?
					<?php
					echo "<span class='";
					if (empty($TakingBannedSubstance) || ($TakingBannedSubstance == "Yes" && empty($BannedSubstanceDescription))) {
						echo "mandatoryFailed";
					} else {
						echo "mandatory";
					}
					echo "'>REQUIRED</span>";
					?>
				</label>

				<div class="rightcolumn">
					<select name="TakingBannedSubstance" id="BannedSubstance" size="1" title="Currently Taking a Banned Substance?">
						<option value=""></option>
						<option value="Yes" <?php if ($TakingBannedSubstance == "Yes") {
							echo 'selected="selected"';
						} ?>>Yes
						</option>
						<option value="No" <?php if ($TakingBannedSubstance == "No") {
							echo 'selected="selected"';
						} ?>>No
						</option>
					</select>

					<div class="BannedSubstanceFields">
						<input name="BannedSubstanceViaPrescription" type="checkbox" value="1" id="BannedSubstanceViaPrescription"
								 class="radio" <?php if ($BannedSubstanceViaPrescription == 1) {
							echo "checked='checked'";
						} ?> />
						<label for="BannedSubstanceViaPrescription" class="radio">Via Prescription</label>

						<div class="row">
							<fieldset class="field">
								<legend>Banned Substance Description</legend>
								<input name="BannedSubstanceDescription" type="text" size="60" title="Banned Substance Description"
									<?php recallText($BannedSubstanceDescription, "no") ?> />
							</fieldset>
						</div>
					</div>

				</div>
			</div>
			
			<?php
		}
		?>
	</fieldset>

	<fieldset class="group">
		<legend>Rugby and Travel Information</legend>
		
		<?php
		if ($includeMembershipID != "Hidden") {
			?>
			<div class="input" style="border-top: none;">
				<label for="MembershipID">USA Rugby Membership ID
					<?php if ($includeMembershipID == "Mandatory") {
						echo "*";
					} ?><br/>
					<span style="font-size: smaller"> Look up your Membership ID at <a href="http://usarugby.org/rosters">http://usarugby.org/rosters</a></span>
				</label>
				<input name="MembershipID" type="text" size="16" id="MembershipID"
						 title="The Membership ID you received when you registered at USA Rugby."
					<?php recallText((empty($MembershipID) ? "" : $MembershipID), ($includeMembershipID == "Mandatory" ? "yes" : "no")); ?> />
			</div>
			<?php
		}
		?>

		<div class="input">
			<label for="PrimaryClub">Primary Club*</label>
			<div id="YesClubFields" class="rightcolumn">
				<div style="display: inline-block;"
					  class="<?php if (empty($ID_Club) && empty($UnlistedClub_flag) && empty($DoNotBelongToAClub_flag)) {
						  $ID_Club_a = " ";
						  echo 'missing';
					  } else {
						  $ID_Club_a = $ID_Club;
					  } ?>">
					<select name="ID_Club" size="1" class="select2" id="PrimaryClub"
							  title="The Primary Club you play for.">
						<option value="">&nbsp;</option>
						<?php
						foreach ($clubValues as $key => $clubValue) {
							echo "<option value='" . $key . "' " . ($ID_Club_a == $key ? "selected='selected'>" : ">") . $clubValue . "</option>";
						}
						?>
					</select>
				</div>
			</div>

			<div class="rightcolumn" style="margin-top: 1em;">
				<input name="OtherClub" type="radio" value="UnlistedClub" class="radio" id="UnlistedClubRadio"
						 title="Select this if your club is not in the drop-down list."
					<?php echo $UnlistedClub_flag == 1 ? "checked='checked'" : ""; ?>
				/>
				<label class="radio" for="UnlistedClubRadio">My Club Is Not Listed</label>
				<input name="OtherClub" type="radio" value="NoClub" class="radio" id="NoClubRadio"
						 title="Select this if you do not belong to a club at the time."
					<?php echo $DoNotBelongToAClub_flag == 1 ? "checked='checked'" : ""; ?>
				/>
				<label class="radio" for="NoClubRadio">I Do Not Currently Belong To A Club</label>
				<div class="hidden" id="YesClub" style="display: inline-block;">
					<input name="OtherClub" type="radio" value="YesClub" class="radio" id="YesClubRadio" title="View the Club list"/>
					<label class="radio" for="YesClubRadio">View the Club List</label></div>
			</div>

			<div class="row hidden rightcolumn" style="border: 1px solid #1b6d85; padding: 4px;" id="UnlistedClubFields">
				<fieldset class="field">
					<legend>Unlisted Club Name</legend>
					<input name="UnlistedClub_Name" type="text" title="Unlisted Club Name"
							 size="50" <?php recallText((empty($UnlistedClub_Name) ? "" : $UnlistedClub_Name), "no"); ?> />
				</fieldset>
				<fieldset class="field">
					<legend>Club City</legend>
					<input name="UnlistedClub_City" type="text" title="Unlisted Club City"
							 size="24" <?php recallText((empty($UnlistedClub_City) ? "" : $UnlistedClub_City), "no"); ?> />
				</fieldset>
				<fieldset class="field">
					<legend>Club State / Country</legend>
					<input name="UnlistedClub_State" type="text" title="Unlisted Club State or Country"
							 size="24" <?php recallText((empty($UnlistedClub_State) ? "" : $UnlistedClub_State), "no"); ?> />
				</fieldset>
			</div>
		</div>
		
		<?php
		if (($includeNationalEligible != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label class="top" for="Eligible">Are you eligible for USA Rugby National Team? If no, please explain.
					<?php if ($includeNationalEligible == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<div <?php if (empty($nationalLevelEligible) && $includeNationalEligible == "Mandatory") {
					echo 'class="missing"';
				} ?> >
					<input name="nationalLevelEligible" type="radio" id="Eligible" value="Yes" class="radio"
						<?php if (!empty($nationalLevelEligible) and $nationalLevelEligible == "Yes") {
							echo 'checked="checked"';
						} ?> />
					<label for="Eligible" class="radio">Yes</label>
					<input name="nationalLevelEligible" class="radio" type="radio" id="EligibleNo" value="No"
						<?php if (!empty($nationalLevelEligible) and $nationalLevelEligible == "No") {
							echo 'checked="checked"';
						} ?> />
					<label for="EligibleNo" class="radio">No</label>
				</div>
				<input name="nationalLevelEligibleExplain" type="text" size="70" id="Eligible"
					<?php
					if ($nationalLevelEligible == "No" && $includeNationalEligible == "Mandatory") {
						recallText((empty($nationalLevelEligibleExplain) ? "" : $nationalLevelEligibleExplain), "yes");
					} elseif ($nationalLevelEligible == "Yes") {
						recallText((empty($nationalLevelEligibleExplain) ? "" : $nationalLevelEligibleExplain), "no");
					}
					?> />
			</div>
			<?php
		}
		?>
		
		<?php
		if (($includePositionFields != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label for="Primary15s">Primary 15's Position
					<?php if ($includePositionFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<select name="primary15sPosition" size="1" id="Primary15s"
					<?php if (empty($primary15sPosition) && $includePositionFields == "Mandatory") {
						$primary15sPosition_a = " ";
						echo 'class="missing"';
					} else if (empty($primary15sPosition)) {
						$primary15sPosition_a = " ";
					} else {
						$primary15sPosition_a = $primary15sPosition;
					} ?>
				>
					<option value="">&nbsp;</option>
					<?php $fifteensValues = $layout->getValueListTwoFields('PHP15sPositions');
					foreach ($fifteensValues as $value) {
						echo "<option value='" . $value . "' " . ($primary15sPosition_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?></select>
			</div>
			<div class="input">
				<label for="Primary7s">Primary 7's Position
					<?php if ($includePositionFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<select name="primary7sPosition" size="1" id="Primary7s"
					<?php if (empty($primary7sPosition)) {
						$primary7sPosition_a = " ";
					} else {
						$primary7sPosition_a = $primary7sPosition;
					} ?>
				>
					<option value="">&nbsp;</option>
					<?php $sevensValues = $layout->getValueListTwoFields('PHP7sPositions');
					foreach ($sevensValues as $value) {
						echo "<option value='" . $value . "' " . ($primary7sPosition_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?></select>
			</div>
			<?php
		}
		?>
		
		<?php
		if ($CampRole == "Player" || !empty($IDType)) {
			if ($includeStartedPlayingFields != "Hidden") {
				?>

				<div class="input">
					<label for="YearStarted">When Did You Start Playing Rugby?
						<?php if ($includeStartedPlayingFields == "Mandatory") {
							echo '*';
						} ?>
					</label>
					
					<?php $required = $includeStartedPlayingFields == "Mandatory" ? "yes" : "no"; ?>

					<div class="rightcolumn">
						<fieldset class="field" style="width: 4em;">
							<legend>Year</legend>
							<input name="yearStartedPlaying" type="text" size="6" id="YearStarted"
									 title="The year you started playing rugby." <?php recallText((empty($yearStartedPlaying) ? "" : $yearStartedPlaying), $required); ?> />
						</fieldset>
						<fieldset class="field" style="width: 4em;">
							<legend>Month</legend>
							<select name="monthStartedPlaying" size="1" id="MonthStarted" title="Month">
								<option value="">&nbsp;</option>
								<?php
								for ($i = 1; $i < 13; $i++) {
									echo "<option value='" . $i . "' " . ($monthStartedPlaying == $i ? "selected='selected'>" : ">") . $i . "</option>";
								}
								?>
							</select>
						</fieldset>
					</div>

				</div>
			
			<?php } ?>

			<div class="input">
				<label for="Video1">Highlight Video Link</label>
				<input name="HighlightVideoLink" type="text" size="70" id="Video1"
					<?php recallText((empty($HighlightVideoLink) ? "" : $HighlightVideoLink), "no"); ?> />
			</div>

			<div class="input">
				<label for="Video2">Full Match Link 1</label>
				<input name="FullMatchLink1" type="text" size="70" id="Video2"
					<?php recallText((empty($FullMatchLink1) ? "" : $FullMatchLink1), "no"); ?> />
			</div>

			<div class="input">
				<label for="Video3">Full Match Link 2</label>
				<input name="FullMatchLink2" type="text" size="70" id="Video3"
					<?php recallText((empty($FullMatchLink2) ? "" : $FullMatchLink2), "no"); ?> />
			</div>

			<div class="input">
				<label for="Video4">Full Match Link 3</label>
				<input name="FullMatchLink3" type="text" size="70" id="Video4"
					<?php recallText((empty($FullMatchLink3) ? "" : $FullMatchLink3), "no"); ?> />
			</div>
			
			<?php
			if ($includeOtherSportsFields != "Hidden") {
				?>

				<div class="input">
					<label>Other Sport Experiences</label>
					<div class="rightcolumn">
						<label class="top">Add a new record</label>

						<div class="row">
							<fieldset class="field" style="width: 9em; margin-right: .5em;">
								<legend>Sport*</legend>
								<select name="OtherSport" id="OtherSport" size="1" title="The sport you have prior experience with.">
									<option value="">&nbsp;</option>
									<?php
									foreach ($othersportsValues as $value) {
										echo "<option value='" . $value . "' " . ($OtherSport == $value ? "selected='selected'>" : ">") . $value . "</option>";
									}
									?>
								</select>
							</fieldset>

							<fieldset class="field" style="width: 12em; margin-right: .5em;">
								<legend>Date Started*</legend>
								<input class="Date-80-1 datepicker" type="text" name="OtherSportDateStart" id="OtherSportDateStart"
										 title="The date you started playing the sport."
									<?php if (empty($OtherSportDateStart) || $OtherSportDateStart == date('m/d/Y')) {
									} else {
										echo "value=$OtherSportDateStartsave";
									} ?> />
							</fieldset>

							<fieldset class="field" style="width: 10em;">
								<legend>Date Ended</legend>
								<input class="Date-80-1 datepicker" type="text" name="OtherSportDateEnd" id="OtherSportDateEnd"
										 title="The date you finished playing the sport."
									<?php if (empty($OtherSportDateEnd) || $OtherSportDateEnd == date('m/d/Y')) {
									} else {
										echo "value=$OtherSportDateEndsave";
									} ?> />
							</fieldset>
						</div>

						<div class="row">
							<fieldset class="field" style="width: 100%;">
								<legend>Description</legend>
								<textarea name="OtherSportDescription" title="Description of your experience" style="width: 98%;" form="mainForm"
											 rows="2" maxlength="1000"></textarea>
							</fieldset>
						</div>

					</div>
					
					<?php
					if ($related_othersports_count > 0) {
						?>

						<div class="rightcolumn">
							<label class="top">Existing Records</label>
							
							<?php
							foreach ($related_othersports as $othersport_record) {
								$OtherSport_RecordID = $othersport_record->getRecordID();
								$OtherSport_Sport = empty($othersport_record->getField('EventPersonnel__OtherSports::Sport')) ? '-' : $othersport_record->getField('EventPersonnel__OtherSports::Sport');
								$OtherSport_DateStarted = empty($othersport_record->getField('EventPersonnel__OtherSports::DateStarted')) ? '-' : $othersport_record->getField('EventPersonnel__OtherSports::DateStarted');
								$OtherSport_DateEnded = empty($othersport_record->getField('EventPersonnel__OtherSports::DateEnded')) ? '-' : $othersport_record->getField('EventPersonnel__OtherSports::DateEnded');
								$OtherSport_Description = empty($othersport_record->getField('EventPersonnel__OtherSports::Description')) ? '-' : $othersport_record->getField('EventPersonnel__OtherSports::Description');
								?>

								<div class='row row-divider row-divider-color'>
									<fieldset class='field' style='width: 14%'>
										<legend>Sport</legend>
										<?php echo $OtherSport_Sport; ?>
									</fieldset>
									<fieldset class='field' style='width: 20%'>
										<legend>Date Started</legend>
										<?php echo $OtherSport_DateStarted; ?>
									</fieldset>
									<fieldset class='field' style='width: 20%'>
										<legend>Date Ended</legend>
										<?php echo $OtherSport_DateEnded; ?>
									</fieldset>
									<fieldset class='field' style='width: 10%'>
										<legend>Delete</legend>
										<input class='alpha50' name='OtherSport_Delete[<?php echo $OtherSport_RecordID; ?>]' type='checkbox' value='1'
												 title='Select this to delete the record'/>
									</fieldset>

									<div class="row">
										<fieldset class='field' style='width: 97%'>
											<legend>Description</legend>
											<?php echo $OtherSport_Description; ?>
										</fieldset>
									</div>
								</div>
								
								<?php
							}
							?>

						</div>
						
						<?php
					}
					?>
				</div>
				
				<?php
			}
		}
		?>
		
		<?php
		if ($includePassportFields != "Hidden") {
			?>
			<div class="input">
				<label for="ValidPassport">Valid Passport with a Minimum of 6 months before expiration?
					<?php if ($includePassportFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<div class="rightcolumn <?php if (empty($passportHolder) && $includePassportFields == "Mandatory") {
					echo ' missing';
				} ?>">
					<input name="passportHolder" type="radio" value="Yes" id="ValidPassportYes" class="radio"
							 title="Yes" <?php if (!empty($passportHolder) and $passportHolder == "Yes") {
						echo 'checked="checked"';
					} ?> />
					<label for="ValidPassportYes" class="radio">Yes</label>
					<input name="passportHolder" type="radio" value="No" id="ValidPassportNo" class="radio"
							 title="No" <?php if (!empty($passportHolder) and $passportHolder == "No") {
						echo 'checked="checked"';
					} ?> />
					<label for="ValidPassportNo" class="radio">No</label>
				</div>
			</div>
			<div class="input">
				<label for="PassportNumber">Passport Number
					<?php if ($includePassportFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<input name="passportNumber" type="text" size="16" title="Your passport number." id="PassportNumber"
					<?php recallText((empty($passportNumber) ? "" : $passportNumber), ($includePassportFields == "Mandatory" ? "yes" : "no")); ?> />
			</div>
			<div class="input">
				<label for="PassportName">Name on Passport
					<?php if ($includePassportFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<input name="nameOnPassport" type="text" size="30" title="Your name as printed in your passport." id="PassportName"
					<?php recallText((empty($nameOnPassport) ? "" : $nameOnPassport), ($includePassportFields == "Mandatory" ? "yes" : "no")); ?> />
			</div>
			<div class="input">
				<label for="passportDate">Passport Expiration
					<?php if ($includePassportFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<script>
                $(function () {
                    $("#passportDate").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-10:+20"
                    });
                });
				</script>
				<input type="text" name="passportExpiration" id="passportDate" title="The date your passport expires." style="z-index: 1001"
					<?php if ((empty($passportExpiration) || $passportExpiration == date('m/d/Y')) && $includePassportFields == "Mandatory") {
						echo 'class="missing"';
					} else {
						echo 'value="' . $passportExpirationsave . '"';
					} ?> />
			</div>
			<div class="input">
				<label for="PassportCountry">Issuing Country
					<?php if ($includePassportFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<select name="passportIssuingCountry" size="1" id="PassportCountry"
						  class="select2" <?php if (empty($passportIssuingCountry)) {
					$passportIssuingCountry_a = " ";
					echo 'class="missing"';
				} else {
					$passportIssuingCountry_a = $passportIssuingCountry;
				} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "' " . ($passportIssuingCountry_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
			<div class="input">
				<label for="Citizen1">Country of Citizenship 1
					<?php if ($includePassportFields == "Mandatory") {
						echo '*';
					} ?>
				</label>
				<select name="Citizen1" size="1" id="Citizen1" class="select2"
					<?php if (empty($Citizen1)) {
						$Citizen1_a = " ";
						if ($includePassportFields == "Mandatory") {
							echo 'class="missing"';
						}
					} else {
						$Citizen1_a = $Citizen1;
					} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "' " . ($Citizen1_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>
			<div class="input">
				<label for="Citizen2">Country of Citizenship 2</label>
				<select name="Citizen2" size="1" id="Citizen2" class="select2" <?php if (empty($Citizen2)) {
					$Citizen2_a = " ";
				} else {
					$Citizen2_a = $Citizen2;
				} ?> >
					<option value="">&nbsp;</option>
					<?php
					foreach ($countryValues as $value) {
						echo "<option value='" . $value . "' " . ($Citizen2_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
					}
					?>
				</select>
			</div>

			<div class="input">
				<label for="slim-Passport">Passport<br/>
					<span style="font-style: italic; font-size: small">Required for International Travel</span>
				</label>

				<div class="rightcolumn imgpreview">

					<div class="slim"
						  id="slim-Passport"
						  data-instant-edit="true"
						  data-download="true"
						  data-fetcher="../fetch.php">
						<input type="file" name="slim_passport[]"/>
						<img src="<?php echo $PassportEditor; ?>" alt="">
					</div>
					<div class="row">
						<span style="font-style: italic;">Select the image above, or drag an image over, to upload a new image.</span>
					</div>

				</div>
			</div>

			<div class="input">
				<label for="slim-OtherTravel">Other Travel Documentation<br/>
					<span style="font-style: italic; font-size: small">e.g., Visa or Green Card</span>
				</label>

				<div class="rightcolumn imgpreview">

					<div class="slim"
						  id="slim-OtherTravel"
						  data-instant-edit="true"
						  data-download="true"
						  data-fetcher="../fetch.php">
						<input type="file" name="slim_other[]"/>
						<img src="<?php echo $OtherTravelEditor; ?>" alt="">
					</div>
					<div class="row">
						<span style="font-style: italic;">Select the image above, or drag an image over, to upload a new image.</span>
					</div>

				</div>
			</div>

			<div class="input" style="border-top: none;">
				<label for="VisaDateIssued">Date Issued</label>
				<script>
                $(function () {
                    $("#VisaDateIssued").datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-30:+1"
                    });
                });
				</script>
				<input type="text" name="VisaDateIssued" id="VisaDateIssued" title="The date your Visa was issued."
					<?php
					echo 'value="' . $VisaDateIssued_save . '"';
					?> />
			</div>
			<?php
		}
		?>
		
		<?php
		if ($includeAirTravel != "Hidden") {
			?>
			<div class="input">
				<label for="airport">Primary Airport
					<?php if ($includeAirTravel == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<select name="ID_primaryAirport" size="1" id="airport" class="select2
				<?php if (empty($ID_primaryAirport)) {
					$ID_primaryAirport_a = " ";
					if ($includeAirTravel == "Mandatory") {
						echo " missing";
					}
				} else {
					$ID_primaryAirport_a = $ID_primaryAirport;
				} ?>">
					<option value="">&nbsp;</option>
					<?php
					foreach ($airportValues as $key => $airportValue) {
						echo "<option value=\"" . $key . "\"" . ($ID_primaryAirport_a == $key ? "selected=\"selected\">" : ">") . $airportValue . "</option>";
					}
					?>
				</select>
			</div>
			<div class="input">
				<label for="airport2">Secondary Airport</label>
				<select name="ID_secondaryAirport" size="1" id="airport2" class="select2
				<?php if (empty($ID_secondaryAirport)) {
					$ID_secondaryAirport_a = " ";
				} else {
					$ID_secondaryAirport_a = $ID_secondaryAirport;
				} ?>">
					<option value="">&nbsp;</option>
					<?php
					foreach ($airportValues as $key => $airportValue) {
						echo "<option value=\"" . $key . "\"" . ($ID_secondaryAirport_a == $key ? "selected=\"selected\">" : ">") . $airportValue . "</option>";
					}
					?>
				</select>
			</div>
			<div class="input">
				<label for="FrequentFlyer">Frequent Flyer Information</label>
				<input name="frequentFlyerInfo" type="text" id="FrequentFlyer" size="70"
					<?php recallText((empty($frequentFlyerInfo) ? "" : $frequentFlyerInfo), "no"); ?> />
			</div>
			<div class="input">
				<label for="TravelComments">Travel Comments</label>
				<input name="travelComments" type="text" size="70" id="TravelComments"
					<?php recallText((empty($travelComments) ? "" : $travelComments), "no"); ?> />
			</div>
			<?php
		}
		?>
	</fieldset>

	<fieldset class="group">
		<legend>Contact</legend>
		<div class="input" style="border-top: none;">
			<label for="EmergencyContact">Emergency Contact *</label>
			<div class="rightcolumn">
				<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
					<tr style="width: auto;">
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>First</legend>
								<input name="emergencyContactFirstName" type="text" size="16" id="EmergencyContact"
										 title="First Name of your emergency contact."
									<?php recallText((empty($emergencyContactFirstName) ? "" : $emergencyContactFirstName), "yes"); ?> />
							</fieldset>
						</td>
						<td style="padding: 0;">
							<fieldset class="field">
								<legend>Last</legend>
								<input name="emergencyContactLastName" type="text" size="16"
										 title="Last Name of your emergency contact."
									<?php recallText((empty($emergencyContactLastName) ? "" : $emergencyContactLastName), "yes"); ?> />
							</fieldset>
						</td>
					</tr>
					<tr style="width: auto;">
						<td style="padding: 4px 0 0 0;">
							<fieldset class="field">
								<legend>Phone</legend>
								<input name="emergencyContactNumber" type="text" size="16"
										 title="Phone number of your emergency contact."
									<?php recallText((empty($emergencyContactNumber) ? "" : $emergencyContactNumber), "yes"); ?> />
							</fieldset>
						</td>
						<td style="padding: 4px 0 0 0;">
							<fieldset class="field">
								<legend>Relationship</legend>
								<select name="emergencyContactRelationship" size="1" title="Relationship to your emergency contact."
									<?php if (empty($emergencyContactRelationship)) {
										$emergencyContactRelationship_a = " ";
										echo 'class="missing"';
									} else {
										$emergencyContactRelationship_a = $emergencyContactRelationship;
									} ?> >
									<option value="">&nbsp;</option>
									<?php
									foreach ($relationshipValues as $value) {
										echo "<option value=\"" . $value . "\"" . ($emergencyContactRelationship_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
									}
									?>
								</select>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
		<?php
		if ($includePartner != "Hidden") {
			?>
			<div class="input">
				<label for="Spouse">Spouse/Partner</label>
				<div class="rightcolumn">
					<input name="spouseName" type="text" size="16" id="Spouse" placeholder="Name"
						<?php recallText((empty($spouseName) ? "" : $spouseName), "no"); ?> />
					<input name="spouseCell" type="tel" size="16" placeholder="Cell Number"
						<?php recallText((empty($spouseCell) ? "" : $spouseCell), "no"); ?> />
					<input name="spouseEmail" type="email" size="40" placeholder="E-Mail"
						<?php recallText((empty($spouseEmail) ? "" : $spouseEmail), "no"); ?> />
				</div>
			</div>
			<?php
		}
		?>
		
		<?php
		if (($includeParent != "Hidden" && $CampRole == "Player") || !empty($IDType)) {
			?>
			<div class="input">
				<label for="Parent">Parent / Guardian 1
					<?php if ($includeParent == "Mandatory") {
						echo "*";
					} ?>
				</label>
				<div class="rightcolumn">
					<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Type</legend>
									<select name="Guardian1Type" size="1" id="Parent"
										<?php
										if (empty($Guardian1Type) && $includeParent == "Mandatory") {
											$Guardian1Type_a = " ";
											echo 'class="missing"';
										} else if (empty($Guardian1Type)) {
											$Guardian1Type_a = " ";
										} else {
											$Guardian1Type_a = $Guardian1Type;
										}
										?>>
										<option value="">&nbsp;</option>
										<?php
										foreach ($guardianValues as $value) {
											echo "<option value=\"" . $value . "\"" . ($Guardian1Type_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>First</legend>
									<input name="Guardian1FirstName" type="text" size="16" title="First name of your first guardian."
										<?php recallText((empty($Guardian1FirstName) ? "" : $Guardian1FirstName), ($includeParent == "Mandatory" ? "yes" : "no")); ?> />
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>Last</legend>
									<input name="Guardian1LastName" type="text" size="16" title="Last name of your first guardian."
										<?php recallText((empty($Guardian1LastName) ? "" : $Guardian1LastName), ($includeParent == "Mandatory" ? "yes" : "no")); ?> />
								</fieldset>
							</td>
						</tr>
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Phone</legend>
									<input name="Guardian1Cell" type="text" size="16" title="Phone number of your first guardian."
										<?php recallText((empty($Guardian1Cell) ? "" : $Guardian1Cell), ($includeParent == "Mandatory" ? "yes" : "no")); ?> />
								</fieldset>
							</td>
							<td colspan="2">
								<fieldset class="field">
									<legend>E-Mail</legend>
									<input name="Guardian1eMail" type="text" size="32" title="E-mail of your first guardian."
										<?php recallText((empty($Guardian1eMail) ? "" : $Guardian1eMail), ($includeParent == "Mandatory" ? "yes" : "no")); ?> />
								</fieldset>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="input">
				<label for="Parent2">Parent / Guardian 2</label>
				<div class="rightcolumn">
					<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Type</legend>
									<select name="Guardian2Type" size="1" id="Parent2"
										<?php
										if (empty($Guardian2Type)) {
										} else {
											$Guardian2Type_a = $Guardian2Type;
										}
										?>>
										<option value="">&nbsp;</option>
										<?php
										foreach ($guardianValues as $value) {
											echo "<option value=\"" . $value . "\"" . ($Guardian2Type_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>First</legend>
									<input name="Guardian2FirstName" type="text" size="16" title="First name of your second guardian."
										<?php recallText((empty($Guardian2FirstName) ? "" : $Guardian2FirstName), "no"); ?> />
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>Last</legend>
									<input name="Guardian2LastName" type="text" size="16" title="Last name of your second guardian."
										<?php recallText((empty($Guardian2LastName) ? "" : $Guardian2LastName), "no"); ?> />
								</fieldset>
							</td>
						</tr>
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Phone</legend>
									<input name="Guardian2Cell" type="text" size="16" title="Phone number of your second guardian."
										<?php recallText((empty($Guardian2Cell) ? "" : $Guardian2Cell), "no"); ?> />
								</fieldset>
							</td>
							<td colspan="2">
								<fieldset class="field">
									<legend>E-Mail</legend>
									<input name="Guardian2eMail" type="text" size="32" title="E-mail of your second guardian."
										<?php recallText((empty($Guardian2eMail) ? "" : $Guardian2eMail), "no"); ?> />
								</fieldset>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="input">
				<label for="Parent3">Parent / Guardian 3</label>
				<div class="rightcolumn">
					<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Type</legend>
									<select name="Guardian3Type" size="1" id="Parent3"
										<?php
										if (empty($Guardian3Type)) {
										} else {
											$Guardian3Type_a = $Guardian3Type;
										}
										?>>
										<option value="">&nbsp;</option>
										<?php
										foreach ($guardianValues as $value) {
											echo "<option value=\"" . $value . "\"" . ($Guardian3Type_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>First</legend>
									<input name="Guardian3FirstName" type="text" size="16" title="First name of your third guardian."
										<?php recallText((empty($Guardian3FirstName) ? "" : $Guardian3FirstName), "no"); ?> />
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>Last</legend>
									<input name="Guardian3LastName" type="text" size="16" title="Last name of your third guardian."
										<?php recallText((empty($Guardian3LastName) ? "" : $Guardian3LastName), "no"); ?> />
								</fieldset>
							</td>
						</tr>
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Phone</legend>
									<input name="Guardian3Cell" type="text" size="16" title="Phone number of your third guardian."
										<?php recallText((empty($Guardian3Cell) ? "" : $Guardian3Cell), "no"); ?> />
								</fieldset>
							</td>
							<td colspan="3">
								<fieldset class="field">
									<legend>E-Mail</legend>
									<input name="Guardian3eMail" type="text" size="32" title="E-mail of your third guardian."
										<?php recallText((empty($Guardian3eMail) ? "" : $Guardian3eMail), "no"); ?> />
								</fieldset>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="input">
				<label for="Parent4">Parent / Guardian 4</label>
				<div class="rightcolumn">
					<table style="width: 100%; max-width: 400px; padding: 0; margin: 0;">
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Type</legend>
									<select name="Guardian4Type" size="1" id="Parent4"
										<?php
										if (empty($Guardian4Type)) {
										} else {
											$Guardian4Type_a = $Guardian4Type;
										}
										?>>
										<option value="">&nbsp;</option>
										<?php
										foreach ($guardianValues as $value) {
											echo "<option value=\"" . $value . "\"" . ($Guardian4Type_a == $value ? "selected=\"selected\">" : ">") . $value . "</option>";
										}
										?>
									</select>
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>First</legend>
									<input name="Guardian4FirstName" type="text" size="16" title="First Name of your fourth guardian."
										<?php recallText((empty($Guardian4FirstName) ? "" : $Guardian4FirstName), "no"); ?> />
								</fieldset>
							</td>
							<td>
								<fieldset class="field">
									<legend>Last</legend>
									<input name="Guardian4LastName" type="text" size="16" title="Last Name of your fourth guardian."
										<?php recallText((empty($Guardian4LastName) ? "" : $Guardian4LastName), "no"); ?> />
								</fieldset>
							</td>
						</tr>
						<tr style="width: auto;">
							<td style="padding: 0;">
								<fieldset class="field">
									<legend>Phone</legend>
									<input name="Guardian4Cell" type="text" size="16" title="Phone number of your fourth guardian."
										<?php recallText((empty($Guardian4Cell) ? "" : $Guardian4Cell), "no"); ?> />
								</fieldset>
							</td>
							<td colspan="4">
								<fieldset class="field">
									<legend>E-Mail</legend>
									<input name="Guardian4eMail" type="text" size="32" title="E-Mail address of your fourth guardian."
										<?php recallText((empty($Guardian4eMail) ? "" : $Guardian4eMail), "no"); ?> />
								</fieldset>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<?php
		}
		?>
	</fieldset>

	<!-- Include Education fields if that camp option is enabled -->
	<?php if ($includeEducationFields != "Hidden" && $CampRole == "Player") { ?>

		<fieldset class="group" id="anchor-education">
			<legend>Education</legend>
			
			<?php if ($CampRole == 'Player') { ?>

				<div class="input" style="border-top: none;">
					<label>High School Search</label>

					<div class="rightcolumn">
						<fieldset class="field">
							<legend>State</legend>
							<select name="StatePlayingIn" id="StatePlayingIn" size="1"
									  title="State or Canadian Province of your high school">
								<?php if (empty($StatePlayingIn)) {
									$StatePlayingIn_a = " ";
								} else {
									$StatePlayingIn_a = $StatePlayingIn;
								} ?>
								<option value="" disabled selected>State</option>
								<?php
								foreach ($stateValues as $value) {
									echo "<option value='" . $value . "'" . ($StatePlayingIn_a == $value ? "selected='selected'>" : ">") . $value . "</option>";
								}
								?>
							</select>
						</fieldset>
						
						<?php if ($U19) { ?>
							<fieldset class="field">
								<legend><?php echo $season ?> Grade Level</legend>
								<select name="CurrentSchoolGradeLevel" id="GradeLevel" size="1"
										  title="The school grade level you are or will be at.">
									<option value=""></option>
									<?php
									for ($i = 3; $i < 14; $i++) {
										echo "<option value='" . $i . "'" . ($CurrentSchoolGradeLevel == $i ? "selected='selected'" : "") . ">" . $i . "</option>";
									}
									?>
								</select>
							</fieldset>
						<?php } ?>

						<button id="UpdateSchoolButton" class='btn btn-primary hidden' style='margin: 2px 0 2px 2em;' type='submit'
								  formaction='Profile.php?UpdateSchool=1#anchor-education'>Search
						</button>
					</div>
				</div>

				<div id="HighSchoolFields">
					<div class="input" style="border-top: none;">
						<label>High School Name</label>
						<select class="select2" name="ID_School" size="1" title="School you are attending">
							<option value=""></option>
							<?php
							foreach ($SchoolValues as $key => $SchoolValue) {
								echo "<option value='" . $key . "'" . ($ID_School == $key ? "selected='selected'>" : ">") . $SchoolValue . "</option>";
							}
							?>
						</select>
					</div>
					
					<?php if (!$U18AtStartOfEvent) { ?>
						<div class="input" style="border-top: none;">
							<label for="HSGraduationYear">High School Graduation Year</label>
							<input type="text" size="6" name="HighSchoolGraduationYear"
									 id="HSGraduationYear" <?php if (!empty($HighSchoolGraduationYear)) {
								echo "value='" . $HighSchoolGraduationYear . "'";
							} ?> />
						</div>
					<?php } ?>

				</div>
			
			<?php }
			if (!$U18AtStartOfEvent) { //show college & military fields ?>

				<div class="input">
					<label for="CollegeName">College</label>
					<select id="CollegeName" class="select2" name="ID_School_College" size="1" title="College you are attending">
						<option value=""></option>
						<?php
						foreach ($CollegeValues as $key => $CollegeValue) {
							echo "<option value='" . $key . "'" . ($ID_School_College == $key ? "selected='selected'>" : ">") . $CollegeValue . "</option>";
						}
						?>
					</select>
				</div>

				<div class="input" style="border-top: none;">
					<label for="CollegeGraduationYear">College Graduation Year</label>
					<input id="CollegeGraduationYear" name="graduationCollegeYear" type="text"
							 size="6" <?php if (!empty($graduationCollegeYear)) {
						echo "value='" . $graduationCollegeYear . "'";
					} ?> />
				</div>

				<div class="input">
					<label>Currently Military?</label>
					<input name="currentlyMilitary" type="radio" value="Yes" class="radio" id="CurrentlyMilitaryYes"
							 title="Yes" <?php if ($currentlyMilitary == "Yes") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="CurrentlyMilitaryYes">Yes</label>

					<input name="currentlyMilitary" type="radio" value="No" id="CurrentlyMilitaryNo" class="radio"
							 title="No" <?php if ($currentlyMilitary == "No") {
						echo 'checked="checked"';
					} ?> />
					<label class="radio" for="CurrentlyMilitaryNo">No</label>
				</div>

				<div class="input">
					<label for="MilitaryBranch">Military Branch</label>
					<select name="militaryBranch" id="MilitaryBranch" size="1">
						<option value=""></option>
						<?php
						foreach ($MilitaryBranchValues as $MilitaryBranchValue) {
							echo "<option value='" . $MilitaryBranchValue . "'" . ($militaryBranch == $MilitaryBranchValue ? "selected='selected'>" : ">") . $MilitaryBranchValue . "</option>";
						}
						?>
					</select>
				</div>

				<div class="input">
					<label for="MilitaryComponent">Military Component</label>
					<select name="militaryComponent" id="MilitaryComponent" size="1">
						<option value=""></option>
						<?php
						foreach ($MilitaryComponentValues as $MilitaryComponentValue) {
							echo "<option value='" . $MilitaryComponentValue . "'" . ($militaryComponent == $MilitaryComponentValue ? "selected='selected'>" : ">") . $MilitaryComponentValue . "</option>";
						}
						?>
					</select>
				</div>
			
			<?php } ?>

		</fieldset>
	
	<?php } ?>

	<!-- Include Reference fields if that event option is selected -->
	<?php
	if ($includeReferenceFields != "Hidden" && $CampRole == "Player") {
		include("../include/referenceFields.php");
	}
	?>

	<!-- Show Signature Fields if selected -->
	<?php
	if ($SignatureOption == "All Players" && $CampRole == "Player") {
		include("../include/parentSignatures.php");
	} else if ($SignatureOption == "U18 Players" && $U18AtStartOfEvent && $CampRole == "Player") {
		include("../include/parentSignatures.php");
	} else {
		echo '
				<div ';
		if (empty($waiver)) {
			echo 'class="missing" style="padding: 1em"';
		}
		echo ' >
						<input type="checkbox" name="waiver" value="1" id="waiver" class="radio" ';
		if ($waiver == 1) {
			echo 'checked="checked"';
		}
		echo ' /><label for="waiver" class="radio">
							&nbspI accept responsibility that the information provided on this form is accurate.</label>
					</div>';
	}
	
	if (empty($IDType)) {
		## Begin HTML block for EventPersonnel IDs ##################################################################
		?>
		<p>
			<input name="respondent_exists" type="hidden" value="true"/>
			<input name="ID" type="hidden" value="<?php echo $ID; ?>"/>
			<input name="heightFeetOriginal" type="hidden" value="<?php echo $heightFeetOriginal; ?>"/>
			<input name="heightInchesOriginal" type="hidden" value="<?php echo $heightInchesOriginal; ?>"/>
			<input name="heightMetersOriginal" type="hidden" value="<?php echo $heightMetersOriginal; ?>"/>
			<input name="weightOriginal" type="hidden" value="<?php echo $weightOriginal; ?>"/>
			<input type="submit" name="submit" value="<?php echo $SubmitTitle; ?>" class="submit" onclick="setValue();" id="Submit_Button"/>
		</p>
		</div> <!-- Container div that does 90% centered margin -->
		
		<?php
	}
	## End HTML block for EventPersonnel IDs ##########################################################################
	?>

	<!-- The B30 values are used to determine nullness, as the Base64 data still returns value with no signature -->
	<input id="signatureConsent" name="signatureConsent" type="hidden" value=""/>
	<input id="signatureConsentB30" name="signatureConsentB30" type="hidden" value=""/>
	<input id="signatureMedical" name="signatureMedical" type="hidden" value=""/>
	<input id="signatureMedicalB30" name="signatureMedicalB30" type="hidden" value=""/>
	<input id="sigConductPlayer" name="sigConductPlayer" type="hidden" value=""/>
	<input id="sigConductPlayerB30" name="sigConductPlayerB30" type="hidden" value=""/>
	<input id="sigConductParent" name="sigConductParent" type="hidden" value=""/>
	<input id="sigConductParentB30" name="sigConductParentB30" type="hidden" value=""/>
	<input id="sigMediaRelease" name="sigMediaRelease" type="hidden" value=""/>
	<input id="sigMediaReleaseB30" name="sigMediaReleaseB30" type="hidden" value=""/>

	<div id="Submit_Dialog" title="Updating Profile">
		<p>Please wait while your profile is updated. This can take up to a minute.</p>
	</div>
</form>

<!-- Image Cropper -->
<!--<script src="../include/script/cropper/main.js"></script>-->
<script src="../include/script/slim/slim.kickstart.min.js"></script>

<script>
    //
    <!-- Dialog popovers -->
    $(function () {
        $("#FacePhoto_Dialog").dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 1000
            },
            hide: {
                effect: "explode",
                duration: 1000
            }
        });
        $("#FacePhoto_Button").on("click", function () {
            $("#FacePhoto_Dialog").removeClass("hidden");
            $("#FacePhoto_Dialog").dialog("open");
        });

        $("#Phone_Dialog").dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 1000
            },
            hide: {
                effect: "explode",
                duration: 1000
            }
        });
        $("#Phone_Button").on("click", function () {
            $("#Phone_Dialog").dialog("open");
        });

        $("#Submit_Dialog").dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 1000
            },
            modal: true
        });
    });

    $(document).ready(function () {
        //
        <!-- Submit popover -->
        $('#Submit_Button').click(function () {
            $("#Submit_Dialog").dialog("open");
        });

        //
        <!-- Searchable drop-down list -->
        $(".select2").select2();

        <!-- Conditional Hidden fields -->
        var OtherClub = $('input:radio[name=OtherClub]');
        var YesClubFields = $('#YesClubFields');
        var UnlistedClubFields = $('#UnlistedClubFields');
        var YesClub = $('#YesClub');

        OtherClub.change(function () { //when the rating changes
            var value = this.value;
            if (value === "UnlistedClub") {
                UnlistedClubFields.removeClass('hidden');
                YesClub.removeClass('hidden');
                YesClubFields.addClass('hidden');
            } else {
                UnlistedClubFields.addClass('hidden');
            }
            if (value === "NoClub") {
                YesClub.removeClass('hidden');
                YesClubFields.addClass('hidden');
            }
            if (value === "YesClub") {
                YesClub.addClass('hidden');
                YesClubFields.removeClass('hidden');
            }
        });

        var UnlistedClubRadio = $('#UnlistedClubRadio');
        var NoClubRadio = $('#NoClubRadio');
        var YesClubRadio = $('#YesClubRadio');

        if ($(UnlistedClubRadio).is(':checked')) {
            UnlistedClubFields.removeClass('hidden');
            YesClub.removeClass('hidden');
            YesClubFields.addClass('hidden');
        } else {
            UnlistedClubFields.addClass('hidden');
        }
        if ($(NoClubRadio).is(':checked')) {
            YesClub.removeClass('hidden');
            YesClubFields.addClass('hidden');
        }
        if ($(YesClubRadio).is(':checked')) {
            YesClub.addClass('hidden');
            YesClubFields.removeClass('hidden');
        }

        var Height_UM = $('#Height_UM');
        var HeightFeet = $('#HeightFeet');
        var HeightInches = $('#HeightInches');
        var HeightMeters = $('#HeightMeters');

        if ($(Height_UM).val() !== "m") {
            HeightFeet.removeClass('hidden');
            HeightInches.removeClass('hidden');
            HeightMeters.addClass('hidden');
        } else {
            HeightFeet.addClass('hidden');
            HeightInches.addClass('hidden');
            HeightMeters.removeClass('hidden');
        }

        Height_UM.change(function () {
            var value = this.value;
            if (value !== "m") {
                HeightFeet.removeClass('hidden');
                HeightInches.removeClass('hidden');
                HeightMeters.addClass('hidden');
            } else {
                HeightFeet.addClass('hidden');
                HeightInches.addClass('hidden');
                HeightMeters.removeClass('hidden');
            }
        });

        var Weight_UM = $('#Weight_UM');
        var Weight = $('#Weight');
        Weight_UM.change(function () {
            var Weight_val = Weight.val();
            var value = this.value;
            if (value !== "kg") {
                Weight.val(Math.round(Weight_val * 2.205 * 10) / 10);
            } else {
                Weight.val(Math.round(Weight_val * .454 * 100) / 100);
            }
        });

        var SchoolState = $('#StatePlayingIn');
        var GradeLevel = $('#GradeLevel');
        var SchoolFields = $('#HighSchoolFields');
        var UpdateSchool = $('#UpdateSchoolButton');

        if ($(SchoolState).val() === "" || $(GradeLevel).val() === "") {
            SchoolFields.addClass('hidden');
        } else {
            SchoolFields.removeClass('hidden');
        }

        SchoolState.change(function () {
            SchoolFields.addClass('hidden');
            UpdateSchool.removeClass('hidden');
        });

        GradeLevel.change(function () {
            SchoolFields.addClass('hidden');
            UpdateSchool.removeClass('hidden');
        });

        //Banned Substances
        var TakingBannedSubstances = $('#BannedSubstance');
        var BannedSubstanceFields = $('.BannedSubstanceFields');

        if (TakingBannedSubstances.val() === "Yes") {
            BannedSubstanceFields.removeClass('hidden');
        } else {
            BannedSubstanceFields.addClass('hidden');
        }

        TakingBannedSubstances.change(function () {
            if (this.value === "Yes") {
                BannedSubstanceFields.removeClass('hidden');
            } else {
                BannedSubstanceFields.addClass('hidden');
            }
        });

    });

    // sets the form values with the js variables
    <!-- Digital Signatures -->
    function setValue() {
        $(document).ready(function () {
            var $sigdiv = $("#signature");
            var sigConsentData = $sigdiv.jSignature('getData');
            var sigConsentB30 = $sigdiv.jSignature('getData', 'base30');
            sigConsentB301 = sigConsentB30[1];
            document.getElementById('signatureConsentB30').value = sigConsentB301;
            // Don't overwrite existing value with null signature
            if (sigConsentB301.length > 25) {
                document.getElementById('signatureConsent').value = sigConsentData;
            } else {
                // Pass on existing data if applicable
                var sigConsentPOST = <?php echo json_encode($_POST['signatureConsent']); ?>;
                if (sigConsentPOST.length > 25) {
                    document.getElementById('signatureConsent').value = sigConsentPOST;
                }
            }

            var $sigdiv2 = $("#signature2");
            var sigMedicalData = $sigdiv2.jSignature('getData');
            var sigMedicalB30 = $sigdiv2.jSignature('getData', 'base30');
            var sigMedicalB301 = sigMedicalB30[1];
            document.getElementById('signatureMedicalB30').value = sigMedicalB301;
            if (sigMedicalB301.length > 25) {
                document.getElementById('signatureMedical').value = sigMedicalData;
            } else {
                // Pass on existing data is applicable
                var sigMedicalPOST = <?php echo json_encode($_POST['signatureMedical']); ?>;
                if (sigMedicalPOST.length > 25) {
                    document.getElementById('signatureMedical').value = sigMedicalPOST;
                }
            }

            var $sigdiv3 = $("#signature3");
            var sigConductPlayerData = $sigdiv3.jSignature('getData');
            var sigConductPlayerB30 = $sigdiv3.jSignature('getData', 'base30');
            var sigConductPlayerB301 = sigConductPlayerB30[1];
            //console.log(sigConductPlayerB301);
            document.getElementById('sigConductPlayerB30').value = sigConductPlayerB301;
            if (sigConductPlayerB301.length > 25) {
                document.getElementById('sigConductPlayer').value = sigConductPlayerData;
                //console.log(sigConductPlayerData);
            } else {
                // Pass on existing data is applicable
                var sigConductPlayerPOST = <?php echo json_encode($_POST['sigConductPlayer']); ?>;
                if (sigConductPlayerPOST.length > 25) {
                    document.getElementById('sigConductPlayer').value = sigConductPlayerPOST;
                }
            }

            var $sigdiv4 = $("#signature4");
            var sigConductParentData = $sigdiv4.jSignature('getData');
            var sigConductParentB30 = $sigdiv4.jSignature('getData', 'base30');
            var sigConductParentB301 = sigConductParentB30[1];
            document.getElementById('sigConductParentB30').value = sigConductParentB301;
            if (sigConductParentB301.length > 25) {
                document.getElementById('sigConductParent').value = sigConductParentData;
            } else {
                // Pass on existing data is applicable
                var sigConductParentPOST = <?php echo json_encode($_POST['sigConductParent']); ?>;
                if (sigConductParentPOST.length > 25) {
                    document.getElementById('sigConductParent').value = sigConductParentPOST;
                }
            }

            var $sigdiv5 = $("#signature5");
            var sigMediaReleaseData = $sigdiv5.jSignature('getData');
            var sigMediaReleaseB30 = $sigdiv5.jSignature('getData', 'base30');
            var sigMediaReleaseB301 = sigMediaReleaseB30[1];
            document.getElementById('sigMediaReleaseB30').value = sigMediaReleaseB301;
            if (sigMediaReleaseB301.length > 25) {
                document.getElementById('sigMediaRelease').value = sigMediaReleaseData;
            } else {
                // Pass on existing data is applicable
                var sigMediaReleasePOST = <?php echo json_encode($_POST['sigMediaRelease']); ?>;
                if (sigMediaReleasePOST.length > 25) {
                    document.getElementById('sigMediaRelease').value = sigMediaReleasePOST;
                }
            }
        });
    }
</script>
</body>
</html>