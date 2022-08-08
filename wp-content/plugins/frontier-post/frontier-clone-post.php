<?php

function frontier_clone_post($fpost_sc_parms = array())
	{
	//extract($fpost_sc_parms);
	
	//echo "CLONE POST<br>";
	
	$frontier_permalink = get_permalink();
	$concat				= get_option("permalink_structure")?"?":"&";
	
	 
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
    
	if($post_task == "clone" && $_REQUEST['postid'])
		{
		
		$old_post		= get_post($_REQUEST['postid']);
		$old_post_id	= $old_post->ID;
		
		//double check current user can add a post with this post type
		if ( frontier_can_add($old_post->post_type)  )
			{
			require_once(ABSPATH . '/wp-admin/includes/post.php');
			global $current_user;
	
			//Get permalink from old post
			$old_permalink = get_permalink($old_post_id);
	
			// lets clone it
			$thispost 				= get_default_post_to_edit( $fpost_sc_parms['frontier_add_post_type'], true );
			
			$new_post_id			= $thispost->ID;
			
			
			
			$tmp_post = array(
			 'ID'				=> $new_post_id,
			 'post_type'		=> $old_post->post_type,
			 'post_title' 		=> __("CLONED from: ", "frontier-post").$old_post->post_title,
			 'post_content' 	=> __("CLONED from: ", "frontier-post").$old_post->post_title."<BR>".$old_post->post_content,
			 'post_status'		=> "draft",
			 'post_author'		=> $current_user->ID,			);
		
			//****************************************************************************************************
			// Apply filter before insert of post 
			// filter:			frontier_post_clone
			// $tmp_post 		Array that holds the updated fields 
			// $old_post  		The post being cloed (Object)
			//****************************************************************************************************
		
			$tmp_post = apply_filters( 'frontier_post_clone', $tmp_post, $old_post );
		
			// save post
			wp_update_post( $tmp_post );
			
			//Get the updated post
			$new_post = get_post($new_post_id);
		
			
			
		 	//get all current post terms ad set them to the new post draft
			$taxonomies = get_object_taxonomies($old_post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ($taxonomies as $taxonomy) 
				{
				$post_terms = wp_get_object_terms($old_post_id, $taxonomy, array('fields' => 'slugs'));
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
				}
 
 			// Set featured image:
 			$post_thumbnail_id = get_post_thumbnail_id( $old_post_id );
 			
 			if ( intval($post_thumbnail_id)>0 )
				 set_post_thumbnail( $new_post_id, $post_thumbnail_id);
				 
			//****************************************************************************************************
			// Action fires after insert and processing of the cloned post
			// Do action 		frontier_post_clone_past_insert
			// $new_post 		Post object for the post just inserted 
			// $old_post  		Post that was cloned from
			//****************************************************************************************************
		
			do_action('frontier_post_clone_past_insert', $new_post, $old_post );
 
 			// Add/Update message
			frontier_post_set_msg(__("Post Cloned and ready to edit", "frontier-post").": ".$new_post->post_title);
			frontier_post_set_msg(__("Post status set to Draft", "frontier-post"));
			
			
			
			frontier_user_post_list($fpost_sc_parms);		
			
			
			}
		
		
		}
	}



?>