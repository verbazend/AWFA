<?php

class MPC_Masterplan extends MPC {
  
  public function init() {
    MasterPress::enqueue_codemirror();
  }
  
	

  public function manage() { 

		global $wf, $blog_id;
		
    $this->setup_view( array(
        "title_args" => array( "text" => sprintf( __("Masterplan - <small>Powered by %s</small>", MASTERPRESS_DOMAIN), '<a href="http://masterpressplugin.com" target="_blank">MasterPress</a>' ) )
      )
    );

    if (defined("MASTERPRESS_LEGACY_DIR_MIGRATE")) {
      
  		if (defined("MASTERPRESS_LEGACY_DIR") && MasterPress::$action != "legacy_dir_migrate") {
			
  			update_option("mp_legacy_dir", "1");
			
  			$ud = wp_upload_dir(); 

  			$steps  = WOOF_HTML::open("ul");
			
  			$new_ul_base = str_replace($wf->site->url, "", $ud["baseurl"]) . "/mp/files/$1";
			
  			$rules = <<<HTML
		
  			&lt;IfModule mod_rewrite.c&gt;<br>

  			RewriteEngine On<br>
  			RewriteBase /<br>
  			<br>
  			RewriteCond %{REQUEST_FILENAME} !-f<br>
  			RewriteRule ^wp-content/mp/uploads/(.+) $new_ul_base [PT]<br>
  			<br>
  			RewriteCond %{REQUEST_FILENAME} !-f<br>
  			RewriteRule ^wp-content/blogs.dir/([0-9]+)/mp/uploads/(.+) wp-content/blogs.dir/$1/mp/files/$2 [PT]<br>
  			<br>
  			RewriteCond %{REQUEST_FILENAME} !-f<br>
  			RewriteRule ^wp-content/mp/(.+) wp-content/uploads/mp/$1 [PT,L]<br>
  			&lt;/IfModule&gt;	
			
HTML;
	
  			$step = 1;
			
  			$steps .= WOOF_HTML::tag("li", "", __("<b>".$step++.".</b> Backup your WordPress database."), MASTERPRESS_DOMAIN );
			
  			$steps .= WOOF_HTML::tag("li", "", sprintf( __("<b>(Optional)</b>. To avoid 404s from existing links to your files, you may wish to place the following lines just above in your .htaccess file (at the root of your site).<code>%s</code><br><b>IMPORTANT:</b> These rules should be <b>placed just above the line # BEGIN WordPress </b> in your .htaccess file, as placing it above rules from caching plugins may cause issues.", MASTERPRESS_DOMAIN ), $rules ) );
			
  			if (MASTERPRESS_MULTI) {

  				if (WOOF_MS_FILES) {

  					if (is_main_site()) {
  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Move the masterpress content folder currently at:<br> <span class="tt">%s/mp</span><br>to:<br> <span class="tt">%s/mp</span>', MASTERPRESS_DOMAIN ), $step++, WP_CONTENT_DIR, $ud["basedir"] ) );
  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Rename the folder:<br> <span class="tt">%s/mp/uploads</span><br>to:<br> <span class="tt">%s/mp/files</span>', MASTERPRESS_DOMAIN ), $step++, WP_CONTENT_DIR, WP_CONTENT_DIR  ) );
  					}
				
  					$base = WP_CONTENT_DIR . WOOF_DIR_SEP . "blogs.dir" . WOOF_DIR_SEP . $blog_id;
  					$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d</b> Rename the folder:<br> <span class="tt">%s/mp/uploads</span><br>to:<br> <span class="tt">%s/mp/files</span>', MASTERPRESS_DOMAIN ), $step++, $base , $base  ) );
  					$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d</b> Click the migrate links button below to update your custom field database entries to point to the new location:<br>%s', MASTERPRESS_DOMAIN ), $step++, WOOF_HTML::tag("a", array("href" => MasterPress::admin_url( "masterplan", "legacy_dir_migrate", "", false ), "class" => "button button-small" ), "Migrate Links" ) ) ); 

  				} else {
				
  					if (is_main_site()) {

  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Move the masterpress content folder currently at:<br> <span class="tt">%s/mp</span><br>to:<br> <span class="tt">%s/mp</span>', MASTERPRESS_DOMAIN ), $step++, WP_CONTENT_DIR, $ud["basedir"] ) );
  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Rename the folder:<br> <span class="tt">%s/mp/uploads</span><br>to:<br> <span class="tt">%s/mp/files</span>', MASTERPRESS_DOMAIN ), $step++, $ud["basedir"], $ud["basedir"]  ) );
  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Click the migrate links button below to update your custom field database entries to point to the new location:<br>%s', MASTERPRESS_DOMAIN ), $step++, WOOF_HTML::tag("a", array("href" => MasterPress::admin_url( "masterplan", "legacy_dir_migrate", "", false ), "class" => "button button-small" ), "Migrate Links" ) ) ); 

  					} else {
					
  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Rename the folder:<br> <span class="tt">%s/mp/uploads</span><br>to:<br> <span class="tt">%s/mp/files</span>', MASTERPRESS_DOMAIN ), $step++, $ud["basedir"], $ud["basedir"]  ) );
  						$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Click the migrate links button below to update your custom field database entries to point to the new location:<br>%s', MASTERPRESS_DOMAIN ), $step++, WOOF_HTML::tag("a", array("href" => MasterPress::admin_url( "masterplan", "legacy_dir_migrate", "", false ), "class" => "button button-small" ), "Migrate Links" ) ) ); 
				
  					}
				
  				}
				
  			} else {
				
  				$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Move the masterpress content folder currently at:<br> <span class="tt">%s/mp</span><br>to:<br> <span class="tt">%s/mp</span>', MASTERPRESS_DOMAIN ), $step++, WP_CONTENT_DIR, $ud["basedir"] ) );
  				$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Rename the folder:<br> <span class="tt">%s/mp/uploads</span><br>to:<br> <span class="tt">%s/mp/files</span>', MASTERPRESS_DOMAIN ), $step++, $ud["basedir"], $ud["basedir"]  ) );
  				$steps .= WOOF_HTML::tag("li", "", sprintf( __('<b>%d.</b> Click the migrate links button below to update your custom field database entries to point to the new location:<br>%s', MASTERPRESS_DOMAIN ), $step++, WOOF_HTML::tag("a", array("href" => MasterPress::admin_url( "masterplan", "legacy_dir_migrate", "", false ), "class" => "button button-small" ), "Migrate Links" ) ) ); 
			
  			}
			
  			$steps .= WOOF_HTML::close("ul");
			
  			MPV::warn( sprintf( __('Your MasterPress installation is storing certain content in folders which are <a href="%s">no longer the default locations in version 1.0.1.</a><br><br>To migrate to the new location: %s', MASTERPRESS_DOMAIN ), "http://masterpressplugin.com/blog/content-in-101/", $steps ) );
			
  		} else if (get_option("mp_legacy_dir")) {
			
  			$migrate = sprintf( __('Click the migrate links button below to update your custom field database entries to point to the new location:<br>%s', MASTERPRESS_DOMAIN ), WOOF_HTML::tag("a", array("href" => MasterPress::admin_url( "masterplan", "legacy_dir_migrate", "", false ), "class" => "button button-small" ), "Migrate Links" ) ); 

  			MPV::warn( sprintf( __('It appears you\'ve moved your MasterPress content folder from the old <span class="tt">wp-content/mp</span> location without running the necessary database migration.<br><br>%s', MASTERPRESS_DOMAIN ), $migrate ) );
			
  		}
		
		}
    
    if (isset($_POST["import_masterplan"]) || isset($_POST["restore_masterplan"])) {
      self::import();
    } 

  }
  
