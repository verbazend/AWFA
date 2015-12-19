
(function($) { 

  var defaults = {
    min: 0,
    max: null,
    step: 1.00,
    format: '0',
    valEmpty: '',
    cnUpDisabled: 'disabled',
    cnDownDisabled: 'disabled',
    cnUpActive: 'active',
    cnDownActive: 'active',
    cnNegative: "negative",
    arrowKeys: true,
    arrowFlashButton: true,
    arrowFlashTimeout: 100,
    repeatOnMouseDown: true,
    repeatDelay: 400,
    repeatInterval: 100,
    restrictInput: true,
    reAllow: /[\$\.\,0-9]/,
    reAllowNegative: /[\$\.\,0-9\-]/
  };
  
  var pn = 'inputspinner';

  var KEY_UP = 38;
  var KEY_DOWN = 40;

  var parseAttr = function(el, name) {
    var attr = el.attr(name);
    
    if (attr && attr != "") {
      return parseFloat(attr);
    }
    
    return NaN;
  };
  
  var isNum = function(val) {
    return val != "" && val !== false && val !== null && !isNaN(val);
  }
  
  
  $.fn[pn] = function() {
    
    var ret = this, all = this, cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    if (this.length) {
      this.each( function() {

        var self, trigger, d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } } self = function() { el[pn].apply(el, arguments); }; trigger = function(n, dt) { if (!p.silent) { var evt = jQuery.Event(n + "." + pn); el.trigger(evt, $.isArray(dt) ? dt : [ dt ] ); return evt.result !== false; } return true; };
      
        var flashButton = function(bt, cn) {
          if (bt && bt.length) {
            bt.addClass(cn);
          
            if (d.ft) {
              clearTimeout(d.ft);
            }
          
            d.ft = setTimeout( function() { bt.removeClass(cn); }, o.arrowFlashTimeout );
          }
        };
        
        var updateButtons = function() {

					if (d.readonly || d.disabled) {

						d.up.addClass(o.cnUpDisabled);
						d.down.addClass(o.cnDownDisabled);

					} else {

						d.up.removeClass(o.cnUpDisabled);
						d.down.removeClass(o.cnDownDisabled);
						
          	if (d.down && o.cnDownDisabled) {
	            if (atMin(d.val)) {
	              d.down.addClass(o.cnDownDisabled);
	            } else {
	              d.down.removeClass(o.cnDownDisabled);
	            }

	          }

	          if (d.up && o.cnUpDisabled) {
	            if (atMax(d.val)) {
	              d.up.addClass(o.cnUpDisabled);
	            } else {
	              d.up.removeClass(o.cnUpDisabled);
	            }
	          }
          
	          if (o.cnUpDisabled && atMax(d.val)) {
	            d.up.addClass(o.cnUpDisabled);
	          }
					
					}
					
        };
        
        var atMax = function(val) {
          if (o.atMax) {
            return o.atMax(el, val, d.max);
          } else {
            return isNum(d.max) && val >= d.max; 
          }
        };

        var atMin = function(val) {
          if (o.atMin) {
            return o.atMin(el, val, d.min);
          } else {
            return isNum(d.min) && val <= d.min; 
          }
        };
        
        var getVal = function() {
          if (o.getVal) { // if a getVal function has been provided
            return o.getVal(el);
          } else {
            return $[pn].parse( el.val() );
          }
        };

        var setVal = function(val) {
          if (o.setVal) { // if a setVal function has been provided
            o.setVal(el, val);
          } 
          else {

            if (o.format) {
              el.val( $[pn].format( val, o.format ) );
            } else {
              el.val( val );
            }
            
            if (val < 0 && o.cnNegative) {
              el.addClass(o.cnNegative);
            } else if (val >= 0 && o.cnNegative) {
              el.removeClass(o.cnNegative);
            }
            
          }
        };

        var clearVal = function(val) {
          if (o.setVal) { // if a setVal function has been provided
            o.clearVal(el, val);
          } 
          else {
            
            el.val( o.valEmpty );
          }

          d.val = el.val();
        };

        var decrement = function(val) {
          if (o.decrement) {
            return o.decrement(val);
          } else {
            return val - d.step
          }
        };
        
        var increment = function(val) {
          if (o.increment) {
            return o.increment(val);
          } else {
            return val + d.step;
          }
        };
        
  
        if (!cmd) {
          // initialization (no command)
          
          d.count = 0;
          
          // look for a min attribute
          var min =  parseAttr(el, "min");
            
          if (isNum(min)) {
            d.min = min;
          } else {
            d.min = o.min;
          }

          // look for a max attribute
          var max =  parseAttr(el, "max");
            
          if (isNum(max)) {
            d.max = max;
          } else {
            d.max = o.max;
          }

          // look for the step attribute
          var step =  parseAttr(el, "step");
            
          if (isNum(step)) {
            d.step = $[pn].parse(step);
          } else {
            d.step = $[pn].parse(o.step);
          }
          
          if (!isNum(d.step)) { // if the step still isn't a number...
            d.step = 1;
          }
          
          
          var stopRepeat = function() {
            clearTimeout(d.to);
            d.to = null; 
            d.count = 0;
          };
          
          var downRepeat = function() {
            self('down');
            
            if (d.to) {
              clearTimeout(d.to);
            }
            
            if (!atMin(d.val)) {
              d.count++;
              d.to = setTimeout(downRepeat, Math.max(o.repeatInterval - ( d.count * 5), 2));
            }
          };

          var upRepeat = function() {
            self('up');
            
            if (d.to) {
              clearTimeout(d.to);
            }

            if (!atMax(d.val)) {
              d.count++;
              d.to = setTimeout(upRepeat, Math.max(o.repeatInterval -  ( d.count * 5), 2));
            }
          };
           
          
          try {
            if (o.up) {
              d.up = $(o.up);
            
              d.up.click( function() {
                self("up");
                stopRepeat();
              });

              if (o.repeatOnMouseDown) {
                // setup mouse down to start spinning quickly after the delay time
                d.up.mouseleave(stopRepeat);
                d.up.mouseup(stopRepeat);
                d.up.mousedown(function() {
                  d.to = setTimeout(upRepeat, o.repeatDelay);
                });
              }

            }

            if (o.down) {

              d.down = $(o.down);
              
              d.down.click( function() {
                self("down");
                stopRepeat();
              });

              if (o.repeatOnMouseDown) {
                // setup mouse down to start spinning quickly after the delay time
                d.down.mouseleave(stopRepeat);
                d.down.mouseup(stopRepeat);
                d.down.mousedown(function() {
                  d.to = setTimeout(downRepeat, o.repeatDelay);
                });
                
              }
              
            }
            
            
            
            if (o.arrowKeys) {
              
              el.keydown( function(event) {
                  
                if (event.which == KEY_UP) {
                  
                  if (o.arrowFlashButton) {
                    flashButton(d.up, o.cnUpActive);
                  }
                  
                  self('up');
                  return false;
                } else if (event.which == KEY_DOWN) {

                  if (o.arrowFlashButton) {
                    flashButton(d.down, o.cnDownActive);
                  }
                  
                  self('down');
                  return false;
                }
                
                return true;
              });
            }


						if (el.attr("readonly") == "readonly") {
							self('readonly', true);
						}

						if (el.attr("disabled") == "disabled") {
							self('disabled', true);
						}

            
            
          } catch (e) {
            alert(e); 
          }
          
          
          if (o.restrictInput && $.fn.inputmask) {
            
            var reAllow = o.reAllow;
            
            if (d.min < 0) {
              reAllow = o.reAllowNegative; // allow minuses 
            }
            
            el.inputmask({ reAllow: reAllow, convert: null });
          }
          
          // setup change event to prevent the value
          
          el.change(function() {
            d.val = getVal();
            
            if (o.min != null && atMin(d.val)) {
              setVal(o.min);
            }

            if (o.max != null && atMax(d.val)) {
              setVal(o.max);
            }
            
            
            updateButtons();
          });
          
          // grab the initial value
          d.val = getVal();

          // format the initial value
          
          
          if (d.val != null && d.val != '' && o.format) {
            el.val( $[pn].format( d.val, o.format ) );
          }
          
          // update initial button state
          updateButtons();

        } 
        else if (cmd == 'up') {
          
					if (!d.disabled && !d.readonly) {
          	if (!atMax(d.val)) {

	            var oldVal = getVal();
	            var newVal = increment(oldVal);
          
	            if (o.max != null && atMax(newVal)) {
	              newVal = o.max;
	            }

	            // prepare event data
	            var ed = { oldVal: oldVal, newVal: newVal, val: newVal };
          
	            if (trigger("beforeup", ed) && trigger("beforechange", ed)) {
	              setVal( newVal );
	              d.val = newVal;
	              trigger("afterup", ed);
	              trigger("afterchange", ed);
	              updateButtons();
	            }
          
	          }
          }
        } 
        else if (cmd == 'down') {
					
					if (!d.disabled && !d.readonly) {
					
	          if (!atMin(d.val)) {
            
	            var oldVal = getVal();
	            var newVal = decrement(oldVal);

	            if (o.min != null && atMin(newVal)) {
	              newVal = o.min;
	            }

        
	            // prepare event data
	            var ed = { oldVal: oldVal, newVal: newVal, val: newVal };

	            if (trigger("beforedown", ed) && trigger("beforechange", ed)) {
	              setVal( newVal );
	              d.val = newVal;
	              trigger("afterdown", ed);
	              trigger("afterchange", ed);
	              updateButtons();
	            }
          
	          }
					
					}
					
        }
				else if (cmd == 'readonly') {
					d.readonly = p;
					updateButtons();
				}
				else if (cmd == 'disabld') {
					d.disabled = p;
					updateButtons();
				}
        else if (cmd == 'update') {
          // grab the initial value
          d.val = getVal();
          updateButtons();
        }
        else if (cmd == 'clear') {
          // grab the initial value
          clearVal(o.emptyVal);
          updateButtons();
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
  
  $[pn] = {};
  
  
  
  
  // some utility functions for working with numbers
  
  $[pn].repeat = function(str, count)
  {
  	var ret = '';

  	for (var i=0; i<count; i++)
  		ret += str;

  	return ret;
  };
    

  /*
      Method: leadingZero
          gets a *string representation* for a given number *padded out with leading zeros to a given length*

      Arguments:
          number - Number, the number to append leading zeros to
          toLength - integer, optional, the total length of the final leading-zero-padded string. 
                    If not specified, defaults to a length of 2

      Examples:
          > $.inputnumber.leadingZero(1);        // 01
          > $.inputnumber.leadingZero(1, 3);     // 001
          > $.inputnumber.leadingZero(10, 3);    // 010
          > $.inputnumber.leadingZero(1000, 3);  // 1000

  */

  $[pn].leadingZero = function(number, toLength) {
    return $[pn].repeat('0', (toLength || 2) - number.toString().length) + number.toString();
  };

  /*

    Method: format
        *formats a given number* in a *specified display format*

    Arguments:
        number - Number object, the number to format
        format - string, describes the format of the output string (see examples below)

    Returns:
        string - the formatted output string

    Examples:
        > $.inputnumber.format(3129.95, "$#,###.##");  // $3,129.95   
        > $.inputnumber.format(3129.95, "$####.##");   // $3129.95 	  
        > $.inputnumber.format(329.95, "$#####.##");   // $329.95 	  
        > $.inputnumber.format(329, "$###");           // $329 		  
        > $.inputnumber.format(-329, "(###)");         // (329)		  
        > $.inputnumber.format(-1234.95, "#,##0.##");  // -1,234.95	  
        > $.inputnumber.format(0.01, "#.##");          // 01		  
        > $.inputnumber.format(0.01, "#.###");         // 010		  
        > $.inputnumber.format(0.01, "0.##");          // 0.01		  
        > $.inputnumber.format(2, "00");               // 02		  
        > $.inputnumber.format(2345, "00000");         // 02345		  
        > $.inputnumber.format(45, "00000");           // 00045

    Formatting Rules:
        > # - substitutes for a number, but only if this position has a definite non-zero number here
        > 0 - substitutes for a number always, using zero if this position has no definite non-zero number here
      
  */

  $[pn].parse = function(str, fallback) {
    
    var fl;
    
    fallback = fallback || 0;
    
    if (str.toString()) {
      str = str.toString();
    }
    
    if (str) {
      var stripped = str.replace(/[^0-9\.\+\-]/ig, '');
      var fl = parseFloat(stripped);
    }
    
    if (isNaN(fl)) {
      return fallback;
    }

    
    return fl;
  };
  
  $[pn].stringOfChar = function(str, length) {
    
    var ret = '';
    
    for (i=0; i < length; i++) {
      ret+=str;
    }
    
    return ret;
  };
  
  $[pn].format = function(number, format) {
    var formatted;
    var formattedDec = '';
    var formattedWhole = '';

    var strWhole;

    var value = Math.abs(number);
  	var valueWhole = Math.floor(value);

  	var formatter = format;

  	var parenthesis = false;

  	// check if negative values should use parenthesis formatting

  	var matches = formatter.match(/\((.*?)\)/, "ig");

  	if (matches && matches.length > 0) {
  		parenthesis = true;
  		// take the rest of the string as the actual formatter
  		formatter = matches[1];
  	}

  	var formatterWhole = formatter;

  	var parts = formatter.split(".");

  	if (parts.length > 1) {
  		// the string has a decimal part
  		value = value.toFixed(parts[1].length);	

  		formatterWhole = parts[0];
  	}
  	else {
  		valueWhole = Math.round(value);	
  	}

  	// now work out how to format the whole number part
  	formatted = value.toString();

  	if (parts.length > 1) {
  		formattedDec = "." + formatted.split(".")[1];
  	}

  	strWhole = Math.abs(valueWhole).toString();

  	// first, pad out formatterWhole up to the length of valueWhole, with #  

  	var count = 0;

  	$.each( formatterWhole.split(""), function (index, chr) {
  		if (chr == '#' || chr == '0')
  			count++;
  		}
  	);

  	matches = formatterWhole.match(/[^#0,]*?([#0,]+)[^#0,]*?/);

  	if (matches.length > 1) {
  		formatterWhole = formatterWhole.replace(matches[1], $[pn].stringOfChar('#', strWhole.length - count) + matches[1]);
  	}

  	var formatterChars = formatterWhole.split("");

  	var digitIndex = strWhole.length - 1;


  	for (var i = formatterChars.length - 1; i>=0; i--) {
  		// process each character in the formatter string 

  		var chr = formatterChars[i];
  		
  		var ten = Math.pow(10, strWhole.length - 1 - digitIndex);


  		if (chr == '#') {
  			if (valueWhole >= ten) {
  				formattedWhole = strWhole.substr(digitIndex, 1) + formattedWhole;
  			}
  			// otherwise add nothing
  			digitIndex = digitIndex - 1;	
  		} else if (chr == '0') {
  			if (valueWhole >= ten) {
  				formattedWhole = strWhole.substr(digitIndex, 1) + formattedWhole;
  			} 
  			else {
  				// otherwise add a 0
  				formattedWhole = '0' + formattedWhole;
  			}

  			digitIndex = digitIndex - 1;
  		}
  		else if (chr == ',') {
  			if (valueWhole >= ten) {
  				formattedWhole = chr + formattedWhole;
  			}
  		}
  		else {
  			formattedWhole = chr + formattedWhole;
  		}
  	}

  	// apply the parenthesis if the original value is negative

  	if (number < 0) {
  		if (parenthesis)
  			return '(' + formattedWhole + formattedDec + ')';
  		else
  			return '-' + formattedWhole + formattedDec;
  	}

  	return (formattedWhole + formattedDec);

  };
  
  
})(jQuery);