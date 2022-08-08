<?php
/*
Plugin Name: Frontier Post
Plugin URI: http://wordpress.org/extend/plugins/frontier-post/
Description: Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - My Posts Widget.
Version: 5.1.5
Author: finnj
Author URI: http://wpfrontier.com
Text Domain: frontier-post
Domain Path: /language
*/

// define constants ..kk
define('FRONTIER_POST_VERSION', "5.1.5"); 

define('FRONTIER_POST_DIR', dirname( __FILE__ )); //an absolute path to this directory
define('FRONTIER_POST_URL', plugin_dir_url( __FILE__ )); //url path to this directory
define('FRONTIER_POST_TEMPLATE_DIR', get_stylesheet_directory().'/plugins/frontier-post')	; //an absolute path to the template directory
define('FRONTIER_POST_TEMPLATE_URL', get_stylesheet_directory_uri().'/plugins/frontier-post/'); //url path to the template directory


define('FRONTIER_POST_DEBUG', false);

define('FRONTIER_POST_SETTINGS_OPTION_NAME', "frontier_post_general_options");
define('FRONTIER_POST_CAPABILITY_OPTION_NAME', "frontier_post_capabilities");

define('FRONTIER_POST_CACHE_TIME', 15*60); // default cache time

define('FRONTIER_POST_MODERATION_FLAG', "_fp_moderation_flag"); // field name to capture if moderation comments has been added
define('FRONTIER_POST_MODERATION_STATUS', "_fp_moderation_status"); // field name to capture moderation status
define('FRONTIER_POST_MODERATION_DATE', "_fp_moderation_date"); // field name to capture date of last moderation comments
define('FRONTIER_POST_MODERATION_TEXT', "_fp_moderation_text"); // Field name of moderation comments 
define('FRONTIER_POST_MODERATION_EMAIL', "_fp_moderation_email"); // Field name for send email on moderation.

define('FRONTIER_MY_POSTS_W_PREFIX', "fp-my-posts-w-");
define('FRONTIER_MY_APPROVALS_W_PREFIX', "fp-my-approvals-w-");



include("include/frontier_post_defaults.php");
include("include/frontier_post_validation.php");
include("include/frontier_post_util.php");
include("include/frontier_email_notify.php");
include("include/frontier_post_quickpost.php");
include('admin/frontier-post-admin-util.php');
	

include("frontier-list-posts.php");
include("frontier-submit-form.php");
include("frontier-delete-post.php");
include("frontier-approve-post.php");
include("frontier-clone-post.php");
include("frontier-add-edit.php");
include("frontier-preview-post.php");



// Settings menu
include('frontier-post-admin.php');


//widgets	
include("include/frontier_my_posts_widget.php");
include("include/frontier_approvals_widget.php");
include("include/frontier_new_category_post_widget.php");


// ** Cude run on plugin activation **
include("frontier-set-defaults.php");
register_activation_hook( __FILE__ , 'frontier_post_set_defaults');



add_action("init","frontier_get_user_role"); 

add_shortcode("frontier-post","frontier_user_posts");


//**********************************************************************************
// Check upgrade
//
//**********************************************************************************



if ( is_admin() )
	{
	$fp_last_upgrade = fp_get_option('fps_options_migrated_version', get_option("frontier_post_version", '0.0.0'));

	// Upgrade old versions, but dont run upgrade if fresh install
	if ( ($fp_last_upgrade != '0.0.0') && version_compare($fp_last_upgrade, '3.1.0') < 0)
		{
		error_log("Frontier Post: Converting setting from pre version 3");
		include(FRONTIER_POST_DIR."/admin/frontier-post-convert-options.php");
		// run the migration 
		fps_cnv_general_options();
		
		}
	
	// Normal version update to capture new settings etc
	$fp_version = fp_get_option('fps_frontier_post_version', '0.0.0');
	//error_log("Frontier Post: Upgrade check - version:".$fp_version);
		
	// Update defaults, but dont if fresh install - Must be the activation trigger
	// Changed in v 3.5.2, always check for updates
	if (  version_compare(FRONTIER_POST_VERSION, $fp_version, '>' ) )
		{
		
		$fps_save_general_options 	= frontier_post_get_settings();
		$tmp_option_list 			= array_keys($fps_general_defaults);
		
		foreach($tmp_option_list as $tmp_option_name)
			{
			if ( !key_exists($tmp_option_name, $fps_save_general_options) )
				$fps_save_general_options[$tmp_option_name] = $fps_general_defaults[$tmp_option_name];			
			}
		$fps_save_general_options['fps_frontier_post_version'] 	= FRONTIER_POST_VERSION;				
		update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_save_general_options);
		fp_delete_widget_cache();
		}
	}
