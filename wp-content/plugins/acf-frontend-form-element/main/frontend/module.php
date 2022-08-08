<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !class_exists( 'Frontend_WP_Hooks' ) ) {
    class Frontend_WP_Hooks
    {
        public function fea_extra_field_setting( $field )
        {
            $types_to_exclude = array( 'tab', 'accordion' );
            if ( in_array( $field['type'], $types_to_exclude ) ) {
                return;
            }
            acf_render_field_setting( $field, array(
                'label'        => __( 'Display Mode' ),
                'instructions' => __( 'Lets you show the editable field or display the value only. You may also hide the field, which is useful if you need to pass hidden data', FEA_NS ),
                'name'         => 'frontend_admin_display_mode',
                'type'         => 'select',
                'choices'      => array(
                'edit'      => __( 'Edit', FEA_NS ),
                'read_only' => __( 'Read Only', FEA_NS ),
                'hidden'    => __( 'Hidden', FEA_NS ),
            ),
            ) );
            global  $post ;
            if ( isset( $post->post_type ) && $post->post_type == 'acf-field-group' ) {
                acf_render_field_setting( $field, array(
                    'label'        => __( 'Show On Frontend Only' ),
                    'instructions' => __( 'Lets you hide the field on the backend to avoid duplicate fields.', FEA_NS ),
                    'name'         => 'only_front',
                    'type'         => 'true_false',
                    'ui'           => 1,
                    'conditions'   => [ [
                    'field'    => 'frontend_admin_display_mode',
                    'operator' => '!=',
                    'value'    => 'hidden',
                ] ],
                ) );
            }
            $types = array(
                'text',
                'textarea',
                'email',
                'number'
            );
            if ( in_array( $field['type'], $types ) ) {
                acf_render_field_setting( $field, array(
                    'label'        => __( 'Read Only', FEA_NS ),
                    'instructions' => __( 'Prevent users from changing the data.', FEA_NS ),
                    'name'         => 'readonly',
                    'type'         => 'true_false',
                    'ui'           => 1,
                ) );
            }
        }
        
        public function hide_frontend_admin_fields( $groups )
        {
            global  $post ;
            
            if ( isset( $post->post_type ) && $post->post_type == 'acf-field-group' ) {
                unset( $groups[__( 'Form', FEA_NS )] );
                unset( $groups[__( 'Mailchimp', FEA_NS )] );
            }
            
            unset( $groups['frontend-admin-hidden'] );
            return $groups;
        }
        
        /* 		public function frontend_admin_load_text_value( $value, $post_id = false, $field = false ){
        			if( ! $this->fea_is_custom( $field ) ){
        				return $value;
        			}
        			if( $post_id ){
        				
        			if( strpos( $post_id, 'comment' ) !== false ){
        					$current_user = wp_get_current_user();
        					if( $current_user !== 0 ){
        						if( isset( $field['custom_author'] ) && $field['custom_author'] == 1 ){
        							$value = esc_html( $current_user->display_name );
        						}				
        					}
        				}
        			}
        
        			return $value;
        		}
        
        
        		public function frontend_admin_load_email_value( $value, $post_id = false, $field = false ){
        			if( ! $this->fea_is_custom( $field ) ){
        				return $value;
        			}
        			if( $post_id ){
        				if( strpos( $post_id, 'comment' ) !== false ){
        					$current_user = wp_get_current_user();
        					if( $current_user !== 0 ){			
        						if( isset( $field['custom_author_email'] ) && $field['custom_author_email'] == 1 ){
        							$value = esc_html( $current_user->user_email );
        						}
        					}
        				}
        			}
        			return $value;
        		}
        			 */
        public function update_frontend_admin_values( $value, $post_id = false, $field = false )
        {
            if ( !empty($field['no_save']) ) {
                return null;
            }
            
            if ( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] == 'publish' ) {
                $revisions = wp_get_post_revisions( $post_id );
                
                if ( !empty($revisions[0]) ) {
                    remove_filter(
                        'acf/update_value',
                        [ $this, 'update_frontend_admin_values' ],
                        7,
                        3
                    );
                    acf_update_value( $value, $revisions[0]->ID, $field );
                    add_filter(
                        'acf/update_value',
                        [ $this, 'update_frontend_admin_values' ],
                        7,
                        3
                    );
                }
            
            }
            
            return $value;
        }
        
        /* 		public function frontend_admin_update_text_value( $value, $post_id = false, $field = false ){
        			if( ! $this->fea_is_custom( $field ) ){
        				return $value;
        			}
        
        			if( strpos( $post_id, 'term' ) !== false ){
        				$term_id = explode( '_', $post_id )[1];
        				$edit_term = get_term( $term_id );
        				if( ! is_wp_error( $edit_term ) ){
        					if( isset( $field['custom_term_name'] ) && $field['custom_term_name'] == 1 ){
        						$update_args = array( 'name' => $value );
        						if( $field['change_slug'] )$update_args['slug'] = sanitize_title( $value );
        						wp_update_term( $term_id, $edit_term->taxonomy, $update_args );
        					}
        				}
        			}elseif( strpos( $post_id, 'comment' ) !== false ){
        				$comment_id = explode( '_', $post_id )[1];
        				$comment_to_edit = [
        					'comment_ID' => $comment_id,
        				];
        				if( isset( $field['custom_author'] ) && $field['custom_author'] == 1 ){
        					$comment_to_edit['comment_author'] = esc_attr( $value );
        				}
        				wp_update_comment( $comment_to_edit );
        			}
        			
        			return null;
        		}
        		
        		
        		public function frontend_admin_update_email_value( $value, $post_id = false, $field = false ){
        			if( ! $this->fea_is_custom( $field ) ){
        				return $value;
        			}
        			if( strpos( $post_id, 'comment' ) !== false ){
        				$comment_id = explode( '_', $post_id )[1];
        				$comment_to_edit = [
        					'comment_ID' => $comment_id,
        				];
        				if( isset( $field['custom_author_email'] ) && $field['custom_author_email'] == 1 ){
        					$comment_to_edit['comment_author_email'] = esc_attr( $value );
        				}
        				wp_update_comment( $comment_to_edit );
        			}
        			
        			return null;
        		} */
        public function exclude_groups( $field_group )
        {
            
            if ( empty($field_group['frontend_admin_group']) ) {
                return $field_group;
            } elseif ( is_admin() ) {
                
                if ( function_exists( 'get_current_screen' ) ) {
                    $current_screen = get_current_screen();
                    
                    if ( isset( $current_screen->post_type ) && $current_screen->post_type == 'admin_form' ) {
                        return $field_group;
                    } else {
                        return null;
                    }
                
                }
            
            }
        
        }
        
        public function load_invisible_field( $field )
        {
            if ( empty($field['invisible']) ) {
                return $field;
            }
            $field['frontend_admin_display_mode'] = 'hidden';
            unset( $field['invisible'] );
            acf_update_field( $field );
            return $field;
        }
        
        public function before_validation()
        {
            if ( isset( $_POST['_acf_field_id'] ) ) {
                acf_add_local_field( array(
                    'key'    => 'frontend_admin_post_type',
                    'label'  => __( 'Post Type', FEA_NS ),
                    'name'   => 'frontend_admin_post_type',
                    'type'   => 'post_type',
                    'layout' => 'vertical',
                ) );
            }
        }
        
        public function skip_validation()
        {
            if ( isset( $_POST['_acf_status'] ) && $_POST['_acf_status'] != 'publish' ) {
                acf_reset_validation_errors();
            }
        }
        
        public function enqueue_scripts( $hook_suffix )
        {
            if ( $hook_suffix != 'frontend_admin_form' && !current_user_can( 'manage_options' ) ) {
                return;
            }
            acf_enqueue_scripts();
            acf_enqueue_uploader();
            acf_localize_text( array(
                'Copy Code'   => __( 'Copy Code', FEA_NS ),
                'Code Copied' => __( 'Code Copied', FEA_NS ),
            ) );
            wp_enqueue_style( 'fea-public' );
            wp_enqueue_style( 'fea-modal' );
            wp_enqueue_script( 'fea-public' );
            wp_enqueue_script( 'fea-modal' );
            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'fea-icon' );
        }
        
        public function acfdata( $form = false )
        {
            
            if ( current_user_can( 'manage_options' ) || $form ) {
                global  $wp_version ;
                acf_localize_data( array(
                    'admin_url'   => admin_url(),
                    'ajaxurl'     => admin_url( 'admin-ajax.php' ),
                    'nonce'       => wp_create_nonce( 'acf_nonce' ),
                    'acf_version' => acf_get_setting( 'version' ),
                    'wp_version'  => $wp_version,
                    'browser'     => acf_get_browser(),
                    'locale'      => acf_get_locale(),
                    'rtl'         => is_rtl(),
                    'screen'      => acf_get_form_data( 'screen' ),
                    'post_id'     => acf_get_form_data( 'post_id' ),
                    'validation'  => acf_get_form_data( 'validation' ),
                    'editor'      => ( acf_is_block_editor() ? 'block' : 'classic' ),
                ) );
                // Print inline script.
                printf( "<script>\n%s\n</script>\n", 'feadata = ' . wp_json_encode( acf_get_instance( 'ACF_Assets' )->data ) . ';' );
            }
        
        }
        
        public function prepare_field_display( $field )
        {
            if ( empty($field['frontend_admin_display_mode']) ) {
                return $field;
            }
            $mode = $field['frontend_admin_display_mode'];
            if ( $mode == 'hidden' ) {
                
                if ( isset( $field['wrapper']['class'] ) ) {
                    $field['wrapper']['class'] .= ' acf-hidden';
                } else {
                    $field['wrapper']['class'] = 'acf-hidden';
                }
            
            }
            return $field;
        }
        
        public function prepare_field_frontend( $field )
        {
            // bail early if no 'admin_only' setting
            if ( empty($field['only_front']) ) {
                return $field;
            }
            $render = true;
            // return false if is admin (removes field)
            if ( is_admin() && !wp_doing_ajax() ) {
                $render = false;
            }
            if ( feadmin_edit_mode() ) {
                $render = true;
            }
            if ( !$render ) {
                return false;
            }
            // return\
            return $field;
        }
        
        public function prepare_field_column( $field )
        {
            if ( !empty($field['start_column']) ) {
                echo  '<div style="width:' . $field['start_column'] . '%" class="acf-column">' ;
            }
            if ( isset( $field['end_column'] ) ) {
                echo  '</div>' ;
            }
            // return\
            return $field;
        }
        
        public function include_forms_as_groups( $groups )
        {
            if ( !empty($GLOBALS['only_acf_field_groups']) ) {
                return $groups;
            }
            $forms = get_posts( array(
                'post_type'      => 'admin_form',
                'posts_per_page' => '-1',
                'post_status'    => 'publish',
            ) );
            foreach ( $forms as $form ) {
                $field_group = (array) maybe_unserialize( $form->post_content );
                // update attributes
                $field_group['ID'] = $form->ID;
                $field_group['title'] = $form->post_title;
                $field_group['key'] = $form->ID;
                $field_group['menu_order'] = $form->menu_order;
                $field_group['active'] = in_array( $form->post_status, array( 'publish', 'auto-draft' ) );
                //$field_group['fields']     = acff()->form_display->get_form_fields( $form->ID );
                $field_group['location'] = array( array( array(
                    'param'    => '',
                    'operator' => '',
                    'value'    => '',
                ) ) );
                acf_add_local_field_group( $field_group );
            }
            return $groups;
        }
        
        public function include_field_types()
        {
            include_once 'walkers/related-terms-walker.php';
            //general
            include_once 'fields/general/related-terms.php';
            include_once 'fields/general/delete-object.php';
            include_once 'fields/general/upload-file.php';
            include_once 'fields/general/upload-image.php';
            include_once 'fields/general/upload-files.php';
            include_once 'fields/general/list-items.php';
            include_once 'fields/general/frontend-blocks.php';
            //include_once('fields/general/flexible-content.php');
            //include_once('fields/general/text.php');
            include_once 'fields/general/relationship.php';
            include_once 'fields/general/text-input.php';
            include_once 'fields/general/url-upload.php';
            include_once 'fields/general/fields-select.php';
            global  $frontend_admin_field_types ;
            if ( !empty($frontend_admin_field_types) ) {
                foreach ( $frontend_admin_field_types as $group => $fields ) {
                    if ( $group == 'options' ) {
                        $group = 'site';
                    }
                    foreach ( $fields as $field ) {
                        include_once "fields/{$group}/{$field}.php";
                    }
                }
            }
        }
        
        public function find_field_type_group( $type )
        {
            $type = str_replace( '_', '-', $type );
            global  $frontend_admin_field_types ;
            if ( !empty($frontend_admin_field_types) ) {
                foreach ( $frontend_admin_field_types as $group => $fields ) {
                    if ( in_array( $type, $fields ) ) {
                        return $group;
                    }
                }
            }
            return false;
        }
        
        public function hide_field_name_setting()
        {
            global  $post ;
            if ( empty($post->post_type) ) {
                return;
            }
            
            if ( $post->post_type == 'acf-field-group' || $post->post_type == 'admin_form' ) {
                global  $frontend_admin_field_types ;
                
                if ( !empty($frontend_admin_field_types) ) {
                    echo  '<style>' ;
                    foreach ( $frontend_admin_field_types as $group => $fields ) {
                        if ( $group == 'general' ) {
                            $fields = array( 'fields-select', 'form-step' );
                        }
                        foreach ( $fields as $field ) {
                            echo  '.acf-field-object-' . $field . ' .acf-field-setting-name,.acf-field-object-' . $field . ' .acf-field-setting-custom_fields_save{display:none}.acf-field-object-' . $field . ' .li-field-name{visibility:hidden}' ;
                        }
                    }
                    $basic_settings = array(
                        'name',
                        'instructions',
                        'required',
                        'conditional_logic',
                        'wrapper',
                        'frontend_admin_display_mode',
                        'field_label_hide',
                        'only_front'
                    );
                    foreach ( $basic_settings as $setting ) {
                        echo  ".acf-field-object-form-step .acf-field-setting-{$setting}, .acf-field-object-submit-button .acf-field-setting-{$setting}, .acf-field-object-fields-select .acf-field-setting-{$setting}{display:none}" ;
                    }
                    echo  '.acf-field-object-form-step .acf-field-setting-custom_fields_save, .acf-field-object-submit-button .acf-field-setting-custom_fields_save{display:none}' ;
                    echo  '.acf-field-object-form-step[data-step="1"] .acf-field-setting-prev_button_text,.acf-field-object-form-step .acf-field-setting-type,.acf-field-object-form-step .acf-field-setting-name,.acf-field-object-submit-button .acf-field-setting-label,.acf-field-object-delete-post .acf-field-setting-label,.acf-field-object-delete-term .acf-field-setting-label,.acf-field-object-delete-user .acf-field-setting-label,.acf-field-object-delete-product .acf-field-setting-label{display:none}' ;
                    echo  '</style>' ;
                }
            
            }
        
        }
        
        public function get_field_types()
        {
            $field_types = array(
                'post' => array(
                'post-to-edit',
                'post-title',
                'post-content',
                'post-excerpt',
                'post-slug',
                'post-status',
                'featured-image',
                'post-type',
                'post-date',
                'post-author',
                'menu-order',
                'allow-comments',
                'delete-post'
            ),
                'user' => array(
                'username',
                'user-email',
                'user-password',
                'user-password-confirm',
                'first-name',
                'last-name',
                'nickname',
                'display-name',
                'user-url',
                'user-bio',
                'role',
                'delete-user'
            ),
                'term' => array(
                'term-name',
                'term-slug',
                'term-description',
                'delete-term'
            ),
            );
            $field_types['general'] = array( 'submit-button', 'fields-select', 'form-step' );
            return $field_types;
        }
        
        public function echo_after_input( $field )
        {
            if ( !empty($field['after_input']) ) {
                echo  $field['after_input'] ;
            }
        }
        
        public function __construct()
        {
            global  $frontend_admin_field_types ;
            $frontend_admin_field_types = $this->get_field_types();
            add_action( 'acf/include_field_types', array( $this, 'include_field_types' ), 6 );
            add_filter( 'acf/load_field_groups', array( $this, 'include_forms_as_groups' ), 5 );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
            add_action( 'admin_print_footer_scripts', [ $this, 'acfdata' ] );
            add_action( 'wp_print_footer_scripts', [ $this, 'acfdata' ] );
            add_action( 'admin_footer', array( $this, 'hide_field_name_setting' ) );
            add_filter( 'acf/prepare_field', array( $this, 'prepare_field_display' ), 3 );
            add_filter( 'acf/prepare_field', array( $this, 'prepare_field_frontend' ), 3 );
            add_filter( 'acf/prepare_field', array( $this, 'prepare_field_column' ), 3 );
            add_action( 'acf/render_field', array( $this, 'echo_after_input' ) );
            //Add field settings by type
            add_action( 'acf/render_field_settings', [ $this, 'fea_extra_field_setting' ] );
            add_filter( 'acf/get_field_types', [ $this, 'hide_frontend_admin_fields' ] );
            add_filter(
                'acf/update_value',
                [ $this, 'update_frontend_admin_values' ],
                7,
                3
            );
            add_filter( 'acf/load_field_group', [ $this, 'exclude_groups' ] );
            add_filter( 'acf/load_field', [ $this, 'load_invisible_field' ] );
            add_action( 'acf/validate_save_post', [ $this, 'before_validation' ], 1 );
            add_action( 'acf/validate_save_post', [ $this, 'skip_validation' ], 999 );
            require_once __DIR__ . '/forms/classes/form-submit.php';
            require_once __DIR__ . '/forms/classes/form-display.php';
            require_once __DIR__ . '/forms/classes/limit-submit.php';
            require_once __DIR__ . '/forms/classes/permissions.php';
            require_once __DIR__ . '/forms/helpers/addon-installer.php';
            require_once __DIR__ . '/forms/helpers/data-fetch.php';
            require_once __DIR__ . '/forms/classes/shortcodes.php';
            require_once __DIR__ . '/forms/helpers/permissions.php';
            require_once __DIR__ . '/forms/actions/action-base.php';
            //actions
            require_once __DIR__ . '/forms/actions/user.php';
            require_once __DIR__ . '/forms/actions/post.php';
            require_once __DIR__ . '/forms/actions/term.php';
            require_once __DIR__ . '/forms/actions/options.php';
        }
    
    }
    fea_instance()->wp_hooks = new Frontend_WP_Hooks();
}
