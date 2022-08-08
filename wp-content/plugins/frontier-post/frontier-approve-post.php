<?php

function frontier_prepare_approve_post($fpost_sc_parms = array())
	{
	//extract($fpost_sc_parms);
	
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
    
	if($post_task == "approve" )
		{
		if($_REQUEST['postid'])
			{
			$thispost		= get_post($_REQUEST['postid']);
			
			$post_author	= $thispost->post_author;
			
			if ( $thispost->post_status === "pending" && current_user_can("edit_others_posts")  )
				{
				
				echo '<div id="frontier-post-alert">'.__("Approve", "frontier-post").':&nbsp;'.fp_get_posttype_label_singular($thispost->post_type).'</div>';
				echo '<br><br>';
				?>
					<div class="frontier_post_approve_form"> 
					<table>
					
					<form action="<?php echo $frontier_permalink; ?>" method="post" name="frontier_approve_post" id="frontier_approve_post" enctype="multipart/form-data" >
						<input type="hidden" name="action" value="wpfrtp_approve_post"> 
						<input type="hidden" name="task" value="approve">
						<input type="hidden" name="postid" id="postid" value="<?php if(isset($thispost->ID)) echo $thispost->ID; ?>">
						<?php wp_nonce_field( 'frontier_approve_post', 'frontier_approve_post_'.$thispost->ID ); ?>
		
						<tr>
						</tr><tr>
						<td><center>
						<button class="button" type="submit" name="submit_approve" 		id="submit_approve" 	value="approvego"><?php _e("Approve", "frontier-post"); ?></button>
						<input type="reset" value="<?php _e("Cancel", "frontier-post");?>" name="cancel" id="cancel" onclick="location.href='<?php the_permalink();?>'">
						</center>
						</td></tr>
					</form>	
					</table>	
					
					<hr>
					<?php 
					echo "<table>";
					echo "<tr>";
					
					
					echo "<td><h1>".$thispost->post_title."</h1></td>"; 
					
					
					
					echo "</tr><tr><td>";
				
					/*
					$tmp_content = apply_filters( 'the_content', $thispost->post_content );
					$tmp_content = str_replace( ']]>', ']]&gt;', $tmp_content );
					*/
					$tmp_content = fp_view_post($thispost);
					echo $tmp_content;
					
					/*
					$content = $thispost->post_content;
					//$content = apply_filters( 'the_content', $content);
					$content = str_replace( ']]>', ']]&gt;', $content );
					echo $content;
					*/ 
					echo "</td>";
					
					// echo $thispost->post_content; 
					
					echo "</tr></table>";
					?>
					</div>
					
				<?php
						
				
				}

			}
	
		}
	
	} // end prepare Approve

function frontier_execute_approve_post($fpost_sc_parms = array())
	{
	//extract($fpost_sc_parms);
	
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
		
	$post_action 		= isset($_POST['action']) ? $_POST['action'] : "Unknown";
    
	$submit_approve 	= isset($_POST['submit_approve']) ? $_POST['submit_approve'] : "Unknown";
	$postid				= isset($_POST['postid']) ? $_POST['postid'] : 0;
	
    if( ($post_action == "wpfrtp_approve_post") && ($postid !=0) )
		{
		if ( !wp_verify_nonce( $_POST['frontier_approve_post_'.$_POST['postid']], 'frontier_approve_post' ) )
			{
			wp_die(__("Security violation (Nonce check) - Please contact your Wordpress administrator", "frontier-post"));
			}
		
		
		
		$thispost		= get_post($postid);	
		if ( ($submit_approve = "approvego") && $thispost->post_status === "pending" && current_user_can("edit_others_posts")  )
			{
			$tmp_title = $thispost->post_title;
			
			
			//Set status to publish
			$tmp_post = array(
			 'ID'				=> $postid,
			 'post_status' 		=> 'publish',
			 );
			wp_update_post( $tmp_post );
			frontier_post_set_msg(__("Post approved", "frontier-post").": ".$tmp_title);
			frontier_user_post_list($fpost_sc_parms);
			}
		}
	}



?>