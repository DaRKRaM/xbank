<?php

// set initial div including my id shortcode parameter
echo '<div id="frontier-post-list-'.$fpost_sc_parms['frontier_myid'].'">';


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
	<fieldset id="frontier_post_fieldset_title" class="frontier_post_fieldset">
		<legend><?php _e("Title", "frontier-post"); ?></legend>
		<input class="frontier-formtitle"  placeholder="<?php _e('Enter title here', 'frontier-post'); ?>" type="text" value="<?php if(!empty($thispost->post_title))echo $thispost->post_title;?>" name="user_post_title" id="fp_title" >			
	</fieldset>
	<fieldset id="frontier_post_fieldset_status" class="frontier_post_fieldset">
		<legend><?php _e("Status", "frontier-post"); ?></legend>
	
		<?php if ( fp_get_option_bool("fps_hide_status") )
				{
				echo '<input type="hidden" id="post_status" name="post_status" value="'.$thispost->post_status.'"  >';
				}
			  else
				{
				//echo ' '.__("Status", "frontier-post").': '; 
				?> 
				<select  class="frontier_post_dropdown" id="post_status" name="post_status" >
					<?php foreach($status_list as $key => $value) : ?>   
						<option value='<?php echo $key ?>' <?php echo ( $key == $tmp_post_status) ? "selected='selected'" : ' ';?>>
							<?php echo $value; ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php } ?>	
	</fieldset>
	<fieldset id="frontier_post_fieldset_submit" class="frontier_post_fieldset">
		<legend><?php _e("Actions", "frontier-post"); ?></legend>
		<button class="button" type="submit" name="user_post_submit" 	id="user_post_submit" 	value="savereturn"><?php echo $fpost_sc_parms['frontier_return_text']; ?></button>
	</fieldset>
	
	</td></tr><tr><td class="frontier_no_border">
	<fieldset class="frontier_post_fieldset">
		<legend><?php _e("Content", "frontier-post"); ?></legend>	
		<div id="frontier_editor_field"> 
		<?php
		wp_editor($thispost->post_content, 'frontier_post_content', frontier_post_wp_editor_args($fpost_sc_parms['frontier_editor_height']));
		?>
		</div>
	</fieldset>
	
	</td></tr></table>
</form> 
	
</div> <!-- ending div -->  
</div>
<?php
	
	// end form file
?>