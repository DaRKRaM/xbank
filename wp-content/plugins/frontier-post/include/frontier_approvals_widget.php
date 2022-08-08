<?php
/**
 * 
 * Show approvals (only admins)
 */
class frontier_approvals_widget extends WP_Widget 
	{
	
	var $defaults;
	
    public function __construct() 
   	{
		//include(FRONTIER_POST_DIR."/include/frontier_post_defaults.php");
	
		$this->defaults = array(
    		'title' 			=> __('My approvals','frontier-post'),
			'show_draft'		=> false,
			'draft_count'		=> 0,
			'show_pending'		=> true,
			'pending_count'		=> 5,
			'postdateformat'	=> 'd/m',
			'show_author'		=> false,
			'show_comments'		=> false,
			'show_comment_spam'	=> true,
    		'show_edit_link'		=> 'after',
			'show_delete_link'		=> 'none',
    		'nolistwrap' 		=> true,
    		'fp_cache_time'		=> FRONTIER_POST_CACHE_TIME,
    		'no_approvals_text'	=> __('You have no approvals pending', 'frontier-post'),
			);

		// dropdown of date format for posts using (mysql2date function)
		$tmp_date_formats 			= fp_date_formats();
		$tmp_date_formats['nodate'] = __('Dont show date', 'frontier-post');
		$this->frontier_widget_date_format = $tmp_date_formats;
		
		
		$this->edit_link_options = array (
    			'before' =>  __('Before','frontier-post'),
    			'after' =>  __('After','frontier-post'),
    			'none' =>  __('No link icon','frontier-post')
    			);
    	
    	$widget_ops = array('description' => __( "List number of posts and comments for approval", 'frontier-post') );
        //parent::WP_Widget(false, $name = 'Frontier My Approvals', $widget_ops);	
		//parent::__construct('frontier-my-posts', 'Frontier My Posts', $widget_ops);
		parent::__construct('frontier-my-approvals', 'Frontier My Approvals', $widget_ops);	
	
		
		}

    /** @see WP_Widget::widget */
    function widget($args, $instance) 
	{
	
	if(is_user_logged_in() && current_user_can("edit_others_posts"))
		{
		$instance 			= array_merge($this->defaults, $instance);
		$frontier_page_link	= get_permalink(fp_get_option('fps_page_id'));
    	
		// from version 3.4.6 caching will be available, and as such changed to handle in one array.
		$fp_cache_name		= FRONTIER_MY_APPROVALS_W_PREFIX.$this->number;
		$fp_cache_time		= $instance['fp_cache_time'];
		$fp_cache_test		= "Cache active";
		global $wpdb;
		
    	
	
		//error_log(print_r($instance), true);
	
		if ( ($fp_cache_time <= 0) || (false === ($fp_wdata = get_transient($fp_cache_name))) )
			{
			$fp_wdata 		= array();
			if (isset( $instance['show_draft'] ) && $instance['show_draft'] == true )
				{
				$fp_wdata['draft_cnt']	= $wpdb->get_var("SELECT count(id) AS draft_cnt FROM $wpdb->posts WHERE post_status = 'draft'");
				$fp_wdata['draft_txt']	= $fp_wdata['draft_cnt'].' '.__('draft posts','frontier-post');
				$fp_wdata['show_draft']	= true;
				}
			else
				{
				$fp_wdata['show_draft']	= false;
				}
		
			if (isset( $instance['show_pending'] ) && $instance['show_pending'] == true )
				{
				$fp_wdata['pending_cnt']	= $wpdb->get_var("SELECT count(id) AS pending_cnt FROM $wpdb->posts WHERE post_status = 'pending'");
				$fp_wdata['pending_txt']	= $fp_wdata['pending_cnt'].' '.__('posts to be approved','frontier-post');
				$fp_wdata['show_pending']	= true;
				}
			else
				{
				$fp_wdata['show_pending']	= false;
				}
		
			if (isset( $instance['show_comments'] ) && $instance['show_comments'] == true )
				{
				$fp_wdata['cmt_pending_cnt']	= $wpdb->get_var("SELECT count(comment_ID) AS cmt_pending_cnt FROM $wpdb->comments WHERE comment_approved = 0");
				$fp_wdata['cmt_pending_txt']	= $fp_wdata['cmt_pending_cnt'].' '.__('comments to be approved','frontier-post');
				$fp_wdata['show_comments'] 		= true;
				}
			else
				{
				$fp_wdata['show_comments']		= false;
				}
		
			if (isset( $instance['show_comment_spam'] ) && $instance['show_comment_spam'] == true )
				{
				$fp_wdata['cmt_spam_cnt']		= $wpdb->get_var("SELECT count(comment_ID) AS cmt_pending_cnt FROM $wpdb->comments WHERE comment_approved = 'spam'");
				$fp_wdata['cmt_spam_txt']		= $fp_wdata['cmt_spam_cnt'].' '.__('spam comments','frontier-post');
				$fp_wdata['show_comment_spam'] 	= true;
				}
			else
				{
				$fp_wdata['show_comment_spam']	= false;
				}
			
			if ( intval($instance['pending_count']) > 0  )
				{
				//$tmp_sql 					= "";
				$tmp_sql 					= "SELECT ID AS post_id, post_title AS post_title FROM $wpdb->posts WHERE post_status = 'pending' ORDER BY post_date DESC LIMIT ".intval($instance['pending_count'])." ";
				$fp_wdata['pending_list']	= $wpdb->get_results($tmp_sql);
				$fp_wdata['pending_count']	= intval($instance['pending_count']);
				}
			else
				{
				$fp_wdata['pending_count']	= 0;
				}
			
			if ( intval($instance['draft_count']) > 0  )
				{
				//$tmp_sql 					= "";
				$tmp_sql 					= "SELECT ID AS post_id, post_title AS post_title FROM $wpdb->posts WHERE post_status = 'draft' ORDER BY post_date DESC LIMIT ".intval($instance['draft_count'])." ";
				$fp_wdata['draft_list']		= $wpdb->get_results($tmp_sql);
				$fp_wdata['draft_count']	= intval($instance['draft_count']);
				}
			else
				{
				$fp_wdata['draft_count']	= 0;
				}
			
			$fp_wdata['postdateformat'] = $instance['postdateformat'];
		
			$fp_wdata['show_author'] = fp_bool($instance['show_author']);
		
		
			
			
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
    	
		
		
		//echo print_r($instance,true)."<br>";
		
		
		echo '<div  class="frontier-my-post-widget">';
		echo '<ul>';
		if ($fp_wdata['show_pending']) 
			{ 
			if (fp_get_option_int('fps_pending_page_id',0) > 0)
				$tmp_pending_link = get_permalink(fp_get_option('fps_pending_page_id'));
			else
				$tmp_pending_link = site_url('/wp-admin/edit.php?post_status=pending&post_type=post');
			
			if (fp_get_option_int('fps_draft_page_id',0) > 0)
				$tmp_draft_link = get_permalink(fp_get_option('fps_draft_page_id'));
			else
				$tmp_draft_link = site_url('/wp-admin/edit.php?post_status=draft&post_type=post');
			
			echo '<li>';
			echo '<a href="'.$tmp_pending_link.'">'.$fp_wdata['pending_txt'].'</a>';
			echo '</li>';
			} 

		if ( array_key_exists('pending_count', $fp_wdata)  && $fp_wdata['pending_count'] > 0)
			{
			if (!$fp_wdata['show_pending'])
				echo '<li>'.__("Pending posts", "frontier-post").'</li>';					
		
			echo '<ul class="frontier-my-approvals-pending">';
		
			foreach ( $fp_wdata['pending_list'] as $q_post)
				{
				$tmp_post = get_post($q_post->post_id);
				
				echo '<li class="frontier-my-approvals-pending">';
				
				if ($instance['show_edit_link'] == 'before')
					echo ' '.frontier_post_edit_link($tmp_post, true, $frontier_page_link, 'fp-my-approvals-post-edit-link' );
	
				if ($instance['show_delete_link'] == 'before')
					echo ' '.frontier_post_delete_link($tmp_post, true, $frontier_page_link, 'fp-my-approvals-post-delete-link' );
				
				
				if ( $fp_wdata['postdateformat'] != 'nodate' )
					{
					echo '<div id="frontier-my-approvals-date-pending">';
					if ($tmp_post->post_status === 'future' )
						{
						echo mysql2date('Y-m-d H:i', $tmp_post->post_date);
						}
					else
						if ( $fp_wdata['postdateformat'] == "human" )
							printf( _x( '%s ago', '%s = human-readable time difference', 'frontier-post' ), human_time_diff( get_the_time( 'U', $tmp_post ), current_time( 'timestamp' ) ) );
						else
							echo mysql2date($fp_wdata['postdateformat'], $tmp_post->post_date);
					
					//echo mysql2date($instance['postdateformat'], $tmp_post->post_date);
					echo '&nbsp;&nbsp;</div>'; 
					}
								
				echo $q_post->post_title;
				
				if ( $fp_wdata['show_author'] )
					echo '<div id="frontier-my-approvals-author">'.'  ('.get_the_author_meta('nicename', $tmp_post->post_author).')</div>'; 
				
				
				if ($instance['show_edit_link'] == 'after')
					echo ' '.frontier_post_edit_link($tmp_post, true, $frontier_page_link, 'fp-my-approvals-post-edit-link' );
	
				if ($instance['show_delete_link'] == 'after')
					echo ' '.frontier_post_delete_link($tmp_post, true, $frontier_page_link, 'fp-my-approvals-post-delete-link' );
				
				echo '</li>';
				}
			echo '</ul>';
			}
	
		
		if ($fp_wdata['show_draft']) 
			{ 
			echo '<li>';
				echo '<a href="'.$tmp_draft_link.'">'.$fp_wdata['draft_txt'].'</a>';
			echo '</li>';
			}
		
		if ( array_key_exists('draft_count', $fp_wdata) && $fp_wdata['draft_count'] > 0)
			{
			if (!$fp_wdata['show_draft'])
				echo '<li>'.__("Draft posts", "frontier-post").'</li>';					
		
			echo '<ul class="frontier-my-approvals-draft">';
		
			foreach ( $fp_wdata['draft_list'] as $q_post)
				{
				$tmp_post = get_post($q_post->post_id);
				echo '<li class="frontier-my-approvals-draft">';
				
				if ($fp_wdata['postdateformat'] != 'nodate')
					echo '<div id="frontier-my-approvals-date-draft">'.mysql2date($instance['postdateformat'], $tmp_post->post_date).'&nbsp;&nbsp;</div>'; 
				
				echo $q_post->post_title;
				
				if ( $fp_wdata['show_author'] )
					echo '<div id="frontier-my-approvals-author">'.'  ('.get_the_author_meta('nicename', $tmp_post->post_author).')</div>'; 
				
				echo ' '.frontier_post_edit_link($tmp_post, true, $frontier_page_link, 'fp-my-approvals-post-edit-link' );
				echo ' '.frontier_post_delete_link($tmp_post, true, $frontier_page_link, 'fp-my-approvals-post-delete-link' );
				
				echo '</li>';
				}
			echo '</ul>';
			}
		
			if ($fp_wdata['show_comments']) 
				{
				echo '<li>';
					echo '<a href="'.site_url('/wp-admin/edit-comments.php?comment_status=moderated').'">'.$fp_wdata['cmt_pending_txt'].'</a>';
				echo '</li>';
				}
		
			if ($fp_wdata['show_comment_spam']) 
				{
				echo '<li>';
					echo '<a href="'.site_url('/wp-admin/edit-comments.php?comment_status=spam').'">'.$fp_wdata['cmt_spam_txt'].'</a>';
				echo '</li>';
			}
		echo '</ul>';
		echo '</div>';
		
		//echo "<pre>".print_r($fp_wdata, true)."</pre><br>";
		
		echo $args['after_widget'];
		}
    }

//**********************************************************************************************
// Update
//**********************************************************************************************

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) 
		{
		$tmp_boolean_fields = array('show_draft', 'show_pending', 'show_comments', 'show_comment_spam', 'show_author');
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
		//error_log("New: ".print_r($new_instance, true)." - old: ".print_r($old_instance, true));
		
		// empty cache
		fp_delete_cache_names(FRONTIER_MY_APPROVALS_W_PREFIX.$this->number);
		
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
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_pending'); ?>"><?php _e('Show number of posts pending approval ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_pending'); ?>" name="<?php echo $this->get_field_name('show_pending'); ?>" value="true" <?php echo ($instance['show_pending'] == true) ? 'checked="checked"':''; ?>/>
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
			<label for="<?php echo $this->get_field_id('pending_count'); ?>"><?php _e('Number of posts pending posts','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('pending_count'); ?>" name="<?php echo $this->get_field_name('pending_count'); ?>" size="3" value="<?php echo esc_attr($instance['pending_count']); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_draft'); ?>"><?php _e('Show number of draft posts ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_draft'); ?>" name="<?php echo $this->get_field_name('show_draft'); ?>" value="true" <?php echo ($instance['show_draft'] == true) ? 'checked="checked"':''; ?>/>
		</p>
		<!--
		<p>
			<label for="<?php echo $this->get_field_id('draft_count'); ?>"><?php _e('Number of posts draft posts','frontier-post'); ?>: </label>
			<input type="text" id="<?php echo $this->get_field_id('draft_count'); ?>" name="<?php echo $this->get_field_name('draft_count'); ?>" size="3" value="<?php echo esc_attr($instance['draft_count']); ?>" />
		</p>
		-->
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
			<label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Show author ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" value="true" <?php echo ($instance['show_author'] == true) ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_comments'); ?>"><?php _e('Show comments pending approval ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_comments'); ?>" name="<?php echo $this->get_field_name('show_comments'); ?>" value="true" <?php echo ($instance['show_comments'] == true) ? 'checked="checked"':''; ?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('show_comment_spam'); ?>"><?php _e('Show comments marked as spam ?', 'frontier-post'); ?>: </label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_comment_spam'); ?>" name="<?php echo $this->get_field_name('show_comment_spam'); ?>" value="true" <?php echo ($instance['show_comment_spam'] == true) ? 'checked="checked"':''; ?>/>
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

//add_action('widgets_init', create_function('', 'return register_widget("frontier_approvals_widget");'));
function frontier_init_approvals_widget()
{
    return register_widget('frontier_approvals_widget');
}
add_action ('widgets_init', 'frontier_init_approvals_widget');



?>