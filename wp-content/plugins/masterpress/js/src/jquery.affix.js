/*!
  affix plug-in for jQuery, License - MIT, Copyright 2009 Traversal: http://traversal.com.au
*/

/*
  Title: affix plug-in
    
  Description:
    Allows an element to be glued in compass co-ordinate positions to either: another *element*, the *window*, or *offscreen*. 
    While this is certainly achievable through other means in jQuery, there are a number of features of affix that set it 
    apart from a manual approach.
    
  Demo Page:
    http://plugins.traversal.com.au/jquery/demos/affix.html
  
  Concise Syntax for elements:
    
    Example: a tooltip can be positioned at the north-east (top right) corner of a link with ID "link", from it's 
    south-west (bottom left) corner but inset by 6 pixels in both x and y direction like so :
    > $('#tooltip').affix({el: $('#link'), to: "ne", from: "sw", inset: 6 } );
   
  Concise Syntax for the window, and "offscreen" positioning:
    An element can be positioned 10 pixels inset from the north-east corner of the window with the very concise :
    > $('#message').affix({w: "ne+10"});
    
    An element can be positioned offscreen (just outside the northeast corner of window) with
    > $('#message').affix({os: "ne" });
  
  Auto Events an Keep In Window:
    When affixing one element to another, affix can be setup to move the element to the correct position as it changes size or scrolls. 
    So for example, a tooltip can be affixed to the north-east of a link at the right-edge of a page, but when the window size is reduced 
    it will automatically be glued to the north-west corner of the link. As this happens, special CSS classes are added to both elements
    to allow you to style the element appropriately (the "nub" of a tooltip can be swapped bubble an be repositioned, and so on)
    
  Automatic reaffix when scrolling or resizing:
    Affix can be setup to automatically reposition elements to the correct place as the window
    is resized or scrolled, saving the developer from having to set this up manually.

  Manual reaffix:
    An "again" plugin command is available for repositioning affixed elements as other elements on the page change size. 


*/

