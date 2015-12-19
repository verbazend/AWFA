(function( $ ) {

  $.widget( "ui.mpft", {

  	options: {
      autoSummary: true
  	},

  	_create: function() {
  	  
  	  this.properties = {};
  	  this.dialogs = {};
  	  
  	  var self = this;
  	  
      var fmd = this.options.field.metadata({ type: 'class' });
      
      this.name = fmd.name;
      this.ui_options = fmd.ui;
      this.model_id = fmd.model_id;
      
  	  if (this.onfirstexpand) {
      
        if (this.expanded()) {
          this.onfirstexpand();
          self.bind_change();
        } else {
  	      this.options.set_item.one("mp_set_itemexpand", function() { self.onfirstexpand.apply(self); self.bind_change.apply(self); });
        }
        
	    }

  	  if (this.ontoggleexpand) {
        this.options.set.bind("mp_set_toggleexpand", function() { self.ontoggleexpand.apply(self); } );
	    }
	    
	    if (this.onexpand) {

        if (this.expanded()) {
          this.onexpand();
        }
  	    
  	    this.options.set_item.bind("mp_set_itemexpand", function() { self.onexpand.apply(self); } );
      
      }
      
      
      
  	},
    
    setup_dialog: function(key, el, options) {
      var self = this;
      var title = el.data("title");
      
      var opts = $.extend(true, { closeOnEnter: true, closeOnEscape: true, width: 560, resizable: false, modal: true }, options, { dialogClass: "aristo wp-dialog", autoOpen: false, title: title });
      
      opts.close = function() {
        self.element.find(".dialogs").append(el);
        
        if (options.close) {
          options.close;
        }
        
      };
      
      if (opts.closeOnEnter) {

        el.find("input.text").bind("keypress", function(event) {
          if (event.which == $.ui.keyCode.ENTER) {
            el.dialog("close");
            return false;
          }
        })

      }
      
      self.dialogs[key] = el.dialog(opts); 
      self.element.find(".dialogs").append(self.dialogs[key]);
    },
    
    clear: function() {
      
    },
    
    hasAutoSummary: function() {
      return this.options.autoSummary;
    },

    show_dialog: function(key) {
      var d = this.dialogs[key];
      
      if (d) {
        d.dialog("widget").find(".ui-dialog-titlebar").after(d);
        d.dialog("open");
      }
    },
    
    focus: function() {
      return false;
    },
    
    can_focus: function() {
      return true;
    },
    
    set_value: function(value) {
      // do nothing by default, this is up to the extended class to implement
    },
    
    bind_change: function() {
      
      var self = this;
      
      this.element.find("input[type=text],textarea").live("keydown", function(event) {
				if (!$(this).attr("readonly")) {
        	self.set_change();
				}
      });
      
      this.element.find("select").bind("change", function(event) {
        self.set_change();
      });

      this.element.find("input[type=checkbox],input[type=radio]").bind("click", function(event) {
        self.set_change();
      });

    },
    
    cache: function(items) {
      if (items) {
        $.extend(true, this.ui, items); 
      }
    },
    
    focus_field: function() {
      var self = this;
      if (self.options.field) {
        self.options.field.mp_field("focus");
      }
    },
    
    blur: function() {
      
    },
    
    label_text: function() {
      return this.options.field_item.find(".mp-field-label").data("label");
    },
    
    start_busy: function() {
      
      var si = this.summary_item();
      
      if (si && si.length) {
        si.addClass("busy");
      }
    },

    end_busy: function() {
      var si = this.summary_item();
      
      if (si && si.length) {
        si.removeClass("busy");
        
        this.update_summary();
      }
    },
    
    lang: function(key) {
      if (!this.md_lang) {
        this.md_lang = this.options.field.metadata({ type: 'class' }).lang || {};
      }

      if (!this.ui_lang) {
        this.ui_lang = $.mpft_lang[this.options.field.metadata({ type: 'class' }).type] || {};
      }
      
      return this.ui_lang[key] || this.md_lang[key];
    },
    
    prop: function(key, val) {
      if (!this.properties[key]) {
        this.properties[key] = this.element.find("input.prop-" + key);
      }
      
      if (val != null) {
        this.properties[key].val(val);
      }
      
      return this.properties[key];
    },
    
    prop_val: function(key) {
      var el = this.prop(key);
      
      if (el && el.length) {
        return this.prop(key).val();
      }
    
      return "";
    },
    
    method: function(method, data, callback, post) {
      
      if (post) {
        return $.mp.postToAction('mpft.dispatch', $.extend(true, {}, data, { type: this.field_type, type_method: method }), callback);
      } else {
        return $.mp.action('mpft.dispatch', $.extend(true, {}, data, { type: this.field_type, type_method: method }), callback);
      }
    },
    
    ajax: function(method, data, callback) {
      return this.method(method, data, callback);
    },
    
    summary_item: function() {
      return this.options.field.mp_field("summary_item");
    },
    
    update_summary: function() {
      this.options.field.mp_field("updateSummary");
    },
    
    summary: function() {
      return '';
    },
    
    is_multiple: function() {
      return this.options.set.hasClass("multiple");
    },

    change: function() {
			//console.log(this.value());
		},

    set_change: function() {

    	this.change();

  		if (this.options.set) {
        this.options.set.mp_set("change");
      }
    },
    
    expand: function() {
      if (this.options.set_item) {
        return this.options.set_item.mp_set_item("expand");
      }
    },

    collapse: function() {
      if (this.options.set_item) {
        return this.options.set_item.mp_set_item("collapse");
      }
    },
    
    expanded: function() {
      if (this.options.set_item) {
        return this.options.set_item.mp_set_item("expanded");
      }
    },
    
    is_empty: function() {
      return true;
    },
    
    destroy: function() {
      
  		$.Widget.prototype.destroy.call( this );
  	}

  });

  
  
  

  // some base classes to make development easier
  
  
  $.widget( "ui.mpft_select", $.ui.mpft, {

    cache_ui: function() {
      this.ui = this.ui || {
        select: this.element.find("select")
      };
    },
    
    onfirstexpand: function() {
      var self = this;

      this.cache_ui();
      this.init_select2();
    },
    
    focus: function() {
      this.ui.select.mp_select2('focus');
      return true;
    },
    
    value: function() {
      
      this.cache_ui();

      if (this.is_basic()) {
        return this.ui.select.val();
      } else {
        this.init_select2();
        return this.ui.select.mp_select2("val");
      }
    },
    
    is_basic: function() {
      return this.ui_options.basic;
    },
    
    set_value: function(value) {

      this.cache_ui();

      
      if (this.is_basic()) {
        this.ui.select.val(value);
      } else {
        this.init_select2();
        this.ui.select.mp_select2("val", value);
      }
    
    },
    
    init_select2: function() {
      
      var self = this;
      
      if (!this.is_basic() && !this.select2_inited) {
        var opts = { refresh: true };
      
        if (this.ui_options.multi_layout == "block") {
          opts.layout = "block";
        }

        if (parseInt(this.ui_options.results_input_length)) {
          opts.minimumInputLength = parseInt(this.ui_options.results_input_length);
        }

        this.select2_inited = true;
        
        this.ui.select.addClass("select2-source");

				if (this.ui.select.is(":disabled")) {
					opts.disabled = true;
        }
        
				this.ui.select.mp_select2(opts);
        
				
        
      }
      
    },
    
    is_empty: function() {
      var val = this.value();
      return $.trim(val) == "";
    },
    
    summary: function() {

      var labels = [];
      
      if (this.is_basic()) {

        var $selected = this.ui.select.find("option:selected");
      
        $selected.each( function() {
          labels.push($(this).html());
        });
      
      } else {

        var labels = this.ui.select.mp_select2("labels");

      }
      
      return labels.join(", ");

    }
    
  });
  
  
})(jQuery);