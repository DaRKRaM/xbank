<?php
/**
 * 
 * Show My Posts Widget
 */
class frontier_my_posts_widget extends WP_Widget 
	{
	var $defaults;
	
    /** constructor */
    //function frontier_my_posts_widget() 
	public function __construct()
		{
		include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
	
		$this->defaults = array(
    		'title' 				=> __('My posts','frontier-post'),
    		'post_type' 			=> 'post',
			'post_status' 			=> 'publish',
			'post_status_list'		=> array('publish'),
			'order'					=> 'DESC',
			'orderby' 				=> 'post_date', 	
			'limit' 				=> 8,
    		'category' 				=> 0,
    		'postdateformat'		=> 'd/m',
			'cmtdateformat'			=> 'd/m',
			'showcomments'			=> 'posts',
			'show_pending_posts'	=> false,
    		'show_draft_posts'		=> false,
    		'nolistwrap' 			=> false,
    		'no_posts_text' 		=> __('You have no posts', 'frontier-post'),
			'show_add_post'			=> 1,
			'show_post_count'		=> 1,
			'excerpt_length'		=>50,
			'show_edit_link'		=> 'after',
			'show_delete_link'		=> 'none',
			'post_type'				=> 'post',
			'fp_cache_time'			=> FRONTIER_POST_CACHE_TIME,
			'fp_access_level'		=> 'edit_posts',
    		
			);

		// dropdown of date format for posts using (mysql2date function)
		$tmp_date_formats 			= fp_date_formats();
		$tmp_date_formats['nodate'] = __('Dont show date', 'frontier-post');
		$this->frontier_widget_date_format = $tmp_date_formats;
		
		$tmp_cmt_date_formats = $tmp_date_formats;
		unset($tmp_cmt_date_formats['human']);
		$this->frontier_widget_cmt_date_format = $tmp_cmt_date_formats;
		
		
		$this->frontier_widget_show_comments = array(
			'posts' 			=> __('Only posts', 'frontier-post'),
			'comments' 			=> __('Posts & comments ', 'frontier-post'),
			'excerpts' 			=> __('Posts and comments excerpt', 'frontier-post'),
			);
		
		$this->edit_link_options = array (
    			'before' =>  __('Before','frontier-post'),
    			'after' =>  __('After','frontier-post'),
    			'none' =>  __('No link icon','frontier-post')
    			);
    	
    		
    	
    	
    	$widget_ops = array('description' => __( "List posts of current user (author)", 'frontier-post') );
        parent::__construct('frontier-my-posts', 'Frontier My Posts', $widget_ops);
		}

    /** @see WP_Widget::widget */
    function widget($args, $instance) 
	{
	
	if (is_user_logged_in())
		{
		
		// check access level
		if (array_key_exists('fp_access_level',$instance) && !current_user_can($instance['fp_access_level']) )
			return;
		
			
		global $current_user, $wpdb, $r;
		
    	$instance 			= array_merge($this->defaults, $instance);
    	$frontier_page_link	= get_permalink(fp_get_option('fps_page_id'));
    	$author				= (int) $current_user->ID;
		$rec_limit			= (int) (isset($instance['limit']) ? $instance['limit'] : 10);
		$excerpt_length		= (int) (isset($instance['excerpt_length']) ? $instance['excerpt_length'] : 20);
		
		if (isset( $instance['postdateformat'] ) && $instance['postdateformat'] != 'nodate' )
			$show_date 			= true;
		else
			$show_date 			= false;
		
		if (isset( $instance['cmtdateformat'] ) && $instance['cmtdateformat'] != 'nodate' )
			$show_comment_date 	= true;
		else
			$show_comment_date 	= false;
		
		if (isset( $instance['showcomments'] ) && $instance['showcomments'] != 'posts' )
			$show_comments 		= true;
		else
			$show_comments 		= false;
		
		// Get comment icon from theme, first check local file path, if exists set tu url of icon
		$comment_icon	= frontier_get_icon('comment');
		
		// from version 3.4.6 caching will be available, and as such changed to handle in one array.
		
		// cache name must contain author id as results are specific to authors
		$fp_cache_name		= FRONTIER_MY_POSTS_W_PREFIX.$this->number."-U-".$author;
		$fp_cache_time		= $instance['fp_cache_time'];
		$fp_cache_test		= "Cache active";
		
		//echo "Cache name: ".$fp_cache_name."<br>";
		
		$post_status_list 	= $instance['post_status_list'];
		
		// manage posts status
		if ( !is_array($post_status_list) )	
    		$post_status_list = array($post_status_list);
    		
    	if ( fp_bool($instance['show_pending_posts']) && !in_array('pending', $post_status_list) )
    		$post_status_list[] = 'pending';
    		
    	if ( fp_bool($instance['show_draft_posts']) && !in_array('draft', $post_status_list) )
    		$post_status_list[] = 'draft';
    		
    		
    	//echo "<pre>".print_r($post_status_list, true)."</pre><br>";	
    	
		
		
		if ( ($fp_cache_time <= 0) || (false === ($fp_wdata = get_transient($fp_cache_name))) )
			{
			$fp_wdata 			= array();

			$fp_wdata['tmp_post_cnt']	= $wpdb->get_var("SELECT count(ID) AS tmp_post_cnt FROM $wpdb->posts WHERE post_author = ".$author." AND post_status = 'publish' AND post_type = 'post'" );
						
		
			// Build sql statement	
			if ($show_comments)
				{
				$tmp_sql =  " SELECT ";
				$tmp_sql .= " $wpdb->posts.ID 					AS post_id, ";
				$tmp_sql .= " $wpdb->posts.post_title 			AS post_title, "; 
				$tmp_sql .= " $wpdb->posts.post_date 			AS post_date, ";
				$tmp_sql .= " $wpdb->comments.comment_ID 		AS comment_id, ";
				$tmp_sql .= " $wpdb->comments.comment_author 	AS comment_author, ";
				$tmp_sql .= " $wpdb->comments.comment_date 		AS comment_date, ";
				$tmp_sql .= " $wpdb->comments.comment_approved	AS comment_approved, ";
				$tmp_sql .= " $wpdb->comments.comment_content 	AS comment_content ";
				$tmp_sql .= " FROM $wpdb->posts ";
				$tmp_sql .= "   left OUTER JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID ";
				$tmp_sql .= " WHERE $wpdb->posts.post_status IN ('".implode("','",$post_status_list)."') ";  
				$tmp_sql .= "   AND $wpdb->posts.post_type 	= '".$instance['post_type']."'";  
				$tmp_sql .= "   AND $wpdb->posts.post_author 	= ".$author."";
				$tmp_sql .= " ORDER BY $wpdb->posts.post_date DESC, $wpdb->comments.comment_date_gmt DESC"; 
				$tmp_sql .= " LIMIT ".($rec_limit*5).";";
				}
			else
				{
				$tmp_sql = " SELECT $wpdb->posts.ID 	AS post_id, ";
				$tmp_sql .= " $wpdb->posts.post_title 	AS post_title, "; 
				$tmp_sql .= " $wpdb->posts.post_date 	AS post_date ";
				$tmp_sql .= " FROM $wpdb->posts ";
				$tmp_sql .= " WHERE $wpdb->posts.post_author = ".$author." "; 
				$tmp_sql .= " AND $wpdb->posts.post_status IN ('".implode("','",$post_status_list)."') ";
				$tmp_sql .= " AND $wpdb->posts.post_type = '".$instance['post_type']."' ";   
				$tmp_sql .= " ORDER BY $wpdb->posts.post_date DESC ";
				$tmp_sql .= " LIMIT ".($rec_limit*5).";";
							 // needs to multiply to account for non approved comments
				
				//echo "<pre>".print_r($tmp_sql, true)."</pre><br>";	
    	
				}
			
			
			$fp_wdata['presult'] 	= $wpdb->get_results($tmp_sql);
		
			if ($fp_cache_time <=0 )
				{
				$fp_cache_test		= "Caching disabled";				
				}
			else
				{
				$fp_cache_test		= "Cache refreshed";				
				set_transient($fp_cache_name, $fp_wdata, $fp_cache_time); 
				} 
		} // end caching		
		
		
		echo $args['before_widget'];
    	if( !empty($instance['title']) )
			{
		    echo $args['before_title'];
		    echo $instance['title'];
		    echo $args['after_title'];
			}
    	
		//echo $args['before_widget'];
		//if ( $args['title'] ) echo $args['before_title'] . $args['title'] . $args['after_title']; 
		//$title = apply_filters('widget_title', empty($instance['title']) ? __('My posts') : $instance['title'], $instance, $this->id_base);
		?>
		
		
		
		
		<div  class="frontier-my-post-widget">
		<ul class="frontier-my-post-widget-list">
		
		
		<?php 
		$last_post 	= 0;
		$post_cnt	= 0;
		if ( $fp_wdata['presult'] ) 
			{
			foreach ( $fp_wdata['presult'] as $q_post)
				{
				$tmp_link = "xx";
				if ( $last_post != $q_post->post_id )
					{ 
					// $q_post is not a WP_post object
					$tmp_post = get_post($q_post->post_id);
						
					
					if ($post_cnt >0)
						echo "</li>";
				
					echo '<li class="frontier-my-post-widget-list">';
				
					
					$post_cnt++;
					
					if ($instance['show_edit_link'] == 'before')
						echo frontier_post_edit_link($tmp_post, true, $frontier_page_link, 'fp-widget-post-edit-link' ).' ';
					
					if ($instance['show_delete_link'] == 'before')
						echo frontier_post_delete_link($tmp_post, true, $frontier_page_link, 'fp-widget-post-delete-link' ).' ';
						
				
					
					if ($show_date)
						{
						if ($tmp_post->post_status === 'future' )
							{
							echo mysql2date('Y-m-d H:i', $tmp_post->post_date);
							}
						else
							if ( $instance['postdateformat'] == "human" )
								printf( _x( '%s ago', '%s = human-readable time difference', 'frontier-post' ), human_time_diff( get_the_time( 'U', $tmp_post ), current_time( 'timestamp' ) ) );
							else
								echo mysql2date($instance['postdateformat'], $tmp_post->post_date);
					
						//echo mysql2date($instance['postdateformat'], $q_post->post_date); 
						echo '&nbsp;&nbsp;';
						}
					
					if ($tmp_post->post_status == "publish")
						echo '<a class="frontier-my-post-widget-link frontier-my-post-widget-status-publish" href="'.get_permalink($q_post->post_id).'">'.$q_post->post_title.'</a>';
					else
						echo '<div id="frontier-my-post-widget-status-'.$tmp_post->post_status.'">'.$q_post->post_title.'</div>';
					
					
					if ($instance['show_edit_link'] == 'after')
						echo ' '.frontier_post_edit_link($tmp_post, true, $frontier_page_link, 'fp-widget-post-edit-link' );
					
					if ($instance['show_delete_link'] == 'after')
						echo frontier_post_delete_link($tmp_post, true, $frontier_page_link, 'fp-widget-post-delete-link' ).' ';
					
					
					}
					
					$last_post = $q_post->post_id;
					if ($show_comments && (!empty($q_post->comment_id)) && ($q_post->comment_approved == 1))
						{
						echo "</br>".$comment_icon."&nbsp;&nbsp;";
						if ($show_comment_date)
							{
							echo mysql2date($instance['cmtdateformat'], $q_post->comment_date)." - ";
							}
						echo $q_post->comment_author; 
						if ( $instance['showcomments'] == 'excerpts' )
							{
							$tmp_comment = substr($q_post->comment_content, 0, $excerpt_length);
							if (strlen($q_post->comment_content) > strlen($tmp_comment))
								$tmp_comment = $tmp_comment."...";
							
							echo ":&nbsp"."</br><i>".$tmp_comment."</i>"; 
							}
						}
						
					if ($post_cnt >= $rec_limit)
						{
						break;
						}
				}
			 
			}
			else
			{
				echo "<li>".$instance['no_posts_text']."</li>";
			}
		?>
		</li>
		</ul>
		<?php 
		if ( isset($instance['show_add_post']) && $instance['show_add_post'] == 1 && frontier_can_add($instance['post_type']) )
			{ 
			if ( $instance['post_type'] !== "post" )
				{
				$tmp_post_type_parm = "&frontier_add_post_type=".$instance['post_type'];
				}
			else
				{
				$tmp_post_type_parm = "";
				}
				
				
				
			echo '<p><center><a id"frontier-post-widget-add-link" href="'.frontier_post_add_link().$tmp_post_type_parm.'">'.__("Create New", "frontier-post")." ".fp_get_posttype_label_singular($instance['post_type']).'</a></center></p>';
			} 
		
		// Count authors posts - get_permalink(fp_get_option('fps_page_id'))
		if (isset($instance['show_post_count']) && $instance['show_post_count'] == 1 )
			{ 
			//$tmp_post_cnt	= $wpdb->get_var("SELECT count(ID) AS tmp_post_cnt FROM $wpdb->posts WHERE post_author = ".$author." AND post_status = 'publish' AND post_type = 'post'" );
			$tmp_post_cnt	= $fp_wdata['tmp_post_cnt'];
			echo '<p><center><a id="frontier-post-widget-post-count" href="'.get_permalink(fp_get_option('fps_page_id')).'">'.__("You have published: ", "frontier-post").$tmp_post_cnt.'&nbsp;'.__("posts", "frontier-post").'</a></center></p>';
			}		
		
		
		?>
		</div>
		<?php
		//echo "<pre>".print_r($instance)."<pre>";
		
		
		echo $args['after_widget'];
		}
	else // If not logged in
		{
		// echo "<p>".__("You need to login to see your posts", "frontier-post")."</p>";
		}
    }

//**********************************************************************************************
// Update
//**********************************************************************************************

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) 
		{
		$tmp_boolean_fields = array('show_add_post', 'show_post_count', 'show_pending_posts', 'show_draft_posts');
    	foreach($this->defaults as $key => $value)
			{
    		if( !isset($new_instance[$key]) )
				{
				//check if is one of the logical fields (checkbox) and set value to false, so it isnt empty...
				if (in_array($key, $tmp_boolean_fields))
					$new_instance[$key] = false;
				else
					$new_instance[$key] = $value;
				
				}
			}
    	
		fp_delete_cache_names(FRONTIER_MY_POSTS_W_PREFIX.$this->number);
    	
    	return $new_instance;
    	
		}
		