	public function legacy_dir_migrate() {
		
		global $wpdb, $wf, $blog_id;
			
		$ud = wp_upload_dir(); 

		$affected_rows = 0;
		
		$tables = array( $wpdb->postmeta, $wpdb->usermeta, MPU::site_table("termmeta"));
		
		if (MASTERPRESS_MULTI) {

			foreach ($tables as $table) {
				// hard-code blogs.dir here, as it's the only setup that was supported anyway (any others would have failed with masterpress 1.0)
				$sql = "UPDATE $table SET meta_value = REPLACE(meta_value, '/blogs.dir/$blog_id/mp/uploads/', '/blogs.dir/$blog_id/mp/files/' ) WHERE meta_value LIKE '%/blogs.dir/$blog_id/mp/uploads/%' ";

				$affected_rows += $wpdb->query( $sql );
			
				// there's no need to update the /sites/files type URLs, as no install could have ever had these working
			}

			if (is_main_site()) {
				foreach ($tables as $table) { 
					$sql = "UPDATE $table SET meta_value = REPLACE(meta_value, '" . WP_CONTENT_URL . "/mp/uploads', '" . $ud["baseurl"] . "/mp/files" . "' ) WHERE meta_value LIKE '%/mp/uploads/%' ";
					$affected_rows += $wpdb->query( $sql );
				}
			}
			
		
		} else {
			
			foreach ($tables as $table) { 
				$sql = "UPDATE $table SET meta_value = REPLACE(meta_value, '" . WP_CONTENT_URL . "/mp/uploads', '" . $ud["baseurl"] . "/mp/files" . "' ) WHERE meta_value LIKE '%/mp/uploads/%' ";
				$affected_rows += $wpdb->query( $sql );
			}

		}
		

		update_option("mp_legacy_dir", "0");
		
		MPV::success( sprintf( __("%d database records were migrated to the new content path", MASTERPRESS_DOMAIN), $affected_rows ) );
		
		$this->manage();

	}

	
  public function import() {
    global $wf;

    $base_dir = MASTERPRESS_TMP_DIR;
    $base_url = MASTERPRESS_TMP_URL;
    
    $restore = false;
    
    if (isset($_POST["import_masterplan"])) {
      $masterplan = $_POST["import_masterplan"];
    } else {
      if (isset($_POST["restore_masterplan"])) {
        $masterplan = $_POST["restore_masterplan"];
        $base_dir = MASTERPRESS_CONTENT_MASTERPLANS_DIR;
        $base_url = MASTERPRESS_CONTENT_MASTERPLANS_URL;
        $restore = true;
      }
    }
    
    
    $view = MasterPress::$view;
    
    if (isset($masterplan)) {
      
      extract(self::open_masterplan($masterplan, true, $base_dir, $base_url));
    
      if ($rep) {



        $icon_dest = MASTERPRESS_CONTENT_MENU_ICONS_DIR;
        $icon_src = $dir.WOOF_DIR_SEP."icons".WOOF_DIR_SEP;

        $field_type_dest = MASTERPRESS_EXTENSIONS_FIELD_TYPES_DIR;
        $field_type_src = $dir.WOOF_DIR_SEP."field-types".WOOF_DIR_SEP;
        
                   
        // install field type extensions
        
        foreach ($field_types as $field_type) {
          $dest = $field_type_dest.$field_type["name"];
          $src = $field_type_src.$field_type["name"];
          
          if (!(file_exists($dest) && !isset($_POST["import_types_overwrite"]))) {
            MPU::copyr($src, $dest);
          }
        
        }
        
        $info = array("log" => array());
          
        $mode = $_REQUEST["import_mode"];
        
        if ($restore) {
          $mode = "replace";
        }
        
        $log = &$info["log"];
        
        $backup = isset($_REQUEST["import_backup"]);

        $view->rep = $rep;
        $view->backup = $backup;
        $view->mode = $mode;
        
        // first obtain the current representation
        
        $current = self::get_current_rep();
        $view->current = $current;
      
        if ($backup) {
          
          // build the backup (a rollback point)
      
          $backup_filename = "_backup.pre-import.".$wf->format_date("[date-time-sortable]").".masterplan.zip";
          $backup_readme = "# Backup Masterplan for ".$wf->sites()->first()->name." #

+ Created: ".$wf->format_date("[date-time-long]");
        
          $backup_rep = $this->export_rep($current);
          $package = $this->build_package($backup_rep, $backup_filename, $backup_readme);
        
          if ($package) {
            $info["backup_package"] = $package;
            $info["backup_url"] = MASTERPRESS_CONTENT_MASTERPLANS_URL.$backup_filename;
            $info["backup_filename"] = $backup_filename;
          }
        
        }
        
        // process the representation

        // store all objects by key to look them up for potential removal (if mode is replace)
        
        $import = array(
          "post_types" => array(),
          "taxonomies" => array(),
          "shared_field_sets" => array(),
          "site_field_sets" => array(),
          "templates" => array(),
          "roles" => array()
        );

        $taxonomies_by_key = array();
        $shared_field_sets_by_key = array();
        $shared_field_sets_by_key = array();
        
        if (isset($rep["post_types"])) {
        
          foreach ($rep["post_types"] as $post_type) {
          
            $data = $post_type;
            unset($data["field_sets"], $data["id"], $data["sites"]);
          
            $name = $post_type["name"];
          
            $insert = true;
          
            if ($model = MPM_PostType::find_by_name($name)) {
              $insert = false;

            } else {
              $model = new MPM_PostType();
            }
            
            $model->set($data, true);
          
            if ($insert) {
              $model->insert();
            } else {
              $model->update();
            }
        
            // install icons
        
            if ($post_type["_builtin"] == "0" && !is_null($post_type["menu_icon"]) && $post_type["menu_icon"] != "") {
              copy($icon_src.$post_type["menu_icon"], $icon_dest.$post_type["menu_icon"]);
            }

            $import["post_types"][$name] = $post_type;
          
            // now run through the field sets, taking care to preserve 
            $import["post_types"][$name]["field_sets"] = self::import_field_sets($post_type["field_sets"], " AND `type` = 'p' AND ".MPM::visibility_rlike("post_types", $name), $dir);


          }
        
        }
        
        if (isset($rep["taxonomies"])) {
        
          foreach ($rep["taxonomies"] as $tax) {
          
            $data = $tax;
            unset($data["field_sets"], $data["id"], $data["sites"]);
          
            $name = $tax["name"];
            
            $insert = true;
          
            if ($model = MPM_Taxonomy::find_by_name($name)) {
              $insert = false;
            } else {
              $model = new MPM_Taxonomy();
            }
            
            $model->set($data, true);
          
            if ($insert) {
              $model->insert();
            } else {
              $model->update();
            }
      
            // install icons
        
            if ($tax["_builtin"] == "0" && !is_null($tax["title_icon"]) && $tax["title_icon"] != "") {
              copy($icon_src.$tax["title_icon"], $icon_dest.$tax["title_icon"]);
            }
          
            $import["taxonomies"][$name] = $tax;
          
            $import["taxonomies"][$name]["field_sets"] = self::import_field_sets($tax["field_sets"], " AND `type` = 'x' AND ".MPM::visibility_rlike("taxonomies", $name), $dir);
          
          }
          
        }

        if (isset($rep["shared_field_sets"])) {
          $import["shared_field_sets"] = self::import_field_sets($rep["shared_field_sets"], " AND `type` = 's' ", $dir);
        }

        if (isset($rep["site_field_sets"])) {
          $import["site_field_sets"] = self::import_field_sets($rep["site_field_sets"], " AND `type` = 'w' ", $dir);
        }
        
        if (isset($rep["templates"])) {
          foreach ($rep["templates"] as $template) {
            
            $id = $template["id"];
            
            // template models are self-creating, so no need for special handling here
            $model = MPM_Template::find_by_id($id);
            
            $data = $template;
            unset($data["field_sets"]);
            
            $model->set($data, true);
            $model->update();

            $import["templates"][$id] = $template;
            
            if (isset($template["field_sets"])) {
              $import["templates"][$id]["field_sets"] = self::import_field_sets($template["field_sets"], " AND `type` = 't' AND ".MPM::visibility_rlike("templates", $id), $dir);
            }

          
          }
        }

        if (isset($rep["roles"])) {
          foreach ($rep["roles"] as $role) {
            if (isset($role["field_sets"])) {
              $name = $role["name"];
              
              $import["roles"][$name] = $role;
              $import["roles"][$name]["field_sets"] = self::import_field_sets($role["field_sets"], " AND `type` = 'r' AND ".MPM::visibility_rlike("roles", $name), $dir);

            }
            
          }
        }
        
        
        if ($mode == "replace") {
          
          // we now need to remove anything in the current setup that ISN'T in the imported masterplan

          if (isset($current["post_types"])) {
            foreach ($current["post_types"] as $post_type) {
              
              $name = $post_type["name"];
              $builtin = $post_type["_builtin"] == "1";
              
              if (!isset($import["post_types"][$name]) && !$builtin) {
                
                $model = MPM_PostType::find_by_name($name);
    
                if ($model) {
                  $model->delete( array(
                    "posts" => "leave",
                    "posts_reassign_type" => "",
                    "field_sets" => "delete",
                    "field_data" => "keep"
                  ));
                }
                
              } else {
                
                if (isset($post_type["field_sets"])) {
                  self::replace_field_sets($post_type["field_sets"], $import["post_types"][$name]["field_sets"]);
                }
                
              }
              
            }
          }
          
          

          if (isset($current["taxonomies"])) {
            foreach ($current["taxonomies"] as $tax) {

              $name = $tax["name"];
              $builtin = $tax["_builtin"] == "1";
              
              if (!isset($import["taxonomies"][$name]) && !$builtin) {
                
                $model = MPM_Taxonomy::find_by_name($name);
                
                if ($model) {
                  $model->delete( array(
                    "existing_terms" => "leave",
                    "existing_terms_reassign_taxonomy" => "",
                    "field_sets" => "delete",
                    "field_data" => "leave"
                  ));
                }
                
              } else {
                if (isset($tax["field_sets"])) {
                  self::replace_field_sets($tax["field_sets"], $import["taxonomies"][$name]["field_sets"]);
                }
              
              }
              
            }
          }

          if (isset($current["shared_field_sets"])) {
            self::replace_field_sets($current["shared_field_sets"], $import["shared_field_sets"]);
          }

          if (isset($current["site_field_sets"])) {
            self::replace_field_sets($current["site_field_sets"], $import["site_field_sets"]);
          }

          if (isset($current["templates"])) {

            foreach ($current["templates"] as $template) {

              $id = $template["id"];

              if (isset($import["templates"][$id])) {
                if (isset($template["field_sets"])) {
                  self::replace_field_sets($template["field_sets"], $import["templates"][$id]["field_sets"]);
                }
              }
              
            }

          }


          if (isset($current["roles"])) {

            foreach ($current["roles"] as $name => $role) {

              if (isset($import["roles"][$name])) {
                  
                if (isset($role["field_sets"])) {
                  self::replace_field_sets($role["field_sets"], $import["roles"][$name]["field_sets"]);
                }
              }
              
            }
          }
          
        } // mode = replace
        
        
        // cleanup the unzipped folder
        
        MPU::rmdir_r($dir);
        
        if ($restore) {
          wp_redirect( MasterPress::admin_url( $this->key(), "manage", array("restore-complete" => "1"), false ) );
        } else {
          wp_redirect( MasterPress::admin_url( $this->key(), "manage", array("import-complete" => "1"), false ) );
        }
      
        
        $view->info = $info;
        
      } else {
        self::ajax_error(__("The Masterplan is no longer valid. Please try another file", MASTERPRESS_DOMAIN));
      }
      
    } else {
      self::ajax_error(__("The Masterplan is no longer valid. Please try another file", MASTERPRESS_DOMAIN));
    }

    
    
  }