//**********************************************************************************
// Main program
//
//**********************************************************************************
 

function frontier_user_posts($atts)
	{    
	global $wp_roles;
	global $current_user;
	global $post;
	$sc_allowed_post_types = fp_get_option_array('fps_sc_allowed_in', array("page"));
	

	//new in version 3.6.6, admin can choose wich post types are aloowed
	if ( has_shortcode( $post->post_content, 'frontier-post') && ( in_array($post->post_type, $sc_allowed_post_types) ) )
		{
		if( is_user_logged_in() )
			{  
			
			//if ( !is_page(get_the_id()) )
			if ( !in_array($post->post_type, $sc_allowed_post_types) )
				{
				die('<center><h1>ERROR: '.__("frontier-post Shortcode only allowed in pages", "frontier-post")." (".$post->post_type.")</h1></center>");
				return;         
				}
			
			
			if (isset($_POST['task']))
				{
				$post_task 	= $_POST['task'];
				}
			else
				{
				if (isset($_GET['task']))
					{
					$post_task 	= $_GET['task'];
					}
				else 
					{
					$post_task 	="notaskset";
					}
				}
			
				
			
			$post_action 	= isset($_POST['action']) ? $_POST['action'] : "Unknown";
		
			$fpost_sc_parms = shortcode_atts( array (
				'frontier_mode' 				=> 'none',
				'frontier_parent_cat_id' 		=> 0,
				'frontier_cat_id' 				=> 0,
				'frontier_list_cat_id' 			=> 0,
				'frontier_list_all_posts'		=> 'false',
				'frontier_list_pending_posts'	=> 'false',
				'frontier_list_draft_posts'		=> 'false',
				'frontier_list_sort'			=> 'post_date',
				'frontier_list_sort_order'		=> 'DESC',
				'frontier_list_text_before'		=> '',
				'frontier_edit_text_before'		=> '',
				'frontier_myid'					=> $post->ID,
				'frontier_page_id'				=> $post->ID,
				'frontier_return_text'			=> __("Save & Return", "frontier-post"),
				'frontier_add_link_text'		=> '',
				'frontier_add_post_type'		=> 'post',
				'frontier_list_post_types'		=> 'post',
				'frontier_custom_tax'			=> '',
				'frontier_custom_tax_layout'	=> '',
				'frontier_edit_form'			=> fp_get_option("fps_default_form", "standard"),
				'frontier_editor_height'		=> fp_get_option_int("fps_editor_lines", 300),
				'frontier_quick_editor_height'	=> fp_get_option_int("fps_quick_editor_lines", 200),
				'frontier_list_form'			=> fp_get_option("fps_default_list", "list"),
				'fps_cache_time_tax_lists'		=> fp_get_option_int("fps_cache_time_tax_lists", 30*60),
				'frontier_pagination'			=> 'true',
				'frontier_ppp'					=> (int) fp_get_option('fps_ppp',5),
				'frontier_user_status'			=> '',
				'frontier_force_quickpost'		=> 'false'
				
				), $atts );
		
		
			// support for link (url) based set of category - If category set in shortcode it will prevail.
			if ( $post_task == "new" && isset($_GET['frontier_cat_id']) && intval(isset($_GET['frontier_cat_id']))>0 )
				$fpost_sc_parms['frontier_cat_id'] = intval($_GET['frontier_cat_id']);
		
			// support for url link based creation of custom post types
			if ( $post_task == "new" && isset($_GET['frontier_add_post_type']) )
				{
				$tmp_post_type =  $_GET['frontier_add_post_type'];
				$tmp_post_type_list = fp_get_option_array('fps_custom_post_type_list', array());
				if ( in_array($tmp_post_type , $tmp_post_type_list) )
					$fpost_sc_parms['frontier_add_post_type'] = $tmp_post_type;
				}
		
			// Remove quotes from post type
			$fpost_sc_parms['frontier_add_post_type'] = str_replace("'", "", $fpost_sc_parms['frontier_add_post_type']);
			$fpost_sc_parms['frontier_add_post_type'] = str_replace('"', '', $fpost_sc_parms['frontier_add_post_type']);
		
			//If Category parsed from widget assign it instead of category from shortcode
			if ( isset($_GET['frontier_new_cat_widget']) && $_GET['frontier_new_cat_widget'] == "true" )
				{
				$_REQUEST['frontier_new_cat_widget'] = "true";
				$fpost_sc_parms['frontier_cat_id'] = isset($_GET['frontier_cat_id']) ? $_GET['frontier_cat_id'] : 0;
				}
			
			//Change Categories to array
			$fpost_sc_parms['frontier_cat_id'] = fp_list2array($fpost_sc_parms['frontier_cat_id']);
			$fpost_sc_parms['frontier_list_cat_id'] = fp_list2array($fpost_sc_parms['frontier_list_cat_id']);
			$fpost_sc_parms['frontier_list_post_types'] = fp_list2array($fpost_sc_parms['frontier_list_post_types']);
			$fpost_sc_parms['frontier_custom_tax'] = fp_list2array($fpost_sc_parms['frontier_custom_tax']);
			$fpost_sc_parms['frontier_custom_tax_layout'] = fp_list2array($fpost_sc_parms['frontier_custom_tax_layout']);
			$fpost_sc_parms['frontier_user_status'] = fp_list2array($fpost_sc_parms['frontier_user_status']);
		
			extract($fpost_sc_parms);
		
			// if mode is add, go directly to show form - enables use directly on several pages
			if ($frontier_mode == "add" && $post_task != 'delete')
				$post_task = "new";
		
			//**************************************************************
			// QUICK Post
			//**************************************************************
			if ($fpost_sc_parms['frontier_mode'] == "quickpost")
				{
				$post_task = "quickpost";
				}
			
			ob_start();
		
			switch( $post_task )
				{
				
				case 'quickpost':
					if ( $post_action == "wpfrtp_save_post" )
						frontier_posting_form_submit($fpost_sc_parms);
					else	
						frontier_quickpost($fpost_sc_parms);
					break;
					
				case 'new':
					if ( $post_action == "wpfrtp_save_post" )
						frontier_posting_form_submit($fpost_sc_parms);
					else	
						frontier_post_add_edit($fpost_sc_parms);
					break;
			
				case 'edit':
					if ( $post_action == "wpfrtp_save_post" )
						frontier_posting_form_submit($fpost_sc_parms);
					else	
						frontier_post_add_edit($fpost_sc_parms);
					break;
			
				case 'delete':
					if ( $post_action == "wpfrtp_delete_post" )
						frontier_execute_delete_post($fpost_sc_parms);
					else
						frontier_prepare_delete_post($fpost_sc_parms);
					break;    
			
				case 'approve':
					if ( $post_action == "wpfrtp_approve_post" )
						frontier_execute_approve_post($fpost_sc_parms);
					else	
						frontier_prepare_approve_post($fpost_sc_parms);
					break;  
				
				case 'clone':
					frontier_clone_post($fpost_sc_parms);
					break;  
			
				default:
					frontier_user_post_list($fpost_sc_parms);
					break;
				}

			//return content to shortcode for output
			$fp_content = ob_get_contents();
			ob_end_clean();
			return $fp_content;
			}
		else
			{
			echo fp_login_text();
			} // user_logged_in
		}
		else
		{
			//Shortcode called from enything else than page, not allowed
			if ( !in_array($post->post_type, $sc_allowed_post_types) && is_singular() )
				{
				// Only show warning if single post
				$sing = is_singular() ? "S" : "M";
				echo '<br><div id="frontier-post-alert">frontier-post shortcode '.__("only allowed in", "frontier-post").': '.implode(", ",$sc_allowed_post_types).' - This post type: ('.$post->post_type.') - ('.$post->ID.'/'.$sing.')</div><br>';
				return;
				}
		} // has_shortcode
	
	
    } // end function frontier_user_posts



