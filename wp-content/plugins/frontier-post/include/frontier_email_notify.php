<?php
// Send email when a post changes status to pending

function frontier_email_on_transition(  $new_status, $old_status, $post ) 
	{
	
    if( $post->post_type !== 'post' )
        return;    //Don't touch anything that's not a post (i.e. ignore links and attachments and whatnot )

			
    //If some variety of a draft is being published, dispatch an email
    if(  $old_status != 'pending'  && $new_status == 'pending' && fp_get_option("fps_mail_to_approve", "false") == "true") 
		{
		$author_name	= get_the_author_meta( 'display_name', $post->post_author );
        $to      		= fp_get_option("fps_mail_address") ? fp_get_option("fps_mail_address") : get_option("admin_email");
        $subject 		= __("Post for approval from", "frontier-post").": ".$author_name ." (".get_bloginfo( "name" ).")";
        $body    		= 		__("Post for approval from", "frontier-post").": ".$author_name ." (".get_bloginfo( "name" ).")"."\r\n\r\n";
		$body    		.= __("Title", "frontier-post").": ".$post->post_title."\r\n\r\n";
		$concat			= get_option("permalink_structure")?"?":"&"; 
		
		if ( fp_get_option_int('fps_pending_page_id',0) > 0 )
				$tmp_link = get_permalink(fp_get_option('fps_pending_page_id')).$concat.'task=approve&postid='.$post->ID;
			else
				$tmp_link =	site_url('/wp-admin/edit.php?post_status=pending&post_type=post');
	
		 
		$body    		.= __("Link to approval", "frontier-post").": ".$tmp_link."\r\n\r\n";

		
        if( !wp_mail($to, $subject, $body ) ) 
			error_log(__("Message delivery failed - Recipient: (", "frontier-post").$to.")");
			
		}
		
	if(  $old_status == 'pending'  && $new_status == 'publish' && fp_get_option("fps_mail_approved", "false") == "true"  )
		{
		if ( $post->post_author == get_current_user_id() )
			return; // no reason to send email if current user is able to publish :)
		
		$to      		= get_the_author_meta( 'email', $post->post_author );
        $subject 		= __("Your post has been approved", "frontier-post")." (".get_bloginfo( "name" ).")";
        $body    		= __("Your post has been approved", "frontier-post").": (".get_bloginfo( "name" ).")"."\r\n\r\n";
		$body    		.= __("Title", "frontier-post").": ".$post->post_title."\r\n\r\n";
		$body    		.= __("Link", "frontier-post").": ".get_permalink($post->ID)."\r\n\r\n";
		
		
		
        if( !wp_mail($to, $subject, $body ) ) 
			error_log(__("Message delivery failed - Recipient: (", "frontier-post").$to.")");
		
		}
	}
	
add_action('transition_post_status', 'frontier_email_on_transition', 10, 3);









?>