  protected function replace_field_sets(&$current_sets, &$import_sets) {
    
    foreach ($current_sets as $set) {
      $name = $set["name"];

      if (!isset($import_sets[$name])) {
        $model = MPM_FieldSet::find_by_id($set["id"]);
        if ($model) {
          $model->delete();
        }
      } else {
        
        $import_set = $import_sets[$name];
        
        foreach ($set["fields"] as $field) {
          $name = $field["name"];
          
          if (!isset($import_set["fields_by_name"][$name])) {

            $model = MPM_Field::find_by_id($field["id"]);

            if ($model) {
              $model->delete();
            }
            
          }
          
        }
        
        
      }
    }
      
  }
  
  protected function import_field_sets(&$field_sets, $lookup_criteria = '', $dir = '') {

    $import = array();
    
    $icon_dest = MASTERPRESS_CONTENT_MENU_ICONS_DIR;
    $icon_src = $dir.WOOF_DIR_SEP."icons".WOOF_DIR_SEP;
    
    if (isset($field_sets) && count($field_sets)) {
      
      foreach ($field_sets as $field_set) {
            
        // try to find a field set with the same specification
        // we need to UPDATE these, rather than creating a new ID, so that all relationships still hold
        
        $where = "`name` = '".$field_set["name"]."' ".$lookup_criteria;
      
        $data = $field_set;
        unset($data["id"], $data["fields"], $data["parent_id"], $data["description"], $data["templates_permission"], $data["sites"], $data["templates"]);

        $results = MPM_FieldSet::find( array( "where" => $where ) );

        $field_set_insert = true;
      
        if (count($results)) {
          $field_set_insert = false;
          $field_set_model = $results[0];
        } else {
          $field_set_model = new MPM_FieldSet();
        }

        $field_set_model->set($data, true);
      
        if ($field_set_insert) {
          $field_set_model->insert();
        } else {
          $field_set_model->update();
        }
      
        $field_set["fields_by_name"] = array();

        // install field set icons

        if (isset($field_set["icon"]) && !is_null($field_set["icon"]) && $field_set["icon"] != "") {
          $src = $icon_src.$field_set["icon"];
         
          if (file_exists($src)) {
            copy($src, $icon_dest.$field_set["icon"]);
          }
        }

        // now run through all of the fields inside the set
      
        foreach ($field_set["fields"] as $field) {
        
          $data = $field;
          
          unset($data["id"], $data["description"], $data["parent_id"], $data["allow_multiple"], $data["tooltip_help"], $data["post_types_inherit"], $data["sites"]);
          
          // if the field set already existed, we need to look up a field with the same name under that parent
          // it's very important to UPDATE these, rather than creating a new ID, so that all meta pointers are still correct
        
          $field_insert = true;

          $field_model = new MPM_Field();
        
          if ($field_set_insert) {
            $where = "`name` = '".$field["name"]."' AND `field_set_id` = ".$field_set_model->id;
          
            $results = MPM_Field::find(array( "where" => $where ));
          
            if (count($results)) {
              $field_model = $results[0];
              $field_insert = false;
            } 
          
          }
        
          $field_model->set($data, true);
          $field_model->field_set_id = $field_set_model->id;
        
          if (isset($field["icon"]) && !is_null($field["icon"]) && $field["icon"] != "") {
            $src = $icon_src.$field["icon"];
         
            if (file_exists($src)) {
              copy($src, $icon_dest.$field["icon"]);
            }
          }


          
          if ($field_insert) {
            $field_model->insert();
          } else {
            $field_model->update();
          }
        
          $field_set["fields_by_name"][$field["name"]] = $field;
        
        }
      
        $import[$field_set["name"]] = $field_set;
      
      }
    
    }
    
    return $import;
  }
  
