
(function($) {

  // the type variable below must be set to the underscored version of the field type key, prefixed by "mpft_"
  // e.g. if you have a field type with key 'my-editor', the type variable below would be "mpft_my_editor"
  
  var field_type = "drop_down_list";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  $.widget( "ui.mpft_" + field_type, $.ui.mpft_select, { field_type: field_type } );


})(jQuery);