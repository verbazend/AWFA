// closure to avoid namespace collision


(function(){
    // creates the plugin

  tinymce.create('tinymce.plugins.masterpress.media', {
		mceTout : 0,

		init : function(ed, url) {
		  
      // Add Media buttons
			ed.addButton('mp_media', {
				title : 'wordpress.add_media',
				onclick : function() {
					
					var $ed = jQuery(ed);
					var workflow = $ed.data("workflow");
					
					if (!workflow) {
						$ed.data("workflow", wp.media.editor.add(ed.id));
						workflow = $ed.data("workflow");
					}

					workflow.open();

				}
			});
		
		}
 
 	});

  // Registers plugin
  tinymce.PluginManager.add('mp_media', tinymce.plugins.masterpress.media);
    
})()