  protected function process_field_sets($sets, $ft_dir, &$info, &$field_types_by_name) {
    
    if (isset($sets)) { 
      foreach ($sets as $field_set) {
      
        if (isset($field_set->fields)) {
          foreach ($field_set->fields as $field) {

            if (isset($field_types_by_name[$field->type])) {
              $icon_url = $field_types_by_name[$field->type]["icon"];
            } else {
              $icon_url = MPU::type_icon_url($field->type);

              if (!MPU::type_exists($field->type)) {
                // check if the field type is included in the package
          
                if (file_exists($ft_dir.$field->type)) {
                  $icon_url = $ft_dir.$field->type.WOOF_DIR_SEP."icon-color.png";
                } else {
                  $info["missing_field_types"] = $field->type;
                  $field->missing = true;
                }
          
              }

            }
        
            $field->icon_url = $icon_url;
            
          }
        }
    
      }
    }
              
  }
     
  public function get_current_rep() {
    
    global $wf;
    
    $rep = array(
      "post_types" => array(),
      "taxonomies" => array(),
      "shared_field_sets" => array(),
      "site_field_sets" => array(),
      "roles" => array()
    );
        
    foreach (MPM_PostType::find() as $post_type) {
      $rep["post_types"][$post_type->name] = $post_type->rep();
    }

    foreach (MPM_Taxonomy::find() as $tax) {
      $rep["taxonomies"][$tax->name] = $tax->rep();
    }

    foreach (MPM_SharedFieldSet::find() as $set) {
      $rep["shared_field_sets"][$set->id] = $set->rep();
    }

    foreach (MPM_SiteFieldSet::find() as $set) {
      $rep["site_field_sets"][$set->id] = $set->rep();
    }
    
    $templates = isset( $_POST["templates"] ) ? $_POST["templates"] : array();
    
    foreach ($templates as $file => $template) { 
      $model = MPM_Template::find_by_id($file);
      $rep["templates"][$file] = $model->rep();
    }

    $roles = $wf->roles();
    
    $rep["roles"] = array();
    
    foreach ($roles as $role) {
      
      $role_id = $role->id();
  
      if (!isset($rep["roles"][$role_id])) {
        $rep["roles"][$role_id] = array(
          "id" => $role_id,
          "name" => $role->name,
          "capabilities" => $role->capabilities()
        );
      }
    
      foreach (MPM_RoleFieldSet::find_by_role($role_id, "id ASC") as $set) {
        $r = $set->rep();
        $rep["roles"][$role_id]["field_sets"][] = $r;
      }
    }
      
    return $rep;
  }
  
