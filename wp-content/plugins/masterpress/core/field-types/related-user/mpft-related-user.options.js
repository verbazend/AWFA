(function($) {

  var field_type = 'related-user';
  
  var p = function(str) { return 'mpft-' + field_type + "-" + str; }, $p = function(id) { return $('#' + p(id)); };
  
  $p('options').each( function() {
    
    var ui = $(this);

    var $user_roles_wrap = $p('user-roles-wrap');

    ui.find(".select-all").click( function() {
      $user_roles_wrap.find("input[type=checkbox]").attr("checked", "checked");
    });

    ui.find(".select-none").click( function() {
      $user_roles_wrap.find("input[type=checkbox]").removeAttr("checked");
    });

    var control_style_single = $p("control_style_drop_down_list");
    var control_style_multi = $p("control_style_list_box_multiple");
    
    var basic = $p("basic");

    var ml_f = $p("multi_layout-f");
    
    var csw = $p('control-selections-wrap');

    var $rg = ui.find(".grid-row-item-prop");
    
    var $ptt = $user_roles_wrap.find("input.checkbox");
    
    var updateDisplay = function() {
      
      if (control_style_single.is(":checked") || basic.is(":checked")) {
        ml_f.hide();
        csw.hide();
      } else {
        ml_f.show();
        csw.show();
      }
    };


    var updateResultGrid = function() {
      
      $ptt.each( function() {
        
        var $row = $rg.find("tr.user-role-" + $(this).val());
        
        
        if ($(this).is(":checked")) {
          $row.show();
        } else {
          $row.hide();
        }
        
      });
      
      
    };
    
    var updateResultGridColumns = function() {
      
      var ec = $rg.find(".excerpt");
      var ic = $rg.find(".image");

      ec.removeClass("disabled");
      ic.removeClass("disabled");
      
      if ($p("row_style_icon_title").is(":checked")) {
        ec.addClass("disabled");
        ic.addClass("disabled");
      }

      if ($p("row_style_image_title").is(":checked")) {
        ec.addClass("disabled");
      }

      if ($p("row_style_icon_title_excerpt").is(":checked")) {
        ic.addClass("disabled");
      }
      
    };
  
    $rg.find("input.text").bind({
      focus: function() {
        var cn = $(this).closest("td").attr("class");
        $rg.find("td." + cn).addClass("focus");
      },
      blur: function() {
        $rg.find("td").removeClass("focus");
      }
    });
    
    
    $ptt.click( updateResultGrid );
    
    
    // setup buttons to launch dialogs
    
    $rg.find("button").click( function() {
    
      var $dialog = $();
      
      
      try {
        $dialog = $('#' + $(this).data("dialog"));
      } catch (e) {
        
      }
      
      if ($dialog.length) {
        
        // tell the dialog where it should populate values
        
        $dialog.data("target", $(this).closest("td").find("input.text"));
        
        var filter = $(this).data("filter");
        
        var $sel = $dialog.find("select");

        var opts = { 
          closeOnEscape: true, 
          width: 500, 
          height: 145, 
          resizable: false, 
          modal: true,
          dialogClass: "wp-dialog aristo dialog-fields", 
          autoOpen: true, 
          title: $dialog.data("title"),
          buttons: { 

            "OK" : function() { 
              var expr = $.trim( $sel.mp_select2("val") );
              
              var $target = $(this).data("target");
              
              if (expr && $target && $target.length) {
                $target.val(expr);
              }
                
              $(this).dialog("close");
              $sel.mp_select2("unfilter");
            },
            
            "Cancel" : function() { 
              $(this).dialog("close");
              $sel.mp_select2("unfilter");
            }
            
          }
        };

        if (!$dialog.data("has_select2")) {
          $dialog.dialog(opts);
          $sel.mp_select2();
          $dialog.data("has_select2", true);
        } else {
          $dialog.dialog('open');
        }

        $sel.mp_select2("val", "");
        $sel.mp_select2("filter", filter);

        $sel.mp_select2('focus');
        
      }
      
    });
    
    
    
    
    ui.find(".row-style").click( function() {
      $(this).find("input.radio").attr("checked", "checked");
      updateResultGridColumns();
    });
        
    control_style_single.click( updateDisplay );
    control_style_multi.click( updateDisplay );
    basic.change( updateDisplay );    
    updateDisplay();
        
  });
  
    
})(jQuery);