(function($) {

   /*
      Property: defaults
        Default options for affix, which may be configured for all calls. See <affix> for available options and their default values.

      Example Usage:
        > jQuery.fn.affix.defaults.keepInWindow = false; // affix now won't try to keep the element inside the window by default

  */

  var defaults = {

    animate 			      : false,
		animateOptions		  : { duration: "normal" },

		to: "ne",           // default case is for the most common form of "tooltip"
		keepInWindow	      : true,
		offset				      : [0,0],
		inset				        : [0,0],

		// inset [x,y] can be used instead of (or in addition to) offset, which intelligently applies offset with respect to the glue position. 
		// positive inset will position *inside* the boundary of that which we are hooking "to", negative inset will position *outside*. 
		// a single integer value can also be used to apply equal [x,y] inset
		
		use                 : "offset", // should be "offset" OR "position" - which function do we use to calculate the position of the "to" element (use "position" when the from and to elements are both offset inside the same relatively positioned container)
		fixed				        : true,
		
		smartOffscreenInset : true,

		classGlueFromPrefix	: "glue-",
		classGlueToPrefix	  : "glue-",
		
    layerfix            : false, // layerfix options hash, so that selects are not rendered on top of the element e.g. { method: "hideSelects" } where method should be false || "hideSelects" || "shim" || "both"  (requires layerfix plugin)
    
    autoEvents          : { scroll: true, resize: true } // sets up events to "reaffix" the target elements when the window is resized or scrolls
    
  };
  
  
  // ========================= getStyleProperty by kangax ===============================
  // http://perfectionkills.com/feature-testing-css-properties/
  var getStyleProperty=(function(){var b=["Moz","Webkit","Khtml","O","Ms"];var c={};function a(k,g){g=g||document.documentElement;var h=g.style,d,j,f,e;if(arguments.length===1&&typeof c[k]==="string"){return c[k];}if(typeof h[k]==="string"){return(c[k]=k);}j=k.charAt(0).toUpperCase()+k.slice(1);for(f=0,e=b.length;f<e;f++){d=b[f]+j;if(typeof h[d]==="string"){return(c[k]=d);}}}return a;}());

  var tested = false; var ignoresTranslationInOffset = false;
  var ie6 = $.browser.msie && $.browser.version < 7.0;
  var pn = 'affix';

  // this is the same test setup as in jQuery's offset function, minus the tables

	var affixCapTest = function() {
    var innerDiv, checkDiv, html = '<div style="position:absolute; top:0; left:0; margin:0; padding:0; width:1px; height:1px;"><div></div></div>';

	  tested = true;
	  
    var $c = $('<div/>').css({ position: "absolute", top: 0, left: 0, margin: 0, border: 0, width: "1px", height: "1px", visibility: "hidden" }).html(html);
  	var checkDiv = $c.find("div>div").get(0);
    $("body").prepend($c);

    window.cTest = $c;
    
    checkDiv.style.position = "absolute"; 
    checkDiv.style[getStyleProperty("transform")] = "translate(20px, 0px)";
    ignoresTranslationInOffset = $(checkDiv).position().left === 0;
    
    $c.remove();
  };

  $.fn[pn + "_translate"] = function() {
    var $el = $(this).eq(0);
    
    var t = { top: 0, left: 0 };
  
    var tp = getStyleProperty("transform");
    
    if (tp) {
      var transform = $el.css(tp);

      if (transform) {
        // jQuery always seems to return the matrix transform rule (I think!)
        var matches = transform.match(/matrix\((.*?)\)/);
  
        if (matches && matches.length && matches.length == 2) {
          vals = matches[1].split(",");
    
          if (vals.length == 6) {
            // add the translation values to the position
            t.top += parseInt(vals[5]);
            t.left += parseInt(vals[4]);
          }
        }

      }
    }
    
    return t;
  };
    
  $.fn[pn + "_position"] = function() {
    
    if (!tested) {
      affixCapTest();
    }
    
    var $el = $(this);
  
    var position = $el.position();
  
    if (ignoresTranslationInOffset) {
      var t = $el[pn + "_translate"]();
      position.top += t.top;
      position.left += t.left;
    }
  
    return position;
  };
     
  
  $.fn[pn + "_offset"] = function() {
    
    if (!tested) {
      affixCapTest();
    }
    
    var $el = $(this);
  
    var offset = $el.offset();
  
    if (ignoresTranslationInOffset) {

      var t = $el[pn + "_translate"]();
      offset.top += t.top;
      offset.left += t.left;
    }
  
    return offset;

  };
  
  
	$.fn.affixPosition = function() {
    
  	// only the first matching element is regarded for this function
    var cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    var d, p = params, o = {}, md = {}, el = $(this).eq(0); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return this; } }

    
	  // define local variables and functions inside this closure
	  var tp, ts, es, ep, wo, ws, info;
    
    if (o.offsets) {
      // compatibility fix ("offsets", and "inset" are inconsistent terms)
      o.offset = o.offsets;
    }

		// assume animation is active if animateProperties or animateOptions were provided
		if (options && (options.animateOptions || options.animateParams))
			o.animate = true;

		if (options && (options.reaffixing)) 
		  o.animate = false; // don't allow animation when reaffixing
		
		if (o.glueInsideWindow) {
		  o.keepInWindow = o.glueInsideWindow;
	  }
    
    o.element = o.element || o.el || o.e || o.target;
    o.window = o.window || o.win || o.w;
    o.offscreen = o.offscreen || o.os || o.o;
    
    if (!(o.element || o.window || o.offscreen)) {
      throw("No affix target specified");
    }

		// calculate element size, and window size and offset
		
		ws = { width: $(window).width(), height: $(window).height() };
    wo = { top: $(window).scrollTop(), left: $(window).scrollLeft() };
		es = { width: el.outerWidth(), height: el.outerHeight() };
    
    
	  // setup routines for hooking to the window 

  	var WIN = {
  		derive: function(eqn)
  		{
  		  var glue = { from: o.from, to: o.to };
  			// get the intended position
  			var pos = getPosition(eqn || WIN.eqn, o.from);

  			// apply any offset, inset
  			var offset = applyOffset(pos, glue);
  			var insetOffset = applyInset(pos, glue);

  			// now apply the correct class name based on the from "glue" (the glue is implied in the affix "to" string)
  			applyClassName(el, o.from, o.classGlueFromPrefix);

  			return { left: pos.left, top: pos.top, from: glue.from, to: glue.to, offset: offset, insetOffset: insetOffset };
  		},

  		eqn: {
  			"n" : "wx+h:ww-h:fw,wy",
  			"ne": "wx+ww-fw,wy",
  			"e" : "wx+ww-fw,wy+h:wh-h:fh",
  			"se": "wx+ww-fw,wy+wh-fh",
  			"s" : "wx+h:ww-h:fw,wy+wh-fh",
  			"sw": "wx,wy+wh-fh",
  			"w" : "wx,wy+h:wh-h:fh",
  			"nw": "wx,wy",
  			"c" : "wx+h:ww-h:fw,wy+h:wh-h:fh"
  		}
  	};

  	// routines for hooking to offscreen

  	var OS = {
  		derive: function()
  		{
  			// piggyback on the WIN derive function - this has the same requirements
  			return WIN.derive(OS.eqn);
  		},

  		eqn: {
  			"n"  : "wx+h:ww-h:fw,wy-fh",
  			"nne": "wx+ww-fw,wy-fh",
  			"ne" : "wx+ww,wy-fh",
  			"ene": "wx+ww,wy",
  			"e"  : "wx+ww,wy+h:wh-h:fh",
  			"ese": "wx+ww,wy+wh-fh",
  			"se" : "wx+ww,wy+wh",
  			"sse": "wx+ww-fw,wy+wh",
  			"s"  : "wx+h:ww-h:fw,wy+wh",
  			"ssw": "wx,wy+wh",
  			"sw" : "wx-fw,wy+wh",
  			"wsw": "wx-fw,wy+wh-fh",
  			"w"  : "wx-fw,wy+h:wh-h:fh",
  			"wnw": "wx-fw,wy",
  			"nw" : "wx-fw,wy-fh",
  			"nnw": "wx,wy-fh"
  		}
  	}; // end OS

  	// setup some aliases for offscreen equations
  	OS.eqn.c 	 = OS.eqn.n;
  	OS.eqn.nwv = OS.eqn.vnw	= OS.eqn.nnw;
  	OS.eqn.nwh = OS.eqn.hnw	= OS.eqn.wnw;
  	OS.eqn.nev = OS.eqn.vne	= OS.eqn.nne;
  	OS.eqn.neh = OS.eqn.hne	= OS.eqn.ene;
  	OS.eqn.sev = OS.eqn.vse	= OS.eqn.sse;
  	OS.eqn.seh = OS.eqn.hse	= OS.eqn.ese;
  	OS.eqn.swv = OS.eqn.vsw	= OS.eqn.ssw;
  	OS.eqn.swh = OS.eqn.hsw	= OS.eqn.wsw;


  	
  	// routines for hooking to another element

  	var EL = {

  		derive: function() {
        
  			// calculate the to element size and position
        var vis = t.css("visibility");
        //t.css("visibility", "hidden");
  			
  			if (o.use == "position" || o.position == "relative") {
  			  tp = t[pn + "_position"]();
  			} else {
  			  tp = t[pn + "_offset"]();
        }
			  
  			ts = {width: t.outerWidth(), height: t.outerHeight()};
        //t.css("visibility", vis);

  			var glue = { to: o.to, from: o.from };

  			// get the intended position
  			var pos = EL.getPosition(glue);

  			// apply any offset
  			var offset = applyOffset(pos, glue);
  			var insetOffset = applyInset(pos, glue);

  			if (o.keepInWindow)
  			{
  				// now, apply tweaks based on the window edges
  				var newGlue = EL.glueInsideWindow(pos, glue);


  				if (newGlue)
  				{
  					glue = newGlue;
  					pos = EL.getPosition(glue);
  					offset = applyOffset(pos, glue);
  					insetOffset = applyInset(pos, glue);
  				}
  			}


  			// now apply the correct class name based on the (eventual) glue

  			applyClassName(el, glue.from, o.classGlueFromPrefix);

  			// now apply class names to the element(s) (if set in the options)
  			applyClassName(t, glue.to, o.classGlueToPrefix);

  			return { left: pos.left, top: pos.top, from: glue.from, to: glue.to, offset: offset, insetOffset: insetOffset };
  		},

  		getPosition: function(glue)
  		{
  			return getPosition(EL.eqn, glue.from + "_" + glue.to, EL.tokenValue);
  		},

  		glueInsideWindow: function(pos, glue)
  		{
  			// record where the element is glued to
  			var gtn = glue.to.search("n") != -1;
  			var gts = glue.to.search("s") != -1;
  			var gte = glue.to.search("e") != -1;
  			var gtw = glue.to.search("w") != -1;

  			// flag if the element will be clipped by any of the window edges
  			// ("cn" stands for "clipped at window's north edge, and so on)

  			var cn = pos.top < wo.top;
  			var cw = pos.left < wo.left; 
  			var ce = pos.left + es.width > wo.left + ws.width;
  			var cs = pos.top + es.height > wo.top + ws.height;

  			// now work out the best cases for transforming the hooks
  			// simply, if clipped at the north or south, swap the north/south.
  			// then, if clipped at the east or west, swap the east/west

  			var keys = [glue.from, glue.to];

  			if (!cn && !cs && !ce && !cw)
  				return false; // not clipped, so let the calling function know that

  			if ((cn && gtn) || (cs && gts))
  			{
  				keys[0] = EL.swapNS(keys[0]);
  				keys[1] = EL.swapNS(keys[1]);
  			}

  			if ((ce && gte) || (cw && gtw))
  			{
  				keys[0] = EL.swapEW(keys[0]);
  				keys[1] = EL.swapEW(keys[1]);
  			}

  			return {from: keys[0], to: keys[1]};
  		},

  		swapNS: function(key)
  		{
  			return key.replace(/n/g,"T").replace(/s/g, "n").replace(/T/g, "s");
  		},

  		swapEW: function(key)
  		{
  			return key.replace(/e/g,"T").replace(/w/g, "e").replace(/T/g, "w");
  		},

  		tokenValue: function(tk)
  		{
  			switch (tk)
  			{
  				case "tpw":
  				case "tpe":
  				case "tps":
  				case "tpn":
  				{
  					return parseEquation(EL.eqnm[tk.substr(2,1)], EL.tokenValue);
  				}
  				case "h:tw":
  				case "h:th":
  				{
  					// half (rounded)
  					return Math.round(EL.tokenValue(tk.substr(2,2)) / 2);
  				}
  				case "tw":
  				{
  					return ts.width;
  				}
  				case "th":
  				{
  					return ts.height;
  				}
  				case "tpx":
  				{
  					return tp.left;
  				}
  				case "tpy":
  				{
  					return tp.top;
  				}
  				default:
  				{
  					return tokenValue(tk);
  				}
  			}

  			return false;
  		},

  		// tpe => "To-Position-Equations"
  		// Note: h:n is equivalent to "half n rounded" or "Math.round(n/2)"

  		eqn: {
  			"n_n"  : "tpx+h:tw-h:fw,tpn",
  			"n_ne" : "tpe-h:fw,tpn",
  			"n_e"  : "tpe-h:fw,tpn+h:th",
  			"n_se" : "tpe-h:fw,tps",
  			"n_s"  : "tpw+h:tw-h:fw,tps",
  			"n_sw" : "tpw-h:fw,tps",
  			"n_w"  : "tpw-h:fw,tpn+h:th",
  			"n_nw" : "tpw-h:fw,tpn",
  			"n_c"  : "tpw+h:tw-h:fw,tpn+h:th",

  			"ne_n" : "tpx+h:tw-fw,tpn",
  			"ne_ne": "tpe-fw,tpn",
  			"ne_e" : "tpe-fw,tpn+h:th",
  			"ne_se": "tpe-fw,tps",
  			"ne_sw": "tpw-fw,tps",
  			"ne_s" : "tpw+h:tw-fw,tps",
  			"ne_w" : "tpw-fw,tpn+h:th",
  			"ne_nw": "tpw-fw,tpn",
  			"ne_c" : "tpw+h:tw-fw,tpn+h:th",

  			"e_n"  : "tpx+h:tw-fw,tpn-h:fh",
  			"e_ne" : "tpe-fw,tpn-h:fh",
  			"e_e"  : "tpe-fw,tpn+h:th-h:fh",
  			"e_se" : "tpe-fw,tps-h:fh",
  			"e_s"  : "tpw+h:tw-fw,tps-h:fh",
  			"e_sw" : "tpw-fw,tps-h:fh",
  			"e_w"  : "tpw-fw,tpn+h:th-h:fh",
  			"e_nw" : "tpw-fw,tpn-h:fh",
  			"e_c"  : "tpw+h:tw-fw,tpn+h:th-h:fh",

  			"se_n" : "tpx+h:tw-fw,tpn-fh",
  			"se_ne": "tpe-fw,tpn-fh",
  			"se_e" : "tpe-fw,tpn+h:th-fh",
  			"se_se": "tpe-fw,tps-fh",
  			"se_s" : "tpw+h:tw-fw,tps-fh",
  			"se_sw": "tpw-fw,tps-fh",
  			"se_w" : "tpw-fw,tpn+h:th-fh",
  			"se_nw": "tpw-fw,tpn-fh",
  			"se_c" : "tpw+h:tw-fw,tpn+h:th-fh",

  			"s_n"  : "tpx+h:tw-h:fw,tpn-fh",
  			"s_ne" : "tpe-h:fw,tpn-fh",
  			"s_e"  : "tpe-h:fw,tpn+h:th-fh",
  			"s_se" : "tpe-h:fw,tps-fh",
  			"s_s"  : "tpw+h:tw-h:fw,tps-fh",
  			"s_sw" : "tpw-h:fw,tps-fh",
  			"s_w"  : "tpw-h:fw,tpn+h:th-fh",
  			"s_nw" : "tpw-h:fw,tpn-fh",
  			"s_c"  : "tpw+h:tw-h:fw,tpn+h:th-fh",

  			"sw_n" : "tpx+h:tw,tpn-fh",
  			"sw_ne": "tpe,tpn-fh",
  			"sw_e" : "tpe,tpn+h:th-fh",
  			"sw_se": "tpe,tps-fh",
  			"sw_s" : "tpw+h:tw,tps-fh",
  			"sw_sw": "tpw,tps-fh",
  			"sw_w" : "tpw,tpn+h:th-fh",
  			"sw_nw": "tpw,tpn-fh",
  			"sw_c" : "tpw+h:tw,tpn+h:th-fh",

  			"w_n"  : "tpx+h:tw,tpn-h:fh",
  			"w_ne" : "tpe,tpn-h:fh",
  			"w_e"  : "tpe,tpn+h:th-h:fh",
  			"w_se" : "tpe,tps-h:fh",
  			"w_s"  : "tpw+h:tw,tps-h:fh",
  			"w_sw" : "tpw,tps-h:fh",
  			"w_w"  : "tpw,tpn+h:th-h:fh",
  			"w_nw" : "tpw,tpn-h:fh",
  			"w_c"  : "tpw+h:tw,tpn+h:th-h:fh",

        

  			"nw_n" : "tpx+h:tw,tpn",
  			"nw_ne": "tpe,tpn",
  			"nw_e" : "tpe,tpn+h:th",
  			"nw_se": "tpe,tps",
  			"nw_s" : "tpw+h:tw,tps",
  			"nw_sw": "tpw,tps",
  			"nw_w" : "tpw,tpn+h:th",
  			"nw_nw": "tpw,tpn",
  			"nw_c" : "tpw+h:tw,tpn+h:th",

  			"c_n"  : "tpx+h:tw-h:fw,tpn-h:fh",
  			"c_ne" : "tpe-h:fw,tpn-h:fh",
  			"c_e"  : "tpe-h:fw,tpn+h:th-h:fh",
  			"c_se" : "tpe-h:fw,tps-h:fh",
  			"c_s"  : "tpw+h:tw-h:fw,tps-h:fh",
  			"c_sw" : "tpw-h:fw,tps-h:fh",
  			"c_w"  : "tpw-h:fw,tpn+h:th-h:fh",
  			"c_nw" : "tpw-h:fw,tpn-h:fh",
  			"c_c"  : "tpw+h:tw-h:fw,tpn+h:th-h:fh"
  		},

  		// equation shorthand macros 
  		eqnm: { "n" : "tpy", "ne" : "", "e" : "tpx+tw", "s" : "tpy+th", "w" : "tpx" }

  	}; // end EL
    

  	// common routines

  	var getPosition = function(eqn, glueKey, fnTokenValue)
  	{
  		var parts = eqn[glueKey].split(",");

  		return {
  			left: Math.round(parseEquation(parts[0], fnTokenValue || tokenValue)),
  			top	: Math.round(parseEquation(parts[1], fnTokenValue || tokenValue))
  		};
  	};

  	var applyClassName = function(element, glueVal, classGluePrefix)
  	{
  		// remove any previous class name applied
  		// via a Regular Expression since using removeClass would be very slow here on over 20 possible class names

  		element.attr("class", element.attr("class").replace(new RegExp("\s?" + classGluePrefix + "[newsvhc]{1,3}\s?" ,"gi"), ""));

  		// now add the appropriate class name to the element
  		element.addClass(classGluePrefix + glueVal);
  	};

  	var tokenValue = function(tk)
  	{
  		// common token values (related to the window, and "from" element)

  		switch (tk)
  		{
  			case "h:fw":
  			case "h:fh":
  			case "h:wh": 
  			case "h:ww": {
  				// half (rounded)
  				return Math.round(tokenValue(tk.substr(2,2)) / 2);
  			}
  			case "ww":
  			{
  				return ws.width;
  			}
  			case "wh":
  			{
  				return ws.height;
  			}
  			case "wx":
  			{
  				return (o.fixed && !ie6) ? 0 : wo.left;
  			}
  			case "wy":
  			{
  				return (o.fixed && !ie6) ? 0 : wo.top;
  			}
  			case "fw": {
  				return es.width;
  			}
  			case "fh": {
  				return es.height;
  			}
  		}

  		return false;
  	};



  	var getOffset = function(glue)
  	{
  		var offset;

  		if (!o.offset.length && o.offset[glue.from + "_" + glue.to]) // offset for combination of "from" and "to" glue
  			offset = o.offset[glue.from + "_" + glue.to];
  		else if (o.offset.length && o.offset.length == 2) // simple array-based [x,y] offset
  			offset = o.offset;
  		else if (o.offset[glue.from]) // offset for just "from" glue
  			offset = o.offset[glue.from];
  		else if (o.offset["*"]) // default offset when offset is a hash
  			offset = o.offset["*"];

  		return offset;
  	};

  	var applyOffset = function(pos, glue)
  	{
  		if (o.offset)
  		{
  			var offset = getOffset(glue) || [0,0];

  			var x = parseInt(offset[0],0);
  			var y = parseInt(offset[1],0);

  			if (!isNaN(x))
  				pos.left += x;

  			if (!isNaN(y))
  				pos.top += y;
  		}

  		return offset;
  	};

  	var applyInset = function(pos, glue)
  	{
  		var io = deriveInsetOffset(pos, glue);

  		pos.left += io[0];
  		pos.top  += io[1];

  		return io;
  	};

  	var deriveInsetOffset = function(pos, glue)
  	{
  		// work out the equivalent offset, based on the "to" glue

  		var i = o.inset;

  		if (i)
  		{
  			if (typeof(i) == "number")
  			{
  				// a single inset for both x,y, build an array
  				i = [i,i];
  			}


  			var ieqn = {
  				"c"  : "0,0",
  				"n"  : "0,y",
  				"nne": "-x,y", 
  				"ne" : "-x,y",
  				"ene": "-x,y",
  				"e"  : "-x,0",
  				"ese": "-x,-y",
  				"se" : "-x,-y",
  				"sse": "-x,-y",
  				"s"  : "0,-y",
  				"ssw": "x,-y",
  				"sw" : "x,-y",
  				"wsw": "x,-y",
  				"w"  : "x,0",
  				"wnw": "x,y",
  				"nw" : "x,y",
  				"nnw": "x,y"
  			};

  			if (o.smartOffscreenInset)
  			{
  				// these mods ensure tweak the x or y inset as appropriate to ensure 
  				// that no part of the element is visible at each window boundary
  				ieqn.nne = "-x,0"; 
  				ieqn.ene = "0,y"; 
  				ieqn.ese = "0,-y"; 
  				ieqn.sse = "-x,0"; 
  				ieqn.ssw = "x,0"; 
  				ieqn.wsw = "0,-y"; 
  				ieqn.wnw = "0,y"; 
  				ieqn.nnw = "x,0"; 
  			}

  			// setup some aliases
  			ieqn.nwv = ieqn.vnw	= ieqn.nnw;
  			ieqn.nwh = ieqn.hnw	= ieqn.wnw;
  			ieqn.nev = ieqn.vne	= ieqn.nne;
  			ieqn.neh = ieqn.hne	= ieqn.ene;
  			ieqn.sev = ieqn.vse	= ieqn.sse;
  			ieqn.seh = ieqn.hse	= ieqn.ese;
  			ieqn.swv = ieqn.vsw	= ieqn.ssw;
  			ieqn.swh = ieqn.hsw	= ieqn.wsw;

  			var p = (ieqn[glue.to] || ieqn["c"]).split(",");

  			return [ parseInsetValue(p[0],i), parseInsetValue(p[1],i) ];
  		}

  		return [0,0];
  	};

  	var parseInsetValue = function(eq, i)
  	{
  		var m = eq.match(/([\-]?)([xy0])/);

  		if (m)
  		{
  			if (m[2] == "x")
  			{
  				if (m[1] == "-")
  				{
  					return -i[0];
  				}
  				else
  				{
  					return i[0];
  				}
  			}
  			else if (m[2] == "y")
  			{
  				if (m[1] == "-")
  				{
  					return -i[1];
  				}
  				else
  				{
  					return i[1];
  				}
  			}
  		}	

  		return 0;
  	};

  	var parseEquation = function(eq, tfn)
  	{
  		// tfn is the "token function" which is called to derive values of tokens, "tokenValue" if not specified

  		var m = eq.match(/([a-z:]+)|([\+\-])/g);

  		var v = 0;
  		var d; // d for delta

  		// for simplicity, we assume that odd numbered matches are operands, even are operators

  		for (var i=0; i<m.length; i++)
  		{
  			d = (tfn || tokenValue)(m[i]);

  			if (d !== false)
  			{
  				if (i == 0)
  				{
  					// the first operand simply add the token value
  					v += d;
  				}
  				else if (i % 2 == 0)
  				{
  					// an operand, but not the first - get the operator before this
  					if (m[i-1] == "+")
  						v += d;
  					else
  						v -= d;
  				}
  			}
  		}

  		return v;
  	};
    
    

    // --------------------- affixPosition function body begin ---------------------


		
		// work out what target is - an element, the window, or "offscreen"

		if (o.window || o.offscreen)
		{
		  var sb = o.window || o.offscreen; // window takes precedence
			var m = sb.match(/^(?:([nweschv]*))?(?:(\+|-)([0-9]+))?(?:,([0-9]+))?/);
			
			if (m) {
				
				if (m[1]) {
					// record the glue (only "from" really applies here but we'll set them both, they are the same)
					o.from = o.to = m[1];
				}
			  
				if (m[2] && m[3]) {
					// record any insets
					var m2v = parseInt(m[2],0);
					var m3v = m[3] ? parseInt(m[3],0) : 0;
					
					if (m[2] == "-")
					{
						o.inset = [-m3v, -m3v];
						
						if (m[4])
							o.inset[1] = -m4v;
					}
					else
					{
						o.inset = [m3v, m3v];

						if (m[4])
							o.inset[1] = m4v;
					}
					
				}
				
				if (o.from && (!o.to || o.to == ""))
				{
					o.to = o.from;
				}
				else if (o.to && (!o.from || o.from == ""))
				{
					o.from = o.to;
				}
				
				if (o.window) {
					info = WIN.derive();
					info.target = "window";
				} else if (o.offscreen) {
					info = OS.derive();
					info.target = "offscreen";
			  }
				
			}
			
			
		}
		else
		{
			var t = $(o.element).eq(0);

  	  if (t.length == 0) {
        throw("affix target element does not exist");
      }

			if (t)
			{
				if (!o.to)
				{
					// if "to" glue has not been specified create default glue for the border of the to element
					var dtg = {"sw":"ne","w":"e","nw":"se","n":"s","ne":"sw","e":"w","se":"nw","s":"n","c":"c"};
					o.to = dtg[o.from] || "c";
				}
				
				if (!o.from)
				{
					// setup default from glue when not specified
					var dfg = {"ne":"sw","e":"w","se":"nw","s":"n","sw":"ne","w":"e","nw":"se","n":"s","c":"c"};
					o.from = dfg[o.to] || "c";
				}
				
				info = EL.derive();
				info.target = "element";
				info.t = t;
			}
		}
    
		return info;
	};


  /* 

    Function: affix
       Affixes an element in a compass directional points to either: another element, the window, or offscreen.
      
    Arguments: 
      options | command - Object, the options hash to configure the plug-in (see below) | String, a command to call for an element that has already been set up with affix(options). See command list below.
      params  - Object, a set of additional parameters for this plug-in call. This argument is available both when initialising (options as first argument) and when issuing a command (string command as first argument).
    
    Options (defaults shown in brackets): 

      win (null)                                  - *Boolean*, set this to true to affix the element to the window (viewport) in the given "to" position. 
                                                    If this is true, offscreen and element options will be ignored
      offscreen (null)                            - *Boolean*, set this to true to affix the element to an "offscreen" position. When an element is "offscreen", it sits in the given "to" position such that it is just outside the visible area of the viewport. 
                                                    If this is true, the value of element will be ignored.
      element (null)                              - *String*, a jQuery selector OR Object, a DOM element OR Array, a jQuery wrapped set. Describes an element to affix to.
      animate ( false )	                          - *Boolean*, animate the reposition of the element over time
      animateOptions ( {duration: "normal"} )     - *Object*, options used in the call to jQuery's "animate" function
      to ( "ne" )                                 - *String*, the specific point (in compass directions, or "c" for center) of the element, window, or offscreen area that we should stick the element to. Valid values are "n,ne,e,se,s,sw,w,nw,c", and also "nne,ene,ese,sse,ssw,wsw,wnw,nnw" for offscreen.
                                                    Note: when affixing to the window or offscreen, you can append "+x,y", "+n" (equal x,y), "-x,y" or "-n" to apply an inset value.
      from ( "sw" )                               - *String*, the specific point that we stick the element from, only when we are affixing to ANOTHER element (. Values are the same as for "to". 
      keepInWindow ( true )	                      - *Boolean*, if true, affix will modify the position in an attempt to keep the element inside the window, but only when affixing to another element. It does this by mirroring the compass direction along the axis that causes the element to fall outside the viewport.   
      offset ( [0,0] )                            - *Array* (int), integer x and y offsets to apply to the element once it has been affixed. Note a single integer can also be provided for equal [x,y] offset.
      inset ( [0,0] )                             - *Array* (int), integer x and y inset values to apply to the element once it has been affixed. Positive "inset" moves the element closer to the inside of that which it is affixed to. 
                                                    Negative "inset" (or outset) moves the element further from the interior of that which it is affixed to. You can also provide ONE integer value for the same inset value for both x and y. 
                                                    Note you can also provide a mix of positive and negative values, and a single integer value can be provided instead for equal [x,y] inset.
                                                    Inset is very different from offset, in that inset calculates an appropriate offset based on the position the element is affixed to.
      use: ( "offset" )                           - *String*, "offset" OR "position"; the positioning function to use when calculating the position relative to the element you're affixing to. 
                                                    Use "offset" (the default) when this element is absolutely positioned relative to the window. Use "position" when this element and the "to" elements are both offset inside the same relatively positioned container.
      smartOffscreenInset                         - *Boolean*, if true, insets will be applied in a more sensible way for offscreen positioning.
      fixed (true)                                - *Boolean*. If this is set to true, affix will use fixed positioning on this element when it can, to keep it in the same place as the viewport is scrolled (this value is always taken as false in IE 6, since it doesn't support fixed positioning).
      classGlueFromPrefix ("glue-")               - *String*, a prefix for a CSS class that will be applied to this element when it is affixed. The suffix of this class is the (eventual) compass point that this element is glued from. 
                                                    This is intended to be used in the case of "tooltips" where we may want to show a "nub" arrow from a specific corner, and we can use this CSS class to do this.  
                                                    (I say "eventual" here, since the keepInWindow option may influence the value of the "to" option depending on where the element is relative to the viewport.)  
      classGlueToPrefix ("glue-")                 - *String*, a prefix for a CSS class that will be applied to the other element (if we are affixing to another element). 
      layerfix (false)                            - *Object*, an options hash for the layerfix plug-in, which will be applied to this element as it is affixed. (The layerfix plug-in addresses the issue in IE 6 where selects are rendered on top of the element)
      autoEvents ({ scroll: true, resize: true }) - *Object*, flags to set up events to "reaffix" this elements when the window is resized or scrolled. 
      el (null)                                   - (An alias for the "element" option, for lazy devs!)
      e (null)                                    - (An alias for the "element" option)
      w   (null)                                  - (An alias for the "win" option)
      os  (null)                                  - (An alias for the "offscreen" option)
      o   (null)                                  - (An alias for the "offscreen" option)

    Compass Points:
      The diagram below shows the compass points available for the "to" option. 
      (see affixmodes.gif)

    Commands:
      clear - Clears the form field hint (only if the field currently has that value
      apply - Re-applies the help text, only if the field value is currently empty  

    Example (Initialisation):
      > $('input.hint').hint({ help: "enter date" });

    Example (Commands):
      > $('#s').hint('clear'); // #s field will clear its value, only if the value matches the help text
      > $('#s').hint('apply'); // #s field will now contain the help text, only if it was previously empty

  */
  
  $.fn.affix = function() {
    
    var cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    return this.each( function() {

      var d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data('affix'); if (!d) { d = {}; el.data('affix', d); } $.extend(true, o, defaults, options, md['affix'] || md || {} ); d.options = o; } else { d = el.data('affix'); if (d) { o = d.options; } else { return; } }
      
      if (!cmd) {
        // standard affix action (not a command)
        
        var info = el.affixPosition.apply(this, a);
  			
  			d.fReaffix = function(event) {
          if (el && el.is(":visible")) { 
            el.affix('again'); 
          }
        };

        
        if (info.target == "window" || info.target == "offscreen") {
          
          if (o.fixed && !ie6) {
    			  // if using fixed position set the element position to fixed if it's not already
    			  var cp = el.css("position");

    			  if (cp != "fixed")
    			    el.css("position", "fixed");
    			}

    			
          if (!d.windowOnResize) {
          
            if (o.autoEvents && o.autoEvents.resize) {
              d.windowOnResize = d.fReaffix;
              $(window).resize(d.windowOnResize);
            }
          }

          if (!d.windowOnScroll) {
            if (o.autoEvents && o.autoEvents.scroll && (ie6 || !o.fixed)) {
          	  d.windowOnScroll = d.fReaffix;
              $(window).scroll(d.windowOnScroll);
            }
          }

        }

        var affixcomplete = function() {
          if (o.layerfix && el.layerfix) {
    			    el.layerfix(o.layerfix);
    			}

			    if (o.animateOptions.complete) {
			      o.animateOptions.complete();
		      }
        };
        
  			
  			if (o.animate)
  			{
  				el.animate( $.extend(true, {}, o.animateParams || {}, { left: info.left, top: info.top }), $.extend(true, {}, o.animateOptions, { complete: affixcomplete }) );
  			}
  			else
  			{
  				el.css( { left: info.left, top: info.top } );
          affixcomplete();
  			}
        
  			
      }
      
      else if (cmd == 'unaffix') {
        
        if (o.layerfix && el.layerfix) {
          el.layerfix('destroy');
        }
        
        el.affix('destroy');
        
      }
      
      else if (cmd == 'again' || cmd == 'reaffix') {
        if (o.element || o.window || o.offscreen) {
          el.affix($.extend(true, {}, o, { reaffixing: true }));
        }
      }
      
      else if (cmd == 'destroy') {
        // remove event handlers, data, and anything else

        if (d.windowOnResize) {
          $(window).unbind("resize", d.windowOnResize);
        	d.windowOnResize = null;
        }
        
        if (d.windowOnScroll) {
          $(window).unbind("scroll", d.windowOnScroll);
        	d.windowOnScroll = null;
        }
        
        if (d.ifs) {
          d.ifs.remove();
        }
        
        d.options = null;

        el.removeData("affix");
      }

    });
      

	};
	
  $.fn.unaffix = function() {
    return this.affix('unaffix');
  };
  
	$.fn.affixAnimate = $.fn.affixa = function(options) {
		return this.affix($.extend(true, {}, options, { animate: true } ));
	};
	
	$.fn.affixAnimateNQ = $.fn.affixanq = function(options) {
		return this.affix($.extend(true, {}, options, { animateOptions: {queue: false} } ));
	};
	
	$.fn.affixAnimateFadeIn = $.fn.affixafi = function(options, opacity) {
		var o = options || {};
		
		return this
			.css({opacity: 0.0})
			.show().affix( $.extend( true, {}, options, { animate: true, animateParams: { opacity: opacity || 1.0 } }));
	};
	
	$.fn.affixAnimateFadeOut = $.fn.affixafo = function(options, opacity) {
		return this
			.css({opacity: opacity || 1.0})
			.affix( $.extend(true, {}, options, { animateParams: { opacity: "hide" }, animate: true, animateOptions: { complete: function() { $(this).hide(); } } }));
	};
	
  
	$.fn.affix.defaults = defaults; // provide public access to defaults
  
})(jQuery);