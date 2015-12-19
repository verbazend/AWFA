
(function($) {

  var field_type = "radio_button_list";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    cache_ui: function() {
      
      this.ui = this.ui || {
        input: this.element.find("input.radio"),
        uncheck_all: this.element.find(".uncheck-all")
      };
    },
    
  	onfirstexpand: function() {
      var self = this;

      this.cache_ui();
      
      this.ui.uncheck_all.click( function() {
        self.ui.input.removeAttr("checked");
      });
      
  	},

    focus: function() {
      var self = this;
      self.ui.input.eq(0).focus();
      self.focus_field();
    },

    checked_input: function() {
      return this.ui.input.filter(":checked");
    },
    
    value: function() {
      this.cache_ui();
      
      var selected = this.ui.input.filter(":checked");
      
      if (selected.length) {
        return selected.attr("value");
      }
    
      return "";
    },
    
    set_value: function(value) {
      this.cache_ui();
      this.element.find("input[val=" + value + "]").attr("checked", "checked");
    },
    
    value_label: function() {
      
      var $input = this.checked_input();
      
      if ($input.length) {
        
        var $label = this.element.find("label[for=" + $input.attr("id") + "]");
        
        if ($label.length) {
          return $label.html();
        }
        
      }
      
      return "";
    },
    
    is_empty: function() {
      return !this.checked_input().length;
    },
    
    summary: function() {
      return this.value_label();
    },
    
    destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}

  });

})(jQuery);