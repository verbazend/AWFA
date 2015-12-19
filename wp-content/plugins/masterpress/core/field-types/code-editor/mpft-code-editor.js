
(function($) {

  var field_type = "code_editor";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

    cache_ui: function() {
      this.ui = this.ui || {
        textarea: this.element.find("textarea"),
        modeselect: this.element.find("select.modeselect"),
        hiddenmode: this.element.find("input.hiddenmode")
      };
    },
    
    onfirstexpand: function() {
      var self = this;
      this.cache_ui();
      self.cm_add();
    },

    focus: function() {
      var self = this;
      self.codeMirror.focus();
      self.focus_field();
    },
    
    is_readonly: function() {
      return (this.ui.textarea.attr("readonly") == "readonly"); 
    },
    
    cm_add: function() {
      var self = this;
      self.codeMirror = CodeMirror.fromTextArea(self.ui.textarea.get(0), { readOnly: this.is_readonly(), onFocus: function() { self.focus_field(); }, theme: self.ui_options.theme || "default", lineWrapping: true, mode: self.ui_options.mode || "htmlmixed", lineNumbers: true });
      
      
      if (this.ui.modeselect && this.ui.modeselect.length) {
        
        this.ui.modeselect.change( function() {
          self.codeMirror.setOption('mode', $(this).val());
        });
        
        this.ui.modeselect.change();
        
      }
      
    
      var maxheight = self.ui_options.maxheight;
      var minheight = self.ui_options.minheight;
      
      if (minheight || maxheight) {
        el = self.codeMirror.getScrollerElement();
        
        if (maxheight) {
          el.style.maxHeight = maxheight + "px";
        }

        if (minheight) {
          el.style.minHeight = minheight + "px";
        }
        
        self.codeMirror.refresh();
      }
      
    },
        
    is_empty: function() {
      this.cache_ui();
      return this.value() == "";
    },

    value: function(entities) {
      var self = this;
      if (self.codeMirror) {
        return this.get_content(entities);
      } else {
        return this.element.find("textarea").val();
      }
    },
    
    set_value: function(value) {
      if (this.expanded()) {
        this.codeMirror.setValue(value);
      } else {
        this.element.find("textarea").val(value);
      }
    },
    
    get_content: function(entities) {
      var self = this;
      val = self.codeMirror.getValue();
      
      if (entities) {
        return $.trim($("<div></div>").text(val).html());
      } else {
        return val;
      }
    },
    
    summary: function() {
      return this.value(true);
    }
    

  });

})(jQuery);