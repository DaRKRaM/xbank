<?php 

global $fps_access_check_msg;
//Reset access message
$fps_access_check_msg = "";

$concat= get_option("permalink_structure")?"?":"&";    
//set the permalink for the page itself
$frontier_permalink = get_permalink();

// set initial div including my id shortcode parameter
echo '<div id="frontier-post-list-'.$fpost_sc_parms['frontier_myid'].'">';

// Get array of sticky posts
$tmp_sticky = get_option('sticky_posts');


//Display before text from shortcode
if ( strlen($fpost_sc_parms['frontier_list_text_before']) > 1 )
	echo '<div id="frontier_list_text_before">'.$fpost_sc_parms['frontier_list_text_before'].'</div>';


//Display message
frontier_post_output_msg();

if (strlen(trim($fpost_sc_parms['frontier_add_link_text']))>0)
		$tmp_add_text = $fpost_sc_parms['frontier_add_link_text'];
	else
		$tmp_add_text = __("Create New", "frontier-post")." ".fp_get_posttype_label_singular($fpost_sc_parms['frontier_add_post_type']);
	

$fp_cat_list = implode(",", $fpost_sc_parms['frontier_cat_id']); 


if (frontier_can_add($fpost_sc_parms['frontier_add_post_type']) && !fp_get_option_bool("fps_hide_add_on_list"))
	{
		

	echo '<table class="frontier-menu" >';
		echo '<tr class="frontier-menu">';
			echo '<th class="frontier-menu" >&nbsp;</th>';
			echo '<th class="frontier-menu" ><a id="frontier-post-add-new-link class="frontier-post-add-link" " href="'.frontier_post_add_link($tmp_p_id).'">'.$tmp_add_text.'</a></th>';
			echo '<th class="frontier-menu" >&nbsp;</th>';
		echo '</tr>';
	echo '</table>';

	} 
else
	{
	if ( current_user_can("manage_options") && strlen(trim($fps_access_check_msg)) > 0)
		{
		echo '<div id="frontier-post-posttype-warning">';
		echo $fps_access_check_msg;
		echo ' - '.__("This message is only shown to admins", "frontier-post").'<br><br>';		
		echo '</div>';
		}
	} // if can_add

//*******************************************************************************************************
//  Quickpost
//*******************************************************************************************************

frontier_quickpost($fpost_sc_parms);

//*******************************************************************************************************
//  Display post list
//*******************************************************************************************************

$tmp_date_format = fp_get_option('fps_date_format_lists', 'Y-m-d');

