(function($) {

  var field_type = 'related-taxonomy';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var $tax_wrap = $p('taxonomies-wrap');

    ui.find(".select-all").click( function() {
      $tax_wrap.find("input[type=checkbox]").attr("checked", "checked");
    });

    ui.find(".select-none").click( function() {
      $tax_wrap.find("input[type=checkbox]").removeAttr("checked");
    });
    
    var control_style_single = $p("control_style_drop_down_list");
    var control_style_multi = $p("control_style_list_box_multiple");
    
    var basic = $p("basic");

    var ml_f = $p("multi_layout-f");
    var csw = $p('control-selections-wrap');
    

    var updateDisplay = function() {
      
      if (control_style_single.is(":checked") || basic.is(":checked")) {
        ml_f.hide();
        csw.hide();
      } else {
        ml_f.show();
        csw.show();
      }
    };
    
    control_style_single.click( updateDisplay );
    control_style_multi.click( updateDisplay );
    basic.change( updateDisplay );    
    updateDisplay();
    
  });
  
    
})(jQuery);