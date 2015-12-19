/*!
  reveal plug-in for jQuery, License - MIT, Copyright: 2009 Traversal - http://traversal.com.au
*/

/*
  Title: reveal plug-in
    
  Description:
    

  Author Info:
    Created By - Traversal <http://traversal.com.au>
    Licence - MIT Style <http://en.wikipedia.org/wiki/MIT_License>
  
  Requires: 
    jQuery 1.3 - http://jquery.com

  Companion plug-ins:
    Metadata - http://plugins.jquery.com/project/metadata (optional)

*/


(function($) { // closure and $ portability

  // plug-in default options (exposed through $.fn.reveal)
   
  var defaults = {
    triggerEvent: "click", // "click" || "hover" || "hoverIntent"
    
    hoverIntent: {    
       sensitivity: 3, // number = sensitivity threshold (must be 1 or higher)    
       interval: 200, // number = milliseconds for onMouseOver polling interval    
       timeout: 0 // number = milliseconds delay before onMouseOut    
    },

    selectorText: ".text",

    classShown: 'reveal-shown',
    affix: null,

    hideOnMouseLeave: false,
    hideOnTargetMouseLeave: false,
    hideOnDocumentClick: false,
    
    hideDelay: 300,
    showDelay: 300,
    
    selectorHide: ".close", // any elements within the MENU matching this selector will hide it

    show: "show",
    hide: "hide",
    showOptions: { duration: 0, effect: "fade", easing: "swing" },
    hideOptions: { duration: 0, effect: "fade", easing: "swing" }
  };
  
  var pn = 'reveal';
  
  $.fn[pn] = function() {
    
    var ret = this, all = this, cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1] || {}; } }
    
    if (this.length) {
      this.each( function() {

      var self, trigger, d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } } self = function() { el[pn].apply(el, arguments); }; trigger = function(n, dt) { return el.trigger(jQuery.Event(n + "." + pn), $.isArray(dt) ? dt : [ dt ] ) !== false; };

      d.trigger = el;
      
      var ts = o.target || o.e || o.el || o.element;
      
      if (o.hideSelector) { // compatibility
        o.selectorHide = o.hideSelector;
      }
        
        
      d.cancelHide = function() { clearTimeout(d.toHide); };
      d.cancelShow = function() { clearTimeout(d.toShow); };
      d.enter = function(event) { if (!d.disabled) { d.cancelHide(); d.toShow = setTimeout( function() { self('show', {eventTarget: event.target}); }, o.showDelay ); } };
      d.leave = function(event) { if (!d.disabled) { d.cancelShow(); d.toHide = setTimeout( function( ) { self('hide', {eventTarget: event.target}); } , o.hideDelay ); } };
      d.click = function(event) {

        if (!d.disabled) {
          if (d.target.is(":visible")) {
            self('hide', {eventTarget: event.target});
          } else {
            self('show', {eventTarget: event.target});
          }
        }
        
        event.preventDefault();
      };
      
      
      d.documentClick = function(event) {
        
        if ( ! ( $(event.target).closest(d.target).length || $(event.target).closest(d.trigger).length ) ) {
          // only hide if the click is on the target or trigger element
          self('hide');
        } else {
          // else rebind the event since this one handler swalled it
          // this is still favorable to binding to EVERY document click, as the handler here will only run while
          // the element is visible
          
          $(document).one("click", d.documentClick);
        }
      
      };
      
      
        
      if (!cmd) {
        // initialization

        // record the text element to update on reveal (if any)
        d.elText = el.find(o.selectorText);
      
      
        if (!d.elText.length) {
          d.elText = d.trigger;
        }
      
        if (o.textShown) {
          d.textShown = o.textShown;
          d.textHidden = d.elText.html();        
        } else if (o.textHidden) {
          d.textHidden = o.textHidden;
          d.textShown = d.elText.html();
        }
                  
        if (!ts) {
          // maybe the element is a hash anchor?
          
          var href = el.attr("href");
          
          if (href && href != "") {
            
            var matches = href.match(/#[a-zA-Z\-\_0-9]+/);
            
            if (matches && matches.length) {
              ts = matches[0];
            }
            
          }
        }
        

        if (ts) {

          // get the reveal element 
          d.target = $(ts).eq(0);
          
          if (d.target.length) {

            
            // store the reverse relationship to allow reveal('hide') and reveal('show') to be called on the item being revealed as well
            d.target.data("reveal", {});
            var dd = d.target.data("reveal");
            dd.trigger = el;
            dd.target = d.target;
            dd.options = o;
            
            if (o.triggerEvent == "click") {
              el.click(d.click); 

              if (o.hideOnMouseLeave) {
                el.mouseenter(d.cancelHide);
                el.mouseleave(d.leave);
              }

              if (o.hideOnTargetMouseLeave) {
                d.target.mouseenter(d.cancelHide);
                d.target.mouseleave(d.leave);
              }
 
            } 
            else if (o.triggerEvent == "hoverIntent" && $.fn.hoverIntent) { 
              var hic = $.extend({}, o.hoverIntent, { over: d.enter, out: d.leave });
              
              el.hoverIntent( hic );

              d.target.mouseenter( function(event) {
                d.cancelHide();
                
                var ob = el.get(0);
                clearTimeout(ob.hoverIntent_t);
                ob.hoverIntent_s = 0;
              });

              if (o.hideOnTargetMouseLeave) {
                d.target.mouseleave(d.leave);  
              }
            }
            else {
              
              el.mouseenter(d.enter);
              el.mouseleave(d.leave);

              d.target.mouseenter(d.cancelHide);

              if (o.hideOnTargetMouseLeave) {
                d.target.mouseleave(d.leave);  
              }

            }

            $(o.selectorHide, d.target).click( function(event) {
              
              self('hide', { eventTarget: event.target });
              //event.stopPropagation();
              event.preventDefault();
            });


          }
        }
          
      }
      else if (cmd == 'hide') {
        
        d.edata = [{ element: d.target, target: d.target, eventTarget: p.eventTarget }];
          
        if (d && d.target && d.trigger) {
        
          var afterhide = function() { 
            
            if (!p.silent) {
              d.trigger.trigger("afterhide.reveal", d.edata );
            }
            
            if (o.fUnposition) {
              o.fUnposition(d.trigger, d.target); // if a cleanup positioning function is defined, call it
            }
            else if (d.target.unaffix && o.affix)
              d.target.unaffix(); //  use unaffix if it's available
            
            if (d.textShown && d.textHidden) {
              
              d.elText.html(d.textHidden);
            }

            if (o.hideOnDocumentClick) {
              $(document).unbind( "click", d.documentClick );
            }


            if (o.hideOptions.complete)
              o.hideOptions.complete();
          };

          if (!p.silent) {
            var e = jQuery.Event("beforehide.reveal"); 
            d.trigger.trigger(e, d.edata );
          }
        
          
          if (p.silent || e.result !== false) {
            d.trigger.removeClass(o.classShown); 
            d.target.stop(true, true);
            
            if (p.instant) {

              d.target.hide();
              afterhide();

            } else {

              if ($.ui && o.hide == "hide") {
                d.target.hide(o.hideOptions.effect, o.hideOptions, o.hideOptions.speed || o.hideOptions.duration, afterhide);
              }
              else {
                d.target[o.hide]($.extend(true, { complete: afterhide }, o.hideOptions));
              }
              
            }
            
            var e = jQuery.Event("beforehideanimate.reveal"); 
            d.trigger.trigger(e, d.edata);


            
          }
        }
        
      } 
      else if (cmd == 'toggle') {
        if (d.target.is(":visible")) {
          self('hide');
        } else {
          self('show');
        }
      }
      else if (cmd == 'show') {
        
        if (d && d.target && d.trigger) {
          
          d.edata = [{ element: d.target, target: d.target, eventTarget: p.eventTarget }];
          
          var aftershow = function() { 
            if (!p.silent) {
              d.trigger.trigger("aftershow.reveal", d.edata); 
            }
          
            if (d.textShown && d.textHidden) {
              d.elText.html(d.textShown);
            }
            
            if (o.showOptions.complete)
              o.showOptions.complete();
              
            if (o.hideOnDocumentClick) {
              $(document).one( "click", d.documentClick );
            }


          };

          if (!p.silent) {
            var e = jQuery.Event("beforeshow.reveal"); 
            d.trigger.trigger(e, d.edata);
          }
          
          if (p.silent || e.result !== false) {
            d.trigger.addClass(o.classShown); 

            if (o.fPosition) {
              o.fPosition(t, r); // if a custom positioning function is defined, call it
            }
            else if (d.target.affix && o.affix) {
              
              if (o.affix.el || o.affix.elem || o.affix.element) {
                d.target.affix(o.affix);  
              } else {
                d.target.affix($.extend(true, {}, o.affix, {target: d.trigger}));  
              }
            }
            
            if (!p.silent) {
              var e = jQuery.Event("afterposition.reveal"); 
              d.trigger.trigger(e, d.edata);
            }
            

            d.target.stop(true, true);
            
            if (p.instant) {
              
              d.target.show();
              aftershow();
              
            } else {
              
              if ($.ui && o.show == "show") {
                d.target.show(o.showOptions.effect, o.showOptions, o.showOptions.speed || o.showOptions.duration, aftershow);
              }
              else {
                d.target[o.show]($.extend(true, { complete: aftershow }, o.showOptions));
              }
            
            }
            
            var e = jQuery.Event("beforeshowanimate.reveal"); 
            d.trigger.trigger(e, d.edata);


            
          }
        }
        
      }
      else if (cmd == 'hold') {
        d.cancelLeave();
      }
      else if (cmd == "disable") {
        d.disabled = true;
        
        if (p.show) {
          self("show", ({instant: true})); 
        } else {
          self("hide", ({instant: true})); 
        }
        
      }
      else if (cmd == "enable") {
        d.disabled = false;
      }
      else if (cmd == 'destroy') {
        
        if (o.triggerEvent == "click") {
          el.unbind("click", d.click); 
          
          if (o.hideOnMouseLeave) {
            el.unbind("mouseenter", cancelLeave);
            el.unbind("mouseleave", d.leave);
          }

          if (o.hideOnTargetMouseLeave) {
            d.target.unbind("mouseenter", d.cancelLeave);
            d.target.unbind("mouseleave", d.leave);
          }

        } else {

          el.unbind("mouseenter", d.enter);
          el.unbind("mouseleave", d.leave);

          d.target.unbind("mouseenter", d.enter);

          if (o.hideOnTargetMouseLeave) {
            d.target.unbind("mouseleave", d.leave);  
          }

        }
          
          
        el.removeData('reveal');
      }
      
      });
    }
    
    return ret;

  };
  
  $.fn.revealClick = function(options) {
    return this.reveal( $.extend(true, {}, options, { triggerEvent: "click" } ) );
  };

  $.fn.revealHover = function(options) {
    
    return this.reveal( $.extend(true, {}, options, { triggerEvent: "hover", hideOnTargetMouseLeave: true } ) );
  };

  $.fn.revealMenuHover = function(options) {
    return this.reveal( $.extend(true, {}, $.fn.reveal.presets.menuHover, options ) );
  };

  $.fn.revealMenuClick = function(options) {
    return this.reveal( $.extend(true, {}, $.fn.reveal.presets.menuClick, options ) );
  };

  $.fn.revealTooltip = function(options) {
    return this.reveal( $.extend(true, {}, $.fn.reveal.presets.tooltip, options ) );
  };
  
  $.fn[pn].defaults = defaults; // expose default options
  
  $.fn.reveal.presets = { 
    menuHover:        { classShown: "menu-shown", show: "slideDown", showDelay: 200, showOptions: { duration: 200 }, hide: "slideUp", hideOptions: { duration: 100 }, triggerEvent: "hoverIntent",  hoverIntent: { interval: 30 }, hideOnTargetMouseLeave: true, affix: { layerfix: { method: "hideSelects" }, to: "sw", from: "nw", keepInWindow: false } },
    menuClick:        { classShown: "menu-shown", show: "slideDown", showOptions: { duration: "fast"}, hide: "slideUp", hideOptions: { duration: "fast"}, triggerEvent: "click", hideOnTargetMouseLeave: true, affix: { layerfix: { method: "hideSelects" }, to: "sw", from: "nw", keepInWindow: false } },
    tooltip:          { classShown: "tooltip-shown", show: "fadeIn", showOptions: { duration: "fast"}, hide: "fadeOut", hideOptions: { duration: "fast"}, triggerEvent: "hover", hideOnTargetMouseLeave: true, affix: { layerfix: { method: "hideSelects" }, to: "ne", from: "sw", keepInWindow: true } } 
  };
  
})(jQuery);