  protected function open_masterplan($filename, $json_assoc = false, $base_dir = MASTERPRESS_TMP_DIR, $base_url = MASTERPRESS_TMP_URL) {

    // first, unzip the masterplan
    
    $file = $base_dir.$filename;

    if (file_exists($file)) {
      
      $working_dir = $base_dir;

      require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
    
  	  $archive = new PclZip($file);
      
  	  $archive_files = $archive->extract(PCLZIP_OPT_PATH, $working_dir, PCLZIP_OPT_REPLACE_NEWER);
      
      if ($archive_files == 0) {
        self::ajax_error($archive->errorInfo(true));
      } else {
        $main_dir = $archive_files[0]["filename"];
        @chmod($main_dir, 0777);
        
        // open the masterplan file
        
        $masterplan = $main_dir.WOOF_DIR_SEP."masterplan.json";
        
        $ft_dir = $main_dir.WOOF_DIR_SEP."field-types".WOOF_DIR_SEP;
        $pi = pathinfo($main_dir);
        
        $url = $base_url."/".$pi["basename"]."/icons/";
        $ft_url = $base_url."/".$pi["basename"]."/field-types/";
        
        if (file_exists($masterplan)) {
          $content = file_get_contents($masterplan);
          
          $rep = json_decode($content, $json_assoc);
          
          if ($rep) {
          
            // check for field types
            $field_types = array();
            $field_types_by_name = array();
            
            if (file_exists($ft_dir)) {
              $iterator = new DirectoryIterator($ft_dir);
          
              foreach ($iterator as $file) {

                $file_name = $file->getFileName();
      
                if (substr($file_name, 0, 1) == ".") {
                  continue;
                }
                
                if ($file->isDir()) {
                  $icon = $ft_url."/".$file_name."/icon-color.png";
                  
                  $field_types[] = array("name" => $file_name, "icon" => $icon);
                  $field_types_by_name[$file_name] = array("name" => $file_name, "icon" => $icon);
                }
              }
            }
            
            return array("rep" => $rep, "dir" => $main_dir, "masterplan" => $masterplan, "url" => $url, "ft_dir" => $ft_dir, "field_types" => $field_types, "field_types_by_name" => $field_types_by_name);
        
          } else {
            self::ajax_error(__("The Masterplan could not be interpreted correctly. Please upload an alternative file.", MASTERPRESS_DOMAIN));
          }
          
        } else {
          self::ajax_error(__("The Masterplan is not valid, as it does not contain a masterplan.json file. Please upload an alternative file.", MASTERPRESS_DOMAIN));
        }

      } 
  
      
    } else {
      self::ajax_error( sprintf( __( "The Masterplan file is missing, which may mean it did not upload correctly. Please make the folder %s writable", MASTERPRESS_DOMAIN), $base_dir ) );
    }
    
    
  }
    
