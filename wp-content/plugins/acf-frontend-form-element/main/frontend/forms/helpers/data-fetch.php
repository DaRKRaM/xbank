<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

function feadmin_form_types()
{
    $form_types = array(
        'general'            => __( 'Frontend Form', FEA_NS ),
        __( 'Post', FEA_NS ) => array(
        'new_post'       => __( 'New Post Form', FEA_NS ),
        'edit_post'      => __( 'Edit Post Form', FEA_NS ),
        'duplicate_post' => __( 'Duplicate Post Form', FEA_NS ),
        'delete_post'    => __( 'Delete Post Button', FEA_NS ),
        'status_post'    => __( 'Post Status Button', FEA_NS ),
    ),
        __( 'User', FEA_NS ) => array(
        'new_user'    => __( 'New User Form', FEA_NS ),
        'edit_user'   => __( 'Edit User Form', FEA_NS ),
        'delete_user' => __( 'Delete User Button', FEA_NS ),
    ),
        __( 'Term', FEA_NS ) => array(
        'new_term'    => __( 'New Term Form', FEA_NS ),
        'edit_term'   => __( 'Edit Term Form', FEA_NS ),
        'delete_term' => __( 'Delete Term Button', FEA_NS ),
    ),
    );
    return $form_types;
}

function feadmin_user_exists( $id )
{
    global  $wpdb ;
    $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->users} WHERE ID = %d", $id ) );
    if ( $count == 1 ) {
        return true;
    }
    return false;
}

function feadmin_get_field_data( $type = null, $form_fields = false )
{
    $field_types = [];
    if ( !$form_fields ) {
        $GLOBALS['only_acf_field_groups'] = 1;
    }
    $acf_field_groups = acf_get_field_groups();
    $GLOBALS['only_acf_field_groups'] = 0;
    // bail early if no field groups
    if ( empty($acf_field_groups) ) {
        die;
    }
    // loop through array and add to field 'choices'
    if ( $acf_field_groups ) {
        foreach ( $acf_field_groups as $field_group ) {
            if ( !empty($field_group['frontend_admin_group']) ) {
                continue;
            }
            $field_group_fields = acf_get_fields( $field_group['key'] );
            if ( is_array( $field_group_fields ) ) {
                foreach ( $field_group_fields as $acf_field ) {
                    
                    if ( $type ) {
                        if ( is_array( $type ) && in_array( $acf_field['type'], $type ) || !is_array( $type ) && $acf_field['type'] == $type ) {
                            $field_types[$acf_field['key']] = $acf_field['label'];
                        }
                    } else {
                        $field_types[$acf_field['key']]['type'] = $acf_field['type'];
                        $field_types[$acf_field['key']]['label'] = $acf_field['label'];
                        $field_types[$acf_field['key']]['name'] = $acf_field['name'];
                    }
                
                }
            }
        }
    }
    return $field_types;
}

function feadmin_user_id_fields()
{
    $fields = feadmin_get_acf_field_choices( array(
        'type' => 'user',
    ) );
    $keys = array_merge( [
        '[author]' => __( 'Post Author', FEA_NS ),
    ], $fields );
    return $keys;
}

function feadmin_get_user_roles( $exceptions = array(), $all = false )
{
    if ( !current_user_can( 'administrator' ) ) {
        $exceptions[] = 'administrator';
    }
    $user_roles = array();
    if ( $all ) {
        $user_roles['all'] = __( 'All', FEA_NS );
    }
    global  $wp_roles ;
    // loop through array and add to field 'choices'
    foreach ( $wp_roles->roles as $role => $settings ) {
        if ( !in_array( strtolower( $role ), $exceptions ) ) {
            $user_roles[$role] = $settings['name'];
        }
    }
    return $user_roles;
}

function feadmin_get_user_caps( $exceptions = array(), $all = false )
{
    $user_caps = array();
    $data = get_userdata( get_current_user_id() );
    
    if ( is_object( $data ) ) {
        $current_user_caps = $data->allcaps;
        foreach ( $current_user_caps as $cap => $true ) {
            if ( !in_array( strtolower( $cap ), $exceptions ) ) {
                $user_caps[$cap] = $cap;
            }
        }
    }
    
    return $user_caps;
}

