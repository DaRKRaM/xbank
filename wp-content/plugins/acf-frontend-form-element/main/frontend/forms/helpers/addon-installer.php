<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
   
function fea_is_plugin_installed( $slug ) {
  if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  $all_plugins = get_plugins();
  if( $all_plugins ){
    foreach( $all_plugins as $plugin ){
      if( $plugin['TextDomain'] == $slug ){
        return true;
      }  
    }
  }
  return false;
  
}
function fea_addon_slug( $slug ) {
  if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  $all_plugins = get_plugins();
  if( $all_plugins ){
    foreach( $all_plugins as $path => $plugin ){
      if( $plugin['TextDomain'] == $slug ){
        return $path;
      }  
    }
  }
  return false;
  
}
 
function fea_install_plugin( $plugin_zip ) {
  include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
  wp_cache_flush();
   
  $upgrader = new Plugin_Upgrader();
  $installed = $upgrader->install( $plugin_zip );
 
  return $installed;
}
 