  public function fetch_masterplan_summary() {

    extract(self::open_masterplan($_REQUEST["filename"]));
    
    // build the representation 
    $info = array();
    
    $info["missing_field_types"] = array();
    
    // add some meta info to the representation, for the front-end
    
    if (isset($rep->post_types)) {
      
      foreach ($rep->post_types as $pt) {
      
        $pt->icon_url = MPU::img_url("menu-icon-posts.png");
      
        if ($pt->name == "post") {
          $pt->icon_url = MPU::img_url("menu-icon-posts.png");
        } else if ($pt->name == "page") {
          $pt->icon_url = MPU::img_url("menu-icon-pages.png");
        } else if (!is_null($pt->menu_icon) && trim($pt->menu_icon) != "") {
          $pt->icon_url = $url.$pt->menu_icon;
        } 

        if (isset($pt->field_sets)) {
          self::process_field_sets($pt->field_sets, $ft_dir, $info, $field_types_by_name);
        }
    
      }
      
    }
    
    if (isset($rep->taxonomies)) {

      foreach ($rep->taxonomies as $tax) {
      
        $tax->icon_url = MPU::img_url("icon-tag.png");
      
        if (!is_null($tax->title_icon) && trim($tax->title_icon) != "") {
          $tax->icon_url = $url.$tax->title_icon;
        }

        if (isset($tax->field_sets)) {
          self::process_field_sets($tax->field_sets, $ft_dir, $info, $field_types_by_name);
        }
    
      }
    
    }

    if (isset($rep->shared_field_sets)) {
      self::process_field_sets($rep->shared_field_sets, $ft_dir, $info, $field_types_by_name);
    }

    if (isset($rep->site_field_sets)) {
      self::process_field_sets($rep->site_field_sets, $ft_dir, $info, $field_types_by_name);
    }
    
    if (isset($rep->templates)) {


      foreach ($rep->templates as $template) {
      
        if (isset($template->field_sets)) {
          self::process_field_sets($template->field_sets, $ft_dir, $info, $field_types_by_name);
        }
    
      }
    
    }
          
    if (isset($rep->roles)) {

      foreach ($rep->roles as $role) {
      
        if (isset($role->field_sets)) {
          self::process_field_sets($role->field_sets, $ft_dir, $info, $field_types_by_name);
        }
    
      }
    
    }
    
              
    $info["field_types"] = $field_types;  
    $info["rep"] = $rep;

    self::ajax_success($info);
    
  }
  
