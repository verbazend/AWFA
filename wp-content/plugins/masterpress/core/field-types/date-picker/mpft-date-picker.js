
(function($) {

  var field_type = "date_picker";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

  	options: {
      
  	},

    cache_ui: function() {
      
      if (!this.ui) {
        this.ui = {
          value: this.element.find("input.value"),
          picker: this.element.find(".picker"),
          picker_start: this.element.find(".picker-start"),
          picker_end: this.element.find(".picker-end"),
          summary: this.element.find(".summary"),
          summary_cal_start: this.element.find(".mp-cal").first()
        };
      
        this.ui.summary_day = this.ui.summary.find(".day");
        this.ui.summary_month = this.ui.summary.find(".month");
        this.ui.summary_year = this.ui.summary.find(".year");

        if (this.ui_options.mode == "start_end") {
          this.ui.summary_cal_end = this.element.find(".cal-end");
          this.ui.summary_day_end = this.ui.summary.find(".day-end");
          this.ui.summary_month_end = this.ui.summary.find(".month-end");
          this.ui.summary_year_end = this.ui.summary.find(".year-end");
        }
      }

    },
    
    is_readonly: function() {
      return (this.ui.value.attr("readonly") == "readonly"); 
    },
    
    onfirstexpand: function() {

      var self = this;
      
      this.cache_ui();
      
      // may need to store the min date as a property AT THE TIME THE RECORD WAS CREATED

      var options = {
		    timeFormat: 'hh:mm TT',
		    ampm: true,
        dateFormat: "dd M yy",
        numberOfMonths: 1,
        stepHour: 1,
        stepMinute: 1,
        showOtherMonths: true,
        showButtonPanel: true,
      };
      
      if (this.ui_options.mindate != "") {
        options.minDate = this.offsetString(this.ui_options.mindate);
      }
      
      if (this.ui_options.maxdate != "") {
        options.maxDate = this.offsetString(this.ui_options.maxdate);
      }

      if (this.ui_options.mode == "start_end") {
        options.onSelect = function(dateText) {
          
          self.set_change();

          self.ui.summary_cal_start.hide();
          self.ui.summary_cal_end.hide();
          
          var vals = [];
          
          var start = self.ui.picker_start.datepicker('getDate');
          
          if (start) {
            vals.push($.datepicker.formatDate("dd M yy", start)); 
            self.ui.summary_cal_start.show();
            self.ui.summary_day.html($.datepicker.formatDate("d", start));
            self.ui.summary_month.html($.datepicker.formatDate("M", start));
            self.ui.summary_year.html($.datepicker.formatDate("y", start));
          }

          var end = self.ui.picker_end.datepicker('getDate');
          
          if (end) {
            self.ui.summary_cal_end.show();
            self.ui.summary_day_end.html($.datepicker.formatDate("d", end));
            self.ui.summary_month_end.html($.datepicker.formatDate("M", end));
            self.ui.summary_year_end.html($.datepicker.formatDate("y", end));
            vals.push($.datepicker.formatDate("dd M yy", end)); 
          }
          
          self.ui.value.val(vals.join(" - ")); 

        };
      } else {
       
        options.onSelect = function(dateText) {
          self.set_change();
          self.update_summary(self.ui.picker.datepicker('getDate'));
        };
        
        this.datepickerChange = function() {
          self.update_summary(self.ui.picker.datepicker('getDate'));
        };
        
        this.ui.picker.bind("change", this.datepickerChange);
        
         
      }
      
      if (!this.is_readonly()) {
        
        if (this.ui_options.timeselect) {
          this.ui.picker.datetimepicker(options);
        } else {
          this.ui.picker.datepicker(options);
        }
        
      }
      
      this.ui.picker.datepicker('widget').addClass("wp-dialog aristo");
      
      
    },
    
    update_summary: function(date) {
      var self = this;
      self.ui.summary_day.html($.datepicker.formatDate("d", date));
      self.ui.summary_month.html($.datepicker.formatDate("M", date));
      self.ui.summary_year.html($.datepicker.formatDate("yy", date));
    },
    
    is_empty: function() {
      return $.trim(this.ui.value.val()) == "";
    },
    
    summary: function() {
      return this.ui.summary.clone();
    },
    
    value: function() {
      return this.element.find("input.value").val();
    },
    
    set_value: function(value) {
      this.cache_ui();
      
      if (this.expanded()) {
        this.ui.picker.datepicker("setDate", value);
      } else {
        this.element.find("input.value").val(value);
        var date = Date.parse(value);
        this.update_summary(date);
      }
    },
    
    offsetNumeric: function(offset) {
      var date = new Date();
      date.setDate(date.getDate() + offset);
      return date;
    },

    offsetString: function(offset) {

    if (offset) {

      try {
      	return $.datepicker.parseDate(this.ui_options.dateFormat, offset); //, $.datepicker._getFormatConfig(inst));
      }
      catch (e) {
      	// Ignore
      }

      var date = (offset.toLowerCase().match(/^c/) ?
      	$.datepicker._getDate(inst) : null) || new Date();
      var year = date.getFullYear();
      var month = date.getMonth();
      var day = date.getDate();
      var pattern = /([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g;
      var matches = pattern.exec(offset);
      while (matches) {
      	switch (matches[2] || 'd') {
      		case 'd' : case 'D' :
      			day += parseInt(matches[1],10); break;
      		case 'w' : case 'W' :
      			day += parseInt(matches[1],10) * 7; break;
      		case 'm' : case 'M' :
      			month += parseInt(matches[1],10);
      			day = Math.min(day, $.datepicker._getDaysInMonth(year, month));
      			break;
      		case 'y': case 'Y' :
      			year += parseInt(matches[1],10);
      			day = Math.min(day, $.datepicker._getDaysInMonth(year, month));
      			break;
      	}
      	matches = pattern.exec(offset);
      }
      return new Date(year, month, day);
      }
    
    }
    

  });

})(jQuery);