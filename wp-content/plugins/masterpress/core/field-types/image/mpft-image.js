
(function($) {

  // the type variable below must be set to the underscored version of the field type key
  // e.g. if you have a field type with key 'my-editor', the type variable below would be "my_editor"
  
  var field_type = "image";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      autoSummary: false
  	},

		is_ie9: function() {
			// hate using browser detection, but alas:
			return !!document.documentMode && document.documentMode >= 9;
		},
		
    focus: function() {
      if (this.is_empty()) {
        if (!this.is_ie9()) {
					this.element.find(".qq-upload-button input").focus();
				}
			
      } else {
        this.ui.thumb.find("a").focus();
      }
      
    },

    onfirstexpand: function() {
      
      var self = this;

      this.overflow = false;
      
      this.ui = {
        state: this.element.find(".ui-state"),
        input: this.element.find("input.value"),
        thumbs: this.element.find(".thumbs"),
        drop_area: this.element.find(".drop-area"),
        uploader: this.element.find(".file-uploader"),
        prop: this.element.find(".prop"),
        thumb: this.element.find(".thumb"),
        summary_thumb: this.element.find(".summary-thumb"),
        file_name: this.element.find(".filename"),
        file_link: this.element.find(".file-link"),
        clear_button: this.element.find("button.clear"),
        delete_button: this.element.find("button.delete"),
        from_url_button: this.element.find("button.button-from-url"),
        from_media_button: this.element.find("button.button-from-media-library"),
        attributes_button: this.element.find("button.attributes"),
        url: this.element.find("input.url"),
        attributes_dialog: this.element.find(".mpft-image-attributes-dialog")
      };
      
      var attributes_close = function() {
        self.ui.state.append(self.ui.attributes_dialog);
        self.ui.attributes_dialog.hide();
      };
      
      self.setup_dialog(
        "attributes", 
        self.ui.attributes_dialog, {
          buttons: [{
            text: "OK",
            click: function() { $(this).dialog("close") }
          }]
        }
      );
      
      self.ui.uploadProgress = self.element.find(".upload-progress");
      self.ui.uploadName = self.ui.uploadProgress.find(".name");
      self.ui.uploadVal = self.ui.uploadProgress.find(".val");
      self.ui.uploadBar = self.ui.uploadProgress.find(".bar");


      this.field_type = field_type;

      this.ui.drop_area.bind("drop", function() {
        $('.drop-area').hide();
      });
      
      this.ui.clear_button.bind("click", function() {
        if (confirm(self.lang("confirm_clear"))) {
          self.clear();
        }
      });

      this.ui.delete_button.bind("click", function() {
        if (confirm(self.lang("confirm_delete"))) {
          self.do_delete();
        }
      });
      
      this.ui.thumb.bind("mouseenter", function() {
        self.ui.drop_area.hide();
      });
      
      this.ui.attributes_button.click( function() {
        self.show_dialog("attributes");
      });
      
      
      this.ui.from_url_button.bind("click", function() {
        self.ui.state.addClass("from-url");
        self.ui.url.focus().select();
        
        if (!self.ui.url_cancel) {
          
          self.ui.url_cancel = self.element.find(".from-url .cancel");
          self.ui.url_download = self.element.find(".from-url .download");
          
          self.ui.url_cancel.bind("click", function() {
            self.ui.state.removeClass("from-url");
            self.ui.from_url_button.focus();
          });

          self.ui.url_download.bind("click", function() {
            self.downloadImage();
          });

        }
      
        
      });
      
      this.ui.url.bind("keypress", function(event) {
        if (event.which == $.ui.keyCode.ENTER) {
          self.downloadImage();
          return false;
        }
        
      });
      
			self.use_new_media = self.ui.from_media_button.data("library") == "new";
			
      this.ui.from_media_button.bind( "click", function() {
        
        if (self.use_new_media) {

					if (!self.frame) {
						
						// WordPress 3.5 Media Library Support
		        self.frame = wp.media( { frame: 'select', library: { type: 'image' } } );
						self.frame.state('library');
				
						self.frame.on("select", function() {
							var selection = self.frame.state().get('selection').first();
					
							if (selection) {
								var atts = selection.attributes;
						
						
								self.ui.state.addClass("media-library");

		          	self.method("verify_attachment", { model_id: self.model_id, id: atts.id }, function(response) {
            
			            self.start_busy();
            
			            if (response.success) {
									
			              self.prop("attachment_id", atts.id);
									
										// set the attributes
			          		self.ui.attributes_dialog.find(".alt").val(atts.alt);
			          		self.ui.attributes_dialog.find(".title").val(atts.title);
		          		
			    					self.set_value(response.url);
              
              
			            } else {
			              alert(response.error);
			              self.hideUploading();
			            }
            
			            self.end_busy();
            
			          });

						
							}
						
						});
					
					}
					
					self.frame.open();
				
				} else {
					
					// reset the set field function to insert into this field
        
        	window.mp_media_library_set_field = function(id) {
	          self.ui.state.addClass("media-library");

	          self.method("verify_attachment", { model_id: self.model_id, id: id }, function(response) {
            
	            self.start_busy();
            
	            if (response.success) {
              
	              self.prop("attachment_id", id);
	              self.set_value(response.url);
              
              
	            } else {
	              alert(response.error);
	              self.hideUploading();
	            }
            
	            self.end_busy();
            
	          });
	        };
        
        	tb_show('', mp_wp_media_library_url );
					
				}
				
        
      });
      
			self.ui.file_link.click( function() {
			  self.ui.thumb.find("a").click();
        return false;
      });
      
			self.ui.summary_thumb.mp_thumb();
			self.ui.thumb.mp_thumb();
      
			this.centerThumbnail();

      // setup an uploader
      
      var maxSize = parseInt(this.ui_options.allowed_maxsize);
      
      if (parseInt(this.ui_options.allowed_maxsize)) {
        maxSize = 1024 * 1024 * maxSize;
      } else {
        maxSize = null;
      }
      
      this.ui.uploader
        .mp_file_uploader({
          manualSet: true,
          allowedExtensions: this.ui_options.allowed_types,
          sizeLimit: maxSize,
          model_id: self.model_id
        })
        .bind("submit.mp_file_uploader", function(event, data) {
          self.start_busy();
          
          self.overflow = false;
          
          self.ui.uploadName.html(data.fileName);
          self.ui.uploadBar.show();
          self.ui.state.addClass("uploading").removeClass("from-url");
        })
        .bind("progress.mp_file_uploader", function(event, data) {
          self.ui.uploadBar.css("width", data.percent + "%");
          self.ui.uploadVal.html(data.percent + "%");
        })
        .bind("complete.mp_file_uploader", function(event, data) {
          
          // hide all drop areas
          $('.drop-area').hide();

          if (data.response.success) {
          
            // remove any URLs setup
            self.ui.url.val('');
            
            // remove any attachment IDs from previous link to a media library item
            self.prop("attachment_id", '');

            if (!self.overflow) {
              self.overflow = true;
              self.set_value( data.url );
            } else {
              // send the image to the set
              
              if (self.is_multiple()) {
                var add_data = {};
                add_data[self.name] = { value: data.url };
                self.options.set.mp_set("add", null, add_data, "bottom");
              }
            
            }
            
          } else {
            
            if (data.response.error) {
              alert(data.response.error);
            } else {
              alert("Sorry, an error occurred while trying to upload this file, likely caused by an out of memory condition.")
            }

            self.hideUploading();
          }

      });

			this.ui.uploader.find(".qq-upload-button span")



    },
   
    
    downloadImage: function() {
      var self = this;
      
      self.ui.url.removeClass("error");
  
      var params = {
        object_id: $.mp.object_id,
        object_type: $.mp.object_type,
        object_type_name: $.mp.object_type_name,
        model_id: self.model_id,
        url: self.ui.url.val()
      };
    
      self.start_busy();

      self.ui.state.addClass("downloading").removeClass("from-url");

      self.method("download_image", params, function(response) {

        if (response && response.success) {
                    
          // remove any attachment IDs from previous link to a media library item
          self.prop("attachment_id", '');

          self.set_value(response.url);
          self.ui.state.addClass("fetching");
        
        } else {
          if (response) { 
            alert(response.error); 
          }
          self.ui.url.addClass("error");
          self.ui.state.removeClass("downloading").addClass("from-url");
          self.ui.url.focus().select();
        }
        
        
      
      });
    
      self.end_busy();
  
  
    },
    
    set_value: function(value) {
      this.element.find("input.value").val( value );

      if (!this.expanded()) {
        this.onfirstexpand();
      } 
      
      this.fetchInfo();
    },

    value: function() {
      return this.element.find("input.value").val();
    },
    
    fetchInfo: function(auto) {
      var self = this;
      
      self.start_busy();

      var url = self.ui.input.val();
      
      if (url != "") {
        // now fire off a request to the PHP class to get the image info, 
        // and generate the expanded and compressed thumbnails
            
          
        self.ui.state.addClass("fetching");

        self.method('image_info', { url: url }, function(response) {

          self.hideUploading();

          if (response && !response.error) {

            self.set_change();

            if (response.width == response.height) {
              self.ui.thumbs.removeClass("portrait landscape").addClass("square");
            } else if (response.width > response.height) {
              self.ui.thumbs.removeClass("portrait square").addClass("landscape");
            } else {
              self.ui.thumbs.removeClass("landscape square").addClass("portrait");
            }
          	
						// show the image info 
            self.ui.prop.find(".filetype").html(response.filetype);
            self.ui.prop.find(".width").html(response.width);
            self.ui.prop.find(".height").html(response.height);
            self.ui.prop.find(".filesize").html(response.size);

						// show the file info
            // self.ui.file_name.attr($.mp.smartTrim(response.basename, 50));
            self.ui.file_link.attr("href", response.url);
            self.ui.file_link.attr("title", response.basename);
						
						// setup the mp_thumbs
						
						self.ui.thumb.mp_thumb('set_info', { 
							thumb: response.thumb,
							thumb_width: response.thumb_width,
							thumb_height: response.thumb_height,
	
							href: response.url, 
							title: response.basename, 
							link_attr: { 'class' : 'thumb' }, 
	
							width: response.width, 
							height: response.height 
						});

						self.ui.summary_thumb.mp_thumb('set_info', { 
							thumb: response.summary_thumb,
							thumb_width: response.summary_thumb_width,
							thumb_height: response.summary_thumb_height,

							width: response.width, 
							height: response.height,

							title: response.basename
						});
								
						

            self.centerThumbnail();

            self.ui.state.removeClass("empty");

          } 
        
                  
          self.end_busy();

          if (auto) {
            self.collapse();
          } else {
            self.ui.thumb.focus();
          }
        });
        
        
      }
            
    },
    
    hideUploading: function() {
      var self = this;
      self.ui.uploadVal.html("");
      self.ui.uploadBar.css("width", 0).hide();
      self.ui.state.removeClass("downloading fetching uploading media-library");
    },
    
    centerThumbnail: function() {
      /* Ensures that very small images are shown in the center of their thumb area */
      
      if (this.expanded()) {

        var p = this.ui.thumb;
        var i = p.find("img");
        
        var pw = p.width();
        var ph = p.height();
				
				var iw = 0;
				var ih = 0;

				iw = i.width();
				ih = i.height();
          
        if (!iw) {
       	  iw = i.data("thumb_width");
        }
        
        if (!ih) {
       	  ih = i.data("thumb_height");
        }
				
        
        i.css({ left: 0, top: 0 });

        if (iw != 0 || ih != 0) {
        
          var hd = Math.floor( ( ph - ih ) / 2 );
          var wd = Math.floor( ( pw - iw ) / 2 );
        
          if (wd > 0 || hd > 0) {
            i.css({ left: wd, top: hd });
          } 
        
        }
      
      }
    },
    
    is_empty: function() {
      return $.trim( this.ui.input.val() ) == "";
    },

    do_delete: function() {
      var self = this;
      
      var set_index = self.element.closest(".mp-set-item").find(".original-set-index").val();
      
      self.method('delete_file', { url: self.ui.input.val() }, function(response) {

        if (response.success) {
          self.clear();
        } else {
          alert(self.lang("delete_error"));
        }

      });
      
    },
    
    clear: function() {
      var self = this;
      self.set_change();
      self.ui.input.val("");
      self.ui.thumbs.removeClass("portrait landscape").addClass("square");
			self.ui.thumb.mp_thumb("clear");
			self.ui.summary_thumb.mp_thumb("clear");
      self.ui.state.addClass("empty");
    },
    
    summary: function() {
      return this.element.find(".summary-thumb").clone();
    },
    
    destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}

  });

})(jQuery);