(function($) {

  var field_type = "checkbox";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }
  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    cache_ui: function() {
      this.ui = this.ui || {
        input: this.element.find("input")
      };
      
    },
    
    onfirstexpand: function() {
      this.cache_ui();
    },
    
    is_empty: function() {
      var checked = this.checked();
      return !checked;
    },
    
    set_value: function(value) {
      this.cache_ui();
      var input = this.element.find("input");
      
      var checked = value !== "false";
      
      if (checked) {
        input.attr("checked", "checked");
      } else {
        input.removeAttr("checked");
      }
      
    },
    
    value: function() {
      return this.element.find("input:checked").length ? true : false;
    },
    
    checked: function() {
      return this.ui.input.is(":checked");
    },
    
    summary: function() {
      return this.lang("checked");
    }

  });

})(jQuery);