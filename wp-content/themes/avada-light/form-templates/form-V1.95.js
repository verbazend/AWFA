//Base path for Excelerate API
var baseHTTSpath = "https://www.australiawidefirstaid.com.au/booking-manager/";
var campaignstring = "";
var campaignstring_sub = "";
var campaignname = "";
var currentPersonQty = 0;

var currentCourseLocationName = "";
var currentCourseName = "";

var globalCurrentCost = 0;
var chosenLocation = 0;


jQuery(function($){
	$("body.home #od_cform_footerhooked").hide();
	$(".homepage-firstaid-minibookingform-header").hide();
	$("body.home .homepage-firstaid-minibookingform-header").show();
	$("body.page-id-4641 .homepage-firstaid-minibookingform-header").show();
	$("body.page-template-contact-php .homepage-firstaid-minibookingform-header").show();
	$("#enrolloading").hide();
	
	//Paypal not integrated yet, disable from form.
	$(".payment-paypal").hide();
	$(".courseSelectionWrapperDiv").hide();
	
	var formhtml = $("#od_cform_footerhooked").html();
	
	$("#homepage_form").html(formhtml);
	$("#od_cform_footerhooked").html('');
	
	$(".enrol-mini-date").hide();
	
	$("body.page-template-contact-php #text-6").html('<div id="contactpage_form">'+formhtml+'</div>');
	
	var CategoryData = $("#woocommerce_product_categories-2 ul").html();
	$("#woocommerce_product_categories-2").html("<div class='orderby-order-container firstaidsupplies-categorylist'><ul class='orderby order-dropdown category'><li><span class='current-li-category'><a>Sort by <strong>Category</strong></a></span><ul><li class='cat-item cat-item-43'><a href='/store/'>All Categories</a></li>"+CategoryData+"</ul></li></ul></div>");
	//$("#woocommerce_product_categories-2 ul").addClass("orderby order-dropdown category");
	
	//var courseSelector = "<div class='courseSelectionWrapperDiv'><div class='center'><div class='courseSelectionWrapper'><span class='closebutton'><a href='#'><img src='/images/close.png'></a></span><h2>Click a Course to select</h2><div class='courseselectionlist'><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></div></div></center></div>";
	
	var courseSelector = jQuery("#courseSelectorTemplate").html();
	
	
	var courseselectionlist = "<div class='courseItem'><a href='/enrol-now/?cid=141274'><div class='courseItemDate'>Monday 23<sup>rd</sup> March 2015</div><div class='courseItemTime'>9:00 AM to 5:00 PM</div></a></div>";
	var courseLocationDetails = "<div class='courselocationdetails'></div>";
	var weekseperator = "<div class='weeksperator'>Courses This Week</div>";
	var weekseperator2 = "<div class='weeksperator'>Courses Next Week</div>";
	var weekseperator3 = "<div class='weeksperator'>Later Courses</div>";
	
	var showmoreCourses = "<div class='courseItem'><a href='#' style='text-align:center;'><center>Show More Courses</center><img style='padding-left:77px; padding-right:70px;' src='/images/more.png'></a></div>";
	
	
	var courseTableHeader = "<table style='width:100%;'>";
	var courseSectionHeader = "<tr class='headerrow'><td>Date</td><td>Time</td><td>Cost</td><td class='selector_remseats'>Seats Left</td><td>Seats <span class='hidemobile'>Required</span></td><td>&nbsp;</td></tr>";
	var courseTableFooter = "</table>";
	

	
	$(courseSelector).appendTo("#wrapper");
	//$(".courseselectionlist").html(courseLocationDetails+weekseperator+courseselectionlist+courseselectionlist+weekseperator2+courseselectionlist+courseselectionlist+courseselectionlist+weekseperator3+courseselectionlist+courseselectionlist+courseselectionlist+courseselectionlist+courseselectionlist+courseselectionlist+courseselectionlist+showmoreCourses);
	
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
				$(".backNextButtons a:nth-child(2)").show(500);
			}
			if(this.value=="payment-paypal"){
				$(".payment-type-paypal").show( 500 );
				$(".backNextButtons a:nth-child(2)").hide(500);
			}
			if(this.value=="payment-ontheday"){
				$(".payment-type-ontheday").show( 500 );
				$(".backNextButtons a:nth-child(2)").hide(500);
			}
			if(this.value=="payment-other"){
				$(".payment-type-other").show( 500 );
				$(".backNextButtons a:nth-child(2)").hide(500);
			}
			if(this.value=="payment-employer"){
				$(".payment-type-employer").show( 500 );
				$(".backNextButtons a:nth-child(2)").show(500);
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
	
	
	var otherPaymentOptions = ["Corporate Invoice"];
	$(".select-otherpaymentoptions").append($('<option/>', { 
        value: "Corporate Invoice",
        text : "Corporate Invoice" 
    }));
	/*$(otherPaymentOptions).each(function(SourceIndex,SourceName){
		$(".select-otherpaymentoptions").append($('<option/>', { 
	        value: SourceName,
	        text : SourceName 
	    }));
	});
	*/
	
	
	function arrayContains(needle, arrhaystack)
	{
	    //return (arrhaystack.indexOf(needle) > -1);
	    //var idx=$.map(arrhaystack, function(item,i){
		//    if(item.instanceDate==needle){
		//        return true;
		 //   }
		//});
		//console.log(arrhaystack.length);
		$.each(arrhaystack, function(courseData){
			//console.log(courseData);
			courseID = courseData.instanceID;
			courseDate = courseData.instanceDate;
			//console.log(courseDate+" - "+needle);
			if(courseDate==needle){
				return true;
			} else {
				return false;
			}
			
		});
		

	}

	//---------------------------- FROM AXCELERATE DRUPAL JS -------------------------------------//
	
    var datepickerDateFilter = function(date) {
    	//console.log(date);
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
		//console.log(date_string+"   -    "+yyyy+"-"+mm+"-"+dd);
		//console.log($courseDates);
		/*
		var vi = 0;
		showDate = false;
		$.each($courseDates, function(i){
			if(showDate==false){
				courseData = $courseDates[vi];
				courseID = courseData.instanceID;
				courseDate = courseData.instanceDate;
	
				vi++;
				
				if(courseDate==date_string){
					showDate = true;
					//console.log("True: "+date_string+"  -  "+courseDate);
				} else {
					showDate = false;
					//console.log("False: "+date_string+"  -  "+courseDate);
				}
			}
		});
		*/
		var ti = 0;
		var showDate = false;
		$.each($courseDates, function(data){
			if(showDate==false){
				//console.log($courseDates[ti].instanceDate);
				if($courseDates[ti].instanceDate==date_string){
					showDate = true;
				} else {
					showDate = false;
				}
			}
			ti++;
		});
		
		
		//console.log(date_string +"   -   "+arrayContains(''+date_string+'', $courseDates))
		//console.log(today);
        if ( showDate ) {
            return [true];
        } else {
        	return [false];
        }
    };
    
    /*$('.axcelerateCourseDate').datepicker({
        beforeShowDay: datepickerDateFilter,
        dateFormat: 'dd-mm-yy',
        onSelect: function(date) {
            $.each($courseDates, function(index,value) {
            	
            	
            	instanceDate = $courseDates[index].instanceDate;
            	instanceID = $courseDates[index].instanceID;
            	compardatearray = instanceDate.split("-");
            	comparedate = compardatearray[2]+"-"+compardatearray[1]+"-"+compardatearray[0];
            	
            	//console.log(index+" - "+comparedate+"  -  "+date);
            	
                if (comparedate == date) {
                    $('.axcelerateCourseID').val(instanceID);
                }
            });
        }
    });
    */
   $(".axcelerateCourseDate").on("click", function(){
   		openCouresSelector();
   });
   
   
   function openCouresSelector(){
   	
   			jsCdate = new Date();
   			cHTML = "";
   			lastHeader = "";
   			lastmonth = "";
   			hasdata = false;
   			
   			$(".courseSelectionWrapperDiv").show();
   			
   			
   			
   			$(".selectedLocationName").html(currentCourseLocationName);
   			$(".selectCourseName").html(currentCourseName);
   			
   			
   	
   			$.each($courseDates, function(index,value) {
            	
            	
            	
   			
            	instanceDate = $courseDates[index].instanceDate;
            	instanceID = $courseDates[index].instanceID;
            	
            	if(instanceID){
            	hasdata = true;
            	}
            	
            	iTime = $courseDates[index].time;
            	iDate = $courseDates[index].date;
            	
            	cost = $courseDates[index].cost;
            	remseats = $courseDates[index].remseats;
            	
            	jsDate = $courseDates[index].jsDate;
            	
            	
            	jsDate = new Date(jsDate);
            	
            	js7date = new Date(jsCdate);
	   			js14date = new Date(jsCdate);
            	

            	//console.log(index+" - "+comparedate+"  -  "+date);
            	js7date.setDate(jsCdate.getDate() + 7);
            	js14date.setDate(jsCdate.getDate() + 14);
            	
            	//console.log("start: "+datetoms(jsDate)+"  -  7 Day: "+datetoms(js7date)+"  -  14 Day: "+datetoms(js14date));
            	
            	if(datetoms(jsDate) < datetoms(js7date)){
            		// is current week
            		//console.log("Week");
            		if(lastHeader=="thisweek"){
            			
            		} else {
            			cHTML = cHTML+"<tr class='Courserow weeksperator'><td colspan='6'>Courses This Week</td></tr>"+courseSectionHeader;
            			lastHeader = "thisweek";
            		}
            	} else if(datetoms(jsDate) > datetoms(js7date) && datetoms(jsDate) < datetoms(js14date)){
            		// is current week
            		//console.log("Next");
            		if(lastHeader=="nextweek"){
            			
            		} else {
            			cHTML = cHTML+"<tr class='Courserow weeksperator'><td colspan='6'>Courses Next Week</td></tr>"+courseSectionHeader;
            			lastHeader = "nextweek";
            		}
            	} else {
            		// is later
            		//console.log("Later");
            		if(lastHeader=="later"){
            			
            			currentMonth = jsDate.getMonth();
            			if(currentMonth==lastmonth){
            				
            			} else {
            				cHTML = cHTML+"<tr class='Courserow weeksperator'><td colspan='6'>Courses In "+getMonthFullName(currentMonth)+"</td></tr>"+courseSectionHeader;
            			}
            			
            			lastmonth = jsDate.getMonth();
            			
            		} else {
            			cHTML = cHTML+"<tr class='Courserow weeksperator'><td colspan='6'>Courses Next Few Weeks</td></tr>"+courseSectionHeader;
            			lastHeader = "later";
            		}
            	}
            	
            	if(remseats=="1"){
            		var seatsReq = "<select id='seatreq_"+instanceID+"'><option>1</option></select>";
            	} else {
            		var seatsReq = "<select id='seatreq_"+instanceID+"'><option>1</option><option>2</option><option>3</option><option value='4'>4+</option></select>";
            	}
            	if(remseats=="2"){
            		var seatsReq = "<select id='seatreq_"+instanceID+"'><option>1</option><option>2</option></select>";
            	}
            	if(remseats=="3"){
            		var seatsReq = "<select id='seatreq_"+instanceID+"'><option>1</option><option>2</option><option>3</option></select>";
            	}
            	if(remseats=="3+"){
            		var seatsReq = "<select id='seatreq_"+instanceID+"'><option>1</option><option>2</option><option>3</option><option value='4'>4+</option></select>";
            	}
            	
            	//cHTML = cHTML+"<div class='courseItem'><a onclick='enrollSelect("+instanceID+");' href='#Enroll:"+instanceID+"'><div class='courseItemDate'>"+iDate+"</div><div class='courseItemTime'>"+iTime+"</div></a></div>";
				
				cHTML = cHTML+"<tr class='Courserow'><td>"+iDate+"</td><td>"+iTime+"</td><td>"+cost+"</td><td class='selector_remseats'>"+remseats+"</td><td>"+seatsReq+"</td><td><a class='enrolbuttonmini' onclick='enrollSelect("+instanceID+");' href='#Enroll:"+instanceID+"'>Book Now</a></td></tr>";
            	
                //if (comparedate == date) {
                //    $('.axcelerateCourseID').val(instanceID);
                //}
            });
            
            
            // courseLocationDetails
            // weekseperator
            // courseselectionlist
            // showmoreCourses
            
            
            if(hasdata==false){
            	$(".courseSelectionWrapperDiv").hide();
   				$(".axcelerateLocation").focus();
   				
   				return;
   			} else {
   				
   				 $(".courseselectionlist").html(courseLocationDetails+courseTableHeader+cHTML+courseTableFooter);
	
   	
   			}
            
            
           
   };
   
   $(".courseSelectionWrapperDiv").on("click", function(){
   			//$(".courseSelectionWrapperDiv").hide();
   });
   $(".closebutton").on("click", function(){
   			$(".courseSelectionWrapperDiv").hide();
   });
   
   
   

    
	$('.axcelerateState').change(function() {
        $('.axcelerateCourseID, .axcelerateCourseDate , .axcelerateCourse').val('');
        $.get(baseHTTSpath + "?rq=getCourseInfo_v2&rd=" + $(this).val() + campaignstring, function(data) {
            $('.axcelerateLocation').html(data);
            $('.axcelerateLocation option:eq(0)').before('<option>Select Location</option>');
            $(".axcelerateLocation").val($(".axcelerateLocation option:first").val());
    	});
 	});

    $('.axcelerateLocation').change(function() {
        $('.axcelerateCourseID, .axcelerateCourseDate').val('');
        
        //console.log("Current Location ID: "+$(this).val());
        //$('.axcelerateLocation').val()
        chosenLocation = $(this).val();
        
        currentCourseLocationName = $(this).find("option:selected").text()
   			
        $.get(baseHTTSpath + "?rq=getCourseInfo_v2&rd=" + $('.axcelerateState').val() + '_' + $(this).val() + campaignstring, function(data) {
            $('.axcelerateCourse').html(data);
            $('.axcelerateCourse option:eq(0)').before('<option>Select Course</option>');
            $(".axcelerateCourse").val($(".axcelerateCourse option:first").val());
            
            
        });
        
        $(".overaxcelerateLocation option[value='"+this.value+"']").attr("selected", "selected");
        
    });
    


    $('.axcelerateCourse').change(function() {
    	
		currentCourseName = $(this).find("option:selected").text()
        $('.axcelerateCourseID, .axcelerateCourseDate').val('');
        address = baseHTTSpath + "?rq=getCourseInfo_v2&rd=" + $('.axcelerateState').val() + "_" + chosenLocation + '_' + $(this).val() + campaignstring;
        
        $("input.axcelerateCourseDate").after("<div class='courseDateLoading' style='margin-bottom:10px;'><img src='/images/courseloading.gif'></div>");
        
        $("input.axcelerateCourseDate").hide(10);
        
        $.get(address, function(data) {
        	
            $courseDates = jQuery.parseJSON(data);
            
            if($courseDates.dates==false){
            	alert("No Courses matching that search criteria!");
            	return false;
            } else {
            
	            $("input.axcelerateCourseDate").show();
	            $(".courseDateLoading").hide();
	            
	            var courseChangeDetailsIsOpen = $( ".courseChangeDetails" ).dialog( "isOpen" );
	            if(courseChangeDetailsIsOpen){
	            	$( ".courseChangeDetails" ).dialog( "close" );	
	            }
	           	
	            
	            openCouresSelector();
            }
             
            //console.log($courseDates.data);
        });
    });
    
    $('.overaxcelerateCourse').change(function() {
    	

        $('.axcelerateCourseID, .axcelerateCourseDate').val('');
        address = baseHTTSpath + "?rq=getCourseInfo_v2&rd=" + $('.axcelerateState').val() + "_" + $('.axcelerateLocation').val() + '_' + $(this).val() + campaignstring;
        
        $("input.axcelerateCourseDate").after("<div class='courseDateLoading' style='margin-bottom:10px;'><img src='/images/courseloading.gif'></div>");
        
        $("input.axcelerateCourseDate").hide(10);
        
        $.get(address, function(data) {
        	
            $courseDates = jQuery.parseJSON(data);
            
            if($courseDates.dates==false){
            	alert("No Courses matching that search criteria!");
            	return false;
            }
            
            
            
            $("input.axcelerateCourseDate").show();
            $(".courseDateLoading").hide();
            
            openCouresSelector();
             
            //console.log($courseDates.data);
        });
    });
    
    
    function miniEnrolNow(){
		
		//openCouresSelector();
		return;
	}
	
	$(".minienrolbuton").on("click", function(){
		
		if($(".axcelerateLocation").val() && $(".axcelerateCourse").val()){	
			openCouresSelector();
			return;
		} else {
			$(".axcelerateLocation").focus();
			openDropdown(".axcelerateLocation");
			
			
		}
	});
	
	
    
    
    
    $(".axcelerateState, .axcelerateLocation, .axcelerateCourse, .axcelerateCourseDate").on("change",function(){
    	//reset validation warnings
		jQuery(".enrol-validationerror").hide();
		jQuery(".enrol-error-message").hide();
    });
    
    jQuery(".enrolmain-validationerror").hide();

	jQuery(".backNextButtons").html("<a href='javascript:goBackStep();' class='enrolNextbutton backButton'>Back</a><a href='javascript:goNextStep();' class='enrolNextbutton nextButton'>Next</a>");
	jQuery(".backNextButtons.nobackbutton .backButton").hide();
	jQuery(".backNextButtons.nonextbutton .nextButton").hide();
	preLoadSampleData();
	
	//Handler for sidebar after click, sidebar click
	jQuery(".sidebartoggle").on("click", function(){
		showHideSidebar();
	});
	
	showEnrolSections(1);
	
	LocationMerge();
	
});


