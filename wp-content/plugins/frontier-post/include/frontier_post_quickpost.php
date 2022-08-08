<?php 


function frontier_quickpost($fpost_sc_parms = array() )
	{
	
	//echo "<br> QUICKPOST <br>";
	
	if ( fp_get_option_bool('fps_use_quickpost') && frontier_can_add($fpost_sc_parms['frontier_add_post_type']) )
		{
		// Show/Hide Quickpost
		
		if ( fp_bool($fpost_sc_parms['frontier_force_quickpost']) ) 
			{
			?>
			<style type="text/css">
				#fp-quickpost-hide { display: none }
				#fp-quickpost-show { display: none }
				.frontier-quickpost-show { display: inline-block}
			</style>
			<?php			
			}
		
		?>
		
		
		
		<script type="text/javascript">
		jQuery(document).ready(function($) 
			{
			$("#fp-quickpost-show").click(function ()
				{
				/*alert('show');*/
				$("#frontier-post-quickpost").show();
				$("#fp-quickpost-show").hide();
				$("#fp-quickpost-hide").show();
				$(".frontier-quickpost-hide").hide();
				$(".frontier-quickpost-show").show();
				$("#fp_show_quickpost").val("true");
				}
			);
			$("#fp-quickpost-hide").click(function ()
				{
				/*alert('hide');*/
				$("#frontier-post-quickpost").hide();
				$("#fp-quickpost-show").show();
				$("#fp-quickpost-hide").hide();
				$(".frontier-quickpost-hide").show();
				$(".frontier-quickpost-show").hide();
				$("#fp_show_quickpost").val("false");
				}
			);
			}
	
		);
		</script>
		<?php
	
		if (strlen(trim($fpost_sc_parms['frontier_add_link_text']))>0)
			$tmp_add_text = $fpost_sc_parms['frontier_add_link_text'];
		else
			$tmp_add_text = __("Create New", "frontier-post")." ".fp_get_posttype_label_singular($fpost_sc_parms['frontier_add_post_type']);

	
		echo '<button class="button frontier-post-quickpost-button frontier-quickpost-hide" type="button" name="fp-quickpost-show" id="fp-quickpost-show" value="show">'.$tmp_add_text.'</button>';
		echo '<button class="button frontier-post-quickpost-button frontier-quickpost-show" type="button" name="fp-quickpost-hide" id="fp-quickpost-hide" value="hide">'.__("Hide", "frontier-post")." ".$tmp_add_text.'</button>';
	
		echo '<fieldset id="frontier-post-quickpost" class="frontier-quickpost-show">';
	
		frontier_post_add_edit($fpost_sc_parms, true);
		echo '</fieldset>';
	
		} // fps_use_quickpost
	} //End function



?>