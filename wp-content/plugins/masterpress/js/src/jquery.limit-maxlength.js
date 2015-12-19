(function($) { // closure and $ portability

  
  $.fn.limitMaxlength = function(options){

  	var settings = $.extend({
  		maxlength: 100,
  		onLimit: function(){},
  		onEdit: function(){}
  	}, options);

  	var textarea = $(this);
    var maxlength = parseInt(options.maxlength);

    var val;
    
  	// Event handler to limit the textarea
  	var onEdit = function(){
  	
  	  val = textarea.val();
  	  
  		if(val.length > maxlength){
  			textarea.val(val.substr(0, maxlength));
  			// Call the onlimit handler within the scope of the textarea
  			$.proxy(settings.onLimit, this)();
  		}

  		// Call the onEdit handler within the scope of the textarea
  		$.proxy(settings.onEdit, this)(Math.max(0, maxlength - val.length));
  	};

    
  	this.each(onEdit);

  	return this.keydown(onEdit)
  				.focus(onEdit)
  				.live('input paste', onEdit);

  };

    
})(jQuery);

