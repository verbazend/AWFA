(function($) {
  
  var field_type = 'list-box';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var $values = $p('values');
    var $default_value = $p('default-value');
    var $default_value_f = $p('default-value-f');
    var $width = $p('width');
    var $height = $p('height');
    var $allow_multiple = $p('allow-multiple');

    var $no_preview = ui.find(".no-preview");
    var $preview = ui.find(".preview");
    var $controls = $p('default-value-controls');

    
    var $buttons_f = $p('buttons-f');
    var $default_value_controls = $p('default-value-controls');
    
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
      
        var option_values = $.woof_html.option_values($values.val() );
        $.wh.select_set_options($default_value, option_values, true);

      }
      
      
    }
    
    update_preview();
    
    function update_preview_size() {
      
      var height = parseInt($height.val()) || 120;
      var width = parseInt($width.val()) || 400;

      if ($allow_multiple.is(":checked")) {
        $buttons_f.show();
        $default_value_controls.show();
        $default_value.removeAttr("size");
        $default_value.attr("multiple", "multiple");
      } else {
        $buttons_f.hide();
        $default_value_controls.hide();
        $default_value.attr("size", "2");
        $default_value.removeAttr("multiple");
      }
      
      $default_value.css( { width: width, height: height } );
      
    }
    
    $height.change(update_preview_size);
    $width.change(update_preview_size);
    $allow_multiple.click(update_preview_size);
    
    update_preview_size();
    
    ui.find(".select-all").click( function() {
      $default_value.find("option").attr("selected", "selected");
    });

    ui.find(".select-none").click( function() {
      $default_value.find("option").removeAttr("selected");
    });
    
    $values.keyup( function(event) {
      
      if ($.inArray(event.keyCode, $.wh.ARROW_KEYS) == -1) {
        update_preview();
      } 
      
    
    });
    
      
  });
  
    
})(jQuery);