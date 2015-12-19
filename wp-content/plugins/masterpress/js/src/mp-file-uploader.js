
(function($) { 

  var defaults = {
    sizeLimit: 0,   
    minSizeLimit: 0,                             
    allowedExtensions: [], // empty will allow ALL extensions (generally dangerous!)
    multiple: false,
    ids: {},
    classes: {},
    inputName: "file",
    selInput: "input",
    selUploader: ".uploader-ui",
    lang: { 
      buttonChoose: 'Choose From Computer&hellip;', 
      buttonReplace: 'Replace From Computer&hellip;' 
    }
  };
  
  var pn = 'mp_file_uploader';

  $.fn[pn] = function() {
    
    var ret = this, all = this, cmd, options = {}, params = {}, a = arguments; if (a.length >= 1) { if (typeof(a[0]) == "string") { cmd = a[0]; } else { options = a[0]; } if (a.length >= 2) { params = a[1]; } }
    
    if (this.length) {
      
      this.each( function() {

        var self, trigger, d, p = params, o = {}, md = {}, el = $(this); if ($.fn.metadata) { md = el.metadata({ type: 'class' }); } if (!cmd) { d = el.data(pn); if (!d) { d = {}; el.data(pn, d); } $.extend(true, o, defaults, options, md[pn] || md || {} ); d.options = o; } else { d = el.data(pn); if (d) { o = d.options; } else { return; } } self = function() { el[pn].apply(el, arguments); }; trigger = function(n, dt) { return el.trigger(jQuery.Event(n + "." + pn), $.isArray(dt) ? dt : [ dt ] ) !== false; };
   
        var formatSize = function(bytes) {

          var i = -1;                                    

          do {
              bytes = bytes / 1024;
              i++;  
          } while (bytes > 99);
        
          return Math.max(bytes, 0.1).toFixed(1) + ['kB', 'MB', 'GB', 'TB', 'PB', 'EB'][i];          

        };
      
        if (!cmd) {
          // initialization (no command)

          d.input =  el.find(o.selInput);
          
          if (d.input.length) {
            
            // this is the name of the hidden input that satisfies the browser requirement of having an input type="file" control
            // the input is hidden, and moved over the button as the use mouses over the button, as described by Shaun Inman here:
            // http://www.shauninman.com/archive/2007/09/10/styling_file_inputs_with_css_and_the_dom
            
            d.inputName = o.inputName;
            
            d.dir = o.params.dir || o.dir || '';
            d.baseURL = o.base_url;
            d.uploader = el.find(o.selUploader);
            d.file = o.file;

            if (!o.params && o.dir) {
              o.params = { dir: o.dir };
            }
            
            if (d.uploader.length) {

			        var params = $.extend(true, {}, o.params, { nonce: mp_nonce, source_action: mp_action, controller: "files", method: "upload_field" }, $.mp.context() );
			        
			        if (o.method) {
			          params.method = o.method;
		          }
		          
			        
			        var allowedExtensions = [];

              if (o.allowedExtensions) {
                if (!$.isArray(o.allowedExtensions)) {
                  allowedExtensions = o.allowedExtensions.split(",");
                } else {
                  allowedExtensions = o.allowedExtensions;
                }
              } 
              
              params.object_id = $.mp.object_id;
              params.object_type = $.mp.object_type;
              params.object_type_name = $.mp.object_type_name;
              params.model_id = o.model_id;
              
              var qqOptions = {
                sizeLimit: o.sizeLimit,
                allowedExtensions: allowedExtensions,
                inputName: d.inputName,
                element: d.uploader.get(0),
                action: mp_ajax_url,
                ids: o.ids,
                params: params,
                
                onSubmit: function(id, fileName){
                  trigger("submit", { id: id, fileName: fileName });
                },
                
                onProgress: function(id, fileName, loaded, total){
                  
                  var data = { fileName: fileName, loadedBytes: loaded, totalBytes: total, total: formatSize(total), loaded: formatSize(loaded), percent: Math.round( ( loaded / total ) * 100 ) };
                  trigger("progress", data);
                         
                },
    
                onComplete: function(id, fileName, responseJSON) {

                  // find the file input that qq-uploader places inside the button
                  $fi = d.button.find("input");

                  // move that file input outside the button
                  //$fi.appendTo(el);
                  d.buttonLabel.html(o.lang.buttonReplace);


                  var data = { input: d.input, baseURL: d.baseURL, dir: responseJSON.dir, sourceFileName: fileName, response: responseJSON };

                  if (responseJSON.filename) {
                    data.destinationFileName = responseJSON.filename;

                    if (!o.manualSet) {
                      // set the input value
                      d.input.val(responseJSON.filename);
                    }
                  
                  }

                  if (responseJSON.success) {
                    data.url = data.baseURL + data.dir.replace(/\\/g, "/") + data.destinationFileName;
                  }

                  trigger("complete", data);

                }
      
              };
              
              qqOptions.template = 
                  '<div class="qq-uploader">' + 
                  '<div class="qq-upload-button"><span>' + o.lang.buttonChoose + '</span></div>' +
                  '<ul class="qq-upload-list"></ul>' + 
                  '</div>';
              
              d.uploader = new qq.FileUploader(qqOptions);
              d.button = el.find(".qq-upload-button");
              d.buttonLabel = d.button.find("span");
            }
          }
          
        }
        else if (cmd == 'clear') {
          d.input.val('');
          d.buttonLabel.html(o.lang.buttonChoose);
        }
        else if (cmd == 'destroy') {
          // remove event handlers, data, and anything else
          el.data(pn, null);
        }

      });
    }

    return ret;
  };
  
  $.fn[pn].defaults = defaults;
  
})(jQuery);