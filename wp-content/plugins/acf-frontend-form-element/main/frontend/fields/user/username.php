<?php

if( ! class_exists('acf_field_username') ) :

class acf_field_username extends acf_field_text {
	
	
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
		$this->name = 'username';
		$this->label = __("Username",FEA_NS);
        $this->category = __( 'User', FEA_NS );
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
		);
        add_filter( 'acf/load_field/type=text',  [ $this, 'load_username_field'] );
		add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
    }
    
    function load_username_field( $field ){
        if( ! empty( $field['custom_username'] ) ){
            $field['type'] = 'username';
        }
        return $field;
    }

    public function prepare_field( $field ) {
        //make sure field is not disabled when no value exists
        if( ! $field['value'] || isset( $GLOBALS['admin_form']['submission'] ) ){
            $field['disabled'] = 0;
        } 

        // return
        return $field;
    }	

    public function load_value( $value, $post_id = false, $field = false ){
        $user = explode( 'user_', $post_id ); 
        if( empty( $user[1] ) ){
            return $value;
        }else{
            $user_id = $user[1]; 
            $edit_user = get_user_by( 'ID', $user_id );
            if( $edit_user instanceof WP_User ){
                $value = $edit_user->user_login;
            }        
        }
        return $value;
    }

    public function validate_value( $is_valid, $value, $field, $input ){        
        if( $field['required'] == 0 && $value == '' ){
            return $is_valid;
        }

        if( ! empty( $field['save_prepend'] ) ) $value = $field['prepend'] . $value;
        if( ! empty( $field['save_append'] ) ) $value .= $field['append'];

        if( ! validate_username( $value ) ){
            return __( 'The username contains illegal characters. Please enter only latin letters, numbers, @, -, . and _', FEA_NS );
        }
        
        if( empty( $_POST['_acf_user'] ) ) return $is_valid;

        $user_id = wp_kses( $_POST['_acf_user'], 'strip' );
        $edit_user = get_user_by( 'ID', $user_id );
        
        $username_taken = sprintf( __( 'The username %s is taken. Please try a different username', FEA_NS ), $value );

        if( username_exists( $value ) ){
            if( ! empty( $edit_user->user_login ) && $edit_user->user_login == $value ){
                return $is_valid;
            }
            return $username_taken;
        }	
        if( email_exists( $value ) ){
            if( ! empty( $edit_user->user_email ) && $edit_user->user_email == $value ){
                return $is_valid;
            }
            return $username_taken;
        }	
        
        return $is_valid;
    }

    function load_field( $field ){
      $field['name'] = $field['type'];
      return $field;
}
function pre_update_value( $value, $post_id = false, $field = false ){
        $user = explode( 'user_', $post_id ); 
        if( ! empty( $user[1] ) ){
            $user_id = $user[1]; 
            $user_object = get_user_by( 'ID', $user_id );
            if( $user_object->user_login == $value ) return null;

            if( get_current_user_id() == $user_id ){
                $_POST['log_back_in'] = array( $user_id, $value );
            }
            global $wpdb; 
            $wpdb->update( $wpdb->users, array( 'user_login' => $value ), ['ID' => $user_id ] );
        }
        return null;
    }

    function render_field( $field ){
        $field['type'] = 'text';
        parent::render_field( $field );
    }

   
}

// initialize
acf_register_field_type( 'acf_field_username' );

endif;
	
?>