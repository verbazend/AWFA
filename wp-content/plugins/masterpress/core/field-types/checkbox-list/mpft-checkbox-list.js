
(function($) {

  var field_type = "checkbox_list";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},
    
    cache_ui: function() {
      this.ui = this.ui || {
        input: this.element.find("input"),
        check_all: this.element.find(".check-all"),
        uncheck_all: this.element.find(".uncheck-all")
      };
    },

  	onfirstexpand: function() {
      var self = this;

      this.cache_ui();
      
      this.ui.check_all.bind("click", function() {
        self.check_all();
      });
      
      this.ui.uncheck_all.bind("click", function() {
        self.uncheck_all();
      });
  	},
    
    uncheck_all: function() {
      this.ui.input.removeAttr("checked");
    },

    check_all: function() {
      this.ui.input.attr("checked", "checked");
    },
    
    is_empty: function() {
      return !this.checked().length;
    },
    
    checked: function() {
      return this.element.find("input:checked");
    },
    
    value: function() {
      var checked = this.checked();
      
      var vals = [];
      
      checked.each( function() {
        vals.push( $(this).val() );
      });
      
      return vals;
    },
    
    set_value: function(value) {
      this.cache_ui();
      
      if (typeof value == "string") {
        value = value.split(",");
      }
      
      if ($.isArray(value)) {
        
        this.element.find("input").each( function() {
          
          if ($.inArray($(this).val(), value) !== -1) {
            $(this).attr("checked", "checked");
          } else {
            $(this).removeAttr("checked");
          }
          
        });
        
      }
      
      
    },
    
    summary: function() {
      
      var self = this;
      var labels = [];

      var checked = this.checked();
      
      checked.each( function() {

        var id = $(this).attr("id");
        var $label = self.element.find("label[for=" + id + "]");
        
        if ($label.length) {
          labels.push( $label.html() );
        }
        
      });
      
      return labels.join(", ");
    },
    
    destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}

  });

})(jQuery);