function feadmin_get_acf_group_choices()
{
    $field_group_choices = [];
    $acf_field_groups = acf_get_field_groups();
    // loop through array and add to field 'choices'
    if ( is_array( $acf_field_groups ) ) {
        foreach ( $acf_field_groups as $field_group ) {
            if ( is_array( $field_group ) && !isset( $field_group['frontend_admin_group'] ) ) {
                $field_group_choices[$field_group['key']] = $field_group['title'];
            }
        }
    }
    return $field_group_choices;
}

/* add_filter('acf/get_fields', function( $fields, $parent ){
	$group = explode( 'acfef_', $parent['key'] ); 

	if( empty( $group[1] ) ) return $fields;

	return array();
}, 5, 2);
 */
function feadmin_get_acf_field_choices( $filter = array(), $return = 'label' )
{
    $all_fields = [];
    
    if ( isset( $filter['groups'] ) ) {
        $acf_field_groups = $filter['groups'];
    } else {
        $acf_field_groups = acf_get_field_groups( $filter );
    }
    
    // bail early if no field groups
    if ( empty($acf_field_groups) ) {
        return array();
    }
    foreach ( $acf_field_groups as $group ) {
        if ( !is_array( $group ) ) {
            $group = acf_get_field_group( $group );
        }
        if ( !empty($field_group['frontend_admin_group']) ) {
            continue;
        }
        $group_fields = acf_get_fields( $group );
        if ( is_array( $group_fields ) ) {
            foreach ( $group_fields as $acf_field ) {
                if ( !is_array( $acf_field ) ) {
                    continue;
                }
                $acf_field_key = ( isset( $acf_field['_clone'] ) ? $acf_field['__key'] : $acf_field['key'] );
                
                if ( !empty($filter['type']) && $filter['type'] == $acf_field['type'] ) {
                    $all_fields[$acf_field['name']] = $acf_field[$return];
                } else {
                    
                    if ( isset( $filter['groups'] ) ) {
                        $all_fields[$acf_field_key] = $acf_field[$return];
                    } else {
                        $all_fields[$acf_field_key] = $acf_field[$return];
                    }
                
                }
            
            }
        }
    }
    return $all_fields;
}

function feadmin_get_post_type_choices()
{
    $post_type_choices = [];
    $args = array();
    $output = 'names';
    // names or objects, note names is the default
    $operator = 'and';
    // 'and' or 'or'
    $post_types = get_post_types( $args, $output, $operator );
    // loop through array and add to field 'choices'
    if ( is_array( $post_types ) ) {
        foreach ( $post_types as $post_type ) {
            $post_type_choices[$post_type] = str_replace( '_', ' ', ucfirst( $post_type ) );
        }
    }
    return $post_type_choices;
}

function feadmin_get_random_string( $length = 15 )
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen( $characters );
    $randomString = '';
    for ( $i = 0 ;  $i < $length ;  $i++ ) {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }
    return $randomString;
}

function feadmin_get_client_ip()
{
    $server_ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    foreach ( $server_ip_keys as $key ) {
        if ( isset( $_SERVER[$key] ) && filter_var( $_SERVER[$key], FILTER_VALIDATE_IP ) ) {
            return $_SERVER[$key];
        }
    }
    // Fallback local ip.
    return '127.0.0.1';
}

function feadmin_get_site_domain()
{
    return str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
}

function feadmin_get_esc_attrs( $attrs )
{
    $html = '';
    // Loop over attrs and validate data types.
    foreach ( $attrs as $k => $v ) {
        // String (but don't trim value).
        
        if ( is_string( $v ) && $k !== 'value' ) {
            $v = trim( $v );
            // Boolean
        } elseif ( is_bool( $v ) ) {
            $v = ( $v ? 1 : 0 );
            // Object
        } elseif ( is_array( $v ) || is_object( $v ) ) {
            $v = json_encode( $v );
        }
        
        // Generate HTML.
        $html .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
    }
    // Return trimmed.
    return trim( $html );
}

