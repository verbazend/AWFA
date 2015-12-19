(function($) {

  var field_type = 'checkbox-list';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);
    
    var $preview = ui.find(".preview");
    var $values = $p('values');
    var $no_preview = ui.find(".no-preview");
    var $controls = $p('default-value-controls');

    var $default_value_f = $p("default-value-f");

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
        
        var $elements = $.wh.input_checkbox_group( 
          "type_options[default_value][]", 
          p("default-value"), 
          option_values, 
          $.wh.checked_values( $preview.find("input") ), 
          '<div class="fwi"></div>'
        );
      
        $preview.html("").append( $elements );

      }
      
      
    }
    
    $values.keyup( update_preview );

    update_preview();

    ui.find(".select-all").click( function() {
      $preview.find("input[type=checkbox]").attr("checked", "checked");
    });

    ui.find(".select-none").click( function() {
      $preview.find("input[type=checkbox]").removeAttr("checked");
    });


  });
  
    
})(jQuery);