//**********************************************************************************************
// Form
//**********************************************************************************************		

    /** @see WP_Widget::form */
    function form($instance) 
	{
    	$instance = array_merge($this->defaults, $instance);
    	include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
    	
    	
     
        
        //build post type list
    	$tmp_post_type_list = fp_get_option_array('fps_custom_post_type_list', array());
    	$post_type_list = array();
    	foreach ($tmp_post_type_list as $key => $post_type)
    		{
    		$post_type_list[$post_type] = $post_type;
    		//$post_type_list[$post_type] = fp_get_posttype_label($post_type);
    		}
    	
    	
        
        ?>
        
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post type','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
				<?php foreach($post_type_list as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['post_type']) && $key == $instance['post_type']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_edit_link'); ?>"><?php _e('Show edit link','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('show_edit_link'); ?>" name="<?php echo $this->get_field_name('show_edit_link'); ?>">
				<?php foreach($this->edit_link_options as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['show_edit_link']) && $key == $instance['show_edit_link']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_delete_link'); ?>"><?php _e('Show delete link','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('show_delete_link'); ?>" name="<?php echo $this->get_field_name('show_delete_link'); ?>">
				<?php foreach($this->edit_link_options as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['show_delete_link']) && $key == $instance['show_delete_link']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_draft_posts'); ?>"><?php _e('Show draft posts ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_draft_posts'); ?>" name="<?php echo $this->get_field_name('show_draft_posts'); ?>" value="1" <?php echo ($instance['show_draft_posts'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_pending_posts'); ?>"><?php _e('Show pending posts ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_pending_posts'); ?>" name="<?php echo $this->get_field_name('show_pending_posts'); ?>" value="1" <?php echo ($instance['show_pending_posts'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('postdateformat'); ?>"><?php _e('Post date format','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('postdateformat'); ?>" name="<?php echo $this->get_field_name('postdateformat'); ?>">
				<?php foreach($this->frontier_widget_date_format as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['postdateformat']) && $key == $instance['postdateformat']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('showcomments'); ?>"><?php _e('Show comments','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('showcomments'); ?>" name="<?php echo $this->get_field_name('showcomments'); ?>">
				<?php foreach($this->frontier_widget_show_comments as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['showcomments']) && $key == $instance['showcomments']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cmtdateformat'); ?>"><?php _e('Comment date format','frontier-post'); ?>: </label>
			<select  id="<?php echo $this->get_field_id('cmtdateformat'); ?>" name="<?php echo $this->get_field_name('cmtdateformat'); ?>">
				<?php foreach($this->frontier_widget_cmt_date_format as $key => $value) : ?>   
	 			<option value='<?php echo $key ?>' <?php echo ( !empty($instance['cmtdateformat']) && $key == $instance['cmtdateformat']) ? "selected='selected'" : ''; ?>>
	 				<?php echo $value; ?>
	 			</option>
				<?php endforeach; ?>
			</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of posts & comments','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" size="3" value="<?php echo esc_attr($instance['limit']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Length of comment excerpt','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" size="3" value="<?php echo esc_attr($instance['excerpt_length']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_add_post'); ?>"><?php _e('Show Add Post link ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_add_post'); ?>" name="<?php echo $this->get_field_name('show_add_post'); ?>" value="1" <?php echo ($instance['show_add_post'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_post_count'); ?>"><?php _e('Show Post Count ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_post_count'); ?>" name="<?php echo $this->get_field_name('show_post_count'); ?>" value="1" <?php echo ($instance['show_post_count'] == '1') ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('no_posts_text'); ?>"><?php _e('No post text','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('no_posts_text'); ?>" name="<?php echo $this->get_field_name('no_posts_text'); ?>" value="<?php echo (!empty($instance['no_posts_text'])) ? $instance['no_posts_text']:__('You have no posts', 'frontier-post'); ?>" >
		</p>
		
		</p>
			<label for="<?php echo $this->get_field_id('fp_access_level'); ?>"><?php _e('Minimum access level', 'frontier-post'); ?>: </label>
		
		
		<?php
		
		
			$tmp_html = '<select name="'.$this->get_field_name('fp_access_level').'" >';
			foreach($fp_roles_caps as $key => $value) :    
				$tmp_html = $tmp_html.'<option value="'.$key.'"';
				if ( $key == $instance['fp_access_level'] )
					$tmp_html = $tmp_html.' selected="selected"';
		
				$tmp_html = $tmp_html.'>'.$value.'</option>';	
			endforeach;
			$tmp_html = $tmp_html.'</select>';
			echo $tmp_html; 
		?>
		</p>
			<label for="<?php echo $this->get_field_id('fp_cache_time'); ?>"><?php _e('Cache time ?', 'frontier-post'); ?>: </label>
		
		<!--$fp_cache_time_list-->
		<?php
		
			$tmp_html = '<select name="'.$this->get_field_name('fp_cache_time').'" >';
			foreach($fp_cache_time_list as $key => $value) :    
				$tmp_html = $tmp_html.'<option value="'.$key.'"';
				if ( $key == $instance['fp_cache_time'] )
					$tmp_html = $tmp_html.' selected="selected"';
		
				$tmp_html = $tmp_html.'>'.$value.'</option>';	
			endforeach;
			$tmp_html = $tmp_html.'</select>';
			echo $tmp_html; 
		?>
		
		</p>
        <?php 
    }
    
}    
// add_action('widgets_init', create_function('', 'return register_widget("frontier_my_posts_widget");'));
function frontier_init_my_posts_widget()
{
    return register_widget('frontier_my_posts_widget');
}
add_action ('widgets_init', 'frontier_init_my_posts_widget');

?>