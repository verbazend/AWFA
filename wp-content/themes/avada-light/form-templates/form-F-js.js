//Base path for Excelerate API
var baseHTTSpath = "https://www.australiawidefirstaid.com.au/booking-new/";
var campaignstring = "";
var campaignstring_sub = "";
var campaignname = "";

jQuery(function($){
	$("body.home #od_cform_footerhooked").hide();
	$(".homepage-firstaid-minibookingform-header").hide();
	$("body.home .homepage-firstaid-minibookingform-header").show();
	$("body.page-id-4641 .homepage-firstaid-minibookingform-header").show();
	$("body.page-template-contact-php .homepage-firstaid-minibookingform-header").show();
	
	//Paypal not integrated yet, disable from form.
	$(".payment-paypal").hide();
	
	var formhtml = $("#od_cform_footerhooked").html();
	
	$("#homepage_form").html(formhtml);
	$("#od_cform_footerhooked").html('');
	
	$("body.page-template-contact-php #text-6").html('<div id="contactpage_form">'+formhtml+'</div>');
	
	var CategoryData = $("#woocommerce_product_categories-2 ul").html();
	$("#woocommerce_product_categories-2").html("<div class='orderby-order-container firstaidsupplies-categorylist'><ul class='orderby order-dropdown category'><li><span class='current-li-category'><a>Sort by <strong>Category</strong></a></span><ul><li class='cat-item cat-item-43'><a href='/store/'>All Categories</a></li>"+CategoryData+"</ul></li></ul></div>");
	//$("#woocommerce_product_categories-2 ul").addClass("orderby order-dropdown category");
	
	
	//Set some vars that we will continue to use below.
	var currentTime = new Date();
	
	var $window = $(window);
        
	//Enrolment Form Page - Sidebar Stickyness
	window.onscroll = function()
	{
	    sidebarsHandler();
	}
	
	sidebarsHandler();
	
	//Run on Load
	function sidebarsHandler(){
		if( window.XMLHttpRequest && $window.width() >= 800 ) {
	        if (document.documentElement.scrollTop > 380 || self.pageYOffset > 380) {
	            $('#enrolment-selection').css('position','fixed');
	            $('#enrolment-selection').css('top','40px');
	        } else if (document.documentElement.scrollTop < 380 || self.pageYOffset < 380) {
	            $('#enrolment-selection').css('position','relative');
	            $('#enrolment-selection').css('top','0px');
	        }
	        $('.enrolment-form #sidebar').removeClass("sideslide");
	    } else {
	    	$('.enrolment-form #sidebar').addClass("sideslide");
	    	$('#enrolment-selection').css('position','relative');
	        $('#enrolment-selection').css('top','0px');
	    }
	}
	
	//Enrolment form confirm Details
	$(".enrolment-form input, .enrolment-form select, .enrolment-form textarea").on("change", function(){
		if(this.value){
			$(".value-"+ $(this).attr("name") ).html(this.value);
		} else {
			$(".value-"+ $(this).attr("name") ).html("--");
		}
	});
	
	// Enables logic switch for payment types (show what child elements etc.)
	$("input[name='paymenttype']").on("click", function(){
			$(".payment-type-wrapper").hide();
			if(this.value=="payment-card"){
				$(".payment-type-card").show( 500 );
			}
			if(this.value=="payment-paypal"){
				$(".payment-type-paypal").show( 500 );
			}
			if(this.value=="payment-ontheday"){
				$(".payment-type-ontheday").show( 500 );
			}
			if(this.value=="payment-other"){
				$(".payment-type-other").show( 500 );
			}
	});
	
	
	$("body").addClass("browser-"+$.browser.name);
	
	//------ Prefill of Form Data, Generation of Drop downs ---------//
	
	//How Did You Find Us - source drop down
	var HowDidYouFindUsSource = ["Google","Facebook","Return Customer","Corporate Customer","Friend/Colleague","Renewal Reminder","Other"];
	$(".select-howdidyoufindus").append($('<option/>', { 
        value: "",
        text : "How did you find us?" 
    }));
	$(HowDidYouFindUsSource).each(function(SourceIndex,SourceName){
		$(".select-howdidyoufindus").append($('<option/>', { 
	        value: SourceName,
	        text : SourceName 
	    }));
	});
	
	
	//Expiry Date - Month Dropdown
	var DefaultToCurrentMonth = true; //If true we will try and set the current month as the default. If False, We start at index 0
	var ExpiryMonthJson = '[{"text":"01 - January","value":"01"},{"text":"02 - February","value":"02"},{"text":"03 - March","value":"03"},{"text":"04 - April","value":"04"},{"text":"05 - May","value":"05"},{"text":"06 - June","value":"06"},{"text":"07 - July","value":"07"},{"text":"08 - August","value":"08"},{"text":"09 - September","value":"09"},{"text":"10 - October","value":"10"},{"text":"11 - November","value":"11"},{"text":"12 - December","value":"12"}]';
	var ExpiryMonth = jQuery.parseJSON(ExpiryMonthJson);
	
	var setAsCurrentMonth = false;
	$(ExpiryMonth).each(function(ExpiryIndex,ExpiryData){
		
		
		if(DefaultToCurrentMonth){
			currentMonth = parseInt(currentTime.getMonth() + 1);
			dataMonth = parseInt(ExpiryData.value);
			if(dataMonth==currentMonth){
				setAsCurrentMonth = true;
			} else {
				setAsCurrentMonth = false;
			}
		} else {
			setAsCurrentMonth = false;
		}
		
		if(setAsCurrentMonth){
			$(".select-expirymonth").append($('<option/>', { 
		        value: ExpiryData.value,
		        text : ExpiryData.text,
		        selected : true
		    }));
	  	} else {
	  		$(".select-expirymonth").append($('<option/>', { 
		        value: ExpiryData.value,
		        text : ExpiryData.text
		    }));
	  	}
	});
	
	//Expiry Date - Year Dropdown
	var YearsAhead = 10; //How many years ahead should we allow an expiry date to be?
	var CurrentYear = currentTime.getFullYear(); //Let the users OS set the current year
	var EndYear = CurrentYear + YearsAhead;
	for ( var i = CurrentYear; i < EndYear; i++ ) {
	    $(".select-expiryyear").append($('<option/>', { 
	        value: i,
	        text : i
	    }));
	}
	
	
	var otherPaymentOptions = ["Direct Debit","Send me an Invoice","Corporate Invoice","Money Order/Cheque"];
	$(".select-otherpaymentoptions").append($('<option/>', { 
        value: "",
        text : "Other Payment Options" 
    }));
	$(otherPaymentOptions).each(function(SourceIndex,SourceName){
		$(".select-otherpaymentoptions").append($('<option/>', { 
	        value: SourceName,
	        text : SourceName 
	    }));
	});
	
	//---------------------------- FROM AXCELERATE DRUPAL JS -------------------------------------//
	
    var datepickerDateFilter = function(date) {
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var date_string = date.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
        
        var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		
		if(date<today){
			return [false];
		}

        if ($courseDates.data.length > 0 && $.inArray(date_string, $courseDates.data) != -1) {
            return [true];
        }
        return [false];
    };
    
    $('.axcelerateCourseDate').datepicker({
        beforeShowDay: datepickerDateFilter,
        dateFormat: 'dd-mm-yy',
        onSelect: function(date) {
            $.each($courseDates.dates, function(index, value) {
                if (value == date) {
                    $('.axcelerateCourseID').val(index);
                }
            });
        }
    });
    
	$('.axcelerateState').change(function() {
        $('.axcelerateCourseID, .axcelerateCourseDate , .axcelerateCourse').val('');
        $.get(baseHTTSpath + "/axcelerate/drill/" + $(this).val() + campaignstring, function(data) {
            $('.axcelerateLocation').html(data);
            $('.axcelerateLocation option:eq(0)').before('<option>Select Location</option>');
            $(".axcelerateLocation").val($(".axcelerateLocation option:first").val());
    	});
 	});

    $('.axcelerateLocation').change(function() {
        $('.axcelerateCourseID, .axcelerateCourseDate').val('');
        $.get(baseHTTSpath + "/axcelerate/drill/" + $('.axcelerateState').val() + '/' + $(this).val() + campaignstring, function(data) {
            $('.axcelerateCourse').html(data);
            $('.axcelerateCourse option:eq(0)').before('<option>Select Course</option>');
            $(".axcelerateCourse").val($(".axcelerateCourse option:first").val());
        });
    });


    $('.axcelerateCourse').change(function() {
        $('.axcelerateCourseID, .axcelerateCourseDate').val('');
        address = baseHTTSpath + "/axcelerate/drill/" + $('.axcelerateState').val() + "/" + $('.axcelerateLocation').val() + '/' + $(this).val() + campaignstring;
        $.get(address, function(data) {
        	
            $courseDates = jQuery.parseJSON(data);
        });
    });
    
    
    $(".axcelerateState, .axcelerateLocation, .axcelerateCourse, .axcelerateCourseDate").on("change",function(){
    	//reset validation warnings
		jQuery(".enrol-validationerror").hide();
		jQuery(".enrol-error-message").hide();
    });
    
    jQuery(".enrolmain-validationerror").hide();


	//preLoadSampleData();
	
	//Handler for sidebar after click, sidebar click
	jQuery(".sidebartoggle").on("click", function(){
		showHideSidebar();
	});
	
});

