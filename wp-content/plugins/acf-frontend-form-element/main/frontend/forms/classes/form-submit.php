<?php

namespace Frontend_WP\Classes;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}


if ( !class_exists( 'Frontend_WP\\Classes\\Submit_Form' ) ) {
    class Submit_Form
    {
        public function validate_submitted_form()
        {
            // validate
            if ( !acf_verify_ajax() ) {
                die;
            }
            do_action( 'acf/validate_save_post' );
            // vars
            $json = array(
                'valid'  => 1,
                'errors' => 0,
            );
            
            if ( !empty($_POST['acff']) ) {
                if ( !empty($_POST['acff']['_validate_email']) ) {
                    acf_add_validation_error( '', __( 'Spam Detected', 'acf' ) );
                }
                $data_types = array(
                    'post',
                    'user',
                    'term',
                    'options',
                    'woo_product'
                );
                foreach ( $data_types as $type ) {
                    if ( isset( $_POST['acff'][$type] ) ) {
                        $this->validate_values( $_POST['acff'][$type], 'acff[' . $type . ']' );
                    }
                }
            }
            
            if ( !empty($_POST['acf']) ) {
                acf_validate_values( $_POST['acf'], 'acf' );
            }
            if ( !empty($_POST['form']) ) {
                acf_validate_values( $_POST['form'], 'form' );
            }
            // vars
            $errors = acf_get_validation_errors();
            // bail ealry if no errors
            if ( !$errors ) {
                wp_send_json_success( $json );
            }
            // update vars
            $json['valid'] = 0;
            $json['errors'] = $errors;
            // return
            wp_send_json_success( $json );
        }
        
        function validate_values( $values, $input_prefix = '' )
        {
            // bail early if empty
            if ( empty($values) ) {
                return;
            }
            $form = json_decode( acf_decrypt( $_POST['_acf_form'] ), true );
            
            if ( isset( $_POST['_acf_step'] ) ) {
                $current_step = $_POST['_acf_step'];
                $steps_count = count( $form['steps'] );
                
                if ( !empty($form['validate_steps']) && $steps_count > $current_step ) {
                    $current_fields = $form['steps'][$current_step]['fields'];
                } else {
                    $current_fields = array();
                    foreach ( $form['steps'] as $step ) {
                        $current_fields = array_merge( $current_fields, $step['fields'] );
                    }
                }
            
            } else {
                $current_fields = $form['fields'];
            }
            
            $current_keys = array_keys( $current_fields );
            // loop
            foreach ( $values as $key => $value ) {
                if ( !in_array( $key, $current_keys ) ) {
                    continue;
                }
                $field = $current_fields[$key];
                if ( !is_array( $field ) ) {
                    $field = acf_maybe_get_field( $field );
                }
                if ( !$field ) {
                    continue;
                }
                $input = $input_prefix . '[' . $key . ']';
                // bail early if not found
                if ( isset( $field['frontend_admin_display_mode'] ) && $field['frontend_admin_display_mode'] == 'hidden' ) {
                    continue;
                }
                // validate
                acf_validate_value( $value, $field, $input );
            }
        }
        
        public function verify_nonce( $value )
        {
            // vars
            $nonce = acf_maybe_get_POST( '_acf_nonce' );
            // bail early nonce does not match (post|user|comment|term)
            if ( !$nonce || !wp_verify_nonce( $nonce, $value ) ) {
                return false;
            }
            // reset nonce (only allow 1 save)
            $_POST['_acf_nonce'] = false;
            // return
            return true;
        }
        
        public function check_submit_form()
        {
            // verify nonce
            if ( !$this->verify_nonce( 'fea_form' ) ) {
                wp_send_json_error( __( 'Nonce Error', FEA_NS ) );
            }
            // bail ealry if form not submit
            if ( empty($_POST['_acf_form']) ) {
                wp_send_json_error( __( 'No Form Data', FEA_NS ) );
            }
            // load form
            $form = json_decode( acf_decrypt( $_POST['_acf_form'] ), true );
            // bail ealry if form is corrupt
            if ( empty($form) ) {
                wp_send_json_error( __( 'No Form Data', FEA_NS ) );
            }
            // kses
            if ( $form['kses'] && isset( $_POST['acff'] ) ) {
                $_POST['acff'] = wp_kses_post_deep( $_POST['acff'] );
            }
            // submit
            $this->submit_form( $form );
        }
        
        function create_record( $form, $save = false )
        {
            // Retrieve all form fields and their values
            if ( empty($form['record']) ) {
                $form['record'] = array(
                    'fields' => false,
                );
            }
            
            if ( isset( $form['field_objects'] ) ) {
                $objects = $form['field_objects'];
            } else {
                $objects = false;
            }
            
            $record = array();
            if ( !empty($form["save_all_data"]) && in_array( 'verify_email', $form["save_all_data"] ) ) {
                $record['emails_to_verify'] = array();
            }
            if ( !empty($_POST) ) {
                foreach ( $_POST as $key => $value ) {
                    
                    if ( $key == 'acff' ) {
                        $actions = array(
                            'post',
                            'user',
                            'term',
                            'woo_product',
                            'options',
                            'admin_options'
                        );
                        foreach ( $value as $k => $inputs ) {
                            
                            if ( in_array( $k, $actions ) ) {
                                if ( is_array( $inputs ) ) {
                                    foreach ( $inputs as $i => $input ) {
                                        $record = $this->add_value_to_record(
                                            $record,
                                            $i,
                                            $input,
                                            $k,
                                            $objects
                                        );
                                    }
                                }
                            } else {
                                $record = $this->add_value_to_record(
                                    $record,
                                    $k,
                                    $inputs,
                                    false,
                                    $objects
                                );
                            }
                        
                        }
                    } else {
                        if ( $key == '_acf_form' ) {
                            continue;
                        }
                        $record[$key] = $value;
                    }
                
                }
            }
            if ( isset( $record['submission_title_filled'] ) ) {
                unset( $record['submission_title_filled'] );
            }
            $form['record'] = $record;
            // add global for backwards compatibility
            $GLOBALS['admin_form'] = $form;
            $form = fea_instance()->dynamic_values->get_form_dynamic_values( $form );
            $save = get_option( 'frontend_admin_save_submissions' );
            if ( isset( $form['no_record'] ) ) {
                $save = false;
            }
            if ( !empty($form['save_form_submissions']) ) {
                $save = true;
            }
            $save = apply_filters( FEA_PREFIX . '/save_submission', $save, $form );
            if ( $save ) {
                $form = $this->save_record( $form, $save );
            }
            return $form;
        }
        
        public function add_value_to_record(
            $record,
            $key,
            $input,
            $group = false,
            $objects = false
        )
        {
            $field = acf_get_field( $key );
            if ( !$field && !empty($objects[$key]) ) {
                $field = $objects[$key];
            }
            
            if ( $field ) {
                if ( $field['type'] == 'fields_select' ) {
                    return $record;
                }
                if ( isset( $record['emails_to_verify'] ) && in_array( $field['type'], array( 'email', 'user_email' ) ) ) {
                    
                    if ( $input ) {
                        
                        if ( email_exists( $input ) ) {
                            $user = get_user_by( 'email', $input );
                            if ( isset( $user->ID ) ) {
                                $verified = get_user_meta( $user->ID, 'frontend_admin_email_verified', 1 );
                            }
                        } else {
                            $verified_emails = get_option( 'frontend_admin_verified_emails' );
                            
                            if ( $verified_emails ) {
                                $verified_emails = explode( ',', $verified_emails );
                                if ( in_array( $input, $verified_emails ) ) {
                                    $verified = true;
                                }
                            }
                        
                        }
                        
                        
                        if ( empty($verified) ) {
                            $record['emails_to_verify'][$input] = $input;
                        } else {
                            $record['verified_emails'][$input] = $input;
                        }
                    
                    }
                
                }
                if ( is_string( $input ) ) {
                    $input = html_entity_decode( $input );
                }
                $field['_input'] = $input;
                
                if ( $field['type'] == 'user_password' ) {
                    $field['value'] = $field['_input'] = wp_hash_password( $field['_input'] );
                } else {
                    $field['value'] = acf_format_value( $input, 0, $field );
                }
                
                
                if ( is_string( $field['value'] ) && empty($record['submission_title_filled']) ) {
                    $record['submission_title'] = $field['value'];
                    $record['submission_title_filled'] = true;
                }
                
                $record = apply_filters(
                    'frontend_admin/add_to_record',
                    $record,
                    $group,
                    $field
                );
                $record = apply_filters(
                    'frontend_admin/add_to_record/' . $field['type'],
                    $record,
                    $group,
                    $field
                );
                $record = apply_filters(
                    'frontend_admin/add_to_record/' . $field['key'],
                    $record,
                    $group,
                    $field
                );
                
                if ( $group ) {
                    $record['fields'][$group][$field['name']] = $field;
                } else {
                    $record['fields'][$field['name']] = $field;
                }
            
            }
            
            return $record;
        }
        
        public function submit_form( $form )
        {
            if ( empty($form['approval']) ) {
                do_action( FEA_PREFIX . '/form/on_submit', $form );
            }
            $form = $this->create_record( $form );
            $form['submission_status'] = 'approved';
            $save = get_option( 'frontend_admin_save_submissions' );
            foreach ( fea_instance()->local_actions as $name => $action ) {
                
                if ( !$save || empty($form["save_all_data"]) || isset( $form['approval'] ) ) {
                    $form = $action->run( $form );
                } else {
                    if ( $name != 'options' && isset( $form["{$name}_id"] ) ) {
                        $form['record'][$name] = $form["{$name}_id"];
                    }
                }
            
            }
            
            if ( !empty($form["save_all_data"]) ) {
                
                if ( in_array( 'verify_email', $form["save_all_data"] ) ) {
                    
                    if ( empty($form['record']['emails_to_verify']) && empty($form['record']['verified_emails']) ) {
                        $current_user = wp_get_current_user();
                        
                        if ( isset( $current_user->ID ) ) {
                            $verified = get_user_meta( $current_user->ID, 'frontend_admin_email_verified', 1 );
                            if ( !$verified ) {
                                $form['record']['emails_to_verify'][$current_user->user_email] = $current_user->user_email;
                            }
                        }
                    
                    }
                    
                    if ( empty($form['record']['emails_to_verify']) ) {
                        unset( $form["save_all_data"]['verify_email'] );
                    }
                } else {
                    unset( $form['record']['emails_to_verify'] );
                }
                
                $form['submission_status'] = implode( ',', $form["save_all_data"] );
            } else {
                unset( $form['record']['emails_to_verify'] );
            }
            
            $this->return_form( $form );
        }
        
        public function send_verification_emails( $form )
        {
            foreach ( $form['record']['emails_to_verify'] as $email_address ) {
                $subject = __( 'Please verify your email.', FEA_NS );
                $message = '<h1>' . $subject . '</h1>';
                $token = wp_create_nonce( 'frontend-admin-verify-' . $email_address );
                $message .= '<p>' . sprintf( __( 'Please click <a href="%s">here</a> to verify your email. Thank you.', FEA_NS ) . '</p>', add_query_arg( array(
                    'submit_id'     => $form['submission'],
                    'email-address' => $email_address,
                    'token'         => $token,
                ), $form['return'] ) );
                // Set the type of email to HTML.
                $headers[] = 'Content-type: text/html; charset=UTF-8';
                $header_string = implode( "\r\n", $headers );
                return wp_mail(
                    $email_address,
                    $subject,
                    $message,
                    $header_string
                );
            }
        }
        
        public function return_form( $form )
        {
            $types = array(
                'post',
                'user',
                'term',
                'product'
            );
            $form_ids = array();
            foreach ( $types as $type ) {
                if ( isset( $form['record'][$type] ) ) {
                    $form[$type . '_id'] = $form['record'][$type];
                }
            }
            $GLOBALS['admin_form'] = $form;
            $update_message = $form['update_message'];
            if ( is_string( $update_message ) && strpos( $update_message, '[' ) !== 'false' ) {
                $update_message = fea_instance()->dynamic_values->get_dynamic_values( $update_message, $form );
            }
            $response = array(
                'to_top' => true,
            );
            
            if ( $form['show_update_message'] ) {
                $response['success_message'] = $update_message;
                $response['location'] = $form['message_location'];
                $response['form_element'] = $form['id'];
            }
            
            
            if ( !empty($form['ajax_submit']) ) {
                
                if ( $form['ajax_submit'] === 'submission_form' ) {
                    $response['submission'] = $form['submission'];
                    $response['submission_title'] = $form['record']['submission_title'];
                    $response['close_modal'] = 1;
                    $submission_form = true;
                } else {
                    
                    if ( isset( $form['form_attributes']['data-field'] ) ) {
                        $response['post_id'] = $form['post_id'];
                        $response['field_key'] = $form['form_attributes']['data-field'];
                        $title = get_post_field( 'post_title', $form['post_id'] ) . '<a href="#" class="acf-icon -pencil small dark edit-rel-post render-form" data-name="edit_item"></a>';
                        $rel_field = acf_get_field( $response['field_key'] );
                        
                        if ( $rel_field && acf_in_array( 'featured_image', $rel_field['elements'] ) ) {
                            // vars
                            $class = 'thumbnail';
                            $thumbnail = acf_get_post_thumbnail( $form['post_id'], array( 17, 17 ) );
                            // icon
                            if ( $thumbnail['type'] == 'icon' ) {
                                $class .= ' -' . $thumbnail['type'];
                            }
                            // append
                            $title = '<div class="' . $class . '">' . $thumbnail['html'] . '</div>' . $title;
                        }
                        
                        $response['append'] = [
                            'id'     => $form['post_id'],
                            'text'   => $title,
                            'action' => ( $form['save_to_post'] == 'edit_post' ? 'edit' : 'add' ),
                        ];
                    }
                    
                    if ( isset( $form['redirect_action'] ) ) {
                        
                        if ( $form['redirect_action'] == 'clear' ) {
                            foreach ( $types as $type ) {
                                
                                if ( $form["save_to_{$type}"] == "new_{$type}" ) {
                                    $form[$type . '_id'] = "add_{$type}";
                                    $form["save_to_{$type}"] = "new_{$type}";
                                }
                            
                            }
                        } else {
                            foreach ( $types as $type ) {
                                $form["save_to_{$type}"] = "edit_{$type}";
                            }
                        }
                    
                    }
                }
            
            } else {
                $form = $this->get_redirect_url( $form );
                // vars
                $return = acf_maybe_get( $form, 'return', '' );
                // redirect
                
                if ( $return ) {
                    // update %placeholders%
                    
                    if ( isset( $form['post_id'] ) ) {
                        $return = str_replace( array( '%post_id%', '[post:id]' ), $form['post_id'], $return );
                        $return = str_replace( array( '%post_url%', '[post:url]' ), get_permalink( $form['post_id'] ), $return );
                    }
                    
                    if ( isset( $form['last_step'] ) ) {
                        $return = remove_query_arg( [ 'form_id', 'modal', 'step' ], $return );
                    }
                    $response['redirect'] = $return;
                    if ( isset( $form['redirect_action'] ) && $form['redirect_action'] == 'edit' ) {
                        foreach ( $types as $type ) {
                            $response['edit_data'][$type] = $form[$type . '_id'];
                        }
                    }
                    if ( !empty($_POST['_acf_modal']) ) {
                        $response['modal'] = true;
                    }
                    $response['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );
                    $expiration_time = time() + 600;
                    setcookie(
                        'admin_form_success',
                        json_encode( $response ),
                        $expiration_time,
                        '/'
                    );
                }
            
            }
            
            $save = get_option( 'frontend_admin_save_submissions' );
            if ( isset( $form['no_record'] ) ) {
                $save = false;
            }
            $save = apply_filters( FEA_PREFIX . '/save_submission', $save, $form );
            if ( $save ) {
                $this->save_record( $form, $form['submission_status'] );
            }
            
            if ( isset( $submission_form ) ) {
                $form = fea_instance()->submissions_handler->get_submission_form( $form['submission'], array(), 1 );
                ob_start();
                fea_instance()->form_display->render_form( $form );
                $response['reload_form'] = ob_get_clean();
            }
            
            do_action( 'fea_after_submit', $form );
            wp_send_json_success( $response );
            die;
        }
        
        public function reload_form( $form_ids, $form, $step )
        {
            if ( isset( $form['step_index'] ) ) {
                $form['step_index'] = $form['step_index'] + 1;
            }
            $types = array(
                'post',
                'user',
                'term',
                'product'
            );
            foreach ( $types as $type ) {
                
                if ( !empty($form['record'][$type]) ) {
                    $form[$type . '_id'] = $form['record'][$type];
                    $form["save_to_{$type}"] = "edit_{$type}";
                }
            
            }
            $form = $this->save_record( $form );
            ob_start();
            fea_instance()->form_display->render_form( $form );
            $reload_form = ob_get_contents();
            ob_end_clean();
            wp_send_json_success( [
                'reload_form' => $reload_form,
                'step'        => $form['step_index'],
            ] );
            die;
        }
        
        public function save_record( $form, $status = 'in_progress' )
        {
            
            if ( isset( $form['no_cookies'] ) ) {
                unset( $form['no_cookies'] );
                $no_cookie = true;
            }
            
            if ( !empty($form['approval']) ) {
                $status = 'approved';
            }
            global  $wpdb ;
            $args = [
                'fields' => acf_encrypt( json_encode( $form ) ),
                'user'   => get_current_user_id(),
                'status' => $status,
                'title'  => $form['record']['submission_title'],
            ];
            
            if ( empty($form['submission']) ) {
                $args['created_at'] = current_time( 'mysql' );
                $args['form'] = $form['id'];
                $form['submission'] = fea_instance()->submissions_handler->insert_submission( $args );
            } else {
                fea_instance()->submissions_handler->update_submission( $form['submission'], $args );
            }
            
            if ( !empty($form['record']['emails_to_verify']) ) {
                if ( empty($form['steps']) || !empty($form['last_step']) ) {
                    $this->send_verification_emails( $form );
                }
            }
            
            if ( empty($no_cookie) ) {
                $expiration_time = time() + 86400;
                setcookie(
                    $form['id'],
                    $form['submission'],
                    $expiration_time,
                    '/'
                );
            }
            
            return $form;
        }
        
        public function form_message()
        {
            
            if ( isset( $_COOKIE['admin_form_success'] ) ) {
                $form_success = json_decode( stripslashes( $_COOKIE['admin_form_success'] ), true );
            } else {
                return;
            }
            
            
            if ( empty($form_success['frontend-form-nonce']) || !wp_verify_nonce( $form_success['frontend-form-nonce'], 'frontend-form' ) ) {
                $user_id = get_current_user_id();
                if ( empty($form_success['message_token']) || get_user_meta( $user_id, 'message_token', true ) !== $form_success['message_token'] || isset( $form_success['used'] ) ) {
                    return;
                }
            }
            
            
            if ( isset( $form_success['success_message'] ) && $form_success['location'] == 'other' ) {
                $message = wp_unslash( wp_kses( $form_success['success_message'], 'post' ) );
                $return = '<div class="-fixed frontend-admin-message">
					<div class="elementor-element elementor-element-' . $form_success['form_element'] . '">
						<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">' . $message . '</p><span class="frontend-admin-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div>
					</div>
					</div>';
                fea_instance()->wp_hooks->enqueue_scripts( 'frontend_admin_form' );
                fea_instance()->wp_hooks->acfdata( true );
                echo  $return ;
                unset( $_COOKIE['admin_form_success'] );
            }
        
        }
        
        public function get_redirect_url( $form )
        {
            if ( !empty($form['return']) ) {
                return $form;
            }
            $redirect_url = '';
            $form['message_location'] = 'other';
            switch ( $form['redirect'] ) {
                case 'custom_url':
                    
                    if ( is_array( $form['custom_url'] ) ) {
                        $redirect_url = $form['custom_url']['url'];
                    } else {
                        $redirect_url = $form['custom_url'];
                    }
                    
                    break;
                case 'current':
                    global  $wp ;
                    $redirect_url = $form['current_url'];
                    //$form_args['reload_current'] = true;
                    $form['message_location'] = 'current';
                    break;
                case 'referer_url':
                    $redirect_url = $form['referer_url'];
                    break;
                case 'post_url':
                    $redirect_url = '%post_url%';
                    break;
            }
            $form['return'] = $redirect_url;
            return $form;
        }
        
        public function delete_object()
        {
            if ( !$this->verify_nonce( 'fea_form' ) ) {
                wp_send_json_error( __( 'Nonce Error', FEA_NS ) );
            }
            // bail ealry if form not submit
            if ( empty($_POST['_acf_form']) ) {
                wp_send_json_error( __( 'No Form Data', FEA_NS ) );
            }
            // load form
            $form = json_decode( acf_decrypt( $_POST['_acf_form'] ), true );
            // bail ealry if form is corrupt
            if ( empty($form) ) {
                wp_send_json_error( __( 'No Form Data', FEA_NS ) );
            }
            if ( empty($_POST['field']) ) {
                wp_send_json_error( __( 'Delete Button Key Not Found', FEA_NS ) );
            }
            $field = acf_get_field( $_POST['field'] );
            //$form = $this->create_record( $form );
            // add global for backwards compatibility
            $GLOBALS['admin_form'] = $form;
            $form['redirect'] = $field['redirect'];
            if ( isset( $field['custom_url'] ) ) {
                $form['custom_url'] = $field['custom_url'];
            }
            $form = $this->get_redirect_url( $form );
            $redirect_args = array(
                'redirect' => $form['return'],
            );
            
            if ( $field['show_delete_message'] ) {
                $message = $field['delete_message'];
                if ( strpos( $message, '[' ) !== 'false' ) {
                    $message = fea_instance()->dynamic_values->get_dynamic_values( $message, $form );
                }
                $redirect_args['success_message'] = $message;
                $redirect_args['location'] = 'other';
                $redirect_args['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );
            }
            
            switch ( $field['type'] ) {
                case 'delete_post':
                    
                    if ( !empty($_POST['_acf_post']) ) {
                        $post_id = intval( $_POST['_acf_post'] );
                        
                        if ( empty($form['force_delete']) ) {
                            $deleted = wp_trash_post( $post_id );
                        } else {
                            $deleted = wp_delete_post( $post_id, true );
                        }
                        
                        $form['record']['post'] = $post_id;
                    }
                    
                    break;
                case 'delete_product':
                    
                    if ( !empty($_POST['_acf_product']) ) {
                        $product_id = intval( $_POST['_acf_product'] );
                        
                        if ( empty($form['force_delete']) ) {
                            $deleted = wp_trash_post( $product_id );
                        } else {
                            $deleted = wp_delete_post( $product_id, true );
                        }
                        
                        $form['record']['product'] = $product_id;
                    }
                    
                    break;
                case 'delete_term':
                    
                    if ( !empty($_POST['_acf_term']) ) {
                        $term_id = intval( $_POST['_acf_term'] );
                        $deleted = wp_delete_term( $term_id, sanitize_text_field( $_POST['_acf_taxonomy_type'] ) );
                        $form['record']['term'] = $term_id;
                    }
                    
                    break;
                case 'delete_user':
                    
                    if ( !empty($_POST['_acf_user']) ) {
                        $user_id = intval( $_POST['_acf_user'] );
                        $deleted = wp_delete_user( $user_id, $field['reassign_posts'] );
                        $form['record']['user'] = $user_id;
                    }
                    
                    break;
                default:
                    wp_send_json_error( __( 'No object found to delete' ) );
            }
            $expiration_time = time() + 600;
            setcookie(
                'admin_form_success',
                json_encode( $redirect_args ),
                $expiration_time,
                '/'
            );
            wp_send_json_success( $redirect_args );
            die;
        }
        
        public function verify_email_address()
        {
            
            if ( isset( $_GET['submit_id'] ) && isset( $_GET['email-address'] ) && isset( $_GET['token'] ) ) {
                $request = $_GET;
            } else {
                return;
            }
            
            $address = $request['email-address'];
            $submission = fea_instance()->submissions_handler->get_submission( $request['submit_id'] );
            if ( empty($submission->fields) ) {
                wp_redirect( home_url() );
            }
            $form = json_decode( acf_decrypt( $submission->fields ), true );
            $record = $form['record'];
            
            if ( isset( $record['emails_to_verify'][$address] ) ) {
                if ( !wp_verify_nonce( $request['token'], 'frontend-admin-verify-' . $address ) ) {
                    return;
                }
                
                if ( email_exists( $address ) ) {
                    $user = get_user_by( 'email', $address );
                    $verified = get_user_meta( $user->ID, 'frontend_admin_email_verified', 1 );
                    if ( $verified ) {
                        return;
                    }
                    if ( isset( $user->ID ) ) {
                        update_user_meta( $user->ID, 'frontend_admin_email_verified', 1 );
                    }
                } else {
                    $verified_emails = get_option( 'frontend_admin_verified_emails', '' );
                    
                    if ( $verified_emails = '' ) {
                        $verified_emails = $address;
                    } else {
                        $verified_emails .= ',' . $address;
                    }
                    
                    update_option( 'frontend_admin_verified_emails', $verified_emails );
                }
                
                unset( $form['record']['emails_to_verify'][$address] );
                $form['record']['verified_emails'][$address] = $address;
                $args = array();
                
                if ( empty($form['record']['emails_to_verify']) ) {
                    
                    if ( $submission->status ) {
                        $old_status = explode( ',', $submission->status );
                        if ( !in_array( 'require_approval', $old_status ) ) {
                            foreach ( fea_instance()->local_actions as $name => $action ) {
                                $form = $action->run( $form );
                            }
                        }
                        $new_status = str_replace( 'verify_email', 'email_verified', $submission->status );
                    }
                    
                    $args['status'] = $new_status;
                }
                
                $args['fields'] = acf_encrypt( json_encode( $form ) );
                fea_instance()->submissions_handler->update_submission( $request['submit_id'], $args );
                $GLOBAL[$address . '_verified'] = true;
            }
        
        }
        
        public function __construct()
        {
            add_action( 'wp_footer', array( $this, 'form_message' ) );
            add_action( 'wp_ajax_acf/validate_save_post', array( $this, 'validate_submitted_form' ), 2 );
            add_action( 'wp_ajax_nopriv_acf/validate_save_post', array( $this, 'validate_submitted_form' ), 2 );
            add_action( 'wp_ajax_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
            add_action( 'wp_ajax_nopriv_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
            add_action( 'admin_post_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
            add_action( 'admin_post_nopriv_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
            add_action( 'wp_ajax_frontend_admin/delete_object', array( $this, 'delete_object' ) );
            add_action( 'wp_ajax_nopriv_frontend_admin/delete_object', array( $this, 'delete_object' ) );
            add_action( 'init', array( $this, 'verify_email_address' ) );
        }
    
    }
    fea_instance()->form_submit = new Submit_Form();
}
