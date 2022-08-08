<?php

//*************************************************************
// Admin settings menu - Frontier Post
//***********************************************************

// Include admin menu pages
include("admin/frontier-post-admin-general.php");
include("admin/frontier-post-admin-capabilities.php");
include("admin/frontier-post-admin-advanced.php");
include("admin/frontier-post-admin-backup.php");

add_action( 'admin_menu', 'frontier_post_admin_menu' );

function frontier_post_admin_menu() 
	{
	//create new top-level menu
	add_menu_page( 'Frontier Settings', 'Frontier', 'manage_options', 'frontier_admin_menu', 'frontier_post_admin_page_general' );
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - General Settings', 'Frontier Post Settings', 'manage_options', 'frontier_admin_menu', 'frontier_post_admin_page_general'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Capabilties & Rolebased settings', 'Frontier Post Capabilities', 'manage_options', 'frontier_post_admin_capabilities', 'frontier_post_admin_page_capabilities'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Advanced Settings', 'Frontier Post Advanced', 'manage_options', 'frontier_post_admin_advanced', 'frontier_post_admin_page_advanced'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Debug Info', 'Debug Info', 'manage_options', 'frontier_post_admin_list_capabilities', 'frontier_post_admin_list_cap'); 
	add_submenu_page( 'frontier_admin_menu', 'Frontier Post - Backup Restore', 'Export/Import', 'manage_options', 'frontier_post_admin_option_exp', 'frontier_post_admin_backup_options'); 
	
	
 
	}

//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function); 



function frontier_post_admin_page_main() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	

	
	echo '<strong>Frontier Post version: '.FRONTIER_POST_VERSION.'</strong>';
		
	 
	} // end function frontier_admin_page_main
	
	
	
	
	
	
function frontier_post_admin_advanced() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	echo '<strong>This is the external capabilities options page</strong>';
	
	 
	} // end function frontier_admin_external_cap
	

function frontier_post_admin_list_cap() 
	{
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	 
	
	
	
	global $wpdb;
	
	// Show content statistics
	$fp_sql = "SELECT post_status, post_type, count(*) as post_count FROM $wpdb->posts GROUP BY post_type, post_status ORDER BY post_type, post_status;";
	$fp_stat = $wpdb->get_results($fp_sql);
	echo '<hr>';
	echo '<h2>Post DB content breakdown</strong></h2>';
	echo '<table border="1" cellpadding="2" cellspacing="4"><tr><th>Post Type</th><th>Post Status</th><th>record count</th></tr>';
	foreach ($fp_stat as $stat)
		{
		echo '<tr>';
		echo '<td>'.$stat->post_type.'</td>';
		echo '<td>'.$stat->post_status.'</td>';
		echo '<td align="right">'.$stat->post_count.'</td>';
		echo '</tr>';
		}
	echo '</table>';
	
	
	echo '<h2>Frontier Option values per role</strong></h2>';
	echo '<hr>';
	echo '<table border="1" cellpadding="2" cellspacing="4"><tr><th>key</th><th>Value</th></tr>';
	
	$fps_general_options		= frontier_post_get_settings();
	
	foreach($fps_general_options as $key => $value)
		{
		echo '<tr>';
		echo '<td>'.$key.'</td>';
		if (is_array($value))
			echo '<td>'.print_r($value, true).'</td>';
		else
			echo '<td>'.$value.'</td>';
		
		echo '</tr>';
		}
	echo '</table>';
	
	echo '<h2>List capabilties per role</strong></h2>';
	echo '<hr>';
	
	
	// Reinstate roles
	$wp_roles	= new WP_Roles();
	$roles 	  	= $wp_roles->get_names();
	$roles		= array_reverse($roles);
	
	foreach( $roles as $key => $item ) 
		{
		$xrole = get_role($key);
		$xrole_caps = $xrole->capabilities;
		echo '<strong>'.$item.'</strong><br>';
		
		foreach($xrole_caps as $tmp_cap_name => $tmp_cap)
			{
			//echo 'pos: '.strpos($tmp_cap_name, "rontier_post").'  -  ';
			if ( strpos($tmp_cap_name, "rontier_post") == 1 )
				echo '<strong>'.$tmp_cap_name.'</strong><br>';
			else
				echo $tmp_cap_name.'<br>';
			}
		echo '<hr>';
		}	
	
	echo '<hr>';
	echo '<h2>List frontier options</strong></h2>';
	
	
	$fp_sql 	= "SELECT option_name  FROM $wpdb->options WHERE option_name LIKE 'frontier_post%';";
	$fp_options = $wpdb->get_results($fp_sql);
	
	
	foreach ($fp_options as $option)
		{
		echo $option->option_name.'<br>';
		}
	echo '<hr>';
	
	}
	
	



	
	