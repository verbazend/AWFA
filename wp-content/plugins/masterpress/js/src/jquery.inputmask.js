/*!
  inputmask plug-in for jQuery, License - MIT, Copyright: 2010 Traversal - <http://traversal.com.au>
*/

/*
  Title: inputmask plug-in
    
  Description:
    Restricts allowed characters in a standard input box or textarea, and allows live conversion of certain characters to other characters. 

  Author Info:
    Created By -  Travis Hensgen <http://traversal.com.au>
    Licence - MIT Style <http://en.wikipedia.org/wiki/MIT_License>
  
  Requires: 
    jQuery 1.3 - http://jquery.com

  Companion plug-ins:
    Metadata - http://plugins.jquery.com/project/metadata (optional)

*/

(function($) { 

  var defaults = {
    reAllow: /[A-Za-z0-9\_\-\s]/,
    convert: { "-" : "_", " " : "_", "&" : "", "%" : "" },
    lowercase: true,
    uppercase: false,
    allowMetaKey: true   // always allow the command key through (mac)  
  };
  
  var pn = 'inputmask';

  $.fn[pn] = function() {
    
    var cmd, ret = this, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    this.each( function() {
      
      var d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } }
      
      if (el.is("input[type=text],input[type=number],input[type=date],textarea")) {
       
        el.keypress( function(event) {

          var result = true;
        
          if (!(event.metaKey && o.allowMetaKey)) {
            if ($.inArray(event.keyCode, o.allowKeys) == -1) {
            
              var chr = String.fromCharCode(event.which);
            
              if (!chr.match(o.reAllow)) {
                result = false;
              }
              
              
            }
          }
          
          if (!result) {
            event.preventDefault();
          }

          return true;
          
        });
        
        
        el.keyup( function(event) {
          
          if ($.inArray(event.keyCode, o.allowKeys) == -1) {
            $.each(o.convert, function(key, val) {
              el.val(el.val().replace(key, val));
            });
          
            if (o.lowercase) {
              el.val(el.val().toLowerCase());
            } else if (o.uppercase) {
              el.val(el.val().toUpperCase());
            }
          }
        
          return true;
          
        });

        el.focus( function(event) {
          d.focusval = el.val();
        });
        
        el.blur( function(event) {
          // fire the change event that Google chrome fails to (for whatever reason!)
          if (el.val() != d.focusval) {
            el.trigger("change");
          }
        });
        
        
        
      }
      
    });
  
    return ret;
    
  };
  
  var ns = $.fn[pn]; // namespace
      

  ns.KEY_DEL = 8;
  ns.KEY_ALT = 18;
  ns.KEY_CMD = 224;
  ns.KEY_ENTER = 13;
  ns.KEY_SHIFT = 16;
  ns.KEY_TAB = 9;
  ns.KEY_RIGHT = 39;
  ns.KEY_UP = 38;
  ns.KEY_DOWN = 40;
  ns.KEY_LEFT = 37;
  
  ns.defaults = defaults;
  ns.defaults.allowKeys = [ ns.KEY_DEL, ns.KEY_SHIFT, ns.KEY_ENTER, ns.KEY_ALT, ns.KEY_TAB, ns.KEY_RIGHT, ns.KEY_LEFT, ns.KEY_UP, ns.KEY_DOWN ]; 
    
})(jQuery);