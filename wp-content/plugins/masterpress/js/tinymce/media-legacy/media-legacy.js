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
					tb_show('', mp_wp_upload_url);
					tinymce.DOM.setStyle( ['TB_overlay','TB_window','TB_load'], 'z-index', '999999' );
				}
			});
		
		}
 
 	});

  // Registers plugin
  tinymce.PluginManager.add('mp_media', tinymce.plugins.masterpress.media);
    
})()