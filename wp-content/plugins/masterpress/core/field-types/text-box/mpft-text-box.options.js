(function($) {

  var field_type = 'text-box';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var $default_value = $p("default-value");
    var $maxlength = $p("maxlength");
    var $font = $p("font");
    var $maxwidth = $p("maxwidth");
    
    
    var updateDefault = function() {
      var maxlength = $maxlength.val();
      var maxwidth = $maxwidth.val();
      
      if (maxlength && parseInt(maxlength)) {
        $default_value.attr("maxlength", maxlength);
      }

      if (maxwidth && parseInt(maxwidth)) {
        $default_value.css("max-width", Math.max(20, Math.min(580, parseInt(maxwidth))));
      } else {
        if (maxlength && parseInt(maxlength)) {
          $default_value.css("max-width", Math.max(20, Math.min(580, 8 * parseInt(maxlength))));
        }
      
      }

      $default_value.attr("font-family", $font.val());
      
    };
    
    
    $maxlength.change( updateDefault );
    $maxwidth.change( updateDefault );
    $font.change( updateDefault );

    updateDefault();
    
  });
  
    
})(jQuery);