<?php
//*****************************************************************************
// Back-up and restore plugin options
//*****************************************************************************

//****************************************************************************
// EXPORT settings
//*******************************************************************************

function frontier_post_export_settings()
	{
	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ "frontier_post_action" ]) && $_POST[ "frontier_post_action" ] == 'export_settings' ) 
		{
		if( ! wp_verify_nonce( $_POST['frontier_post_export_nonce'], 'frontier_post_export_nonce' ) )
			wp_die( __('Security check failed (nonce)') );
		
		if( ! current_user_can( 'manage_options' ) )
			wp_die( __('You do not have sufficient permissions to access this page.') );
		
		$fp_export_capabilities 	= frontier_post_get_capabilities();
		$fp_export_settings			= frontier_post_get_settings();
		
		$fp_export_settings['export_site_name']	= get_bloginfo( 'name' );
		$fp_export_settings['export_site_url'] 	= network_site_url( '/' );
		$fp_export_settings['export_time'] 		= mysql2date('Y-m-d H:i',current_time( 'timestamp') );
		
		unset($fp_export_settings['fps_custom_login_txt']);
		
		//$settings = get_option( 'pwsix_settings' );
	
		// combine settings and capabilities in one file
		
		$fp_export_option = array();
		
		$fp_export_option['fp_settings'] 		= $fp_export_settings;
		$fp_export_option['fp_capabilities'] 	= $fp_export_capabilities;
		
	
		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=frontier-post-settings-export-' . date( 'Y-m-d' ) . '.json' );
		header( "Expires: 0" );
		echo json_encode( $fp_export_option );
		exit;
		}
	}
add_action( 'admin_init', 'frontier_post_export_settings' );



//****************************************************************************
// IMPORT settings
//*******************************************************************************

function frontier_post_import_settings() 
	{
	if( isset($_POST[ "frontier_post_action" ]) && $_POST[ "frontier_post_action" ] == 'import_settings' ) 
		{
		if( ! wp_verify_nonce( $_POST['frontier_post_import_nonce'], 'frontier_post_import_nonce' ) )
			wp_die( __('Security check failed (nonce)') );
		
		if( ! current_user_can( 'manage_options' ) )
			wp_die( __('You do not have sufficient permissions to access this page.') );
		
		$extension = end( explode( '.', $_FILES['import_file']['name'] ) );
		
		if( $extension != 'json' )
			wp_die( __( 'Please upload a valid .json file' ) );
			
		$import_file = $_FILES['import_file']['tmp_name'];
	
		if( empty( $import_file ) ) 
			wp_die( __( 'Please upload a file to import' ) );
		
		// Retrieve the settings from the file and convert the json object to an array.
		$fp_export_option = (array) json_decode( file_get_contents( $import_file ), true );
		
		// Keep existing settings
		$orig_settings	= frontier_post_get_settings();
		
		$settings 		= (array) $fp_export_option['fp_settings'];
		$capabilities 	= (array) $fp_export_option['fp_capabilities'];
		
		/*
		error_log("IMPORT FILE");
		error_log(print_r($fp_export_option,true));
		
		error_log("SETTINGS");
		error_log(print_r($settings,true));
		
		error_log("CAPABILITIES");
		error_log(print_r($capabilities,true));
		*/
		
		//exit;
		
		
		/*
		//remove site specific settings
		unset($settings['fps_page_id']);
		unset($settings['fps_pending_page_id']);
		unset($settings['fps_draft_page_id']);
		unset($settings['fps_hide_title_ids']);
		unset($settings['fps_excl_cats']);
		unset($settings['fps_custom_login_txt']);
		*/
		
		
		
		// Save settings
		update_option( FRONTIER_POST_SETTINGS_OPTION_NAME, $settings );
		
		// restore site specific values 
		
		$settings		= frontier_post_get_settings();
		
		$settings['fps_page_id']			= $orig_settings['fps_page_id'];
		$settings['fps_pending_page_id']	= $orig_settings['fps_pending_page_id'];
		$settings['fps_draft_page_id']		= $orig_settings['fps_draft_page_id'];
		$settings['fps_hide_title_ids']		= $orig_settings['fps_hide_title_ids'];
		$settings['fps_excl_cats']			= $orig_settings['fps_excl_cats'];
		$settings['fps_custom_login_txt']	= $orig_settings['fps_custom_login_txt'];
		
		// Save settings
		update_option( FRONTIER_POST_SETTINGS_OPTION_NAME, $settings );
		
		// Save capabilities
		update_option( FRONTIER_POST_CAPABILITY_OPTION_NAME, $capabilities );
		
		// Delete cache
		fp_delete_widget_cache();
		fp_delete_cache_names("frontier_post");
		
		error_log("Frontier Post - Settings imported");
		
		
		/*
		// Put an settings updated message on the screen
		echo '<div class="updated"><p><strong>'.__('Settings saved.', 'frontier-post' ).'</strong></p></div>';
		
		add_action('admin_notices', 'fp_import_notice');
		function fp_import_notice()
			{
			echo '<div class="updated"><p><strong>'.__("Plugin: Frontier Post, settings imported from file - Please review settings", "frontier-post").'</strong></p></div>';
			}
		
		*/
		
		wp_safe_redirect( admin_url( 'admin.php?page=frontier_post_admin_option_exp' ) ); 
		
		
		exit;
		}
	}