  protected function add_icon(&$icons, $obj, $key = "icon") {
    if (isset($obj[$key]) && !is_null($obj[$key]) && $obj[$key] != "") {
      $icons[] = $obj[$key];
    } 
  }
     
  protected function export_rep($export) {
    global $wf;
    
    // create the associative array representation
    
    $rep = array(
      "created" => date("c"),
      "masterpress_version" => MasterPress::$version,
      "icons" => array()
    );
    
    $types = array();

    if (isset($export["post_types"])) {
      
      $rep["post_types"] = array();
      
      foreach (array_keys($export["post_types"]) as $post_type_name) {
        $post_type = MPM_PostType::find_by_name($post_type_name);
        
        $r = $post_type->rep();
        $rep["post_types"][] = $r;
        
        self::add_icon($rep["icons"], $r, "menu_icon");

        if (isset($r["field_sets"])) {
          
          foreach ($r["field_sets"] as $set) {
            self::add_icon($rep["icons"], $set);

            foreach ($set["fields"] as $field) {

              self::add_icon($rep["icons"], $field);
              $types[$field["type"]] = true;

            }
          }
        
        }
      
      }
      
    }    

    if (isset($export["taxonomies"])) {
      
      $rep["taxonomies"] = array();
      
      foreach (array_keys($export["taxonomies"]) as $tax_name) {
        $tax = MPM_Taxonomy::find_by_name($tax_name);
        $r = $tax->rep();
        $rep["taxonomies"][] = $r;

        self::add_icon($rep["icons"], $r, "title_icon");

        if (isset($r["field_sets"])) {
          foreach ($r["field_sets"] as $set) {
            self::add_icon($rep["icons"], $set);

            foreach ($set["fields"] as $field) {
              self::add_icon($rep["icons"], $field);
              $types[$field["type"]] = true;
            }
          }
        }
          
      }
      
    } 

    if (isset($export["shared_field_sets"])) {
      
      $rep["shared_field_sets"] = array();
      
      foreach (array_keys($export["shared_field_sets"]) as $id) {
        $set = MPM_FieldSet::find_by_id($id);
        $r = $set->rep();

        self::add_icon($rep["icons"], $r);

        $rep["shared_field_sets"][] = $r;
        
        if (isset($r["fields"])) {

          foreach ($r["fields"] as $field) {
            self::add_icon($rep["icons"], $field);
            $types[$field["type"]] = true;
          }
        
        }
      
      }
      
    } 

    if (isset($export["site_field_sets"])) {
      
      $rep["site_field_sets"] = array();
      
      foreach (array_keys($export["site_field_sets"]) as $id) {
        $set = MPM_FieldSet::find_by_id($id);
        $r = $set->rep();
        $rep["site_field_sets"][] = $r;

        self::add_icon($rep["icons"], $r);

        if (isset($r["fields"])) {

          foreach ($r["fields"] as $field) {
            self::add_icon($rep["icons"], $field);
            $types[$field["type"]] = true;
          }
        
        }
      
      }
      
    } 

    if (isset($export["templates"])) {
      
      $rep["templates"] = array();
      
      foreach (array_keys($export["templates"]) as $template) {

        $template = MPM_Template::find_by_id($template);
        
        $r = $template->rep();
        $rep["templates"][] = $r;

        if (isset($r["field_sets"])) {

          foreach ($r["field_sets"] as $set) {
            self::add_icon($rep["icons"], $set);

            foreach ($set["fields"] as $field) {
              self::add_icon($rep["icons"], $field);
              $types[$field["type"]] = true;
            }
          }

        }
      

      }
      
    } 
    
    
    if (isset($export["roles"])) {
      
      $rep["roles"] = array();
      
      foreach (array_keys($export["roles"]) as $role) {
        
        $wr = $wf->role($role);
        
        $rr = array("id" => $role, "name" => $wr->name, "capabilities" => $wr->capabilities, "field_sets" => array());

        foreach (MPM_RoleFieldSet::find_by_role($role, "id ASC") as $set) {
          $r = $set->rep();

          self::add_icon($rep["icons"], $r);
          
          if (isset($r["fields"])) {

            foreach ($r["fields"] as $field) {
              $types[$field["type"]] = true;
              self::add_icon($rep["icons"], $field);
            }
          
          }
        
          $rr["field_sets"][] = $r;

        }
        
        $rep["roles"][] = $rr;

      }
      
    } 
    
    $rep["field_types"] = array_keys($types);
    
    return $rep;
    
  }

