(function($) {

  var field_type = 'drop-down-list';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);
    var $prompting_label = $p('prompting-label');
    var $values = $p('values');
    var $default_value = $p('default-value');
    var $default_value_f = $p('default-value-f');
    
    var $preview = ui.find(".preview");
    var $controls = $p('default-value-controls');


    $values
      .autoResize({ extraSpace: 12, animateDuration : 0, limit: 400 })
      .trigger("change.dynSiz");


    function update_preview() {

      var val = $.trim($values.val());
      
      if ($.trim(val) == "") {
        
        $default_value_f.hide();
                
      } else {
        
        var option_values = $.woof_html.option_values(val);

        if (!$default_value_f.is(":visible")) {
          $default_value_f.fadeIn("normal");
        }
        
        var option_values = $.woof_html.option_values($values.val(), $prompting_label.val() );
        $.wh.select_set_options($default_value, option_values, true);

      }
      
      
    }
     
    update_preview();
       
    
    $values.keyup( update_preview );
    $prompting_label.change( update_preview );
    
      
  });
  
    
})(jQuery);