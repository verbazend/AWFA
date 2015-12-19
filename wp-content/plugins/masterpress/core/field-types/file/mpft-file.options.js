(function($) {

  var field_type = 'file';
  
  function array_diff(a, b) {
    return a.filter(function(i) {return !(b.indexOf(i) > -1);});
  };

  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);
  
    $at = $p('allowed-types-wrap');
    $af = $p('allowed_field');
    
    $atc = $p('allowed-types-custom');
    
    $at.find(".select-all").click( function() {
      $(this).closest(".file-category").find("input.checkbox").attr("checked", "checked");
      updateField();
    });

    $at.find(".select-none").click( function() {
      $(this).closest(".file-category").find("input.checkbox").removeAttr("checked");
      updateField();
    });
    
    var $cb = $at.find("input.checkbox");
    
    var all_types = [];
    
    $cb.change( updateField );
    $cb.each( function() {
      all_types.push($(this).val());
    });
    
    function updateField() {
      var types = [];
      
      $at.find("input.checkbox:checked").each( function() {
        types.push($(this).val());
      });

      // retain any custom extenstions not listed in the checkboxes
      var custom = array_diff( array_diff($af.val().split(","), types), all_types );
      
      $af.val(types.concat(custom).join(","));
      
      $atc.empty();
      
      $.each(custom, function(index, val) {
        $atc.append($('<input type="hidden" name="type_options[allowed_types][]" value="' + val + '" />')); 
      });
      
    }
    
    
    function updateCheckboxes() {
      // when the field changes, update the checked state
      
      var selected = $af.val().split(",");
      
      $cb.each( function() {
        
        if ($.inArray($(this).val(), selected) != -1) {
          $(this).attr("checked", "checked");
        } else {
          $(this).removeAttr("checked");
        }
        
      });
      
    }

    $af.keyup(updateCheckboxes);
    
  });
  
    
})(jQuery);