
(function($) {

  var field_type = "color_picker";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    cache_ui: function() {
      
      if (!this.ui) {
        
        this.ui = {
          input: this.element.find("input"),
          colorpicker: this.element.find(".colorpicker"),
          colorpreview: this.element.find(".colorpreview"),
          summary: this.element.find(".color-info")
        };
      
        this.ui.summarywell = this.ui.summary.find(".well");
        this.ui.summaryvalue = this.ui.summary.find(".value");
        
      }
      
    },
    
    is_readonly: function() {
      return (this.ui.input.attr("readonly") == "readonly"); 
    },
    
  	onfirstexpand: function() {
  	 
      var self = this;

      this.cache_ui();
      
      if (!this.is_readonly()) {

        this.ui.input.iris({
          hide: false,
          change: function( event, ui ) {
            
            var val = ui.color.toString();
            
            self.ui.colorpreview.css( "background-color", val );
            self.ui.input.val( val );
            
          }
        });

      }
      
      
      
      this.ui.input.change( function() {
        var val = $.mp.parseColor( $.trim(self.ui.input.val()) );
        self.ui.input.attr("value", val);
      });

      // should eventually refactor this into a wrapper plug-in.

      function changeColor(color) {
        self.set_change();
        self.ui.input.val(color);
        self.update_color_well();
      };
    
      self.ui.colorpreview.click( function() { self.ui.input.focus(); } );
       
			this.update_color_well();

      
  	},
    
    update_color_well: function() {
      var self = this;
      self.ui.colorpreview.css("background-color", self.ui.input.val());
      self.ui.summarywell.css("background-color", self.ui.input.val());
      self.ui.summaryvalue.html(self.ui.input.val());
    },
    
    set_value: function(value) {
      this.cache_ui();
      this.element.find("input").val(value);
      this.update_color_well();
    },
    
    value: function() {
      return this.element.find("input").val();
    },
    
    is_empty: function() {
      return $.trim(this.ui.input.val()) == "";
    },
    
    summary: function() {
      this.cache_ui();
      this.update_color_well();
      return this.ui.summary.clone();
    },
    
    destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}

  });

})(jQuery);