if( $user_posts->found_posts > 0 )
	{
	
	$tmp_status_list = get_post_statuses( );

	// If post for all users is viewed, show author instead of category
	if (fp_bool($fpost_sc_parms['frontier_list_all_posts']) || fp_bool($fpost_sc_parms['frontier_list_pending_posts']) )
		$cat_author_heading = __("Author", "frontier-post");
	else	
		$cat_author_heading = __("Category", "frontier-post");
		
		
	//****************************************************************************************************
	// Action fires before displaying posts
	// Do action 		frontier_post_list_top
	// $user_posts 		Post objects for list  
	//****************************************************************************************************
	
	do_action('frontier_post_list_top', $user_posts);
	
?>

<table class="frontier-list" id="user_post_list">
 	<div id="frontier-post-list-heading">
	<thead>
		<tr>
			<th class="frontier-list-posts" id="frontier-list-posts-date"><?php _e("Date", "frontier-post"); ?></th>
			<th class="frontier-list-posts" id="frontier-list-posts-title"><?php _e("Title", "frontier-post"); ?></th>	
			<?php
			// do not show Status if list all posts, as all are published
			if ( !fp_bool($fpost_sc_parms['frontier_list_all_posts']) || current_user_can( 'edit_private_posts' ) )
				echo '<th class="frontier-list-posts" id="frontier-list-posts-status">'.__("Status", "frontier-post").'</th>';
			?>
			<th class="frontier-list-posts" id="frontier-list-posts-category"><?php echo $cat_author_heading ?></th>
			<th class="frontier-list-posts" id="frontier-list-posts-cmt"><?php echo frontier_get_icon('comments'); ?></th> <!--number of comments-->
			<th class="frontier-list-posts" id="frontier-list-posts-action"><?php _e("Action", "frontier-post"); ?></th>
		</tr>
	</thead> 
	<!--</div>-->
	<tbody>
	<?php 
	while ($user_posts->have_posts()) 
		{
			$user_posts->the_post();
			
			//$display_date = mysql2date($tmp_date_format, $post->post_date);
			
			//mysql2date('Y-m-d', $post->post_date)
			
			//****************************************************************************************************
			// Action fires before each post record
			// Do action 		frontier_post_list_record
			// $post 			Post objects 
			//****************************************************************************************************

			do_action('frontier_post_list_record', $post);
				
				
	?>
			<tr>
				<td class="frontier-list-posts" id="frontier-list-posts-date">
				<?php 
					//echo $display_date; 
				if ($post->post_status === 'future' )
					{
					echo mysql2date('Y-m-d H:i', $post->post_date);
					}
				else
					if ( $tmp_date_format == "human" )
						printf( _x( '%s ago', '%s = human-readable time difference', 'frontier-post' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
					else
						echo mysql2date($tmp_date_format, $post->post_date);
				?>
				</td>
				<td class="frontier-list-posts" id="frontier-list-posts-title">
				<?php if ($post->post_status == "publish")
						{ ?>
						<a class="frontier-list-posts" id="frontier-list-posts-title-link" href="<?php echo get_permalink($post->ID);?>"><?php echo $post->post_title;?></a>
				<?php	} 
					else
						{
						echo $post->post_title;
						} ?>
						
				</td>
				<?php
				if ( !fp_bool($fpost_sc_parms['frontier_list_all_posts']) || current_user_can( 'edit_private_posts' ) )
					echo '<td class="frontier-list-posts" id="" >'.( isset($tmp_status_list[$post->post_status]) ? $tmp_status_list[$post->post_status] : $post->post_status );
					// check if moderation comments
				if ($post->post_status == "draft" || $post->post_status == "pending")
					{
					$tmp_flag = get_post_meta( $post->ID, 'FRONTIER_POST_MODERATION_FLAG', true );
					if (isset($tmp_flag) && $tmp_flag == "true")
						echo " ".frontier_get_icon('moderation');
					}
				// If post is Sticky
				if ( $post->post_status == "publish" && in_array($post->ID, $tmp_sticky) )
					{
					echo ' ('.__("Sticky").')';
					}
				echo '</td>';
				?>
				<?php  
					

					// If post for all users is viewed, show author instead of category
					if (fp_bool($fpost_sc_parms['frontier_list_all_posts']) || fp_bool($fpost_sc_parms['frontier_list_pending_posts']) )
						{
						echo '<td class="frontier-list-posts" id="frontier-list-posts-author">';
						echo get_the_author_meta( 'display_name', $post	->author);
						}
					else
						{
						echo '<td class="frontier-list-posts" id="frontier-list-posts-category">';
						// List categories
						$categories=get_the_category( $post->ID );
						$cnt = 0;
						foreach ($categories as $category) :
							$cnt = $cnt+1;
							if ($cnt > 1)
								echo "</br>".$category->cat_name; 
							else
								echo $category->cat_name; 
						endforeach;
						}
				?></td>
				<td class="frontier-list-posts" id="frontier-list-posts-cmt"><?php  echo $post->comment_count;?></td>
				<td class="frontier-list-posts" id="frontier-list-posts-action">
					<?php
					echo frontier_post_display_links($post, $fp_show_icons, $frontier_permalink);
					/*
					echo frontier_post_edit_link($post, $fp_show_icons, $frontier_permalink);
					echo frontier_post_delete_link($post, $fp_show_icons, $frontier_permalink);
					echo frontier_post_preview_link($post, $fp_show_icons);
					*/
							
					?>
					&nbsp;
				</td>
			</tr>
		<?php 
		//****************************************************************************************************
		// Action fires after displaying posts
		// Do action 		frontier_post_list_botttom
		// $user_posts 		Post objects for list  	
		//****************************************************************************************************
	
		do_action('frontier_post_list_bottom', $user_posts);
	
		
		} 
		?>
	</tbody>
</table>
<?php

	if ( fp_bool($fpost_sc_parms['frontier_pagination']) )
		{
		$pagination = paginate_links( 
			array(
				'base' => add_query_arg( 'pagenum', '%#%'),
				'format' => '',
				'prev_text' => __( '&laquo;', 'frontier-post' ),
				'next_text' => __( '&raquo;', 'frontier-post' ),
				'total' => $user_posts->max_num_pages,
				'current' => $pagenum,
				'add_args' => false  //due to wp 4.1 bug (trac ticket 30831)
				) 
			);

		//if ( $pagination ) 
		//	echo $pagination;
		if ( $pagination ) 
			{
			echo '<br><div id="frontier-post-pagination">'.$pagination.'</div>';
			}
		
		
		}
	if ( !fp_bool($fpost_sc_parms['frontier_list_all_posts']) )
		echo "</br>".__("Number of posts already created by you: ", "frontier-post").$user_posts->found_posts."</br>";
	else
		echo "</br>".__("Number of posts: ", "frontier-post").$user_posts->found_posts."</br>";
		
	
	
	} // end if have posts
	
else
	{
		echo "</br><center>";
		
		if ( !fp_bool($fpost_sc_parms['frontier_list_all_posts']) )
			_e('Sorry, you do not have any posts (yet)', 'frontier-post');
		else
			_e('Sorry, no posts to display', 'frontier-post');
		
		echo "</center><br></br>";
	} // end post count
	
echo '</div>';

//Re-instate $post for the page
wp_reset_postdata();

?>