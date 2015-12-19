	$(document).ready(function(){
		$("#header .menu").superfish();
		
		$("#slider").nivoSlider({
			captionOpacity:0,
			directionNav:false,
			directionNavHide:false	 
		});
		
		$("#footer .menu li").last().addClass("noborder");
		
		$("#nav .menu li li").last().addClass("bborder");
		$("#nav .menu li li li").last().addClass("bborder");
		$('.scrollPage').click(function() {
		   var elementClicked = $(this).attr("href");
		   var destination = $(elementClicked).offset().top;
		   $("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-20}, 500 );
		   return false;
		});
		//$("select").uniform();
		$(".update2").uniform();
		
	});
	
	/*var section = "demos/datepicker";

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
          	dateFormat: 'yy-mm-dd',
          	beforeShowDay: datepickerDateFilter
          });
          
          $('#location, #course').change(function(){
          	$courseDates = [];
			$.ajax({
				type: 'POST',
				url: '/dates.php',
				dataType: "json",
				data: { locations_id: $('#location').val() },
				success: function( data ) {
					$courseDates = data.dates;
				},
				error: function(xhr, textStatus, errorThrown){
					alert('Error retrieving dates');
				},
				complete: function() {
					$('#datepicker').datepicker('destroy').datepicker({
						dateFormat: 'yy-mm-dd',
						beforeShowDay: datepickerDateFilter
					});
				}
			});
          });
					
					$('#address_state').change(function(){
          	$courseDates = [];
							$.ajax({
								type: 'POST',
								url: 'includes/selected_location.php',
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
					
    });*/