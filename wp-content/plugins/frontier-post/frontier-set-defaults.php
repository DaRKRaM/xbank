<?php


function frontier_post_set_defaults()
	{
	
	error_log("Frontier Post - Plugin activation");
	
	if (!defined('FRONTIER_POST_SETTINGS_OPTION_NAME'))
		{
		define('FRONTIER_POST_SETTINGS_OPTION_NAME', "frontier_post_general_options");
		}
	if (!defined('FRONTIER_POST_CAPABILITY_OPTION_NAME'))
		define('FRONTIER_POST_CAPABILITY_OPTION_NAME', "frontier_post_capabilities");
	
	include(FRONTIER_POST_DIR.'/include/frontier_post_defaults.php');	
	
	

	
	$fp_last_upgrade = fp_get_option('fps_options_migrated_version', get_option("frontier_post_version", '0.0.0'));
	
	//error_log("Frontier Post - Plugin activation - last upgrade".$fp_last_upgrade);
	
	// Upgrade old versions, but dont run upgrade if fresh install
	if ( ($fp_last_upgrade != '0.0.0') && version_compare($fp_last_upgrade, '3.3.0') < 0)
		{
		include(FRONTIER_POST_DIR.'/admin/frontier-post-convert-options.php');
		fps_cnv_general_options(true);
		$fp_upgrade_msg = 'Frontier Post - Settings upgraded from version: '.$fp_last_upgrade.' to version: '.FRONTIER_POST_VERSION;
		error_log($fp_upgrade_msg);
		}
	else
		{
		
		//******************************************************************************
		// add settings if not already there
		//******************************************************************************
		//error_log("Frontier Post - Plugin activation - Add new settings");
		if (!fp_get_option_bool('fps_keep_options_uninstall', false))
			{
			error_log("Frontier Post - Plugin activation - Setting new capabilities");
			
			// Set default capabilities
			$saved_capabilities = frontier_post_get_capabilities();
				
			// administrators capabilities
			$tmp_administrator_cap = array(
				'frontier_post_can_add' 		=> 'true', 
				'frontier_post_can_edit' 		=> 'true', 
				'frontier_post_can_delete' 		=> 'true', 
				'frontier_post_can_publish'		=> 'true',  	
				'frontier_post_can_draft' 		=> 'true',  
				'frontier_post_can_pending' 	=> 'true',  
				'frontier_post_can_private' 	=> 'true', 	
				'frontier_post_redir_edit' 		=> 'true', 
				'frontier_post_show_admin_bar' 	=> 'true',  	
				'frontier_post_exerpt_edit' 	=> 'true',  
				'frontier_post_tags_edit' 		=> 'true',  
				'frontier_post_can_media'		=> 'true', 
				'frontier_post_can_page'		=> 'true', 
				'fps_role_editor_type'		 	=> 'full',
				'fps_role_category_layout'		=> 'multi',
				'fps_role_default_category'		=> get_option("default_category"),
				'fps_role_allowed_categories' 	=> '',
			
				);
		
			// editor
			$tmp_editor_cap 	= $tmp_administrator_cap;
		
			// Author
			$tmp_author_cap 	= $tmp_editor_cap;
		
			$tmp_author_cap['frontier_post_can_private']		= 'false';
			$tmp_author_cap['frontier_post_show_admin_bar']		= 'false';
			$tmp_author_cap['frontier_post_can_page']			= 'false';
		
			// Contributor
			$tmp_contributor_cap 	= $tmp_author_cap;
		
			$tmp_contributor_cap['frontier_post_can_delete']	= 'false';
			$tmp_contributor_cap['frontier_post_can_publish']	= 'false';
			$tmp_contributor_cap['frontier_post_redir_edit']	= 'false';
			$tmp_contributor_cap['frontier_post_tags_edit']		= 'false';
			$tmp_contributor_cap['frontier_post_can_media']		= 'false';
			$tmp_contributor_cap['fps_role_editor_type']		= 'minimal-visual';
		
			// Subscriber
			$tmp_subscriber_cap 	= $tmp_contributor_cap;
		
			$tmp_subscriber_cap['frontier_post_can_add']		= 'false';
			$tmp_subscriber_cap['frontier_post_can_edit']		= 'false';
			$tmp_subscriber_cap['frontier_post_can_pending']	= 'false';
			$tmp_subscriber_cap['frontier_post_can_draft']		= 'false';
		
		
		
			$wp_roles			= new WP_Roles();
			$roles 	  			= $wp_roles->get_names();
		
			$saved_capabilities = frontier_post_get_capabilities();
		
		
		
			foreach( $roles as $key => $item ) 
				{
			
				switch ($key)
					{
					case 'administrator':
						$tmp_cap_list = $tmp_administrator_cap;
						break;
					
					case 'editor':
						$tmp_cap_list = $tmp_editor_cap;
						break;
					
					case 'author':
						$tmp_cap_list = $tmp_author_cap;
						break;
					
					case 'frontier-author':
						$tmp_cap_list = $tmp_author_cap;
						break;
					
					case 'contributor':
						$tmp_cap_list = $tmp_contributor_cap;
						break;
				
					case 'subscriber':
						$tmp_cap_list = $tmp_subscriber_cap;
						break;	
					
					default:
						$tmp_cap_list = $tmp_contributor_cap;
						break;
					}
				
				$saved_capabilities[$key] = $tmp_cap_list;
			
				} // roles
			
			// Save options
			update_option(FRONTIER_POST_CAPABILITY_OPTION_NAME, $saved_capabilities);
		
		
	
			} // end update settings if not saved from during previous uninstall
		} //end Upgrade or not
		
		
		
	// update default settings
	fp_post_set_defaults();
	

	// Set Wordpress capabilities
	frontier_post_set_cap();
	
	
	
	
	//*******************************************************************************************************
	// Check if pages are present, if not create them.
	//*******************************************************************************************************
	
	// make sure we have loaded last bversion of the options.
	$fps_general_options = get_option(FRONTIER_POST_SETTINGS_OPTION_NAME);
	
	function fp_create_page($tmp_name, $tmp_sc_name)
		{
		global $wpdb;	
		$tmp_id = $wpdb->get_var(
			"SELECT id 
			  FROM $wpdb->posts 
			  WHERE post_type='page' AND 
			  post_status='publish' AND 
			 post_content LIKE '%[".$tmp_sc_name."%'
			");
	
		if ( ((int)$tmp_id) <= 0)
			{
			// Add new page
			$my_page = array(
					 'post_title' 		=> $tmp_name,
					 'post_content' 	=> "[".$tmp_sc_name."]",				 
					 'post_status' 		=> 'publish',
					 'comment_status' 	=> 'closed',
					 'post_type' 		=> 'page',
					);
				
			// Insert the page into the database
			$tmp_id = wp_insert_post( $my_page );
			return $tmp_id;
			}
		return intval($tmp_id);
		
		} // end function create page
		
	
	
	$tmp_id = fp_create_page(__('My Posts', 'frontier-post'), "frontier-post");
	$fps_general_options ['fps_page_id'] 	= $tmp_id;			
		
	$tmp_id = fp_create_page(__('Pending Posts', 'frontier-post'), 'frontier-post frontier_list_pending_posts="true"');
	$fps_general_options ['fps_pending_page_id'] 	= $tmp_id;			
	
	$tmp_id = fp_create_page(__('Draft Posts', 'frontier-post'), 'frontier-post frontier_list_draft_posts="true"');
	$fps_general_options ['fps_draft_page_id'] 	= $tmp_id;			
	
	
	update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_general_options );
	
	
	
	//save to options that capabilities has been migrated
	$fps_general_options = frontier_post_get_settings();
	$fps_general_options['fps_options_migrated'] = "true";
	$fps_general_options['fps_options_migrated_version'] = FRONTIER_POST_VERSION;
	$fps_general_options['fps_options_activation_date'] = date('j. F Y  H:i');
	
	update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_general_options);
	
	
	// Check if plugin Classic Editor is activated
	$tmp_msg =  __('Classic Editor plugin is not activated, this is recommended for Frontier Post to function optimally', 'frontier-post');
	//error_log($tmp_msg);

	function check_classic_editor_error() 
		{
		$class = 'notice notice-error';
		$message =  __('Classic Editor plugin is not activated, this is recommended for Frontier Post to function optimally', 'frontier-post');
	
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		}
	
	global $wp_version;
	if ( (version_compare($wp_version, '5.0.0') <= 0) && !is_plugin_active('classic-editor/classic-editor.php') )
		{
		add_action( 'admin_notices', 'check_classic_editor_error' );
		}
	


	
	} // end function



?>