/*!
  Picturefill plug-in for jQuery, License - MIT, Copyright: 2012 Traversal - http://traversal.com.au
*/

/*
  Title: Picturefill plug-in
    
  Description:
    A jQuery version of the picturefill plugin by Scott Jehl ( https://github.com/scottjehl/picturefill )

  Author Info:
    Created By -  Traversal < - >
    Licence - MIT Style <http://en.wikipedia.org/wiki/MIT_License>
  
  Requires: 
    jQuery 1.3 - http://jquery.com

*/


/*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas. Dual MIT/BSD license */
window.matchMedia=window.matchMedia||(function(e,f){var c,a=e.documentElement,b=a.firstElementChild||a.firstChild,d=e.createElement("body"),g=e.createElement("div");g.id="mq-test-1";g.style.cssText="position:absolute;top:-100em";d.appendChild(g);return function(h){g.innerHTML='&shy;<style media="'+h+'"> #mq-test-1 { width: 42px; }</style>';a.insertBefore(d,b);c=g.offsetWidth==42;a.removeChild(d);return{matches:c,media:h}}})(document);


(function($) { 

  var defaults = {
		cnLoaded : 'picturefill-loaded',
		cnLazy: 'lazy'
  };
  
  var pn = 'picturefill';

  $.fn[pn] = function() {
    
    var ret = this, all = this, cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    if (this.length) {
      this.each( function() {

        var self, trigger, d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } } self = function() { el[pn].apply(el, arguments); }; trigger = function(n, dt) { return el.trigger(jQuery.Event(n + "." + pn), $.isArray(dt) ? dt : [ dt ] ) !== false; };
   
        if (!cmd) {

					d.imageload = function() {
            
            if (d.source && d.target) {
              d.target.attr("src", d.source.attr("src"));
            }
            
						el.addClass(o.cnLoaded);
						trigger( "loaded" );
					};

					self('fill');

        }
        else if (cmd === 'fill') {
					
					if ( el.get(0).getAttribute( "data-picture" ) !== null ) {
							
						var	matches = [];
						
						el.find("div").each( function() {
							
							var $source = $(this);
							
							var media = $source.data("media");
							
							// if there's no media specified, OR w.matchMedia is supported
							
							if( !media || ( window.matchMedia && window.matchMedia( media ).matches ) ) {
								matches.push( $source );
							}
							
						});

						var $img = el.find("img");
						
						if ( matches.length ) {			
							 
							var src = matches.pop().data( "src" );
               
							// if there is no image element, create one
							if ( !$img.length ){
								
                var attr = {
                  "alt" : el.data("alt")
                };
                
                var lazy = el.hasClass(o.cnLazy);
                
                if (lazy) {
                  attr["data-original"] = src;
                } else {
                  attr["src"] = src;
                }
                
								$img = $('<img />');
								$img.attr(attr);
								
								
								if (!lazy && !$img.data("load")) {
									$img.data("load", d.imageload);
								
									if ($.fn.imageLoad) {
										$img.imageLoad( d.imageload );
									} else {
										$img.load( d.imageload );
									}
								
								}

								el.append($img);

							} else {
								
								if (el.hasClass(o.cnLazy)) {
  								// just set the new src
  								$img.attr("data-original", src );
                } else {
                  
                  // create a separate element
                  
                  d.source = $('<img />').attr("src", src);
                  
                  if ($.fn.imageLoad) {
										d.source.imageLoad( d.imageload );
									} else {
										d.source.load( d.imageload );
									}
                  
                  d.target = $img;
                   
                   
                }
              
							}
							
							
							
						}
						else if ( $img.length ) {
							// no matches, remove the image
							$img.remove();
						}

					}	
					 
        }
        else if (cmd === 'destroy') {
          // remove event handlers, data, and anything else
          el.data(pn, null);
        }

      });
    }

    return ret;
  };
  
  $.fn[pn].defaults = defaults;
  
  $.fn.lazypicturefill = function(options) {

    var o = options || {};

    return $(this).each( function() {
      $(this).picturefill(o)
        .filter(".lazy")
          .find("img")
            .lazyload(o)
            .bind("lazyloaded", function() {
              $(this).closest(".picture").addClass("picturefill-loaded");
            });
    });

  };
  
})(jQuery);

