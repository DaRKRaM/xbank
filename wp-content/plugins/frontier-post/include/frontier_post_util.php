<?php
/*
Utilities for Frontier Post plugin.
*/

//*********************************************************************************
// Cache expiration 
//*********************************************************************************

function frontier_post_cache_expiration($tmp_type = "NONE")
	{
	return fp_get_option_int("fps_cache_time_tax_lists", 0);
	
	
	}

function frontier_get_tax_lists($tmp_page_id = 0, $tmp_parent_tax = 0, $fp_cache_time = 0)
	{
	$fp_cache_name		= "frontier_post_tax_cache_".$tmp_page_id;
	//$fp_cache_time		= frontier_post_cache_expiration();
	
	if (  (($fp_cache_time <= 0) || (false === ($form_lists = get_transient($fp_cache_name)))) )
		{
		
		$form_lists			= array();
		$level_sep			= "- ";
		
		$fp_tax_list 		= get_taxonomies(array('public'   => true));
		// remove post formats
		unset($fp_tax_list ['post_format']); 
		
		
		foreach ($fp_tax_list as $tax_id => $tmp_tax_name)
			{
			$tmp_tax_list = array();
			
			if ($tmp_tax_name == 'category')
				{
				$exclude_list	= fp_get_option("fps_excl_cats", '');
				$parent_tax 	= intval($tmp_parent_tax);
				}
			else
				{
				$parent_tax 	= 0;
				$exclude_list 	= "";
				}
			/*
			error_log("-------------------------------------------------");
			error_log("Tax: ".$tmp_tax_name);
			error_log("exclude: ".print_r($exclude_list, true));
			*/
			
			foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $parent_tax, 'exclude' => $exclude_list, 'show_count' => true)) as $tax1) :
				$tmp_tax_list[$tax1->cat_ID] = $tax1->cat_name;
				foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $tax1->cat_ID, 'exclude' => $exclude_list, 'show_count' => true)) as $tax2) :
					$tmp_tax_list[$tax2->cat_ID] = $level_sep.$tax2->cat_name;
					foreach ( get_categories(array('taxonomy' => $tmp_tax_name, 'hide_empty' => 0, 'hierarchical' => 1, 'parent' => $tax2->cat_ID, 'exclude' => $exclude_list, 'show_count' => true)) as $tax3) :
						$tmp_tax_list[$tax3->cat_ID] = $level_sep.$level_sep.$tax3->cat_name;
					endforeach; // Level 3
				endforeach; // Level 2
			endforeach; //Level 1
			
			//error_log("Values: ".print_r($tmp_tax_list, true));
			
			
			$form_lists[$tmp_tax_name] = $tmp_tax_list;
			
			
			
			}
		// only save cache if cache is enabled
		if ($fp_cache_time > 0)
			{
			set_transient($fp_cache_name, $form_lists, $fp_cache_time);
			//echo '<div id="frontier-post-info_text_bottom">cache updated</div>';
			}
		
		}
	else
		{
		//echo '<div id="frontier-post-info_text_bottom">cache read</div>';
		}
	return $form_lists;
	}
	


//********************************************************************************
// Output taxonomy html
//********************************************************************************

