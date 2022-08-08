<?php
//*****************************************************************************
// Admin settings menu - Frontier Post - General settings
//*****************************************************************************




function frontier_post_admin_page_advanced() 
	{
	
	//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
		wp_die( __('You do not have sufficient permissions to access this page.') );
	
	include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");

	//echo print_r(fp_get_option("fps_custom_post_type_list"), true);
	//echo "<br>";
	
	//****************************************************************************
	// Save settings
	//*******************************************************************************

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ "frontier_isupdated_advanced_hidden" ]) && $_POST[ "frontier_isupdated_advanced_hidden" ] == 'Y' ) 
		{
		if ( !check_admin_referer( 'frontier_post_admin_advanced', 'frontier_post_admin'  ) )
			{
			wp_die(__("Security violation (Nonce check) - Please contact your Wordpress administrator", "frontier-post"));
			}
		
		
		
		$fps_save_general_options = frontier_post_get_settings();
		
		
		foreach($fps_advanced_option_list as $tmp_option_name)
			{
			if ( !key_exists($tmp_option_name, $fps_save_general_options) )
				$fps_save_general_options[$tmp_option_name] = $fps_general_defaults[$tmp_option_name];	
				
			$fps_save_general_options[$tmp_option_name] = isset($_POST[$tmp_option_name]) ? $_POST[$tmp_option_name] : "";
			/*
			if (is_array($fps_save_general_options[$tmp_option_name]))
				{
				echo "Saving. ".$tmp_option_name." - Value: ";
				echo print_r($fps_save_general_options[$tmp_option_name], true);
				echo"<br>";
				}
			else
				echo "Saving. ".$tmp_option_name." - Value: ".$fps_save_general_options[$tmp_option_name]."<br>";
			*/	
			}
		
		if (intval($fps_save_general_options["fps_tag_count"]) == 0)
				$fps_save_general_options["fps_tag_count"] = 3;
		
		update_option(FRONTIER_POST_SETTINGS_OPTION_NAME, $fps_save_general_options);
		
		// *** Add/Remove Frontend Author role ***
		if ( fp_get_option_bool("fps_author_role") )
			{
			// add role if it doesnt exists
			if (!get_role($frontier_author_role_name))
				add_role($frontier_author_role_name, __("Frontend Author"), $frontier_author_default_caps);					
			
			}
		else
			{
			// remove role if it  exists
			if (get_role($frontier_author_role_name))
				remove_role($frontier_author_role_name);					
			
			}
			
		
		// Put an settings updated message on the screen
		echo '<div class="updated"><p><strong>'.__("Settings saved.", 'frontier-post' ).'</strong></p></div>';
				
		} // end save settngs
		
	
	
		
	
	
	//**********************************************************************
	//* Form start
	//**********************************************************************
	
	// Load settings from options	
	$fps_general_options		= frontier_post_get_settings();
	
	
	
	echo '<div class="wrap">';
	echo '<div class="frontier-admin-menu">';
	echo '<h2>'.__("Frontier Post Advanced Settings", "frontier-post").'</h2>';
	echo '<hr>'.__("Documentation", "frontier_post").': <a href="http://wpfrontier.com/frontier-post-advanced-settings/" target="_blank">Advanced settings</a><hr>';
		
	echo '<form name="frontier_post_settings" method="post" action="">';
		echo '<input type="hidden" name="frontier_isupdated_advanced_hidden" value="Y">';
		wp_nonce_field( 'frontier_post_admin_advanced' , 'frontier_post_admin'); 
		
		echo '<table border="1" cellspacing="0" cellpadding="2">';
				
		echo "<tr>";
		
			
			echo "<td>".__("Add Frontier Author user role", "frontier-post")."</td>";
			fps_html_field("fps_author_role", 'checkbox', $fps_general_options, true, 1);
			echo "<td>".__("Adds a new role: Frontend Author to Wordpress", "frontier-post")."</td>";
		
		echo "</tr><tr>";
			echo "<td>".__("Show ID in category list", "frontier-post")."</td>";
			fps_html_field("fps_catid_list", 'checkbox', $fps_general_options, true, 1);
			echo "<td>".__("If checked ID column will be added to the standard category list in admin panel", "frontier-post")."</td>";
		
		echo "</tr><tr>";
			echo "<td>".__("Number of tags to edit on the input form", "frontier-post")."</td>";
			echo "<td></td>";
			if (intval($fps_general_options["fps_tag_count"]) == 0)
				$fps_general_options["fps_tag_count"] = 3;
			fps_html_field("fps_tag_count", 'text', $fps_general_options, true, 1);
			
		echo "</tr><tr>";
				echo "<td>".__("Tag transformation", "frontier-post")."</td>";
				echo "<td></td>";
				fps_html_field("fps_tags_transform", 'select', $fps_general_options, true, 1, $fp_tag_transform_list );
		
		
		echo "</tr><tr>";
			echo "<td>".__("Hide post status", "frontier-post")."</td>";
			fps_html_field("fps_hide_status", 'checkbox', $fps_general_options, true, 1);
			echo "<td>".__("Hide the post status on the entry form", "frontier-post")."</td>";
		
		
		echo "</tr><tr>";
			echo "<td>".__("Input form", "frontier-post")."</td>";
			echo "<td></td>";
			fps_html_field("fps_default_form", 'select', $fps_general_options, true, 1, $frontier_post_forms );
			
		
		echo "</tr><tr>";
			echo "<td>".__("Height of editor", "frontier-post")."</td>";
			echo "<td></td>";
			fps_html_field("fps_editor_lines", 'text', $fps_general_options, true, 1);
		
		echo "</tr><tr>";
				echo "<td>".__("Custom login text", "frontier-post")."</td>";
				fps_html_field("fps_use_custom_login_txt", 'checkbox', $fps_general_options, true);
				echo "<td align='left'>";
					echo '<textarea rows="4" cols="100" name="fps_custom_login_txt">'.stripslashes($fps_general_options['fps_custom_login_txt']).'</textarea>';
					echo '<br>'.__("This text (and link) is displayed when a user is required to login", "frontier-post");
					echo ' - Default text: '.__("Please log in !", "frontier-post").'&nbsp;<a href="'.wp_login_url().'">'.__("Login Page", "frontier-post").'</a>';
				echo "</td>";		
		
		
		echo "</tr><tr>";
			echo "<td>".__("Hide title on these pages", "frontier-post")."</td>";
			echo "<td></td>";
			echo "<td>";
				echo fps_text_field("fps_hide_title_ids",  $fps_general_options['fps_hide_title_ids'], 100);
				echo '<br>'.__("comma separated list of IDs", "frontier-post");
			echo "</td>";		
		
		
		echo "</tr><tr>";
				echo "<td>".__("Allow Custom Taxonomies", "frontier-post")."</td>";
				echo "<td></td>";
					echo "<td><strong>".__("Taxonomies", "frontier-post").":</strong><br>";
					echo fps_checkbox_select_field("fps_custom_tax_list[]", $fps_general_options["fps_custom_tax_list"], fp_get_tax_list())."</td>";
					
		echo "</tr><tr>";
				echo "<td>".__("Default Taxonomy layout", "frontier-post")."</td>";
				echo "<td></td>";
				fps_html_field("fps_default_tax_select", 'select', $fps_general_options, true, 1, array_flip($category_types) );
				
		echo "</tr><tr>";
				echo "<td>".__("Allow shortcode in Post Types", "frontier-post")."</td>";
				echo "<td></td>";
				echo "<td><strong>".__("Post Types", "frontier-post").":</strong><br>";
				echo fps_checkbox_select_field("fps_sc_allowed_in[]", $fps_general_options["fps_sc_allowed_in"], fp_get_post_type_list());
				echo "<strong>".__("It only recommended to allow shortcode in pages, allowing in posts can enable authors to display information they might not have access to !!", "frontier-post")."</strong></td>";
		
		echo "</tr><tr>";
				echo "<td>".__("Custom delete text", "frontier-post")."</td>";
				echo "<td>&nbsp;</td>";
				echo "<td align='left'>";
					echo '<textarea rows="3" cols="100" name="fps_custom_delete_txt">'.stripslashes($fps_general_options['fps_custom_delete_txt']).'</textarea>';
					echo '<br>'.__("This text is displayed when a user is confirming delete of a post", "frontier-post");
				echo "</td>";
		
		echo "</tr><tr>";
				echo "<td>".__("Send email to Admins on post to approve", "frontier-post")."</td>";
				fps_html_field("fps_mail_to_approve", 'checkbox', $fps_general_options, true);
				echo "<td>";
					echo fps_text_field("fps_mail_address", $fps_general_options['fps_mail_address'], 100);
					echo '<br>'.__("Approver email (ex: name1@domain.xx, name2@domain.xx)", "frontier-post");
				echo "</td>";		
			
		echo "</tr><tr>";
				echo "<td>".__("Send email to author when post is approved", "frontier-post")."</td>";
				fps_html_field("fps_mail_approved", 'checkbox', $fps_general_options, true);
				
		echo "</tr><tr>";
				echo "<td>".__("Disable control of Admin Bar", "frontier-post")."</td>";
				fps_html_field("fps_disable_abar_ctrl", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("If this is checked, frontier post will not enable/disable the admin bar (Check this if another plugin is enabling/disabling the admin bar to avoid conflict)", "frontier-post")."</td>";
			
		echo "</tr><tr>";
				echo "<td>".__("Keep Frontier Post settings on uninstall", "frontier-post")."</td>";
				fps_html_field("fps_keep_options_uninstall", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("If this is checked, the Frontier Settings will not be deleted on uninstall", "frontier-post")."</td>";
			
		echo "</tr><tr>";
				echo "<td>".__("Template directory", "frontier-post")."</td>";
				echo "<td></td>";
				echo "<td>";
					echo frontier_template_dir();  
					// check if frontuier post templates are used
					if (locate_template(array('/plugins/frontier-post/'."frontier_post_form_standard.php"), false, true))
						echo "<br /><strong><font color='red'> frontier_post_form_standard.php </font> ".__("exists in the template directory", "fontier-post")."</strong>";
					
					if (locate_template(array('/plugins/frontier-post/'."frontier_post_form_old.php"), false, true))
						echo "<br /><strong><font color='red'> frontier_post_form_old.php </font> ".__("exists in the template directory", "fontier-post")."</strong>";
					
					if (locate_template(array('/plugins/frontier-post/'."frontier_post_form_simple.php"), false, true))
						echo "<br /><strong><font color='red'> frontier_post_form_simple.php </font> ".__("exists in the template directory", "fontier-post")."</strong>";
					
					if (locate_template(array('/plugins/frontier-post/'."frontier_post_form_list.php"), false, true))
						echo "<br /><strong><font color='red'> frontier_post_form_list.php </font> ".__("exists in the template directory", "fontier-post")."</strong>";
					
					if (locate_template(array('/plugins/frontier-post/'."frontier_post_form_page.php"), false, true))
						echo "<br /><strong><font color='red'> frontier_post_form_page.php </font> ".__("exists in the template directory", "fontier-post")."</strong>";
					
					if (locate_template(array('/plugins/frontier-post/'."frontier_post_form_preview.php"), false, true))
						echo "<br /><strong><font color='red'> frontier_post_form_preview.php </font> ".__("exists in the template directory", "fontier-post")."</strong>";
					
					if (locate_template(array('/plugins/frontier-post/'."frontier-post.css"), false, true))
						echo "<br /><strong><font color='red'> frontier-post.css </font>".__("exists in the template directory", "fontier-post")."</strong>";					
				echo "</td>";
				
		echo "</tr><tr>";
			echo "<td>".__("Set Capabilities externally", "frontier-post")."</td>";
				fps_html_field("fps_external_cap", 'checkbox', $fps_general_options, true);
				echo '<td>'.__("If checked capabilities will be managed from external plugin ex.: User Role Editor", "frontier-post").'</td>';
			echo "</tr><tr>";
			if ( fp_get_option_bool("fps_external_cap") )
				{
				echo "<td>".__("Default Editor", "frontier-post")."</td>";
				fps_html_field("fps_default_editor", 'select', $fps_general_options, true, 1, array_flip($editor_types) );
				echo "</tr><tr>";	
				echo "<td>".__("Default category select", "frontier-post")."</td>";
				fps_html_field("fps_default_cat_select", 'select', $fps_general_options, true, 1, array_flip($category_types) );
				echo "</tr><tr>";
				}
		
		echo "</tr><tr>";
				echo "<td>".__("Use tinymce Word count", "frontier-post")."</td>";
				fps_html_field("fps_tinymce_wordcount", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("If this is checked, the tinymce Wordcount will be used instead of standard wordcount", "frontier-post")."</td>";
		
		echo "</tr><tr>";
				$tmp_edit_link_types = array(
							"post"		=> __("Post", "frontier-post"),
							"page"		=> __("Page", "frontier-post"),
							);
				echo "<td>".__("Super Admin Edit Link", "frontier-post")."</td>";
				echo "<td></td>";
				echo "<td><strong>".__("Redirect to Frontier Post edit form for the below post types", "frontier-post").":</strong><br>";
				echo fps_checkbox_select_field("fps_sc_super_admin_types[]", $fps_general_options["fps_sc_super_admin_types"], $tmp_edit_link_types);
				echo "".__("If above post types are checked, the standard Wordpress edit link is changed to point at Frontier Post, as opposed to the backend.", "frontier-post")."</td>";
		
		echo "</tr><tr>";
				echo "<td>".__("Allow empty content in posts", "frontier-post")."</td>";
				fps_html_field("fps_allow_empty_content", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("If this is unchecked, post status will be set to draft, if content is empty", "frontier-post")."</td>";
		/*
		echo "</tr><tr>";
				echo "<td>".__("Force load of media scripts", "frontier-post")."</td>";
				fps_html_field('fps_force_media_load', 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("If this is checked, the media javascript libraries will be loaded on all pages in front-end", "frontier-post")."</td>";
		*/
		
		// moderation
		echo "</tr><tr>";
			echo "<th colspan='3'>".__("Post Moderation", "frontier-post")."	<th>";
		echo "</tr><tr>";
				echo "<td>".__("Activate post moderation", "frontier-post")."</td>";
				fps_html_field("fps_use_moderation", 'checkbox', $fps_general_options, true, 1);
				echo "<td>".__("If this is checked, moderation comments can be edited on the post form", "frontier-post")."</td>";
		
		
		
		echo "</tr><tr>";
				echo "<td>".__("Cache time for taxonomy lists", "frontier-post")."</td>";
				echo "<td></td>";
				fps_html_field("fps_cache_time_tax_lists", 'select', $fps_general_options, true, 1, $fp_cache_time_list );
		
		
		
		echo '</tr></table>';
	
		echo '<p class="submit"><input type="submit" name="Submit" class="button-primary" value="'.__('Save Changes').'"></p>';
	echo '</form>';
	echo '<hr>';
		
	echo '</div>'; //frontier-admin-menu 
	echo '</div>'; //wrap 

	} // end function advanced options
	
	?>