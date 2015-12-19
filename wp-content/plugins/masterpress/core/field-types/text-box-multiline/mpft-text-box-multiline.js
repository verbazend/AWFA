
(function($) {

  // the type variable below must be set to the underscored version of the field type key
  // e.g. if you have a field type with key 'my-editor', the type variable below would be "my_editor"
  
  var field_type = "text_box_multiline";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    cache_ui: function() {
      this.ui = this.ui || {
        textarea: this.element.find("textarea"),
        status: this.element.find(".status")
      };      
    },
    
  	onfirstexpand: function() {

      var self = this;
      
      this.cache_ui();
      
      this.meta = this.ui.textarea.metadata({ type: 'class' });
      
      this.characters_remaining = this.lang("characters_remaining");
      this.character_remaining = this.lang("character_remaining");
      this.no_characters_remaining = this.lang("no_characters_remaining");
      
      if (this.meta.maxlength) {
        // setup a character limiter
          
        var onEditCallback, onLimitCallback;
        
        if (self.ui.status.length) {
        	onEditCallback = function(remaining){
        	  self.ui.status.html($.mp.itemCount(remaining, self.character_remaining, self.characters_remaining, self.no_characters_remaining));
          
        		if (remaining > 0){
        			self.ui.status.removeClass("at-limit");
        		} 
        	};

        	onLimitCallback = function(){
        		self.ui.status.addClass("at-limit");

            setTimeout( function() {
              self.ui.status.removeClass("at-limit");
            }, 1000)

        	};
        }
        
      	self.ui.textarea.limitMaxlength({
          maxlength: this.meta.maxlength,
      		onEdit: onEditCallback,
      		onLimit: onLimitCallback
      	});

      }
      
  	},

    set_value: function(value) {
      this.cache_ui();
      this.element.find("textarea").val(value);
    },
    
    value: function() {
      return this.element.find("textarea").val();
    },
    
    is_empty: function() {
      return $.trim(this.ui.textarea.val()) == "";
    },
    
    summary: function() {
      return this.ui.textarea.val().replace(/\r|\n/gi, "<br />");
    }

  });

})(jQuery);