  protected function build_package($rep, $filename, $readme = "") {
    $export_filename = MASTERPRESS_TMP_DIR.$filename;

    $destination_filename = MASTERPRESS_CONTENT_MASTERPLANS_DIR.$filename;

    $info["filename"] = $export_filename;
    
    // setup PCL Zip to create the package containing a json Masterplan and any referenced menu icons

    require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
    
    $zip = new PclZip($export_filename);

    // create a file out of the representation
    
    $pi = pathinfo($filename);
    
    $dir = MASTERPRESS_TMP_DIR.$pi["filename"];
    
    wp_mkdir_p($dir);
    wp_mkdir_p($dir.WOOF_DIR_SEP."icons");
    
    $handle = fopen( $dir.WOOF_DIR_SEP."masterplan.json", "w");
    
    fwrite($handle, WOOF::json_indent( json_encode($rep) ) );
    fclose($handle);

    if (trim($readme) != "") {
      $handle = fopen( $dir.WOOF_DIR_SEP."README.markdown", "w");
      fwrite($handle, $readme );
      fclose($handle);
    }
  
    $file_list  = array($dir);
  
    $icon_list = array();
    $type_list = array();
      
    // grab any referenced icons
    
    foreach ($rep["icons"] as $icon_file) {
      $icon = MASTERPRESS_CONTENT_MENU_ICONS_DIR.$icon_file;
      
      if (file_exists($icon)) {
        $dest = $dir.WOOF_DIR_SEP."icons".WOOF_DIR_SEP.$icon_file;
        $icon_list[] = array("name" => $icon_file, "url" => MASTERPRESS_CONTENT_MENU_ICONS_URL.$icon_file);
        copy($icon, $dest);
      }
    }
    
    // now check if there are any field type extensions we need too
    
    $ft_dir = $dir.WOOF_DIR_SEP."field-types";
    
    foreach ($rep["field_types"] as $type) {
      if (MPU::extension_type_exists($type)) {
        wp_mkdir_p($ft_dir);
        $source = MPU::extension_type_dir($type);
        $dest = $ft_dir.WOOF_DIR_SEP.$type;
        $type_list[] = array("key" => $type, "icon" => MPU::type_icon_url($type));
        MPU::copyr($source, $dest);
      }
    }
    

      
    $zip->create( $file_list, PCLZIP_OPT_REMOVE_PATH, MASTERPRESS_TMP_DIR );
    
    copy($export_filename, $destination_filename);

    unlink($export_filename);
    MPU::rmdir_r($dir);
    
    return array("path" => $destination_filename, "icon_list" => $icon_list, "type_list" => $type_list);
    
  } 

  public function export() {

    global $wf;
    
    if (!isset($_REQUEST["export"])) {

      self::ajax_error(__("You need to select at least one item to export", MASTERPRESS_DOMAIN));

    } else {
      
      // make sure we have a temp directory to work with
      
      if (!file_exists(MASTERPRESS_TMP_DIR)) {
        wp_mkdir_p(MASTERPRESS_TMP_DIR);
      }

      if (!file_exists(MASTERPRESS_TMP_DIR) || !is_writable(MASTERPRESS_TMP_DIR)) {
        self::ajax_error(sprintf(__("Your Masterplan cannot be exported as a working folder could not be created. Please create a folder at %s and ensure that it is writeable with permissions 755", MASTERPRESS_DOMAIN), MASTERPRESS_TMP_DIR));
      }

      $export = $_REQUEST["export"];
      $ref = $_REQUEST["ref"];

      if (!isset($_REQUEST["export_filename"])) {

        self::ajax_error(__("Please specify a filename", MASTERPRESS_DOMAIN));

      } else {
        
        // build the requested Masterplan
        
        $filename = $_REQUEST["export_filename"].".".$wf->format_date("[date-time-sortable]").".masterplan.zip";
        $rep = $this->export_rep($export);

        $package = $this->build_package($rep, $filename, $_REQUEST["export_readme"]);
        
        if ($package) {
          $info["package"] = $package;
          $info["url"] = MASTERPRESS_CONTENT_MASTERPLANS_URL.$filename;
          $info["filename"] = $filename;
        }


        
        self::ajax_success($info);
        
      }
      
    }
    
    
  }


  public function backup() {

    global $wf;
      
      // make sure we have a temp directory to work with
      
      if (!file_exists(MASTERPRESS_TMP_DIR)) {
        wp_mkdir_p(MASTERPRESS_TMP_DIR);
      }

      if (!file_exists(MASTERPRESS_TMP_DIR) || !is_writable(MASTERPRESS_TMP_DIR)) {
        self::ajax_error(sprintf(__("Your Masterplan backup cannot be exported as a working folder could not be created. Please create a folder at %s and ensure that it is writable with permissions 755", MASTERPRESS_DOMAIN), MASTERPRESS_TMP_DIR));
      }


      $ref = $_REQUEST["ref"];
      
      if ($_REQUEST["backup_filename"] != "") {
        $backup_filename = "_backup.".$_REQUEST["backup_filename"].$_REQUEST["backup_filename_suffix"];
      } else {
        $backup_filename = "_backup".$_REQUEST["backup_filename_suffix"];
      }
    
      $backup_readme = $_REQUEST["backup_readme"];
      
      // build the backup Masterplan

      $rep = $this->export_rep($ref);
      
      $package = $this->build_package($rep, $backup_filename, $backup_readme);
      
      if ($package) {
        $info["package"] = $package;
        $info["url"] = MASTERPRESS_CONTENT_MASTERPLANS_URL.$backup_filename;
        $info["filename"] = $backup_filename;
      }
      
      self::ajax_success($info);
      
    
  }
    

}
