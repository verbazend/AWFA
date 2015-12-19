
(function($) {

  var field_type = "map";
  
  // validity checks - do not remove
  if ($.fn["mpft_" + field_type]) { throw(sprintf($.mp.errors.duplicate_widget, field_type)); } else if (field_type == "type_key") { throw(sprintf($.mp.errors.incomplete_widget, field_type)); }

  
  $.widget( "ui.mpft_" + field_type, $.ui.mpft, {

    cache_ui: function() {
      
      var self = this;
      
      if (!self.ui) {
        
        self.ui = self.ui || {
          state: self.element.find(".state"),
          input: self.element.find("input.value"),
          canvas: self.element.find(".map-canvas"),
					lng: self.element.find("li.lng .value"),
					lat: self.element.find("li.lat .value"),
					search: self.element.find(".map-search input"),
					search_button: self.element.find(".map-search button"),
					zoom: self.element.find("li.zoom .value"),
          clear_button: self.element.find(".button.clear"),
          restore_button: self.element.find(".button.restore"),
          begin: self.element.find(".map-begin")
        };

        self.mapOptions = {
          center: new google.maps.LatLng(0,0),
          scrollwheel: false,
					zoom: 1,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        if (self.is_readonly()) {
          self.mapOptions.disableDoubleClickZoom = true;
          self.mapOptions.draggable = false;
          self.mapOptions.disableDefaultUI = true;
        }
        
        self.map = new google.maps.Map(self.ui.canvas.get(0), self.mapOptions);
       
        
        // setup events

        google.maps.event.addListener(self.map, 'drag', function() {
          self.set_value( self.map.getCenter().toUrlValue() );
					self.update_location();
					
					if (!self.initial) {
          	self.set_state("changed");
            self.set_change();
          }
        });

        google.maps.event.addListener(self.map, 'zoom_changed', function() {
          self.prop( "zoom", self.map.getZoom() );
					self.update_location();

          if (!self.initial) {
          	self.set_state("changed");
            self.set_change();
          }

        });
        

				self.ui.search.keypress( function(event) {
					
					if (event.keyCode == 13) {
						self.search();
		        return false;
			    }
					
				});
				
				self.ui.search_button.click( function(event) {
					self.search();
				});
        
				self.ui.restore_button.click( function() {
					self.restore();
					self.update_map();
					self.update_location();
					self.set_state("");
				});

				self.ui.clear_button.click( function() {
					self.clear();
					self.update_map();
					self.update_location();
				});
				
				self.ui.begin.click( function() {
					self.set_state("");
					
					var center = self.map.getCenter();
					var zoom = self.map.getZoom();
					self.set_value(center.toUrlValue());
					self.prop("zoom", zoom);
					self.update_location();
					return false;
				});
				
				$(window).resize( function() {
				  
				  var center = self.map.getCenter(); 
          google.maps.event.trigger(self.map, 'resize'); 
          self.map.setCenter(center);
                
				  //google.maps.event.trigger(self.map, 'resize');
          
				  
			  });
			  

      }
      
    },
    
    is_readonly: function() {
      return this.ui.canvas.data("readonly");
    },
    
		search: function() {
			var self = this;
			var term = self.ui.search.val();
			
			if ($.trim(term) == "") {
				alert(self.lang("enter_search_term"));
			} else {
				
				if (!self.geocoder) {
					self.geocoder = new google.maps.Geocoder();
				}
    		
				self.geocoder.geocode( { 'address': term }, function(results, status) {
		      if (status == google.maps.GeocoderStatus.OK) {
						
						self.map.fitBounds(results[0].geometry.viewport);
						
						self.clear_markers();
						
						self.overlays.push(
							new google.maps.Marker({
	            	map: self.map,
	            	position: results[0].geometry.location
	        		})						
						);
						
		      } else {
		        alert("Geocode was not successful for the following reason: " + status);
		      }
		    });
		
				
				self.set_state("changed");
			
			} 

		},

		clear_markers: function() {
			if (this.overlays) {
				while(this.overlays[0]){
			   	this.overlays.pop().setMap(null);
			  }
			}
			
			this.overlays = [];
		},
		
		set_state: function(state) {
			
			var self = this;
			
			if (state == "changed" && !self.original_values) {
				state = "changed-new";
			}
			
			this.ui.state.attr("class", state + " state");

		},

    set_value: function(value) {
      this.cache_ui();
      
      this.ui.input.val( value );
    },
    
    // updates the center of the map to the current value
    
    update_map: function(initial) {

      var self = this;
      
      if (initial) {
        self.initial = true;
      }

			var ll = this.lat_lng();
			
			if (ll) {
			  this.map.setCenter( new google.maps.LatLng( ll.lat, ll.lng ) );
      }
			
      
      var zoom = this.prop_val('zoom');
      
      if (zoom) {
        this.map.setZoom( parseInt(zoom) );
      }

      self.initial = false;
      
    }, 
     
		update_location: function() {
			this.ui.zoom.html(zoom);

			var ll = this.lat_lng();
			
			if (ll) {
				this.ui.lat.html(ll.lat.toFixed(4));
				this.ui.lng.html(ll.lng.toFixed(4));

	      var zoom = this.prop_val('zoom');
      
	      if (zoom) {
	        this.ui.zoom.html( zoom );
	      }

      } 
			
		},
    
		lat_lng: function() {
			var ll = this.ui.input.val().split(",");
      
      if (ll.length == 2) {
				return { lat: parseFloat(ll[0]), lng: parseFloat(ll[1]) };
      }
			
			return false;
		},
		
    place_marker: function(location) {
        
      var self = this;
      
      var marker = new google.maps.Marker({
          position: location,
          map: self.map
      });

    },
    
    onexpand: function() {
      this.update_map(true);
    },
    
    onfirstexpand: function() {
      var self = this;
      this.cache_ui();
			

			if (!self.is_empty()) {
				var ll = self.lat_lng();
				var zoom = self.prop_val("zoom");

				self.original_values = { lat: ll.lat, lng: ll.lng, zoom: zoom };
			}

			this.update_location();
    },

    focus: function() {
      var self = this;

    },
    
    is_empty: function() {
      this.cache_ui();
      return this.value() == "";
    },

		clear: function() {
			this.prop("zoom", "");
			this.set_value("");
			this.set_state("empty");
		},
		
		restore: function() {
			var ov = this.original_values;
			
			if (ov) {
				this.prop("zoom", ov.zoom);
				this.set_value(ov.lat + "," + ov.lng);
			}
		},
		
		focus: function() {
		  this.ui.search.focus();
	  },
	  
    value: function(entities) {
      var self = this;
      this.cache_ui();
      return this.ui.input.val();
    },
    
    summary: function() {
      
      var width = 118 * 2;
      var height = 68 * 2;
      
      var zoom = parseInt( this.prop_val('zoom') );
      
      if (zoom > 1) {
        zoom = zoom - 1;
      }
      
      var url = 'http://maps.googleapis.com/maps/api/staticmap?center=' + this.value() + '&zoom=' + zoom + '&size=' + width + 'x' + height + '&maptype=roadmap&sensor=false';
      
      var wrap = $('<div class="map">');
      wrap.append( $('<img src=' + url + ' />') );
      return wrap;
    }
    

  });

})(jQuery);