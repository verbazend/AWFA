
(function($) {

  // the type variable below must be set to the underscored version of the field type key
  // e.g. if you have a field type with key 'my-editor', the type variable below would be "my_editor"
  
  
  var field_type = "video";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      autoSummary: false
  	},
    
    cleanDate: function(date) {
      var clean = date;
      var matches = date.match(/(.*)\.000Z/);
      
      if (matches) {
        clean = matches[1];
      }
    
      return clean;
    },
    
    focus: function() {
      var self = this;
      self.ui.url.focus().select();
      self.focus_field();
    },
    
    formatDate: function(date) {
      
      var date = Date.parse(this.cleanDate(date));
      
      if (date) {
        return date.toString("dd MMM yyyy");
      }

      return false;
      
    },
    
    duration: function(secs) {
      var t = new Date(1970,0,1);
      t.setSeconds(secs);
      return t.toTimeString().substr(0,8);
    },

    cache_ui: function() {
      this.ui_content = this.ui_content || this.element.find(".ui-content");
      
      this.ui = this.ui || {
        state: this.element.find(".state"),
        summary: this.element.find(".summary-content"),
        error: this.ui_content.find(".error-message"),
        url: this.ui_content.find("input.url"),
        thumb: this.ui_content.find(".mp-thumb"),
        duration: this.ui_content.find(".prop .duration"),
        updated_wrap: this.ui_content.find(".updated"),
        published_wrap: this.ui_content.find(".published"),
        updated: this.ui_content.find(".updated .val"),
        published: this.ui_content.find(".published .val"),
        bt_refresh: this.ui_content.find("button.refresh"),        
        title_link: this.ui_content.find(".title-link")
      };      
      
      this.summary_ui = this.summary_ui || {
        thumb: this.ui.summary.find(".summary-thumb"),
        host_name: this.ui.summary.find(".host-name"),
        host_type: this.ui.summary.find(".host-type"),
        duration: this.ui.summary.find(".duration"),
        title: this.ui.summary.find(".title")
      };
      
    },
    
  	onfirstexpand: function() {
      
      var self = this;
      
      this.field_type = field_type;

      this.cache_ui();

			self.ui.thumb.mp_thumb();
			self.summary_ui.thumb.mp_thumb();
      
      self.ui.bt_refresh.bind("click", function() {
        self.infer_info();
        return false;
      });
      
      self.ui.url.bind("change", function() {
        self.infer_info();
      });
      
      if (self.ui.url.val() != "" && self.ui.state.hasClass("empty")) {
        self.infer_info();
      }
    
    },
    
    infer_info: function() {
      
      var self = this;
      
      var error;
      
      var url = self.ui.url.val();
      
      // http://www.youtube.com/watch?v=mS76qGGPSmg
      // http://youtu.be/mS76qGGPSmg
      
      self.ui.error.html("");
      self.ui.state.removeClass("mp-error");
      
      if (url && url != "") {
        
        var matches;
      
        var host;
        var nurl; // normalized URL
        var video_id;
      
      
        // YouTube (Page) - http://www.youtube.com?/watch?v=VIDEO_ID

        if (!matches) {
          if (matches = url.match(/youtube.com\/watch\?.*v=([^&]+)/)) {
            host = "youtube";
          }
        }
      
        // YouTube (Player) - http://www.youtube.com/v/VIDEO_ID

        if (!matches) {
          if (matches = url.match(/youtube.com\/v\/([^&\?]+)/)) {
            host = "youtube";
          }
        }

        // YouTube (Embed) - http://www.youtube.com/embed/VIDEO_ID

        if (!matches) {
          if (matches = url.match(/youtube.com\/embed\/([^&\?]+)/)) {
            host = "youtube";
          }
        }
    
        // YouTube (Page - Shortened) - http://youtu.be/VIDEO_ID

        // YOUTUBE - http://www.youtube.com?/watch?v=VIDEO_ID

        if (!matches) {
          if (matches = url.match(/youtu\.be\/([^&\?]+)/)) {
            host = "youtube";
          }
        }
      
        if (!matches) {
          if (matches = url.match(/vimeo.com\/([^&\?]+)/)) {
            host = "vimeo";
          }
        }

        if (!matches) {
          if (matches = url.match(/player.vimeo.com\/video\/([^&\?]+)/)) {
            host = "vimeo";
          }
        }
        
        self.ui.title_link.attr("class", "title-link " + host); 

        if (matches && host == "youtube") {
          nurl = "http://youtu.be/" + matches[1];
          video_id = matches[1];
        
          self.prop("host", host);  
          self.prop("video_id", matches[1]);  

          self.ui.url.val(nurl);  

          // update the URL for the thumbnail
          self.ui.thumb.attr("href", "http://youtube.com/v/" + video_id);

          // update the URL for the title link
          self.ui.title_link.attr("href", "http://youtu.be/" + video_id);

        } else if (matches && host == "vimeo") {
          
          nurl = "http://vimeo.com/" + matches[1];
          video_id = matches[1];
        
          self.prop("host", host);  
          self.prop("video_id", matches[1]);  
					
					
          self.ui.url.val(nurl);  

          // update the URL for the thumbnail
          self.ui.thumb.attr("href", "http://player.vimeo.com/video/" + video_id);

          // update the URL for the title link
          self.ui.title_link.attr("href", nurl);
          
        }
        
        
      
        if (matches) {
       
          self.ui.state.addClass("fetching");

          self.start_busy();

          self.method('video_info', { host: host, video_id: video_id }, function(response) {

            self.ui.state.removeClass("fetching");

            if (response.success) {
            
              self.ui.state.removeClass("empty error");
              self.ui.title_link.html($.mp.smartTrim(response.title, 54));

              self.ui.duration.html(self.duration(response.duration));

              self.summary_ui.duration.html("(" + self.duration(response.duration) + ")");
              self.summary_ui.host_name.html(self.lang(host));
              self.summary_ui.host_type.attr("class", "host-type " + host);
              self.summary_ui.title.html($.mp.smartTrim(response.title, 44));

							self.ui.thumb.mp_thumb('set_info', { 
								thumb: response.thumb,
								thumb_width: response.thumb_width,
								thumb_height: response.thumb_height,
	
								href: response.url, 
								link_attr: { 'class' : 'thumb iframe' }, 
	
								width: response.width, 
								height: response.height 
							});

							self.summary_ui.thumb.mp_thumb('set_info', { 
								thumb: response.summary_thumb,
								thumb_width: response.summary_thumb_width,
								thumb_height: response.summary_thumb_height,

								width: response.width, 
								height: response.height
							});
								
								
                        
              self.ui.thumb.removeClass("empty");
            
              // set property values
            
              self.prop("duration", response.duration);
              self.prop("title", response.title);
              self.prop("description", response.description);
              self.prop("keywords", response.keywords);
              self.prop("categories", response.categories);
              self.prop("aspect_ratio", response.aspect_ratio);
              self.prop("thumbnail", response.thumbnail);
              self.prop("thumbnails", response.thumbnails);

              self.prop("width", response.width);
              self.prop("height", response.height);
              self.prop("stats", response.stats);
              self.prop("user_name", response.user_name);
              self.prop("user_url", response.user_url);
            
              self.ui.updated_wrap.hide();
              self.ui.published_wrap.hide();

              if (response.published) {
                var pub = self.formatDate(response.published);

                self.prop("published", self.cleanDate(response.published));
              
                if (pub) {
                  self.ui.published_wrap.show();
                  self.ui.published.html(pub);
                }
              } else {
                self.prop("published", "");
              }

              if (response.updated && response.updated != "") {
                var up = self.formatDate(response.updated);

                self.prop("updated", self.cleanDate(response.updated));
              
                if (up) {
                  self.ui.updated_wrap.show();
                  self.ui.updated.html(up);
                }
              } else {
                self.prop("updated", "");
              }
            	
            } else {
              self.show_error(response.error);
            }

            self.end_busy();
            
          });
        
        } else {
          self.show_error(self.lang("error_invalid_url"));
        }
      
      } else {
        self.clear();
      }
      
    },
    
    show_error: function(error) {
      var self = this;
      self.clear();
      self.ui.state.addClass("mp-error");
      self.ui.error.html("<i></i>" + error);
    },
    
    clear: function() {
      var self = this;
      
      self.ui.state.addClass("empty");
			self.ui.thumb.mp_thumb('clear');

      self.prop("video_id", "");
      self.prop("duration", "");
      self.prop("title", ""); 
      self.prop("description", ""); 
      self.prop("keywords", "");
      self.prop("category", "");
      self.prop("category_description", "");
      self.prop("aspect_ratio", "");
      self.prop("thumbnail", "");
      self.prop("thumbnails", "");
      self.prop("published", "");
      self.prop("updated", "");
      self.prop("width", "");
      self.prop("height", "");
      self.prop("stats", "");
      self.prop("user_name", "");
      self.prop("user_url", "");

      self.summary_ui.duration.html("");
      self.summary_ui.host_name.html("");
      self.summary_ui.host_type.attr("class", "host-type");
      self.summary_ui.title.html("");

			self.summary_ui.thumb.mp_thumb('clear');
			
			
    },
    
    set_value: function(value) {
      this.cache_ui();
      this.element.find("input.url").val(value);

      if (!this.expanded()) {
        this.onfirstexpand();
      } else {
        this.infer_info();
      }
    },
    
    value: function() {
      return this.element.find("input.url").val();
    },
    
    is_empty: function() {
      return $.trim(this.ui.url.val()) == "" || this.prop_val("video_id") == "";
    },
    
    summary: function() {
      return this.element.find(".summary-content").clone();
    },
    
    destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}
  	

  });

})(jQuery);