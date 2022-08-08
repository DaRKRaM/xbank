<?php
namespace Frontend_WP\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'Dynamic_Values' ) ) :

class Dynamic_Values{
	function __construct(){
		add_filter( 'template_include', array( $this, 'template_shortcodes' ) );
		add_filter( 'the_content', array( $this, 'run_shortcode' ), 11 );
	}


	public function template_shortcodes( $template ){
			global $_wp_current_template_content;
		if( $_wp_current_template_content ) $_wp_current_template_content = $this->get_dynamic_values( $_wp_current_template_content );
		
		return $template;
	}
	public function run_shortcode( $content ) {
		$content = $this->get_dynamic_values( $content );
		return $content;
	}
	
/* 	function implode_recur($separator, $arrayvar) {
		$return = "";
		foreach ($arrayvar as $av)
		if (is_array ($av)) 
			$return .= $this->implode_recur($separator, $av); // Recursive array 
		else                   
			$return .= $separator.$av;
	
		return $return . '<br>';
	} */

	function get_user_field( $field, $user = null, $context = 'display' ) {
		if( is_object( $user ) ) $user = $user->ID;
		if( is_array( $user ) ) $user = $user['ID'];

		$user_data = get_userdata( $user );
	
		if ( ! $user_data ) {
			return '';
		}
	
		if ( ! isset( $user_data->$field ) ) {
			return '';
		}
	
		return sanitize_user_field( $field, $user_data->$field, $user, $context );
	}

	function get_form_dynamic_values( $form ){
		if( empty( $form['record']['fields'] ) ) return $form;
		foreach( $form['record']['fields'] as $type => $fields ){
			foreach( $fields as $key => $field ){

				if( ! is_string( $field['_input'] ) ) continue;
				
				$input = $this->get_dynamic_values( $field['_input'], $form ); 
				if( ! $input && isset( $field['default_value'] ) && is_string( $field['default_value'] ) ){
					$dynamic_value = $this->get_dynamic_values( $field['default_value'], $form ); 
					if( $dynamic_value ) $input = $dynamic_value;
				}

				$form['record']['fields'][$type][$key]['_input'] = $input;
				
			}
			
		}
			
		return $form;
	}

	function get_current_form(){
		// If no record search for a global form record
		if( isset( $GLOBALS['admin_form'] ) ) return $GLOBALS['admin_form'];
		// If no global record, look for a record stored in the cookie
		if( isset( $form['id'] ) && empty( $form['record'] ) ) return fea_instance()->form_display->get_record( $form ); 

		return false;
	}

	function get_dynamic_values( $text, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();

		// Find all merge tags
		if ( ! preg_match_all( "/\[\s*(.+?)?\s*\]/", $text, $matches ) ) return $text;
		foreach ( $matches[1] as $i=>$tag ){
			$replaced = false;
			$value = false;
			
			if( isset( $form['record'] ) && 'all_fields' == $tag ){				
				$value = $this->get_all_fields_values( $tag, $form );
				$replaced = true;
			}
			if( preg_match_all( '/acf\s*:\s*(.*)/', $tag, $args ) ){
				$value = $this->get_field_value( $args, $form );
				if( ! $value ) $value = $this->get_sub_field_value( $args, $form );
				$replaced = true;
			}

			if( ! $value && preg_match_all( '/post\s*:\s*(.*)/', $tag, $args ) ){
				$value = $this->get_post_value( $args, $form );
				$replaced = true;
			}
			if( ! $value && preg_match_all( '/product\s*:\s*(.*)/', $tag, $args ) ){
				$value = $this->get_product_value( $args, $form );
				$replaced = true;
			}
			if( ! $value && preg_match_all( '/user\s*:\s*(.*)/', $tag, $args ) ){
				$value = $this->get_user_value( $args, $form );
				$replaced = true;
			}
			if( ! $value && preg_match_all( '/term\s*:\s*(.*)/', $tag, $args ) ){
				$value = $this->get_term_value( $args, $form );
				$replaced = true;
			}
			if( $replaced ) $text = str_replace( $matches[0][$i], $value, $text );
		}	
		
		return $text;
	}

