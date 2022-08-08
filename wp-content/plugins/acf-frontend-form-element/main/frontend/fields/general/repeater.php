<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('acf_frontend_repeater_field') ) :

    class acf_frontend_repeater_field {

    public function frontend_admin_repeater_row_author( $field ) {
        if( empty( $field['filter_row_edit'] ) ){
            return $field;
        }

            global $post;
            if( ! isset( $post->post_type  ) || $post->post_type == 'acf-field-group' ){
                return $field;
            }

            $row_author_field = [
                'prefix' => 'acf',
                'name' => 'row_author', 
                '_name' => 'row_author', 
                'key' =>  'frontend_admin_row_author', 
                'type' => 'text',
                'required' => '0',
                'instructions' => '',
                'default_value' => 'user_' . get_current_user_id(),
                'wrapper' => [
                    'width' => '',
                    'class' => 'acf-hidden',
                    'id' => '',
                ],
                'maxlength' => '',
                'label' => '',
                'parent' => $field['key'],
            ];
            acf_add_local_field( $row_author_field );

            $field['sub_fields'][] = $row_author_field;

        return $field;
    }	

    public function frontend_admin_repeater_field( $field ) {

        acf_render_field_setting( $field, array(
            'label'			=> __( 'Limit Row Edit to',FEA_NS ),
            'instructions'	=> '',
            'type'			=> 'select',
            'name'			=> 'filter_row_edit',
            'instructions'	=> __( 'Save data to the rows and filter the rows based on that data.', FEA_NS ),
            'choices'		=> [
                'author' =>  __( 'Author of the Row',FEA_NS ),
            ],
            'multiple'		=> 1,
            'ui'			=> 1,
            'allow_null'	=> 1,
        ));

    }


    public function frontend_admin_before_repeater_field( $field ){
		if( empty( $field['filter_row_edit'] ) || is_admin() ){
				return;
			}

			ob_start();
		}

		public function frontend_admin_after_repeater_field( $field ){
			if( empty( $field['filter_row_edit'] ) || is_admin() ){
				return;
			}

			$repeater = ob_get_contents();
			ob_end_clean();

			$rows = htmlentities( $repeater );
			$before = preg_split( '{' . htmlentities( '<tbody>' ) . '}', $rows );

			$after = preg_split( '{' . htmlentities( '</tbody>' ) . '}', $before[1] );

			$rows = preg_split( '{' . htmlentities( '</tr>' ) . '}', $after[0], 0 );

			$rows_display = '';
			$subtract = 0;

			foreach( $rows as $index => $row ){	
				if( $index+2 == count( $rows ) ){
					$rows_display .= $row;
					continue;
				}elseif( is_user_logged_in() ){
					if( strpos( $row, 'user_' . get_current_user_id() ) !== false ) {
						if( $subtract > 0 ){
							$new_index = $index - $subtract;
							$row = str_replace( 'row-'. $index, 'row-'. $new_index, $row );
							$row = str_replace( '<span>'. $index, '<span>'. $new_index, $row );
						}
						$rows_display .= $row;
						$rows_display .= htmlentities( '</tr>' );
					}else{
						$subtract++;
					} 
				}
			}  
			
			$output = $before[0] . htmlentities( '<tbody>' ) . $rows_display . htmlentities( '</tbody>' ) . $after[1];

			echo html_entity_decode( $output );
	
		}
        public function frontend_admin_update_repeater_value( $value, $post_id = false, $field = false ){
			if( empty( $field['filter_row_edit'] ) || is_admin() ){
				return $value;
			}
				
			if( !empty($value) ) { 

			$rows = [];
			$value = array_values( $value );
			$old_value = (int) acf_get_metadata( $post_id, $field['name'] );
			$value_rows = count( $value );

				// remove acfcloneindex
				if( isset($value['acfcloneindex']) ) {
				
					unset($value['acfcloneindex']);
					
				}
				$new_value = 0;

				// loop through rows
				for( $i = 0; $i < $old_value; $i++ ) {
					$row_author = acf_get_metadata( $post_id, $field['name'] . '_' . $i . '_row_author' );
					if( ! empty( $value[ $new_value ] ) && ( ! $row_author || $row_author == $value[ $new_value ]['frontend_admin_row_author'] ) && $row_author != 'user_0'  ){
						$rows[] = $value[ $new_value ];
						$new_value++;
					}else{
						$rows = $this->add_row( $rows, $field, $post_id, $i );
					}
				}

				// remove old rows
				if( $value_rows > $new_value ) {
					
					// loop
					for( $i = $new_value; $i < $value_rows; $i++ ) {
						$rows[] = $value[ $i ];					
					}
					
				}	
			}
			
			return $rows;
			
		}

		public function add_row( $rows, $field, $post_id, $i = 0 ) {		
			// bail early if no layout reference
			if( !is_array($rows) ) return false;
				
			// bail early if no layout
			if( empty( $field['sub_fields'] ) ) return false;
			$new_row = [];
			// loop
			foreach( $field['sub_fields'] as $sub_field ) {
				$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";
				// value
				$value = acf_get_metadata( $post_id, $sub_field['name'] );	

				$new_row[ $sub_field['key'] ] = $value;
			}

			$rows[] = $new_row;

			return $rows;
		}
        function __construct(){
            add_filter( 'acf/update_value/type=repeater', [ $this,'frontend_admin_update_repeater_value'], 9, 3 );
			add_action( 'acf/render_field/type=post_object',  [ $this, 'frontend_admin_add_post_option'], 8); 
			add_action( 'acf/render_field/type=repeater',  [ $this, 'frontend_admin_before_repeater_field'], 8); 
			add_action( 'acf/render_field/type=repeater',  [ $this, 'frontend_admin_after_repeater_field'], 10); 
			//add_filter( 'acf/load_field',  [ $this, 'frontend_admin_repeater_row_author'] );

			add_action( 'acf/render_field_settings/type=repeater',  [ $this, 'frontend_admin_repeater_field'], 5 );
        }
    }

endif;


      