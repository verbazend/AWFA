
(function($) {

  // the type variable below must be set to the underscored version of the field type key
  // e.g. if you have a field type with key 'my-editor', the type variable below would be "my_editor"
  
  
  var field_type = "visual_editor";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    focus: function() {
      var self = this;
      
      if (self.ui.button_visual.hasClass("current")) {
        tinyMCE.execCommand('mceFocus', false, this.mceId);
      } else {
        if (this.using_cm()) {
          self.codeMirror.focus();
        } else {
          self.ui.textarea.focus();
        }
      }
      
      self.focus_field();
    },
    
    cache_ui: function() {
      this.ui = this.ui || {
        button_visual: this.element.find(".tab-button.visual"),
        button_html: this.element.find(".tab-button.html"),
        textarea: this.element.find("textarea")
      };      
    },
    
    ontoggleexpand: function() {
      var self = this;
      if (self.ui.button_visual.hasClass("current")) {
        self.mce_add();
      }
    },
    
    is_readonly: function() {
      return (this.ui.textarea.attr("readonly") == "readonly"); 
    },
    
  	onfirstexpand: function() {
      
      var self = this;
      
      this.cache_ui();
      
      var mceInit = {};
      
      if (tinyMCEPreInit) {
        mceInit = tinyMCEPreInit.mceInit || {};
      }
      
      // Wordpress 3.3 patch (this will need some more thought when I can more fully understand the API changes)
      
      if (mceInit.content) {
        mceInit = mceInit.content;
      }

      if (window.wpActiveEditor) {
        // fix for new behaviour in WP 3.3
        this.options.set_item.mousedown( function() {
          wpActiveEditor = self.ui.textarea.attr("id");
        });
      }
      
      this.mce = true;
      this.mceId = this.ui.textarea.attr("id");

      this.mceOptions = jQuery.extend(true, {}, mceInit, { 
        setup : function(ed) {
          ed.onClick.add( function(ed, l) {
            self.focus_field();
          });

          ed.onKeyPress.add( function(ed, l) {
            self.focus_field();
            self.set_change();
          });
        }
      });
      
      this.mceOptions.mode = "exact";
      this.mceOptions.elements = this.mceId;


      this.ui.button_html.click(
        function() {
          if (!self.ui.button_html.hasClass("current")) {
            self.ui.textarea.wrap($('<div class="textarea-wrap"></div>'));
            self.ui.button_visual.removeClass("current");
            self.ui.button_html.addClass("current");
            
            self.mce_remove();
            self.pretty();
            if (self.using_cm()) {
              self.cm_add();
            }
          }

          self.focus_field();
        }
      );

      this.ui.button_visual.click(
        function() {
          if (!self.ui.button_visual.hasClass("current")) {
            self.ui.textarea.unwrap();
            self.ui.button_html.removeClass("current");
            self.ui.button_visual.addClass("current");

            if (self.using_cm()) {
              self.cm_remove();
            }
          
            self.mce_add(true);
            
          }
          
          self.focus_field();
        }
      );
      
      
      // add the media buttons
    
      
      
      this.mceOptions.theme_advanced_buttons1 = "mp_media,|," + this.mceOptions.theme_advanced_buttons1;
      
    
      if (this.ui_options.mce_blockformats) {
        this.mceOptions.theme_advanced_blockformats = this.ui_options.mce_blockformats;
      }

      if (this.ui_options.mce_styles) {
        this.mceOptions.theme_advanced_styles = this.ui_options.mce_styles.split(/\n|;/g).join(";");
        
        var addStyleSelect = true;
        
        if (this.mceOptions.theme_advanced_buttons1 && this.mceOptions.theme_advanced_buttons1.match(/styleselect/)) {
          addStyleSelect = false;
        }

        if (this.mceOptions.theme_advanced_buttons2 && this.mceOptions.theme_advanced_buttons2.match(/styleselect/)) {
          addStyleSelect = false;
        }

        if (this.mceOptions.theme_advanced_buttons3 && this.mceOptions.theme_advanced_buttons3.match(/styleselect/)) {
          addStyleSelect = false;
        }

        if (addStyleSelect) {
          if (this.mceOptions.theme_advanced_buttons2) {
            this.mceOptions.theme_advanced_buttons2 = this.mceOptions.theme_advanced_buttons2.replace(/formatselect/, "formatselect,styleselect");
          } else if (this.mceOptions.theme_advanced_buttons) {
            this.mceOptions.theme_advanced_buttons = this.mceOptions.theme_advanced_buttons.replace(/formatselect/, "formatselect,styleselect");
          }
        
        }
      
      }

      // check for readonly
      this.mceOptions.readonly = this.is_readonly();
      
      // remove the fullscreen plug-in, not sure how to support this yet....
      
      if (this.mceOptions.theme_advanced_buttons1) {
        this.mceOptions.theme_advanced_buttons1 = this.mceOptions.theme_advanced_buttons1.replace(/,fullscreen/, '');
        this.mceOptions.theme_advanced_buttons1 = this.mceOptions.theme_advanced_buttons1.replace(/,wp_fullscreen/, '');
      }
    
      var h = parseInt(this.ui_options.height);
      
      if (h && h > 60) {
        this.mceOptions.height = h;
      }
    
      // this.mceOptions.theme_advanced_resizing = true;
      
      this.mceOptions.init_instance_callback = function(inst) {
        
        // remove the wpautop stuff (hope this is maintainable, since I can't find ANY OTHER WAY!!)
        inst.onSaveContent.listeners = [];

        var $frame = $(inst.getWin());
        self.mce_height = $frame.innerHeight();
        self.mce_width = $frame.innerWidth();
        
        
        tinymce.dom.Event.add(inst.getWin(), 'resize', function(e) {
           
           if (e && e.currentTarget && e.currentTarget.frameElement) {
             var $frame = $(e.currentTarget.frameElement);
             
             self.mce_height = $frame.innerHeight();
             self.mce_width = $frame.innerWidth();
             
             self.ui.textarea.css("height", self.mce_height + "px");
           }
           
           
        });
        
        
      };
      
			try { // wp sometimes throws errors that I think we can ignore
      	tinyMCE.init(this.mceOptions);
    	} catch(e) {
	
			}

      if (this.ui.button_visual.hasClass("current")) {
        //self.mce_add();
      } else {
        this.ui.textarea.wrap($('<div class="textarea-wrap"></div>'));
        self.pretty();
        
        if (self.using_cm()) {
          self.cm_add();
        }
        
      }
    	
			
			// patch to stop tinymce breaking when using sortable metaboxes
      
			var $sortables = self.element.parents(".ui-sortable").add($('#normal-sortables,#advanced-sortables'));
			
			  $sortables.each( function() {
			
			  var $sortable = $(this);
			    
  			if (!$sortable.data("mce-dragfix")) {
					
  				$sortable.data("mce-dragfix", true);
				  
  				
  	      $sortable.bind({

  					sortstart: function(event, ui) {

						  var $set_item = $(event.originalEvent.target).closest(".mp-set-item");
        			
						  var $fields;
						    
						  if ($set_item.length) {
						    $fields = $set_item.find(".mpft-visual-editor");
					    } else {
						    $fields = $(ui.item).closest(".postbox").find(".mpft-visual-editor");
				      }

        			
  						$fields.each( function() {
							
  							var $field = $(this);
							  
							  if ($field.data("loaded")) {
							    if ($field.mp_field('ui', 'mce_active')) {
                		$field.mp_field('ui', 'mce_remove');
    							}
							  }
							  
  						});
						
  	        },

  	        sortstop: function(event, ui) {
						
  						var $set_item = $(event.originalEvent.target).closest(".mp-set-item");

						  var $fields;

						  if ($set_item.length) {
						    $fields = $set_item.find(".mpft-visual-editor");
					    } else {
						    $fields = $(ui.item).closest(".postbox").find(".mpft-visual-editor");
				      }
						
  						$fields.each( function() {
							
  							var $field = $(this);
							  
							  if ($field.data("loaded")) {
                
							    if ($field.mp_field('ui', 'mce_active')) {
    								$field.mp_field('ui', 'mce_add', true);
    							}
							
							  }
							  
  						});

  	        }
      
  	      });
        }
        
			});
			
			
  	},
    
		mce_active: function() {
			return this.ui && this.ui.button_visual && this.ui.button_visual.hasClass("current");
		},
		
    using_cm: function() {
      return this.ui_options.html_editor == "cm";
    },
    
    cm_remove: function() {
      var self = this;
      
      if (self.codeMirror) {
        self.codeMirror.toTextArea();
      }    
    },
    
    pretty: function() {
      var self = this;
      
      var wrapChars = 80;
      
      if (self.mce_width) {
        wrapChars = Math.round(self.mce_width / 9.3);
      }

      var val = self.ui.textarea.val();
      
      val = val.replace(/<\/(p|div|li|ul|ol)>/g, "\n</$1>\n")
        .replace(/<img/g, "\n<img")
        .replace(/\/>/g, "/>\n")
        .replace(/<(p|div|ul|ol|li)([^>]*)>/g, "<$1$2>\r")
        .replace(/<(h1|h2|h3|h4|h5|h6)>/g, "\n<$1>")
        .replace(/<\/(h1|h2|h3|h4|h5|h6)>/g, "</$1>\n")
        .replace(/\n\s(<a)/g, "\n$1")
        .replace(/<br\s?\/>/g, "<br />\n")
        .replace(/<br\s?\/>\n\n/g, "<br />\n");
               
      self.ui.textarea.val(val);

    },
    
    cm_add: function() {
      var self = this;
      
      
      self.codeMirror = CodeMirror.fromTextArea(self.ui.textarea.get(0), { readOnly: self.is_readonly(), theme: self.ui_options.cm_theme, mode: "htmlmixed", lineWrapping: true, lineNumbers: true, enterMode: 'flat', electricChars: false });

      if (self.mce_height) {
      
        el = self.codeMirror.getScrollerElement();
        el.style.height = self.mce_height + 74 + "px";
        self.codeMirror.refresh();
        
				self.focus();
      }

    },
    
    mce_remove: function(focus) {
      
      this.mce = false;
      tinyMCE.execCommand('mceRemoveControl', false, this.mceId);
      
      if (focus && this.ui && this.ui.textarea) {
        this.ui.textarea.focus();
      }
        
    },
    
    mce_add: function(focus) {
      this.mce = true;
      tinyMCE.execCommand('mceAddControl', true, this.mceId); 
      
      if (focus) {
        tinyMCE.execCommand('mceFocus', false, this.mceId);
      }
    },
    
    get_content: function() {
      var self = this;
      var val;
      
      if (self.ui.button_html.hasClass("current")) {
        if (self.codeMirror) {
          val = self.codeMirror.getValue();
        } else {
          val = self.ui.textarea.val();
        }
        
      } else {
        val = tinyMCE.get(this.mceId).getContent();
      }
      
      return val;
    },
    
    strip_value: function(val) {
      return $.trim( val.replace(/<[^>]*>/ig, "&nbsp;").replace(/^(&nbsp;)+/g, "").replace(/(&nbsp;){2,}/g, "&nbsp;") );
    },
    
    wordwrap: function( str, width, brk, cut, noempty ) {

        brk = brk || '\n';
        width = width || 75;
        cut = cut || false;

        if (!str) { return str; }

        var regex = '.{1,' +width+ '}(\\s|$)' + (cut ? '|.{' +width+ '}|.+$' : '|\\S+?(\\s|$)');

        var ret = str.match( RegExp(regex, 'g') ).join( brk );
        
        return ret;
        
    },
    
    is_empty: function() {
      return this.value() == "";
    },
    
    value: function() {
      
      var self = this;
      self.cache_ui();
      
      if (self.mce) {
        return this.get_content();
      } else {
        return this.element.find("textarea").val();
      }
    },

    set_value: function(value) {
      var self = this;
      this.cache_ui();
      if (this.expanded()) {
        
        if (self.ui.button_html.hasClass("current")) {
          if (self.codeMirror) {
            self.codeMirror.setValue(value);
          } else {
            self.ui.textarea.val(value);
          }

        } else {
          val = tinyMCE.get(this.mceId).setContent(value);
        }
        
      } else {
        this.element.find("textarea").val(value);
      }
    },
    
    summary: function() {
	
			var val = this.strip_value(this.value());
      // remove shorttags (more readable summary) 
      
      var content = $.trim(val.replace(/\[[^\]]*\]/ig, '').replace(/^\s*/ig, '').replace(/^(&nbsp;)+/g, ""));
      
			if (content == "") {
				content = val;
			}
			
      return content;
    }


  });

})(jQuery);