	function get_all_fields_values( $tag, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();

		$record = $form['record']['fields'];
		$return = '<table class="acf-display-values">';
		
		if( isset( $tag[2] ) ) $field['return_type'] = $tag[2];

		foreach ( $record as $group => $fields ) {
			if( ! is_array( $fields ) ) continue;	
			foreach( $fields as $field ){

				if ( 'clone' == $field['type'] ) {
					
					foreach ( $field['sub_fields'] as $sub_field ) {					
						$return .= sprintf( '<tr><th>%s</th></tr>', $sub_field['label'] );
						$return .= sprintf( '<tr><td>%s</td></tr>', $this->display_field( $sub_field, $field['value'][ $sub_field['name'] ] ) );					
					}
					
				} else {
					$return .= sprintf( '<tr><th>%s</th></tr>', $field['label'] );
					$return .= sprintf( '<tr><td>%s</td></tr>', 
					$this->display_field( $field, false ) );
				}
			}
		}
		
		$return .= '</table>';
	
		return $return;
	  }
	
	  function get_user_value( $matches, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();
		
		$value = '';

		if( isset( $form['record']['user'] ) ){
			$user_id = $form['record']['user'];
		}else{
			$user_id = get_current_user_id();
		}
		$edit_user = get_user_by( 'ID', $user_id );

		if( empty( $edit_user->user_login ) ) return $value;
		
		if( isset( $form['record']['fields']['user'] ) ){
			$record = $form['record']['fields']['user'];
		}

		$field_name = $matches[1][0];
		switch( $field_name ){
			case 'id':
				$value = $user_id;
			break;
			case 'username':
				if( isset( $record['username']['_input'] ) ){
					return $record['username']['_input'];
				}
				return $edit_user->user_login;
			break;
			case 'email':
				if( isset( $record['user_email']['_input'] ) ){
					return $record['user_email']['_input'];
				}
				return $edit_user->user_email;
			break;
			case 'first_name':
				if( isset( $record[$field_name]['_input'] ) ){
					return $record[$field_name]['_input'];
				}
				return $edit_user->first_name;
			break;
			case 'last_name':
				if( isset( $record[$field_name]['_input'] ) ){
					return $record[$field_name]['_input'];
				}
				return $edit_user->last_name;
			break;
			case 'display_name':
				if( isset( $record[$field_name]['_input'] ) ){
					return $record[$field_name]['_input'];
				}
				return $edit_user->display_name;
			break;
			case 'role':
				if( isset( $record[$field_name]['_input'] ) ){
					$role = $record[$field_name]['_input'];
				}else{	
					$role = $edit_user->roles[0];
				}
				global $wp_roles;
				return $wp_roles->roles[ $role ]['name'];
			break;
			case 'bio':
				if( isset( $record['user_bio']['_input'] ) ){
					return $record['user_bio']['_input'];
				}
				return $edit_user->description;
			break;
			default:
				if( isset( $record ) ){
					$field = $record[$field_name];
				}else{
					$field = $field_name;
				}
				return $this->display_field( $field, false, 'user_' . $user_id );
		}
		return $value;
	}
	function get_post_value( $matches, $form = false ){
		if( ! $form ) $form = $this->get_current_form();

		$value = '';
		if( isset( $form['record']['post'] ) ){
			$post_id = $form['record']['post'];
			$edit_post = get_post( $post_id );
		}
		if( empty( $edit_post->ID ) ){
			if( isset( $form['record']['fields']['post'] ) ){
				$record = $form['record']['fields']['post'];
			}else{
				global $post;
				if( empty( $post->ID ) ) return $value;

				$post_id = $post->ID;
				$edit_post = $post;
			}
		}
		
		$field_name = $matches[1][0];

			$tag_parts = explode( ':', $field_name );
			$return_type = false;
			if( isset( $tag_parts[1] ) ){
				$field_name = str_replace( ' ', '', $tag_parts[0] );
				$return_type = str_replace( ' ', '', $tag_parts[1] );
			}
			switch( $field_name ){
				case 'id':
					return $post_id;
				break;
				case 'post_title':
				case 'title':
					if( isset( $record['post_title']['_input'] ) ){
						return $record['post_title']['_input'];
					}
					return $edit_post->post_title;
				break; 
				case 'slug':
					if( isset( $record['post_slug']['_input'] ) ){
						return $record['post_slug']['_input'];
					}
					return $edit_post->post_name;
				break;
				case 'post_content':
				case 'content':
				case 'desc':
					if( isset( $record['post_content']['value'] ) ){
						return $record['post_content']['value'];
					}
					return $edit_post->post_content;
				break;
				case 'post_excerpt':
				case 'excerpt':
				case 'short_desc':
					if( isset( $record['post_excerpt']['value'] ) ){
						return $record['post_excerpt']['value'];
					}
					return $edit_post->post_excerpt;
				break;
				case 'featured_image':
				case 'main_image':
					if( isset( $record['featured_image']['value'] ) ){
						$post_thumb_id = $record['featured_image']['value']['ID'];
						$post_thumb_url = $record['featured_image']['value']['url'];
					}else{	
						$post_thumb_id = get_post_thumbnail_id( $post_id );
						$post_thumb_url = wp_get_attachment_url( $post_thumb_id );
					}
					$max_width = '500px';
					if( $return_type ){
						if( $return_type == 'image_link' ){
							return $post_thumb_id;
						}elseif( $return_type == 'image_id' ){
							return $post_thumb_url;
						}else{
							$max_width = $return_type;
							if( is_numeric( $max_width ) ) $max_width .= 'px';							
						}
					}
					if( ! $value ){
						return '<div style="max-width:' .$max_width. '"><a href="' .$post_thumb_url. '"><img style=" width: 100%;height: auto" src="' . $post_thumb_url . '"/></a></div>';
					}
				break;
				case 'post_url':
				case 'url':
						return get_permalink( $post_id );
				break;
				case 'author':
					if( isset( $record['post_author']['_input'] ) ){
						$author = $record['post_author']['_input'];
					}else{
						$author = $edit_post->post_author;
					}
					if( $author ){
						$author = get_user_by( 'id', $author );
						if( isset( $author->first_name ) ){
							return sprintf( '%s %s', $author->first_name, $author->last_name );			
						}
					}

				break;
				default:
					if( isset( $record ) ){
						$field = $record[$field_name];
					}else{
						$field = $field_name;
					}

					return $this->display_field( $field, false, $post_id );
			}
			return $value;
	
	}
	function get_product_value( $matches, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();

		$value = '';
		if( isset( $form['record']['product'] ) ){
			$post_id = $form['record']['product'];
			$edit_post = get_post( $post_id );
		}
		if( empty( $edit_post->ID ) ){
			if( isset( $form['record']['fields']['product'] ) ){
				$record = $form['record']['fields']['product'];
			}else{
				global $post;
				if( empty( $post->ID ) || $post->post_type != 'product' ) return $value;

				$post_id = $post->ID;
				$edit_post = $post;
			}
		}
		

			$field_name = $matches[1][0];

			$tag_parts = explode( ':', $field_name );
			$return_type = false;
			if( isset( $tag_parts[1] ) ){
				$field_name = str_replace( ' ', '', $tag_parts[0] );
				$return_type = str_replace( ' ', '', $tag_parts[1] );
			}
			switch( $field_name ){
				case 'id':
					return $post_id;
				break;
				case 'title':
					if( isset( $record['product_title']['_input'] ) ){
						return $record['product_title']['_input'];
					}
					return $edit_post->post_title;
				break;
				case 'slug':
					if( isset( $record['product_slug']['_input'] ) ){
						return $record['product_slug']['_input'];
					}
					return $edit_post->post_name;
				break;
				case 'desc':
					if( isset( $record['product_description']['value'] ) ){
						return $record['product_description']['value'];
					}
					return $edit_post->post_content;
				break;
				case 'short_desc':
					if( isset( $record['product_short_description']['value'] ) ){
						return $record['product_short_description']['value'];
					}
					return $edit_post->post_excerpt;
				break;
				case 'main_image':
					if( isset( $record['main_image']['value'] ) ){
						$post_thumb_id = $record['main_image']['value']['ID'];
						$post_thumb_url = $record['main_image']['value']['url'];
					}else{	
						$post_thumb_id = get_post_thumbnail_id( $post_id );
						$post_thumb_url = wp_get_attachment_url( $post_thumb_id );
					}
					$max_width = '500px';
					if( $return_type ){
						if( $return_type == 'image_link' ){
							return $post_thumb_id;
						}elseif( $return_type == 'image_id' ){
							return $post_thumb_url;
						}else{
							$max_width = $return_type;
							if( is_numeric( $max_width ) ) $max_width .= 'px';							
						}
					}
					if( ! $value ){
						return '<div style="max-width:' .$max_width. '"><a href="' .$post_thumb_url. '"><img style=" width: 100%;height: auto" src="' . $post_thumb_url . '"/></a></div>';
					}
				break;
				case 'url':
						return get_permalink( $post_id );
				break;
				case 'author':
					if( isset( $record['product_author']['_input'] ) ){
						$author = $record['product_author']['_input'];
					}else{
						$author = $edit_post->post_author;
					}
					if( $author ){
						$author = get_user_by( 'id', $author );
						if( isset( $author->first_name ) ){
							return sprintf( '%s %s', $author->first_name, $author->last_name );			
						}
					}

				break;
				default:
					if( isset( $record ) ){
						$field = $record[$field_name];
					}else{
						$field = $field_name;
					}

					return $this->display_field( $field, false, $post_id );
			}
			return $value;
	
	}
	function get_term_value( $matches, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();

		$value = '';
		if( isset( $form['record']['term'] ) ){
			$term_id = $form['record']['term'];
			$edit_term = get_term( $term_id );
		}else{
			if( empty( $edit_term->term_id ) ){
				if( isset( $form['record']['fields']['term'] ) ){
					$record = $form['record']['fields']['term'];
				}else{
					return false;
				}
			}
		}


		$field_name = $matches[1][0];

			$tag_parts = explode( ':', $field_name );
			$return_type = false;
			if( isset( $tag_parts[1] ) ){
				$field_name = str_replace( ' ', '', $tag_parts[0] );
				$return_type = str_replace( ' ', '', $tag_parts[1] );
			}

			switch( $field_name ){
				case 'id':
					return $term_id;
				break;
				case 'name':
					if( isset( $record['term_name']['_input'] ) ){
						$author = $record['term_name']['_input'];
					}
					return $edit_term->name;
				break;
				case 'slug':
					if( isset( $record['term_slug']['_input'] ) ){
						$author = $record['term_slug']['_input'];
					}
					return $edit_term->slug;
				break;
				case 'desc':
					if( isset( $record['term_description']['_input'] ) ){
						$author = $record['term_description']['_input'];
					}
					return $edit_term->description;
				break;
				default:
					if( isset( $record ) ){
						$field = $record[$field_name];
					}else{
						$field = $field_name;
					}
					return $this->display_field( $field, false, 'term_' . $term_id );
			}
			return $value;
		
	}
	
