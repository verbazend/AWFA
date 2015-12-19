(function($) {

  var field_type = 'radio-button-list';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var $values = $p('values');
    
    ui.find('.checkbox-alt-label').checkboxAltLabel();

    var $preview = ui.find(".preview");
    var $controls = $p('default-value-controls');
    var $default_value_f = $p('default-value-f');
    
    $values
      .autoResize({ extraSpace: 12, animateDuration : 0, limit: 300 })
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
      
        var $elements = $.wh.input_radio_group( 
          "type_options[default_value]", 
          p("default-value"), 
          option_values, 
          $.wh.checked_values( $preview.find("input") ), 
          '<div class="fwi"></div>'
        );
            
        $preview.html("").append( $elements );

      }
      
      
    }
    
    update_preview();
    
    ui.find("button.uncheck-all").click( function() {
      $preview.find("input:checked").removeAttr("checked");
    });
    
    
    $values.keyup( update_preview );

  });
    
    
})(jQuery);