<?php

if( ! class_exists('acf_field_user_password') ) :

class acf_field_user_password extends acf_field_password {
	
	
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
		$this->name = 'user_password';
		$this->label = __("Password",FEA_NS);
        $this->category = __( 'User', FEA_NS );
		$this->defaults = array(
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> '',
            'force_edit'    => 0,
            'password_strength' => '3',
        );
        add_filter( 'acf/load_field/type=password',  [ $this, 'load_user_password_field'] );
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      

    }
    
    function load_user_password_field( $field ){
        if( ! empty( $field['custom_password'] ) ){
            $field['type'] = 'user_password';
        }
        return $field;
    }

    function prepare_field( $field ){
        if( isset( $field['wrapper']['class'] ) ){
            $field['wrapper']['class'] .= ' password_main';
        }else{
            $field['wrapper']['class'] = 'password_main';
        }	

        if( ! $field['value'] ) return $field;

        if( empty( $field['force_edit'] ) ){
            $field['required'] = false;
            $field['wrapper']['class'] .= ' edit_password';
            $field['edit_user_password'] = true;	
        } 

        $form = $GLOBALS['admin_form'];
        if( isset( $form['approval'] ) ){
            return false;
        }else{
            $field['value'] = '';
        }
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
                $value = 'i';
            }
        }
        return $value;
    }

    function validate_value( $is_valid, $value, $field, $input ){
        if( is_numeric( $_POST['_acf_user'] ) && ! isset( $_POST['edit_user_password'] ) ){
            return $is_valid;
        }
                 
            if( isset( $_POST['custom_password_confirm'] ) && $_POST['acff']['user'][ $_POST['custom_password_confirm'] ] != $value ){
                return __( 'The passwords do not match', FEA_NS );
            }	
            if( (int) esc_attr( $_POST['password-strength'] ) < (int) esc_attr( $_POST['required-strength'] ) ){
                if( ! $field['required'] && $value == '' && ! isset( $_POST['edit_user_password'] ) ){
                    return $is_valid;
                }
                return __( 'The password is too weak. Please make it stronger.', FEA_NS );
            }	
                    
        return $is_valid;
    }

    function load_field( $field ){
      $field['name'] = $field['type'];
      return $field;
}
function pre_update_value( $value, $post_id = false, $field = false ){
        if( empty( $_POST['edit_user_password'] ) ) return null;
        $user = explode( 'user_', $post_id ); 

        if( ! empty( $user[1] ) ){
            $user_id = $user[1]; 
            remove_action( 'acf/save_post', '_acf_do_save_post' );
            wp_update_user( array( 'ID' => $user_id, 'user_pass' => $value ) );
            add_action( 'acf/save_post', '_acf_do_save_post' );
        }
        return null;
    }

    function update_value( $field ){
        return null;
    }

    function render_field( $field ){
        $field['type'] = 'password';

        parent::render_field( $field );
        wp_enqueue_script( 'password-strength-meter' );
        wp_enqueue_script( 'fea-password-strength' );
        echo  '<input type="hidden" name="custom_password" value="' . $field['key'] . '"/>' ;
        if( isset( $field['password_strength'] ) ){
            
            echo '<div class="pass-strength-result weak"></div>';
            echo '<input type="hidden" value="' . $field['password_strength'] . '" name="required-strength"/>';	
            echo '<input class="password-strength" type="hidden" value="" name="password-strength"/>';
        }	
        if ( empty( $field[ 'force_edit' ] ) ) {        
            if ( ! empty( $field['edit_user_password'] ) ) {
                $edit_text = empty( $field['edit_password'] ) ? __( 'Edit Password', FEA_NS ) : $field['edit_password'];
                $cancel_text = empty( $field['cancel_edit_password'] ) ? __( 'Cancel', FEA_NS ) : $field['cancel_edit_password'];
                echo '<button class="cancel-edit" type="button">'.$cancel_text.'</button><button class="acf-button button button-primary edit-password" type="button">'.$edit_text.'</button>';
            }
        }

    }

    function render_field_settings( $field ){
        parent::render_field_settings( $field );
        acf_render_field_setting( $field, array(
            'label'			=> __('Password Strength',FEA_NS),
            'name'			=> 'password_strength',
            'type'			=> 'select',
            'default_value' => '3',
            'choices'       => array(
                '1'  => __( 'Very Weak', FEA_NS ),
                '2' => __( 'Weak', FEA_NS ),
                '3' => __( 'Medium', FEA_NS ),
                '4' => __( 'Strong', FEA_NS ),
            ),
        ) );
        acf_render_field_setting( $field, array(
            'label'			=> __('Force Edit',FEA_NS),
            'instructions'	=> __( 'Force User to edit the password when editing their account.', FEA_NS ),
            'name'			=> 'force_edit',
            'type'			=> 'true_false',
            'ui'			=> 1,
        ) );
        acf_render_field_setting( $field, array(
            'label'			=> __( 'Edit Password Button', FEA_NS ),
            'name'			=> 'edit_password',
            'type'			=> 'text',
            'default_value' => __( 'Edit Password Button', FEA_NS ),
            'conditions'	=> array(
                array(
                    'field'		=> 'force_edit',
                    'operator'	=> '!=',
                    'value'		=> '1'
                )
            )
        ) );
        acf_render_field_setting( $field, array(
            'label'			=> __( 'Cancel Button', FEA_NS ),
            'name'			=> 'cancel_edit_password',
            'type'			=> 'text',
            'default_value' => __( 'Cancel', FEA_NS ),
            'conditions'	=> array(
                array(
                    'field'		=> 'force_edit',
                    'operator'	=> '!=',
                    'value'		=> '1'
                )
            )
        ) );
    }
}

// initialize
acf_register_field_type( 'acf_field_user_password' );

endif;
	
?>