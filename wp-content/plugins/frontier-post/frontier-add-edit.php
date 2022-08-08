<?php

function frontier_post_add_edit($fpost_sc_parms = array(), $fp_use_quickpost = false)
	{
	require_once(ABSPATH . '/wp-admin/includes/post.php');
	global $current_user;
	//global $wpdb;
	global $fps_access_check_msg;
	//Reset access message
	$fps_access_check_msg = "";
	
	$frontier_permalink = get_permalink();
	$concat				= get_option("permalink_structure")?"?":"&";
	
	
	$user_can_edit_this_post 	= false;
	
	
	//Get Frontier Post capabilities
	$fp_capabilities	= frontier_post_get_capabilities();
	
	/*
	if( isset($_REQUEST['task']) )
		echo "Task: ".$_REQUEST['task']."<br>";
	else
		echo "No task set<br>";
	*/	
	
	
	if (!is_user_logged_in())
		{
		// stop and display message
		echo fp_login_text();
		}
	else	
		{
		// Check if new, and if Edit that current users is allowed to edit
		if(isset($_REQUEST['task']) && $_REQUEST['task']=="edit")
			{
			$thispost			= get_post($_REQUEST['postid']);
			$user_post_excerpt	= get_post_meta($thispost->ID, "user_post_excerpt");
			$tmp_task_new 		= false;
			if ( frontier_can_edit($thispost) )
				$user_can_edit_this_post = true;
			}
		else
			{
			if ( frontier_can_add($fpost_sc_parms['frontier_add_post_type'])  )
				{
				if ( empty($thispost->ID) )
					{					
					$thispost 				= get_default_post_to_edit( $fpost_sc_parms['frontier_add_post_type'], true );
					$thispost->post_author 	= $current_user->ID;
					$thispost->post_type	= $fpost_sc_parms['frontier_add_post_type'];
					//echo "New post for edit: <pre>".print_r($thispost, true)."</pre><br>";
					}
				$_REQUEST['task']="new";
				$tmp_task_new = true;
				$user_can_edit_this_post = true;
				
				}
			else
				{
				
				echo '<br><div id="frontier-post-alert">';
				echo $fps_access_check_msg;
				echo '</div><br>';
				return;
				}
			}
			
		}
	
	//**************************************************************************************************
	// Do not proceed with all the processing if user is not able to add/edit
	//**************************************************************************************************
		
	if ( !$user_can_edit_this_post  )	
		{		
		// Echo reason why user cant add/edit post.
		global $fps_access_check_msg;
		if ( empty($fps_access_check_msg) || ($fps_access_check_msg < " ") )
			echo __("You are not allowed to edit this post, sorry ", "frontier-post");
		else
			echo "<br>".$fps_access_check_msg;
		
		//Reset message once displayed
		$fps_access_check_msg = "";
		
		return;
		}
	else	
		{
		//$fp_settings	= frontier_post_get_settings();
		$post_id 		= $thispost->ID;
		$users_role 	= frontier_get_user_role();
		$tax_form_lists	= frontier_get_tax_lists($fpost_sc_parms['frontier_page_id'], intval($fpost_sc_parms['frontier_parent_cat_id']), intval($fpost_sc_parms['fps_cache_time_tax_lists']) );
		
		//******************************************************************************************
		// Set defaults, so post can be saved without errors
		//******************************************************************************************
		if(!isset($thispost->post_type))
			$thispost->post_type = 'post';
		
		if(!isset($thispost->post_content))
			$thispost->post_content = '';
		
		// Call media fix (to support older versions) 
		frontier_media_fix( $post_id );
		
		//******************************************************************************************
		// Manage post status
		//******************************************************************************************
		
		//build post status list based on current status and users capability
		$tmp_status_list = get_post_statuses( );
		$tmp_status_list = array_reverse($tmp_status_list);
		
		// Remove private status from array if not allowed
		if (!current_user_can('frontier_post_can_private'))
			unset($tmp_status_list['private']);
		
		// Remove draft status from array if user is not allowed to use drafts
		if (!current_user_can('frontier_post_can_draft'))
			unset($tmp_status_list['draft']);
		
		// Remove pending status from array if user is not allowed to use pending status or if it is a page we are editing
		if ( !current_user_can('frontier_post_can_pending') || ($thispost->post_type == 'page') )
			unset($tmp_status_list['pending']);
		
		
		// Remove publish status from array if not allowed
		if (!current_user_can( 'frontier_post_can_publish' ))
			unset($tmp_status_list['publish']);
		
		// Add Future to status list, if post status is future
		if ($thispost->post_status == "future")
			$tmp_status_list['future'] = __("Future", "frontier-post");
		
		
		// Set default status if new post - Check if the default status is in the allowed statuses, and if so set the default status
		$tmp_default_status 	= fp_get_option("fps_default_status", "publish");
		
		if ( ($tmp_task_new == true) && array_key_exists($tmp_default_status , $tmp_status_list))
			$thispost->post_status	= $tmp_default_status;
			
		$status_list 		= array();
		$tmp_post_status 	= $thispost->post_status ? $thispost->post_status : $tmp_default_status;
		
		// if The deafult status is not in the list, set default status to the first in the list
		if ( !in_array($tmp_post_status, array_keys($tmp_status_list)) )
			$tmp_post_status = current(array_keys($tmp_status_list));

		$status_list = $tmp_status_list;
		
		
		
		//************************************************************************
		// Setup category	
		//************************************************************************
		
		$cats_excluded = fp_list2array(fp_get_option('fps_excl_cats'));
		//echo "cat excluded: ".print_r($cats_excluded,true)."<br>";
				
		
		// Do not manage categories for page
		if ( $thispost->post_type != 'page' )
			{
			
			// If capabilities is managed from other plugin, use default setting for all profiles
			if ( fp_get_option("fps_external_cap", "false") == "true" )
				$category_type 			= fp_get_option("fps_default_cat_select", "multi");
			else
				$category_type 			= $fp_capabilities[$users_role]['fps_role_category_layout'] ? $fp_capabilities[$users_role]['fps_role_category_layout'] : "multi"; 
	
		
			$default_category			= $fp_capabilities[$users_role]['fps_role_default_category'] ? $fp_capabilities[$users_role]['fps_role_default_category'] : get_option("default_category"); 
			//echo "cat default: ".print_r($default_category,true)."<br>";
			//echo "cat_id: ".print_r($fpost_sc_parms['frontier_cat_id'],true)."<br>";
	
			// set default category, if new and category parsed from shortcode, 
			if ( $tmp_task_new )
				{
				$cats_selected = fp_list2array($fpost_sc_parms['frontier_cat_id']);
				// check if excluded category
					
				if ( count($cats_selected) > 0 && $cats_selected[0] > 0 && !in_array($fpost_sc_parms['frontier_cat_id'][0], $cats_excluded))
					{ 
					$default_category =  intval($fpost_sc_parms['frontier_cat_id'][0]);
					}
				}
			else
				{
				$cats_selected	= $thispost->post_category;
				}
			
			// remove excluded categories:
			$cats_selected = array_diff($cats_selected+$cats_excluded, $cats_excluded )	;
			//echo "cat default Final: ".print_r($default_category,true)."<br>";
			//echo "cat selected Final: ".print_r($cats_selected,true)."<br>";
					
			}
		else
			{
			$cats_selected = array();
			} // end exclude categories for pages
		
			
		// Set variable for hidden field, if category field is removed from the form
		$cats_selected_txt = implode(',', $cats_selected);
		
		//***************************************************************************************
		//* Set tags
		//***************************************************************************************
		
		$fp_tag_count	= fp_get_option_int("fps_tag_count",3);
		
		if ( current_user_can( 'frontier_post_tags_edit' ) && ($thispost->post_type != 'page') )
			{
			$taglist = array();
			if (isset($thispost->ID))
				{
				$tmptags = get_the_tags($thispost->ID);
				if ($tmptags)
					{
					foreach ($tmptags as $tag) :
						array_push($taglist, $tag->name);
					endforeach;
					}
				}
			}
		
		//***************************************************************************************
		//* Get post moderation fields
		//***************************************************************************************
		
		if ( fp_get_option_bool("fps_use_moderation") && (current_user_can("edit_others_posts") || $current_user->ID == $thispost->post_author) )
			{
			$fp_moderation_comments = get_post_meta( $post_id, 'FRONTIER_POST_MODERATION_TEXT', true );
			}
		
		//***************************************************************************************
		// Enqueue media javascript
		//***************************************************************************************
		
		wp_enqueue_media( array( 'post' => $thispost->ID ) );
		
		//***************************************************************************************
		// Setup entry form
		//***************************************************************************************
		
		
		$fp_form = $fpost_sc_parms['frontier_edit_form'];
		
		// override if this is a quickpost
		if ( fp_bool($fp_use_quickpost) )
			{
			$fp_form = "quickpost";
			}
		
		if ($thispost->post_type == 'page')
			$fp_form = "page";
		
		switch($fp_form)
			{
			case "standard":
				include(frontier_load_form("frontier_post_form_standard.php"));	
				break;
			
			case "old":
				include(frontier_load_form("frontier_post_form_old.php"));	
				break;
			
			case "simple":
				include(frontier_load_form("frontier_post_form_simple.php"));
				break;
			
			case "page":
				include(frontier_load_form("frontier_post_form_page.php"));	
				break;
			
			case "quickpost":
				include(frontier_load_form("frontier_post_form_quickpost.php"));
				break;
			
			
			default:
				include(frontier_load_form("frontier_post_form_standard.php"));	
				break;
			
			
			}
		} // end  $user_can_edit_this_post
		
	} // end function



?>