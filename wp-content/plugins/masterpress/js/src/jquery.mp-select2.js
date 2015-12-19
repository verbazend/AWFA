
(function($) { 

  function splitVal(string, separator) {
      var val, i, l;
      if (string === null || string.length < 1) return [];
      val = string.split(separator);
      
      for (i = 0, l = val.length; i < l; i = i + 1) val[i] = $.trim(val[i]);
      return val;
  }
 

  var defaults = {
    allowClear: true,
    sortable: true,
    layout: 'float',
    resultsMinHeight: 46,
    alwaysBelow: true,
    resultsBuffer: 5, // minimum height buffer for results
    dropGap: 20, // gap to leave between bottom of dropdown and window
    width: "resolve",
    allowPositionAbove: false
  };
  
  var pn = 'mp_select2';

  $.fn[pn] = function() {
    
    var ret = this, all = this, cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    if (this.length) {
      this.each( function() {

        var self, trigger, d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } } self = function() { el[pn].apply(el, arguments); }; trigger = function(n, dt) { return el.trigger(jQuery.Event(n + "." + pn), $.isArray(dt) ? dt : [ dt ] ) !== false; };
   
        if (cmd) {
          el = d.el;
        }
        

        if (!cmd) {
          // initialization (no command)
          
          // copy supportable options across
          
          var s2o = $.extend({}, true, o);
          
          delete s2o['layout'];
          
          d.multiple = el.attr("multiple");
          d.data = o.data;
          
          var objVal = null;
          
          if (el.hasClass("select2-source")) {
          
            objVal = [];
            
            // create the data from the select (unobtrusive setup - would allow standard controls on mobile, for example)
            
            d.data = [];
            d.data_by_id = {};
            
            var $items = el.find("> option, > optgroup");
            
            $items.each( function() {
            
              var $item = $(this);
              
              if ($item.is("optgroup")) {

                var og = { text: $item.attr("label"), children: [], cls: $item.attr("class") || "" };
                
                og.selection_prefix = $item.data("selection-prefix");
                
                
                $item.find("option").each( function() {
                  var $option = $(this);
                  var text = $option.html();
                  var item = { text: text.mp_entity_decode(), id: $option.attr("value") || text, parent: og, cls: $option.attr("class") || "" };
                
                  if ($option.attr("selected")) {
                    objVal.push(item.id);
                  }
                  
                  // here, we'd extend the item with other known properties embedded in data attributes
                  item = $.extend( item, $option.data() );
                
                  og.children.push( item );
                  d.data_by_id[item.id] = item;
                });
              
                d.data.push( og );

              } else {
                
                var id = $item.attr("value");
                
                if (id != "") {
                  
                var text = $item.html();
                var item = { text: text.mp_entity_decode(), id: $item.attr("value") || text, cls: $item.attr("class") || "" };
                
                item = $.extend( item, $item.data() );
                
                if ($item.attr("selected")) {
                  objVal.push(item.id);
                }

                d.data.push( item );
                d.data_by_id[item.id] = item;
                
                }

              }
              
            });
            
            var val = el.val();
            
            // now build a hidden input
            
            var val_id = el.data("value-input");
            
            var $val_input = $();
            
            try {
              $val_input = $("#" + val_id);
            } catch (e) {
              
            }
            
            if ($val_input.length) {
              d.el = $val_input;
              
              // setup the correct object array to set the initial values IN THE CORRECT ORDER!
              
              objVal = [];
              
              $.each( $val_input.val().split(","), function(index, val) {
                var item = d.data_by_id[val];
                
                if (item != undefined && item != null) {
                  objVal.push( item.id );
                }
              });
              
            } else {
              
              d.el = $('<input>').attr({
                "type" : "hidden",
                "name" : el.attr("name").replace(/\[\]$/, '') // ensure that the name is not an array-style as this won't work
              });
            
              d.el.val(el.val());
            }
            
            d.el.attr({
              "id" : el.attr("id"),
              "data-placeholder" : el.data("placeholder"),
              "style" : el.attr("style")
            });
            
            d.input = d.el;
            
            // set the width of the hidden input, so that select2 can pick it up properly.
            
            d.el.css("width", "99%");
            
            d.el.bind("change", function() {
              el.trigger("change");
            });
            
            if (el.attr("multiple")) {
              s2o.multiple = true;
              o.multiple = true;
            }
            
            // rename the original so that submissions go through on the hidden control
            el.attr("id", "source-" + el.attr("id"));
            el.attr("name", "source-" + el.attr("name"));

            d.el.insertAfter(el);
            el.hide();
            
            var filterMatch = function( item ) {
              
              if (!item.cls) {
                return true;
              }
              
              return item.cls.split(" ").indexOf(d.filter) > 0;
              
            };
            
            // setup the custom query function, which supports optgroups
         
            s2o.query = function (query) {
              
              var term = $.trim(query.term);
                  
              var all = d.data;

              var matches = [];
              
              for (var i=0; i<all.length; i++) {
                
                var item = all[i];
                
                if (term == "" && !d.filter) { // everything matches an empty term

                  matches.push(item);

                } else {
                  
                  if (!item.children && (term == "" || query.matcher(query.term, item.text))) {
                    if (d.filter) {
                    
                      if (filterMatch(item)) {
                        matches.push(item);
                      }

                    } else {
                      matches.push(item);
                    }
                  }
                  else if (item.children) {
                         
                   // check the children - if any of them match, add the header with matching children too
                   var childMatches = [];

                   for (var j = 0; j<item.children.length; j++) {
                     var child = item.children[j];
                   
                     if (term == "" || query.matcher(query.term, child.text)) {
                       if (d.filter) {
                         if (filterMatch(child)) {
                           childMatches.push(child);
                         }
                       } else {
                         childMatches.push(child);
                       }
                     }
                   
                   }

                   if (childMatches.length) {
                     matches.push({ text: item.text, children: childMatches });
                   }
                  
                  }
                
                }
                
              }
              
              query.callback({ results: matches });
          
            }; 
            
            
            s2o.initSelection = function(element, callback) {
    
              var data = [];
  
              var split = splitVal(element.val(), ",");
              
              $(split).each(function (index, val) {
                var $o = el.find('option[value="' + val + '"]');
                var d = {id: val, text: val, icon: $o.data("icon")};
                data.push(d);
              });
              
              if (!d.multiple && data.length) {
                data = data[0];
              }
            
              callback(data);
            };
          
          
            
    
            
            
          } // select source
          else {
            d.el = el;
          }

          
          s2o.formatResult = function(result, label, query) {
    
            var markup = [];
    
            markup.push('<div class="result ');
    
            if (!result.image) {
              markup.push(result.cls);
            }
  
            markup.push('">');

            var text = result.text, term = query.term;
    
            var match = text.toUpperCase().indexOf(term.toUpperCase()),
                tl = term.length;
        
            if ( result.children) {
              markup.push(text);
              markup.push('</div>');
              return markup.join("");
            }
    
            if (result.no_image) {
              markup.push('<span class="no-image');

              if (!result.excerpt) {
                markup.push(' small');
              }
      
              markup.push('">');
              markup.push(result.image);
              markup.push('</span>');
            } else if (result.image) {
              markup.push('<img ');

              if (result.image_width) {
                markup.push(' width="' + result.image_width + '" ');
              }

              if (result.image_height) {
                markup.push(' height="' + result.image_height + '" ');
              }
      
              markup.push('src="' + result.image + '">');        
            }

    
            if (result.image) {
              markup.push('<div class="info">');
      
              if (result.excerpt) {
                markup.push('<h5>');
              } else {
                markup.push('<h4>');
              }
    
            }
    
            if (result.icon && !result.image) {
              markup.push('<i class="' + result.icon + '"></i>');
            }
            
            if (match < 0) {

              markup.push('<span class="select2-match">');
              markup.push(text);
              markup.push('</span>');

            } else { 
    
              var hltext = text.substring(match, match + tl);
    
              markup.push(text.substring(0, match));
              markup.push('<span class="select2-match">');
    
              var len = $.trim(hltext).length;
    
              if (len) {
                markup.push("<b>");
              }
    
              markup.push(hltext);
    
              if (len) {
                markup.push("</b>");
              }
    
    
              markup.push("</span>");
              markup.push(text.substring(match + tl, text.length));
    
            }
  
            if (result.image) {
              if (result.excerpt) {
                markup.push('</h5>');
              } else {
                markup.push('</h4>');
              }
            }
  
            if (result.no_excerpt) {
              markup.push('<p class="no-excerpt">');
            } else if (result.excerpt) {
              markup.push('<p>');
            }
    
    
            if (result.excerpt) {
              markup.push(result.excerpt);        
              markup.push('</p>');        
            } 

            if (result.image) {
              markup.push('</div>');
            }
    
    
            markup.push("</div>");

            return markup.join("");
          };
  
          s2o.formatSelection = function(sel) {
    
            if (sel) { // sometimes there is no object? (not sure why)
              
              var obj = d.data_by_id ? d.data_by_id[sel.id] : sel;
              
              if (obj) {
                var markup = [];
    
                if (sel.icon) {
                  markup.push('<i class="' + sel.icon + '"></i>');
                }
                
                markup.push('<span class="text ');
                markup.push(obj.cls);
                markup.push('">');
                
                if (obj.parent && obj.parent.selection_prefix) {
                  markup.push('<b>');
                  markup.push(obj.parent.selection_prefix);
                  markup.push("</b>");
                }
    
                markup.push(obj.text);
                markup.push('</span>');
    
                return markup.join("");
              
              }
              
            }
    
          };
          
          
          d.el.select2(s2o);

					if (s2o.disabled) {
						d.el.select2("disable");
					}
					
          if (objVal) {
            
            if (o.multiple) {
              d.el.select2('val', objVal);
            } else {
              if (objVal.length) {
                d.el.select2('val', objVal[0]);
              }
            }
          
          }

          // setup pointers to select2 elements

          // main element
          d.s2 = d.el.select2('container');
					
					var maxWidth = d.el.css("max-width");
					
					if (maxWidth.match(/\d+px/)) {
						d.s2.css("max-width", parseInt(maxWidth) + "px");
					} else {
						d.s2.css("max-width", "580px");
					}
					
					d.s2.addClass("mp-select2");
					
          // sub-elements
          d.s2_results = d.s2.find('.select2-results');
          d.s2_search = d.s2.find(".select2-search");
          d.s2_drop = d.s2.find(".select2-drop");
          d.s2_input  = d.s2.find(".select2-input");

          

          d.drop_resize = function(event) {

            d.last = d.last || {};
            

            var resize = true;
            
            
            if (event.which == 9 || event.which == 13 || event.which == 27 || (event.which >= 33 && event.which <= 40) ) {
              resize = false;
            }
          
            if (resize) {
              
              var drop_vis = d.multiple ? d.s2_drop.is(":visible") : d.s2_drop.hasClass("select2-drop-active");
              
              if (drop_vis) {
								
                var st = $(window).scrollTop();
                var oft = d.s2.offset().top;

                var wh = $(window).height();
                var top = (oft - st);
                
                var selectable = d.s2_results.find(".select2-result-selectable");
                
                
                 d.last.top = top;
                 d.last.wh = wh;
                
                 d.s2_results.css("max-height", "none");
              
                 var sh = d.s2_search.outerHeight();
                 var rh = d.s2_results.outerHeight();
                 var ch = d.s2.outerHeight();
          
                 var drop_height = Math.max( sh + o.resultsMinHeight, Math.min(wh - top - ch - o.dropGap, rh + sh + o.resultsBuffer ) );
          
              
                 d.s2_drop.css("height", drop_height);
          
                 // now resize the results, to overflow
          
                 d.s2_results.css("max-height", drop_height - sh - 3);
              
                 // now scroll to the highlighted result
              	
                 var hl = d.s2_results.find(".select2-highlighted");
              
                 var prev = hl.prev(".select2-result");
              
                 if (!prev.length) {
                   var sub = hl.closest("ul.select2-result-sub");
                
                   if (sub.length) {
                     hl = sub.prev(".select2-result-label");
                   }
                 }
              
                
              }
            
            }
            
          };
          
          d.el.bind("aftershow", d.drop_resize);
					d.s2_input.bind("keyup", d.drop_resize);
          
          // $(window).bind("resize", d.drop_resize);
          
					/*
          if (d.multiple) {

            // patch a width bug for stylesheet based width - check that there is no width set
            
            style = d.el.attr("style");
            
            var present = false;
            
            if (style !== undefined) {
                attrs = style.split(';');
                for (i = 0, l = attrs.length; i < l; i = i + 1) {
                    matches = attrs[i].replace(/\s/g, '')
                        .match(/width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/);
                    if (matches)
                        present = true;
                }
            }

            if (!present) {
              d.s2.css("width", "+=2");
            }
            
            //d.s2.css({ maxWidth: d.el.css("width"), width: "97%" });

          }
					*/

          
          if (o.sortable && !o.disabled && !d.el.is("select")) {
           
            d.s2.addClass("select2-sortable").find("ul.select2-choices").sortable({
                tolerance: 'pointer',
                containment: 'parent',
                start: function() { d.el.select2("close") },
                update: function() { d.s2.select2("onSortEnd"); }
                
            });
            
          }
          
          if (o.layout == "block") {
            d.s2.addClass("select2-block");
          }
          
        }
        else if (cmd == 'filter') {
          d.filter = p;
        }
        else if (cmd == 'unfilter') {
          d.filter = null;
        }
        else if (cmd == 'labels') {
          var val = d.el.select2('val');
          
          ret = [];
          
          if ($.isArray(val)) {
            
            for (i=0; i<val.length; i++) {
              var item = d.data_by_id[val[i]];
              
              if (item != undefined && item != null) {
                ret.push(item.text);
              }
            }
            
          } else {
            
            var item = d.data_by_id[val];
              
            if (item != undefined && item != null && item.text) {
              ret = [ item.text ];
            }
          
          }
          
        }
        else if (cmd == 'label') {
          var val = d.el.select2('val');

          ret = "";
          
          var item = d.data_by_id[val];
              
          if (item != undefined && item != null && item.text) {
            ret = item.text;
          }
          
        }
        else if (cmd == 'selections') {
          // return the full object for the selection (including its metadata)
          var val = d.el.select2('val');
          
          if ($.isArray(val)) {
            
            ret = [];
            
            for (i=0; i<val.length; i++) {
              var item = d.data_by_id[val[i]];
              
              if (item) {
                ret.push(item);
              }
            }
            
          } else {
            ret = [ d.data_by_id[val] ];
          }
          
        }
        else if (cmd == 'selection') {
          // return the full object for the selection (including its metadata)
          var val = d.el.select2('val');
          
          ret = false;
          
          if ($.isArray(val)) {
            if (val.length) {
              ret = d.data_by_id[val[0]];
            }
          } else {
            ret = d.data_by_id[val];
          }
          
        }
        else if (cmd == 'val') {


          if ($.isEmptyObject(p)) {
            // this is a get 
            ret = d.el.select2('val');
          } else {
            
            // patch to accept other formats - CSV, literal, etc
            
            var vals = [];

            var val = p;
            
            // first, normalize val to an array of objects with "id" key-value pairs
            
            if ( typeof val == "string" ) {
              // split into an array
              val = val.split(",");
            } 
            

            if (!$.isArray(val)) {
              val = [ val ];
            }

            $.each( val, function(index, item) {
              
              var id = item.id || item;
              
              // lookup our data store
              var item = d.data_by_id[id];
              
              if (item != undefined && item != null && item.text) {
                vals.push(item.id);
              }
              
            });
            
            
            if (d.multiple) {
              d.el.select2('val', vals);
            } else {
              
              // ensure the field is set first (bug??)
              d.el.val(vals[0]);
              // only set the first item
              d.el.select2('val', vals[0]);
            } 
            
          }
          
        }
        else if (cmd == 'focus') {
          d.s2_input.focus();
        }
        else if (cmd == 'destroy') {
          // remove event handlers, data, and anything else
          el.data(pn, null);
        } 
        else { // pass the command on
          
          var result;
          
          if ($.isEmptyObject(p)) {
            ret = d.el.select2(cmd);
          } else {
            ret = d.el.select2(cmd, p);
          }

        }
        
        

      });
    }

    return ret;
  };
  
  $.fn[pn].defaults = defaults;
  
})(jQuery);