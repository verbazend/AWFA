$(document).ready(function(){
	var section = "demos/datepicker";

	var $courseDates = [];
	var datepickerDateFilter = function (date) {
		var month = date.getMonth()+1;
		var day = date.getDate();
		var date_string = date.getFullYear()+'-'+(month < 10 ? '0' : '')+month+'-'+(day < 10 ? '0' : '')+day;
		if($courseDates.length > 0 && $.inArray(date_string, $courseDates) != -1){
			return [true];
		}
		return [false];
	};
	
	$(function() {
          $( "#datepicker" ).datepicker({
          	dateFormat: 'dd-mm-yy',
          	beforeShowDay: datepickerDateFilter
          });
          $('#course').change(function(){
            	$courseDates = [];
  							$.ajax({
  								type: 'POST',
  								url: '/dates-mb.php',
  								dataType: "json",
  								data: { locations_id: $('#location').val(), courses_id: $('#course').val() },
  								success: function( data ) {
  									$courseDates = data.dates;
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
  									$('#datepicker').datepicker('destroy').datepicker({
  										dateFormat: 'dd-mm-yy',
  										beforeShowDay: datepickerDateFilter
  									});
  									/* Hide spinner background here */
  	  							  $("#datepicker").css({"background-image":"none"});
  	  								$("#datepicker").val("Select Date");
  								}	
  							});		
            });
					
					$('#address_state').change(function(){
					  alert("hello");
          	$courseDates = [];
							$.ajax({
								type: 'POST',
								url: '/selected_location.php',
								dataType: "html",
								data: { state: $('#address_state').val() },
								success: function( data ) {
									$('#uniform-location span').html('Select Location');
									$('#location').html('');
									$('#location').html(data);
								},
								error: function(xhr, textStatus, errorThrown){
									alert('Error retrieving data');
								}
							});
          });
					
    });
});