add_action( 'admin_init', 'frontier_post_import_settings' );


//****************************************************************************
// Input form
//*******************************************************************************


function frontier_post_admin_backup_options() 
	{
	
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
	
	echo '<strong>Frontier Post version: '.FRONTIER_POST_VERSION.'</strong>';


	
	// ****************************************************************************
	// Show export options
	//*******************************************************************************
	echo '<div class="frontier-post-admin-menu">';
	
	
	
	echo '<h2>'.__("Frontier Post - Export", "frontier-post").'</h2>';
	echo '<form method="post">';
	echo '<table>';
	echo '<tr>';
		echo '<td>'.__("Export Frontier Post Settings","frontier-post").'</td>';
		echo '<td><input type="hidden" name="frontier_post_action" value="export_settings" />';
		wp_nonce_field( 'frontier_post_export_nonce', 'frontier_post_export_nonce' ); 
		submit_button( __( 'Export' ), 'secondary', 'submit', false ); 
		echo '</td>';
	echo '</tr></table>';	
	echo '</form>';
	
	
	//**********************************************************************
	// Import	
	//**********************************************************************
	
	echo '<br>';
	echo '<div id="frontier-post-admin-alert">';
	echo __("This is BETA, and IMPORT should be used with CAUTION (No validation checks implemented)", "frontier-post");
	echo "</div>";
	echo '<br>';
	echo '<h2>'.__("Frontier Post - Import", "frontier-post").'</h2>';
	
	echo '<form method="post" enctype="multipart/form-data">';
	echo '<table>';
	echo '<tr>';
		echo '<td>'.__("Import Frontier Post Settings","frontier-post").": ".'<input type="file" name="import_file"/>'.'</td>';
		echo '<td><input type="hidden" name="frontier_post_action" value="import_settings" />';
		wp_nonce_field( 'frontier_post_import_nonce', 'frontier_post_import_nonce' ); 
		submit_button( __( 'import' ), 'secondary', 'submit', false ); 
		echo '</td>';
	echo '</tr>';
	
	echo '</table>';	
	echo '</form>';
	
	echo "<br>";
	echo '<div id="frontier-post-admin-alert">';
	echo __("After import you will need to review and save", "frontier-post").":<br>";
	echo "- ".__("Frontier Post Settings", "frontier-post")."<br>";
	echo "- ".__("Frontier Post Capabilities and role based settings", "frontier-post")."<br>";
	echo "- ".__("Frontier Post Advanced Settings", "frontier-post")."<br>";
	echo "- ".__("Following fields are not imported", "frontier-post").": (";
	echo "fps_page_id, fps_pending_page_id, fps_draft_page_id, fps_hide_title_ids, fps_excl_cats, fps_custom_login_txt)<br>";
	echo "</div>";
	echo "<br><br>";
	
	$fps_general_options	= frontier_post_get_settings();
	$saved_capabilities 	= frontier_post_get_capabilities();
	
	
	echo '<h2>Settings - Stored values </strong></h2>';
	echo '<hr>';
	echo '<table border="1" cellpadding="2" cellspacing="4"><tr><th>key</th><th>Value</th></tr>';
	
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
	
	echo '<h2>Capabilities - Stored values </strong></h2>';
	echo '<hr>';
	echo '<table border="1" cellpadding="2" cellspacing="4"><tr><th>key</th><th>Value</th></tr>';
	
	foreach($saved_capabilities as $key => $value)
		{
		echo '<tr>';
		echo '<td>'.$key.'</td>';
		if (is_array($value))
			{
			echo '<td>';
			foreach($value as $subkey => $subvalue)
				{
				echo $subkey.' --> '.print_r($subvalue, true).'<br>';
				}
			echo '</td>';
			//echo '<td>'.print_r($value, true).'</td>';
			}
		else
			echo '<td>'.$value.'</td>';
		
		echo '</tr>';
		}
	echo '</table>';
	
	
	echo '</div>'; //frontier-admin-menu 

		


	} // end function frontier_post_admin_backup_options
	
	?>