//*******************************************************************************************	
// Shorcode to show users capabilities.
//*******************************************************************************************

add_shortcode("fp-capability","frontier_post_show_capabilities");

function frontier_post_show_capabilities()
	{
	if( !is_user_logged_in() )
		{
		echo "<h3>Login required ! </h3>";
		return;
		}
		
	$tmp_user_id = 0;	
	if ( current_user_can( 'manage_options' ) )
		{
	
		echo '<form action="" method="post" name="frontier-post-caps" id="frontier-post-caps" enctype="multipart/form-data" >';
			echo "Show capabilities for user: ";
			wp_dropdown_users( array("name" => "fp_user_id") );
			echo "&nbsp;&nbsp;";
			echo '<button class="button" type="submit" name="user_show_cap" id="user_show_cap" 	value="frontier-post-show-caps">'.__("Show capabilities", "frontier-post").'</button>';
		echo '</form>';
		echo '<br>';
	
	
		if ( array_key_exists("fp_user_id", $_POST) )
			$tmp_user_id = intval($_POST["fp_user_id"]);
		else
			$tmp_user_id = get_current_user_id();
			
		}
	else
		{
		$tmp_user_id = get_current_user_id();
		echo "<br>Only subset of capabilities is shown - Administrators will see all<br>";
		}
	
	
	
	
	if( is_user_logged_in() )
		{
		$udata = get_userdata( $tmp_user_id );
 
		if ( is_object( $udata) ) 
			{
			echo '<br><h3>'.$udata->display_name.' capabilities</h3><br>';
			$xcaps = $udata->allcaps;
	 		//echo "<pre>".print_r($xcaps,true)."</pre>"; 
			
	 		ksort($xcaps);
	 		foreach($xcaps as $tmp_cap_name => $tmp_cap)
				{
				if ( strpos($tmp_cap_name, "rontier_post") == 1 || $tmp_cap_name == "edit_posts" || $tmp_cap_name == "upload_files")
					{
					echo '<strong>'.$tmp_cap_name.'</strong><br>';
					}
				else
					{
					if (current_user_can( 'manage_options' ))
						echo $tmp_cap_name.'<br>';
					}
				}
			echo '<hr>';
			}
		else
			{
			echo "Unable to get capabilities (No object present)!<br>";
			}
		}
	else
		{
		echo "Unable to determine logged in user!<br>";
		}
	return;
	}



