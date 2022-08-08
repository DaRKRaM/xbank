<?php
/*
Validations for Frontier Post plugin
*/

function frontier_post_age($tmp_post_date)
	{
	return round((time() - strtotime($tmp_post_date))/(24*60*60));					
	}
	

//****************************************************************************
// Can Add
//****************************************************************************

function frontier_can_add($tmp_post_type = "NONE")
	{
	global $fps_access_check_msg;
	$tmp_can_do = true;
	
	if ( !current_user_can( 'frontier_post_can_add' ) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to add new posts", "frontier-post")."<br>";
		}
	
	// Always allow the boss 
	if ( current_user_can( 'administrator' ) )
		{
		$tmp_can_do = true;
		$fps_access_check_msg = "";
		}
		
	
		
	// check if it is an allowed posttype
	if ( !fp_check_post_type($tmp_post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to add new post type", "frontier-post").": ".$tmp_post_type;
		$fps_access_check_msg .= " - Allowed: (".implode(", ", fp_default_post_type_list()).")<br>";
		}
	
	// check if posttype exists
	if ( !post_type_exists($tmp_post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("Post type does not exists", "frontier-post").": ".$tmp_post_type."<br>";
		}
	
	//echo "<hr>add validation msg: ".$fps_access_check_msg."<br>";
	
	return $tmp_can_do;
	
	}	
	

//****************************************************************************
// Can Clone
//****************************************************************************

	
function frontier_can_clone($tmp_post_type = "NONE")
	{
	global $fps_access_check_msg;
	$tmp_can_do = true;
	
	// first check if user can add
	if (!frontier_can_add($tmp_post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to add new posts", "frontier-post")."<br>";
		}
		
	if ( !current_user_can( 'frontier_post_can_clone' ) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to clone posts", "frontier-post")."<br>";
		}
	
	// Always allow the boss 
	if ( current_user_can( 'administrator' ) )
		{
		$tmp_can_do = true;
		$fps_access_check_msg = "";
		}
		
		
	// check if it is an allowed posttype
	if ( !fp_check_post_type($tmp_post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to clone post type", "frontier-post").": ".$tmp_post_type;
		$fps_access_check_msg .= " - Allowed: (".implode(", ", fp_default_post_type_list()).")<br>";
		}
	
	// check if posttype exists
	if ( !post_type_exists($tmp_post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("Post type does not exists", "frontier-post").": ".$tmp_post_type."<br>";
		}
	
	//echo "<hr>add validation msg: ".$fps_access_check_msg."<br>";
	
	return $tmp_can_do;
	
	}		


//****************************************************************************
// Can Edit
//****************************************************************************

	
function frontier_can_edit($tmp_post)
	{
	global $fps_access_check_msg;
	
	$cur_user 				= wp_get_current_user();
	$tmp_can_do 			= true;
	
	
	// Check if the user is allowed to edit posts
	if ( !current_user_can( 'frontier_post_can_edit' ) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit posts", "frontier-post")."<br>";
		}
		
	// Users can not edit other users posts unless they have capability "edit_others_posts" (Administrators & Editors) 
	if( ($cur_user->ID != $tmp_post->post_author) && (!current_user_can( 'edit_others_posts' )) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit post from another user", "frontier-post")."<br>";
		}
	
	// Check that the age of the post is below the Frontier Post setting
	if ( ($tmp_post->post_status == "publish")  && (frontier_post_age($tmp_post->post_date) > fp_get_option_int('fps_edit_max_age')) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit post older than: ", "frontier-post").fp_get_option_int('fps_edit_max_age')." ".__("days", "frontier-post")."<br>";
		}
	
	// Check that user is allowed to edit posts that already has comments
	if ( (intval($tmp_post->comment_count) > 0) && !fp_get_option_bool("fps_edit_w_comments") )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit post that already has comments", "frontier-post")."<br>";
		}
	
	// Check if user is allowed to edit a post that is already published
	if ( !fp_get_option("fps_change_status") && ($tmp_post->post_status == "publish") )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit post that is published", "frontier-post")."<br>";
		}
	
	// check if it is an allowed posttype
	if ( !fp_check_post_type($tmp_post->post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit", "frontier-post").": ".fp_get_posttype_label($tmp_post->post_type)."<br>";
		}
	
	// Always allow the boss 
	if ( current_user_can( 'administrator' ) )
		{
		$tmp_can_do = true;
		$fps_access_check_msg = "";
		}
		
	// Last check, PRIVATE posts can only be edited by the author or Users with the capability edit_private_posts
	if ( $tmp_post->post_status == "private" && ($cur_user->ID != $tmp_post->post_author || !current_user_can( 'frontier_post_can_private' )) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to edit PRIVATE post from another user", "frontier-post")."<br>";
		}
	
	return $tmp_can_do;
	
	}	


//****************************************************************************
// Can Delete
//****************************************************************************


function frontier_can_delete($tmp_post)
	{
	$fps_access_check_msg	= "";
	$cur_user 				= wp_get_current_user();
	$tmp_can_do 			= true;
	
	// Check if the user is allowed to delete posts
	if ( !current_user_can( 'frontier_post_can_delete' ) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to delete posts", "frontier-post")."<br>";
		}
	
	// Users can not delete other users posts unless they have capability "delete_others_posts" (Administrators & Editors) 
	if ( ($cur_user->ID != $tmp_post->post_author) && (!current_user_can( 'delete_others_posts' )) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to delete post from another user", "frontier-post")."<br>";
		}
		
	
	// Check that the age of the post is below the Frontier Post setting
	if ( frontier_post_age($tmp_post->post_date) > fp_get_option_int('fps_delete_max_age') )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to delete post older than: ", "frontier-post").get_option('frontier_post_delete_max_age')." ".__("days", "frontier-post")."<br>";
		}


	// Check that user is allowed to delete posts that already has comments	
	if ( ( (int) $tmp_post->comment_count) > 0 && ( !fp_get_option_bool("fps_del_w_comments") ))
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to deelete post that already has comments", "frontier-post")."<br>";
		}	
	
	// Check that user is allowed to delete published posts 
	if ( !fp_get_option("fps_change_status") && ($tmp_post->post_status == "publish") )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to delete published posts", "frontier-post")."<br>";
		}	
	
	// check if it is an allowed posttype
	if ( !fp_check_post_type($tmp_post->post_type) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to delete", "frontier-post").": ".fp_get_posttype_label($tmp_post->post_type)."<br>";
		}
	
	
	// Always allow the boss 
	if ( current_user_can( 'administrator' ) )
		{
		$tmp_can_do = true;
		$fps_access_check_msg = "";
		}
		
	// Last check, PRIVATE posts can only be deleted by the author, or users with capability delete_private_posts (admins and editors)
	if ( $tmp_post->post_status == "private" && ($cur_user->ID != $tmp_post->post_author || !current_user_can( 'frontier_post_can_private' ) || !current_user_can( 'frontier_post_can_delete' )) )
		{
		$tmp_can_do = false;
		$fps_access_check_msg .= __("You are not allowed to delete PRIVATE post from another user", "frontier-post")."<br>";
		}
	
	
	
	return $tmp_can_do;
	
	}	

?>