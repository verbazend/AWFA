(function($) {

  $.widget( "ui.mp_thumb", {

  	options: {

  	},

  	_create: function(options) {

  		this.thumb = this.element.find("img");
  	  this.link = this.element.find("a");

  	  this.setup_2x();
		  this.setup_fancybox();

  	},
    
		
		setup_2x: function(reset) {

			if ($.mp.is2x) {

				// adjust container and image width so that small images are presented at full-res for retina displays.

				// get the width and height being rendered
				var w = parseInt( this.element.css("width") );
				var h = parseInt( this.element.css("height") );
				
				// get the actual image dimensions from the data attributes
				var iw = this.thumb.data("width");
				var ih = this.thumb.data("height");

        
				if (w == 0) {
				  var w = this.thumb.data("thumb_width");
  				var h = this.thumb.data("thumb_height");
			  }

        this.element.css("width", this.thumb.data("thumb_width"));
        
				if (iw < w * 2) {

          this.element.css("width", '');

          this.thumb.data("thumb_width", parseInt(this.thumb.data("thumb_width")) / 2);
          this.thumb.data("thumb_height", parseInt(this.thumb.data("thumb_height")) / 2);
					
					if (this.link.length) {
						this.link.css("width", iw / 2);
						this.link.css("height", "auto");
					} else {
            this.element.css("width", iw / 2);
						this.element.css("height", "auto");
					}
				
					this.thumb.css("width", iw / 2);
					this.thumb.css("height", "auto");
					this.thumb.addClass("small-2x");

				} else if (reset) {
					
					if (this.link.length) {
						this.link.css("width", w);
						this.link.css("height", "auto");
					} else {
						this.element.css("width", w);
						this.element.css("height", "auto");
					}
          
					this.thumb.css("width", w);
					this.thumb.css("height", "auto");
					this.thumb.removeClass("small-2x");
					
					
				}
			
			}
			
		},
		
		set_info: function(info) {
			
			var self = this;
			
			var create_image = !this.thumb.length;
			var create_link = info.href && !this.link.length;
			
			if (create_image) {
				self.thumb = $("<img />");
			}
			
			self.thumb
				.attr({ src: info.thumb, width: info.thumb_width, height: info.thumb_height })
				.data({ width: info.width, height: info.height, thumb_width: info.thumb_width, thumb_height: info.thumb_height });
				

			if (create_link) {
				self.link = $('<a />');
			}

			self.link.attr( $.extend({}, true, { href : info.href }, info.link_attr || {} ) ); 
			
			self.element.removeClass("empty");

			if (info.title) {
				self.element.attr("title", info.title);
			}
			
			if (info.href) {
				
				self.link.append( self.thumb );
				self.link.css({ width: info.thumb_width, height: 'auto' });
				self.element.append( self.link );

			} else {
				
				if (self.link.length) {
					self.link.remove();
					self.fancybox = false;
				}

				self.element.append( self.thumb );
				self.element.css({ width: info.thumb_width, height: 'auto' });
			}
			
			if (create_link) {
				self.setup_fancybox();
			}
			 
			self.setup_2x(true);
			
		},
		
		clear: function() {
			
			if (this.thumb.length) {
				this.thumb.remove();
			}

			if (this.link.length) {
				this.link.remove();
				this.fancybox = false;
			}
			
			this.element.addClass("empty");
			this.element.css({ height: "auto", width: "auto" }); 

		},
		
		setup_fancybox: function() {
		
			var self = this;
			
			if (!self.fancybox && self.link.length) {
				
				var options = {
	  			overlayShow: false,
	  			transitionIn	: 'elastic',
	  			speedIn: 200,
	  			speedOut: 200,
			
					is2x: $.mp.is2x,
	    		overlayShow : true,
	    		overlayOpacity : 0.4,
	    		overlayColor : '#000',

	  			transitionOut	: 'elastic',
	  			hideOnContentClick: true,
	  			padding: 8,
					onStart: function() {
						window.mp_fancy_box_element = self.link;

	  			  if (!self.link.attr("href")) {
	  			    return false;
				    }
				  }
	  		};
	
				if (self.link.data("type")) {
					options.type = self.link.data("type");
				}
				
				// mark as initialised
				self.fancybox = true;
				
				self.link.fancybox(options);
		
				self.link
					.bind("focus", function() {
						self.element.addClass("focus");
					})
					.bind("blur", function() {
						self.element.removeClass("focus");
					})
					.bind("fancybox-cleanup", function() {
						if (window.mp_fancy_box_element == self.link) {
							self.link.focus();
						}
					});
			
      	
			}
			
		}
    
  });
  
  
})(jQuery);





