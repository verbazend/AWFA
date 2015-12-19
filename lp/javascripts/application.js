$(document).ready(function() {


if (window.location.host == "localhost:4000") {
  var testEnvironment = false;
} else {
  var testEnvironment = false;
}

$('.totop').affix({
      offset: {
        top: 100
      }
});	

	// Open external links in a new window
	hostname = window.location.hostname
	$("a[href^=http]")
	  .not("a[href*='" + hostname + "']")
	  .addClass('link external')
	  .attr('target', '_blank');


      $("a[data-toggle=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault()
      })

    $(".scroller").click(function (event) {
        event.preventDefault();
        $('html,body').animate({ scrollTop: $(this.hash).offset().top - 20 }, 400);
    });

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
	
	if (testEnvironment == true) {

      $( "#datepicker" ).datepicker({
      	dateFormat: 'dd-mm-yy',
      	defaultDate: "+1w"
      });
      
	} else {
	
	$(function() {
          	$courseDates = [];
							$.ajax({
								type: 'POST',
								url: 'https://www.australiawidefirstaid.com.au/dates-mb.php',
								dataType: "json",
								crossDomain: true,
								data: { locations_id: $('#location').val(), courses_id: $('#course').val() },
								success: function( data ) {
									$courseDates = data.dates;
								},
								error: function(xhr, textStatus, errorThrown){
									alert('Error retrieving dates');
								},
								complete: function() {
									$('#datepicker').datepicker('destroy').datepicker({
										dateFormat: 'dd-mm-yy',
										beforeShowDay: datepickerDateFilter,
										
                    onSelect: function(dateText, inst) { 
                      $("#date").val(dateText);
                      $('#dateform').submit();

                    }
										
									});
								}
							});

					
        });
  }

});

