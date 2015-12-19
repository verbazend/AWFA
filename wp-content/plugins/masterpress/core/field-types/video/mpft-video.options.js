(function($) {

  var field_type = 'visual-editor';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var html_editor = $p("html-editor");
    var cm_theme_f = $p("cm-theme-f");


    var toggleCMTheme = function() {
      
      if (html_editor.val() == "cm") {
        cm_theme_f.show(); 
      } else {
        cm_theme_f.hide();
      }

    };
    
    html_editor.change( toggleCMTheme );
    
    toggleCMTheme();
    

  });
  
    
})(jQuery);