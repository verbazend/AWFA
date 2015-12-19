/*!
  linkify plug-in for jQuery, License - MIT, Copyright: 2010 Traversal - <http://traversal.com.au>
*/

/*
  Title: linkify plug-in
    
  Description:
    Turns any element into a faux clickable link, by reading the regular information stored in an anchor tag from metadata
    or the options hash, and setting up appropriate event handlers.

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
    target : '_self',
    cursor: 'pointer',
    selectorLink: 'a:first',
    selectorNoClosest: "a",
    cn: { hover: 'hover', active: 'active' }
  };
  
  var pn = 'linkify';

  $.fn[pn] = function() {
    
    var cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    return this.each( function() {
      
      var d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } }
      
      if (!cmd) {

        // initialization (no command)
        
        d.href = o.href;
        
        if (!d.href) {
          //  assume the first anchor provides the href
          if (o.selectorLink) {
            d.href = el.find(o.selectorLink).attr('href');
          }
        }
        
          
        if (d.href) {

          d.click = function(event) {
            var anc = $(event.target).closest(o.selectorNoClosest);
    
            if (anc.length == 0) {      

              if (o.target == "_blank") {
                  window.open(o.href);
              } else if (o.target == "_top") {
                  top.location.href = d.href;
              } else {
                  location.href = d.href;
              }
      
              event.stopPropagation();
            }
          };
        
          d.mouseenter = function(event) {
            el.addClass(o.cn.hover);
            el.trigger("linkify.mouseenter");
          };

          d.mouseleave = function(event) {
            el.removeClass(o.cn.hover);
            el.trigger("linkify.mouseleave");
          };
          
          d.mousedown = function(event) {
            el.addClass(o.cn.active);
            el.trigger("linkify.mousedown");
          };

          d.mouseup = function(event) {
            el.removeClass(o.cn.active);
            el.trigger("linkify.mouseup");
          };
        
          if (o.cursor) {
            el.css({cursor: o.cursor});
          }

          el.click( d.click );
          
          if (o.cn.hover) {
            el.mouseenter(d.mouseenter);
            el.mouseleave(d.mouseleave);
          }      
          
          if (o.cn.active) {
            el.mousedown(d.mousedown);
            el.mouseup(d.mouseup);
          }      

        }
       
      }
      else if (cmd == 'destroy') {
        // remove event handlers, data, and anything else
        el.data(pn, null);
        el.unbind("click", d.click);

        if (o.cn.hover) {
          el.unbind("mouseenter", d.mouseenter);
          el.unbind("mouseleave", d.mouseleave);
        }      
        
        if (o.cn.active) {
          el.unbind("mousedown", d.mousedown);
          el.unbind("mouseup", d.mouseup);
        }      

      }

    });
    
  };
  
  $.fn[pn].defaults = defaults;
  
})(jQuery);




  