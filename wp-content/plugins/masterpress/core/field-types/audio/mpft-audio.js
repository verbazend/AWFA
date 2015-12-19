
(function($) {

  var field_type = "audio";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      autoSummary: false
  	},

    onfirstexpand: function() {
      
      var self = this;

      this.overflow = false;

      this.ui = {
        state: this.element.find(".ui-state"),
        input: this.element.find("input.value"),
        drop_area: this.element.find(".drop-area"),
        uploader: this.element.find(".file-uploader"),
        prop: this.element.find(".prop"),
        file_info: this.element.find(".file-info"),
        player: this.element.find(".player"),
        clear_button: this.element.find("button.clear"),
        delete_button: this.element.find("button.delete"),
        view_button: this.element.find("button.view"),
        from_url_button: this.element.find("button.button-from-url"),
        from_media_button: this.element.find("button.button-from-media-library"),
        url: this.element.find("input.url")
      };
      
      self.ui.uploadProgress = self.element.find(".upload-progress");
      self.ui.uploadName = self.ui.uploadProgress.find(".name");
      self.ui.uploadVal = self.ui.uploadProgress.find(".val");
      self.ui.uploadBar = self.ui.uploadProgress.find(".bar");

      
      self.ui.summary = self.element.find(".summary");
      self.ui.summary_name = self.ui.summary.find(".name");
      self.ui.summary_size = self.ui.summary.find(".size");
      self.ui.summary_type = self.ui.summary.find(".type");
            
      var si = self.summary_item();
      this.ui.summary_head = si.find("h4");
      this.ui.summary_head_icon = si.find("h4 i");
            
      this.ui.file_name = this.ui.file_info.find(".name");
      this.ui.file_name_icon = this.ui.file_info.find(".name i");

      this.field_type = field_type;

      this.ui.file_info.click( function() {
        if (self.ui.state.hasClass("empty")) {
          return false;
        }
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

      this.ui.view_button.bind("click", function() {
        window.open(self.ui.input.val());
      });

      
      this.ui.from_url_button.bind("click", function() {
        
        self.ui.state.addClass("from-url");
        self.ui.url.focus().select();
        
        if (!self.ui.url_cancel) {
          
          self.ui.url_cancel = self.element.find(".from-url .cancel");
          self.ui.url_download = self.element.find(".from-url .download");
          
          self.ui.url_cancel.bind("click", function() {
            self.ui.state.removeClass("from-url");
          });

          self.ui.url_download.bind("click", function() {
            self.downloadFile();
          });

        }
      
        
      });
      
      this.ui.url.bind("keypress", function(event) {
        if (event.which == $.ui.keyCode.ENTER) {
          self.downloadFile();
          return false;
        }
        
      });


      this.ui.from_media_button.bind( "click", function() {
        
        // reset the set field function to insert into this field
        
        window.mp_media_library_set_field = function(id) {
          self.ui.state.addClass("media-library");

          self.method("verify_attachment", { model_id: self.model_id, id: id }, function(response) {
            
            self.start_busy();
            
            if (response.success) {
              
              self.prop("attachment_id", id);
              self.set_value(response.url);
              
              
            } else {
              if (response.error) {
                alert(response.error);
              } else {
                alert("Sorry, an error occurred while trying to upload this file, likely caused by an out of memory condition.")
              }
            
              self.hideUploading();
            }
            
            self.end_busy();
            
          });
        };
        
        tb_show('', mp_wp_media_library_url );
        
        
      });
      
      
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
          model_id: this.model_id
        })
        .bind("submit.mp_file_uploader", function(event, data) {

          self.start_busy();

          self.overflow = false;

          self.ui.uploadName.html('<i></i>' + $.mp.smartTrim(data.fileName, 40));
          self.ui.uploadBar.show();
          self.ui.state.addClass("uploading");
        })
        .bind("progress.mp_file_uploader", function(event, data) {
          self.ui.uploadBar.css("width", data.percent + "%");
          self.ui.uploadVal.html(data.percent + "%");
        })
        .bind("complete.mp_file_uploader", function(event, data) {
          
          // hide all drop areas
          $('.drop-area').hide();
            
          // TODO - add error message?

          if (data.response.success) {
            
            self.ui.url.val('');

            // remove any attachment IDs from previous link to a media library item
            self.prop("attachment_id", '');

            self.set_change();
            
            
            if (!self.overflow) {
							
              self.overflow = true;
              self.set_value( data.url );

            } else {

              if (self.is_multiple()) {
                var add_data = {};
                add_data[self.name] = { value: data.url };
                
                self.options.set.mp_set("add", null, add_data);
              }

            }
            
          } else {
            self.hideUploading();

            if (data.response.error) {
              alert(data.response.error);
            } else {
              alert("Sorry, an error occurred while trying to upload this file, likely caused by an out of memory condition.")
            }

          }
          
      });
      
      
      this.setupPlayer();
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
    
    downloadFile: function() {
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

      self.method("download_file", params, function(response) {

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
    
    setupPlayer: function() {
      var self = this;
      
      self.audio = self.ui.player.find("audio").get(0);
              
      if (self.audio) {
        
        self.me_player = new MediaElementPlayer(
          self.audio, { 
            success: function (me) {
              
                me.addEventListener('loadeddata', function () {
                  self.prop("duration", me.duration);
                })
            },
            error: function(e) { console.log(e); }

          }
        );
      }
    },
    
    fetchInfo: function() {
      var self = this;
      
      var url = self.ui.input.val();
      
      if (url != "") {
        // now fire off a request to the PHP class to get the file info, 

        self.start_busy();
            
        self.ui.state.addClass("fetching");

        self.method('upload_info', { url: url }, function(response) {
					
          self.hideUploading();

          if (response && !response.error) {

            self.ui.summary_name.html($.mp.smartTrim(response.basename, 27, '&hellip;'));
            self.ui.summary_name.attr("title", response.basename);

            self.ui.summary_type.html($.mp.smartTrim(response.short_filetype, 26, '&hellip;'));
            self.ui.summary_type.attr("title", response.filetype);

            self.ui.summary_size.html(response.size);
            
            self.ui.prop.find(".filetype").html(response.filetype);
            self.ui.prop.find(".filesize").html(response.size);

            self.ui.file_name.html('<i></i>' + $.mp.smartTrim(response.basename, 35));
            self.ui.file_name.attr("title", response.basename);
            self.ui.file_name.find("i").attr("class", "file-type file-type-" + response.extension);
            self.ui.summary_head_icon.attr("class", "file-type file-type-" + response.extension);
            
            if ($.inArray(response.extension, ['3fr','ari','arw','srf','sr2','bay','crw','cr2','cap','iiq','eip','dcs','dcr','drf','k25','kdc','dng','erf','fff','mef','mos','mrw','nef','nrw','orf','pef','ptx','pxn','R3D','raf','raw','rw2','raw','rwl','dng','rwz','srw','x3f']) != -1) {
              self.ui.summary_head_icon.attr("class", "file-type file-type-raw");
              self.ui.file_name.find("i").attr("class", "file-type file-type-raw");
            }
            
            self.ui.file_info.attr("href", response.url);
            
            if (response.extension == "mp3") {
              self.ui.player.removeClass("player-na");
              self.ui.player.html('<audio controls="control" preload="auto" src="' + response.url + '" type="audio/mp3"></audio>');
              self.setupPlayer();
            } else {

              if (self.me_player) {
                self.me_player = null;  
              }
              self.ui.player.empty();
              self.ui.player.addClass("player-na");
            }
          

            self.ui.state.removeClass("empty");

          } else {
						if (response) {
            	alert(response.error);
						}
					
          }

          self.end_busy();

        });
      
      }
            
    },
    
    hideUploading: function() {
      var self = this;
      self.ui.uploadVal.html("");
      self.ui.uploadBar.css("width", 0).hide();
      self.ui.state.removeClass("downloading fetching uploading media-library");
    },
    
    is_empty: function() {
      return $.trim(this.ui.input.val()) == "";
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
      self.ui.file_name.html(self.lang("no_file"));
      self.ui.file_name.removeAttr("title");
      self.ui.file_name.attr("class", "name");
      self.ui.summary_head.attr("class", "");
      self.ui.state.addClass("empty");
    },


    summary: function() {
      return this.ui.summary.clone();
    },
    
    destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}

  });

})(jQuery);