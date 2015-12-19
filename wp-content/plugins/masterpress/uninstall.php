<?php

/**
 * Uninstall file for MasterPress
 * 
 */

global $wpdb;

if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit();
}

// We won't delete data tables, but will delete MasterPress definition tables

$tables = array("post_types", "templates", "taxonomies", "fields", "field_sets");

foreach ($tables as $table) {
  $table_name = MPU::table($table);
  $sql = "DROP TABLE IF EXISTS $table_name;";
  $wpdb->query($sql);
}

delete_option( "masterpress_version" );
delete_option( "masterpress_flush_rules" );
delete_option( "mp_licence_key" );
