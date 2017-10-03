$(document).ready(function () {

    <!-- Searchable drop-down list -->
    $(".select2").select2();

    <!-- Submit popovers -->
    $('#Submit_Button').click(function () {
        $("#Submit_Dialog").dialog("open");
    });
    $('.Processing').click(function () {
        $("#Processing").dialog("open");
    });
    $('.loading').click(function () {
        $("#loading").dialog("open");
    });

    <!-- Datepickers -->
    $("#DOBDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "-80:-4",
        defaultDate: "-20y"
    });
    $(".DOBDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "-80:-4",
        defaultDate: "-20y"
    });
    $(".Date-80-1").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "-80:+1"
    });
    $("#CertificationDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "-30:+1"
    });

    <!-- IE Warning -->
    // To use, put the class IEWarning in a div you want visible for IE browsers
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
    {
        // alert(parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
        $('#IEWarning').removeClass('hidden');
    }
    else  // If another browser, return 0
    {
        // alert('otherbrowser');
    }

    <!-- Dialog popovers -->
    $("#eMail_Dialog").dialog({
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
    $("#eMail_Button").on("click", function () {
        $("#eMail_Dialog").dialog("open");
    });

    $("#PrimaryRole_Dialog").dialog({
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
    $("#PrimaryRole_Button").on("click", function () {
        $("#PrimaryRole_Dialog").dialog("open");
    });

    $("#PasswordChanged_Dialog").dialog({
        autoOpen: true,
        show: {
            effect: "blind",
            duration: 1000
        },
        hide: {
            effect: "explode",
            duration: 1000
        }
    });

    $("#loading").dialog({
        autoOpen: false,
        show: {
            duration: 500
        },
        height: 0
    });

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

    $("#Bio_Dialog").dialog({
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
    $("#Bio_Button").on("click", function () {
        $("#Bio_Dialog").dialog("open");
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

    $("#OpenRegistration_Dialog").dialog({
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
    $("#OpenRegistration_Button").on("click", function () {
        $("#OpenRegistration_Dialog").dialog("open");
    });

    $("#Submit_Dialog").dialog({
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 1000
        },
        modal: true
    });
    $("#Submit_Dialog_Club").dialog({
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 1000
        },
        modal: true
    });
    $("#Processing").dialog({
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 1000
        },
        modal: true
    });

    $("#Add_Membership").on("click", function () {
        $("#Add_Membership_Dialog").dialog("open");
    });
    $("#Add_Membership_Dialog").dialog({
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 1000
        },
        modal: true
    });

});

$(window).on("load", function () {

    // Conditional Hidden fields //

    // Camp: Will you be attending?
    var Attending_set = $('input:radio[name=inviteStatus]');
    var Attending = $('input:radio[name=inviteStatus]:checked');
    var AttendanceYesDiv = $('#attendanceYesDiv');
    var AttendanceNoDiv = $('#attendanceNoDiv');
    if ($(Attending).val() === "Yes") {
        AttendanceYesDiv.removeClass('hidden');
    } else {
        AttendanceYesDiv.addClass('hidden');
    }
    if ($(Attending).val() === "No") {
        AttendanceNoDiv.removeClass('hidden');
    } else {
        AttendanceNoDiv.addClass('hidden');
    }
    Attending_set.change(function () {
       var avalue = this.value;
        if (avalue === "Yes") {
            AttendanceYesDiv.removeClass('hidden');
        } else {
            AttendanceYesDiv.addClass('hidden');
        }
        if (avalue === "No") {
            AttendanceNoDiv.removeClass('hidden');
        } else {
            AttendanceNoDiv.addClass('hidden');
        }
    });
    // Camp: Will you be attending? </end>

    //Health Insurance
    var NoInsurance = $('#NoInsurance');
    var HealthInsuranceFields = $('.HealthInsuranceFields');

    if ($(NoInsurance).is(':checked')) {
        HealthInsuranceFields.addClass('hidden');
    } else {
        HealthInsuranceFields.removeClass('hidden');
    }

    // When the NoInsurance checkbox changes
    NoInsurance.change(function () {
        if ($(this).is(':checked')) {
            HealthInsuranceFields.addClass('hidden');
        } else {
            HealthInsuranceFields.removeClass('hidden');
        }
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

    var ValidPassport_set = $('input:radio[name=passportHolder]');
    var ValidPassport = $('input:radio[name=passportHolder]:checked');
    var PassportFields = $('#PassportFields');

    if ($(ValidPassport).val() !== "Yes") {
        PassportFields.addClass('hidden');
    } else {
        PassportFields.removeClass('hidden');
    }

    ValidPassport_set.change(function () {
        var pvalue = this.value;
        if (pvalue !== "Yes") {
            PassportFields.addClass('hidden');
        } else {
            PassportFields.removeClass('hidden');
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
        var hvalue = this.value;
        var HeightFeet = $('#HeightFeet');
        var HeightInches = $('#HeightInches');
        var HeightMeters = $('#HeightMeters');
        if (hvalue !== "m") {
            HeightFeet.removeClass('hidden');
            HeightInches.removeClass('hidden');
            HeightMeters.addClass('hidden');
        } else {
            HeightFeet.addClass('hidden');
            HeightInches.addClass('hidden');
            HeightMeters.removeClass('hidden');
        }
    });
});