(function($) { // closure and $ portability

  $(document).ready( function() {

    // compile the tooltip template
    
    window.mptt_template = Handlebars.compile($('#mp-field-label-tooltip-template').html());
    


    $.fn.serializeDeep = function() {
    
      // serializes a form with complex input names ( referring to an eventual associative array structure ) into a format that a PHP action can work with
      
      var items = $(this).serializeArray();
      var data = {};

      $.each(items, function(index, tuple) {

        var matches = tuple.name.match(/([a-z0-9\_]+$)/i);
        
        if (matches && matches.length == 2) {

          // this is a simple "name=value" tuple
          
          data[tuple.name] = tuple.value;

        } else {
        
          // this is a complex associative array key - build an object that follows the path
          
          var matches = tuple.name.match(/([a-z0-9\_]+)(\[.*\])/i);

          if (matches && matches.length == 3) {
            var name = matches[1];
            var keys = matches[2];
        
            if (!data[name]) {
              data[name] = {};
            }
        
            var path_matches = keys.match(/\[[^\]]*\]+/ig);
        
            var base = data[name];
            
            var keybuild = "";
            var key = "";
            
            $.each( path_matches, function(si, part) {
              
              var key = part.replace(/\[/, "").replace(/\]/, "");
              
              if (key == "") {

                if (!$.isArray(base)) {
                  base = [];
                }

                if (si == path_matches.length - 1 ) {
                  base.push(tuple.value);
                } else {                
                  base.push({});
                  base = base[key];
                }

              } else {
                              
                keybuild += part; 

                if (!base[key]) {
                  base[key] = {};
                }

                if (si == path_matches.length - 1 ) {
                  base[key] = tuple.value;
                } else {  
                  prev = base; 
                  base = base[key];
                }

              }
              
            
            });
        
        
          }
        
        }
        
        
      });
          
      return data;
    };
    

    $.fn.mp_tooltip_setup = function() {
      
      return this.each( function() {
        
        var el = $(this);
        
        var $tt = $();
        
        if (el.data("tooltip")) {
          $tt = $(window.mptt_template({ text: el.data("tooltip") }));
          $tt.hide().appendTo("body");
        } else {

          if (matches = el.attr("href").match(/(#.*?$)/)) {
            var ttid = matches[1];
            var $tt = $(ttid);

            if ($tt.length && !$tt.data("mptt")) {
              $tt.data("mptt", true);
              var $tooltip = window.mptt_template({ text: $tt.html() });
              $tt.hide().empty().append($tooltip).appendTo("body");
            } 
          } 
        
        }
        
        if ($tt.length) {
          el.reveal({ el: $tt, triggerEvent: "hover", show: "fadeIn", hide: "fadeOut", hideOnTargetMouseLeave: true, showDelay: 100, hideDelay: 0, showOptions: { duration: 200 }, hideOptions: { duration: 100 }, affix: { from: "sw", to: "nw", offset: [ -10, 6 ] }})
          
          el.data("revealInitTimeout", setTimeout(function() { el.reveal("show") }, 100 ));
          el.bind("mouseleave", function() { clearTimeout(el.data("revealInitTimeout")) } );
          
          el.bind("blur", function() {
            $(this).reveal("hide");
          });
        }
        
      });
      
    };
    
    $.fn.mp_tooltip = function(options) {
      return this.each( function() {

        $(this).bind({
          mouseenter: function() {
      
            var el = $(this);
      
            if (!el.data("reveal")) {
              el.mp_tooltip_setup();
            }
      
          }
        });
          
        if (!$(this).hasClass("mp-go")) {
          
          $(this).bind({
            click: function() {

              var el = $(this);
    
              if (!el.data("reveal")) {
                el.mp_tooltip_setup();
              }
      
              $(this).reveal("show");
      
              return false;
      
            }
          });
        
        }

      });
      
    };
    
    $(".mp-tooltip-icon,.with-mptt").mp_tooltip();


    if (window.mpft_script) {
      $.getScript( window.mpft_script );
    }
    
    $.fn.checkboxAltLabel = function() {

      return this.each( function() {
        var $el = $(this);
        
        if (!$el.data("checkboxAltLabel")) {
          
          $el.data("checkboxAltLabel", true);
            
          $el.click( function() {
            
            var for_el = $(this).metadata({ type: 'class' }).for_el;
        
            $chk = $();
        
            if (for_el) {
              try {
                $chk = $(for_el);
              } catch (e) { 
                $chk = $();
              }
            }
        
            if (!$chk.length) {
              $chk = $el.prev("input[type=checkbox]");
            }

            if ($chk.length) {
              $chk.focus();
              if ($chk.is(":checked")) {
                $chk.removeAttr("checked");
              } else {
                $chk.attr("checked", "checked");
              }
          
              $chk.change();
            }
          });
        }
      
      });
    
    };
    
		// setup select2 for templates
		
		if ($('#mp-attributes').length) {
			$('#mp-attributes select').mp_select2();
		}
			
		// -- run picturefill
		$('.picture').picturefill();
		
    // setup masterpress thumbnail elements
    $(".mp-thumb:not(.managed)").mp_thumb();
			
    $('select.custom-taxonomy-filter')
      .find("option")
        .each( function() {
          $(this).data("html", $(this).html());
        })
        .end()
      .change( function() {
        
        var md = $(this).metadata({ type: 'class' });
        
        var $options = $(this).find("option");
        
        $options.each( function() {
          $(this).html($(this).data("html"));
        });
        
        if (md.taxonomy && $(this).val() != '') {
          var $os = $options.filter(":selected");
          $os.html(md.taxonomy + ": " + $os.html().replace(/\&nbsp\;/gi, "") + "&nbsp;");
        }
        
      })
      .change();
    
    var $mpv = $('.mpv');
    
    var $mpvAdmin = $('.mpv-admin');
    
    
    
    // setup spinners
    
    $('.auto-spinner').each( function() {
      
      var $el = $(this);
      
      $el.find("input").inputspinner({
        up: $el.find(".up"),
        down: $el.find(".down"),
        step: 1,
        min: 0
      });
      
    });
    
    
    if ($mpvAdmin.length) {
      
      var fillVars = function() {};
      
      // find the lang
    
      var lang = $('.mpv').metadata({ type: 'class' }).lang_short || "en";
      
      $grids = $('table.grid');

      // focus the first input

      $updated = $grids.find("tr.updated");
      
      if ($updated.length) {
        $.scrollTo($updated.closest("table"));
      }
      
      // hide the updated class after 2 seconds
      setTimeout( function() {
        $updated.removeClass("updated");
      }, 2000);
      
      $grids.find("a[disabled=disabled]").click( function() { return false; } );
      
      $('.mpv form').find("input[type=text],input[type=checkbox],input[type=radio],textarea").not(".readonly").eq(0).focus();
      
      var gridSortableStop = function() {

        $grids.each( function() {
    
          var $tbody = $(this).find("tbody");  
          var $orphanedRows = $(this).find(">tr");
        
          // move orphaned rows into the 
          $orphanedRows.each( function() {
            $(this).appendTo($tbody);
          });
        
          var $rows = $(this).find("tbody tr.sub");
    
    
          $rows
          .removeClass("first even")
          .filter(":odd").addClass("even").end()
          .eq(0).addClass("first");
      
        });
      
      };
  
      var $gridRows = $('table.grid tbody tr.editable,table.grid tbody td.manage');
    
      $gridRows
        .linkify({ selectorNoClosest: "a,td.sort,th.sort,td.actions,th.actions,tr.panel" });

    
      // setup the column builder
      
      var $cb = $('.fs-column-builder');
      
      if ($cb.length) {
        
        var $cbci = $cb.find("ul.column-items");
      
        $cb.find('.columns').sortable({
          items: "li:not(.nosort)", 
          tolerance: "pointer", 
          stop: function() {
            $cb.find('.column').each( function(index, el) {

              var $el = $(el);
              
              $el.find("input.text,input[type=hidden],textarea").each( function(item_index, item) {
                var $item = $(item);
                var name = $item.attr("name");
                var new_name = name.replace(/\[\d+\]/i, "[" + ( index ) + "]");
                $item.attr("name", new_name);
              });
              
              
            });
            
          }
        });
      
        var colRemoveClick = function() {
          var $el = $(this);
          var $col = $el.closest(".column");
          
          if ($col.hasClass("core")) {
            var core = $col.data("core");
            $('#core-column-' + core).removeAttr("checked");
            $col.addClass("disabled").find("input.disabled").val("yes");
            
          } else {
            // remove the column
            $col.remove();
          }
          
        };
        
        
        
        $cb.find(".column .remove").click( colRemoveClick );
        
        $cb.find(".core-columns input").change( function() {
          
          var $col = $cb.find(".column." + $(this).val());
          
          if ($(this).is(":checked")) {
            $col.removeClass("disabled").find("input.disabled").val("");
          } else {
            $col.addClass("disabled").find("input.disabled").val("yes");
          }
          
        });
        
        
        // setup add button
        
        if ($("#custom-column-template").length) {
          var column_add_tmpl = Handlebars.compile( $("#custom-column-template").html() );
        }
      
        if ($("#tax-add-button-template").length) {
          var tax_add_button_tmpl = Handlebars.compile( $("#tax-add-button-template").html() ); 
        }
      
        var addCustomColumn = function(title, content) {
          var c = content || "";
          
          var $new_col = $(column_add_tmpl({ index: $cb.find(".column").length }));
          $new_col.appendTo($cb.find('.columns'));
          
          $new_col.find(".remove").click( colRemoveClick );
          $new_col.find(".head input").focus().select();
          
          if (title) {
            $new_col.find(".head input").val(title).focus().select();  
          }
          
          $new_col.find(".body textarea").val("{{" + c + "}}").css("z-index", 200 - $cb.find(".column").length)
          
          return false;
        };
        
        $('#add-custom-column').click( function() { addCustomColumn() } );
        
        $('button.add.taxonomy').live( "click", function() {
          var $new_col = $(column_add_tmpl({ index: $cb.find(".column").length }));
          $new_col.appendTo($cb.find('.columns'));
          
          $new_col.find(".remove").click( colRemoveClick );
          $new_col.find(".head input").val($(this).data("title")).focus().select();
          $new_col.find(".body textarea").val("{{col.terms(" + $(this).data("taxonomy") + ")}}").css("z-index", 200 - $cb.find(".column").length);
        
        });
        
        $('.fs-taxonomies input.checkbox').change( function() {
          
          var $el = $(this);
          
          var tax = $el.val();
          var title = $el.data("title");
          var builtin = $el.data("builtin") == 1;
          
          var $ctrl = $(".taxonomy-control." + tax);
          
          if ($el.is(":checked")) {
            if (!$ctrl.length && !builtin) {
              $('.custom-columns').append( $(tax_add_button_tmpl({ taxonomy: tax, title: title }) ) );
            } 
          } else {
            $ctrl.remove();
          }
          
        });
        
        
        
        var $dialogFields = $('#dialog-fields');
        
        var $fieldSets = $('#add-field-column-field-sets');
        var fsSelect2 = false;
        
        
        $("#add-field-column").click( function() {
          
          var opts = { 
            closeOnEscape: true, 
            width: 500, 
            height: 145, 
            resizable: false, 
            modal: true,
            dialogClass: "wp-dialog aristo dialog-fields", 
            autoOpen: true, 
            title: $dialogFields.data("title"),
            buttons: { 

              "OK" : function() { 

                var expr = $fieldSets.mp_select2("val");
                var title = $fieldSets.mp_select2("label");
                
                addCustomColumn(title, expr);
                $(this).dialog("close");
              },
              
              "Cancel" : function() { 
                $(this).dialog("close");
              }
              
            }
          };

          if (!fsSelect2) {
            $dialogFields.dialog(opts);
            $fieldSets.mp_select2();
            fsSelect2 = true;
          } else {
            $dialogFields.dialog('open');
          }

          $fieldSets.mp_select2('focus');
          
          
        });
        
      
      }
      
      //$('.fs-capability-keys select.capabilities').mp_select2();

      $('.fs-capability-keys .f-capabilities button.suggest').click( function() {
        var $input = $(this).closest(".f-capabilities").find("input.text");
        
        var tmpl = $input.data("suggest");
        
        if (tmpl && tmpl != "") {
          
          var template = Handlebars.compile(tmpl);
          $input.val( template( { name: $('#name').val() } ) );
        }
      });
         
      
      function updateColumnVisibility() {
        
        var $cols = $cb.find(".columns");
        
        var $col_comments = $cb.find(".column.comments");
        var $col_author = $cb.find(".column.author");
        var $col_title = $cb.find(".column.title");
        var $col_categories = $cb.find(".column.categories");
        var $col_tags = $cb.find(".column.tags");
        
				var comments = true;
				var author = true;
				var title = true;
				var categories = true;
				var post_tag = true;

				if ($('.fs-supports').length) {
        	title = $('#supports_title').is(":checked");
      	}
        
       	comments = $('#supports_comments').is(":checked") && $('#core-column-comments').is(":checked");
       	author = $('#supports_author').is(":checked") && $('#core-column-author').is(":checked");
       	categories = $('#taxonomies_category').is(":checked") && $('#core-column-categories').is(":checked");
       	post_tag = $('#taxonomies_post_tag').is(":checked") && $('#core-column-tags').is(":checked");

        if (comments) {
          $col_comments.removeClass("disabled").find("input.disabled").val("");
        } else {
          $col_comments.addClass("disabled").find("input.disabled").val("yes");
        }

        if (author) {
          $col_author.removeClass("disabled").find("input.disabled").val("");
        } else {
          $col_author.addClass("disabled").find("input.disabled").val("yes");
        }

        if (title) {
          $col_title.removeClass("disabled").find("input.disabled").val("");
        } else {
          $col_title.addClass("disabled").find("input.disabled").val("yes");
        }

        if (categories) {
          $col_categories.removeClass("disabled").find("input.disabled").val("");
        } else {
          $col_categories.addClass("disabled").find("input.disabled").val("yes");
        }
        
        if (post_tag) {
          $col_tags.removeClass("disabled").find("input.disabled").val("");
        } else {
          $col_tags.addClass("disabled").find("input.disabled").val("yes");
        }
      }

      $('#supports_comments,#supports_author,#supports_title,#taxonomies_category,#taxonomies_post_tag').change( updateColumnVisibility );
      


      updateColumnVisibility();
      
      $('#taxonomies_category').change( function() {
        $('#fw-core-column-categories').css("display", $(this).is(":checked") ? "block" : "none");
        updateColumnVisibility();
      });

      $('#taxonomies_post_tag').change( function() {
        $('#fw-core-column-tags').css("display", $(this).is(":checked") ? "block" : "none");
        updateColumnVisibility();
      });
      
      $('#supports_author').change( function() {
        $('#fw-core-column-author').css("display", $(this).is(":checked") ? "block" : "none");
      });

      $('#supports_comments').change( function() {
        $('#fw-core-column-comments').css("display", $(this).is(":checked") ? "block" : "none");
      });
      
     
      
      // setp action highlighting
      
      $grids.find("tbody td,tbody th").hover( 
        function(event) {

          var $tr = $(event.target).closest("tr");
          var $td = $(this);
          
          if (!$td.hasClass("actions")) {
            if ($td.hasClass("manage")) {
              $tr.find(".button-manage").addClass("hover");
            } else {
              $tr.find(".button-edit").addClass("hover");
            }
          }
          
          
        },
        
        function(event) {
          
          var $tr = $(event.target).closest("tr");
          $tr.find(".button").removeClass("hover");
          
        }
      );
        
        
        
      
      $('.checkbox-alt-label').checkboxAltLabel();
    
      var sortableListMenuStop = function(event) {
        // update the hidden field order
      
        var subCount = 1;
        var $lpbi = $([]);
        
        $(this).find("li:not(.divide,.holder)").each( function(index, val) {
          
          var $li = $(this);
          
          // find the previous built-in item, and check the base_pos
          
          var $pbi = $li.prevAll("li.bi");
           
          if ($pbi.length) {
            var basePos = $pbi.metadata({ type: 'class' }).base_pos;
            
            
            if (basePos) {
              // set the standard menu position
              $li.find("input.pos").val(basePos);

              if ($lpbi.get(0) == $pbi.get(0)) {
                // increase the sub count
                subCount++;
              } else {
                subCount = 1;
              }
              
              $lpbi = $pbi;
                            
              $li.find("input.sub_pos").val(subCount);

            }
          }
         
        });
        
        $(this).sortableListUpdateArrow();

      };
      

      var sortableListStop = function(event) {
        // update the hidden field order
    
        $(this).find("li").each( function(index, li) {
          var $li = $(this);
          $li.find("input").val(index + 1);
        });
      
        $(this).sortableListUpdateArrow();
      };
    
      $.fn.sortableListUpdateArrow = function() {
        return this.each( function() {
        
          var $el = $(this);
          var $arrow = $el.find(".arrow");
        
          var $current = $el.find("li.current");
        
          if ($current.length) {
            $arrow.css("top", $current.position().top + 7);
          }
        
        });
      };
    
      
			var $slm = $('.sortable-list-menu');
			
			if ($slm.length) {

      $slm
        .sortable({
          items: "li:not(.first)",
          cancel: ".nomove",
          axis: "y",
          revert: 200,
          stop: sortableListMenuStop,
          sort: function() { $(this).sortableListUpdateArrow(); }
        })
        .sortableListUpdateArrow();
				
			}
			
			var $slf = $('.sortable-list-fields');

			if ($slf.length) {

	      $slf
	        .sortable({
	          items: "li",
	          axis: "y",
	          revert: 200,
	          stop: sortableListStop,
	          sort: function() { $(this).sortableListUpdateArrow(); }
	        })
	        .sortableListUpdateArrow();
			
			}
			


    
      // setup the menu icon uploader - this code is now much simpler, since the uploader component has
      // been made a generic plug-in wrapper, to allow usage in field types.
      
      var iconUploaderClear = function() {
        
        $(this).hide();
        var $ul = $(this).closest(".icon-uploader");
        
        $ul.mp_file_uploader('clear');
        $ul.find(".preview").css("backgroundImage", "").addClass("preview-none");
        var $name = $ul.find(".name");
        $name.addClass("name-none");
        var none = $name.data("none");
        
        $ul.find(".clear").hide();
        
        if (!none) {
          none = "( None )";
        }
        
        $name.html(none);
        $('.sortable-list-menu .current .icon').css({ backgroundImage: '' });

      };
      
      $('.icon-uploader')
        .mp_file_uploader({ method: "upload_menu_icon" })
        .find("button.clear").click( iconUploaderClear ).end()
        .bind("submit.mp_file_uploader", function(event, data) {
          $(this).find(".file").addClass("progress");
        })
        .bind("complete.mp_file_uploader", function(event, data) {

          var $up = $(this);

          var $clear = $up.find("button.clear").appendTo("body");
          
          // remove any existing file preview elements (from previous uploads, or the static state of the page)
          $up.find(".file").remove();
          
          $preview = $('<span class="preview"></span>').css({ backgroundImage: 'url(' + data.url + ')' });
          $file = $('<div class="file"><span class="name">' + data.destinationFileName + '</span></div>');
          $file.prepend($preview);
          $clear.show().appendTo($file);
          $up.prepend($file);
        
          // update the sortable menu icon
          $('.sortable-list-menu .current .icon').css({ backgroundImage: 'url(' + data.url + ')' });
          
        });
        
      // setup icon select
      
      $('.icon-select').mp_select2();
      
      $.fn.iconSelectUploader = function() {
        
        var ulid = $(this).data("uploader");
      
        if (ulid && ulid.length) {
        
          try {
        
            var $uploader = $('#' + ulid);
            return $uploader;
        
          } catch (e) {
        
          }

        }
        
        return $();
      };
      
      $('.icon-select').bind("change", function() {

        var base = $(this).data("base");
        var val = $(this).mp_select2("val");
        
        var url = "url(" + base + val + ")";

        var $uploader = $(this).iconSelectUploader();
        
        if (val != "") {
          $('#menu_icon').val('');
          $uploader.hide();
          $uploader.find(".preview").css("backgroundImage", "").addClass("preview-none");
          var $name = $uploader.find(".name");
          $name.addClass("name-none");
          var none = $name.data("none");
          
          $uploader.find(".clear").hide();
          
          if (!none) {
            none = "( None )";
          }
          
          $name.html(none);

        } else {
          
          var pbg = $uploader.find(".preview").css("backgroundImage");
          
          if (pbg) {
            url = pbg;
          } else {
            url = "url(" + $(this).data("no_icon") + ")";
          }
        
          $(this).iconSelectUploader().show();
        }
        
        $('.sortable-list-menu .current .icon').css({ backgroundImage: url });
        
      }).change();
      
      
      
      // fill other strings
      
      $.fn.fill = function() {
      
        return this.each( function() {
          var $el = $(this);
          var md = $el.metadata({ type: 'class' });

          try { 

            var srcVal = md.src;
            
            if (srcVal) {
              srcItems = md.src.split(",");
            }

            var src = $();
            
            if (srcItems.length > 1) {
              $.each( srcItems, function(index, val) {
                $item = $(val);
              
                if ($item.is(":visible") && !src.length) {
                  src = $item;
                }
              });
            } else {
              src = $(srcVal);
              
            }

            var text = $(src).val();
      
            if (text == "") {
              text = "?";
            } else {
          
              if (md.format) {
                if (md.format == 'pluralize') {
                  text = text.pluralize();
                } else if (md.format == 'titleize') {
                  text = text.titleize();
                } else if (md.format == 'dasherize') {
                  text = text.dasherize();
                }

              } 
        
            }
        
            if (md.func == "val") {
              $el.val(text);
            } else {
              $el.html(text);
            }
          } catch (err) {
          
          }
        });
      
      };
  
    
      $.fn.updateLabels = function(singularName, pluralName) {
      
        update = true;

        if (update) {
          
          this.find('input').each( function() {
            var $el = $(this);
            var md = $el.metadata({ type: 'class' });
            var tmpl = md.tmpl;
        
            if (name == "") {
              name = "?";
            }

            
            if (pluralName == "") {
              pluralName = "?";
            }
        
            if (tmpl) {

              var template = Handlebars.compile(tmpl);
              var text = template({ plural_name: pluralName, singular_name: singularName, name: singularName });
              text = text.replace(/[\_\-]/gi, " ");
          
              if (!md.lowercase) {
                text = text.titleize();
              }
          
              $el.val( text );
            }
        
          });
        
        }
        
      
            
      
      };
    
      var nameChange = function() {

        // create the default labels
        
        
        if ($('#name').length ) {

          var pluralVal = '';
          
          if ($('#plural_name').length) {
            pluralVal = $('#plural_name').val();
          }
          
          if ($('#name').val() != $('#name_last').val()) {
            $('#name_last').val($('#name').val());
            $('.fs-labels').updateLabels($('#name').val(), pluralVal);
            $('.fill').fill();
            fillVars();
          }

        }
      
      };

      var namePluralChange = function() {

        // this function is used when "name" is the plural form (fields and field sets)
        
        // create the default labels
      
        if ($('#name').length && $('#singular_name').length) {
          if ($('#name').val() != $('#name_last').val()) {
            $('#name_last').val($('#name').val());
            $('.fs-labels').updateLabels($('#singular_name').val(), $('#name').val());
            $('.fill').fill();
          }
        }
      
      };


      var nameWarning = function() {
        
        // check if converting the plural name to singular results in the same word as the current singular name
        // If not, the singular form might accidentally be a plural
      
        $('#name_warning').hide();

        var ok = true;
        
        if ($('#allow_multiple').length && !$('#allow_multiple').is(":checked")) {
          ok = false;
        }
        
        if (lang == "en" && ok) {
          
          if ($('#name').val() != "" && $('#plural_name').val() != "") {
            var singular = $('#plural_name').val().singularize();
            
            if (singular != $('#name').val()) {
              $('#name_warning').show();
            } 
          }
        
        }
      
      };
    
      var namePluralWarning = function() {
        
        // check if converting the singular name to plural results in the same word as the current plural name
        // If not, the plural form might accidentally be a singular
      
        $('#name_warning').hide();

        var ok = true;
        
        if ($('#allow_multiple').length && !$('#allow_multiple').is(":checked")) {
          ok = false;
        }
        
        if (lang == "en" && ok) {
          
          if ($('#name').val() != "" && $('#singular_name').val() != "") {
            var plural = $('#singular_name').val().pluralize();
            
            if (plural != $('#name').val()) {
              $('#name_warning').show();
            } 
          }
        
        }
      
      };
      
      var namePluralize = function() {
        
        if (lang == "en") {
          // add auto-pluralization for english language
          
          // pluralize the singular name
          var name = $('#name').val();
  
          if (name != "") {
            $('#plural_name').val( name.pluralize() );
          }
        
          nameWarning();
        
        }
      
      };

      var nameSingularize = function() {
        
        if (lang == "en") {
          // add auto-pluralization for english language
          
          // pluralize the singular name
          var name = $('#name').val();
  
          if (name != "") {
            $('#singular_name').val( name.singularize() );
          }
        
          namePluralWarning();
        
        }
      
      };


      // setup select all / select none buttons in field sets
      
      $('.fs .button-select-all').click( function() {
        $(this).closest(".fss,.fs").find("input.checkbox").attr("checked", "checked").change();
        return false;
      });

      $('.fs .button-select-none').click( function() {
        $(this).closest(".fss,.fs").find("input.checkbox").removeAttr("checked").change();
        return false;
      });
  
      $('.fs .button-auto-fill').click( function() {
        $(this).closest(".fs").find("input.checkbox").attr("checked", "checked").change();
        return false;
      });


      // setup select all / select none buttons in lists
      
      $('.list .list-select-all').click( function() {
        $(this).closest(".list").find("input.checkbox").attr("checked", "checked").change();
        return false;
      });

      $('.list .list-select-none').click( function() {
        $(this).closest(".list").find("input.checkbox").removeAttr("checked").change();
        return false;
      });
      
// -- Roles
                     
      if ($('#mpv-roles').length) {
        $('#mpv-roles').find('.fs-tabs').mp_tabs();
        
        $('#name').change( function() {
          $('#display_name').fill();
        });
          
          
      }

// -- Templates 

      if ($('.mpv-templates-edit').length) {
        
        var supportsToggle = function() {
          
          var enabled = $('#supports_type_inherit').is(":checked");

          if (enabled) {
            $('#fs-supports-custom').hide();
          } else {
            $('#fs-supports-custom').show();
          }
          
        };
        
        $('#supports_type_inherit,#supports_type_custom').change( supportsToggle );
        
        supportsToggle();
        
      }
   
// -- Masterplan 
    
      if ($('#mpv-masterplan').length) {

        $('#mpv-masterplan .fs-tabs').mp_tabs();


        function showRelatedLinks(type, name) {
          if (name) {
            $('.' + type + '-link-' + name).show();
          }
        }

        function hideRelatedLinks(type, name) {
          if (name) {
            $('.' + type + '-link-' + name).hide();
          }
        }
        
        $ov = $('.mpv .fs-masterplan');
        
        $ov.find("li.editable.linkify").linkify();
        
        $ov.find('.taxonomies li').hover( 
          function() {
            showRelatedLinks("tax", $(this).data("name"));
          },
          function() {
            hideRelatedLinks("tax", $(this).data("name"));
          }
        );

        $ov.find('.post-types li').hover( 
          function() {
            showRelatedLinks("post-type", $(this).data("name"));
          },
          function() {
            hideRelatedLinks("post-type", $(this).data("name"));
          }
        );

        $ov.find('.roles li').hover( 
          function() {
            showRelatedLinks("role", $(this).data("name"));
          },
          function() {
            hideRelatedLinks("role", $(this).data("name"));
          }
        );
        
        $ov.find('.shared-field-sets li').hover( 
          function() {
            showRelatedLinks("shared-field-set", $(this).data("name"));
          },
          function() {
            hideRelatedLinks("shared-field-set", $(this).data("name"));
          }
        );
        
        // export - make the title checkboxes select all checkboxes in the panel content
        
        $('.fsit input.checkbox').click( function() {
          
          var $c = $(this);
          var checked = $c.is(":checked");
          
          var $fsi = $c.closest(".fsi");
          
          var $all = $fsi.find(".fsic input.checkbox");
          
          if (checked) {
            $all.attr("checked", "checked");
          } else {
            $all.removeAttr("checked");
          }
          
        });
        
        $('#export_filename').autoGrowInput();
        
          
        var readme_cm = false;
        var backup_readme_cm = false;

        $('.fs-masterplan .fs-tabs').bind("afterchange.mp_tabs", function(event, data) {
          
          if (data.panel.length && data.panel.attr("id") == "masterplan-export") {
            setTimeout( function() { 
              $('#export_filename').autoGrowInput('update'); 
              
              if (!readme_cm) {
                readme_cm = CodeMirror.fromTextArea($('#export_readme').get(0), { theme: "sunburst", lineWrapping: true, mode: "markdown", lineNumbers: true });
              }
              
            }, 100 );
          }
          
          if (data.panel.length && data.panel.attr("id") == "masterplan-backup") {
            setTimeout( function() { 
              $('#backup_filename').autoGrowInput('update'); 
              
              if (!backup_readme_cm) {
                backup_readme_cm = CodeMirror.fromTextArea($('#backup_readme').get(0), { theme: "sunburst", lineWrapping: true, mode: "markdown", lineNumbers: true });
              }
              
            }, 100 );
          }
          
          
          
        });
        
        
        // make the list checkboxes check everything underneath
        
        $('#export-select-all').click( function() {
          $('.fsic input.checkbox').attr("checked", "checked");
          $('.fsit input.checkbox').attr("checked", "checked");
        });

        $('#export-select-none').click( function() {
          $('.fsic input.checkbox').removeAttr("checked");
          $('.fsit input.checkbox').removeAttr("checked");
        });
        
        $('.fsic input.checkbox').click( function() {
          
          var $c = $(this);
          var checked = $c.is(":checked");
          
          var $fsi = $c.closest("li");
          
          var $all = $fsi.find("ul input.checkbox");
          
          if (checked) {
            $all.attr("checked", "checked");
          } else {
            $all.removeAttr("checked");
          }
          
          
          // also make a checkbox above auto-check, if anything below is checked
          
          if (checked) {
            var $uli = $c.closest("ul").closest("li");
            $uli.find(">input.checkbox").attr("checked", "checked");
            
            // and one more level
            var $uuli = $uli.closest("ul").closest("li");
            $uuli.find(">input.checkbox").attr("checked", "checked");

          }
          
          
        });
        


        
        $('#masterplan-export form').bind("submit", function(event) {
          
          event.preventDefault();

          var data = $(this).serializeDeep();
          
          $('#export-ui').hide();
          $('#export-progress').show();
          
          $.mp.postToAction("masterplan.export", data, function(response) {

            $('#export-progress').hide();
            
            if (response.error) {
              alert(response.error);
              $('#export-ui').show();
            } else {
              
              var $dl = $('#export-file-download');
              var msg = '<i class="zip"></i>' + sprintf($dl.data("message"), response.filename);
              $dl.attr("href", response.url).html(msg);
              
              if (response['package'].icon_list.length || response['package'].type_list.length) {
                
                if (response['package'].icon_list.length) {
                  
                  $.each( response['package'].icon_list, function(index, icon) {
                    $('#extras-icons ul').append($('<li style="background-image: url(' + icon.url + ')">' + icon.name + '</li>'));
                  });
                  
                  $('#extras-icons').show();
                }
                

                if (response['package'].type_list.length) {
                  $.each( response['package'].type_list, function(index, type) {
                    $('#extras-types ul').append($('<li style="background-image: url(' + type.icon + ')">' + type.key + '</li>'));
                  });

                  $('#extras-types').show();
                } else {
                  $('#extras-types').hide();
                  
                }
                
                $('#extras-summary').show();
                
              }
              
              $('#export-summary').show();
              
              
            }
            
          });
          
          
          return false;
          
        });
        
        
        $('#masterplan-backup form').bind("submit", function(event) {
          
          event.preventDefault();

          var data = $(this).serializeDeep();
          var exp = $('#masterplan-export form').serializeDeep();
          data.ref = exp.ref;
          
          $('#backup-ui').hide();
          $('#backup-progress').show();
          
          $.mp.postToAction("masterplan.backup", data, function(response) {

            $('#backup-progress').hide();
            
            if (response.error) {
              alert(response.error);
              $('#backup-ui').show();
            } else {

              if (response['package'].icon_list.length || response['package'].type_list.length) {
                
                if (response['package'].icon_list.length) {
                  
                  $.each( response['package'].icon_list, function(index, icon) {
                    $('#backup-extras-icons ul').append($('<li style="background-image: url(' + icon.url + ')">' + icon.name + '</li>'));
                  });
                  
                  $('#backup-extras-icons').show();
                } else {
                  $('#backup-extras-icons').hide();
                }
                
                

                if (response['package'].type_list.length) {
                  $.each( response['package'].type_list, function(index, type) {
                    $('#backup-extras-types ul').append($('<li style="background-image: url(' + type.icon + ')">' + type.key + '</li>'));
                  });

                  $('#backup-extras-types').show();
                } else {
                  $('#backup-extras-types').hide();
                  
                }
                
                $('#backup-extras-summary').show();
                
              }
              
              $('#backup-summary').show();
              
              
            }
            
          });
          
          
          return false;
          
        });
        
        
        // setup the menu icon uploader - this code is now much simpler, since the uploader component has
        // been made a generic plug-in wrapper, to allow usage in field types.

        $('#masterplan-import').find(".file-uploader")
          .mp_file_uploader({ method: "import_masterplan" })
          .bind("complete.mp_file_uploader", function(event, data) {
            var $up = $(this);
            $up.find(".name").removeClass("name-none").html(data.destinationFileName);

            $('#import-summary').hide();

            $('#label-import_file').hide();
            
            var $file_name = $('#masterplan-import').find(".file .name");

            $file_name.removeClass("error");

            // now we'd display a summary and import button
            
            $('#import-fetching-summary').show();
            
            var fetch_data = { filename: data.destinationFileName };
            
            $.mp.action("masterplan.fetch_masterplan_summary", fetch_data, function(response) {

              $('#import-fetching-summary').hide();
              
              if (response.success) {
              
                $('#import-masterplan').val(data.destinationFileName);
                
                var $rep = $('#import-rep');
                
                var $post_types = $rep.find(".fsi.post-types").hide();
                var $taxonomies = $rep.find(".fsi.taxonomies").hide();
                var $shared_field_sets = $rep.find(".fsi.shared-field-sets").hide();
                var $site_field_sets = $rep.find(".fsi.site-field-sets").hide();
                var $templates = $rep.find(".fsi.templates").hide();
                var $roles = $rep.find(".fsi.roles").hide();
                
                // build the representation view
                
                var base = response.base_url;
                
                var rep = response.rep;
                
                var get_field_sets = function( items ) {
                  
                  var $fsul = $();
                  
                  if (items && items.length) {
                    $fsul = $('<ul>').addClass("field-sets");
                      
                    $.each( items, function(j, field_set) {
                      
                      var $fsli = $('<li>').addClass("field-set");
                      var $fslabel = $('<span>').html(field_set.labels.name || field_set.name).addClass("label field-set").appendTo($fsli);
                      
                      var $i = $('<i></i>');
                      
                      if (field_set.allow_multiple == "1") {
                        $i.addClass("metabox-add-remove-large");
                      } else {
                        $i.addClass("metabox-large");
                      }
                      
                      $fslabel.prepend($i);
                      
                      $fsul.append($fsli);
                      
                      if (field_set.fields && field_set.fields.length) {
                        
                        var $ful = $('<ul>').addClass("fields");
                        
                        $.each(field_set.fields, function(k, field) {
                          
                          if (!field.missing) {
                            var $fli = $('<li>').addClass("field");
                            var $flabel = $('<span>').html(field.labels.name || field.name).addClass("label field").appendTo($fli);
                            $flabel.css("background-image", "url(" + field.icon_url + ")" );
                      
                            $ful.append($fli);
                          }
                        
                        });
                        
                        $fsli.append($ful);
                        
                      }
                      
                    });
                  
                  }
                  
                  return $fsul;
                  
                }; 
                  
                
                if (rep.post_types && rep.post_types.length) {
                  $post_types.show();
                  
                  var $tree = $post_types.find(".object-tree").empty();
                  
                  $.each(rep.post_types, function(index, post_type) {
                    
                    var $li = $('<li></li>');
                    $label = $('<span>').html(post_type.labels.name || post_type.name).addClass("label").appendTo($li);
                    $label.css("background-image", "url(" + post_type.icon_url + ")" );
                    $tree.append($li);
                    
                    $li.append(get_field_sets(post_type.field_sets));

                  });

                }

                if (rep.taxonomies && rep.taxonomies.length) {
                  $taxonomies.show();

                  var $tree = $taxonomies.find(".object-tree").empty();

                  $.each(rep.taxonomies, function(index, tax) {
                    
                    var $li = $('<li></li>');
                    $label = $('<span>').html(tax.labels.name || tax.name).addClass("label").appendTo($li);
                    $label.css("background-image", "url(" + tax.icon_url + ")" );
                    $tree.append($li);
                    
                    $li.append(get_field_sets(tax.field_sets));

                  });
                  
                  
                }

                if (rep.shared_field_sets && rep.shared_field_sets.length) {
                  $shared_field_sets.show();

                  var $tree = $shared_field_sets.find(".object-tree").empty();
                  $field_sets = get_field_sets(rep.shared_field_sets);
                  $tree.append( $field_sets.find(">li") ); 
                }

                if (rep.site_field_sets && rep.site_field_sets.length) {
                  $site_field_sets.show();

                  var $tree = $site_field_sets.find(".object-tree").empty();
                  $field_sets = get_field_sets(rep.site_field_sets);
                  $tree.append( $field_sets.find(">li") ); 
                }

                if (rep.templates && rep.templates.length) {
                  $templates.show();
                  var $tree = $templates.find(".object-tree").empty();

                  $.each(rep.templates, function(index, template) {
                    
                    var $li = $('<li></li>');
                    $label = $('<label class="template"></label>').html('<i class="template"></i>' + template.id);
                    $li.append($label);
                    $tree.append($li);
                    
                    $li.append(get_field_sets(template.field_sets));

                  });
                  
                }


                if (rep.roles && rep.roles.length) {
                  $roles.show();

                  var $tree = $roles.find(".object-tree").empty();

                  $.each(rep.roles, function(index, role) {
                    
                    var $li = $('<li></li>');

                    $label = $('<label class="role"></label>').html('<i class="user-role"></i>' + role.name);
                    $li.append($label);
                    $tree.append($li);
                    
                    $li.append(get_field_sets(role.field_sets));

                  });
                  
                }
                
                if (response.field_types && response.field_types.length) {
                  $('#f-types-overwrite').show();
                }
                
                $('#import-confirmation').show();
                $('#import-preview').show();

                
                
              } else {
                $('#label-import_file').show();
                alert(response.error);
                $file_name.addClass("error");
              }
              
            });
            
          });
          
        $('#masterplan-restore form').submit( function() {
          
          if ($('#restore-masterplan').val() == '') {
            alert($('#restore-masterplan').data("empty"));
            return false;
          }
          
          return true;
          
        });
        
        
        
      } // end masterplan  
      

      // setup input masks
      $("input.key").inputmask();

// -- Create Field Set 
    
      if ($('.mpv-field-sets-form').length) {

        $('#plural_name').change( namePluralChange );

        $('#name').change( function() {
          nameSingularize();
          namePluralChange();
        }); 
      
				if ($('#name').length) {
        	namePluralWarning();
				}
				
      }


// -- Create Field 
    
      if ($('.mpv-fields-form:not(.mpv-fields-delete)').length) {

        $('#name').change( function() {
          nameChange();
        }); 

      }

    
// -- Create Post Type 


      if ($('.mpv-post-types-form,.mpv-taxonomies-form').length) {
        
        
        $('#capability_type_custom_value').change( function() {
          $('#fw_capability_type_custom').find(".custom-fill").fill();
        });
        
        if ($('#capability_type_custom').is(":checked")) {
          $('#fw_capability_type_custom').find(".custom-fill").fill();
        }
        
        
        var fillVars = function() {
          // fill the rewrite slug
          $('#rewrite_slug').val($('#plural_name').val().dasherize());

          // fill the query var
          $('#query_var').val($('#name').val().dasherize());
        };
        
        $('#plural_name').change( function() {
          nameChange();
        }); 
        
        $('#name').change( function() {
          namePluralize();
          nameChange();
        });


      } 
      
      
      $('#autofill-labels').click( function() {
        if ($('#plural_name').length) {  // the name field is assumed singular
          $('.fs-labels').updateLabels($('#name').val(), $('#plural_name').val());
        } else { // the name field is assumed plural
          $('.fs-labels').updateLabels($('#singular_name').val(), $('#name').val());
        }
        $('.fill').fill();

        return false;
      });

// -- Edit Post Type

      if ($('.mpv-post-types-edit').length) {
        $('span.fill').fill();
        
        $('#plural_name_suggest').click( function() {
          namePluralize();
        });
        
      } 

      if ($('.mpv-post-types-form').length) {
				
				var $capfw = $('.fs-capability-keys .fw');
				
				var updateKeysVis = function() {
					
					$capfw.each( function() {
						
						var $eg = $(this).find(".eg")
						
						$eg.hide();
						
						if ($(this).find("input.radio").is(":checked")) {
							$eg.show();
						}
					});
					
				};
				
				$capfw.find("input.radio").click( updateKeysVis );
				updateKeysVis();

			}
			
			
			
      if ($('.mpv-post-types-delete').length) {
        
        var updatePanelState = function() {
          if ($('#posts_reassign').is(":checked") || $('#posts_keep').is(":checked") || $('#posts_keep').length == 0) {
            $('#label_field_sets_keep').removeClass("disabled");
            $('#label_field_data_keep').removeClass("disabled");

            $('#field_sets_keep').removeAttr("disabled").attr("checked", "checked");
            $('#field_data_keep').removeAttr("disabled").attr("checked", "checked");

            $('#label_field_sets_delete').removeClass("disabled");
            $('#label_field_data_delete').removeClass("disabled");

            $('#field_data_delete').removeAttr("disabled");
            $('#field_sets_delete').removeAttr("disabled");
            
          }
          
          if ($('#posts_keep').length == 0) {
            $('#field_sets_delete').attr("checked", "checked");
          }
          
          if ($('#posts_trash').is(":checked")) {

            $('#label_field_sets_delete').addClass("disabled");
            $('#label_field_data_delete').addClass("disabled");

            $('#label_field_sets_keep').removeClass("disabled");
            $('#label_field_data_keep').removeClass("disabled");

            $('#field_data_delete').attr("disabled", "disabled");
            $('#field_sets_delete').attr("disabled", "disabled");

            $('#field_data_keep').removeAttr("disabled").attr("checked", "checked");
            $('#field_sets_keep').removeAttr("disabled").attr("checked", "checked");

          }
          
          if ($('#posts_delete').is(":checked")) {

            $('#label_field_sets_keep').addClass("disabled");
            $('#label_field_data_keep').addClass("disabled");

            $('#label_field_sets_delete').removeClass("disabled");
            $('#label_field_data_delete').removeClass("disabled");

            $('#field_data_delete').removeAttr("disabled").attr("checked", "checked");
            $('#field_sets_delete').removeAttr("disabled").attr("checked", "checked");

            $('#field_data_keep').attr("disabled", "disabled");
            $('#field_sets_keep').attr("disabled", "disabled");

          }
          
        };
        
        $('#posts_reassign,#posts_keep,#posts_reassign_type,#posts_trash,#posts_delete').click( function() {
          updatePanelState();
        });

        updatePanelState();

        
      }

      
      
      // Visibility options (shared among post types, taxonomies, field sets forms)
      
      if ($('.fs-visibility').length) {
      
        if ($('.fsg-multisite').length) {
          
          var visSitesToggle = function() {

            $('#visibility-sites-list').hide();

            var $sites = $('#visibility-sites');

            if ($('#visibility-type-sites-allow').is(":checked") || $('#visibility-type-sites-deny').is(":checked")) {
            
              $('#visibility-sites-list').show();
              
              if (!$sites.data("has-select2")) {
                $sites.mp_select2({ layout: 'block' });
                $sites = $('#visibility-sites');
                $sites.data("has-select2", true);
              }

            }

          };
        
          $('#visibility-type-sites-allow,#visibility-type-sites-deny,#visibility-type-sites-all').click( visSitesToggle );
          visSitesToggle();
          
        }
        
        
        if ($('.fsg-templates').length) {
          
          var visTemplatesToggle = function() {

            $('#visibility-templates-list').hide();

            if ($('#visibility-type-templates-allow').is(":checked") || $('#visibility-type-templates-deny').is(":checked")) {
              $('#visibility-templates-list').show();
            }

          };
        
          $('#visibility-type-templates-allow,#visibility-type-templates-deny,#visibility-type-templates-all,#visibility-type-templates-none').click( visTemplatesToggle );
          visTemplatesToggle();
        
          // if ( ( $('#visibility-type-post-types-allow').is(":checked") && $('#visibility-post-types-page').is(":checked") ) || ( $('#visibility-type-post-types-deny').is(":checked") && !$('#visibility-post-types-page').is(":checked") ) || $('#visibility-type-post-types-all').is(":checked")) {
          
          
          var postTypesPageToggle = function() {
            if ( ( $('#visibility-type-post-types-allow').is(":checked") ) || ( $('#visibility-type-post-types-deny').is(":checked") ) || $('#visibility-type-post-types-all').is(":checked")) {
              $('.fsg-templates').show();
            } else {
              //$('.fsg-templates').hide();
            }
          
          };
        
          $('#visibility-post-types-page,#visibility-type-post-types-all,#visibility-type-post-types-allow,#visibility-type-post-types-deny').click( postTypesPageToggle );
        
          postTypesPageToggle();
        
          
        }
        
        
				if ($('.fsg-fields').length) {
          
					$vis_fields = $('#visibility-fields');
					
        	$vis_fields.mp_select2();
					
					
					var visFieldsToggle = function() {


            $('#visibility-fields-options').hide();

            if ($('#visibility-type-fields-allow').is(":checked") || $('#visibility-type-fields-deny').is(":checked")) {
              $('#visibility-fields-options').show();
            }

          };

          $('#visibility-type-fields-allow,#visibility-type-fields-deny,#visibility-type-fields-none').click( visFieldsToggle );
          visFieldsToggle();
					
          
        }

        if ($('.fsg-roles').length) {
          
          var visRolesToggle = function() {

            $('#visibility-roles-list').hide();

            if ($('#visibility-type-roles-allow').is(":checked") || $('#visibility-type-roles-deny').is(":checked")) {
              $('#visibility-roles-list').show();
            }

          };

          $('#visibility-type-roles-allow,#visibility-type-roles-deny,#visibility-type-roles-all,#visibility-type-roles-none').click( visRolesToggle );
          visRolesToggle();

          
        }
        
        if ($('.fsg-taxonomies').length) {
          
          var visTaxonomiesToggle = function() {

            $('#visibility-taxonomies-list').hide();

            if ($('#visibility-type-taxonomies-allow').is(":checked") || $('#visibility-type-taxonomies-deny').is(":checked")) {
              $('#visibility-taxonomies-list').show();
            }

          };

          $('#visibility-type-taxonomies-allow,#visibility-type-taxonomies-deny,#visibility-type-taxonomies-all,#visibility-type-taxonomies-none').click( visTaxonomiesToggle );
          visTaxonomiesToggle();
          
        }
        
        if ($('.fsg-post-types').length) {
          
          var visPostTypesToggle = function() {

            $('#visibility-post-types-list').hide();

            if ($('#visibility-type-post-types-allow').is(":checked") || $('#visibility-type-post-types-deny').is(":checked")) {
              $('#visibility-post-types-list').show();
            }

          };
        
          $('#visibility-type-post-types-allow,#visibility-type-post-types-deny,#visibility-type-post-types-all').click( visPostTypesToggle );
          visPostTypesToggle();
          
        }
        
        
        
      }
      

// -- Create / Edit Taxonomy and Create / Edit Post Type

      if ($('.mpv-taxonomies-form,.mpv-post-types-form').length) {
        
        var $hierarchical = $('#hierarchical');
        
        if ($hierarchical.length) {
          
          $hels = $('.hierarchical-only');
          
          var hierarchicalToggle = function() {
            if ($hierarchical.is(":checked")) {
              $hels.show();
            } else {
              $hels.hide();
            }
          };
          
          hierarchicalToggle();

          $hierarchical.change(hierarchicalToggle);

        }
        
      }
      


// -- Create Taxonomy 


      if ($('.mpv-taxonomies-create').length) {
        
        var fillVars = function() {
          // fill the rewrite slug
          $('#rewrite_slug').val($('#name').val().dasherize());

          // fill the query var
          $('#query_var').val($('#name').val().dasherize());
        };
        
        $('#plural_name').change( function() {
          nameChange();
          fillVars();
        }); 
        
        $('#name').change( function() {
          namePluralize();
          nameChange();
          fillVars();
        });

        namePluralize();
        nameChange();
        fillVars();


      } 


      if ($('.mpv-taxonomies-delete').length) {
        
        var updatePanelState = function() {
          if ($('#existing_terms_reassign').is(":checked") || $('#existing_terms_keep').is(":checked") || $('#existing_terms_keep').length == 0) {
            $('#label_field_sets_keep').removeClass("disabled");
            $('#label_field_data_keep').removeClass("disabled");

            $('#field_sets_keep').removeAttr("disabled").attr("checked", "checked");
            $('#field_data_keep').removeAttr("disabled").attr("checked", "checked");

            $('#label_field_sets_delete').removeClass("disabled");
            $('#label_field_data_delete').removeClass("disabled");

            $('#field_data_delete').removeAttr("disabled");
            $('#field_sets_delete').removeAttr("disabled");
            
          }
          
          if ($('#existing_terms_keep').length == 0) {
            $('#field_sets_delete').attr("checked", "checked");
          }

          
          if ($('#existing_terms_delete').is(":checked")) {

            $('#label_field_sets_keep').addClass("disabled");
            $('#label_field_data_keep').addClass("disabled");

            $('#label_field_sets_delete').removeClass("disabled");
            $('#label_field_data_delete').removeClass("disabled");

            $('#field_data_delete').removeAttr("disabled").attr("checked", "checked");
            $('#field_sets_delete').removeAttr("disabled").attr("checked", "checked");

            $('#field_data_keep').attr("disabled", "disabled");
            $('#field_sets_keep').attr("disabled", "disabled");

          }
          
        };
        
        $('#existing_terms_reassign,#existing_terms_keep,#existing_terms_reassign_taxonomy,#existing_terms_delete').click( function() {
          updatePanelState();
        });

        updatePanelState();

        
      }
      

      $('select.check').bind("click change", function() {
        
        try {
          $($(this).metadata({ type: 'class' }).el).attr("checked", true);
        } catch (err) {
          
        }
        
      });
      

// -- Create / Edit Field

      if ($('.mpv-fields-form').length) {
        
        var $fto = $('.fs-field-type-options');
        var $ftoc = $('#field-type-options-content');
        var $ftl = $('#field-type-loading');
        
        var $type = $('#type');
        
        $type.mp_select2();
        
        var fieldTypeChange = function(event, data) {
          
          var $el = $type;
          
          var val = $el.mp_select2("val");
          
          var selection = $el.mp_select2("selection");
          
          if (selection) {
            
            if (selection.icon) {
              $('#f-position').find("li.current .icon").attr("class", "icon " + selection.icon);
            }
            
            if (selection.description) {
              $('#field-type-description').html( selection.description );
            }
          
          }
        
          
          // fire off an ajax request to render the correct field options UI
        
          var oid = "mpft-" + val + "-options";
            
          var $oel = $('#' + oid);

          // move any existing options forms to the body (so they don't submit data)
          $('.mpft-options').hide().appendTo("body");
          
          if (!$oel.length) {

            // show the loading spinner after 100ms
            clearTimeout( $ftl.data("lto") );
            $ftl.data("lto", setTimeout( function() { $ftl.show(); $ftoc.hide() }, 100 ));
        
            // fire off an ajax request to get the options form  
          
            $.mp.action('field.options', { type: val }, function(data) {
              clearTimeout( $ftl.data("lto") );

              var loaded = true;
              
              $ftl.hide(); 
              
              if (data.css_file) {
                var cid = "mpft-" + data.type + "-options-css";
              
                var $link = $('#' + cid);
              
                if (!$link.length) {
                  $link = $("<link />").attr({ "id" : cid, "type" : "text/css", "href" : data.css_file, "rel" : "stylesheet" });
                  $("html head").append($link);
                }
                
              }
              
              $oel = $('<div />').attr({ "class" : "mpft-options", "id" : oid }).append(data.form);

              if (data.form != '') {
                $oel.data("hasOptions", true); 
              }

              $oel.show().appendTo($ftoc);
              
              if (data.js_file) {
                  
                loaded = false;
                
                $.getScript( data.js_file, function() {
                  
                  if ($oel.html() != '') {
                    $fto.show();
                  } else {
                    $fto.hide();
                  }

                });
                
              }
              
              $ftoc.show();
            
              if (loaded) {

                if ($oel.html() != '') {
                  $fto.show();
                } else {
                  $fto.hide();
                }
            
              }
            
            
              
            });
          
          } 
          else {

            $oel.show().appendTo($ftoc);
            $ftoc.show();

            if ($.trim($oel.html()) != '') {
              $fto.show();
            } else {
              $fto.hide();
            }


          }  

          
          
          
        };
        
        $type.bind("change.select2", fieldTypeChange );
        
        fieldTypeChange();

        
      }

// -- Create / Edit Post Type

      if ($('.mpv-post-types-form').length) {
        
  

        var disabledToggle = function() {
          var $current = $('.fs-menu-options .sortable-list li.current');
          
          if ($('#disabled').is(":checked")) {
            $current.addClass("disabled");
          } else {
            $current.removeClass("disabled");
          }
        };
        
        var menuOptionsToggle = function() {
          var $fs = $(".fs-menu-options");
          
          if ($('#show_in_menu').is(":checked") || !$('#show_in_menu').length) {
            $fs.show();
          } else {
            $fs.hide();
          }
        };
        
        $('#show_in_menu').change( menuOptionsToggle );
        $('#disabled').change( disabledToggle );
        
        disabledToggle();
        menuOptionsToggle();
        
      } 

// -- Create Field Set

      
      if ($('.mpv-field-sets-form').length) {

        var disabledToggle = function() {
          
          var $current = $('#f-position .sortable-list li.current');
          
          if ($('#disabled').is(":checked")) {
            $current.addClass("disabled");
          } else {
            $current.removeClass("disabled");
          }
        };

        $('#disabled').change( disabledToggle );


        var expandedManuallyChecked = false;
 
        if ($('.mpv-edit-field-set').length) {
          expandedManuallyChecked = true;
        }
  

        var allowMultipleToggle = function() {
          if ($('#allow_multiple').is(":checked")) {

            if (!expandedManuallyChecked) {
              $('#expanded').removeAttr("checked");
            }
            
            nameSingularize();
            namePluralChange();
            
            $('#f-singular_name').show();
            $('.f-allow-multiple').show();
          } else {

            if (!expandedManuallyChecked) {
              $('#expanded').attr("checked", "checked");
            }
          
            $('#f-singular_name').hide();
            $('#name_warning').hide();
            $('.f-allow-multiple').hide();
          }
          
        };
        
        $('#expanded').click( function() {
          expandedManuallyChecked = true;
        }); 

        $('#allow_multiple').change(
          function() {
            allowMultipleToggle();
            $('.sortable-list .fill').fill();
            namePluralWarning();
          }
        );
        
        allowMultipleToggle();
      }

    
    
    
      // -- Settings


			$('#mpv-settings').each( function() {
				
				var updateKeysVis = function() {
					
					if ($('#mp_cap_specific').is(":checked")) {
						$('#specific-keys').show();
						$('#mp-grant').show();
					} else {
						$('#specific-keys').hide();
						$('#mp-grant').hide();
					}
					
				};
				
				$('#mp_cap_specific,#mp_cap_standard').click( updateKeysVis );
				updateKeysVis();
				
				//var cm = CodeMirror.fromTextArea($('#server-info').get(0), { theme: "sunburst", readOnly: true, lineWrapping: true, mode: "gfm", lineNumbers: false });
				
				$('#select-all-server-info').click( function() {
				  $('#server-info').select();
				  //cm.setSelection({ line: 0, ch: 0  }, { line: cm.lineCount(), ch: 999 });
			  });
			  
			
			
				// Licence Key Validation
				
				var $progress = $('#licence-progress');
				var $key = $('#licence_key');
				var $valid = $('#licence-valid');
				var $invalid = $('#licence-invalid');
				var $empty = $('#licence-empty');
				
				function validate_key() {
						
					// trim the licence key
					
					$key.val( $.trim($key.val()) );
					
					$progress.show();
					
					$.mp.action("settings.validate_licence_key", { key: $key.val() }, function(response) {

						$progress.hide();

            if (response && response.success) {
            
              // now create a set and set the data
             
							if (response.valid) {
								$invalid.hide();
								$empty.hide();
								$valid.show();
							} else {
								$empty.hide();
								$valid.hide();
								$invalid.find(".reason").html(" - " + response.reason).end().show();
							} 

           	}

					});
					
					
				}
				
				$('#licence_key').change(validate_key);
					
			
				
			});

    
    
    
      
      
      
      if ($('.mpv-fields-form').length) {
        
        var disabledToggle = function() {
          
          var $current = $('#f-position .sortable-list li.current');
          
          if ($('#disabled').is(":checked")) {
            $current.addClass("disabled");
          } else {
            $current.removeClass("disabled");
          }
        };

        $('#disabled').change( disabledToggle );
        
        disabledToggle();
        
        $('#position').hide();
        
        
      }
      
      
      if ($('.mpv-post-types-manage-field-sets,.mpv-shared-field-sets-manage,.mpv-templates-manage-field-sets').length) {
        // focus the create field button on the updated set/field
        $grids.find("tr.updated").nextAll('tr.summary').eq(0).find("a.create-field").focus();
      }
      
      
    } // mpvAdmin.length
    

		


  }); // document.ready
  
  $(window).load( function() {
    
    var $gridRows = $('table.grid tbody tr');
    
    var $update = $gridRows.find(".update-text");
    
		if ($update.length) {
    	$update.effect("pulsate", { times: 2 } );
    }
                        
    var $grids = $('table.grid');

    $updated = $grids.find("tr.updated");
      
    if ($updated.length) {
      $gap = $updated.prev("tr.gap");
      
      if ($gap.length) {
        var pos = $gap.position().top - 10; // allow for the admin bar
        
        $.scrollTo(pos);
      }
    
    }


  });
    
})(jQuery);