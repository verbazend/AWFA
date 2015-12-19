(function($) { // closure and $ portability

  var reenablePublish = function() {
    $('#ajax-loading').hide();
    $('#draft-ajax-loading').hide();
    $('#publish').removeClass("button-primary-disabled");
  };
  
  $(document).ready( function() {
    
    // setup the set UI widgets, responsible for all of the behaviour of the MasterPress Custom Metabox UI
    
    var $sets = $('.postbox .inside > .mp-set').mp_set();
    
    
    $('#major-publishing-actions').after( $('#invalid-fields-publish-message-template').html() ); 
    $('#major-publishing-actions').after( $('#title-publish-message-template').html() ); 
    
    $('#post').submit( function() {
      $('#title-publish-message').hide();

      if (!$('#title').length) {
        return true;
      }
      
      if (!$.trim($("#title").val())) {
        $('#title-publish-message').show();
        $('#title').addClass("error");
        
        $('#publishing-action .spinner').hide();
        
        reenablePublish();
        return false;
      }

      return true;
    });
    
      
    if ($('#mp-attributes').length && $('#pageparentdiv').length) {
      $('#pageparentdiv').hide().find("input,select").attr("disabled", "disabled");
      $('#adv-settings').find('label[for=pageparentdiv-hide]').hide();
    }
    
    // setup template switching to load in any additional sets / hide non-applicable sets via AJAX
      
    $('#mp-attributes select[name=page_template]').change( function() {
      
      var postbox_template = Handlebars.compile($('#mp-postbox-template').html());
      var $meta_wrap = $('#normal-sortables');
      
      var template = $(this).val();

      var set_ids = $(this).find("option:selected").data("sets").toString();
      
      var sets = [];
      
      if ($.trim(set_ids) != "") {
        sets = set_ids.split(",");
      }
    
      var supports = $(this).find("option:selected").data("supports").toString().split(",");
      
      // build a list of panels in this list that AREN'T currently available (these will be the sets to load) 
      
      var sets_to_load = [];
      
      $.each( sets, function(index, val) {
        $set = $('.mp-set-' + val);
        
        if (!$set.length) {
          sets_to_load.push(val);
        } else {
          // if the set is present but isn't visible, show it (it may have been hidden by a previous template change)
          $set.closest(".postbox").show();
        }
      });
      
      // hide panels that are currently available that AREN'T available in the newly selected template

      var sets_to_hide = [];
      
      $('.mp-set').each( function() {

        var matches = $(this).attr("class").match(/mp-set-([0-9]+)/);
        
        if (matches.length) {
          var set_id = matches[1];
          
          if (!($.inArray(set_id, sets) != -1)) {
            $(this).closest(".postbox").hide();
          }
        }
        
      });
      
      if (sets_to_load.length) {
        $('#mp-templates-loading-fields').show();

        $.mp.action("post.get_sets", { template: template, set_ids: sets_to_load, object_id: $.mp.object_id }, function(response) {
          
          $('#mp-templates-loading-fields').hide();
          
          if (response.success) {
            
            
            $.each( response.sets, function(index, set) {
              
              $('body').append(set.templates);
              
              var $set = $(postbox_template(set));
              $set.css("background", "#FFF");
              $set.find(".inside").css({padding: 0, margin: 0}); 
              
                $meta_wrap.prepend( $set );
              
              $set.find(".mp-set").mp_set();
            });
            
          }
        
        });
      
      }
      
    });
    
    
    
    $('.mp-go').click( function() {
      
      var go = true;
      
      if ($sets.filter(".is-dirty").length) {
        go = confirm(jQuery.mp.lang['confirm_go']);  
      }
      
      return go;

    });
    
    
    $('#addtag #submit').click( function() {
      
      // allow validation to run.

      // WordPress needs more EVENT triggering :(

      setTimeout( function() { 
        
        if ( ! $('#addtag .form-invalid').length ) {
          
          $('#addtag .mp-set').each( function() {
            
            var $set = $(this);
            
            $set.mp_set("lazyload");
            
            if ($set.hasClass("multiple")) {
              $set.find(".mp-set-item").remove();
            } else {
              $set.find(".mp-field").mp_field("clear");
            }
            
            $set.mp_set("updateCount").mp_set("updateIndexes").mp_set("updateCheckedControls").mp_set("updateControls");
            
            $(".save-nag").hide();
            
          });
          
        } 
        
      }, 100);
      
    });
    
    
  });

  /* -- Set (Essentially the whole meta box) -- */

  $.widget( "ui.mp_set", {
    
  	_create: function() {
      
      var self = this;
      
      this.multiple = this.element.hasClass("multiple");
      
      if (this.multiple) {
        this.items = this.element.find("> .mp-set-inner > .mp-set-items > .mp-set-item");
      } else {
        this.items = this.element.find("> .mp-set-inner > .mp-set-item");
      }
    
      this.meta = this.element.metadata({ type: 'class' });
      this.lang = this.meta.lang;
      this.name = this.meta.name;
      
      this.postbox = this.element.closest(".postbox");
      
      this.postbox.find("h3.hndle").click( function() {
        
        setTimeout(function() {
          
          if (!self.postbox.hasClass("closed")) {
            self.element.trigger("mp_set_toggleexpand");
          }
          
        }, 50);
        
      });
      
      this.preview = this.options.preview;
      
			this.readonly = this.element.hasClass("readonly");
      
      this.metabox = this.element.closest(".postbox");
      this.save_nag = this.element.find(".save-nag");
      
      this.save_nag.appendTo(this.metabox);
      
      this.itemOptions = {
        preview: this.preview,
        readonly: this.readonly,
        multiple: this.multiple,
        expand: function(evebt, data) {
          self.updateControls();
        },
        collapse: function(event, data) {
          self.updateControls();
        },
        remove: function(event, data) {
          self.onremove();
        },
        check: function(event, data) {
          self.updateCheckedControls();
        },
        uncheck: function(event, data) {
          self.updateCheckedControls();
        },
        lang: this.lang,
        set: this.element
      };
      
      if (self.options.addItemTemplate) {
        self.addItemTemplate = Handlebars.compile(self.options.addItemTemplate);
      }
      
      // initialise the set item widget on each item in this set (this will also be called when adding new items etc)
      
			
			if (this.preview) {
        
        this.items.mp_set_item(this.itemOptions);
        
      } else {
  			this.items.filter(".expanded").mp_set_item(this.itemOptions);

  			// new: lazy initialisation of the set items to substantially improve onload performance
			  
  			var $capture;
			
  			if (self.multiple) {
  				$capture = this.element.find(".mp-set-items");
  			} else {
  				$capture = this.element.find(".mp-set-item");
  			}
			
  			$capture.click( function(event) {
				
  				var $target = $(event.target);
  				var $summary = $target.closest(".mp-set-summary");
  				var $item = $target.closest(".mp-set-item");

  				if (!$item.data("loaded")) {
					
				
  					if ($summary.length) {

  						// init, and fire the summary click, which will also expand the item
  						$item.mp_set_item(self.itemOptions).mp_set_item('summary_click', event);
						
  					} else {
					
  						var $controls = $target.closest(".mp-controls");
					
  						if ($controls.length) {

  							if ($target.closest(".control-toggle").length) {

  								// init and expand
  								$item.mp_set_item(self.itemOptions).mp_set_item('expand');

  							} else if ($target.closest(".control-remove").length) {
							
  								var remove = true;
            
  	            	if (!event.shiftKey) {
  		              remove = confirm(self.lang.remove_confirm);
  		            }
            
  		            if (remove) {
  									$item.remove();
  									self.onremove();
  		            }
							
  							} else if ($target.closest(".control-check").length) {
								
  								var $checkbox;
								
  								if ($target.is("input")) {
  									$checkbox = $target;
  								} else {
  									$checkbox = $target.find("input");
									
  									if ($checkbox.is(":checked")) {
  										$checkbox.removeAttr("checked");
  									} else {
  										$checkbox.attr("checked", "checked");
  									}

  								}

  								if ($checkbox.is(":checked")) {
  									$item.addClass("checked");
  								} else {
  									$item.removeClass("checked");
  								}
								
  								self.updateCheckedControls();
							
  							} 
						
						
  						} 
					
  					}
				
  				}
			
				
  			});

			} 
			
      this.controls = {
        head: this.element.find("> .mp-set-inner > .mp-set-head"),
        foot: this.element.find("> .mp-set-inner > .mp-set-foot")
      };
      
      if (this.multiple) {
        this.controls.copyPaste = this.controls.foot.find(".copy-paste").add( this.controls.head.find(".copy-paste") );
        this.controls.versions = this.controls.foot.find(".versions").add( this.controls.head.find(".versions") );
      } else {
        this.controls.copyPaste = this.items.eq(0).find("> .mp-controls .copy-paste");
        this.controls.versions = this.items.eq(0).find("> .mp-controls .versions");
      }
      
      
      this.controls.expandAll = this.controls.head.find(".expand-all");
      this.controls.collapseAll = this.controls.head.find(".collapse-all");
      this.controls.addItem = this.controls.head.find(".add-set-item")
      this.controls.addItemFoot = this.controls.foot.find(".add-set-item");
      this.controls.removeChecked = this.controls.head.find(".remove-checked");
      
      this.controls.itemCount = this.controls.head.find(".set-item-count").add(this.controls.foot.find(".set-item-count"));

      this.controls.checkAllWrap = this.controls.head.find(".control-check-all");
      this.controls.checkAll = this.controls.head.find(".control-check-all input");

      this.controls.copyPaste.bind("click", function() {
        self.copyPaste();
      });

      this.controls.expandAll.bind( "click", function() {
        self.expand();
      });

      this.controls.collapseAll.bind( "click", function() {

          if (!$(this).hasClass("disabled")) {
            self.collapse();
          }
          
      });

      this.controls.versions.bind("click", function() {
        self.versions();
      });
      
      this.controls.checkAllWrap.bind("click", function() {
        if (!self.controls.checkAll.is(":checked")) {
          self.check();
        } else {
          self.uncheck();
        }
      });

      this.controls.checkAll.bind("click", function(event) {
        if (self.controls.checkAll.is(":checked")) {
          self.check();
        } else {
          self.uncheck();
        }
        
        event.stopPropagation();
      });
      
      
      this.controls.removeChecked.bind({
        click: function(event) {

          self.items.filter(".checked").addClass("to-remove");

          if (confirm($.mp.lang.confirm_remove_checked_items)) {
            self.removingChecked = true;
            self.items.filter(".checked").mp_set_item(self.itemOptions).mp_set_item("remove", true);
          
            setTimeout( 
              function() { 
                self.removingChecked = false; 
                self.updateIndexes();
                self.updateCount();
                self.updateCheckedControls();
                self.updateControls();
                
                if (!self.items.length) {
                  self.uncheck();
                }
              },
              10
            );
          
          }
        },
        mouseenter: function(event) {
          self.items.filter(".checked").addClass("to-remove");
        },
        mouseleave: function(event) {
          self.items.filter(".checked").removeClass("to-remove");
        }
        
      });
      
      this.controls.head.find(".add-set-item").bind({ 
        mouseover: function() {
          self.controls.head.addClass("hover");
        }
      });
        
      this.controls.addItem.bind( "click", function(event) {
        self.add(event, null, "top");
        event.stopPropagation();
      });

      this.controls.addItemFoot.bind( "click", function(event) {
        self.add(event);
        event.stopPropagation();
      });


      if (!this.preview && !this.readonly) {
      
        this.controls.foot.bind( "click", function(event) {
          if (!$(event.target).closest("a,button:not(.add-set-item),input").length) {
            self.add(event);
          }
        });


        this.controls.head.bind({
          click: function(event) {
            if (!$(event.target).closest(".control-check-all,a,button:not(.add-set-item),input").length) {
              self.add(event, null, "top");
            }
          },
          mouseover: function(event) {
            if (!$(event.target).closest(".control-check-all,button,input").length) {
              self.controls.head.addClass("hover");
            }
          },
          mouseout: function(event) {
            self.controls.head.removeClass("hover");
          }
        
        });
        
      }
      
      if (!this.preview && !this.readonly && this.multiple) {
        this.element.sortable({ 
          items: ".mp-set-item", 
          axis: "y",
          handle: ".set-index", 
          distance: 10,
          tolerance: "pointer",
          revert: 40,
          stop: function() {
            self.updateIndexes();
          }
        });
      }
      
      
      
  	},
    
		lazyload: function() {
			var self = this;
			
			if (!self.element.data("loaded")) {
				self.items.mp_set_item(self.itemOptions);
				self.element.data("loaded", true);
			}
			
		},

		onremove: function() {
			
			var self = this;
			
			if (!self.removingChecked) {
         self.updateIndexes();
         self.updateCount();
         self.updateCheckedControls();
         self.change();
      } 
		},
    
		saveNag: function() {
      if (!this.nagVisible) {
        
        var val = $('#publish').val();
        
        this.save_nag.find(".update").html(val);
        this.save_nag.fadeIn("fast");
        this.nagVisible = true;
      }
    },
    
    markDirty: function() {
      this.element.addClass("is-dirty");
      this.element.find(".dirty").removeAttr("disabled");
    },
    
    count: function() {
      return this.items.length;
    },
    
    change: function() {
      if (!this.preview && !this.readonly) {
        this.saveNag();
        this.markDirty();
      }
    },
    
    reinit: function() {

    },
    
    is_preview: function() {
      return this.preview;  
    },
    
    add: function(event, data, location) {
      var self = this;
      
      if (!self.addItemTemplate) {
        
        var name = self.meta.name || self.element.data("name");
        
        var id = "mpft_ui_" + name;
        
        if (self.element.data("add_item_template")) {
          id = self.element.data("add_item_template");
        }
        
        var $tmpl = $("#" + id);
        
        if ($tmpl.length) {
          var html = $tmpl.html().replace(/!!(.+?)!!/gi, "{{$1}}");
          
          self.addItemTemplate = Handlebars.compile(html);
        } else {
          throw("A template could not be found for this field set");
        }
      }

      if (self.addItemTemplate) {
        
        // build the HTML from the template, and insert before the foot
        
        var setIndex = self.items.length;
        
        self.items.find("> .set-index").each( function() {
          setIndex = Math.max(setIndex, parseInt( $(this).val() ));
        });
        
        //var setIndex = self.items.length + 1;
        var $newItem = $(self.addItemTemplate({ set_index: setIndex + 1}));

        var $setItems = self.element.find("> .mp-set-inner > .mp-set-items");
        
        if (location == "top") {
          $newItem.prependTo( $setItems );
        } else {
          $newItem.appendTo( $setItems );
        }
      
        $newItem
          .mp_set_item($.extend(true, {}, self.itemOptions, { data: data }));
        
        if (!self.preview) {  
          $newItem.mp_set_item('markAdded');
        }

        $newItem.find(".mp-tooltip-icon,.with-mptt").mp_tooltip();


        if (event && event.altKey) {
          // pressing alt/option will collapse all other items except this one
          self.items.mp_set_item('collapse');
        }

        self.items = self.items.add($newItem);
        
        self.updateCount();
        self.updateIndexes();
        
                
        if (event && !event.shiftKey) {
          // pressing shift will add new items collapsed, to allow multiple items to be added
          $newItem.mp_set_item('expand');
          $newItem.mp_set_item('focus');

        } 
          
        self.change();
      }
    },
    
    updateIndexes: function() {
      var self = this;
      // reinit the items, since the order has changed

      if (this.multiple) {
        self.items = this.element.find("> .mp-set-inner > .mp-set-items > .mp-set-item");
      } else {
        self.items = this.element.find("> .mp-set-inner > .mp-set-item");
      }

      self.items.each( function(index, item) {

        var $item = $(item);
        var cn = $item.attr("class");
  
        // find and replace the index class with the new position
        var newcn = cn.replace(/mp-set-item-\d/, 'mp-set-item-' + ( index + 1 ));
  
        $item.attr("class", newcn);

        // update the set index display
        $item.find(".set-index span").html(index + 1);

        var set_index = $item.find(".set-index:first").val();
        
        
        var name = $item.find(".set-order:first").attr("name");
        
        var new_name = name.replace(/\[\d+\]$/i, "[" + ( index + 1 ) + "]");
        // update the set index hidden field
        
        $item.find(".set-order").attr("name", new_name).val(set_index);
        
      });
    },
    
    updateCount: function() {

      if (!this.preview && !this.readonly) {
        
        if (this.items.length) {
          this.controls.head.show();
          this.controls.addItem.attr("title", this.lang.add_another);
          this.controls.foot.attr("title", this.lang.add_another);
          this.controls.head.attr("title", this.lang.add_another);
          this.controls.foot.find(".rt").hide();

        } else {
          this.controls.addItem.attr("title", this.lang.click_to_add);
          this.controls.foot.attr("title", this.lang.click_to_add);
          this.controls.head.attr("title", this.lang.click_to_add);
          this.controls.head.hide();
          this.controls.foot.find(".rt").show();
        }

      }
      
      this.controls.itemCount.html($.mp.itemCount(this.items.length, this.lang.item, this.lang.items, this.lang.no_items)); 
      
    },
    
    updateControls: function() {
      
      if (this.items.length == 0) {
        this.uncheck();
      } 

      if (this.items.length == this.items.filter(".collapsed").length) {
        this.controls.collapseAll.addClass("disabled").attr("disabled", "disabled");
      } else {
        this.controls.collapseAll.removeClass("disabled").removeAttr("disabled");
      }

      if (this.items.length == this.items.filter(".expanded").length) {
        this.controls.expandAll.addClass("disabled").attr("disabled", "disabled");
      } else {
        this.controls.expandAll.removeClass("disabled").removeAttr("disabled");
      }
      
    },

    updateCheckedControls: function() {
      var self = this;
      
      if (!self.items.filter(".checked").length) {
        self.disableCheckedControls();
      } else {
        self.enableCheckedControls();
      }
        
    },
    
    enableCheckedControls: function() {
      var self = this;
      self.controls.removeChecked.removeClass("disabled").removeAttr("disabled").attr("title", $.mp.lang.remove_checked_items);
    },

    disableCheckedControls: function() {
      var self = this;
      self.controls.removeChecked.addClass("disabled").attr("disabled", "disabled").removeAttr("title");
    },
    
    check: function() {
			this.lazyload();
      var self = this;
      self.controls.checkAll.attr("checked", "checked");
      self.items.mp_set_item("check");

      if (self.items.length > 0) {
        self.enableCheckedControls();
      }

      self.updateCount();
    },

    uncheck: function() {
			this.lazyload();
      var self = this;
      self.controls.checkAll.removeAttr("checked");
      self.items.mp_set_item("uncheck");
      self.disableCheckedControls();
      self.updateCount();
    },
        
    collapse: function() {
			this.lazyload();
      this.items.mp_set_item("collapse");
      this.controls.expandAll.focus();
      this.updateControls();
      this._trigger("collapse");
    },
    
    expand: function() {
			this.lazyload();
      this.items.mp_set_item("expand");
      this.controls.collapseAll.focus();
      this.updateControls();
      this._trigger("expand");
    },
    
    value: function(value, replace, allowBlanks) {
      
      var self = this;
      
      if (value == null) {
        
        if (this.multiple) {
          var rep = [];
          
          this.items.each( function(index, item) {
            rep.push($(item).mp_set_item("value"));
          });

          return rep;

        } else {
          return this.items.eq(0).mp_set_item("value");
        }
        
      } else {
        
          if ($.isArray(value)) {
          
            if (self.multiple) {
            
              if (replace) {
                // delete all existing items
                this.items.mp_set_item("remove");
              } 
            
              $.each( value, function(index, obj) {
                self.add(false, obj);
              });
            
            } else {
              this.items.eq(0).mp_set_item("value", value[0], allowBlanks);
            }
          
          } else if (typeof(value) == "object") {
            this.items.eq(0).mp_set_item("value", value, allowBlanks);
          }
        
      }
      
    },
    
    to_json: function(spaces) {
      if (!spaces) {
        spaces = 2;
      }
      
      var val = this.value();
      
      return JSON.stringify(val, false, spaces);
    },

    fetch_version: function() {
      
      var self = this;

      this.versions = this.versions || [];

      var id = this.versionsSelect.val();
      
      if (self.activeVersion != id) {
        
        if (!this.versions[id]) {
      
          self.versionsLoading.show();

          var args = { id: id };
        
          if (!self.previewTemplate) {
            args.model_id = self.element.data("model_id");
            args.fetch_template = true;
          }
        
          $.mp.action( "meta.get_version", args, function(response) {

            if (response.success) {
            
              // now create a set and set the data
             
              if (response.template) {
                self.previewTemplate = response.template;
              }
            
              self.versions[id] = response.value;

            }
        
            self.versionsLoading.hide();
            self.activeVersion = id;
            self.show_preview(self.versions[id]);
          });
        
        } else {
          self.show_preview(self.versions[id]);
          self.activeVersion = id;
        }
      
      }

      
    },
    
    show_preview: function(value) {
      var self = this;
      
      var multiple = self.multiple;
      
      if (self.previewTemplate) {
        // remove the current preview 

        self.versionPreview.find(".mp-set").remove();
        
        if (multiple) {
          var $version = $(self.setMultipleTemplate);
        } else {
          var $version = $(self.setTemplate);
          // here we need to insert the preview template
          self.prevewTemplateHB = self.prevewTemplateHB || Handlebars.compile( self.previewTemplate );
          
          $version.find(".mp-set-inner").append( self.prevewTemplateHB({ set_index: 1 }) );

          if (value.length) {
            value = value[0];
          }
        }
      
        $version.data("name", self.meta.name);
        
        self.versionRestore.hide();  

        self.versionPreview.append($version);
        
        $version
          .mp_set({ preview: true, addItemTemplate: self.previewTemplate });
        
        $version
          .mp_set("value", value);

        if (value.length || !multiple) {  
          self.versionContent.show();
          self.versionNoData.hide();

        } else {
          self.versionContent.hide();
          self.versionNoData.show();
        }
        
        $versionCheckboxes = $version.find("input.checkbox");

        function toggleRestore() {
          if ($versionCheckboxes.filter(":checked").length) {
            self.versionRestore.show();  
          } else {
            self.versionRestore.hide();  
          }
        };
        
        if (multiple) {

            self.versionPreview.find(".control-check,.control-check-all,input.checkbox").bind("click", function() {
              toggleRestore();
            });
        
            self.versionRestore.unbind("click").click( function() {

              var values = [];

              $version.find(".mp-set-item").each( function( index, set_item ) {

                var $set_item = $(set_item);

                if ($set_item.find(".mp-controls input.checkbox").is(":checked")) {
                  values.push( $set_item.mp_set_item('value') );
                }

              });
              
              if (values.length) {
                self.value(values);
                self.change();
              }

              self.versionsPanel.dialog("close");
          
              return false;

            });
        
         
        
        } else {
          self.versionRestore.show();

          self.versionRestore.unbind("click").click( function() {
          
            if (confirm('Restore set: are you sure? Any current changes to this set will be lost.')) {
              self.value($version.find(".mp-set-item").mp_set_item("value"), false, true);
              self.versionsPanel.dialog("close");
            }

            return false;

          });

        }
          
          
          
      
      } else {
        alert("The preview could not be displayed");
      }
    },
    
    versions: function() {
      var self = this;
      self.lazyload();
      self.setTemplate = self.setTemplate || $('#mp-set-preview-template').html();
      self.setMultipleTemplate = self.setMultipleTemplate || $('#mp-set-preview-multiple-template').html();

      self.versionsPanel = self.versionsPanel || self.element.find(".mp-set-versions:first");
      self.versionsPanel.show();
      
      if (!self.versionsSelect) {
        self.versionsSelect = self.versionsSelect || self.versionsPanel.find("select");

        self.versionsSelect.mp_select2();
      
        self.versionsSelect.change( function() {
          self.fetch_version();
        });

        self.versionRestore = self.versionsPanel.find(".restore");
        self.versionContent = self.versionsPanel.find(".version-content");
        self.versionPreview = self.versionsPanel.find(".version-preview");
        self.versionNoData = self.versionsPanel.find(".version-no-data");
        self.versionsLoading = self.versionsPanel.find(".loading");
      }
      
      
      var opts = { 
        closeOnEscape: false, 
        width: "90%", 
        height: "auto",
        resizable: false, 
        modal: true,
        dialogClass: "aristo versions-dialog wp-dialog", 
        autoOpen: true, 
        title: self.versionsPanel.data("title")
      };
      
      // resize the version preview
      
      function resizePreview() {
        var ph = Math.max( $(window).height() - 360, 300 );
        
        self.versionPreview.css("height", ph);
        opts.height = ph + 214;
      };
      
      resizePreview();
      
      self.versionsPanel.dialog(opts);
      
      self.versionsSelect.mp_select2('focus');
      
      self.fetch_version();
        
      self.versionsWidget = self.versionsWidget || self.versionsPanel.dialog("widget")
      self.versionsWidget.affix({ w: "c" });
      

      return false;
      
    },
    
    copyPaste: function() {

			this.lazyload();

      var self = this;
      self.cpPanel = self.cpPanel || self.element.find(".mp-set-copy-paste:first");
      self.cpPanel.show();
      self.copyRep = self.copyRep || self.cpPanel.find("textarea").get(0);
      
      self.copyRep.value = this.to_json();
      
      self.copyPanel = self.copyPanel || self.cpPanel.find(".copy-content");
      self.pastePanel = self.pastePanel || self.cpPanel.find(".paste-content");
      
      var opts = { 
        closeOnEscape: true, 
        width: "80%", 
        height: "auto", 
        resizable: false, 
        modal: true,
        dialogClass: "aristo wp-dialog", 
        autoOpen: true, 
        title: self.cpPanel.data("title"),
        buttons: { 
          
          "OK" : function() { 
            
            // if paste
          
            if (self.pastePanel.hasClass("current")) {
            
              var val = $(self.pasteRep).val();
          
              var obj = $.parseJSON(val);
          
              try {
                if (obj) {
                  self.value(obj);
                }
              } catch (e) {
                console.log(e);
              }
        
            }
            
            $(this).dialog("close");
          
          }
        }
      };
      
      self.cpPanel.dialog(opts);
      self.cpPanel.dialog("widget").affix({ w: "c" });
      
      var setupCopy = function() {
        self.copyRep.focus()
        self.copyRep.select();
      };
      
      
      
      if (!self.cpTabs) {
        self.cpTabs = self.cpPanel.find(".fs-tabs");
        self.cpTabs.mp_tabs();
        
        self.cpTabs.bind("afterchange.mp_tabs", function(event, data) {
          if (data.panel.length && data.panel.hasClass("paste-content")) {
            setTimeout( function() { 
              
              if (!self.pasteRep) {
                self.pasteRep = self.pasteRep || data.panel.find("textarea").get(0);
              }
              
              self.pasteRep.focus();
              self.pasteRep.select();
              
            }, 100 );
          } else {
            setupCopy();
          }
          
        });
        
      }

      if (self.items.length) {
        self.cpTabs.mp_tabs("select", { index: 0 });
      } else {
        self.cpTabs.mp_tabs("select", { index: 1 });
      }

      
      return false;
    },
    
  	destroy: function() {
  		$.Widget.prototype.destroy.call( this );
  	}

  });
  



  /* ----- Set Item ----- */
  
  
  $.widget( "ui.mp_set_item", {
    
    
  	_create: function(data) {
      
      var self = this;

			if (!self.element.data("loaded")) {
				self.element.data("loaded", true);
			
	      this.preview = this.options.preview;
	      this.readonly = this.options.preview;
      
	      this.fields = self.element.find("> .mp-set-fields > .mp-field");
      
	      this.fieldOptions = {
	        set: this.options.set,
	        set_item: this.element,
	        beforefocus: function() {
	          self.fields.mp_field('blur');
	        }
	      };
      
	      var value = null;

	      var data = self.options.data || {};
      
	      // create the field 
	      self.fields.each( function() {
	        var value = null;
	        var name = $(this).metadata({ type: 'class' }).name;
          
	        if (data[name] && data[name].value) {
	          value = data[name].value;
	        }
        
	        $(this).mp_field($.extend(true, {}, self.fieldOptions, { value: value }));
        
	      });
      
	      self.required_fields = self.fields.filter(".required");

	      if (self.required_fields.length) {
        
	        self.element.closest("form").submit( $.proxy(self.formsubmit, this) );
	      }
      
	      this.controls = {
	        toggle: this.element.find(".toggle:first"),
	        removeItem: this.element.find(".remove-set-item:first"),
	        checkWrap: this.element.find(".control-check:first"),
	        check: this.element.find(".control-check:first input"),
	        summary: this.element.find(".mp-set-summary:first"),
	        fields: this.element.find(".mp-set-fields")
	      };

	      this.controls.fieldsCollapse = this.controls.fields.find(".collapse,.collapse-lower");
      
	      this.lang = this.options.lang;
      
	      this.controls.toggle.bind( "click", function(event) {
	        if ($(this).hasClass("expand")) {
	          self.expand();
	        } else {
	          self.collapse();
	        }
        
	      });

	      this.controls.fieldsCollapse.bind("click", function(event) {
	        self.collapse();
	        self.controls.toggle.focus();
      
	        if (!self.preview && self.element.hasClass("single")) {
	          $.scrollTo(self.element.closest(".postbox"), { duration: 0, offset: -50 });
	        }
    
	      });
        
	      if (this.options.multiple) {
         
	        this.controls.checkWrap.bind("click", function(event) {
	          if (!self.controls.check.is(":checked")) {
	            self.check();
	          } else {
	            self.uncheck();
	          }
	        });

	        this.controls.check.bind("click", function(event) {
	          if (self.controls.check.is(":checked")) {
	            self.check();
	          } else {
	            self.uncheck();
	          }
        
	          event.stopPropagation();
	        });
      
        
      
      
	        this.controls.removeItem.bind({
	          click: function(event) {
            
	            var remove = true;
            
	            if (!event.shiftKey) {
	              remove = confirm(self.lang.remove_confirm);
	            }
            
	            if (remove) {
	              self.remove();
	            }
	
							event.stopPropagation();
	          },
	          mouseenter: function() {
	            self.element.addClass("to-remove");
	          },
	          mouseleave: function() {
	            self.element.removeClass("to-remove");
	          }
	        });
      
	      }


	      this.controls.summary.bind({
	        click: function(event) {
		 				self.summary_click(event);
					},
	        mouseenter : function() {
	          if (self.element.hasClass("collapsed") && !self.element.hasClass("ui-sortable-helper")) {
	            self.controls.toggle.addClass("hover");
	          }
	        },
	        mouseleave: function() {
	          self.controls.toggle.removeClass("hover");
	        }
	      });
      } // if not loaded
  	},
    
		summary_click: function(event) {
			var self = this;
			self.expand();
      var fieldSummaryItem = $(event.target).closest(".mp-field-summary");
     
      if (fieldSummaryItem && fieldSummaryItem.length) {
        var fieldId = fieldSummaryItem.metadata({ type: 'class' }).mp_field_id;
     
        try {
       
          var $field = $('#' + fieldId);
       
          var offset = - ( $(window).height() / 2 ) + ( $field.innerHeight() / 2) ;
       
          if (!self.preview) {
            $.scrollTo($field, { duration: 200, easing: "easeOutQuart", offset: offset, onAfter: function() { $field.mp_field('focusControl'); } });
          }
       
        } catch (e) {
       
        }
     
      }
	
		},
    
		is_preview: function() {
      return self.element.closest(".mp_set").mp_set("is_preview");
    },
    
    formsubmit: function() {
      var self = this;
      
      self.required_fields = self.fields.filter(".required");

      $('#invalid-fields-publish-message').hide();

      var invalid_fields = $();
      var invalid_field_summaries = $();

      self.required_fields.each( function() {
        var field = $(this);

        var summary_item = field.mp_field('summary_item');

        if (!self.expanded()) {

          // check if the summary is marked empty which should reliably mean that the field hadn't been provided yet

          if (summary_item.hasClass("empty")) {
            invalid_field_summaries = invalid_field_summaries.add(summary_item);
            invalid_fields = invalid_fields.add(field);
          }

        } else {

          // check if the field is currently empty

          if (field.mp_field('isEmpty')) {
            invalid_field_summaries = invalid_field_summaries.add(summary_item);
            invalid_fields = invalid_fields.add(field);
          }

        }

      });

      invalid_fields.addClass("invalid");
      invalid_field_summaries.addClass("invalid");

      if (invalid_fields.length) {
        reenablePublish();

        // show the invalid message

        if ($('#invalid-fields-publish-message').length) {
          $('#invalid-fields-publish-message').show();
          $.scrollTo($('#invalid-fields-publish-message'), { offset: -50 });
        }
        
        return false;
      }
      
      
    },
    
    remove: function(instant) {
      var self = this;
      var $item = this.element;
      $item.hide();
      self.destroy();
      self.element.remove();
      self._trigger("remove");
    },
    
    check: function() {
      var self = this;
      self.controls.check.attr("checked", "checked");
      this._updateCheckedDisplay();
      self._trigger("check", null, { checkbox:  self.controls.check });
    },

    uncheck: function() {
      var self = this;
      self.controls.check.removeAttr("checked");
      this._updateCheckedDisplay();
      self._trigger("uncheck", null, { checkbox: self.controls.check });
    },

    _updateCheckedDisplay: function() {
      var self = this;
      if (self.controls.check.is(":checked")) {
        self.element.addClass("checked");
      } else {
        self.element.removeClass("checked");
      }
    },
    
    collapse: function() {
      
      if (this.expanded()) {
          
        this.element
          .closest(".mp-set-item")
            .removeClass("expanded").addClass("collapsed")
            .find(".toggle:first").removeClass("collapse").addClass("expand").end()
            .find(".mp-set-fields:first").hide().end()
            .find(".mp-set-summary:first").show();

        this._trigger("collapse");

        this.fields.mp_field("updateSummary");
      
        this.updateColor();

      }
      
    },

    markAdded: function() {
      this.element.addClass("added");
    },

    unmarkAdded: function() {
      this.element.removeClass("added");
    },
    
    focus: function() {
      this.fields.eq(0).mp_field('focusControl');
    },
    
    expand: function() {

      this.element
        .closest(".mp-set-item")
          .addClass("expanded").removeClass("collapsed")
          .find(".toggle:first").removeClass("expand").addClass("collapse").end()
          .find(".mp-set-summary:first").hide().end()
          .find(".mp-set-fields:first").show();

      this._trigger("expand");
      
    },
    
    expanded: function() {
      return this.element.hasClass("expanded");
    },
    
    value: function(value, allowBlanks) {
      
      if (value == null) {
        
        var rep = {};
      
        this.fields.each( function(index, item) {
        
          var name = $(item).data("name");
        
          if (name) {
            rep[name] = $(item).mp_field("value");
          }
      
        });
      
        return rep;
      
      } else {

        this.fields.each( function() {
          var name = $(this).metadata({ type: 'class' }).name;
          
          if ((value[name] && value[name].value) || allowBlanks) {
            $(this).mp_field("value", value[name].value);
          }

        });
            
      }
    
    
    },
    
    updateColor: function() {
      
      if (this.controls.summary.find(".mp-field-summary:last").hasClass("empty")) {
        this.controls.summary.addClass("last-empty");
      } else {
        this.controls.summary.removeClass("last-empty");
      }
      
    },
    
  	destroy: function() {
  	  
  	  var self = this;
  	  
  	  self.element.closest("form").unbind( "submit", this.formsubmit );
      $.Widget.prototype.destroy.call( this );
  	}

  });
  
    
  
  /* -- Field -- */

  $.widget( "ui.mp_field", {
    
  	options: {
  		value: 0
  	},

  	_create: function() {
      
      var self = this;
      
      self.element.data("loaded", true);
      
      this.controls = {
        ui: this.element.find(".mp-field-ui")
      };
      
      this.meta = this.element.metadata({ type: 'class' });
      
      this.lang = this.meta.lang;
      this.type = this.meta.type;
      this.type_widget = this.meta.type_widget;

      this.name = this.meta.name;
      
      this.set = this.options.set;
      this.set_item = this.options.set_item;
      
      try {
        this.controls.summary_item = $(this.meta.summary_id);
        
        if (this.controls.summary_item.length) {
          this.controls.value_summary = this.controls.summary_item.find(".value-summary");
          this.controls.empty_summary = this.controls.summary_item.find(".empty-summary");
        }

      } catch (e) {
        this.controls.summary_summary = $();
        this.controls.value_summary = $();
        this.controls.empty_summary = $();
      }
      
    
      if ($.fn[this.type_widget]) {
        this.mpft_widget = this.controls.ui[this.type_widget];
        this.mpft_widget.apply(this.controls.ui, [ { field: this.element, set: this.set, set_item: this.set_item } ]);
      }

      this.element.delegate("a,input,textarea,button,select", "focusout", function(event) { self.blur(); });
      this.element.delegate("a,input,textarea,button,select", "focusin", function(event) { self.focus(); });

      var value = this.options.value;
      
      if (value) {
        // must expand to setup the values
        // this.set_item.mp_set_item("expand");
        this.ui("set_value", value, true);
        
        if (this.ui("hasAutoSummary")) {
          
          this.updateSummary();
        }
        
      }
      
  	},

    value: function(value, simple) {
      if (value == null) {
        
        var val = this.ui("value");
        
        if (simple) {
          return val;
        }
        
        return { value: val };

      } else {
        
        try {
          this.ui("set_value", value);

          if (this.ui("hasAutoSummary")) {
            this.updateSummary();
          }

        } catch(e) {
          if (console.log) { console.log(e); }
        }
        
      }
    },
    
    summary_item: function() {
      return this.controls.summary_item;
    },
    
    summary: function() {
      
      try {
        var summary = this.ui("summary");
      } catch (e) {
        var err = "An error occured for the field type '" + this.type + "' in the 'summary' method: " + e;
        if (console && console.error) {
          console.error(err);
        }
      
      }
    
      
      if (!summary || summary == this) {
        return '';
      }
      
      if ($.type(summary) == "object") {
        return summary;
      } else {
        return $.mp.smartTrim(summary, 100 + ((this.summaryWidth() - 1) * 130));
      }
      
    
    },
    
    isEmpty: function() {
      
      try {
        var empty = this.ui("is_empty");
      } catch (e) {

        var err = "An error occured for the field type '" + this.type + "' in the 'is_empty' method: " + e;
        
        if (console && console.error) {
          console.error(err);
        }

        return true;
      }

      if (!empty) {
        this.controls.summary_item.removeClass("invalid");
        this.element.removeClass("invalid");
      }
      
      return empty;
    },
    
    clear: function() {
      this.ui("clear");
    },
    
    ui: function() {
      var self = this;

      if (self.mpft_widget) {
        return self.mpft_widget.apply(self.controls.ui, arguments);
      }
    },
        
    summaryWidth: function() {
      return this.meta.summary_width || 1;
    },
    
    updateSummary: function() {
      
      if (this.controls.summary_item.length) {

        if (this.isEmpty()) {  
          this.controls.summary_item.addClass("empty");
          this.controls.value_summary.hide();
          this.controls.empty_summary.show();
        } else {
          this.controls.summary_item.removeClass("empty");
          this.controls.empty_summary.hide();
          this.controls.value_summary.show();
        }
        
        this.controls.value_summary.empty().append(this.summary());


      }
    },
    
    focusControl: function() {

      if (this.ui('focus') === false) {
      
        var prioritySelector = "input[type=file],input.text:visible,input.search:visible,input.checkbox:visible,input.radio:visible,textarea:visible,select:visible";
        var selector = "button:visible,a:visible";
      
        var $p = this.element.find(prioritySelector);
      
        if ($p.length) {
          $p.first().focus();
        } else {
          $p = this.element.find(selector);
        
          if ($p.length) {
            $p.first().focus();
          } 
        }
      
      }
      
    },
    
    focus: function() {


      if (this._trigger("beforefocus")) {
        
        var can_focus = true;
        
        try {
          can_focus = this.ui("can_focus");
        } catch (e) {
          
        }
        
        if (can_focus) {
          this.element.addClass("active");
          this._trigger("afterfocus");
        }
      } 
      
    },
    
    blur: function() {
      
      if (this._trigger("beforeblur")) {
        this.element.removeClass("active");
        this._trigger("afterblur");
      } 

      
    }

  });

    
    
})(jQuery);