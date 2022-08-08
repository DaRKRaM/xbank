<?php

if( ! class_exists('acf_field_term_slug') ) :

class acf_field_term_slug extends acf_field_text {
	
	
	/*
	*  initialize
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'term_slug';
		$this->label = __("Term Slug",FEA_NS);
        $this->category = __( 'Term', FEA_NS );
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> '',
            'change_slug'   => 0
		);
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
		
	}

    function prepare_field( $field ){
        $field['type'] = 'text';
        return $field;
    }

    public function load_value( $value, $post_id = false, $field = false ){
        if( strpos( $post_id, 'term_' ) !== false ){
            $term_id = explode( '_', $post_id )[1];
            $edit_term = get_term( $term_id );
            if( isset( $edit_term->slug ) ){
                $value = $edit_term->slug;
            }
        }
        return $value;
    }

    function validate_value( $is_valid, $value, $field, $input ){
        if( ! isset( $_POST['_acf_taxonomy_type'] ) ){
            return $is_valid;
        }			

        if( term_exists( $value, $_POST['_acf_taxonomy_type'] ) ){
            $term_id = wp_kses( $_POST['_acf_term'], 'strip' );
            if( $term_id != 'add_term' && ! empty( $term_id ) ){
                $term_to_edit = get_term( $term_id );
                if( ! empty( $term_to_edit->slug ) && $term_to_edit->slug == sanitize_title( $value ) ){
                    return $is_valid;
                }
            }
            return __( 'The term ' . $value . ' exists.', FEA_NS );
        }
        return $is_valid;
    }

    function load_field( $field ){
      $field['name'] = $field['type'];
      return $field;
}
function pre_update_value( $value, $post_id = false, $field = false ){
        $term_id = explode( '_', $post_id )[1];
        $edit_term = get_term( $term_id );
        if( ! is_wp_error( $edit_term ) ){
            $update_args = array( 'slug' => $value );
            remove_action( 'acf/save_post', '_acf_do_save_post' );
            wp_update_term( $term_id, $edit_term->taxonomy, $update_args );
            add_action( 'acf/save_post', '_acf_do_save_post' );
        }
     
        return $value;
    }

    public function update_value( $value, $post_id = false, $field = false ){
        return null;
    }

}

// initialize
acf_register_field_type( 'acf_field_term_slug' );

endif;
	
?>