<?php

//Display message
frontier_post_output_msg();

if ( strlen($fpost_sc_parms['frontier_edit_text_before']) > 1 )
	echo '<div id="frontier_edit_text_before">'.$fpost_sc_parms['frontier_edit_text_before'].'</div>';



//***************************************************************************************
//* Start form
//***************************************************************************************

	$fp_show_quickpost = get_user_meta( $current_user->ID, 'frontier_post_show_quickpost', true );
	
	echo '<div class="frontier_post_form"> ';
	echo '<form action="'.$frontier_permalink.'" method="post" name="frontier_post" id="frontier_post" enctype="multipart/form-data" >';
	echo '<input type= "hidden" id="fp_show_quickpost" name="fp_show_quickpost" value="'.$fp_show_quickpost.'" >'; 
	
	// do not remove this include, as it holds the hidden fields necessary for the logic to work
	include(FRONTIER_POST_DIR."/forms/frontier_post_form_header.php");	

	wp_nonce_field( 'frontier_add_edit_post', 'frontier_add_edit_post_'.$thispost->ID ); 


?>		
	
	<table class="frontier-post-taxonomies"><tbody><tr>
	<td class="frontier_no_border">
	
	<?php 
		//****************************************************************************************************
		// Action fires before displaying the editor
		// Do action 		frontier_post_form_quickpost_top
		// $thispost 		Post object for the post  
		// $tmp_task_new  	Equals true if the user is adding a post
		//****************************************************************************************************
		
		do_action('frontier_post_form_quickpost_top', $thispost, $tmp_task_new);
	?>
	
	<?php
	if ( fp_get_option_bool("fps_title_required") )
		$fp_title_required = "REQUIRED";
	else
		$fp_title_required = "";
		
	?>
	<!-- Title -->
	<fieldset id="frontier_quickpost_fieldset_title" class="frontier_post_fieldset">
		<legend><?php _e("Title", "frontier-post"); ?></legend>
		<input class="frontier-formtitle" <?php echo $fp_title_required; ?>  placeholder="<?php _e('Enter title here', 'frontier-post'); ?>" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="fp_title" >			
	</fieldset>
	
	<!-- Status -->
	<fieldset id="frontier_quickpost_fieldset_status" class="frontier_post_fieldset">
		<legend><?php _e("Status", "frontier-post"); ?></legend>
	
		<?php if ( fp_get_option_bool("fps_hide_status") )
				{
				echo '<input type="hidden" id="post_status" name="post_status" value="'.$thispost->post_status.'"  >';
				}
			  else
				{
				//echo ' '.__("Status", "frontier-post").': '; 
				?> 
				<select class="frontier_post_dropdown" id="post_status" name="post_status" >
					<?php foreach($status_list as $key => $value) : ?>   
						<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
							<?php echo $value; ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php } ?>	
	</fieldset>
	
	
	
	<?php
	
		if ( $category_type != "hide" && $category_type != "readonly" )
			{
	?>
	<!-- Category -->
	<fieldset id="frontier_quickpost_fieldset_category" class="frontier_post_fieldset">
		<legend><?php _e("Category", "frontier-post"); ?></legend>
	
	<?php
			//echo "selected: ".$cats_selected[0]."<br>";
			if (empty($cats_selected) || count($cats_selected) <= 0)
				{
				$cats_selected[0] = '';
				}
			else 
				$cats_selected[0] = intval($cats_selected[0]);
			
			//echo "selected: ".print_r($cats_selected,0)."<br>";
			
			
			$tmp_field_name			= frontier_tax_field_name("category");
			$tmp_input_field_name	= $tmp_field_name.'[]';
			wp_dropdown_categories(array('taxonomy' => "category", 'id'=>$tmp_field_name, 'exclude' => $cats_excluded, 'hide_empty' => 0, 'name' => $tmp_input_field_name, 'orderby' => 'name', 'selected' => $cats_selected[0], 'hierarchical' => true, 'show_count' => true, 'show_option_none' => __("None", "frontier-post"), 'option_none_value' => '0','class' => 'frontier_post_dropdown')); 
			
	?>			
	</fieldset>
	
	<?php } ?>
	
	
	
	</td></tr><tr><td class="frontier_no_border">
	
	
	<!-- Editor -->
	<fieldset class="frontier_post_fieldset">
		<legend><?php _e("Content", "frontier-post"); ?></legend>	
		<div id="frontier_editor_field"> 
		<?php
		wp_editor($thispost->post_content, 'frontier_post_content', frontier_post_wp_editor_args($fpost_sc_parms['frontier_quick_editor_height']));
		?>
		</div>
	</fieldset>
	
	<?php
	
	//****************************************************************************************************
	// Action fires just before the submit buttons
	// Do action 		frontier_post_form_quickpost
	// $thispost 		Post object for the post  
	// $tmp_task_new  	Equals true if the user is adding a post
	//****************************************************************************************************
	
	do_action('frontier_post_form_quickpost', $thispost, $tmp_task_new);

	?>
	
	</td></tr><tr><td class="frontier_no_border">
	
	<!-- Actions -->
	<fieldset id="frontier_post_fieldset_submit" class="frontier_post_fieldset">
		<legend><?php _e("Actions", "frontier-post"); ?></legend>
		
		<?php
			
		if ( fp_get_option_bool("fps_submit_save") )
		{ ?>
			<button class="button fp_quickpost_button" type="submit" name="user_post_submit" 		id="user_post_save" 	value="save"><?php _e("Save", "frontier-post"); ?></button>
		<?php }
		
		if ( fp_get_option_bool("fps_submit_savereturn") )
		{ ?>
			<button class="button fp_quickpost_button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $fpost_sc_parms['frontier_return_text']; ?></button>
		<?php }
		
		if ( fp_get_option_bool("fps_submit_publish") && ($thispost->post_status !== "publish" || $tmp_task_new) && current_user_can("frontier_post_can_publish") )
		{ ?>
			<button class="button fp_quickpost_button" type="submit" name="user_post_submit" 	id="user_post_publish" 	value="publish"><?php _e("Publish", "frontier-post"); ?></button>
		<?php }
		
		if ( fp_get_option_bool("fps_submit_preview") )
		{ ?>
			<button class="button fp_quickpost_button" type="submit" name="user_post_submit" 	id="user_post_preview" 	value="preview"><?php _e("Save & Preview", "frontier-post"); ?></button>
		<?php } 
		
		if ( fp_get_option_bool("fps_submit_cancel") )
		{ ?>
		<input type="reset" value="<?php _e("Cancel", "frontier-post"); ?>"  name="cancel" id="frontier-post-cancel" onclick="location.href='<?php the_permalink();?>'">
		<?php
		} ?>
		
	</fieldset>
	
	</td></tr></table>
</form> 
	
</div> <!-- ending div -->  

<?php
	
	// end form file
?>