//*******************************************************************************************	
// Load plugin teplates functions	
//*******************************************************************************************

	
function frontier_template_dir()
	{
 	// get frontier dir in theme or child-theme	
	return get_stylesheet_directory().'/plugins/frontier-post/';		
	}	
	
function frontier_load_form($frontier_form_name)
	{
 	// Check if template is located in theme or child-theme
	$located = locate_template(array('/plugins/frontier-post/'.$frontier_form_name), false, true);
	
	if(!$located )
		{
		// if not found in theme folders, load native fronpier form
		$located = FRONTIER_POST_DIR."/forms/".$frontier_form_name;
		}
	
	return $located;		
	}

//*******************************************************************************************	
// Load css from plugin form directory in theme if exists - And add version	
//*******************************************************************************************

function frontier_enqueue_scripts()
	{
 	// Check if css is located in theme or child-theme
	$located = locate_template(array('plugins/frontier-post/frontier-post.css'), false, false);
	
	
	if($located )
		{
		$located = get_stylesheet_directory_uri().'/plugins/frontier-post/frontier-post.css';
		}
	else
		{
		// if not found in theme folders, load native frontier form
		$located = plugins_url('frontier-post/frontier-post.css');
		}
	
	wp_enqueue_style('frontierpost', $located, '', FRONTIER_POST_VERSION);
	
	} 

add_action("wp_enqueue_scripts","frontier_enqueue_scripts");  

