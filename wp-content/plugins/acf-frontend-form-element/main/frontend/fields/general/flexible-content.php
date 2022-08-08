<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('acf_frontend_flexible_content_field') ) :

	class acf_frontend_flexible_content_field {

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

		public function layout_settings( $field ) {
            if( $field['class'] == 'layout-name' ){               

                $_field_key = explode( 'acf_fields[', $field['prefix'] );

                if( isset( $_field_key[1] ) ){
                    $layout_key = explode( '[layout_', $_field_key[1] );
                    if( isset( $layout_key[1] ) ){
                        $layout_key = explode( ']', $layout_key[1] )[0];
                    }else{
                        return;
                    }
                    $_field_key = explode( ']', $_field_key[1] )[0];
                }else{
                    return;
                }
                $_field = acf_get_field( $_field_key );            

                echo '</div></li>
                <li class="acf-fc-meta-preview-image">
                ';
                if( ! empty( $_field['layouts']['layout_' . $layout_key]['preview'] ) ){
                    $preview_image = $_field['layouts']['layout_' . $layout_key]['preview'];
                }else{
                    $preview_image = '';
                } 
                //$layout = $this->get_valid_layout( $layout );
                acf_render_field_wrap( array(
                    'label'        	=> __('Preview Image'),
                    'prefix'        => $field['prefix'],
                    'value'         => $preview_image,
                    'name'			=> 'preview',
                    'class'			=> 'layout-preview',
                    'type'			=> 'image',
                ) );	

                echo '</li>';
            }
		}
	

		public function __construct() {
			
			add_action( 'acf/render_field/name=name',  [ $this, 'layout_settings'] );
		}
	}

	new acf_frontend_flexible_content_field();

endif;

