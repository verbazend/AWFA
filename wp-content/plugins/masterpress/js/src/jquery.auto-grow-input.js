
(function($) { 

  var defaults = {
    maxWidth: 1000,
    minWidth: 20,
    comfortZone: 20
  };
  
  var pn = 'autoGrowInput';

  $.fn[pn] = function() {
    
    var ret = this, all = this, cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    if (this.length) {
      this.filter('input:text').each( function() {

        var self, trigger, d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } } self = function() { el[pn].apply(el, arguments); }; trigger = function(n, dt) { return el.trigger(jQuery.Event(n + "." + pn), $.isArray(dt) ? dt : [ dt ] ) !== false; };
   
        if (!cmd) {
          // initialization (no command)
        
            var minWidth = o.minWidth || el.width(),
                val = '',
                testSubject = $('<tester/>').css({
                    position: 'absolute',
                    top: -9999,
                    left: -9999,
                    width: 'auto',
                    fontSize: el.css('fontSize'),
                    fontFamily: el.css('fontFamily'),
                    fontWeight: el.css('fontWeight'),
                    letterSpacing: el.css('letterSpacing'),
                    whiteSpace: 'nowrap'
                });
              
                d.check = function() {

                    if (val === (val = el.val())) {return;}

                    // Enter new content into testSubject
                    var escaped = val.replace(/&/g, '&amp;').replace(/\s/g,'&nbsp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    testSubject.html(escaped);

                    // Calculate new width + whether to change
                    var testerWidth = testSubject.width(),
                        newWidth = (testerWidth + o.comfortZone) >= minWidth ? testerWidth + o.comfortZone : minWidth,
                        currentWidth = el.width(),
                        isValidWidthChange = (newWidth < currentWidth && newWidth >= minWidth)
                                             || (newWidth > minWidth && newWidth < o.maxWidth);

                    // Animate width
                    if (isValidWidthChange) {
                      el.width(newWidth);
                    }

                };

            testSubject.insertAfter(el);

            $(this).bind('keyup keydown blur update', d.check);

        }
        else if (cmd == 'update') {
          d.check();
        }
        else if (cmd == 'destroy') {
          // remove event handlers, data, and anything else
          el.data(pn, null);
        }

      });
    }

    return ret;
  };
  
  $.fn[pn].defaults = defaults;
  
})(jQuery);