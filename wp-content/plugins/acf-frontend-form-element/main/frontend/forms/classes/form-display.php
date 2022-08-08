<?php

namespace Frontend_WP\Classes;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}


if ( !class_exists( 'Frontend_WP\\Classes\\Display_Form' ) ) {
    class Display_Form
    {
        public function get_form_data( $form )
        {
            global  $post ;
            $active_user = wp_get_current_user();
            $objects = array();
            global  $frontend_admin_success_returned ;
            if ( isset( $frontend_admin_success_returned ) ) {
                if ( isset( $frontend_admin_success_returned['edit_data'] ) ) {
                    $objects = $frontend_admin_success_returned['edit_data'];
                }
            }
            /* 		if( 'new_comment' == $form['main_action'] ){
            				$form['post_id'] = 'new_comment';
            				if( $form['comment_parent_post'] == 'current_post' ){
            					$comment_parent_post = $post->ID;
            				}else{
            					$comment_parent_post = $form['select_parent_post'];
            				}
            				$form['html_after_fields'] .= '<input type="hidden" value="' . $comment_parent_post . '" name="frontend_admin_parent_post"/><input type="hidden" value="0" name="frontend_admin_parent_comment"/>';
            			} */
            if ( !empty($form['options']) ) {
                return $form;
            }
            $local_actions = fea_instance()->local_actions;
            foreach ( $local_actions as $type => $action ) {
                if ( !empty($form['save_to_' . $type]) && empty($form[$type . '_id']) ) {
                    $form = $action->load_data( $form );
                }
            }
            return $form;
        }
        
        public function get_form( $key )
        {
            
            if ( is_numeric( $key ) && get_post_type( $key ) == 'admin_form' ) {
                $form = get_post( $key );
                return $this->get_form_args( $form );
            }
            
            if ( strpos( $key, 'form_' ) !== false ) {
                $key = 'form_' . $key;
            }
            $args = array(
                'post_type'      => 'admin_form',
                'posts_per_page' => '1',
                'meta_key'       => 'form_key',
                'meta_value'     => $key,
                'post_status'    => 'any',
            );
            $form = get_posts( $args );
            if ( $form ) {
                return $this->get_form_args( $form[0] );
            }
            return array();
        }
        
        public function get_form_args( $form )
        {
            // Get form object if $form is the ID
            if ( is_numeric( $form ) ) {
                $form = get_post( $form );
            }
            // Make sure we have a post and that it's a form
            if ( empty($form) || 'admin_form' != $form->post_type ) {
                return false;
            }
            $form_args = ( $form->post_content ? maybe_unserialize( $form->post_content ) : array() );
            $form_key = get_post_meta( $form->ID, 'form_key', 1 );
            if ( !$form_key ) {
                $form_key = $form->ID;
            }
            $form_args['id'] = str_replace( 'form_', '', $form_key );
            $form_args['ID'] = $form->ID;
            $form_args = $this->get_form_fields( $form->ID, $form_args );
            $form_args['title'] = $form->post_title;
            $form_args['form_title'] = '';
            return $form_args;
        }
        
        public function get_form_fields( $form, $args = array() )
        {
            $fields_args = array(
                'post_type'      => 'acf-field',
                'posts_per_page' => '-1',
                'post_parent'    => $form,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            );
            $multi = false;
            $step = 0;
            foreach ( get_posts( $fields_args ) as $index => $field ) {
                $object = acf_get_field( $field );
                
                if ( empty($_POST['parent_form']) && $object['type'] == 'form_step' ) {
                    $multi = true;
                    
                    if ( $step == 0 ) {
                        $step = 1;
                    } else {
                        if ( !$multi ) {
                            continue;
                        }
                        $step++;
                    }
                    
                    $args['steps'][$step] = $object;
                    $args['steps'][$step]['form_title'] = $object['label'];
                    $args['steps'][$step]['fields'] = array();
                } else {
                    
                    if ( $multi && empty($_POST['parent_form']) ) {
                        $args['steps'][$step]['fields'][$object['key']] = $object;
                    } else {
                        $args['fields'][$object['key']] = $object;
                    }
                
                }
            
            }
            return $args;
        }
        
        public function validate_form( $form )
        {
            if ( !is_array( $form ) ) {
                $form = $this->get_form( $form );
            }
            /* if( empty( $form['no_cookies'] ) && empty( $form['no_record'] ) && ! feadmin_edit_mode() ){
            				$form = $this->get_record( $form );
            			}  */
            $form_class = ( empty($form['form_attributes']['class']) ? 'frontend-form -submit' : 'frontend-form -submit ' . $form['form_attributes']['class'] );
            // defaults
            $form = feadmin_parse_args( $form, array(
                'id'                    => ( isset( $form['ID'] ) ? $form['ID'] : 'acf-form' ),
                'parent_form'           => '',
                'main_action'           => '',
                'custom_fields_save'    => '',
                'fields'                => false,
                'field_objects'         => false,
                'form'                  => true,
                'form_title'            => '',
                'show_form_title'       => false,
                'form_attributes'       => array(
                'class'      => $form_class,
                'action'     => '',
                'method'     => 'post',
                'novalidate' => 'novalidate',
            ),
                'saved_drafts'          => array(),
                'saved_revisions'       => array(),
                'save_progress'         => '',
                'show_delete_button'    => false,
                'message_location'      => 'other',
                'hidden_fields'         => array(),
                'submit_value'          => __( "Update", FEA_NS ),
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'field_el'              => 'div',
                'uploader'              => 'wp',
                'honeypot'              => true,
                'show_update_message'   => true,
                'update_message'        => __( "Post updated", FEA_NS ),
                'html_updated_message'  => '<div class="frontend-admin-message"><div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">%s</p><span class="frontend-admin-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div></div>',
                'error_message'         => __( 'There has been an error.', FEA_NS ),
                'kses'                  => ( isset( $form['no_kses'] ) ? !$form['no_kses'] : true ),
                'new_post_type'         => 'post',
                'new_post_status'       => 'publish',
                'redirect'              => 'current',
                'custom_url'            => '',
                'current_url'           => home_url( add_query_arg( NULL, NULL ) ),
            ) );
            $form['referer_url'] = $form['current_url'];
            if ( wp_get_referer() ) {
                $form['referer_url'] = wp_get_referer();
            }
            $form = $this->get_form_data( $form );
            
            if ( !empty($form['wp_uploader']) ) {
                $form['uploader'] = 'wp';
            } else {
                $form['uploader'] = 'basic';
            }
            
            // filter
            $form = apply_filters( 'acf_frontend/validate_form', $form );
            // return
            return $form;
        }
        
        public function render_submit_button( $form, $index, $hidden = false )
        {
            
            if ( isset( $form['steps'] ) ) {
                $submit_attrs = array(
                    'class'     => 'acf-field',
                    'data-step' => $index,
                );
                
                if ( $hidden ) {
                    $submit_attrs['class'] .= ' frontend-admin-hidden';
                } else {
                    $submit_attrs['data-current'] = 1;
                }
                
                ?>
				<div <?php 
                echo  feadmin_get_esc_attrs( $submit_attrs ) ;
                ?>>
			<?php 
                echo  $this->multi_step_buttons( $form, $index ) ;
                ?>
				</div>
			<?php 
            } else {
                echo  '<div class="fea-submit-buttons"><button type="button" class="fea-submit-button button" data-state="publish">' . $form['submit_value'] . '</button></div>' ;
            }
        
        }
        
        public function multi_step_buttons( $args, $current_step = false )
        {
            if ( !$current_step ) {
                $current_step = $args['step_index'];
            }
            $prev_button = $buttons_class = '';
            $steps_count = count( $args['steps'] );
            $form_step = $args['steps'][$current_step];
            if ( $current_step > 1 ) {
                
                if ( $form_step['prev_button_text'] ) {
                    $prev_step = $current_step - 1;
                    $prev_button .= '<button type="button" name="prev_step" class="prev-button change-step button" data-step="' . $prev_step . '">' . $form_step['prev_button_text'] . '</button> ';
                    $buttons_class = 'frontend-admin-multi-buttons-align';
                }
            
            }
            $next_button_text = ( $form_step['next_button_text'] ? $form_step['next_button_text'] : __( 'Next', FEA_NS ) );
            if ( $current_step == $steps_count && !$form_step['next_button_text'] ) {
                $next_button_text = __( 'Submit', FEA_NS );
            }
            $next_button = '<button class="fea-submit-button button" data-state="publish">' . $next_button_text . '</button>';
            $submit_button = '<div class="fea-submit-buttons ' . $buttons_class . '">' . $prev_button . $next_button . '</div>';
            return $submit_button;
        }
        
        public function step_tabs( $form, $current_step )
        {
            $form = wp_parse_args( $form, array(
                'steps_tabs_display'    => array(),
                'steps_counter_display' => array(),
                'steps_display'         => array(),
            ) );
            $total_steps = count( $form['steps'] );
            $editor = feadmin_edit_mode();
            $current_post = get_post();
            $active_user = wp_get_current_user();
            $screens = [ 'desktop', 'tablet', 'phone' ];
            $tabs_responsive = '';
            if ( !empty($form['steps_tabs_display']) ) {
                foreach ( $screens as $screen ) {
                    if ( !in_array( $screen, $form['steps_tabs_display'] ) ) {
                        $tabs_responsive .= 'frontend-admin-hidden-' . $screen . ' ';
                    }
                }
            }
            $counter_responsive = '';
            if ( !empty($form['steps_counter_display']) ) {
                foreach ( $screens as $screen => $label ) {
                    if ( !in_array( $screen, $form['steps_counter_display'] ) ) {
                        $counter_responsive .= 'frontend-admin-hidden-' . $label . ' ';
                    }
                }
            }
            
            if ( !empty($form['steps_display']) ) {
                
                if ( in_array( 'counter', $form['steps_display'] ) ) {
                    $the_step = '<span class="current-step">' . $current_step . '</span>';
                    
                    if ( isset( $form['counter_text'] ) ) {
                        $counter_text = str_replace( '[current_step]', $the_step, $form['counter_text'] );
                        $counter_text = str_replace( '[total_steps]', $total_steps, $counter_text );
                    } else {
                        $counter_text = $form['counter_prefix'] . $the_step . $form['counter_suffix'];
                    }
                    
                    echo  '<div class="' . $counter_responsive . 'step-count"><p>' . $counter_text . '</p></div>' ;
                }
                
                
                if ( in_array( 'tabs', $form['steps_display'] ) ) {
                    echo  '<div class="frontend-admin-tabs frontend-admin-tabs-view-' . $form['tabs_align'] . '"><div class="frontend-admin-tabs-wrapper ' . $tabs_responsive . '">' ;
                    $steps = $form['steps'];
                    foreach ( $steps as $step_count => $form_step ) {
                        $active = '';
                        if ( $step_count == $current_step ) {
                            $active = 'active';
                        }
                        $change_form = '';
                        if ( $editor || $form['tab_links'] ) {
                            $change_form = ' change-step';
                        }
                        $step_title = ( isset( $form_step['form_title'] ) ? $form_step['form_title'] : $form['form_title'] );
                        
                        if ( isset( $form_step['step_tab_text'] ) ) {
                            $step_title = $form_step['step_tab_text'];
                        } else {
                            $step_title = $form_step['label'];
                        }
                        
                        if ( $step_title == '' ) {
                            $step_title = __( 'Step', FEA_NS ) . ' ' . $step_count;
                        }
                        if ( !empty($form['step_number']) ) {
                            $step_title = $step_count . '. ' . $step_title;
                        }
                        echo  '<a class="form-tab ' . $active . $change_form . '" data-step="' . $step_count . '"><p class="step-name">' . $step_title . '</p></a>' ;
                    }
                    echo  '</div>' ;
                }
            
            }
            
            echo  '<div class="form-steps frontend-admin-content-wrapper">' ;
        }
        
        public function form_set_data( $form = array() )
        {
            // defaults
            $data = wp_parse_args( $form['hidden_fields'], array(
                'screen'     => 'fea_form',
                'nonce'      => '',
                'validation' => 1,
                'changed'    => 0,
                'status'     => '',
                'form'       => acf_encrypt( json_encode( $form ) ),
            ) );
            $data_types = array(
                'post',
                'user',
                'term',
                'product'
            );
            foreach ( $data_types as $type ) {
                if ( !empty($form[$type . '_id']) ) {
                    $data[$type] = $form[$type . '_id'];
                }
            }
            // crete nonce
            $data['nonce'] = wp_create_nonce( $data['screen'] );
            // return
            return $data;
        }
        
        public function form_render_data( $form = array() )
        {
            // set form data
            $data = $this->form_set_data( $form );
            $error_msg = '';
            if ( $form['error_message'] ) {
                $error_msg = 'data-error="' . $form['error_message'] . '"';
            }
            ?>
			<div <?php 
            echo  $error_msg ;
            ?> class="acf-form-data acf-hidden">
				<?php 
            // loop
            foreach ( $data as $name => $value ) {
                // input
                acf_hidden_input( array(
                    'name'  => '_acf_' . $name,
                    'value' => $value,
                ) );
            }
            // actions
            do_action( 'acf/form_data', $data );
            do_action( 'acf/input/form_data', $data );
            ?>
			</div>
			<?php 
        }
        
        public function render_field_setting( $field, $setting, $global = false )
        {
            // Validate field.
            $setting = acf_validate_field( $setting );
            // Add custom attributes to setting wrapper.
            $setting['wrapper']['data-key'] = $setting['name'];
            $setting['wrapper']['class'] .= ' acf-field-setting-' . $setting['name'];
            if ( !$global ) {
                $setting['wrapper']['data-setting'] = $field['type'];
            }
            // Copy across prefix.
            $setting['prefix'] = $field['prefix'];
            // Find setting value from field.
            if ( $setting['value'] === null ) {
                // Name.
                
                if ( isset( $field[$setting['name']] ) ) {
                    $setting['value'] = $field[$setting['name']];
                    // Default value.
                } elseif ( isset( $setting['default_value'] ) ) {
                    $setting['value'] = $setting['default_value'];
                }
            
            }
            // Add append attribute used by JS to join settings.
            if ( isset( $setting['_append'] ) ) {
                $setting['wrapper']['data-append'] = $setting['_append'];
            }
            // Render setting.
            $this->render_field_wrap( $setting, 'tr', 'label' );
        }
        
        public function render_field_wrap( $field, $element = 'div', $instruction = 'label' )
        {
            $field = feadmin_parse_args( $field, array(
                'prefix'       => '',
                'type'         => '',
                'required'     => 0,
                'instructions' => '',
                '_name'        => '',
                'wrapper'      => array(
                'class' => '',
                'id'    => '',
                'width' => '',
            ),
            ) );
            $field = apply_filters( FEA_PREFIX . '/prepare_field', $field );
            $field = apply_filters( FEA_PREFIX . '/prepare_field/type=' . $field['type'], $field );
            if ( isset( $field['key'] ) ) {
                $field = apply_filters( FEA_PREFIX . '/prepare_field/key=' . $field['key'], $field );
            }
            if ( isset( $field['name'] ) ) {
                $field = apply_filters( FEA_PREFIX . '/prepare_field/name=' . $field['name'], $field );
            }
            
            if ( empty($field['_prepare']) ) {
                // Ensure field is complete (adds all settings).
                if ( function_exists( 'acf_validate_field' ) ) {
                    $field = acf_validate_field( $field );
                }
                // Prepare field for input (modifies settings).
                if ( function_exists( 'acf_prepare_field' ) ) {
                    $field = acf_prepare_field( $field );
                }
            }
            
            // Allow filters to cancel render.
            if ( !$field ) {
                return;
            }
            // Determine wrapping element.
            $elements = array(
                'div' => 'div',
                'tr'  => 'td',
                'td'  => 'div',
                'ul'  => 'li',
                'ol'  => 'li',
                'dl'  => 'dt',
            );
            
            if ( isset( $elements[$element] ) ) {
                $inner_element = $elements[$element];
            } else {
                $element = $inner_element = 'div';
            }
            
            
            if ( empty($field['no_wrap']) ) {
                // Generate wrapper attributes.
                $wrapper = array(
                    'id'        => '',
                    'class'     => 'acf-field',
                    'width'     => '',
                    'style'     => '',
                    'data-name' => $field['_name'],
                    'data-type' => $field['type'],
                    'data-key'  => $field['key'],
                );
                // Add field type attributes.
                $wrapper['class'] .= " acf-field-{$field['type']}";
                // add field key attributes
                if ( $field['key'] ) {
                    $wrapper['class'] .= " acf-field-{$field['key']}";
                }
                // Add required attributes.
                // Todo: Remove data-required
                
                if ( $field['required'] ) {
                    $wrapper['class'] .= ' is-required';
                    $wrapper['data-required'] = 1;
                }
                
                // Clean up class attribute.
                $wrapper['class'] = str_replace( '_', '-', $wrapper['class'] );
                $wrapper['class'] = str_replace( 'field-field-', 'field-', $wrapper['class'] );
                // Merge in field 'wrapper' setting without destroying class and style.
                if ( $field['wrapper'] ) {
                    $wrapper = acf_merge_attributes( $wrapper, $field['wrapper'] );
                }
                // Extract wrapper width and generate style.
                // Todo: Move from $wrapper out into $field.
                $width = acf_extract_var( $wrapper, 'width' );
                
                if ( $width ) {
                    $width = acf_numval( $width );
                    
                    if ( $element !== 'tr' && $element !== 'td' ) {
                        $wrapper['data-width'] = $width;
                        $wrapper['style'] .= " width:{$width}%;";
                    }
                
                }
                
                // Clean up all attributes.
                $wrapper = array_map( 'trim', $wrapper );
                $wrapper = array_filter( $wrapper );
                /**
                 * Filters the $wrapper array before rendering.
                 *
                 * @date	21/1/19
                 * @since	5.7.10
                 *
                 * @param	array $wrapper The wrapper attributes array.
                 * @param	array $field The field array.
                 */
                $wrapper = apply_filters( 'acf/field_wrapper_attributes', $wrapper, $field );
                // Append conditional logic attributes.
                if ( !empty($field['conditional_logic']) ) {
                    $wrapper['data-conditions'] = $field['conditional_logic'];
                }
                if ( !empty($field['conditions']) ) {
                    $wrapper['data-conditions'] = $field['conditions'];
                }
                // Vars for render.
                $attributes_html = acf_esc_attr( $wrapper );
                // Render HTML
                echo  "<{$element} {$attributes_html}>" . "\n" ;
                
                if ( $element !== 'td' && (!isset( $field['field_label_hide'] ) || !$field['field_label_hide']) ) {
                    echo  "<{$inner_element} class=\"acf-label\">" . "\n" ;
                    acf_render_field_label( $field );
                    echo  "</{$inner_element}>" . "\n" ;
                }
                
                echo  "<{$inner_element} class=\"acf-input\">" . "\n" ;
                if ( $instruction == 'label' ) {
                    acf_render_field_instructions( $field );
                }
            }
            
            
            if ( !empty($field['frontend_admin_display_mode']) && $field['frontend_admin_display_mode'] == 'read_only' ) {
                echo  fea_instance()->dynamic_values->display_field( $field ) ;
            } else {
                
                if ( isset( $field['php_code'] ) ) {
                    echo  $field['message'] ;
                } else {
                    acf_render_field( $field );
                }
            
            }
            
            
            if ( empty($field['no_wrap']) ) {
                if ( $instruction == 'field' ) {
                    acf_render_field_instructions( $field );
                }
                echo  "</{$inner_element}>" . "\n" ;
                echo  "</{$element}>" . "\n" ;
            }
        
        }
        
        public function get_field_data_type(
            $field,
            $data_type,
            $form,
            $step = false
        )
        {
            
            if ( $data_type != 'options' && isset( $form["{$data_type}_id"] ) ) {
                $data_id = $form["{$data_type}_id"];
            } else {
                $data_id = $data_type;
            }
            
            if ( !feadmin_edit_mode() && $data_id == 'none' && $field['type'] != $data_type . '_to_edit' ) {
                return false;
            }
            
            if ( $step ) {
                $field['wrapper']['class'] .= ' frontend-admin-hidden';
                $field['wrapper']['data-step'] = $step;
            }
            
            
            if ( $data_type == 'product' ) {
                $field['prefix'] = 'acff[woo_' . $data_type . ']';
                $data_type = 'post';
            } else {
                $field['prefix'] = 'acff[' . $data_type . ']';
            }
            
            if ( !isset( $field['value'] ) || $field['value'] === null ) {
                $field['value'] = $this->get_field_value( $data_id, $data_type, $field );
            }
            return $field;
        }
        
        public function render_other_steps( $steps, $form )
        {
            $cf_save = $form['custom_fields_save'];
            $prepend_types = array( 'user', 'term', 'comment' );
            if ( isset( $steps ) ) {
                foreach ( $steps as $index => $step ) {
                    ?>
					<?php 
                    $fields = $this->get_fields_to_display( $form, $step['fields'] );
                    $show_submit = 0;
                    
                    if ( $fields ) {
                        $fields = array_filter( $fields );
                        foreach ( $fields as $field ) {
                            $data_type = fea_instance()->wp_hooks->find_field_type_group( $field['type'] );
                            
                            if ( !$data_type || $data_type == 'general' ) {
                                $data_type = $cf_save;
                                if ( !empty($field['custom_fields_save']) ) {
                                    $data_type = $field['custom_fields_save'];
                                }
                            }
                            
                            $field = $this->get_field_data_type(
                                $field,
                                $data_type,
                                $form,
                                $index
                            );
                            if ( !$field ) {
                                continue;
                            }
                            $show_submit = 1;
                            // Render wrap.
                            $this->render_field_wrap( $field );
                        }
                    }
                    
                    if ( $show_submit && empty($form['submit_button_field']) || isset( $form['approval'] ) ) {
                        $this->render_submit_button( $form, $index, true );
                    }
                    ?>
					<?php 
                }
            }
        }
        
        public function get_field_to_display( $field_data, $fields, $parent = false )
        {
            
            if ( $parent ) {
                $field_data = acf_maybe_get_field( $field_data, false, false );
                if ( !$field_data ) {
                    return $fields;
                }
                if ( isset( $parent['fields_class'] ) ) {
                    $field_data['wrapper']['class'] .= ' ' . $parent['fields_class'];
                }
                if ( !empty($parent['custom_fields_save']) ) {
                    $field_data['custom_fields_save'] = $parent['custom_fields_save'];
                }
                $fields[] = $field_data;
                $GLOBALS['form_fields'][$field_data['type']] = $field_data['key'];
                return $fields;
            }
            
            
            if ( isset( $field_data['column'] ) ) {
                $fields[] = $field_data;
                return $fields;
            } else {
                if ( is_string( $field_data ) ) {
                    $field_data = acf_maybe_get_field( $field_data, false, false );
                }
                if ( $field_data ) {
                    
                    if ( !empty($field_data['sub_fields']) ) {
                        $sub_fields = array();
                        foreach ( $field_data['sub_fields'] as $sub_field ) {
                            $sub_fields = $this->get_field_to_display( $sub_field, $sub_fields );
                        }
                        $field_data['sub_fields'] = $sub_fields;
                        $fields[] = $field_data;
                    } else {
                        $fields[] = $field_data;
                        $GLOBALS['form_fields'][$field_data['type']] = $field_data['key'];
                    }
                
                }
            }
            
            return $fields;
        }
        
        public function get_fields_to_display( $form, $current_fields )
        {
            
            if ( $form['field_objects'] ) {
                $fields = $form['field_objects'];
            } else {
                $fields = array();
                if ( $current_fields ) {
                    foreach ( $current_fields as $field_data ) {
                        if ( isset( $form['approval'] ) && isset( $field_data['type'] ) && $field_data['type'] == 'fields_select' ) {
                            continue;
                        }
                        $fields = $this->get_field_to_display( $field_data, $fields );
                    }
                }
            }
            
            if ( empty($fields) ) {
                return false;
            }
            return $fields;
        }
        
        public function render_fields(
            $form,
            $current_step,
            $hidden = true,
            $current_fields = array()
        )
        {
            if ( isset( $form['fields_to_display'] ) ) {
                foreach ( $form['fields_to_display'] as $chosen_fields ) {
                    
                    if ( is_numeric( $chosen_fields ) ) {
                        $fields_post = get_post( $chosen_fields );
                        
                        if ( isset( $fields_post->post_name ) ) {
                            $chosen_fields = $fields_post->post_name;
                        } else {
                            continue;
                        }
                    
                    }
                    
                    $type_of_choice = explode( '_', $chosen_fields );
                    if ( empty($type_of_choice[1]) ) {
                        continue;
                    }
                    switch ( $type_of_choice[0] ) {
                        case 'field':
                            $current_field = acf_get_field( $chosen_fields );
                            if ( $current_field ) {
                                $current_fields[] = $current_field;
                            }
                            break;
                        case 'group':
                            $current_fields = array_merge( $current_fields, acf_get_fields( $chosen_fields ) );
                            break;
                        case 'form':
                            $current_group = $this->get_form( $chosen_fields );
                            if ( isset( $current_group['fields'] ) ) {
                                $current_fields = array_merge( $current_fields, $current_group['fields'] );
                            }
                            break;
                    }
                }
            }
            if ( empty($current_fields) ) {
                
                if ( isset( $form['steps'] ) ) {
                    $current_fields = $form['steps'][$current_step]['fields'];
                } else {
                    $current_fields = $form['fields'];
                }
            
            }
            $fields = $this->get_fields_to_display( $form, $current_fields );
            $cf_save = $form['custom_fields_save'];
            $el = $form['field_el'];
            $instruction = $form['instruction_placement'];
            /**
             * Filters the $fields array before they are rendered.
             *
             * @date	12/02/2014
             * @since	5.0.0
             *
             * @param	array $fields An array of fields.
             * @param	array $form An array of all of the form data.
             */
            $fields = apply_filters( FEA_PREFIX . '/pre_render_fields', $fields, $form );
            $show_submit_button = 0;
            // Loop over and render fields.
            
            if ( $fields ) {
                // Filter our false results.
                $fields = array_filter( $fields );
                $fields = array_merge( $this->hidden_default_fields( $form ), $fields );
                $open_columns = 0;
                foreach ( $fields as $field ) {
                    
                    if ( isset( $field['_input'] ) ) {
                        $field['value'] = $field['_input'];
                        if ( is_string( $field['value'] ) ) {
                            $field['value'] = stripslashes( $field['value'] );
                        }
                    }
                    
                    
                    if ( isset( $field['render_content'] ) ) {
                        echo  $field['render_content'] ;
                    } elseif ( isset( $field['column'] ) ) {
                        
                        if ( $field['column'] == 'endpoint' ) {
                            if ( $open_columns ) {
                                echo  '</div>' ;
                            }
                            $open_columns--;
                        } else {
                            
                            if ( isset( $field['nested'] ) ) {
                                $open_columns++;
                            } else {
                                if ( $open_columns ) {
                                    while ( $open_columns > 0 ) {
                                        echo  '</div>' ;
                                        $open_columns--;
                                    }
                                }
                                $open_columns++;
                            }
                            
                            echo  '<div class="acf-column elementor-repeater-item-' . $field['column'] . '">' ;
                        }
                    
                    } else {
                        
                        if ( $field['key'] == '_validate_email' ) {
                            $field['prefix'] == 'acff';
                        } elseif ( isset( $form['admin_options'] ) ) {
                            $field['prefix'] = 'acff[admin_options]';
                            $field['value'] = get_option( $field['key'] );
                        } else {
                            $data_type = fea_instance()->wp_hooks->find_field_type_group( $field['type'] );
                            
                            if ( !$data_type || $data_type == 'general' ) {
                                $data_type = $cf_save;
                                if ( !empty($field['custom_fields_save']) ) {
                                    $data_type = $field['custom_fields_save'];
                                }
                            } else {
                                $field['fea_wp_core'] = 1;
                                $new_field_name = 'fea_' . $field['type'];
                                
                                if ( $new_field_name != $field['_name'] ) {
                                    $field['name'] = $new_field_name;
                                    acf_update_field( $field );
                                    $field['_name'] = $new_field_name;
                                }
                            
                            }
                            
                            if ( $current_step ) {
                                $field['wrapper']['data-step'] = $current_step;
                            }
                            $field['wrapper']['data-current'] = 1;
                            $field = $this->get_field_data_type( $field, $data_type, $form );
                            if ( !$field ) {
                                continue;
                            }
                        }
                        
                        if ( empty($field['no_data_collect']) ) {
                            $show_submit_button = 1;
                        }
                        if ( $field['type'] == 'submit_button' ) {
                            $form['submit_button_field'] = $field['key'];
                        }
                        // Render wrap.
                        $this->render_field_wrap( $field, $el, $instruction );
                    }
                
                }
                if ( $open_columns > 0 ) {
                    while ( $open_columns > 0 ) {
                        echo  '</div>' ;
                        $open_columns--;
                    }
                }
            }
            
            if ( !empty($open_accordion) ) {
                echo  '</div></div></div>' ;
            }
            
            if ( $show_submit_button ) {
                $form['show_steps'] = true;
                
                if ( $hidden && (empty($form['submit_button_field']) || isset( $form['approval'] )) ) {
                    ?>
					<?php 
                    $this->render_submit_button( $form, $current_step );
                    ?>
				<?php 
                }
            
            }
            
            /**
             *  Fires after fields have been rendered.
             *
             *  @date	12/02/2014
             *  @since	5.0.0
             *
             * @param	array $fields An array of fields.
             * @param	array $form An array of all of the form data.
             */
            do_action( FEA_PREFIX . '/render_fields', $fields, $form );
            return $form;
        }
        
        function get_field_value( $id, $type, $field )
        {
            if ( $type == 'woo_product' ) {
                $type = 'post';
            }
            
            if ( $type == 'post' || !is_numeric( $id ) ) {
                $object_id = $id;
            } else {
                $object_id = $type . '_' . $id;
            }
            
            // Allow filter to short-circuit load_value logic.
            $value = apply_filters(
                'acf/pre_load_value',
                null,
                $object_id,
                $field
            );
            if ( $value !== null ) {
                return $value;
            }
            // Get field name.
            $field_name = $field['name'];
            // Check store.
            $store = acf_get_store( 'values' );
            if ( $store->has( "{$object_id}:{$field_name}" ) ) {
                return $store->get( "{$object_id}:{$field_name}" );
            }
            // Load value from database.
            // todo: investigate this line causing new post forms
            $null = apply_filters(
                'acf/pre_load_metadata',
                null,
                $object_id,
                $field_name,
                false
            );
            if ( $null !== null ) {
                return ( $null === '__return_null' ? null : $null );
            }
            
            if ( $object_id === 'options' ) {
                $value = get_option( 'options_' . $field_name, null );
            } else {
                
                if ( is_numeric( $id ) ) {
                    $meta = get_metadata(
                        $type,
                        $id,
                        $field_name,
                        false
                    );
                    $value = ( isset( $meta[0] ) ? $meta[0] : null );
                }
            
            }
            
            // Use field's default_value if no meta was found.
            if ( $value === null && isset( $field['default_value'] ) ) {
                if ( empty($field['frontend_admin_display_mode']) || $field['frontend_admin_display_mode'] != 'hidden' ) {
                    $value = $field['default_value'];
                }
            }
            /**
             * Filters the $value after it has been loaded.
             *
             * @date    28/09/13
             * @since   5.0.0
             *
             * @param   mixed $value The value to preview.
             * @param   string $object_id The post ID for this value.
             * @param   array $field The field array.
             */
            $value = apply_filters(
                'acf/load_value',
                $value,
                $object_id,
                $field
            );
            // Update store.
            $store->set( "{$object_id}:{$field_name}", $value );
            // Return value.
            return $value;
        }
        
        public function saved_drafts( $args )
        {
            $element_id = $args['hidden_fields']['element_id'];
            $drafts_args = array(
                'posts_per_page' => -1,
                'post_status'    => 'draft',
                'post_type'      => $args['new_post_type'],
                'author'         => get_current_user_id(),
            );
            $form_submits = get_posts( $drafts_args );
            if ( !$form_submits ) {
                return;
            }
            ?>
			<div class="frontend-form-posts"><p class="drafts-heading"><?php 
            echo  $args['saved_drafts']['saved_drafts_label'] ;
            ?></p>
			
			<?php 
            $draft_choices = [
                'add_post' => $args['saved_drafts']['saved_drafts_new'],
            ];
            
            if ( feadmin_edit_mode() ) {
                for ( $x = 1 ;  $x < 4 ;  $x++ ) {
                    $draft_choices[$x] = 'Draft ' . $x . ' (' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . ')';
                }
                $select_class = 'preview-form-drafts';
            } else {
                foreach ( $form_submits as $submit ) {
                    $post_time = get_the_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $submit->ID );
                    $draft_choices[$submit->ID] = $submit->post_title . ' (' . $post_time . ')';
                }
                $select_class = 'posts-select';
            }
            
            acf_select_input( array(
                'choices' => $draft_choices,
                'class'   => $select_class,
                'value'   => $args['post_id'],
            ) );
            ?>
			</div>
			<?php 
        }
        
        public function saved_revisions( $args )
        {
            $element_id = $args['hidden_fields']['element_id'];
            
            if ( get_post_type( $args['post_id'] ) == 'revision' ) {
                $parent_post = wp_get_post_parent_id( $args['post_id'] );
            } else {
                $parent_post = $args['post_id'];
            }
            
            $form_submits = wp_get_post_revisions( $parent_post );
            if ( !$form_submits ) {
                return;
            }
            ?>
			<br><div class="frontend-form-posts"><p class="revisions-heading"><?php 
            echo  $args['saved_revisions']['saved_revisions_label'] ;
            ?></p>
			
			<?php 
            $revision_choices = [
                $parent_post => $args['saved_revisions']['saved_revisions_edit_main'],
            ];
            
            if ( feadmin_edit_mode() ) {
                for ( $x = 1 ;  $x < 4 ;  $x++ ) {
                    $revision_choices[$x] = 'Revision ' . $x . ' (' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) . ')';
                }
                $select_class = 'preview-form-revisions';
            } else {
                $first = true;
                
                if ( is_array( $form_submits ) && count( $form_submits ) > 1 ) {
                    foreach ( $form_submits as $index => $submit ) {
                        
                        if ( $first ) {
                            $first = false;
                            continue;
                        }
                        
                        $post_time = get_the_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $index );
                        $revision_choices[$index] = $submit->post_title . ' (' . $post_time . ')';
                    }
                    $select_class = 'posts-select';
                }
            
            }
            
            acf_select_input( array(
                'choices' => $revision_choices,
                'class'   => $select_class,
                'value'   => $args['post_id'],
            ) );
            ?>
			</div>
			<?php 
        }
        
        public function get_record( $form )
        {
            if ( empty($form['id']) || !isset( $_COOKIE[$form['id']] ) ) {
                return $form;
            }
            $record = fea_instance()->submissions_handler->get_submission( $_COOKIE[$form['id']] );
            if ( empty($record->id) ) {
                return $form;
            }
            
            if ( $record->status == 'in_progress' ) {
                $form = json_decode( acf_decrypt( $record->fields ), true );
                $form['submission'] = $record->id;
                return $form;
            }
            
            return $form;
        }
        
        public function get_form_structure( $form )
        {
            if ( empty($form['fields_selection']) ) {
                return $form;
            }
            $wg_id = $form['id'];
            
            if ( !empty($form['multi']) ) {
                $form['multi'] = true;
            } else {
                $form['multi'] = false;
            }
            
            
            if ( $form['multi'] ) {
                $current_step = 1;
                $form['steps'][$current_step] = $form['first_step'][0];
                $form['steps'][$current_step]['fields'] = array();
            } else {
                $form['fields'] = array();
            }
            
            if ( isset( $form['fields_selection'] ) ) {
                foreach ( $form['fields_selection'] as $ind => $form_field ) {
                    $local_field = $acf_field_groups = $acf_fields = $fields = array();
                    
                    if ( $form['multi'] ) {
                        $fields = $form['steps'][$current_step]['fields'];
                    } else {
                        $fields = $form['fields'];
                    }
                    
                    switch ( $form_field['field_type'] ) {
                        case 'ACF_field_groups':
                            
                            if ( $form_field['dynamic_acf_fields'] ) {
                                $filters = $this->get_field_group_filters( $form );
                                $acf_field_groups = feadmin_get_acf_field_choices( $filters, 'key' );
                            } elseif ( $form_field['field_groups_select'] ) {
                                $acf_field_groups = feadmin_get_acf_field_choices( array(
                                    'groups' => $form_field['field_groups_select'],
                                ), 'key' );
                            }
                            
                            
                            if ( $acf_field_groups ) {
                                $fields_exclude = $form_field['fields_select_exclude'];
                                
                                if ( $fields_exclude ) {
                                    $acf_fields = array_diff( $acf_field_groups, $fields_exclude );
                                } else {
                                    $acf_fields = $acf_field_groups;
                                }
                            
                            }
                            
                            break;
                        case 'ACF_fields':
                            $acf_fields = $form_field['fields_select'];
                            break;
                        case 'step':
                            
                            if ( !$form['multi'] ) {
                                
                                if ( feadmin_edit_mode() && $current_step == 0 ) {
                                    echo  '<div class="acf-notice -error acf-error-message"><p>' . __( 'Note: You must turn on "Multi Step" for your steps to work.', FEA_NS ) . '</p></div>' ;
                                    $current_step++;
                                }
                            
                            } else {
                                $current_step++;
                                $form['steps'][$current_step] = $form_field;
                                $fields = [];
                            }
                            
                            break;
                        case 'column':
                            
                            if ( $form_field['endpoint'] == 'true' ) {
                                $fields[] = [
                                    'column' => 'endpoint',
                                ];
                            } else {
                                $column = [
                                    'column' => $form_field['_id'],
                                ];
                                if ( $form_field['nested'] ) {
                                    $column['nested'] = true;
                                }
                                $fields[] = $column;
                            }
                            
                            break;
                        case 'tab':
                            
                            if ( $form_field['endpoint'] == 'true' ) {
                                $fields[] = [
                                    'tab' => 'endpoint',
                                ];
                            } else {
                                $tab = [
                                    'tab' => $form_field['_id'],
                                ];
                                $fields[] = $tab;
                            }
                            
                            break;
                        case 'recaptcha':
                            $local_field = array(
                                'key'          => $wg_id . '_' . $form_field['field_type'] . '_' . $form_field['_id'],
                                'type'         => 'recaptcha',
                                'wrapper'      => [
                                'class' => '',
                                'id'    => '',
                                'width' => '',
                            ],
                                'required'     => 0,
                                'version'      => $form_field['recaptcha_version'],
                                'v2_theme'     => $form_field['recaptcha_theme'],
                                'v2_size'      => $form_field['recaptcha_size'],
                                'site_key'     => $form_field['recaptcha_site_key'],
                                'secret_key'   => $form_field['recaptcha_secret_key'],
                                'disabled'     => 0,
                                'readonly'     => 0,
                                'v3_hide_logo' => $form_field['recaptcha_hide_logo'],
                            );
                            break;
                        default:
                            if ( isset( $form_field['__dynamic__'] ) ) {
                                $form_field = $this->parse_tags( $form_field );
                            }
                            $default_value = $form_field['field_default_value'];
                            $local_field = array(
                                'label'         => '',
                                'wrapper'       => [
                                'class' => '',
                                'id'    => '',
                                'width' => '',
                            ],
                                'instructions'  => $form_field['field_instruction'],
                                'required'      => ( $form_field['field_required'] ? 1 : 0 ),
                                'placeholder'   => $form_field['field_placeholder'],
                                'default_value' => $default_value,
                                'disabled'      => $form_field['field_disabled'],
                                'readonly'      => $form_field['field_readonly'],
                                'min'           => $form_field['minimum'],
                                'max'           => $form_field['maximum'],
                                'prepend'       => $form_field['prepend'],
                                'append'        => $form_field['append'],
                            );
                            
                            if ( isset( $data_default ) ) {
                                $local_field['wrapper']['data-default'] = $data_default;
                                $local_field['wrapper']['data-dynamic_value'] = $default_value;
                            }
                            
                            if ( $form_field['field_hidden'] ) {
                                $local_field['frontend_admin_display_mode'] = 'hidden';
                            }
                            
                            if ( $form_field['field_type'] == 'message' ) {
                                $local_field['type'] = 'message';
                                $local_field['message'] = $form_field['field_message'];
                                $local_field['name'] = $local_field['key'] = $wg_id . '_' . $form_field['_id'];
                            }
                            
                            break;
                    }
                    if ( $acf_fields ) {
                        $local_field = array(
                            'key'           => $wg_id . '_' . $form_field['field_type'] . '_' . $form_field['_id'],
                            'name'          => $wg_id . '_' . $form_field['field_type'] . '_' . $form_field['_id'],
                            'type'          => 'fields_select',
                            'fields_select' => $acf_fields,
                            'fields_class'  => 'elementor-repeater-item-' . $form_field['_id'],
                            'wrapper'       => array(
                            'class' => '',
                        ),
                        );
                    }
                    
                    if ( isset( $local_field ) ) {
                        $sub_fields = false;
                        
                        if ( $form_field['field_type'] == 'attributes' ) {
                            $sub_fields = $form['attribute_fields'];
                            unset( $form['attribute_fields'] );
                        }
                        
                        
                        if ( $form_field['field_type'] == 'variations' ) {
                            $sub_fields = $form['variable_fields'];
                            unset( $form['variable_fields'] );
                        }
                        
                        foreach ( feadmin_get_field_type_groups() as $name => $group ) {
                            
                            if ( in_array( $form_field['field_type'], array_keys( $group['options'] ) ) ) {
                                $action_name = explode( '_', $name )[0];
                                
                                if ( isset( fea_instance()->local_actions[$action_name] ) ) {
                                    $action = fea_instance()->local_actions[$action_name];
                                    $local_field = $action->get_fields_display(
                                        $form_field,
                                        $local_field,
                                        $wg_id,
                                        $sub_fields
                                    );
                                    
                                    if ( isset( $form_field['field_label_on'] ) ) {
                                        $field_label = ucwords( str_replace( '_', ' ', $form_field['field_type'] ) );
                                        $local_field['label'] = ( $form_field['field_label'] ? $form_field['field_label'] : $field_label );
                                    }
                                    
                                    
                                    if ( isset( $local_field['type'] ) ) {
                                        
                                        if ( $local_field['type'] == 'number' ) {
                                            $local_field['placeholder'] = $form_field['number_placeholder'];
                                            $local_field['default_value'] = $form_field['number_default_value'];
                                        }
                                        
                                        
                                        if ( $form_field['field_type'] == 'taxonomy' ) {
                                            $taxonomy = ( isset( $form_field['field_taxonomy'] ) ? $form_field['field_taxonomy'] : 'category' );
                                            $local_field['name'] = $wg_id . '_' . $taxonomy;
                                            $local_field['key'] = $wg_id . '_' . $taxonomy;
                                        } else {
                                            $local_field['name'] = $wg_id . '_' . $form_field['field_type'];
                                            $local_field['key'] = $wg_id . '_' . $form_field['field_type'];
                                        }
                                    
                                    }
                                    
                                    if ( !empty($form_field['default_terms']) ) {
                                        $local_field['default_terms'] = $form_field['default_terms'];
                                    }
                                }
                                
                                break;
                            }
                        
                        }
                    }
                    
                    if ( isset( $local_field['label'] ) ) {
                        
                        if ( !$form_field['field_label_on'] ) {
                            $local_field['field_label_hide'] = 1;
                        } else {
                            $local_field['field_label_hide'] = 0;
                        }
                    
                    }
                    if ( isset( $form_field['button_text'] ) && $form_field['button_text'] ) {
                        $local_field['button_text'] = $form_field['button_text'];
                    }
                    
                    if ( isset( $local_field['key'] ) ) {
                        $field_key = '';
                        $local_field['wrapper']['class'] .= ' elementor-repeater-item-' . $form_field['_id'];
                        
                        if ( feadmin_edit_mode() && $local_field['type'] != 'fields_select' ) {
                            acf_add_local_field( $local_field );
                            $field_key = $local_field['key'];
                            $fields[$field_key] = $local_field;
                        } else {
                            $local_field['key'] = 'field_' . $wg_id . $form_field['_id'];
                            $field_obj = acf_get_field( $local_field['key'] );
                            if ( $field_obj ) {
                                //print("<pre>".print_r($field_obj,true)."</pre>");
                                $local_field = array_merge( $field_obj, $local_field );
                            }
                            acf_update_field( $local_field );
                            $field_obj = acf_get_field( $local_field['key'] );
                            // todo: remove after 3.5. Put in place to fix bug
                            $field_key = $local_field['key'];
                            $fields[$field_key] = $field_obj;
                        }
                    
                    }
                    
                    
                    if ( $form['multi'] ) {
                        $form['steps'][$current_step]['fields'] = $fields;
                    } else {
                        $form['fields'] = $fields;
                    }
                
                }
            }
            unset( $form['fields_selection'] );
            unset( $form['first_step'] );
            return $form;
        }
        
        public function parse_tags( $settings )
        {
            $dynamic_tags = $settings['__dynamic__'];
            foreach ( $dynamic_tags as $control_name => $tag ) {
                $settings[$control_name] = $tag;
            }
            return $settings;
        }
        
        public function get_field_group_filters( $form )
        {
            $filters = array(
                'post_id' => $form['post_id'],
            );
            
            if ( $form['save_to_post'] == 'new_post' ) {
                $filters = array(
                    'post_type' => $form['new_post_type'],
                );
            } else {
                $filters = array(
                    'post_id' => $form['post_id'],
                );
            }
            
            return $filters;
        }
        
        public function delete_record( $form )
        {
            
            if ( !empty($form['id']) && isset( $_COOKIE[$form['id']] ) ) {
                $expiration_time = time();
                setcookie(
                    $form['id'],
                    '0',
                    $expiration_time,
                    '/'
                );
            }
        
        }
        
        public function show_messages( $form )
        {
        }
        
        public function render_submissions( $form, $preview = false )
        {
            $editor = feadmin_edit_mode();
            $form = $this->validate_form( $form );
            $form = wp_parse_args( $form, array(
                'submissions_per_page' => 10,
                'total_submissions'    => '',
            ) );
            $form = apply_filters( FEA_PREFIX . '/show_submissions', $form );
            
            if ( empty($form['display']) && !$preview ) {
                if ( !empty($form['message']) && $form['message'] !== 'NOTHING' ) {
                    echo  $form['message'] ;
                }
                return;
            }
            
            if ( isset( $form['id'] ) ) {
                $args = array(
                    'form_key' => $form['id'],
                );
            }
            if ( isset( $form['ID'] ) ) {
                $args = array(
                    'form_id' => $form['ID'],
                );
            }
            $total_submits = $submissions = fea_instance()->submissions_handler->record_count( $args );
            
            if ( $form['submissions_per_page'] ) {
                $args['per_page'] = $form['submissions_per_page'];
            } else {
                $args['per_page'] = 10;
            }
            
            
            if ( $form['total_submissions'] && $total_submits > $form['total_submissions'] ) {
                $total_submits = $form['total_submissions'];
                
                if ( !empty($_REQUEST['item_count']) ) {
                    $item_count = (int) $_REQUEST['item_count'];
                    if ( $item_count + $args['per_page'] > $total_submits ) {
                        $args['per_page'] = $total_submits - $item_count;
                    }
                }
            
            }
            
            $submissions = fea_instance()->submissions_handler->get_submissions( $args );
            
            if ( !$submissions ) {
                if ( !empty($form['no_submissions_message']) ) {
                    echo  '<div class="acf-notice -error acf-error-message -dismiss"><p>' . $form['no_submissions_message'] . '</p></div>' ;
                }
                return;
            }
            
            $rows = array();
            if ( empty($_REQUEST['load_more']) ) {
                ?>
				<div class="fea-list-container">
			<?php 
            }
            foreach ( $submissions as $submission ) {
                $this->render_submission_item( $submission );
            }
            $count = count( $submissions );
            
            if ( $count < $total_submits && !isset( $_REQUEST['current_page'] ) ) {
                $total_pages = ceil( $total_submits / $args['per_page'] );
                ?>
					<div class="load-more-results" data-form="<?php 
                echo  $form['ID'] ;
                ?>" data-page="1" data-count="<?php 
                echo  $count ;
                ?>" data-total="<?php 
                echo  $total_pages ;
                ?>"><span class="acf-loading acf-hidden"></span></div>
				<?php 
            }
            
            if ( empty($_REQUEST['load_more']) ) {
                ?>
			</div>
			<?php 
            }
        }
        
        function render_submission_item( $submission )
        {
            ?>
			<div class="fea-list-item" data-id="<?php 
            echo  $submission['id'] ;
            ?>">
			<?php 
            if ( !$submission['title'] ) {
                $submission['title'] = sprintf( __( 'Submission #%d', FEA_NS ), $submission['id'] );
            }
            $submission_form = fea_instance()->submissions_handler->get_submission_form( $submission['id'], array(), 1 );
            $submission_form = array_merge( $submission_form, array(
                'show_in_modal'     => 1,
                'modal_button_text' => __( 'Review', FEA_NS ),
                'ajax_submit'       => 'submission_form',
            ) );
            echo  '<h4 class="item-title">' . $submission['title'] . '</h4>' ;
            $this->render_form( $submission_form );
            
            if ( $submission['user'] ) {
                $user = get_user_by( 'ID', $submission['user'] );
                
                if ( isset( $user->user_login ) ) {
                    $user_text = $user->user_login;
                    
                    if ( $user->display_name ) {
                        $user_text .= " ({$user->display_name})";
                    } elseif ( $user->first_name && $user->last_name ) {
                        $user_text .= " ({$user->first_name} {$user->last_name})";
                    } elseif ( $user->first_name ) {
                        $user_text .= " ({$user->first_name})";
                    }
                    
                    echo  '<p class="item-user">' . __( 'Submitted By', FEA_NS ) . ': ' . $user_text . '</p>' ;
                }
            
            }
            
            $status_label = fea_instance()->submissions_handler->get_status_label( $submission['status'] );
            echo  '<p class="item-status">' . __( 'Status', FEA_NS ) . ': ' . $status_label . '</p>' ;
            echo  '<p class="item-date">' . __( 'Date', FEA_NS ) . ': ' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $submission['created_at'] ) ) . '</p>' ;
            ?>
			</div>
			<?php 
        }
        
        public function render_form( $form, $preview = false )
        {
            $editor = feadmin_edit_mode();
            if ( isset( $_GET['submit_id'] ) ) {
                
                if ( isset( $_GET['email_address'] ) ) {
                    $address = $_GET['email_address'];
                    if ( $GLOBAL[$address . '_verified'] ) {
                        echo  '<div class="frontend-admin-message"><div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">' . __( 'Email Verified Successfully' ) . '</p><span class="frontend-admin-dismiss close-msg acf-notice-dismiss acf-icon -cancel small"></span></div></div>' ;
                    }
                } else {
                    $form = fea_instance()->submissions_handler->get_submission_form( $_GET['submit_id'] );
                }
            
            }
            $form = $this->validate_form( $form );
            
            if ( !empty($form['ID']) ) {
                global  $wp_admin_bar ;
                
                if ( $wp_admin_bar && current_user_can( 'manage_options' ) ) {
                    $args = array(
                        'id'     => FEA_PRE . '-form-' . $form['ID'],
                        'parent' => FEA_PRE,
                        'title'  => sprintf( __( 'Edit %s', FEA_NS ), $form['title'] ),
                        'href'   => get_edit_post_link( $form['ID'] ),
                        'meta'   => array(
                        'class' => 'fea-edit-form',
                    ),
                    );
                    $wp_admin_bar->add_node( $args );
                }
                
                $form['id'] = $form['ID'];
            }
            
            
            if ( $preview ) {
                
                if ( isset( $preview['modal'] ) ) {
                    $form['show_in_modal'] = true;
                    $form['modal_button_text'] = $preview['modal'];
                }
                
                $form['preview_mode'] = true;
            }
            
            
            if ( empty($form['approval']) ) {
                $form = apply_filters( FEA_PREFIX . '/show_form', $form );
                
                if ( empty($form['display']) && !$preview ) {
                    if ( !empty($form['message']) && $form['message'] !== 'NOTHING' ) {
                        echo  $form['message'] ;
                    }
                    
                    if ( feadmin_edit_mode() ) {
                        echo  '<div class="preview-display">' ;
                    } else {
                        return;
                    }
                
                }
            
            }
            
            $this->show_messages( $form );
            fea_instance()->wp_hooks->enqueue_scripts( 'frontend_admin_form' );
            fea_instance()->wp_hooks->acfdata( true );
            
            if ( !empty($form['show_in_modal']) && empty($form['page_builder']) ) {
                $attrs = array(
                    'class'     => 'modal-button render-form',
                    'data-name' => 'admin_form',
                    'data-form' => $form['id'],
                );
                if ( isset( $form['modal_width'] ) ) {
                    $attrs['data-form_width'] = $form['modal_width'];
                }
                if ( !empty($form['hide_modal_button']) ) {
                    $attrs['class'] .= ' acf-hidden';
                }
                $button_text = $form['modal_button_text'];
                if ( !empty($form['modal_button_icon']['value']) ) {
                    $button_text .= ' <span class="' . $form['modal_button_icon']['value'] . '"></span';
                }
                echo  '<button ' . acf_esc_attr( $attrs ) . '>' . $button_text . '</button>' ;
                acf_hidden_input( array(
                    'class' => 'form-data',
                    'name'  => 'form_' . $form['id'],
                    'value' => acf_encrypt( json_encode( $form ) ),
                ) );
                return;
            }
            
            $form['form_attributes']['id'] = $form['id'];
            $GLOBALS['admin_form'] = $form;
            $form = $this->get_form_structure( $form );
            $current_step = 1;
            $form_title = $form['form_title'];
            
            if ( isset( $form['steps'] ) ) {
                $steps_count = count( $form['steps'] );
                
                if ( $steps_count > 1 ) {
                    
                    if ( isset( $form['step_index'] ) ) {
                        $current_step = $form['step_index'];
                    } else {
                        $form['step_index'] = $current_step;
                    }
                    
                    $form['hidden_fields']['step'] = $current_step;
                    if ( $current_step == $steps_count ) {
                        $form['last_step'] = true;
                    }
                    //if( $editor ){
                    $other_steps = $form['steps'];
                    unset( $other_steps[$current_step] );
                    //array_splice( $other_steps, $current_step-1, 1 );
                    /*} else{
                    			if( $current_step > 1 ) $other_steps = array_slice( $form['steps'], 0, $current_step );
                    		} */
                    $form['show_form_title'] = false;
                    $step_tabs = true;
                    $form['form_attributes']['data-steps'] = $steps_count;
                    if ( !empty($form['validate_steps']) ) {
                        $form['form_attributes']['data-validate-steps'] = 1;
                    }
                }
            
            }
            
            // Set uploader type.
            acf_update_setting( 'uploader', $form['uploader'] );
            ?>
			<form <?php 
            echo  feadmin_get_esc_attrs( $form['form_attributes'] ) ;
            ?>> 
			<?php 
            global  $frontend_admin_success_returned ;
            
            if ( isset( $frontend_admin_success_returned ) ) {
                
                if ( isset( $form['step_index'] ) && $form['step_index'] > 1 ) {
                    $no_message = true;
                } else {
                    
                    if ( empty($frontend_admin_success_returned['frontend-form-nonce']) || !wp_verify_nonce( $frontend_admin_success_returned['frontend-form-nonce'], 'frontend-form' ) ) {
                        $user_id = get_current_user_id();
                        if ( empty($frontend_admin_success_returned['message_token']) || get_user_meta( $user_id, 'message_token', true ) !== $frontend_admin_success_returned['message_token'] ) {
                            $no_message = true;
                        }
                    }
                
                }
                
                if ( empty($no_message) ) {
                    if ( isset( $frontend_admin_success_returned['success_message'] ) && $frontend_admin_success_returned['location'] == 'current' && $frontend_admin_success_returned['form_element'] == $form['id'] ) {
                        printf( $form['html_updated_message'], wp_unslash( wp_kses( $frontend_admin_success_returned['success_message'], 'post' ) ) );
                    }
                }
            }
            
            $this->form_render_data( $form );
            ob_start();
            $form = $this->render_fields( $form, $current_step );
            if ( isset( $current_step ) && isset( $other_steps ) ) {
                $this->render_other_steps( $other_steps, $form );
            }
            $output = ob_get_clean();
            
            if ( isset( $step_tabs ) ) {
                if ( !empty($form['fields']) ) {
                    $form = $this->render_fields(
                        $form,
                        false,
                        false,
                        $form['fields']
                    );
                }
                
                if ( !empty($form['show_steps']) ) {
                    $this->step_tabs( $form, $current_step );
                    unset( $form['show_steps'] );
                }
            
            }
            
            ?>
			<div class="acf-fields acf-form-fields -<?php 
            echo  esc_attr( $form['label_placement'] ) ;
            ?>">
				<?php 
            echo  $output ;
            ?>
				 
			</div>
			<?php 
            if ( isset( $step_tabs ) ) {
                echo  '</div></div>' ;
            }
            /* if( $form['save_progress'] ){ 
            				if( !empty( $form['save_progress']['text'] ) ) $form['save_progress'] = $form['save_progress']['text'];
            				$state = $form['post_id'] == 'add_post' ? 'draft' : 'revision';
            				?>
            				<div class="save-progress-buttons">
            				<input formnovalidate type="submit" class="save-progress-button acf-submit-button button" value="<?php echo $form['save_progress']; ?>" name="save_progress" data-state="<?php echo $state ?>" /></div>
            			<?php	
            			} */
            ?>
			</form>
			<?php 
            do_action( FEA_PREFIX . '/after_form', $form );
            if ( feadmin_edit_mode() ) {
                echo  '</div>' ;
            }
        }
        
        public function hidden_default_fields( $form )
        {
            $fields = array();
            
            if ( $form['honeypot'] ) {
                acf_add_local_field( array(
                    'prefix'          => 'acf',
                    'name'            => '_validate_email',
                    'key'             => '_validate_email',
                    'no_data_collect' => 1,
                    'type'            => 'text',
                    'value'           => '',
                    'no_save'         => 1,
                    'wrapper'         => array(
                    'style' => 'display:none !important',
                ),
                ) );
                $fields[] = acf_get_field( '_validate_email' );
            }
            
            $element_id = $form['id'];
            
            if ( !feadmin_edit_mode() && !empty($form['product_id']) ) {
                
                if ( empty($GLOBALS['form_fields']['product_types']) ) {
                    $field_key = $element_id . '_product_type';
                    acf_add_local_field( array(
                        'name'            => $field_key,
                        'key'             => $field_key,
                        'type'            => 'product_types',
                        'no_data_collect' => 1,
                        'wrapper'         => array(
                        'style' => 'display:none !important',
                    ),
                    ) );
                    $GLOBALS['form_fields']['product_types'] = $field_key;
                    $fields[] = acf_get_field( $field_key );
                } else {
                    acf_hidden_input( array(
                        'name'  => 'acff[woo_product][types]',
                        'value' => $GLOBALS['form_fields']['product_types'],
                    ) );
                }
                
                
                if ( empty($GLOBALS['form_fields']['manage_stock']) ) {
                    $field_key = $element_id . '_manage_stock';
                    acf_add_local_field( array(
                        'name'            => $field_key,
                        'key'             => $field_key,
                        'type'            => 'manage_stock',
                        'no_data_collect' => 1,
                        'ui'              => 0,
                        'wrapper'         => array(
                        'style' => 'display:none !important',
                    ),
                    ) );
                    $GLOBALS['form_fields']['manage_stock'] = $field_key;
                    $fields[] = acf_get_field( $field_key );
                }
            
            }
            
            return $fields;
        }
        
        public function ajax_get_submissions()
        {
            if ( empty($_REQUEST['form_id']) ) {
                wp_send_json_error();
            }
            $form = $this->get_form( $_REQUEST['form_id'] );
            
            if ( $form ) {
                $this->render_submissions( $form );
                die;
            }
            
            wp_send_json_error( __( 'No Submissions Found', FEA_NS ) );
        }
        
        public function change_form()
        {
            if ( empty($_REQUEST['form_data']) ) {
                wp_send_json_error();
            }
            $form = json_decode( acf_decrypt( $_REQUEST['form_data'] ), true );
            if ( !$form ) {
                wp_send_json_error();
            }
            
            if ( isset( $_REQUEST['item_id'] ) ) {
                $type = $_REQUEST['type'];
                $form[$type . '_id'] = $_REQUEST['item_id'];
                if ( $form[$type . '_id'] ) {
                    
                    if ( is_numeric( $form[$type . '_id'] ) ) {
                        $form['save_to_' . $type] = 'edit_' . $type;
                    } else {
                        $form['save_to_' . $type] = 'new_' . $type;
                    }
                
                }
            } else {
                
                if ( isset( $_REQUEST['step'] ) ) {
                    $form['step_index'] = $_REQUEST['step'];
                } else {
                    $form['step_index'] = $form['step_index'] - 1;
                }
                
                
                if ( $form['step_index'] == count( $form['steps'] ) ) {
                    $form['last_step'] = true;
                } else {
                    if ( isset( $form['last_step'] ) ) {
                        unset( $form['last_step'] );
                    }
                }
            
            }
            
            $GLOBALS['admin_form'] = $form;
            ob_start();
            //$form['no_cookies'] = 1;
            $this->render_form( $form );
            $reload_form = ob_get_contents();
            ob_end_clean();
            wp_send_json_success( [
                'reload_form' => $reload_form,
                'to_top'      => true,
            ] );
            die;
        }
        
        public function get_steps( $field )
        {
            if ( $field['field_type'] == 'step' ) {
                return true;
            }
            return false;
        }
        
        public function ajax_add_form()
        {
            // vars
            $args = wp_parse_args( $_POST, array(
                'nonce'       => '',
                'field_key'   => '',
                'parent_form' => '',
                'form_action' => '',
                'form_args'   => '',
                'data_type'   => 'post',
            ) );
            // verify nonce
            if ( !acf_verify_ajax() ) {
                die;
            }
            
            if ( $args['form_action'] == 'admin_form' ) {
                
                if ( !empty($args['form']) ) {
                    $form = json_decode( acf_decrypt( $args['form'] ), true );
                } else {
                    $form = array(
                        'post_id'            => 'add_post',
                        'save_to_post'       => 'new_post',
                        'custom_fields_save' => 'post',
                        'new_post_type'      => 'admin_form',
                        'new_post_status'    => 'draft',
                        'fields'             => array(
                        'admin_form_types'     => 'admin_form_types',
                        'frontend_admin_title' => 'frontend_admin_title',
                    ),
                        'return'             => admin_url( 'post.php?post=%post_id%&action=edit' ),
                        'honeypot'           => false,
                        'no_record'          => 1,
                        'submit_value'       => __( 'Create New Form', FEA_NS ),
                    );
                }
                
                unset( $form['show_in_modal'] );
                $this->render_form( $form );
                die;
            }
            
            // load field
            $field = acf_get_field( $args['field_key'] );
            if ( !$field ) {
                die;
            }
            $edit_post = is_numeric( $args['form_action'] );
            $hidden_fields = [
                'field_id' => $args['field_key'],
            ];
            $form_id = $args['field_key'];
            $type = $args['data_type'];
            $form_args = array(
                $type . '_id'        => $args['form_action'],
                'post_fields'        => [
                'post_status' => 'publish',
            ],
                'id'                 => $form_id,
                'form_attributes'    => array(
                'data-field' => $args['field_key'],
            ),
                'ajax_submit'        => true,
                'hidden_fields'      => $hidden_fields,
                'redirect_action'    => 'clear_form',
                'return'             => '',
                'parent_form'        => $args['parent_form'],
                'new_post_status'    => 'publish',
                'save_to_' . $type   => ( $edit_post ? 'edit_' . $type : 'new_' . $type ),
                'custom_fields_save' => $type,
            );
            
            if ( $type == 'post' ) {
                $form_args['fields'] = array( 'frontend_admin_title' );
            } else {
                $form_args['fields'] = array( 'frontend_admin_term_name' );
            }
            
            
            if ( !empty($field['post_form_template']) ) {
                
                if ( is_array( $field['post_form_template'] ) ) {
                    $form_args['fields_to_display'] = $field['post_form_template'];
                } else {
                    $form_args['fields_to_display'] = array( $field['post_form_template'] );
                }
                
                
                if ( in_array( 'current', $form_args['fields_to_display'] ) ) {
                    $pos = array_search( 'current', $form_args['fields_to_display'] );
                    $form_args['fields_to_display'][$pos] = $field['parent'];
                }
            
            } else {
                
                if ( is_numeric( $args['form_action'] ) ) {
                    $form_args['update_message'] = __( 'Post Updated Successfully!', FEA_NS );
                    $form_args['submit_value'] = __( 'Update', FEA_NS );
                } else {
                    $form_args['update_message'] = __( 'Post Added Successfully!', FEA_NS );
                    $form_args['submit_value'] = __( 'Publish', FEA_NS );
                    $form_args['post_fields'] = [
                        'post_status' => 'publish',
                    ];
                }
            
            }
            
            $all_post_types = acf_get_pretty_post_types();
            if ( $args['form_action'] == 'add_item' ) {
                
                if ( $type == 'post' ) {
                    
                    if ( empty($field['post_type']) ) {
                        $form_args['new_post_type'] = 'post';
                        $post_type_choices = $all_post_types;
                    } elseif ( count( $field['post_type'] ) > 1 ) {
                        $form_args['new_post_type'] = $field['post_type'][0];
                        $post_type_choices = [];
                        foreach ( $field['post_type'] as $post_type ) {
                            $post_type_choices[$post_type] = $all_post_types[$post_type];
                        }
                    } else {
                        $form_args['new_post_type'] = $field['post_type'][0];
                    }
                    
                    
                    if ( !empty($post_type_choices) ) {
                        acf_add_local_field( array(
                            'key'           => 'frontend_admin_post_type',
                            'label'         => __( 'Post Type', FEA_NS ),
                            'default_value' => current( $post_type_choices ),
                            'name'          => 'frontend_admin_post_type',
                            'type'          => 'post_type',
                            'layout'        => 'vertical',
                            'choices'       => $post_type_choices,
                        ) );
                        $form_args['fields'][] = 'frontend_admin_post_type';
                    }
                
                }
            
            }
            $this->render_form( $form_args );
            die;
        }
        
        /**
         * Registers the shortcode advanced_form which renders the form specified by the "form" attribute
         *
         * @since 1.0.0
         *
         */
        public function form_shortcode( $atts )
        {
            
            if ( isset( $atts['form'] ) ) {
                $form_id = $atts['form'];
                unset( $atts['form'] );
                ob_start();
                $this->render_form( $form_id );
                $output = ob_get_clean();
                return $output;
            }
            
            
            if ( isset( $atts['submissions'] ) ) {
                $form_id = $atts['submissions'];
                unset( $atts['submissions'] );
                ob_start();
                $this->render_submissions( $form_id );
                $output = ob_get_clean();
                return $output;
            }
        
        }
        
        function success_message_cookie()
        {
            
            if ( isset( $_COOKIE['admin_form_success'] ) ) {
                global  $frontend_admin_success_returned ;
                $frontend_admin_success_returned = json_decode( stripslashes( $_COOKIE['admin_form_success'] ), true );
                
                if ( isset( $frontend_admin_success_returned['used'] ) ) {
                    $expiration_time = time() - 600;
                    setcookie(
                        'admin_form_success',
                        '',
                        $expiration_time,
                        '/'
                    );
                } else {
                    $frontend_admin_success_returned['used'] = 1;
                    $expiration_time = time() + 600;
                    setcookie(
                        'admin_form_success',
                        json_encode( $frontend_admin_success_returned ),
                        $expiration_time,
                        '/'
                    );
                }
            
            }
        
        }
        
        public function __construct()
        {
            add_shortcode( 'frontend_admin', array( $this, 'form_shortcode' ) );
            add_shortcode( 'acf_frontend', array( $this, 'form_shortcode' ) );
            add_action( 'init', array( $this, 'success_message_cookie' ) );
            add_action( 'wp_ajax_frontend_admin/forms/get_submissions', array( $this, 'ajax_get_submissions' ) );
            add_action( 'wp_ajax_frontend_admin/forms/change_form', array( $this, 'change_form' ) );
            add_action( 'wp_ajax_nopriv_frontend_admin/forms/change_form', array( $this, 'change_form' ) );
            add_action( 'wp_ajax_frontend_admin/forms/add_form', array( $this, 'ajax_add_form' ) );
            add_action( 'wp_ajax_nopriv_frontend_admin/forms/add_form', array( $this, 'ajax_add_form' ) );
        }
    
    }
    fea_instance()->form_display = new Display_Form();
}