function fp_enqueue_admin_scripts()
	{
	wp_register_style( 'frontier-post-admin.css', plugins_url('frontier-post/admin/frontier-post-admin.css'), false, FRONTIER_POST_VERSION );
	wp_enqueue_style( 'frontier-post-admin.css' );
	}
add_action( 'admin_enqueue_scripts', 'fp_enqueue_admin_scripts' );


//***********************************************************************************
//* Settings and documentation link on plugin list
//***********************************************************************************	

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'fp_plugin_action_links' );

function fp_plugin_action_links( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, '?page=frontier_admin_menu') ) .'">Settings</a>';
   $links[] = '<a href="http://wpfrontier.com" target="_blank">Documentation</a>';
   return $links;
}
	

//*******************************************************************************************	
// Load tinymce wordcount
//*******************************************************************************************
if (fp_get_option_bool('fps_tinymce_wordcount')) 
	add_filter('mce_external_plugins', 'fp_tinymce_wordcount');
else
	remove_filter('mce_external_plugins', 'fp_tinymce_wordcount');

function fp_tinymce_wordcount($plugins_array = array()) 
	{
	$plugins = array('wordcount'); 
	//Build the response - the key is the plugin name, value is the URL to the plugin JS
	foreach ($plugins as $plugin ) 
		{
		$plugins_array[ $plugin ] = plugins_url('tinymce/', __FILE__) . $plugin . '/plugin.min.js';
		}
	return $plugins_array;
	}
	

//*******************************************************************************************	
// Get user role	
//*******************************************************************************************

	
function frontier_get_user_role() 
	{
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	return $user_role ? $user_role : 'unkown';
	}
	
//*******************************************************************************************	
// Link for Frontier add post	
//*******************************************************************************************


function frontier_post_add_link($tmp_p_id = null, $tmp_cat_id = null) 
	{
	$url = '';
	$concat= get_option("permalink_structure")?"?":"&";    
	//set the permalink for the page itself if not parsed
	if ( !isset($tmp_p_id) )
		$tmp_p_id = fp_get_option('fps_page_id');
	
	$frontier_permalink = get_permalink($tmp_p_id);
	$url = $frontier_permalink.$concat."task=new";
	if ( isset($tmp_cat_id) && $tmp_cat_id > 0 )
		$url = $url."&frontier_cat_id=".$tmp_cat_id;
	
	return $url;
	} 	

//*******************************************************************************************	
// Delete cache for My Approvals widget, when post are cnaging to/from pending status
//*******************************************************************************************

function frontier_post_reset_my_approval_cache( $new_status, $old_status, $post ) {
    //error_log("Old status: ".$old_status." - New status: ".$new_status." - Post id: ".$post->ID);
    if ( $new_status == "pending" || $old_status == "pending" ) 
    	{
    	//error_log("delete cache");
        fp_delete_cache_names(FRONTIER_MY_APPROVALS_W_PREFIX);
        }
}
add_action(  'transition_post_status',  'frontier_post_reset_my_approval_cache', 10, 3 );

//*******************************************************************************************	
// Hide admin bar for user role based on settings
//*******************************************************************************************

function frontier_admin_bar()
	{
	$tmp_fp_settings = get_option("frontier_post_general_options", array());
	// check if enable/disable adminbar functionality has been disabled for all users
	if (!array_key_exists("fps_disable_abar_ctrl", $tmp_fp_settings) || $tmp_fp_settings["fps_disable_abar_ctrl"] != "true")
		{
		if (!current_user_can( 'frontier_post_show_admin_bar' ))
			show_admin_bar(false);
		else
			show_admin_bar(true);
		}
	}
add_action("init","frontier_admin_bar");  

//*******************************************************************************************
// Redirect standard link for edit post from backend (admin interface) to frontend
//*******************************************************************************************

