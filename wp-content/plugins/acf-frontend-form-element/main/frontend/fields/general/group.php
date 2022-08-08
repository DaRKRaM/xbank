<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('frontend_admin_group_field_hooks') ) :

	class frontend_admin_group_field_hooks {

		function load_sub_fields( $fields, $parent ) {
			if( ! isset( $_GET['action'] ) || $_GET['action'] != 'elementor' ){
				return $fields;
			} 
			$sub_fields = array();
			foreach( $fields as $i => $field ){
				if($field['type'] == 'group' ){
					foreach($field['sub_fields'] as $sub_field){
						$fields[] = $sub_field; 
					}
				}
			}

			return $fields;
		}
		public function load_sub_field_value( $value, $post_id = false, $field = false ){
			if( $value || explode( '_', $field['parent'] )[0] !== 'field' ) return $value;

			$parent = acf_get_field( $field['parent'] );
			if( $parent['type'] == 'group' ){
				$value = acf_get_metadata( $post_id, $parent['name']. '_' .$field['name'] );
			}

			return $value;
		}

		public function field_settings( $field ) {
			acf_render_field_setting( $field, array(
				'label'			=> __('No Border'),
				'name'			=> 'no_border',
				'type'			=> 'true_false',
				'ui'			=> 1,
			) );	
		}
		public function prepare_field( $field ) {
			if( empty( $field['no_border'] ) ) return $field;
		
			$field['wrapper']['class'] .= ' acf-group-no-border';

			return $field;
		}

		public function __construct() {
			add_filter( 'acf/load_fields', array( $this, 'load_sub_fields' ), 2, 2 );	
			add_filter( 'acf/load_value', array( $this, 'load_sub_field_value' ), 10, 3 );	
			add_action( 'acf/render_field_settings/type=group',  array( $this, 'field_settings' ) );
			add_filter( 'acf/prepare_field/type=group',  array( $this, 'prepare_field' ) );
		}
	}

	new frontend_admin_group_field_hooks();

endif;

