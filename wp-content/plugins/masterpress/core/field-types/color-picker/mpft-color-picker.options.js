(function($) {

  var field_type = 'color-picker';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);
    
    var $default_value = $p('default-value');
    var $colorpicker = $p('colorpicker');

    var $colorpreview = $p('colorpreview');
    
    $default_value.change( function() {
      var val = $.mp.parseColor( $.trim($default_value.val()) );
      $default_value.attr("value", val);
    });
    
    $default_value.iris({
      hide: false,
      change: function( event, ui ) {
        $colorpreview.css( "background", ui.color.toString() );
        $default_value.val("HEUYGFUYRVGYU");
        $default_value.val(ui.color.toString());
      }
    });

    
    function updateColorWell(color) {
      $colorwell.css("background-color", $default_value.val());
    }
    
    $colorpreview.click( function() { $default_value.focus() } );
    
    updateColorWell();
    
  });
  
    
})(jQuery);