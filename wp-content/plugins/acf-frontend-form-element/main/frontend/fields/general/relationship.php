<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('acf_frontend_relationship_field') ) :

	class acf_frontend_relationship_field {

        public function add_edit_field( $field ) {
			$users = get_users();
			$label = __( 'Dynamic', FEA_NS );
			$user_choices = [ $label => ['current_user' => __( 'Current User', FEA_NS ) ] ];
			// Append.
			if( $users ) {
				$user_label = __( 'Users', FEA_NS );
				$user_choices[ $user_label ] = [];
				foreach( $users as $user ) {
					$user_text = $user->user_login;	
					// Add name.
					if( $user->first_name && $user->last_name ) {
						$user_text .= " ({$user->first_name} {$user->last_name})";
					} elseif( $user->first_name ) {
						$user_text .= " ({$user->first_name})";
					}
					$user_choices[ $user_label ][ $user->ID ] = $user_text;
				}
			}		
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Filter by Post Author',FEA_NS ),
				'instructions'	=> '',
				'type'			=> 'select',
				'name'			=> 'post_author',
				'choices'		=> $user_choices,
				'multiple'		=> 1,
				'ui'			=> 1,
				'allow_null'	=> 1,
				'placeholder'   => __( "All Users",FEA_NS ),
			));
			
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Add and Edit Posts' ),
				'instructions'	=> __( 'Allow posts to be created and edited whilst editing',FEA_NS ),
				'name'			=> 'add_edit_post',
				'type'			=> 'true_false',
				'ui'			=> 1,
			) );
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Add Post Button' ),
				'name'			=> 'add_post_button',
				'type'			=> 'text',
				'default_value' => __( 'Add Post' ),
				'placeholder'   => __( 'Add Post' ),
				'conditions'	=> [
					[
						'field'		=> 'add_edit_post',
						'operator'	=> '==',
						'value'		=> '1'
					]
				]
			) );
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Form Container Width' ),
				'name'			=> 'form_width',
				'type'			=> 'number',
				'prepend'		=> 'px',
				'default_value' => 600,
				'placeholder'   => 600,
				'conditions'	=> [
					[
						'field'		=> 'add_edit_post',
						'operator'	=> '==',
						'value'		=> '1'
					]
				]
			) );
	
			$templates_options = ['none' => __( 'Default', FEA_NS ), 'current' => __( 'Current Form/Field Group', FEA_NS ) ];

			acf_render_field_setting( $field, array(
				'label'			=> __('Forms/Field Groups'),
				'name'			=> 'post_form_template',
				'instructions'  => '<div>' . __( 'Select an existing field group or form or the current field group or form', FEA_NS ) . '</div>',
				'type'			=> 'select',
				'ajax' 		   => 1,
				'multiple' 		   => 1,
				'ajax_action'  => 'acf/fields/form_fields/query',
				'choices'      => feadmin_get_selected_fields( $field['post_form_template'] ),
				'ui'			=> 1,
				'conditions'	=> [
					[
						'field'		=> 'add_edit_post',
						'operator'	=> '==',
						'value'		=> '1'
					]
				]
			) ); 
		
		}
		/*
		*  get_selected_fields
		*
		*  This function will return an array of choices data for Select2
		*
		*  @type    function
		*  @date    17/06/2016
		*  @since   5.3.8
		*
		*  @param   $value (mixed)
		*  @return  (array)
		*/

		function get_selected_fields( $value ) {

			// vars
			$choices = array();

			// bail early if no $value
			if ( empty( $value ) ) {
				return $choices;
			}

			// force value to array
			$value = acf_get_array( $value );

			// loop
			foreach ( $value as $v ) {

				$choices[ $v ] = feadmin_get_selected_field( $v );

			}

			// return
			return $choices;

		}
		
		/*
		*  get_selected_field
		*
		*  This function will return the label for a given clone choice
		*
		*  @type    function
		*  @date    17/06/2016
		*  @since   5.3.8
		*
		*  @param   $selector (mixed)
		*  @return  (string)
		*/

		function get_selected_field( $selector = '' ) {

			// bail early no selector
			if ( ! $selector ) {
				return '';
			}

			// ajax_fields
			if ( isset( $_POST['fields'][ $selector ] ) ) {

				return $this->get_clone_setting_field_choice( $_POST['fields'][ $selector ] );

			}

			// field
			if ( acf_is_field_key( $selector ) ) {

				return $this->get_clone_setting_field_choice( acf_get_field( $selector ) );

			}

			// group
			if ( acf_is_field_group_key( $selector ) ) {

				return $this->get_clone_setting_group_choice( acf_get_field_group( $selector ) );

			}

			// return
			return $selector;

		}


		public function load_relationship_field( $field ) {
			if( ! isset( $field['add_edit_post'] ) ) return $field;

			if( isset( $field['form_width'] ) ){
				$field['wrapper']['data-form_width'] = $field['form_width'];
			}

			return $field;
		}

        public function edit_post_button( $title, $post, $field, $post_id ){
			if( ! empty( $field['add_edit_post'] ) ) : 
				$title .= '<a href="#" class="acf-icon -pencil small dark edit-rel-post render-form" data-name="edit_item"></a>';
			endif;
			return $title;
		}

		public function add_post_button( $field ){

			if( ! empty( $field['add_edit_post'] ) ) : 
			$add_post_button = ( $field['add_post_button'] ) ? $field['add_post_button'] : __( 'Add Post', FEA_NS );
			?>
				<div class="margin-top-10 acf-actions">
					<a class="add-rel-post acf-button button button-primary render-form" href="#" data-name="add_item"><?php echo $add_post_button ?></a>
				</div>
				
			<?php endif;
		}

        public function relationship_query( $args, $field, $post_id ){
			if( ! isset( $field['post_author'] ) ) return $args;

			$post_author = acf_get_array( $field['post_author'] );

			if( in_array( 'current_user', $post_author ) ){
				$key = array_search( 'current_user', $post_author );
				$post_author[ $key ] = get_current_user_id();
			}

			$args['author__in'] = $post_author;

			return $args;
		}

			/*
		*  ajax_query
		*
		*  description
		*
		*  @type    function
		*  @date    17/06/2016
		*  @since   5.3.8
		*
		*  @param   $post_id (int)
		*  @return  $post_id (int)
		*/

		function ajax_query() {
			// validate
			if ( ! acf_verify_ajax() ) {
				die();
			}

			// disable field to allow clone fields to appear selectable
			acf_disable_filter( 'fields_select' );

			// options
			$options = acf_parse_args(
				$_POST,
				array(
					'post_id' => 0,
					'paged'   => 0,
					's'       => '',
					'title'   => '',
					'fields'  => array(),
				)
			);
			// vars
			$results     = array();
			if( $options['paged'] == 1 && ! $options['s'] ){
				$results = [['id' => 'none', 'text' => __( 'Default', FEA_NS )],['id' => 'current', 'text' => __( 'Current Form/Field Group', FEA_NS )]];
			}
			$s           = false;
			$i           = -1;
			$limit       = 20;
			$range_start = $limit * ( $options['paged'] - 1 );  // 0,  20, 40
			$range_end   = $range_start + ( $limit - 1 );         // 19, 39, 59

			// search
			if ( $options['s'] !== '' ) {

				// strip slashes (search may be integer)
				$s = wp_unslash( strval( $options['s'] ) );

			}

			// load groups
			$field_groups = acf_get_field_groups();
			$field_group  = false;

			// bail early if no field groups
			if ( empty( $field_groups ) ) {
				die();
			}

			// move current field group to start
			foreach ( array_keys( $field_groups ) as $j ) {

				// check ID
				if ( $field_groups[ $j ]['ID'] !== $options['post_id'] ) {
					continue;
				}

				// extract field group and move to start
				$field_group = acf_extract_var( $field_groups, $j );

				// field group found, stop looking
				break;

			}

			// if field group was not found, this is a new field group (not yet saved)
			if ( ! $field_group ) {

				$field_group = array(
					'ID'    => $options['post_id'],
					'title' => $options['title'],
					'key'   => '',
				);

			}

			// move current field group to start of list
			array_unshift( $field_groups, $field_group );

			// loop
			foreach ( $field_groups as $field_group ) {

				// vars
				$fields   = false;
				$ignore_s = false;
				$data     = array(
					'text'     => $field_group['title'],
					'children' => array(),
				);

				// get fields
				if ( $field_group['ID'] == $options['post_id'] ) {

					$fields = $options['fields'];

				} else {

					$fields = acf_get_fields( $field_group );
					$fields = acf_prepare_fields_for_import( $fields );

				}

				// bail early if no fields
				if ( ! $fields ) {
					continue;
				}

				// show all children for field group search match
				if ( $s !== false && stripos( $data['text'], $s ) !== false ) {

					$ignore_s = true;

				}

				// populate children
				$children   = array();
				$children[] = $field_group['key'];
				foreach ( $fields as $field ) {
					$children[] = $field['key']; }

				// loop
				foreach ( $children as $child ) {

					// bail ealry if no key (fake field group or corrupt field)
					if ( ! $child ) {
						continue;
					}

					// vars
					$text = false;

					// bail early if is search, and $text does not contain $s
					if ( $s !== false && ! $ignore_s ) {

						// get early
						$text = feadmin_get_selected_field( $child );

						// search
						if ( stripos( $text, $s ) === false ) {
							continue;
						}
					}

					// $i
					$i++;

					// bail early if $i is out of bounds
					if ( $i < $range_start || $i > $range_end ) {
						continue;
					}

					// load text
					if ( $text === false ) {
						$text = feadmin_get_selected_field( $child );
					}

					// append
					$data['children'][] = array(
						'id'   => $child,
						'text' => $text,
					);

				}

				// bail early if no children
				// - this group contained fields, but none shown on this page
				if ( empty( $data['children'] ) ) {
					continue;
				}

				// append
				$results[] = $data;

				// end loop if $i is out of bounds
				// - no need to look further
				if ( $i > $range_end ) {
					break;
				}
			}
			// return
			acf_send_ajax_results(
				array(
					'results' => $results,
					'limit'   => $limit,
				)
			);

		}

        
		public function __construct() {
			$fields = array( 'relationship' );	
			add_action( 'wp_ajax_acf/fields/form_fields/query', array( $this, 'ajax_query' ) );	
			
			foreach( $fields as $field ){
				add_filter( "acf/load_field/type=$field",  [ $this, 'load_relationship_field'] );
				add_action( "acf/render_field_settings/type=$field",  [ $this, 'add_edit_field'] );
				add_action( "acf/render_field/type=$field",  [ $this, 'add_post_button'], 10); 
				add_filter( "acf/fields/$field/result", [ $this, 'edit_post_button'], 10, 4 );	
				add_filter( "acf/fields/$field/query", [ $this, 'relationship_query'], 10, 3 );	
			}
		}
	}

	new acf_frontend_relationship_field();

endif;