function feadmin_duplicate_slug( $prefix = '' )
{
    static  $i ;
    
    if ( null === $i ) {
        $i = 2;
    } else {
        $i++;
    }
    
    $new_slug = sprintf( '%s_copy%s', $prefix, $i );
    
    if ( !feadmin_slug_exists( $new_slug ) ) {
        return $new_slug;
    } else {
        return feadmin_duplicate_slug( $prefix );
    }

}

function feadmin_slug_exists( $post_name )
{
    global  $wpdb ;
    
    if ( $wpdb->get_row( "SELECT post_name FROM {$wpdb->posts} WHERE post_name = '{$post_name}'", 'ARRAY_A' ) ) {
        return true;
    } else {
        return false;
    }

}

function feadmin_parse_args( $args, $defaults )
{
    $new_args = (array) $defaults;
    foreach ( $args as $key => $value ) {
        
        if ( is_array( $value ) && isset( $new_args[$key] ) ) {
            $new_args[$key] = feadmin_parse_args( $value, $new_args[$key] );
        } else {
            $new_args[$key] = $value;
        }
    
    }
    return $new_args;
}

function feadmin_edit_mode()
{
    $edit_mode = false;
    if ( !empty(fea_instance()->elementor) ) {
        $edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
    }
    if ( !empty($GLOBALS['admin_form']['preview_mode']) ) {
        $edit_mode = true;
    }
    return $edit_mode;
}

function feadmin_get_product_object()
{
    
    if ( isset( $GLOBALS['admin_form']['save_to_product'] ) ) {
        $form = $GLOBALS['admin_form'];
        if ( $form['save_to_product'] == 'edit_product' ) {
            return wc_get_product( $form['product_id'] );
        }
    }
    
    return false;
}

function feadmin_get_field_type_groups( $type = 'all' )
{
    $fields = [];
    
    if ( $type == 'all' ) {
        $fields['acf'] = array(
            'label'   => __( 'ACF Field', FEA_NS ),
            'options' => array(
            'ACF_fields'       => __( 'ACF Fields', FEA_NS ),
            'ACF_field_groups' => __( 'ACF Field Groups', FEA_NS ),
        ),
        );
        $fields['layout'] = array(
            'label'   => __( 'Layout', FEA_NS ),
            'options' => array(
            'message' => __( 'Message', FEA_NS ),
            'column'  => __( 'Column', FEA_NS ),
        ),
        );
    }
    
    if ( $type == 'all' || $type == 'post' ) {
        $fields['post'] = array(
            'label'   => __( 'Post' ),
            'options' => array(
            'title'          => __( 'Post Title', FEA_NS ),
            'slug'           => __( 'Slug', FEA_NS ),
            'content'        => __( 'Post Content', FEA_NS ),
            'featured_image' => __( 'Featured Image', FEA_NS ),
            'excerpt'        => __( 'Post Excerpt', FEA_NS ),
            'categories'     => __( 'Categories', FEA_NS ),
            'tags'           => __( 'Tags', FEA_NS ),
            'author'         => __( 'Post Author', FEA_NS ),
            'published_on'   => __( 'Published On', FEA_NS ),
            'post_type'      => __( 'Post Type', FEA_NS ),
            'menu_order'     => __( 'Menu Order', FEA_NS ),
            'allow_comments' => __( 'Allow Comments', FEA_NS ),
            'taxonomy'       => __( 'Custom Taxonomy', FEA_NS ),
        ),
        );
    }
    if ( $type == 'all' || $type == 'user' ) {
        $fields['user'] = array(
            'label'   => __( 'User', FEA_NS ),
            'options' => array(
            'username'         => __( 'Username', FEA_NS ),
            'password'         => __( 'Password', FEA_NS ),
            'confirm_password' => __( 'Confirm Password', FEA_NS ),
            'email'            => __( 'Email', FEA_NS ),
            'first_name'       => __( 'First Name', FEA_NS ),
            'last_name'        => __( 'Last Name', FEA_NS ),
            'nickname'         => __( 'Nickname', FEA_NS ),
            'display_name'     => __( 'Display Name', FEA_NS ),
            'bio'              => __( 'Biography', FEA_NS ),
            'role'             => __( 'Role', FEA_NS ),
        ),
        );
    }
    if ( $type == 'all' || $type == 'term' ) {
        $fields['term'] = array(
            'label'   => __( 'Term', FEA_NS ),
            'options' => array(
            'term_name'        => __( 'Term Name', FEA_NS ),
            'term_slug'        => __( 'Term Slug', FEA_NS ),
            'term_description' => __( 'Term Description', FEA_NS ),
        ),
        );
    }
    return $fields;
}

