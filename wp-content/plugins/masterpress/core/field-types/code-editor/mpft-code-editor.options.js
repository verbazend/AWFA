(function($) {

  var field_type = 'code-editor';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var $mode = $p('mode');
    var $hiddenmode = $p('hiddenmode');
    
    if ($hiddenmode.length && $hiddenmode.val() != "") {
      var selected = $hiddenmode.val();
    } else {
      var selected = $mode.val();
    }

  });
  
    
})(jQuery);