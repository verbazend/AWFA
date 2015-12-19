/*!
  cell plug-in for jQuery, License - MIT, Copyright: 2010 Traversal http://traversal.com.au
*/

/*
  Title: cell plug-in
    
  Description:
    Causes the subject elements to match the height of each other by setting them all to the height of the tallest element in the group.
    Importantly, grow also excepts a "strut" option - a selector defining an inner element which determines the height of all of the elements; this element will generally be the element that most tightly wraps variable length HTML content.
    
  Author Info:
    Created By - Traversal <http://traversal.com.au>
    Licence - MIT Style <http://en.wikipedia.org/wiki/MIT_License>
  
  Requires: 
    jQuery 1.3 - http://jquery.com

  Companion plug-ins:
    Metadata - http://plugins.jquery.com/project/metadata (optional)

*/

(function($) { 

  var defaults = { 
    strut : null, 
    extra: 0, 
    context: null 
  };
    
  var pn = 'cell';

  $.fn[pn] = function() {
    
    var cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    var d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } }
    
    if (!cmd) {
      // initialization (no command)
      el[pn]('update');
    }
    else if (cmd == 'update') {
    
      var getTarget = function(element) {
        var rel = element;
        
        if (o.strut) {
          var inner = element.find(o.strut);
        
          if (inner.length > 0) {
            rel = inner;
          }
        }
        
        return rel;
      };
      
      var getHeight = function(element) {
        return getTarget(element).innerHeight();
      };
      
      
      // first regard the maximum height as the height of this element
      var max = getHeight(el);
      
      // now look through the others to discover the tallest
      el.each( function() {
        
        var oel = $(this);
        var h = getHeight(oel);
        
        if (h > max) {
          max = h;
        }
      });
      
      // now set all other elements to match the tallest height
      el.each( function() {
        var oel = getTarget($(this));
        var pad = ( parseInt(oel.css("padding-top")) || 0 ) + ( parseInt(oel.css("padding-bottom")) || 0 );

        oel.css({height: max - pad + o.extra});
      });
      
      el.trigger(pn + ".afterupdate", [ { height: max } ]);
      
    }
    else if (cmd == 'destroy') {
      // remove event handlers, data, and anything else
      el.data(pn, null);
    }

    return this;
    
  };
  
  $.fn[pn].defaults = defaults;
  
})(jQuery);