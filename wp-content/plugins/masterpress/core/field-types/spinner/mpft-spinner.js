
(function($) {

  // the type variable below must be set to the underscored version of the field type key
  // e.g. if you have a field type with key 'my-editor', the type variable below would be "my_editor"
  
  var field_type = "spinner";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    cache_ui: function() {
      this.ui = this.ui || {
        input: this.element.find("input"),
        up: this.element.find("button.up"),
        down: this.element.find("button.down")
      };
    },
    
    onfirstexpand: function() {
      var self = this;
      
      this.cache_ui();
      
      var max = null;
      
      if (this.ui_options.max != '') {
        max = parseInt(this.ui_options.max);
      }

      var format = '0';
      
      if (this.ui_options.format != '') {
        format = this.ui_options.format;
      }
          
      this.ui.input.inputspinner({
        up: this.ui.up,
        down: this.ui.down,
        step: this.ui_options.step || 1,
        min: this.ui_options.min || 0,
        max: this.ui_options.max,
        cnNegative: this.ui_options.negative_red ? 'negative' : null,
        format: this.ui_options.format || '0'
      })
      .bind("afterchange.inputspinner", function() {
        self.set_change();
      });

    },
    
    value: function() {
      return this.element.find("input").val();
    },
    
    set_value: function(value) {
      this.cache_ui();
      this.element.find("input").val(value);
    },
    
    is_empty: function() {
      return $.trim(this.ui.input.val()) == "";
    },
    
    summary: function() {
      if (this.ui.input.hasClass("negative")) {
        return '<span class="negative">' + this.ui.input.val() + '</span>';
      }
      
      return this.ui.input.val();
    }
    

  });

})(jQuery);