	  function get_sub_field_value( $matches, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();
		
		$post_id = isset( $form['record']['post_id'] ) ? $form['record']['post_id'] : false;
		$record = array();
		if( ! empty( $form['record']['fields'] ) ){
			foreach( $form['record']['fields'] as $type ){
				if(  is_array( $type ) ){
					$record = array_merge( $record, $type );
				}
			}
		}
		$field_name = $matches[1][0];
		if( isset( $record[$field_name] ) ){
			$field = $record[$field_name];
		}else{
			$field = get_field_object( $field_name, $post_id );	
		}
		if ( ! $field ) return '';
	
		$selector = explode( '][', $matches[2][0] );
	
		if( isset( $selector[1] ) ){
			$sub_field = $this->sub_field( $field, $selector );
			$sub_field_value = $this->sub_field_value( $field, $selector );
	
			if ( $sub_field ) {
				$return_type = false;
				if( isset( $tag[2] ) ) $return_type = $tag[2];
			
				return $this->display_field( $sub_field, $sub_field_value );
			}
		}
	
		return '';
	  }
	
	  function get_field_value( $matches, $form = false ) {
		if( ! $form ) $form = $this->get_current_form();

		$post_id = isset( $form['record']['post_id'] ) ? $form['record']['post_id'] : false;
		$record = array();
		if( ! empty( $form['record']['fields'] ) ){
			foreach( $form['record']['fields'] as $type ){
				if(  is_array( $type ) ){
					$record = array_merge( $record, $type );
				}
			}
		}

		$field_name = $matches[1][0];
		
		if( isset( $record[$field_name] ) ){
			$field = $record[$field_name];
		}else{
			$field = get_field_object( $field_name, $post_id );	
		}
		if ( ! $field ) return '';

		if( isset( $tag[2] ) ) $field['return_type'] = $tag[2];
		
		return $this->display_field( $field, false );
	  }