function frontier_edit_post_link( $url, $post_id ) 
	{
	
	// Do not change edit link if called from admin panel
	if ( is_admin() && strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin' ) !== false)
		return $url;
	
	$tmp_post_type 		= get_post_type($post_id);
	
	// Do not change edit link if not page or post
	if ( !in_array($tmp_post_type, array("post", "page")) )
		return $url;
	
	
	// Check specific settings for super admin (multisite) 
	if ( current_user_can('manage_network_options' ) )
		{
		$tmp_sadmin_types 	= fp_get_option_array('fps_sc_super_admin_types');
		
		if ( !in_array($tmp_post_type, $tmp_sadmin_types) )
			return $url;	
		}
	
	// Do not change edit link if page and user camt edit page using Frontier Post
	if ( $tmp_post_type == "page" && !current_user_can('frontier_post_can_page') )
		return $url;
		
	if ( current_user_can( 'frontier_post_redir_edit' )	)
		{
		$frontier_edit_page = (int) fp_get_option('fps_page_id');
		$url = '';
		$concat= get_option("permalink_structure")?"?":"&";    
		//set the permalink for the page itself
		$frontier_permalink = get_permalink($frontier_edit_page);
		$url = $frontier_permalink.$concat."task=edit&postid=".$post_id;
		}
	return $url;
    }

add_filter( 'get_edit_post_link', 'frontier_edit_post_link', 10, 2 );

//***********************************************************************************
//* Hide page title on specific pages - Only activate filter if there is any pages to hide (perfomance)
//***********************************************************************************	


function frontier_post_hide_title($fp_tmp_title, $fp_tmp_id = 0)
	{
	$fp_tmp_id = (int) $fp_tmp_id;
	
	// only execute and hide title if id been parsed, if it is a page and if the page is in the list of pages where title should be hidden.... 
	if ( $fp_tmp_id > 0 && is_page($fp_tmp_id))
		{
	
		$fp_tmp_id_list = explode(",", fp_get_option("fps_hide_title_ids", ""));
		if (in_array($fp_tmp_id, $fp_tmp_id_list) )
			{
			$fp_tmp_title = "";
			}
		}
	return $fp_tmp_title;
	}
	
$fp_tmp_id_list = explode(",", fp_get_option("fps_hide_title_ids", ""));

if ( (count($fp_tmp_id_list) > 0) && ( (int) $fp_tmp_id_list[0] > 0) )
	add_filter("the_title", "frontier_post_hide_title", 99, 2);
	
//***********************************************************************************
//* Add Id to Category list
//***********************************************************************************	

if ( fp_get_option("fps_catid_list", "false") == "true" )
	{
	function frontier_add_categoryid_list($columns) 
		{
		$tmp = array( "cat_id" => "ID" );
		$columns = array_merge($columns, $tmp);
		//$columns['catID'] = __('ID');
		return $columns;
		}

	function frontier_add_categoryid_row( $value, $name, $cat_id )
		{
		if( $name == 'cat_id' ) 
			echo $cat_id;
		}

	function frontier_post_cat_column_width()	
		{
		// Tags page, exit earlier
		if( $_GET['taxonomy'] != 'category' )
			return;
		echo '<style>.column-cat_id {width:5%}</style>';
		}
		
	add_filter( 'manage_edit-category_columns', 'frontier_add_categoryid_list' );
	add_filter( 'manage_category_custom_column', 'frontier_add_categoryid_row', 10, 3 );
	add_action( 'admin_head-edit-tags.php', 'frontier_post_cat_column_width' );
	}
	
//***********************************************************************************
//* Post media fixes
//***********************************************************************************	

function frontier_media_fix( $post_id ) 
	{
	global $frontier_post_id;
	global $post_ID; 
	
	/* WordPress 3.4.2 fix */
	$post_ID = $post_id; 
	
	// WordPress 3.5.1 fix
	$frontier_post_id = $post_id;	
    add_filter( 'media_view_settings', 'frontier_media_fix_filter', 10, 2 ); 
	} 


	
//Fix insert media editor button filter
 
function frontier_media_fix_filter( $settings, $post ) 
	{
	global $frontier_post_id;
	
    $settings['post']['id'] = $frontier_post_id;
	
	return $settings;
	} 	



//add translation files
function frontier_post_init() 
	{
	load_plugin_textdomain('frontier-post', false, dirname( plugin_basename( __FILE__ ) ).'/language');
	}
	
add_action('plugins_loaded', 'frontier_post_init');



?>