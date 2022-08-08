<?php

// set initial div including my id shortcode parameter
echo '<div id="frontier-post-form-'.$fpost_sc_parms['frontier_myid'].'">';


//Display message
frontier_post_output_msg();


if ( strlen($fpost_sc_parms['frontier_edit_text_before']) > 1 )
	echo '<div id="frontier_edit_text_before">'.$fpost_sc_parms['frontier_edit_text_before'].'</div>';



//***************************************************************************************
//* Start form
//***************************************************************************************



	echo '<div class="frontier_post_form"> ';
	echo '<form action="'.$frontier_permalink.'" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >';
	
	// do not remove this include, as it holds the hidden fields necessary for the logic to work
	include(FRONTIER_POST_DIR."/forms/frontier_post_form_header.php");	

	wp_nonce_field( 'frontier_add_edit_post', 'frontier_add_edit_post_'.$thispost->ID ); 

?>			
	<table class="frontier-post-taxonomies"><tbody><tr>
	<td class="frontier_no_border">
	
	<?php
	if ( fp_get_option_bool("fps_title_required") )
		$fp_title_required = "REQUIRED";
	else
		$fp_title_required = "";
		
	?>
	
	<fieldset id="frontier_post_fieldset_title" class="frontier_post_fieldset">
		<legend><?php _e("Title", "frontier-post"); ?></legend>
		<input class="frontier-formtitle" <?php echo $fp_title_required; ?> placeholder="<?php _e('Enter title here', 'frontier-post'); ?>" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="fp_title" >			
	</fieldset>
	
	<?php if ( fp_get_option_bool("fps_hide_status") )
		{
		echo '<input type="hidden" id="post_status" name="post_status" value="'.$thispost->post_status.'"  >';
		}
	else
		{
		//echo ' '.__("Status", "frontier-post").': '; 
		?> 
		<fieldset id="frontier_post_fieldset_status" class="frontier_post_fieldset">
			<legend><?php _e("Status", "frontier-post"); ?></legend>
			<select  class="frontier_post_dropdown" id="post_status" name="post_status" >
			<?php foreach($status_list as $key => $value) : ?>   
				<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
					<?php echo $value; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</fieldset>	
	<?php } ?>	

	</td></tr>
	
	<?php 
		//****************************************************************************************************
		// Action fires before displaying the editor
		// Do action 		frontier_post_form_standard_top
		// $thispost 		Post object for the post  
		// $tmp_task_new  	Equals true if the user is adding a post
		//****************************************************************************************************
		
		do_action('frontier_post_form_standard_top', $thispost, $tmp_task_new);
	?>
	
	<tr><td class="frontier_no_border">
	<fieldset class="frontier_post_fieldset">
		<legend><?php _e("Content", "frontier-post"); ?></legend>	
		<div id="frontierpost"> 
		<?php
		wp_editor($thispost->post_content, 'frontier_post_content', frontier_post_wp_editor_args($fpost_sc_parms['frontier_editor_height']));
		//wp_editor($thispost->post_content, 'user_post_desc', array());
		
		?>
		</div>
	</fieldset>
	</td></tr>
	<?php
	
	
	
	//**********************************************************************************
	//* Taxonomies
	//**********************************************************************************
			
	//$tax_list = array("category", "group", "article-type");
	$tax_list 			= $fpost_sc_parms['frontier_custom_tax'];
	$tax_layout_list 	= fp_get_tax_layout($fpost_sc_parms['frontier_custom_tax'], $fpost_sc_parms['frontier_custom_tax_layout']);
	
	
	echo '<tr><td class="frontier_no_border">';
	
	

	foreach ( $tax_layout_list as $tmp_tax_name => $tmp_tax_layout) 
		{
		if ($tmp_tax_layout != "hide")
			{
			// Cats_selected is set from script, but only for category
			if ($tmp_tax_name != 'category')
				$cats_selected = wp_get_post_terms($thispost->ID, $tmp_tax_name, array("fields" => "ids"));
			
			echo '<fieldset class="frontier_post_fieldset_tax frontier_post_fieldset_tax_'.$tmp_tax_name.'">';
			echo '<legend class="frontier_post_legend_tax" >'.fp_get_tax_label($tmp_tax_name).'</legend>';
			
			//****************************************************************************************************
			// Action fires before displaying taxonomies
			// Do action 		frontier_post_form_standard_top
			// $thispost 		Post object for the post  
			// $tmp_task_new  	Equals true if the user is adding a post
			// $tmp_tax_name	Name of Taxonomy (ex Category")
			//****************************************************************************************************
	
			do_action('frontier_post_form_standard_tax', $thispost, $tmp_task_new, $tmp_tax_name);
			
			frontier_tax_input($thispost->ID, $tmp_tax_name, $tmp_tax_layout, $cats_selected, $fpost_sc_parms, $tax_form_lists[$tmp_tax_name]);
			echo '</fieldset>';
			echo PHP_EOL;
			}
		}
	
	
	if ( current_user_can( 'frontier_post_tags_edit' ) || fp_get_option("fps_show_feat_img", "false") !== "false" )
		{
		
		//****************************************************************************************************
		// tags
		//****************************************************************************************************
		
		if ( current_user_can( 'frontier_post_tags_edit' ) )
			{ 
			echo '<fieldset class="frontier_post_fieldset_tax frontier_post_fieldset_tax_tag">';
			echo '<legend>'.__("Tags", "frontier-post").'</legend>';
			for ($i=0; $i<$fp_tag_count; $i++)
				{
				$tmp_tag = isset($taglist[$i]) ? fp_tag_transform($taglist[$i]) : "";
				//$tmp_tag = array_key_exists($i, $taglist) ? fp_tag_transform($taglist[$i]) : "";
				echo '<input placeholder="'.__("Enter tag here", "frontier-post").'" type="text" value="'.$tmp_tag.'" name="user_post_tag'.$i.'" id="user_post_tag"><br>';	
				}
			echo '</fieldset>';
			} 
		
		//****************************************************************************************************
		// Featured image
		//****************************************************************************************************
		
	
		if ( fp_get_option("fps_show_feat_img", "false") !== "false" )
			{
			echo '<div class="frontier-thumbnail">';
			echo '<fieldset class="frontier_post_fieldset_tax frontier_post_fieldset_tax_featured">';
			echo '<legend  class="frontier_post_legend_featured">'.__("Featured image", "frontier-post").'</legend>';
			
			if ( fp_get_option('fps_show_feat_img') === "simple" )
				{
				echo '<div id="frontier_post_featured_image_label">'.__("Upload featured image","frontier-post").":<br></div>";
				echo '<input type="file" name="thumbnail" id="thumbnail"><br>';
				echo '<div id="frontier_post_featured_image_txt">'.__("Not uploaded until post is saved", "frontier-post").'</div>';
				//echo '<br>'; 
				}
			else
				{
				$uparms =  '?post_id='.$post_id;
				$uparms .= '&amp;'.'type=image';
				$uparms .= '&amp;'.'TB_iframe=1';
				//$uparms .= '&amp;'.'inline=1';
				$uparms .= '&amp;'.'tab=library';
			
				echo '<a title="'.__("Select featured image").'" href="'.site_url('/wp-admin/media-upload.php').$uparms.'" id="frontier-post-select-thumbnail set-post-thumbnail" name="set-post-thumbnail" class="thickbox">';
				echo __("Select featured image", "frontier-post").'</a><br>';
				echo '<div id="frontier_post_featured_image_txt">'.__("Not updated until post is saved", "frontier-post").'</div>';
				echo '<br>';
				}
			
			
			if (has_post_thumbnail($post_id))
				{
				$thumpid = get_post_thumbnail_id($post_id);
				echo get_the_post_thumbnail($post_id, 'small', array('class' => 'frontier-post-feat-img'));
				}
			else
				{
				$thumpid = -1;
				}

			echo '<input id="_thumbnail_id" name="_thumbnail_id" value="'.$thumpid.'" type="hidden">';
			echo '</fieldset>';
			echo '</div>';
							
			
			
			
			} // end featured image
		
		} // end tags and featured image
		
	if ( current_user_can( 'frontier_post_exerpt_edit' ) )
			{ ?>
			<fieldset class="frontier_post_fieldset_excerpt">
				<legend><?php _e("Excerpt", "frontier-post")?>:</legend>
				<textarea name="user_post_excerpt" id="user_post_excerpt" ><?php if(!empty($thispost->post_excerpt))echo $thispost->post_excerpt;?></textarea>
			</fieldset>
			
	<?php 	} 
	
		echo '</td></tr>';
		
		//****************************************************************************************************
		// post moderation
		//****************************************************************************************************
		
		if ( fp_get_option_bool("fps_use_moderation") && (current_user_can("edit_others_posts") || $current_user->ID == $thispost->post_author))
			{
			echo '<tr><td class="frontier_no_border">';
			echo '<fieldset class="frontier_post_fieldset_moderation">';
			echo '<legend>'.__("Post Moderation", "frontier-post").'</legend>';
			//Allow email to be send to author on comment update
			if (current_user_can("edit_others_posts"))
				echo __("Email author with moderation comments ?", "frontier-post").' '.'<input name="frontier_post_moderation_send_email" id="frontier_post_moderation_send_email" value="true"  type="checkbox"><br>';
			
			echo '<textarea name="frontier_post_moderation_new_text" id="frontier_post_moderation_new_text" >';
			echo '</textarea>';
			echo __("Previous comments", "frontier-post").':<br>';
			echo '<hr>';
			echo $fp_moderation_comments;
			
			echo '</fieldset>';
	
	
			echo '</td></tr>';
		
			}	
		//****************************************************************************************************
		// Action fires just before the submit buttons
		// Do action 		frontier_post_form_standard
		// $thispost 		Post object for the post  
		// $tmp_task_new  	Equals true if the user is adding a post
		//****************************************************************************************************
		
		do_action('frontier_post_form_standard', $thispost, $tmp_task_new);
		
		
		echo '<tr><td class="frontier_no_border">';
	
	?>
	
	
	
		<fieldset class="frontier_post_fieldset">
		<legend><?php _e("Actions", "frontier-post"); ?></legend>
		<?php
			
		if ( fp_get_option_bool("fps_submit_save") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 		id="user_post_save" 	value="save"><?php _e("Save", "frontier-post"); ?></button>
		<?php }
		
		if ( fp_get_option_bool("fps_submit_savereturn") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $fpost_sc_parms['frontier_return_text']; ?></button>
		<?php }
		
		if ( fp_get_option_bool("fps_submit_publish") && ($thispost->post_status !== "publish" || $tmp_task_new) && current_user_can("frontier_post_can_publish") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 	id="user_post_publish" 	value="publish"><?php _e("Publish", "frontier-post"); ?></button>
		<?php }
		
		if ( fp_get_option_bool("fps_submit_preview") )
		{ ?>
			<button class="button" type="submit" name="user_post_submit" 	id="user_post_preview" 	value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
		<?php } 
		
		if ( fp_get_option_bool("fps_submit_delete")  && current_user_can("frontier_post_can_delete") && !$tmp_task_new )
		{ ?>
			<button class="button frontier-post-form-delete" type="submit" name="user_post_submit" 	id="user_post_delete" 	value="delete"><?php _e("Delete", "frontier-post"); ?></button>
		<?php } 
		
		if ( fp_get_option_bool("fps_submit_cancel") )
		{ ?>
		<input type="reset" value="<?php _e("Cancel", "frontier-post"); ?>"  name="cancel" id="frontier-post-cancel" onclick="location.href='<?php the_permalink();?>'">
		<?php } 
		
		
		/*
		if ( fp_get_option_bool("fps_submit_delete") && $thispost->post_status !== "publish" && current_user_can("frontier_post_can_delete") && !$tmp_task_new )
			{ 
			echo "&nbsp;".frontier_post_delete_link($thispost, false, $frontier_permalink, 'frontier-post-form-delete' );
			}
		*/
		echo '<p class="frontier-post-form-posttype">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('.__("Post type", "frontier-post").": ".fp_get_posttype_label_singular($thispost->post_type).') </p>';
		?>
	</fieldset>
	
	</td></tr></table>
</form> 
	
	</div> <!-- ending div --> 
	</div> 
<?php
	
	// end form file
?>