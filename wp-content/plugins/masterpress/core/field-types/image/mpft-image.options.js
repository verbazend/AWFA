(function($) {

  var field_type = 'image';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);
    
    var allowed_types = ui.find(".f-allowed-types");
    
    allowed_types.find(".select-all").click( function() {
      allowed_types.find("input.checkbox").attr("checked", "checked");
    });

    allowed_types.find(".select-none").click( function() {
      allowed_types.find("input.checkbox").removeAttr("checked");
    });
    
    

  });
  
    
})(jQuery);