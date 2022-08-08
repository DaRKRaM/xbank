<?php

if( ! class_exists('acf_field_term_name') ) :

class acf_field_term_name extends acf_field_text {
	
	
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
		$this->name = 'term_name';
		$this->label = __("Term Name",FEA_NS);
        $this->category = __( 'Term', FEA_NS );
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> '',
            'change_slug'   => 0
		);
        add_filter( 'acf/load_field/type=text',  [ $this, 'load_term_name_field'] );
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
		
	}
    
    function load_term_name_field( $field ){
        if( ! empty( $field['custom_term_name'] ) ){
            $field['type'] = 'term_name';
        }
        return $field;
    }

    function prepare_field( $field ){
        $field['type'] = 'text';
        return $field;
    }

    function load_value( $value, $post_id = false, $field = false ){
        if( strpos( $post_id, 'term_' ) !== false ){
            $term_id = explode( '_', $post_id )[1];
            $edit_term = get_term( $term_id );
            if( isset( $edit_term->name ) ){
                $value = $edit_term->name;
            }
        }
        return $value;
    }

    function validate_value( $is_valid, $value, $field, $input ){
        if( ! isset( $_POST['_acf_taxonomy_type'] ) ){
            return $is_valid;
        }			


        $slug_key = $_POST['term_slug_field'];
		if( ! empty( $_POST['acff']['term'][$slug_key] ) || ( empty( $field['change_slug'] ) && $term != 'add_term' ) ){
			return $is_valid;	
		}

        $value = sanitize_title( $value );

        if( term_exists( $value, $_POST['_acf_taxonomy_type'] ) ){
            $term_id = wp_kses( $_POST['_acf_term'], 'strip' );
            if( $term_id != 'add_term' && ! empty( $term_id ) ){
                $term_to_edit = get_term( $term_id );
                if( ! empty( $term_to_edit->slug ) && $term_to_edit->slug == $value ){
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
            $update_args = array( 'name' => $value );
            if( ! empty( $field['change_slug'] ) ){
                $slug_key = $_POST['term_slug_field'];
                if( empty( $_POST['acff']['term'][$slug_key] ) ){
                    $update_args['slug'] = sanitize_title( $value );
                }

            }
            remove_action( 'acf/save_post', '_acf_do_save_post' );
            wp_update_term( $term_id, $edit_term->taxonomy, $update_args );
            add_action( 'acf/save_post', '_acf_do_save_post' );
        }
     
        return $value;
    }

    function update_value( $value, $post_id = false, $field = false ){
        return null;
    }

    	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
        parent::render_field_settings( $field );
		// default_value
        acf_render_field_setting( $field, array(
            'label'			=> __('Change Slug'),
            'instructions'	=> 'Save value as term slug as well.',
            'name'			=> 'change_slug',
            'type'			=> 'true_false',
            'ui'			=> 1,
        ) );
    }

}

// initialize
acf_register_field_type( 'acf_field_term_name' );

endif;
	
?>