function loadCourseDataToSidebar(courseID){
	if(courseID==""){
		return false;
	}
	courseData = jQuery.parseJSON(courseData);
	
	jQuery(".enrol-coursename").html(courseData.courseName);
	jQuery(".enrol-coursedate").html(courseData.courseDate);
	jQuery(".enrol-coursetime").html(courseData.courseTimings);
	jQuery(".enrol-courselocation").html(courseData.courseLocation);
	jQuery(".enrol-coursecost").html("$"+courseData.courseTotalCost);
	
	jQuery("#enrol-courseID").val(courseID);
	
}

function miniEnrolNow(){
	var courseID = jQuery(".axcelerateCourseID").val();
	if(courseID){
		document.location = "https://www.australiawidefirstaid.com.au/enrol-now/?cid="+courseID + campaignstring_sub;
	} else {
		//Run through form validation
		var enrolState    = jQuery(".axcelerateState").val();
		var enrolLocation = jQuery(".axcelerateLocation").val();
		var enrolCourse   = jQuery(".axcelerateCourse").val();
		var enrolDate     = jQuery(".axcelerateCourseDate").val();
		
		
		//reset validation warnings
		jQuery(".enrol-validationerror").hide();
		
		if(!enrolState || enrolState == "0"){
			//Failed Validation
			jQuery(".enrol-mini-state .enrol-validationerror").show();
			return false;
		}
		if(!enrolLocation || enrolLocation == "Select Location"){
			//Failed Validation
			jQuery(".enrol-mini-location .enrol-validationerror").show();
			return false;
		}
		if(!enrolCourse || enrolCourse == "Select Course"){
			//Failed Validation
			jQuery(".enrol-mini-course .enrol-validationerror").show();
			return false;
		}
		if(!enrolDate){
			//Failed Validation
			jQuery(".enrol-mini-date .enrol-validationerror").show();
			return false;
		}
	}
}

