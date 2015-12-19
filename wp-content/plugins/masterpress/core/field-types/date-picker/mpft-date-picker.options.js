(function($) {

  var field_type = 'date-picker';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);
    
    ui.find("input.date").each( function() {

      
      var $el = $(this);
      var md = $el.metadata({ type: 'class' });
    
      var options = $.extend(true, {}, {
        showOn: "button",
        constrainInput: false,
        showOtherMonths: true,
        dateFormat: "dd M yy"
      }, md );
      
      $el.datepicker(options);

			$el.datepicker('widget').addClass("wp-dialog aristo");

      
      /*
      $p('mindate').datepicker("option", "onSelect", function(dateText, inst) {
        
        var date = $.datepicker.parseDate("dd M yy", dateText);
        
        if (date && date.addDays) {
          var maxdate = date.addDays(1);
          $p('maxdate').datepicker("option", "minDate", maxdate);
        }
        
      });
      */
  
    });
  
  });
  
    
})(jQuery);