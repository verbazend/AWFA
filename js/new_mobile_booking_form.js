$(document).ready(function(){
	$('.updateState').change(function(){
		var state = $(".updateState :selected").text();
		$.ajax({
		    url:"/new_updateform.php?id=state",
			type:"post",
			data:'state='+ state,
			dataType: "html",
			success:function(data){
			$(".updateLocations").html(data);
			}
		});
		$(".updateDate").val('Select Date');
		$(".updateDate").datepicker( "destroy" );
	});
});

$(document).ready(function(){
	$('.updateLocations').change(function(){
		var location = $(".updateLocations :selected").val();
		$.ajax({
	    url:"/new_updateform.php?id=location",
		type:"post",
		data:'location='+ location,
		dataType: "html",
		success:function(data){
		$(".updateCourse").html(data);
		}
	});
	$(".updateDate").val('Select Date');
		$(".updateDate").datepicker( "destroy" );
	});
});

$('#datepicker').change(function(){
	$('#sidebarsubmit').removeAttr('disabled')
});

var datepickerDateFilter = function (date) {
	var month = date.getMonth()+1;
	var day = date.getDate();
	var date_string = date.getFullYear()+'-'+(month < 10 ? '0' : '')+month+'-'+(day < 10 ? '0' : '')+day;
	if($courseDates.length > 0 && $.inArray(date_string, $courseDates) != -1){
		return [true];
	}
	return [false];
};

var availableDates = {};

function populateAvailableDates(y, m) {
	$.ajax({
        type: 'POST',
		url: '/newdates.php',
		dataType: "json",
		data: { locations_id: $('#location').val(), courses_id: $('#course').val(), year: y, month: m },
		success: function( data ) {
			$courseDates = data.dates;
			$("#datepicker").datepicker("refresh");
		},
		error: function(xhr, textStatus, errorThrown){
			alert('Error retrieving dates');
		},
		beforeSend: function() {
			/* set the spinner bg Image */
			$("#datepicker").css({"background-image":"url(/images/mobile_images/spinner_000000_16px.gif)"});
			$("#datepicker").val("");
		},
		complete: function() {
			/* Hide spinner background here */
			 $("#datepicker").css({"background-image":"none"});
			$("#datepicker").val("Select Date");
		}
    });
}

var currentDate = new Date();

$(document).ready(function(){
	$('.updateCourse').change(function(){
		$("#datepicker").css({"background-image":"url(/images/mobile_images/spinner_000000_16px.gif)"});
		$("#datepicker").val("");
                $('#datepicker').datepicker( "setDate" , null );
		populateAvailableDates(currentDate.getFullYear(), currentDate.getMonth()+1);
		$("#datepicker").datepicker({
			dateFormat: 'dd-mm-yy',
		    beforeShowDay: datepickerDateFilter,
		    onChangeMonthYear: function (y, m) {
		         populateAvailableDates(y, m);
		    },
			complete: function() {
				/* Hide spinner background here */
				 $("#datepicker").css({"background-image":"none"});
				$("#datepicker").val("Select Date");
			}
		});
	});
});