function preLoadSampleData(){
	jQuery("input[name='firstname']").val("Adam");
	jQuery("input[name='lastname']").val("Sample");
	jQuery("input[name='mobile']").val("0400000000");
	jQuery("input[name='email']").val("adam@example.com");
	jQuery("input[name='address']").val("123 Fake ST");
	jQuery("input[name='suburb']").val("Faketown");
	jQuery("input[name='postcode']").val("4000");
	
	jQuery("input[name='cardnumber']").val("4444333322221111");
	jQuery("input[name='cardpostcode']").val("4000");
	jQuery("input[name='cardccv']").val("123");
}

function enrolNow(){
	
	startLoading();
	

	
	jQuery(".enrolmain-validationerror").hide();
	var en_courseid      = jQuery("input[name='enrol-courseID']").val();
	
	//Validate enrol now form. If that pases, submit info to server for processing :)
	var en_firstname      = jQuery("input[name='firstname']").val();
	var en_lastname       = jQuery("input[name='lastname']").val();
	var en_mobile         = jQuery("input[name='mobile']").val();
	var en_email          = jQuery("input[name='email']").val();
	var en_address        = jQuery("input[name='address']").val();
	var en_suburb         = jQuery("input[name='suburb']").val();
	var en_postcode       = jQuery("input[name='postcode']").val();
	var en_workplace      = jQuery("input[name='workplace']").val();  //NO VALIDATION
	var en_source         = jQuery("select[name='source']").val(); //NO VALIDATION
	var en_additionalinfo = jQuery("textarea[name='additionalinfo']").val(); //NO VALIDATION
	var en_tacagree       = jQuery("input[name='tacagree']");
	var en_sendreminders  = jQuery("input[name='sendreminders']").val(); //NO VALIDATION
	var en_paymenttype    = jQuery("input[name='paymenttype']:checked");
	var en_paymentvalue    = jQuery("input[name='paymenttype']:checked").val();
	
	//console.log(en_paymenttype);
	
	//Credit Card Fields
	var en_cardnumber      = jQuery("input[name='cardnumber']").val();
	var en_cardpostcode    = jQuery("input[name='cardpostcode']").val();
	var en_cardccv         = jQuery("input[name='cardccv']").val();
	var en_cardexpirymonth = jQuery("select[name='cardexpirymonth']").val();
	var en_cardexpiryyear  = jQuery("select[name='cardexpiryyear']").val();
	
	//Other Payment options Fields
	var en_otherpaymentselection = jQuery("select[name='otherpaymentselection']").val();
	
	var en_campaign = jQuery("select[name='campaign']").val();
	
	
	if(!en_firstname){ elementselector = ".enrolmain-validationerror.en_firstname"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_lastname){ elementselector = ".enrolmain-validationerror.en_lastname"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_mobile){ elementselector = ".enrolmain-validationerror.en_mobile"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_email || !isValidEmailAddress(en_email)){ elementselector = ".enrolmain-validationerror.en_email"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_address){ elementselector = ".enrolmain-validationerror.en_address"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_suburb){ elementselector = ".enrolmain-validationerror.en_suburb"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_postcode){ elementselector = ".enrolmain-validationerror.en_postcode"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_tacagree.is(':checked')){ elementselector = ".enrolmain-validationerror.en_tacagree"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_paymenttype.is(':checked')){ elementselector = ".enrolmain-validationerror.en_paymenttype"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	
	if(en_paymenttype.val()=="payment-card"){
		//console.log(en_paymenttype.val());
		if(!en_cardnumber){ elementselector = ".enrolmain-validationerror.en_cardnumber"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
		if(!en_cardpostcode){ elementselector = ".enrolmain-validationerror.en_cardpostcode"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
		if(!en_cardccv){ elementselector = ".enrolmain-validationerror.en_cardccv"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	}
	
	if(en_paymentvalue=="payment-other"){
		if(!en_otherpaymentselection || en_otherpaymentselection=="Other Payment Options"){ elementselector = ".enrolmain-validationerror.en_otherpaymentselection"; jQuery(elementselector).show(); scrollToElement(elementselector);  cancelLoading(); return false; }
	}
	
	
	var pData = {fname:en_firstname, lname:en_lastname, mobile:en_mobile, email:en_email, workplace:en_workplace, source:en_source, special_needs:en_additionalinfo, address:en_address, suburb:en_suburb, postcode:en_postcode, terms:en_tacagree.val(), payment:en_paymentvalue, cc:en_cardnumber,expiryM:en_cardexpirymonth,expiryY:en_cardexpiryyear,cvv:en_cardccv, opt_in:en_sendreminders, courseid:en_courseid, campaign:en_campaign};
	
	
	
	console.log(pData);
	
	jQuery.post(baseHTTSpath+"/axcelerate/book/enrolment/", pData, function(rdata){
	    console.log(rdata); // check your console, you should see some output
	    
	    respData = jQuery.parseJSON(rdata);
	    if(respData.success){
	    	jQuery(".enrol-section-1").hide(200);
			jQuery(".enrol-section-2").hide(200);
			
			scrollToElement(".enrol-error-message");
			
	    	//console.log("TRANSCTION OK")
	    	document.location = "/enrol-now/thank-you/?cid="+en_courseid+"&t="+respData.txnid;
	    } else {
	    	jQuery(".enrol-error-text").html(respData.error_message);
	    	jQuery(".enrol-error-message").show(600);

			
	    	cancelLoading();
	    	
	    	return false;
	    }
	    
	});
	
	//document.location = "/enrol-now/thank-you/";
}
function startLoading(){
	jQuery(".enrol-now-button-large").addClass("loading");
}
function cancelLoading(){
	jQuery(".enrol-now-button-large").removeClass("loading");
}
function scrollToElement(elementselector){
	jQuery('html, body').animate({
        scrollTop: jQuery(elementselector).offset().top-80
    }, 600);
}


function startSidebarTest(){
	jQuery("body").addClass("slideleft");
}

function showHideSidebar(){
	var isSlideLeft = jQuery("body").hasClass("slideleft");
	
	if(isSlideLeft){
		jQuery("body").removeClass("slideleft");
	} else {
		jQuery("body").addClass("slideleft");
	}
}
//----------- Helper Functions ----------------//
var QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    	// If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    	// If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    	// If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
    return query_string;
} ();


function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};



/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}
function setCampaign(campaignstring_self){
	campaignname = campaignstring_self;
	campaignstring = "?campaign="+campaignstring_self;
	campaignstring_sub = "&campaign="+campaignstring_self;
	return true;
}
