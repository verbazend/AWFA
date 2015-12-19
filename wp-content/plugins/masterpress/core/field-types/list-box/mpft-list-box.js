
(function($) {

  var field_type = "list_box";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  $.widget( "ui.mpft_" + field_type, $.ui.mpft_select, { field_type: field_type } );

})(jQuery);