function copydetailstoconfirm(){
	
	jQuery(".confirm-person1").hide();
	jQuery(".confirm-person2").hide();
	jQuery(".confirm-person3").hide();
	
	if(currentPersonQty==1 || currentPersonQty == 2 || currentPersonQty == 3){
		jQuery(".confirm-person1").show();
		jQuery(".value-firstname1").html(jQuery("input[name='firstname1']").val());
		jQuery(".value-lastname1").html(jQuery("input[name='lastname1']").val());
		jQuery(".value-mobile1").html(jQuery("input[name='mobile1']").val());
		jQuery(".value-email1").html(jQuery("input[name='email1']").val());
		jQuery(".value-address1").html(jQuery("input[name='address1']").val());
		jQuery(".value-suburb1").html(jQuery("input[name='suburb1']").val());
		jQuery(".value-postcode1").html(jQuery("input[name='postcode1']").val());
		jQuery(".value-usi1").html(jQuery("input[name='usi1']").val());
		jQuery(".value-workplace1").html(jQuery("input[name='workplace1']").val());
		jQuery(".value-additionalinfo1").html(jQuery("input[name='additionalinfo1']").val());
	}
	
	if(currentPersonQty==2 || currentPersonQty == 3){
		jQuery(".confirm-person2").show();
		jQuery(".value-firstname2").html(jQuery("input[name='firstname2']").val());
		jQuery(".value-lastname2").html(jQuery("input[name='lastname2']").val());
		jQuery(".value-mobile2").html(jQuery("input[name='mobile2']").val());
		jQuery(".value-email2").html(jQuery("input[name='email2']").val());
		jQuery(".value-address2").html(jQuery("input[name='address2']").val());
		jQuery(".value-suburb2").html(jQuery("input[name='suburb2']").val());
		jQuery(".value-postcode2").html(jQuery("input[name='postcode2']").val());
		jQuery(".value-usi2").html(jQuery("input[name='usi2']").val());
		jQuery(".value-workplace2").html(jQuery("input[name='workplace2']").val());
		jQuery(".value-additionalinfo2").html(jQuery("input[name='additionalinfo2']").val());
	}

	if(currentPersonQty==3){
		jQuery(".confirm-person3").show();
		jQuery(".value-firstname3").html(jQuery("input[name='firstname3']").val());
		jQuery(".value-lastname3").html(jQuery("input[name='lastname3']").val());
		jQuery(".value-mobile3").html(jQuery("input[name='mobile3']").val());
		jQuery(".value-email3").html(jQuery("input[name='email3']").val());
		jQuery(".value-address3").html(jQuery("input[name='address3']").val());
		jQuery(".value-suburb3").html(jQuery("input[name='suburb3']").val());
		jQuery(".value-postcode3").html(jQuery("input[name='postcode3']").val());
		jQuery(".value-usi3").html(jQuery("input[name='usi3']").val());
		jQuery(".value-workplace3").html(jQuery("input[name='workplace3']").val());
		jQuery(".value-additionalinfo3").html(jQuery("input[name='additionalinfo3']").val());
	}
	
	
	jQuery(".enrolmain-validationerror").hide();
	
	if(currentPersonQty==1 || currentPersonQty == 2 || currentPersonQty == 3){
		if(jQuery("input[name='firstname1']").val()==""){ elementselector = ".enrolmain-validationerror.en_firstname1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
		if(jQuery("input[name='lastname1']").val()==""){ elementselector = ".enrolmain-validationerror.en_lastname1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
		if(jQuery("input[name='mobile1']").val()==""){ elementselector = ".enrolmain-validationerror.en_mobile1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
		if(jQuery("input[name='email1']").val()=="" || !isValidEmailAddress(jQuery("input[name='email1']").val())){ elementselector = ".enrolmain-validationerror.en_email1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
		if(jQuery("input[name='address1']").val()==""){ elementselector = ".enrolmain-validationerror.en_address1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
		if(jQuery("input[name='suburb1']").val()==""){ elementselector = ".enrolmain-validationerror.en_suburb1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
		if(jQuery("input[name='postcode1']").val()==""){ elementselector = ".enrolmain-validationerror.en_postcode1"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
	}
	if(currentPersonQty==2 || currentPersonQty == 3){
		if(jQuery("input[name='firstname2']").val()==""){ elementselector = ".enrolmain-validationerror.en_firstname2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
		if(jQuery("input[name='lastname2']").val()==""){ elementselector = ".enrolmain-validationerror.en_lastname2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
		if(jQuery("input[name='mobile2']").val()==""){ elementselector = ".enrolmain-validationerror.en_mobile2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
		if(jQuery("input[name='email2']").val()=="" || !isValidEmailAddress(jQuery("input[name='email2']").val())){ elementselector = ".enrolmain-validationerror.en_email2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
		if(jQuery("input[name='address2']").val()==""){ elementselector = ".enrolmain-validationerror.en_address2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
		if(jQuery("input[name='suburb2']").val()==""){ elementselector = ".enrolmain-validationerror.en_suburb2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
		if(jQuery("input[name='postcode2']").val()==""){ elementselector = ".enrolmain-validationerror.en_postcode2"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(2); return false; }
	}
	if(currentPersonQty==3){
		if(jQuery("input[name='firstname3']").val()==""){ elementselector = ".enrolmain-validationerror.en_firstname3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
		if(jQuery("input[name='lastname3']").val()==""){ elementselector = ".enrolmain-validationerror.en_lastname3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
		if(jQuery("input[name='mobile3']").val()==""){ elementselector = ".enrolmain-validationerror.en_mobile3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
		if(jQuery("input[name='email3']").val()=="" || !isValidEmailAddress(jQuery("input[name='email3']").val())){ elementselector = ".enrolmain-validationerror.en_email3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
		if(jQuery("input[name='address3']").val()==""){ elementselector = ".enrolmain-validationerror.en_address3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
		if(jQuery("input[name='suburb3']").val()==""){ elementselector = ".enrolmain-validationerror.en_suburb3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
		if(jQuery("input[name='postcode3']").val()==""){ elementselector = ".enrolmain-validationerror.en_postcode3"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(3); return false; }
	}
	
	if(!jQuery("input[name='tacagree']").is(':checked')){ elementselector = ".enrolmain-validationerror.en_tacagree"; jQuery(elementselector).show(); scrollToElement(elementselector); editmultiperson(1); return false; }
	
	
}


function enrollSelect(courseID){
		 jQuery('.axcelerateCourseID').val(courseID);
		 
		 var courseqty = jQuery("#seatreq_"+courseID+"").val();
		 
		 jQuery('.axcelerateCourseQTY').val(courseqty);
		 
		 if(courseqty=="1"){
		 			miniMultiEnrolNow(1);
		 }
		 if(courseqty=="2"){
		 			miniMultiEnrolNow(2);
		 }
		 if(courseqty=="3"){
		 			miniMultiEnrolNow(3);
		 }
		 if(courseqty=="4"){
		 			//miniEnrolNow();
		 			document.location="/contact/";
		 }
		 //console.log(courseqty);
   	//
}
function datetoms(inp){
   	
   		var d = new Date(inp);
		var seconds = d.getTime() / 1000;
		
		return parseInt(seconds);
   	
}
   
function getMonthFullName(inp){
   	
   		var monthNames = ["January", "February", "March", "April", "May", "June",
  		"July", "August", "September", "October", "November", "December"
		];
		
		
		return  monthNames[inp];
   	
}

var currentStep = 1;
function goNextStep(){
	
	if(copydetailstoconfirm()==false){
		showEnrolSections(1);
		return false;
	} else {
	
		if(currentStep==1){
			showEnrolSections(2);
		} else if(currentStep==2){
			showEnrolSections(3);
		} else if(currentStep==3){
			//We cant go forward.. Reset step 3?
			showEnrolSections(3);
		}
	}
}
function goBackStep(){
	if(currentStep==1){
		//We cant go back.. Reset?
		showEnrolSections(1);
	} else if(currentStep==2){
		showEnrolSections(1);
	} else if(currentStep==3){
		showEnrolSections(2);
	}
}
var canAdjust = true;
function showEnrolSections(sectionToShow){
	if(canAdjust){
		canAdjust = false;
		//alert(currentStep);
		jQuery(".enrol-section-1").hide(500);
		jQuery(".enrol-section-2").hide(500);
		jQuery(".enrol-section-3").hide(500);
		
		jQuery(".enrol-section-"+sectionToShow).show(500);
		currentStep = sectionToShow;
		
		if(sectionToShow==3){
			jQuery(".enrolNextbutton.nextButton").hide();
			jQuery(".enrol-now-button-large").show();
			
		} else {
			jQuery(".enrolNextbutton.nextButton").show();
			jQuery(".enrol-now-button-large").hide();
			
		}
		canAdjust = true;
	}
}


function showAllEnrolSections(){
	jQuery(".enrol-section-1").show();
	jQuery(".enrol-section-2").show();
	jQuery(".enrol-section-3").show();
	jQuery(".enrolNextbutton").hide();
}

function loadCourseDataToSidebar(courseID){
	if(courseID==""){
		return false;
	}
	courseData = jQuery.parseJSON(courseData);
	
	jQuery(".enrol-coursename").html(courseData.websiteName);
	jQuery(".enrol-coursedate").html(courseData.courseDate);
	jQuery(".enrol-coursetime").html(courseData.courseTimings);
	jQuery(".enrol-courselocation").html(courseData.courseLocation);
	jQuery(".enrol-coursecost").html("$"+courseData.courseTotalCost);
	
	if(courseData.promotion==""){
		jQuery(".promotionname").hide();
	} else {
		jQuery(".promotionname").show();
		jQuery(".enrol-promotion").html(courseData.promotion);	
	}
	
	
	
	jQuery("#enrol-courseID").val(courseID);
	
	//Data to control functions based on coupons
	//courseData.allowOfflinePayments
	
	//courseData.forceOfflinePayments
	
	if(courseData.frcEmp=="0"){
		
	} else {

		jQuery("input[name='workplace']").val(courseData.frcEmp);
		jQuery("input[name='workplace']").after('<input type="text" disabled="disabled" name="false_workplace" placeholder="Workplace" value="'+courseData.frcEmp+'">');
		jQuery("input[name='workplace']").attr("type","hidden");
	}
	
	if(courseData.dontInvoiceUser=="1"){
		jQuery(".payment-type-wrapper").hide();
		
		jQuery(".payment-type-employer").show();
		jQuery(".payment-creditcard").hide();
		jQuery(".payment-other").hide();
		
		jQuery(".enrol-coursecost").html("Paid for by your Employer");
		
		jQuery("#payment_employer").attr("checked","checked");
		jQuery(".payment-type-employer").show( 500 );
	} else {
		jQuery(".payment-type-employer").hide();
		jQuery("#payment_cc_rb").attr("checked","checked");
		jQuery(".payment-type-card").show( 500 );
	}
	
}
/*
function miniEnrolNow(){
	
	openCouresSelector();
	return;
	var courseID = jQuery(".axcelerateCourseID").val();
	if(courseID){ 
		//alert(courseID + campaignstring_sub);
		//alert("http://awfa.vbz.com.au/enrol-now/?cid="+courseID + campaignstring_sub);
		
		//document.location = "https://awfa.dev.h1.whm.vbz.com.au/enrol-now/?cid="+courseID + campaignstring_sub;
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
*/

function miniMultiEnrolNow(qty){
	var courseID = jQuery(".axcelerateCourseID").val();
	if(courseID){ 
		//alert(courseID + campaignstring_sub);

		document.location = "https://www.australiawidefirstaid.com.au/enrol-now-multi/?qty="+qty+"&cid="+courseID + campaignstring_sub;
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
	return true;
}

var processingEnrolment = false;
function enrolNow(){
	
	if(processingEnrolment){
		console.log("Already processing enrolment...");
		return false;
	} else {
		processingEnrolment = true;
	}
	startLoading();
	
	showAllEnrolSections();
	
	jQuery(".enrolmain-validationerror").hide();
	var en_courseid      = jQuery("input[name='enrol-courseID']").val();
	
	//Validate enrol now form. If that pases, submit info to server for processing :)
	var en_firstname1      = jQuery("input[name='firstname1']").val();
	var en_lastname1       = jQuery("input[name='lastname1']").val();
	var en_mobile1         = jQuery("input[name='mobile1']").val();
	var en_email1          = jQuery("input[name='email1']").val();
	var en_address1        = jQuery("input[name='address1']").val();
	var en_suburb1         = jQuery("input[name='suburb1']").val();
	var en_postcode1       = jQuery("input[name='postcode1']").val();
	var en_usi1            = jQuery("input[name='usi1']").val();
	var en_workplace1      = jQuery("input[name='workplace1']").val();  //NO VALIDATION
	var en_source1         = jQuery("select[name='source1']").val(); //NO VALIDATION
	var en_additionalinfo1 = jQuery("textarea[name='additionalinfo1']").val(); //NO VALIDATION
	
	var en_firstname2      = jQuery("input[name='firstname2']").val();
	var en_lastname2       = jQuery("input[name='lastname2']").val();
	var en_mobile2         = jQuery("input[name='mobile2']").val();
	var en_email2          = jQuery("input[name='email2']").val();
	var en_address2        = jQuery("input[name='address2']").val();
	var en_suburb2         = jQuery("input[name='suburb2']").val();
	var en_postcode2       = jQuery("input[name='postcode2']").val();
	var en_usi2            = jQuery("input[name='usi2']").val();
	var en_workplace2      = jQuery("input[name='workplace2']").val();  //NO VALIDATION
	var en_source2         = jQuery("select[name='source2']").val(); //NO VALIDATION
	var en_additionalinfo2 = jQuery("textarea[name='additionalinfo2']").val(); //NO VALIDATION
	
	var en_firstname3      = jQuery("input[name='firstname3']").val();
	var en_lastname3       = jQuery("input[name='lastname3']").val();
	var en_mobile3         = jQuery("input[name='mobile3']").val();
	var en_email3          = jQuery("input[name='email3']").val();
	var en_address3        = jQuery("input[name='address3']").val();
	var en_suburb3         = jQuery("input[name='suburb3']").val();
	var en_postcode3       = jQuery("input[name='postcode3']").val();
	var en_usi3            = jQuery("input[name='usi3']").val();
	var en_workplace3      = jQuery("input[name='workplace3']").val();  //NO VALIDATION
	var en_source3         = jQuery("select[name='source3']").val(); //NO VALIDATION
	var en_additionalinfo3 = jQuery("textarea[name='additionalinfo3']").val(); //NO VALIDATION
	
	
	
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
	
	var en_campaign = jQuery("input[name='campaign']").val();
	var confirmdetails = false;
	
	if(confirmdetails==true){
	if(!en_firstname){ elementselector = ".enrolmain-validationerror.en_firstname"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_lastname){ elementselector = ".enrolmain-validationerror.en_lastname"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_mobile){ elementselector = ".enrolmain-validationerror.en_mobile"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_email || !isValidEmailAddress(en_email)){ elementselector = ".enrolmain-validationerror.en_email"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_address){ elementselector = ".enrolmain-validationerror.en_address"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_suburb){ elementselector = ".enrolmain-validationerror.en_suburb"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_postcode){ elementselector = ".enrolmain-validationerror.en_postcode"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_tacagree.is(':checked')){ elementselector = ".enrolmain-validationerror.en_tacagree"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	if(!en_paymenttype.is(':checked')){ elementselector = ".enrolmain-validationerror.en_paymenttype"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	}

	if(en_paymenttype.val()=="payment-card"){
		//console.log(en_paymenttype.val());
		if(!en_cardnumber){ elementselector = ".enrolmain-validationerror.en_cardnumber"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
		if(!en_cardpostcode){ elementselector = ".enrolmain-validationerror.en_cardpostcode"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
		if(!en_cardccv){ elementselector = ".enrolmain-validationerror.en_cardccv"; jQuery(elementselector).show(); scrollToElement(elementselector); cancelLoading(); return false; }
	}
	
	if(en_paymentvalue=="payment-other"){
		if(!en_otherpaymentselection || en_otherpaymentselection=="Other Payment Options"){ elementselector = ".enrolmain-validationerror.en_otherpaymentselection"; jQuery(elementselector).show(); scrollToElement(elementselector);  cancelLoading(); return false; }
	}
	
	
	
	var pData = {usertotal:currentPersonQty, fname1:en_firstname1, lname1:en_lastname1, mobile1:en_mobile1, email1:en_email1, workplace1:en_workplace1, source1:en_source1, special_needs1:en_additionalinfo1, address1:en_address1, suburb1:en_suburb1, postcode1:en_postcode1, fname2:en_firstname2, lname2:en_lastname2, mobile2:en_mobile2, email2:en_email2, workplace2:en_workplace2, source2:en_source2, special_needs2:en_additionalinfo2, address2:en_address2, suburb2:en_suburb2, postcode2:en_postcode2, fname3:en_firstname3, lname3:en_lastname3, mobile3:en_mobile3, email3:en_email3, workplace3:en_workplace3, source3:en_source3, special_needs3:en_additionalinfo3, address3:en_address3, suburb3:en_suburb3, postcode3:en_postcode3, terms:en_tacagree.val(), payment:en_paymentvalue, cc:en_cardnumber,expiryM:en_cardexpirymonth,expiryY:en_cardexpiryyear,cvv:en_cardccv, opt_in:en_sendreminders, courseid:en_courseid, campaign:en_campaign, otherPtype:en_otherpaymentselection, usi1:en_usi1, usi2:en_usi2, usi3:en_usi3, crq:globalCurrentCost};
	
	jQuery(".enrol-section-1").hide(200);
	jQuery(".enrol-section-2").hide(200);
	
	//console.log(pData);
	
	jQuery.post(baseHTTSpath+"/?rq=multienrol", pData, function(rdata){
	    //console.log(rdata); // check your console, you should see some output
	    
	    respData = jQuery.parseJSON(rdata);

	    if(respData.result){
	    	
					enrolmentCheckStatus(respData.sessionKey);
			  
	    } else {
	    	jQuery(".enrol-section-1").show(200);
			  jQuery(".enrol-section-2").show(200);
			
	    	jQuery(".enrol-error-text").html(respData.error_message);
	    	jQuery(".enrol-error-message").show(600);

			  scrollToElement(".enrol-error-message");
			  
			  $('a[rel*=leanModal]').leanModal({ top : 200, overlay : 0.4, closeButton: ".modal_close" });
			  $("#modaltriger").trigger("click");
			    
	    	cancelLoading();
	    	window.setTimeout("scrollToElement('.enrol-error-message');",1000);
	    	
	    	return false;
	    }
	    
	    
	    processingEnrolment = false;
	    
	});
	
	
}

function close_modaled(modelid){
	//jQuery("#"+modelid).hide(300);
	
}

function enrolmentCheckStatus(groupID){
	autoreload = true;
	pData = "";
	
	jQuery.post(baseHTTSpath+"/?rq=checkenrolmentstatus&enrolmentkey="+groupID, pData, function(rdata){
		
		var response=jQuery.parseJSON(rdata);
		if(typeof response =='object' && response!=""){
	
	
			respData = response;
			
			//console.log(respData.statuscode);
	
			
			
			if(respData.statuscode==98 || respData.statuscode==0){
				// Enrolment details saved. enrolling in course.
				jQuery(".progress-label.mainmessage").html("Starting Enrolment... Step 1 of 4");
			}
			if(respData.statuscode==1){
				// Enrolled in course, confirming payment details
				jQuery(".progress-label.mainmessage").html("Creating Your Account... Step 1 of 4");
			}
			if(respData.statuscode==2){
				// Payment details confirmed, awaiting finalisation.
				jQuery(".progress-label.mainmessage").html("Finalising Account Creation... Step 1 of 4");
			}
			if(respData.statuscode==3){
				// Payment details confirmed, awaiting finalisation.
				jQuery(".progress-label.mainmessage").html("Enrolling in Course... Step 2 of 4");
			}
			if(respData.statuscode==4){
				// Payment details confirmed, awaiting finalisation.
				jQuery(".progress-label.mainmessage").html("Finalising Course Enrolment... Step 2 of 4");
			}
			if(respData.statuscode==5){
				// Payment details confirmed, awaiting finalisation.
				jQuery(".progress-label.mainmessage").html("Processing your Payment... Step 3 of 4");
			}
			if(respData.statuscode==6){
				// Payment details confirmed, awaiting finalisation.
				jQuery(".progress-label.mainmessage").html("Finalising Payment Processing... Step 3 of 4");
			}
			if(respData.statuscode==7){
				// Payment details confirmed, awaiting finalisation.
				jQuery(".progress-label.mainmessage").html("Finalising Enrolment... Step 4 of 4");
			}
			if(respData.statuscode==8){
				// Order Completed Success Fully
				
				jQuery(".progress-label.mainmessage").html("Enrolment Completed. Loading...");
				
				scrollToElement(".enrol-error-message");
				document.location = "/enrol-now/thank-you/?cid="+respData.en_courseid+"&t="+respData.txnid+"&inv="+respData.invoice+"&campaign="+respData.en_campaign;
				
				autoreload = false;
				return false;
			}
			
			if(respData.statuscode==99){
				// Order in Error, Return error to user.
				jQuery(".enrol-section-1").show(200);
			  	jQuery(".enrol-section-2").show(200);
			
	    		jQuery(".enrol-error-text").html(respData.error_message);
	    		jQuery(".enrol-error-message").show(600);
	
			  	scrollToElement(".enrol-error-message");
			  
			  	jQuery('a[rel*=leanModal]').leanModal({ top : 200, overlay : 0.4, closeButton: ".modal_close" });
				jQuery("#modaltriger").trigger("click");
			
	    		cancelLoading();
	    		window.setTimeout("scrollToElement('.enrol-error-message');",1000);
	    		autoreload = false;
	    		return false;
			}
		}
		if(autoreload){
			window.setTimeout("enrolmentCheckStatus('"+groupID+"');",2000);
		}
		
		
	});
}
function startLoading(){
	jQuery(".enrol-now-button-large").addClass("loading");
	jQuery("#enrolloading").show(200);
	showLoaderBar();
}
function cancelLoading(){
	processingEnrolment = false;
	jQuery(".enrol-now-button-large").removeClass("loading");
	jQuery("#enrolloading").hide(200);
	hideLoaderBar();
}
function scrollToElement(elementselector){
	jQuery('html, body').animate({
        scrollTop: jQuery(elementselector).offset().top-80
    }, 800);
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
	campaignstring = "&campaign="+campaignstring_self;
	campaignstring_sub = "&campaign="+campaignstring_self;
	addCampaignToBookingForm(campaignstring_self);
	
	return true;
}

function addCampaignToBookingForm(campaignstring_self){
	jQuery.get(baseHTTSpath + "?rq=getCampaignDetailsPUB&rd=" + campaignstring, function(data) {
		
        //retCampData = jQuery.parseJSON(data);
        //console.log(retData);
        if(data){

			campaignName = data[0]['Name'];
			defaultLocation = data[0]['defaultLocation'];
			
			jQuery(".firstaid-minibookingform-formwrapper").prepend("<div class='enrol-mini-promo'><label>Promotion <div><b>"+campaignName+"</b><br><br></div></label></div>");
			
			if(defaultLocation){
				setLocationWithDefault(defaultLocation);
			} else {
				LocationMerge();
			}
			
        }
	});
	
}

function setLocationWithDefault(defaulLocation){
	
	jQuery("#sidebar .firstaid-minibookingform-formwrapper .enrol-mini-state").hide();
	jQuery("#homepage_form .firstaid-minibookingform-formwrapper .enrol-mini-state").hide();
	jQuery.get(baseHTTSpath + "?rq=getCourseLocations&rd=" + campaignstring +"&defaultLocation="+defaulLocation, function(data) {
		
		//jQuery("#sidebar .firstaid-minibookingform-formwrapper .axcelerateLocation").html(data);
		
		jQuery("#sidebar .firstaid-minibookingform-formwrapper .axcelerateLocation").html(data);
		jQuery("#homepage_form .firstaid-minibookingform-formwrapper .axcelerateLocation").html(data);
		jQuery(".courseSelectionWrapper .axcelerateLocation").html(data);
		jQuery('.axcelerateLocation optgroup:eq(0)').before('<option>Select Location</option>');
        jQuery(".axcelerateLocation").val(jQuery(".axcelerateLocation option:first").val());

	});
	
	jQuery("#homepage_form").addClass("simpleLocation");
	
	jQuery(".firstaid-minibookingform").addClass("simpleLocation");
	
	
	
	jQuery("#sidebar .firstaid-minibookingform-formwrapper .enrolbutton").parent().append("<img class='secure' src='/wp-content/uploads/2014/06/secure.png'>");
	jQuery("#homepage_form .firstaid-minibookingform-formwrapper .enrolbutton").parent().append("<img class='secure' src='/wp-content/uploads/2014/06/secure.png'>");
	
	//jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(2)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(3)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(4)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(5)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(6)").hide();
	
	
}

function LocationMerge(){
	
	jQuery(".firstaid-minibookingform-formwrapper .enrol-mini-state").hide();
	//jQuery(".firstaid-minibookingform-formwrapper .enrol-mini-state").hide();
	jQuery.get(baseHTTSpath + "?rq=getCourseLocations&rd=" + campaignstring, function(data) {
		
		jQuery(".firstaid-minibookingform-formwrapper .axcelerateLocation").html(data);
		//jQuery("#homepage_form .firstaid-minibookingform-formwrapper .axcelerateLocation").html(data);
		jQuery(".courseSelectionWrapper .axcelerateLocation").html(data);
		jQuery(".courseSelectionWrapper .overaxcelerateLocation").html(data);
		jQuery('.axcelerateLocation optgroup:eq(0)').before('<option>Select Location</option>');
        jQuery(".axcelerateLocation").val(jQuery(".axcelerateLocation option:first").val());
        
		
	});
	
	jQuery("#homepage_form").addClass("simpleLocation");
	jQuery(".firstaid-minibookingform").addClass("simpleLocation");
	
	
	jQuery(".firstaid-minibookingform-formwrapper .enrolbutton").parent().append("<img class='secure' src='/wp-content/uploads/2014/06/secure.png'>");
	//jQuery("#homepage_form .firstaid-minibookingform-formwrapper .enrolbutton").parent().append("<img class='secure' src='/wp-content/uploads/2014/06/secure.png'>");
	
	//jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(2)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(3)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(4)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(5)").hide();
	jQuery(".firstaid-minibookingform-formwrapper .secure:nth-child(6)").hide();
	
	
}

jQuery(".manager-nextstep").on("click", function($){
	
	if(totalmultipersons==1){
		
	}
	if(totalmultipersons==2){
		
	}
	if(totalmultipersons==3){
		
	}
	
	
});


function setmultienrolup(startqty){
	totalmultipersons = startqty;
	if(startqty==1){
		jQuery(".manager-person1").show();
		jQuery(".manager-person2").hide();
		jQuery(".manager-person3").hide();
		jQuery(".manager-addperson").show();
	}
	if(startqty==2){
		jQuery(".manager-person1").show();
		jQuery(".manager-person2").show();
		jQuery(".manager-person3").hide();
		jQuery(".manager-addperson").show();
		
	}
	if(startqty==3){
		jQuery(".manager-person1").show();
		jQuery(".manager-person2").show();
		jQuery(".manager-person3").show();
		jQuery(".manager-addperson").hide();
		

	}
	
	jQuery(".booking-person1").show();
	jQuery(".booking-person2").hide();
	jQuery(".booking-person3").hide();
	
	jQuery(".manager-singleperson").removeClass("manager-person-selected");
	jQuery(".manager-person1").addClass("manager-person-selected");
	
	//console.log("Course Total: "+courseData['courseTotalCost']);
	//console.log("Total Persons: "+totalmultipersons);
	
	
	
	currentCost = courseData['courseTotalCost'] * totalmultipersons;
	//console.log("Total: "+currentCost);
	currentCost = Math.round(currentCost * 100) / 100;
	
	jQuery(".enrol-coursecost").html("$"+courseData['courseTotalCost']+" Per Person");
	
	jQuery(".enrol-coursecost-total").html("$"+currentCost);
	
	currentPersonQty = startqty;
	globalCurrentCost = currentCost;
	
}

function editmultiperson(selectedid){
	
	if(currentPersonQty<selectedid){
		return false;
	}
	
	jQuery(".booking-person1").hide();
	jQuery(".booking-person2").hide();
	jQuery(".booking-person3").hide();
	
	jQuery(".booking-person"+selectedid).show(300);
	
	jQuery(".manager-singleperson").removeClass("manager-person-selected");
	jQuery(".manager-person"+selectedid).addClass("manager-person-selected");
	
	
}


function addperson(){
	
	if(currentPersonQty==3){
			//Max 3 person enrolment enforced.
	} else {
		newpersonqty = parseInt(currentPersonQty)+1;
		setmultienrolup(newpersonqty);
	}
	
	
	
}

function removeperson(personID){
	
	//editmultiperson(personID-1);
	
	if(personID==2){
			//merge person 3 down to person 2.
		if(currentPersonQty==3){
			jQuery("input[name='firstname2']").val(jQuery("input[name='firstname3']").val());
			jQuery("input[name='lastname2']").val(jQuery("input[name='lastname3']").val());
			jQuery("input[name='mobile2']").val(jQuery("input[name='mobile3']").val());
			jQuery("input[name='email2']").val(jQuery("input[name='email3']").val());
			jQuery("input[name='address2']").val(jQuery("input[name='address3']").val());
			jQuery("input[name='suburb2']").val(jQuery("input[name='suburb3']").val());
			jQuery("input[name='postcode2']").val(jQuery("input[name='postcode3']").val());
			jQuery("input[name='usi2']").val(jQuery("input[name='usi3']").val());
			jQuery("input[name='workplace2']").val(jQuery("input[name='workplace3']").val());
			jQuery("select[name='source2']").val(jQuery("select[name='source3']").val());
			jQuery("textarea[name='additionalinfo2']").html(jQuery("textarea[name='additionalinfo3']").html());
			
			jQuery("input[name='firstname3']").val("");
			jQuery("input[name='lastname3']").val("");
			jQuery("input[name='mobile3']").val("");
			jQuery("input[name='email3']").val("");
			jQuery("input[name='address3']").val("");
			jQuery("input[name='suburb3']").val("");
			jQuery("input[name='postcode3']").val("");
			jQuery("input[name='usi3']").val("");
			jQuery("input[name='workplace3']").val("");
			jQuery("select[name='source3']").val("");
			jQuery("textarea[name='additionalinfo3']").html("");
			
			setmultienrolup(2);
		} else {
			setmultienrolup(1);
		}
	} else {
		
			jQuery("input[name='firstname3']").val("");
			jQuery("input[name='lastname3']").val("");
			jQuery("input[name='mobile3']").val("");
			jQuery("input[name='email3']").val("");
			jQuery("input[name='address3']").val("");
			jQuery("input[name='suburb3']").val("");
			jQuery("input[name='postcode3']").val("");
			jQuery("input[name='usi3']").val("");
			jQuery("input[name='workplace3']").val("");
			jQuery("select[name='source3']").val("");
			jQuery("textarea[name='additionalinfo3']").html("");
			
			setmultienrolup(2);
			
	}
	
	
}


function openDropdown(elementId) {
    jQuery(elementId).focus().focus();
}