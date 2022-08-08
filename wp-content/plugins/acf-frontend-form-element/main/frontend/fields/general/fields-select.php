<?php

if ( ! class_exists( 'acf_field_fields_select' ) ) :

	class acf_field_fields_select extends acf_field {


		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   n/a
		*  @return  n/a
		*/

		function initialize() {

			// vars
			$this->name      = 'fields_select';
			$this->label     = __( 'ACF Fields', FEA_NS );
			$this->category  = __( 'Form', FEA_NS );
			$this->defaults  = array(
				'fields_select'        => '',
			);
			$this->cloning = array();

			acf_enable_filter( 'fields_select' );

			// filters
            add_action( 'wp_ajax_acf/fields/fields_select/query', array( $this, 'ajax_query' ) );
			

		}

		function load_field( $field ) {
			global $post, $form_preview;
			if( isset( $post->post_type ) && $post->post_type == 'admin_form' && empty( $form_preview ) ){
				return $field;
			}
			$field['no_wrap'] = 1;
			// load sub fields
			// - sub field name's will be modified to include prefix_name settings
			$field['name'] = $field['key'];
			$field['fields_select'] = $this->get_cloned_fields( $field );

			// return
			return $field;

		}
		function get_cloned_fields( $field ) {

			// vars
			$fields = array();

			// bail early if no clone setting
			if ( empty( $field['fields_select'] ) ) {
				return $fields;
			}

			// bail ealry if already cloning this field (avoid infinite looping)
			if ( isset( $this->cloning[ $field['key'] ] ) ) {
				return $fields;
			}

			// update local ref
			$this->cloning[ $field['key'] ] = 1;

			// Loop over selectors and load fields.
			foreach ( $field['fields_select'] as $selector ) {
				// Field Group selector.
				if ( acf_is_field_group_key( $selector ) ) {

					$field_group = acf_get_field_group( $selector );
					if ( ! $field_group ) {
						continue;
					}

					$field_group_fields = acf_get_fields( $field_group );
					if ( ! $field_group_fields ) {
						continue;
					}

					$fields = array_merge( $fields, $field_group_fields );

					// Field selector.
				} elseif ( acf_is_field_key( $selector ) ) {
					$fields[] = acf_get_field( $selector );
				}
			}

			// field has ve been loaded for this $parent, time to remove cloning ref
			unset( $this->cloning[ $field['key'] ] );

			// clear false values (fields that don't exist)
			$fields = array_filter( $fields );

			// bail early if no sub fields
			if ( empty( $fields ) ) {
				return array();
			}

			// loop
			// run acf_clone_field() on each cloned field to modify name, key, etc
			foreach ( array_keys( $fields ) as $i ) {

				$fields[ $i ] = acf_clone_field( $fields[ $i ], $field );

			}

			// return
			return $fields;

		}

		function render_field( $field ) {

			// bail early if no sub fields
			if ( empty( $field['fields_select'] ) ) {
				return;
			}

			acf_hidden_input( array( 'name' => $field['name'] ) );

			// load values
			foreach ( $field['fields_select'] as $index => $sub_field ) {
				// add value
				if ( ! empty( $field['value'] ) && isset( $field['value'][ $sub_field['key'] ] ) ) {
					// this is a normal value
					$sub_field['value'] = $field['value'][ $sub_field['key'] ];

				} elseif ( isset( $sub_field['default_value'] ) ) {

					// no value, but this sub field has a default value
					$sub_field['value'] = $sub_field['default_value'];

				}
				$sub_field['prefix'] = $field['prefix'];
				if( isset( $field['wrapper']['class'] ) ){
					$sub_field['wrapper']['class'] .= ' ' . $field['wrapper']['class'];
				}
				if( isset( $field['wrapper']['data-step'] ) ){
					$sub_field['wrapper']['data-step'] = $field['wrapper']['data-step'];
				}
				fea_instance()->form_display->render_field_wrap( $sub_field );
			}


		}

			/*
		*  prepare_field_for_db
		*
		*  description
		*
		*  @type    function
		*  @date    4/11/16
		*  @since   5.5.0
		*
		*  @param   $post_id (int)
		*  @return  $post_id (int)
		*/

		function prepare_field_for_db( $field ) {

			// bail early if no sub fields
			if ( empty( $field['fields_select'] ) ) {
				return $field;
			}

			// bail early if name == _name
			// this is a parent clone field and does not require any modification to sub field names
			/* if ( $field['name'] == $field['_name'] ) {
				echo 'hi';

				return $field;
			} */

			// this is a sub field
			// _name = 'my_field'
			// name = 'rep_0_my_field'
			// modify all sub fields to add 'rep_0_' name prefix (prefix_name setting has already been applied)
			$length = strlen( $field['_name'] );
			$prefix = substr( $field['name'], 0, -$length );

			// bail ealry if _name is not found at the end of name (unknown potential error)
			if ( $prefix . $field['_name'] !== $field['name'] ) {
				return $field;
			}

			// acf_log('== prepare_field_for_db ==');
			// acf_log('- clone name:', $field['name']);
			// acf_log('- clone _name:', $field['_name']);

			// loop

			foreach ( $field['fields_select'] as $index => $sub_field_key ) {
				$sub_field = acf_maybe_get_field( $sub_field_key );
				if( ! $sub_field ) continue;

				$sub_field['name'] = $prefix . $sub_field['name'];
				$field['fields_select'][$index] = $sub_field;
			}

			// return
			return $field;

		}

		/*
		*  load_value()
		*
		*  This filter is applied to the $value after it is loaded from the db
		*
		*  @type    filter
		*  @since   3.6
		*  @date    23/01/13
		*
		*  @param   $value (mixed) the value found in the database
		*  @param   $post_id (mixed) the $post_id from which the value was loaded
		*  @param   $field (array) the field array holding all the field options
		*  @return  $value
		*/

		function load_value( $value, $post_id, $field ) {

			global $post, $form_preview;
			if( isset( $post->post_type ) && $post->post_type == 'admin_form' && empty( $form_preview ) ){
				return $value;
			}

			// bail early if no sub fields
			if ( empty( $field['fields_select'] ) ) {
				return $value;
			}

			// load sub fields
			$value = array();

			// loop
			foreach ( $field['fields_select'] as $index => $sub_field ) {		
				// add value
				$value[ $sub_field['key'] ] = acf_get_value( $post_id, $sub_field );

			}

			// return
			return $value;

		}

	
		/*
		*  render_field_settings()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
		*
		*  @param   $field  - an array holding all the field's data
		*
		*  @type    action
		*  @since   3.6
		*  @date    23/01/13
		*/

		function render_field_settings( $field ) {

			// temp enable 'local' to allow .json fields to be displayed
			acf_enable_filter( 'local' );

			// default_value
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Fields or Field Groups', 'acf' ),
					'instructions' => __( 'Select one or more fields or field groups', FEA_NS ),
					'type'         => 'select',
					'name'         => 'fields_select',
					'multiple'     => 1,
					'allow_null'   => 1,
					'choices'      => feadmin_get_selected_fields( $field['fields_select'] ),
					'ui'           => 1,
                    'ajax'         => 1,
					'ajax_action'  => 'acf/fields/fields_select/query',
					'placeholder'  => '',
				)
			);

			acf_disable_filter( 'local' );

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
			$GLOBALS['only_acf_field_groups'] = 1;
			$field_groups = acf_get_field_groups();
			$GLOBALS['only_acf_field_groups'] = 0;
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

		public function get_selected_fields( $fields_select, $fields = array() ){
			if( $fields ) $fields[] = $fields_select;
			foreach( $fields_select['fields_select'] as $sub_field ){
				if( strpos( $sub_field, 'field_' ) !== false ){
					$fields = fea_instance()->form_display->get_field_to_display( $sub_field, $fields, $fields_select );
				}else{
					$selected_fields = feadmin_get_acf_field_choices( array( 'groups' => array( $sub_field ) ), 'key' );
					foreach( $selected_fields as $sub_field ){
						$fields = fea_instance()->form_display->get_field_to_display( $sub_field, $fields, $fields_select );
					}
				} 
			}
			return $fields;
		}

		/*
		*  validate_value
		*
		*  description
		*
		*  @type    function
		*  @date    11/02/2014
		*  @since   5.0.0
		*
		*  @param   $post_id (int)
		*  @return  $post_id (int)
		*/

		function validate_value( $valid, $value, $field, $input ) {

			// bail early if no sub fields
			if ( empty( $field['fields_select'] ) ) {
				return $valid;
			}

			$group = explode( 'acff[', $input );

			if( isset( $group[1] ) ){
				$group = explode( ']', $group[1] )[0];
			}else{
				return $valid;
			}
			// loop
			foreach( $field['fields_select'] as $sub_field ) {
				$k         = $sub_field['key'];
				$sub_input = str_replace( $field['key'], $k, $input );
				if ( ! isset( $_POST['acff'][$group][$k] ) ) {
					continue;
				}

				// validate
				acf_validate_value( $_POST['acff'][$group][$k], $sub_field, $sub_input );

			}

			// return
			return $valid;

		}

	}


	// initialize
	acf_register_field_type( 'acf_field_fields_select' );

endif; // class_exists check

?>