/*
*  get_selected_field
*
*  This function will return the label for a given clone choice
*
*  @type    function
*  @date    17/06/2016
*  @since   5.3.8
*
*  @param   $selector (mixed)
*  @return  (string)
*/
function feadmin_get_selected_field( $selector = '', $type = '' )
{
    // bail early no selector
    if ( !$selector ) {
        return '';
    }
    // ajax_fields
    if ( isset( $_POST['fields'][$selector] ) ) {
        return feadmin_field_choice( $_POST['fields'][$selector] );
    }
    // field
    if ( acf_is_field_key( $selector ) ) {
        return feadmin_field_choice( acf_get_field( $selector ) );
    }
    // group
    if ( acf_is_field_group_key( $selector ) ) {
        return feadmin_group_choice( acf_get_field_group( $selector ) );
    }
    if ( feadmin_is_admin_form_key( $selector ) ) {
        return feadmin_group_choice( fea_instance()->form_display->get_form( $selector ) );
    }
    // return
    return $selector;
}

/*
*  feadmin_field_choice
*
*  This function will return the text for a field choice
*
*  @type    function
*  @date    20/07/2016
*  @since   5.4.0
*
*  @param   $field (array)
*  @return  (string)
*/
function feadmin_field_choice( $field )
{
    // bail early if no field
    if ( !$field ) {
        return __( 'Unknown field', 'acf' );
    }
    // title
    $title = ( $field['label'] ? $field['label'] : __( '(no title)', 'acf' ) );
    // append type
    $title .= ' (' . $field['type'] . ')';
    // ancestors
    // - allow for AJAX to send through ancestors count
    $ancestors = ( isset( $field['ancestors'] ) ? $field['ancestors'] : count( acf_get_field_ancestors( $field ) ) );
    $title = str_repeat( '- ', $ancestors ) . $title;
    // return
    return $title;
}

/*
*  feadmin_group_choice
*
*  This function will return the text for a group choice
*
*  @type    function
*  @date    20/07/2016
*  @since   5.4.0
*
*  @param   $field_group (array)
*  @return  (string)
*/
function feadmin_group_choice( $field_group )
{
    // bail early if no field group
    if ( !$field_group ) {
        return __( 'Unknown field group', 'acf' );
    }
    // return
    return sprintf( __( 'All fields from %s', FEA_NS ), $field_group['title'] );
}

/*
*  get_selected_fields
*
*  This function will return an array of choices data for Select2
*
*  @type    function
*  @date    17/06/2016
*  @since   5.3.8
*
*  @param   $value (mixed)
*  @return  (array)
*/
function feadmin_get_selected_fields( $value, $choices = array() )
{
    // bail early if no $value
    if ( empty($value) ) {
        return $choices;
    }
    // force value to array
    $value = acf_get_array( $value );
    // loop
    foreach ( $value as $v ) {
        $choices[$v] = feadmin_get_selected_field( $v );
    }
    // return
    return $choices;
}

function feadmin_is_admin_form_key( $id )
{
    if ( is_string( $id ) && substr( $id, 0, 5 ) === 'form_' ) {
        return true;
    }
    return false;
}

function feadmin_form_choices( $choices = array() )
{
    $args = array(
        'post_type'      => 'admin_form',
        'posts_per_page' => '-1',
        'post_status'    => 'any',
    );
    $forms = get_posts( $args );
    if ( empty($forms) ) {
        return $choices;
    }
    foreach ( $forms as $form ) {
        $choices[$form->ID] = $form->post_title;
    }
    return $choices;
}