	  function sub_field( $field, $selector ) {
		  
		while ( ! empty( $selector ) && $field && isset( $field['sub_fields'] ) ) {
			$search = array_shift( $selector );
			$field = acf_search_fields( $search, $field['sub_fields'] );
		}

		return $field;
	}

   function sub_field_value( $field, $selector ) {
		$value = $field['value'];

		while ( ! empty( $selector ) ) {
			$search = array_shift( $selector );
			if ( isset( $value[ $search ] ) ) {
				return $value[ $search ];
			} else {
				return false;
			}
		}

		return $value;
	}
	
	function display_field( $field, $value = false, $object_id = false, $form = false ) {

		if( is_string( $field ) ){
			$field = get_field_object( $field, $object_id );
			if( ! $field ){
				$object = explode( '_', $object_id );
				if( isset( $object[1] ) ){
					$object_type = $object[0];
				}else{
					$object_type = 'post';
				}
				if( ! empty( $form['record']['fields'][$object_type][$field] ) ){
					$field = $form['record']['fields'][$object_type][$field];	
				}
				
				if( ! $field ) return false;
			}
		}
		if ( ! $value ) {
			$value = $field['value'];
		}
		if ( ! $value ) return '';
	
		$return = '';

		switch( $field['type'] ){
			case 'repeater':
				if( is_array( $value ) ){
					$return .= '<table class="acf-display-values acf-display-values-repeater">';
			
					// Column headings
					$return .= '<thead><tr>';
					
					foreach ( $field['sub_fields'] as $sub_field ) {
						$return .= sprintf( '<th>%s</th>', $sub_field['label'] );
					}
					
					$return .= '</tr></thead>';
					
					
					// Rows
					$return .= '<tbody>';
					
					if ( is_array( $value ) ) {
						foreach ( $value as $row_values ) {
							$return .= '<tr>';
							
							foreach ( $field['sub_fields'] as $sub_field ) {
								$row_value = false;
								if( isset( $row_values[ $sub_field['name'] ] ) ){
									$row_value = $row_values[ $sub_field['name'] ];
								}
								if( isset( $row_values[ $sub_field['key'] ] ) ){
									$row_value = $row_values[ $sub_field['key'] ];
								}
								
								$return .= sprintf( '<td>%s</td>', $this->display_field( $sub_field, $row_value ) );
								
							}
							
							$return .= '</tr>';
						}
					}
					
					$return .= '</tbody>';
					
					
					$return .= '</table>';
				}
			break;
			case 'clone':
			case 'group':
				$return .= sprintf( '<table class="acf-display-values acf-display-values-%s">', $field['type'] );
		
				foreach ( $field['sub_fields'] as $sub_field ) {
					if ( isset( $value[ $sub_field['name'] ] ) ) {
						$return .= sprintf( '<tr><th>%s</th></tr>', $sub_field['label'] );
						$return .= sprintf( '<tr><td>%s</td></tr>', $this->display_field( $sub_field, $value[ $sub_field['name'] ] ) );
					}
				}
				
				$return .= '</table>';
			break;
			case 'true_false':
			case 'mailchimp_status':
					$true_text = isset( $field['ui_on_text'] ) && ! empty( $field['ui_on_text'] ) ? $field['ui_on_text'] : __( 'Yes', 'advanced-forms' );
				$false_text = isset( $field['ui_off_text'] ) && ! empty( $field['ui_off_text'] ) ? $field['ui_off_text'] : __( 'No', 'advanced-forms' );
				
				$return .= $value ? $true_text : $false_text;
			break;
			case 'image':
			case 'featured_image':
			case 'main_image':
			case 'site_logo':
				if( is_array( $value ) ){
					$attachment = $value;
				}else{
					$attachment = acf_get_attachment( $value );
				}			
				$max_width = '';
				if( isset( $field['return_type'] ) ){
					$return_type = $field['return_type'];
					if( $return_type == 'image_link' || $return_type == 'link' ){
						$return .= $attachment['url'];
					}elseif( $return_type == 'image_id' || $return_type == 'id' ){
						$return .= $attachment['ID'];
					}else{
						$max_width = 'style="max-width:' .$return_type. '"';
					}
				}
				$return .= sprintf( '<img '. $max_width .'src="%s" alt="%s" />', esc_attr( $attachment['sizes']['medium'] ), esc_attr( $attachment['alt'] ) );
			break;
			case 'gallery':
			case 'product_images':
				foreach ( $value as $image ) {
					$return .= sprintf( '<img src="%s" alt="%s" />', esc_attr( $image['sizes']['medium'] ), esc_attr( $image['alt']));
				}
			break;
			case 'file':
				$return .= sprintf( '<a href="%s">%s</a>', $value['url'], htmlspecialchars( $value['title'] ) );
			break;
			case 'wysiwyg':
			case 'textarea':
			case 'post_excerpt':
			case 'post_content':
			case 'user_bio':
				$return .= wp_kses_post( stripslashes( $value ) );
			break;
			case 'taxonomy':
				if( $value ){
					$returns = array();
					foreach( $value as $single_value ){
						if( $field['return_format'] == 'id' ) $single_value = get_term( $single_value );
						$returns[] = $single_value->name;
					}
					$return .= join( ', ', $returns );
				}
			break;
			case 'relationship':
			case 'product_grouped':
			case 'product_upsells':
			case 'product_cross_sells':
			case 'post_object':
				if( is_array( $value ) && $value ){
					$returns = array();
					foreach( $value as $single_value ){
						if( $field['return_format'] == 'id' ) $single_value = get_post( $single_value );
						$returns[] = $single_value->post_title ? $single_value->post_title : '(no-name)';
					}
					$return .= join( ', ', $returns );
				}elseif( $value ){
					if( $field['return_format'] == 'id' ) $value = get_post( $value );
					$return .= $value->post_title ? $value->post_title : '(no-name)';
				}
			break;
			case 'user':
			case 'post_author':
				if( is_array( $value ) && $value ){
					$returns = array();
					if( $field['return_format'] == 'array' ){
						foreach( $value as $single_value ){
							$returns[] = sprintf( '%s %s', $value['user_firstname'], $value['user_lastname'] );
						}
					}else{
						foreach( $value as $single_value ){
							if( $field['return_format'] == 'id' ) $single_value = get_userdata( $single_value );
							$returns[] = sprintf( '%s %s', $value->first_name, $value->last_name );
						}
					}
					$return .= join( ', ', $returns );
				}elseif( $value ){
					if( $field['return_format'] == 'array' ){
						$return .= sprintf( '%s %s', $value['user_firstname'], $value['user_lastname'] );
					}else{
						if( $field['return_format'] == 'id' ) $value = get_userdata( $value );
						$return .= sprintf( '%s %s', $value->first_name, $value->last_name );
					}
				}
			break;
			default:
				$return .= $this->display_default_value( $value ); 
		}
		
		// Allow third-parties to alter rendered field
		$return = apply_filters( FEA_PREFIX.'/display_value', $return, $field, $value );
		$return = apply_filters( FEA_PREFIX.'/display_value/name=' . $field['name'], $return, $field, $value );
		$return = apply_filters( FEA_PREFIX.'/display_value/key=' . $field['key'], $return, $field, $value );
		
		return $return;
	}
	
	
	function display_default_value( $value ) {
		$return = '';
		 
		if ( $value instanceof WP_Post ) {
			
			$return = $value->post_title ? $value->post_title : '(no-name)';
			
		} elseif ( $value instanceof WP_User ) {
			
			$return = sprintf( '%s %s', $value->first_name, $value->last_name );
		
		} elseif ( is_array( $value ) && isset( $value['user_email'] ) ) {
			
			$return = sprintf( '%s %s', $value['user_firstname'], $value['user_lastname'] );
			
		} elseif ( $value instanceof WP_Term ) {
			
			$return = $value->name;
			
		} elseif ( is_array( $value ) ) {
			
			$returns = array();
			
			foreach ( $value as $single_value ) {
				
				$returns[] = $this->display_default_value( $single_value );
				
			}
			
			$return = join( ', ', $returns );
			
		} else {
			
			$return = (string)$value;
			
		}
	
		// Sanitize output to protect against XSS
		return htmlspecialchars( $return );
	}
	

}

fea_instance()->dynamic_values = new Dynamic_Values();

endif;	