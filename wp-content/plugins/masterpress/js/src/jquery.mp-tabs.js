/*!
  tabs plug-in for jQuery, License - MIT, Copyright: 2009 Traversal - http://traversal.com.au
*/

/*
  Title: tabs plug-in
    
  Description:
    A simple tabs plug-in for jQuery.

  Author Info:
    Created By -  Traversal - http://traversal.com.au>
    Licence - MIT Style <http://en.wikipedia.org/wiki/MIT_License>
  
  Requires: 
    jQuery 1.3 - http://jquery.com

  Companion plug-ins:
    Metadata - http://plugins.jquery.com/project/metadata (optional)

*/


(function($) { 

  var defaults = { 
    selector : 'a',
    classNameCurrent: 'current',
    classNameAfterCurrent: 'after-current',    
    classNameBeforeCurrent: 'before-current',    
    classNamePanelCurrent: 'current',
    index: "derive",
    allowUnselect: false,
    event: "click", // "click" || "hover"
    show: "show",
    showOptions: { effect: "fade", duration: 0 },
    hide: "hide",
    hideOptions: { effect: "fade", duration: 0 } 
  };

  var pn = 'mp_tabs';
  
  $.fn.mp_tabs = function() {
    
    var cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    return this.each( function() {

      var d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } }
    
      if (cmd) {
        d.edata = { tab: d.tab, panel: d.panel, panelOld: d.panelOld, tabOld: d.tabOld };
      }
      
      if (!cmd) {
        // initialization (no command)

        d.tabs = el.find(o.selector);
        d.panels = d.tabs.tabPanels();
        d.index = -1;
        d.animating = false;

        d.tabOld = d.panelOld = d.tab = d.panel = $([]);
        
        if (o.index == "derive") {
          // look for the first tab with the "current" css class
          d.index = d.tabs.index(d.tabs.filter("." + o.classNameCurrent).eq(0));
        }
        else if (o.index != null && o.index < d.tabs.length && o.index >= 0) {
          d.index = o.index;
        }
        
        d.panels.hide();

        d.handleEvent = function(event) {


          if (!d.animating) {
            
            var ni = d.tabs.index($(this));
          
            if (o.event == "click" && d.index != -1 && d.index == ni && o.allowUnselect) {
              
              el.mp_tabs('unselect');
            } else {
              d.indexPrevious = d.index;
              d.index = ni;

              el.mp_tabs('update');
            }

          }

          if (o.event == "click" && !o.allowDefault) {
            event.preventDefault();
          }

        };
        
        if (o.event == "click") {
          d.tabs.click(d.handleEvent);
        } else if (o.event == "mouseenter") {
          d.tabs.mouseenter(d.handleEvent);
        }
        
        if (d.index != -1) {
          el.mp_tabs('update');
        }
      }
      else if (cmd == 'update') {
        
        var aftershow = function() { 
          
          d.animating = false;
          afterEvents();

          d.panel.addClass(o.classNamePanelCurrent);
          
          if (o.showOptions.complete)
            o.showOptions.complete();
        };
        
        var afterEvents = function() {
          
          el.trigger("afterchange.mp_tabs", [ d.edata ] ); 

          if (p.unselect) {
            el.trigger("afterunselect.mp_tabs", [ d.edata ]);
          }

        };
        
        var afterhide = function() { 

          if (d.panelOld) {
            d.panelOld.removeClass( o.classNamePanelCurrent );

          }
          
          if (o.hideOptions.complete)
            o.hideOptions.complete();

          if (d.index == -1) {
            d.animating = false;
            afterEvents();
          } else {
            show();
          }
          
          if (p.unselect) {
            d.tab = $();
            d.index = -1;
          }

        };
          
          
        var show = function() {
          
          if (d.index != -1) {

            d.tab.addClass(o.classNameCurrent);
            
            
            if (d.index > 0) {
              d.tabs.eq(d.index - 1).addClass(o.classNameBeforeCurrent);
            }
            
            if (d.index < d.tabs.length - 1) {
              d.tabs.eq(d.index + 1).addClass(o.classNameAfterCurrent);
            }
            
            d.panel[o.show]($.extend(true, { complete: aftershow }, o.showOptions));

          } else {
            d.animating = false;
          }
          
        };
        
        
        d.edata.tabOld = d.tab;
        d.edata.panelOld = d.panelOld = d.panel;
        
        // event result
        var r = true;
        
        if (d.index != -1) {
          d.st = d.tabs.eq(d.index);
        
          if (d.st.length) {
            d.sp = d.st.tabPanels();
          }
        }

        

        if (true) {
          d.animating = true;
          d.edata.panel = d.sp;
          d.edata.tab = d.st;
          
          d.tabs.removeClass(o.classNameCurrent).removeClass(o.classNameAfterCurrent).removeClass(o.classNameBeforeCurrent);
        
          if (p.unselect) {
            var e = jQuery.Event("beforeunselect.mp_tabs"); 
            el.trigger(e, [ d.edata ]);
          
            r = r && (e.result !== false);
          } else {
            var e = jQuery.Event("beforechange.mp_tabs"); 
            el.trigger(e, [ d.edata ]);
          }
        
        
          r = r && (e.result !== false);
        
          if (r !== false) {
            d.tab = d.st;
            d.panel = d.sp;
          
          
            if (d.panelOld.length) {
              // hide previous tab
              d.panelOld[o.hide]($.extend(true, { complete: afterhide }, o.hideOptions));
              
            } else if (d.panel && d.panel.length) {
              // the old tab doesn't exist, but we need to show the new one
              show();
            }
          }

        }


        
      }
      else if (cmd == 'select' || cmd == 'to') {
        
        if (!d.animating) {

          d.indexPrevious = d.index;
        
          if (p.el) {
            var tab = $(p.el);

            // check the element exists
            if (tab.length) {
            
              // find the element index
              var ei = d.tabs.index(tab);
              
              if (ei != -1) {
                d.index = ei;
                el.mp_tabs('update', p);
              } else {
                // try to find the element in the panels collection
                var ei = d.panels.index(tab);
                
          
          
                if (ei != -1) {
                  d.index = ei;
                  el.mp_tabs('update', p);
                }
              

              }
              
            }
          }
          else if (p.index !== null) { // must allow zeros
        
            if (p.index == "first") {
              p.index = 0;
            } else if (p.index == "last") {
              p.index = d.tabs.length - 1;
            }
        
        
            if (p.index >= 0) {
              d.index = p.index;
              
              el.mp_tabs('update', p);
            }
          }
        
        }
          
      }
      else if (cmd == 'unselect') {
        
        if (!d.animating) {
          d.indexPrevious = d.index;
        
          d.index = -1;
          el.mp_tabs('update', $.extend(true, {}, p, { unselect: true } ));
        }

      }
      else if (cmd == 'destroy') {
        // remove event handlers, data, and anything else
        
        if (o.event == "click") {
          d.tabs.unbind("click", d.handleEvent);
        } else if (o.event == "hover") {
          d.tabs.unbind("mouseenter", d.handleEvent);
        }

        el.removeData('tabs');
      }

    });
    
    
  };
  
  $.fn.tabPanels = function() {
    return this.map( function(i, v) {
      var el = this;
      
      var panel;
      var ts;
      
      if ($.fn.metadata) { 
        ts = $(this).metadata({ type: 'class' }).target;
      }
      
      if (ts) {
        panel = $(ts);
      } else {
        panel = $($(this).attr("href")); 
      }

      if (panel.length) {
        return panel.get(0);
      }
    });
  };
  
  $.fn.mp_tabs.defaults = defaults;
  
})(jQuery);  


