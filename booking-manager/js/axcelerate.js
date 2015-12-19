(function($) {
    Drupal.behaviors.axcelerate = {
        attach: function(context) {
            //console.log(context);
            //console.log();
            var datepickerDateFilter = function(date) {
                var month = date.getMonth() + 1;
                var day = date.getDate();
                var date_string = date.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
                console.log(date_string);
                if ($courseDates.data.length > 0 && $.inArray(date_string, $courseDates.data) != -1) {
                    return [true];
                }
                return [false];
            };


            if ($('#edit-payment').length > 0) {
                $('#edit-payment').change(function() {
                    var sVal = $(this).val();
                    console.log(sVal);
                    $('fieldset').css('display', 'none');
                    switch (sVal) {
                        case '1':
                            $('#edit-cc-details').css('display', 'block');
                            break;
                        case '2':
                            $('#edit-invoice-details').css('display', 'block');
                            break;
                    }



                })


            }
            if (jQuery().datepicker) {
                $('#axcelerateCourseDate').datepicker({
                    beforeShowDay: datepickerDateFilter,
                    dateFormat: 'dd-mm-yy',
                    onSelect: function(date) {
                        $.each($courseDates.dates, function(index, value) {
                            if (value == date) {
                                $('#axcelerateCourseID').val(index);
                            }
                        });
                    }
                });
            }






            $('#axcelerateState').change(function() {
                $('#axcelerateCourseID, #axcelerateCourseDate , #axcelerateCourse').val('');
                $.get(Drupal.settings.basePath + "/axcelerate/drill/" + $(this).val(), function(data) {
                    $('#axcelerateLocation').html(data);
                    $('#axcelerateLocation option:eq(0)').before('<option>Select Location</option>');
                    //$('#axcelerateLocation').trigger('change');
                    /*
          	    var count = $('#axcelerateLocation option').length;
          	    if (count == 1){ {
          	    }
          	   
          	    }
          	     */
                    //  alert(count);
                });

            });

            $('#axcelerateLocation').change(function() {
                $('#axcelerateCourseID, #axcelerateCourseDate').val('');
                $.get(Drupal.settings.basePath + "/axcelerate/drill/" + $('#axcelerateState').val() + '/' + $(this).val(), function(data) {
                    $('#axcelerateCourse').html(data);
                    $('#axcelerateCourse option:eq(0)').before('<option>Select Course</option>');
                    //$('#axcelerateCourse').trigger('change');
                    /*
	   var count = $('#axcelerateCourse option').length;
	    if (count == 1){ 
	    
	    }
	    */
                    // $('#axcelerateLocation').html(data);
                });
            });


            $('#axcelerateCourse').change(function() {
                $('#axcelerateCourseID, #axcelerateCourseDate').val('');
                address = Drupal.settings.basePath + "/axcelerate/drill/" + $('#axcelerateState').val() + "/" + $('#axcelerateLocation').val() + '/' + $(this).val();
                $.get(address, function(data) {
                    $courseDates = jQuery.parseJSON(data);
                });
            });
        }
    };

})(jQuery);