function frontier_tax_input($tmp_post_id, $tmp_tax_name, $input_type = 'checkbox', $tmp_selected = array(), $tmp_shortcode_parms, $tmp_tax_list)
	{
	if ( !empty($tmp_tax_name) )
		{
		/*
		if ( $input_type == "readonly" )
			$force_simple = true;
		else
			$force_simple = false;
		*/
		
		
		if ($tmp_tax_name == 'category')
			{
			// need to handle include as this is user role dependendt
			$fp_capabilities	= frontier_post_get_capabilities();
			$cat_incl 			= fp_array_remove_zero(fp_list2array($fp_capabilities[frontier_get_user_role()]['fps_role_allowed_categories']));
			//Remove all array entries that is not included
			if (count($cat_incl)>0)
				$tmp_tax_list = array_intersect_key($tmp_tax_list, array_flip($cat_incl));
			}
		
		//$tmp_selected 			= wp_get_post_terms( $tmp_post_id, $tmp_tax_name, array("fields" => "ids"));		
		$tmp_tax_heading		= $tmp_tax_name;
		$tmp_field_name			= frontier_tax_field_name($tmp_tax_name);
		$tmp_input_field_name	= $tmp_field_name.'[]';
		if ($tmp_tax_name == 'category')
				{
				$exclude_list	= fp_get_option("fps_excl_cats", '');
				}
			else
				{
				$exclude_list 	= "";
				}
		
				
		switch ($input_type) 
			{
			
			case "single":
				if (count($tmp_selected) == 0)
					$tmp_selected[0] = '';
					
				wp_dropdown_categories(array('taxonomy' => $tmp_tax_name, 'id'=>$tmp_field_name, 'exclude' => $exclude_list, 'hide_empty' => 0, 'name' => $tmp_input_field_name, 'orderby' => 'name', 'selected' => $tmp_selected[0], 'hierarchical' => true, 'show_count' => true, 'show_option_none' => __("None", "frontier-post"), 'option_none_value' => '0','class' => 'frontier_post_dropdown')); 
				//echo "selected: ".$tmp_selected[0]."<br>";
				break;
		
			case "multi":
				echo frontier_post_tax_multi($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name, 10);
				//echo '</br><div class="frontier_helptext">'.__("Select category, multiple can be selected using ctrl key", "frontier-post").'</div>';
				break;

			case "checkbox":
				echo frontier_post_tax_checkbox($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;
			
			case "radio":
				echo frontier_post_tax_radio($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;
			
			case "radioline":
				echo frontier_post_tax_radioline($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;	
			
			case "readonly":
				echo frontier_post_tax_readonly($tmp_tax_list , $tmp_selected, $tmp_input_field_name, $tmp_field_name);
				break;
			} // switch
		
		} // if !empty()
			
	
	}	// function frontier_tax_input



//Build html multiselect dropdown for taxonomies
Function frontier_post_tax_multi($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id, $tmp_size)
	{
	$tmp_html = '<select class="frontier_post_dropdown" name="'.$tmp_name.'" id="'.$tmp_id.'" multiple="multiple" size="'.$tmp_size.'">';
	
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<option class="fp_multi" value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.' selected="selected"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'</option>';
	endforeach;
	$tmp_html = $tmp_html.'</select>';
	return $tmp_html;					 
	}

//Build html multiselect checkbox for taxonomies
Function frontier_post_tax_checkbox($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	
	$tmp_html = '';
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<input class="fp_checkbox" type="checkbox" ';
		//$tmp_html = $tmp_html.' id="'.$tmp_id.'"'; 
		$tmp_html = $tmp_html.' name="'.$tmp_name.'"';
		
		$tmp_html = $tmp_html.' value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.' checked="checked"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'<br />'.PHP_EOL;
		endforeach;
	return $tmp_html;	
	}		


//Build html radio button select for taxonomies
Function frontier_post_tax_radio($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	$tmp_html = '';
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<input class="fp_radio" type="radio" ';
		//$tmp_html = $tmp_html.' id="'.$tmp_id.'"'; 
		$tmp_html = $tmp_html.' name="'.$tmp_name.'"';
		
		$tmp_html = $tmp_html.' value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.' checked="checked"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'<br />';
		endforeach; 
	return $tmp_html;	
	}		

//Build html radio button select for taxonomies
Function frontier_post_tax_radioline($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	$tmp_html = '';
	foreach ( $tmp_cat_list as $taxid => $taxname) :
		$tmp_html = $tmp_html.'<input class="fp_radioline" type="radio" ';
		//$tmp_html = $tmp_html.' id="'.$tmp_id.'"'; 
		$tmp_html = $tmp_html.' name="'.$tmp_name.'"';
		
		$tmp_html = $tmp_html.' value="'.$taxid.'"'; 
		if ( $tmp_selected && in_array( $taxid, $tmp_selected ) ) 
			{ 
			$tmp_html = $tmp_html.' checked="checked"'; 
			}
		$tmp_html = $tmp_html.'>'.$taxname.'&nbsp;';
		endforeach; 
	return $tmp_html;	
	}		
//Build html output for readonly taxonomy
Function frontier_post_tax_readonly($tmp_cat_list, $tmp_selected, $tmp_name, $tmp_id)
	{
	
	$tmp_html = '<ul class="fp_readonly_list" >';
	if ( count($tmp_selected) == 0 )
		{
		$tmp_html = $tmp_html.'<li class="fp_readonly_list">'.__("None", "frontier-post")."</li>";
		}
	else
		{
		foreach ( $tmp_selected as $taxid ) :
			$tmp_html = $tmp_html.'<li class="fp_readonly_list">'.$tmp_cat_list[$taxid].'</li>';
		endforeach; 
		}
	$tmp_html = $tmp_html."</ul>";
	return $tmp_html;	
	}		




//********************************************************************************
// Out messages
//********************************************************************************


function frontier_post_set_msg($tmp_msg, $tmp_msg_type = "default")
	{
	if ( ( isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '' ) != '' )
		$_REQUEST['frontier-post-msg'] = $_REQUEST['frontier-post-msg']."<br>".$tmp_msg;
	else
		$_REQUEST['frontier-post-msg'] = $tmp_msg;
	}

function frontier_post_output_msg($tmp_msg_type = "default")
	{
	if ( fp_get_option_bool("fps_show_msg") )
		{
		$tmp_msg = isset($_REQUEST['frontier-post-msg']) ? $_REQUEST['frontier-post-msg'] : '';
		
		//****************************************************************************************************
		// Apply filter before output of message
		// filter:			frontier_post_msg_output
		// $tmp_msg 		The message retrieved from the form
		// $tmp_msg_type  	Message type	
		//****************************************************************************************************
		
		$tmp_msg = apply_filters( 'frontier_post_msg_output', $tmp_msg, $tmp_msg_type );
		
		echo '<div class="frontier_post_msg">'.$tmp_msg.'</div>';
		}
	// This way cusom messages can be output	
	do_action('frontier_post_custom_msg', $tmp_msg, $tmp_msg_type );
	
	$_REQUEST['frontier-post-msg'] = null;
	}
	
	


//********************************************************************************
// Check post type functions
//********************************************************************************

// Return list of post types
function fp_get_post_type_list()
		{
		$tmp_pt_array = get_post_types(array('public'   => true));
		if (array_key_exists('attachment', $tmp_pt_array))
			unset($tmp_pt_array['attachment']);	
			
		return $tmp_pt_array;
		}


//Default list of allowed post types for a user
function fp_default_post_type_list()
	{
	$tmp_pt_array = fp_get_option_array('fps_custom_post_type_list');
	if ( !current_user_can('frontier_post_can_page') )
		{
		if(($tmp_key = array_search('page', $tmp_pt_array)) !== false) 
			unset($tmp_pt_array[$tmp_key]);
		}
	return $tmp_pt_array;	
	}


// return allowed post types of $tmp_pt_array
function fp_validate_post_type_list($tmp_pt_array)
	{
	return array_intersect($tmp_pt_array, fp_default_post_type_list() );	
	}


// Check if user can add/edit/delete posts with this post_type	
function fp_check_post_type($tmp_post_type)
	{
	if ( in_array($tmp_post_type, fp_default_post_type_list() ) )  
		return true;
	else
		return false;	
	}

// Get name (label) of post type (plural)
function fp_get_posttype_label($tmp_pt_name)
	{
	$tmp_pt = get_post_type_object($tmp_pt_name);
	return $tmp_pt->label;
	}

// Get singular name (label) of post type
function fp_get_posttype_label_singular($tmp_pt_name)
	{
	$tmp_pt_name = trim($tmp_pt_name, '"');
	$tmp_pt_name = trim($tmp_pt_name, "'");
	$tmp_pt = get_post_type_object($tmp_pt_name);
	return $tmp_pt->labels->singular_name;
	}

//********************************************************************************
// Check Taxonomy functions
//********************************************************************************

// return list of public taxonomies
function fp_get_tax_list()
	{
	return get_taxonomies(array('public'   => true, '_builtin' => false));
	}


//Default list of allowed taxonomies for a user
function fp_default_tax_list()
	{
	return fp_get_option_array('fps_custom_tax_list');	
	}



// return allowed taxonomies of $tmp_tax_array
function fp_validate_tax_list($tmp_tax_array)
	{
	return array_intersect($tmp_tax_array, fp_default_tax_list() );	
	}

// Check if it is an allowed taxonomy	
function fp_check_tax($tmp_tax)
	{
	if ( array_search($tmp_tax, fp_default_tax_list() ) !== false)  
		return true;
	else
		return false;	
	}
	
//Check that the number of elements in the layout array corresponds to the length of the array of taxonomies

function fp_get_tax_layout($tax_list, $layout_list = array())
	{
	$tmp_layout_list 	= array();
	
	if ( !array_key_exists('category', $tax_list) )
		{
		$fp_capabilities = frontier_post_get_capabilities();
		$tmp_layout_list['category'] = $fp_capabilities[frontier_get_user_role()]['fps_role_category_layout'] ? $fp_capabilities[frontier_get_user_role()]['fps_role_category_layout'] : "multi";
		}
	
	if ( count($tax_list) > 0  )
		{
		include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
		$chk_layout = array_values($category_types);
		$s 			= 0;
		
		foreach ($tax_list as $tmp_tax)
			{
			if ( ($s >= count($layout_list)) || empty($layout_list[$s]) )
				$tmp_layout = fp_get_option('fps_default_tax_select', 'radio');
			else
				$tmp_layout = $layout_list[$s];
				
			// Check that it is a valid layout
			if ( !in_array($tmp_layout, $chk_layout, true) )
				$tmp_layout = fp_get_option('fps_default_tax_select', 'radio');
			
			$tmp_layout_list[$tmp_tax] = $tmp_layout;
			
			$s++;
			}
		}
	
	return $tmp_layout_list;	
	}
	

function frontier_tax_field_name($tmp_tax_name)
	{
	return 'fp_tax_'.$tmp_tax_name;
	}




// Get name (label) of taxonomy (plural)
function fp_get_tax_label($tmp_tax_name)
	{
	$tmp_tax = get_taxonomy($tmp_tax_name);
	return $tmp_tax->label;
	}

// Get singular name (label) of taxonomy
function fp_get_tax_label_singular($tmp_tax_name)
	{
	$tmp_tax = get_taxonomy($tmp_tax_name);
	return $tmp_tax->labels->singular_name;
	}

// Get all taxonomy values for a post
function fp_get_tax_values($postid, $tmp_sep = " | ")
	{
	$tax_list = array_merge( array("category", "post_tag"), fp_array_remove_blanks(fp_default_tax_list()) );
	
	$post_terms = wp_get_post_terms($postid, $tax_list, array("fields" => "all") );
	
					
	$tmp_output = "";
	
	foreach ($tax_list as $key => $tax_slug)
		{
			$term_list 		= array();
			foreach ($post_terms as $term)
				{
				if ($term->taxonomy == $tax_slug)
					{
					$term_link = get_term_link( $term );
					if ( !is_wp_error($term_link) ) 
						$term_list[$term->slug] = '<a href="'.esc_url( $term_link ).'">'.$term->name.'</a>';				
					}
				}
			
			if (count($term_list)>0)
				{
				$tmp_output .= $tmp_sep.fp_get_tax_label($tax_slug).": ";
				$tmp_output .= implode(", ", $term_list);
				}
		} // foreach tax names
	
	return $tmp_output;
	}

//********************************************************************************
// get icon img url
// 1: Look in the frontier post template folder
// 2: if not found get the default one from /frontier-post/images
//********************************************************************************



function frontier_get_icon($tmp_icon, $tmp_class = '')
	{
	// first Frontier Post template folder
	$return_icon				= FRONTIER_POST_TEMPLATE_DIR.'/'.$tmp_icon.'.png';
	
	if ( strlen(trim($tmp_class)) > 0) 
			$tmp_class .= '-'.$tmp_icon;
	
	//error_log($return_icon);
	
	if (file_exists($return_icon))
		{
		$return_icon_html			= '<img id="frontier-post-list-icon-'.$tmp_icon.'" class="frontier-post-list-icon '.$tmp_class.'" src="'.FRONTIER_POST_TEMPLATE_URL.$tmp_icon.'.png'.'"></img>';
		}
	else
		{
		// then the default icon from plugin
		$return_icon_html			= '<img id="frontier-post-list-icon-'.$tmp_icon.'" class="frontier-post-list-icon '.$tmp_class.'" src="'.FRONTIER_POST_URL.'images/'.$tmp_icon.'.png'.'"></img>';
		}	
	return $return_icon_html;
	}

//********************************************************************************
// get comment icon for the list
// 1: Look in the frontier post template folder
// 2: if not found look in the active theme (not child theme)
// 3: Fall back, standard wordpress comment icon
//********************************************************************************



function frontier_get_comment_icon()
	{
	// first Frontier Post template folder
	$comment_icon				= FRONTIER_POST_TEMPLATE_DIR.'/comments.png';
	if (file_exists($comment_icon))
		{
		$comment_icon_html			= '<img src="'.FRONTIER_POST_TEMPLATE_URL.'comments.png"></img>';
		}
	else
		{
		// Then the theme (not child theme folder)
		$comment_icon				= get_template_directory()."/images/comments.png";
		// if no icon in theme, check wp-includes, and if it isnt the use a space
		if (file_exists($comment_icon))
			{
			$comment_icon_html			= "<img src='".get_template_directory_uri()."/images/comments.png'></img>";
			}
		else
			{
			// Fallback, the standard wp comment icon
			$comment_icon_html	= "<img src='".includes_url()."images/wlw/wp-comments.png'></img>";
			}
		}	
	return $comment_icon_html;
	}

//********************************************************************************
// Display Frontier Post Icon or Link
//********************************************************************************
function frontier_post_display_links($fp_post, $fp_show_icons = true, $tmp_plink = "default", $tmp_class = '')
	{
	if ( $tmp_plink == "default" )
		{
		$tmp_plink = get_permalink(fp_get_option('fps_page_id'));
		}
	
	$tmp_out = '';
	$tmp_out .= frontier_post_edit_link($fp_post, $fp_show_icons, $tmp_plink, $tmp_class);
	$tmp_out .= frontier_post_approve_link($fp_post, $fp_show_icons, $tmp_plink, $tmp_class);
	$tmp_out .= frontier_post_clone_link($fp_post, $fp_show_icons, $tmp_plink, $tmp_class);
	$tmp_out .= frontier_post_delete_link($fp_post, $fp_show_icons, $tmp_plink, $tmp_class);
	$tmp_out .= frontier_post_preview_link($fp_post, $fp_show_icons, $tmp_plink, $tmp_class);
	return $tmp_out;
	}
//********************************************************************************
// Display edit Icon or Link
//********************************************************************************

function frontier_post_edit_link($fp_post, $fp_show_icons = true, $tmp_plink, $tmp_class = '')
	{
	$fp_return = '';
	
	if (frontier_can_edit($fp_post) == true)
		{
		
		$concat= get_option("permalink_structure")?"?":"&";    
		if ($fp_show_icons)
			{
			$fp_return = '<a title='.__("Edit", "frontier-post").' class="frontier-post-list-icon '.$tmp_class.'" id="frontier-post-list-icon-edit" href="'.$tmp_plink.$concat.'task=edit&postid='.$fp_post->ID.'">'.frontier_get_icon('edit', $tmp_class).'</a>';	
			}
		else
			{
			$fp_return = '<a class="frontier-post-list-text '.$tmp_class.'" id="frontier-post-list-text-edit" href="'.$tmp_plink.$concat.'task=edit&postid='.$fp_post->ID.'">'.__("Edit", "frontier-post").'&nbsp;&nbsp;</a>';
			}
		}
	return $fp_return;
	}

//********************************************************************************
// Display DELETE Icon or Link
//********************************************************************************

function frontier_post_delete_link($fp_post, $fp_show_icons = true, $tmp_plink,  $tmp_class = '')
	{
	$fp_return = '';
	if (frontier_can_delete($fp_post) == true)
		{
		
		$concat= get_option("permalink_structure")?"?":"&";    
		if ($fp_show_icons)
			{
			$fp_return = '<a title='.__("Delete", "frontier-post").' class="frontier-post-list-icon '.$tmp_class.'" id="frontier-post-list-icon-delete" href="'.$tmp_plink.$concat.'task=delete&postid='.$fp_post->ID.'">'.frontier_get_icon('delete', $tmp_class).'</a>';	
			}
		else
			{
			$fp_return = '<a class="frontier-post-list-text '.$tmp_class.'" id="frontier-post-list-text-delete" href="'.$tmp_plink.$concat.'task=delete&postid='.$fp_post->ID.'">'.__("Delete", "frontier-post").'&nbsp;&nbsp;</a>';
			}
		}
	return $fp_return;
	}

//********************************************************************************
// Display APPROVE Icon or Link
//********************************************************************************

function frontier_post_approve_link($fp_post, $fp_show_icons = true, $tmp_plink,  $tmp_class = '')
	{
	$fp_return = '';
	if ($fp_post->post_status === "pending" && current_user_can("edit_others_posts") )
		{
		
		$concat= get_option("permalink_structure")?"?":"&";    
		if ($fp_show_icons)
			{
			$fp_return = '<a title='.__("Approve", "frontier-post").' class="frontier-post-list-icon '.$tmp_class.'" id="frontier-post-list-icon-approve" href="'.$tmp_plink.$concat.'task=approve&postid='.$fp_post->ID.'">'.frontier_get_icon('approve', $tmp_class).'</a>';	
			}
		else
			{
			$fp_return = '<a class="frontier-post-list-text '.$tmp_class.'" id="frontier-post-list-text-approve" href="'.$tmp_plink.$concat.'task=approve&postid='.$fp_post->ID.'">'.__("Approve", "frontier-post").'&nbsp;&nbsp;</a>';
			}
		}
	return $fp_return;
	}

//********************************************************************************
// Display CLONE Icon or Link
//********************************************************************************

function frontier_post_clone_link($fp_post, $fp_show_icons = true, $tmp_plink,  $tmp_class = '')
	{
	$fp_return = '';
	if (current_user_can("frontier_post_can_clone") )
		{
		
		$concat= get_option("permalink_structure")?"?":"&";    
		if ($fp_show_icons)
			{
			$fp_return = '<a title='.__("Clone", "frontier-post").' class="frontier-post-list-icon '.$tmp_class.'" id="frontier-post-list-icon-clone" href="'.$tmp_plink.$concat.'task=clone&postid='.$fp_post->ID.'">'.frontier_get_icon('clone', $tmp_class).'</a>';	
			}
		else
			{
			$fp_return = '<a class="frontier-post-list-text '.$tmp_class.'" id="frontier-post-list-text-clone" href="'.$tmp_plink.$concat.'task=clone&postid='.$fp_post->ID.'">'.__("Clone", "frontier-post").'&nbsp;&nbsp;</a>';
			}
		}
	return $fp_return;
	}

//********************************************************************************
// Display Preview Icon or Link
//********************************************************************************

function frontier_post_preview_link($fp_post, $fp_show_icons = true, $tmp_plink,  $tmp_class = '')
	{
	
	$fp_return = '';
	$concat= get_option("permalink_structure")?"?":"&";    
	if ($fp_show_icons)
		{
		$fp_return = '<a title='.__("Preview", "frontier-post").' class="frontier-post-list-icon '.$tmp_class.'" id="frontier-post-list-icon-preview" href="'.site_url().'/?p='.$fp_post->ID.'&preview=true">'.frontier_get_icon('view', $tmp_class).'</a>';	
		}
	else
		{
		$fp_return = '<a class="frontier-post-list-text '.$tmp_class.'" id="frontier-post-list-text-preview" href="'.site_url().'/?p='.$fp_post->ID.'&preview=true">'.__("Preview", "frontier-post").'&nbsp;&nbsp;</a>';
		}
	return $fp_return;
	}

//********************************************************************************
// Get settings/options
//********************************************************************************

function frontier_post_get_capabilities()
	{
	$fps_capabilities = get_option(FRONTIER_POST_CAPABILITY_OPTION_NAME, array() );
	if ( count($fps_capabilities) == 0 )
		error_log("Unable to load frontier_post_capabilities or empty");
	
	return $fps_capabilities;
	}

function frontier_post_get_settings()
	{
	$fps_settings = get_option(FRONTIER_POST_SETTINGS_OPTION_NAME, array() );
	if ( count($fps_settings) == 0 )
		$fps_settings = array();
		
	return $fps_settings;
	}

function fp_get_option($tmp_option_name, $tmp_default = '')
	{
	$fp_settings = frontier_post_get_settings();
	
	if ( array_key_exists($tmp_option_name, $fp_settings) )
		return $fp_settings[$tmp_option_name];
	else
		{
		return $tmp_default;
		}
	}

function fp_get_option_int($tmp_option_name, $tmp_default = 0)
	{
	$fp_settings = frontier_post_get_settings();
	
	if ( array_key_exists($tmp_option_name, $fp_settings) )
		return intval($fp_settings[$tmp_option_name]);
	else
		{
		return intval($tmp_default);
		}
	}

function fp_get_option_array($tmp_option_name, $tmp_default = array())
	{
	$fp_settings = frontier_post_get_settings();
	
	if ( is_array($fp_settings[$tmp_option_name]) )
		return $fp_settings[$tmp_option_name];
	else
		return array($fp_settings[$tmp_option_name]);
		
	}

function fp_get_option_bool($tmp_option_name)
	{
	$fp_settings = frontier_post_get_settings();	
	if ( array_key_exists($tmp_option_name, $fp_settings) )
		{
		$tmp_value = ($fp_settings[$tmp_option_name] ? $fp_settings[$tmp_option_name] : "false");
		return fp_bool($tmp_value);
		}
	else
		{
		return false;
		}
	}


function fp_bool($tmp_value)
	{
	if ( in_array($tmp_value, array('true', 'True', 'TRUE', 'yes', 'Yes', 'y', 'Y', '1','on', 'On', 'ON', true, 1), true) )
		return true;
	else
		return false;
	}
//********************************************************************************
// Editor
//********************************************************************************



function frontier_post_wp_editor_args($editor_lines = 300)
	{
	global $dfw;
	$media_button	= current_user_can( 'frontier_post_can_media' ) ? true : false;
	
	//$editor_layout	= array('dfw' => $dfw, 'editor_height' => $editor_lines, 'media_buttons' => $media_button );
	$editor_layout	= array('dfw' => false, 'editor_height' => $editor_lines, 'media_buttons' => $media_button );
	
	//$editor_layout		 	= array('dfw' => false, 'tabfocus_elements' => 'sample-permalink,post-preview', 'editor_height' => 300 );
	
	
	
	// If capabilities is managed from other plugin, use default setting for all profiles
	if ( fp_get_option_bool("frontier_post_external_cap") )
		{
		$editor_type 		= fp_get_option("fps_default_editor", "full");
		}
	else
		{
		//Get Frontier Post capabilities
		$user_role 			= frontier_get_user_role();
		$fp_capabilities	= frontier_post_get_capabilities();
		$editor_type 		= $fp_capabilities[$user_role]['fps_role_editor_type'] ? $fp_capabilities[$user_role]['fps_role_editor_type'] : "full"; 
		
		}
	
	
	if ($editor_type == "full")
		$editor_layout = array_merge($editor_layout, array('teeny' => false,  'tinymce' => true));
	
	
	if ($editor_type == "minimal-visual")
		$editor_layout = array_merge($editor_layout, array('teeny' => true));
	
	if ($editor_type == "minimal-html")
		$editor_layout = array_merge($editor_layout, array('teeny' => true, 'tinymce' => false));
		
	if ($editor_type == "text")	
		$editor_layout = array_merge($editor_layout, array('tinymce' =>false));
	
	
	
	//unset($editor_layout["quicktags"]);
	
	
	//error_log("**Editor Args**");
	//error_log(print_r($editor_layout,true));
		
	
	return $editor_layout;
	}


function fp_login_text()
	{
	if (fp_get_option_bool('fps_use_custom_login_txt', false))
		{
		$out = fp_get_option('fps_custom_login_txt', __("Please log in !", "frontier-post"));
		}
	else
		{
		include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
		$out  = '';
		$out .= "<br>---- ";
		if (fp_get_option_bool("fps_show_login", false) )
				{
				$out .= $frontier_default_login_txt;
				}
			else
				{
				$out .= __("Please log in !", "frontier-post");
				}	
		$out .=  " ------<br><br>";
		}
	return '<div id="frontier-post-login-msg">'.stripslashes($out).'</div>';	
	
	}

//*********************************************************************************
// Remove zero from arrays
//*********************************************************************************

function fp_array_remove_zero($tmp_array)
	{
	foreach ($tmp_array as $key => $value) 
		{
    	if (intval($value) == 0 )  
        	unset($tmp_array[$key]);
    	}
    return $tmp_array;
	}
	
//*********************************************************************************
// Remove blanks from arrays
//*********************************************************************************

function fp_array_remove_blanks($tmp_array)
	{
	foreach ($tmp_array as $key => $value) 
		{
    	if (strlen(trim($value)) == 0 || $value == "0" )  
        	unset($tmp_array[$key]);
    	}
    return $tmp_array;
	}

//*********************************************************************************
// Converts comma separated list to array
//*********************************************************************************
		
function fp_list2array($tmp_list)
	{
	if (is_array($tmp_list))
		{
		$tmp_array = $tmp_list;
		}
	else
		{
		if ($tmp_list > " ")
			$tmp_array = explode(",", $tmp_list);
		else
			$tmp_array = array();
		}		
	return $tmp_array;
	}


//********************************************************************************
// Transform tags lower/upper case, First letter, None
//********************************************************************************

function fp_tag_transform($tmp_tag)
	{
	$tmp_transform = fp_get_option('fps_tags_transform', 'none');
	
	switch ($tmp_transform)
		{
		case 'lower':
			return strtolower(sanitize_text_field($tmp_tag));
	
		case 'upper':
			return strtoupper(sanitize_text_field($tmp_tag));
	
		case 'ucwords':
			return ucwords(sanitize_text_field($tmp_tag));
	
		default:
			return sanitize_text_field($tmp_tag);
		}
	}

//********************************************************************************
// Frontier post date formats
//********************************************************************************
	
function fp_date_formats($incl_human = true)
	{
	$fp_list_date_formats = array();	
	$fp_list_date_formats = array(
			'd/m-y' 	=> 'dd/mm-yy',
			'd/m-Y' 	=> 'dd/mm-yyyy',
			'd/m-y H:i'	=> 'dd/mm-yy hh:mm',
			'd/m-Y H:i'	=> 'dd/mm-yyyy hh:mm',
			'd/m' 		=> 'dd/mm',
			'd/m H:i'	=> 'dd/mm hh:mm',
			'd.m.y' 	=> 'dd.mm.yy',
			'd.m.Y' 	=> 'dd.mm.yyyy',
			'd.m.y H:i'	=> 'dd.mm.yy hh:mm',
			'd.m.Y H:i'	=> 'dd.mm.yyyy hh:mm',
			'm/d' 		=> 'mm/dd',
			'm/d g:i a' 	=> 'mm/dd am/pm',
			'm/d-y' 	=> 'mm/dd-yy',
			'm/d-Y' 	=> 'mm/dd-yyyy',
			'm/d-y g:i a' => 'mm/dd-yy am/pm',
			'm/d-Y g:i a' => 'mm/dd-yyyy am/pm',
			'y-m-d' 	=> 'yy-m-d',
			'Y-m-d' 	=> 'yyyy-mm-dd',
			'y-m-d H:i' 	=> 'yy-m-d hh:mm',
			'Y-m-d H:i' 	=> 'yyyy-mm-dd hh:mm',
			'F j, Y' 	=> 'mmmm d, yyyy',
			'F j, y' 	=> 'mmmm d, yy',
			'j. F Y' 	=> 'd. mmmm yyyy',
			'j. F y' 	=> 'd. mmmm yy',
			
			 );

	// Make sure default date t ime settings is present in the format options

	$tmp_datetime = get_option( 'date_format' );
	if ( !array_key_exists($tmp_datetime, $fp_list_date_formats) )
		$fp_list_date_formats[$tmp_datetime] = $tmp_datetime." (".date($tmp_datetime)." )";

	$tmp_datetime = get_option( 'date_format' )." ".get_option( 'time_format' );
	if ( !array_key_exists($tmp_datetime, $fp_list_date_formats) )
		$fp_list_date_formats[$tmp_datetime] = $tmp_datetime." (".date($tmp_datetime)." )";


	// Set example format in valuse
	foreach ($fp_list_date_formats as $key => $value)
		{
		$fp_list_date_formats[$key] = $key." (".date($key)." )";
		}


	// Insert Human readable if true (default)
	if ( $incl_human )
		$fp_list_date_formats['human']		= 'Human Readable';
	
	
	return $fp_list_date_formats;
	}	

//********************************************************************************
// Delete users cache for my posts
//********************************************************************************

function fp_delete_my_posts_cache($tmp_user_id)
	{
	global $wpdb;
	$tmp_user_id = intval($tmp_user_id);
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%_fpuser_".$tmp_user_id."'");
	}

//********************************************************************************
// Generate output to view post
//********************************************************************************

function fp_view_post($view_post)
	{
	
	//post variable: $preview_post
	$tmp_content = apply_filters( 'the_content', $view_post->post_content );
	$tmp_content = str_replace( ']]>', ']]&gt;', $tmp_content );
	return $tmp_content;
	}
//********************************************************************************
// Delete users cache for my posts
//********************************************************************************

function fp_delete_widget_cache()
	{
	fp_delete_cache_names(FRONTIER_MY_POSTS_W_PREFIX);
	fp_delete_cache_names(FRONTIER_MY_APPROVALS_W_PREFIX);
	}

function fp_delete_my_post_w_cache()
	{
	$current_user = wp_get_current_user();
	$names = fp_get_cache_names(FRONTIER_MY_POSTS_W_PREFIX);
	foreach ($names as $key => $value)
		{
		if (strpos($value, "-U-".$current_user->ID) !== false) 
			{
			//error_log("Deleting transient: ".substr($value, 11));
			delete_transient( substr($value, 11) );
			}
		}
	return;	
	}

//********************************************************************************
//  Get array transient names based on prefix
//********************************************************************************

function fp_get_cache_names($prefix)
		{
		$names = array();
		if ( strlen($prefix) == 0 )
			return $names;
		
		global $wpdb;
		$fq_sql = "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '_transient_".$prefix."%';";
		
		$fq_results 	= $wpdb->get_results($fq_sql);
		
		foreach ($fq_results as $fq_result)
			{
			$names[] = $fq_result->option_name;
			}
		return $names;
		}

//********************************************************************************
//  delete transient names based on prefix
//********************************************************************************

function fp_delete_cache_names($prefix)
		{
		$names = fp_get_cache_names($prefix);
		foreach ($names as $key => $value)
			{
			//error_log("Deleting transient: ".substr($value, 11));
			delete_transient( substr($value, 11) );
			}
		return;
		}

?>