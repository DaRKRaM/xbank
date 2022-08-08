<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( function_exists('register_post_type') ):
    $dashboard_slug = get_option( 'frontend_admin_dashboard_slug' );
    if( ! $dashboard_slug ) $dashboard_slug = 'frontend-dashboard';

    $labels = array(
        'name'                  => _x( 'Forms', 'Post Type General Name', FEA_NS ),
        'singular_name'         => _x( 'Form', 'Post Type Singular Name', FEA_NS ),
        'menu_name'             => __( 'Forms', FEA_NS ),
        'name_admin_bar'        => __( 'Form', FEA_NS ),
        'archives'              => __( 'Form Archives', FEA_NS ),
        'all_items'             => __( 'Forms', FEA_NS ),
        'add_new_item'          => __( 'Add New Form', FEA_NS ),
        'add_new'               => __( 'Add New', FEA_NS ),
        'new_item'              => __( 'New Form', FEA_NS ),
        'edit_item'             => __( 'Edit Form', FEA_NS ),
        'update_item'           => __( 'Update Form', FEA_NS ),
        'view_item'             => __( 'View Form', FEA_NS ),
        'search_items'          => __( 'Search Form', FEA_NS ),
        'not_found'             => __( 'Not found', FEA_NS ),
        'not_found_in_trash'    => __( 'Not found in Trash', FEA_NS ),
        'items_list'            => __( 'Forms list', FEA_NS ),
        'item_published'        => __( 'Settings Saved', FEA_NS ),
        'item_updated'          => __( 'Settings Saved', FEA_NS ),
        'items_list_navigation' => __( 'Forms list navigation', FEA_NS ),
        'filter_items_list'     => __( 'Filter forms list', FEA_NS ),
    );
   
    $args = array(
        'label'                 => __( 'Form', FEA_NS ),
        'description'           => __( 'Form', FEA_NS ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'show_in_rest'          => true,
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => FEA_PRE.'-settings',
        'menu_position'         => 80,
        'show_in_admin_bar'     => true,
        'can_export'            => true,
        'rewrite'               => array( 
            'with_front' => true, 'slug' => $dashboard_slug
        ),
        'capability_type'       => 'page',
        'query_var'				=> false,
    );
    register_post_type( 'admin_form', $args );

    add_filter( 'post_updated_messages', function( $messages ){
        $messages['admin_form'] = array(
            '',
            __( 'Form updated.' ),
            __( 'Custom field updated.' ),
            __( 'Custom field deleted.' ),
            __( 'Form updated.' ),
            '',
            __( 'Form published.' ),
            __( 'Form saved.' ),
            __( 'Form submitted.' ),
            '',
            __( 'Form draft updated.' ),
        );
        return $messages;
    } );

/*    
    $labels = array(
        'name'                  => _x( 'Templates', 'Post Type General Name', FEA_NS ),
        'singular_name'         => _x( 'Template', 'Post Type Singular Name', FEA_NS ),
        'menu_name'             => __( 'Templates', FEA_NS ),
        'name_admin_bar'        => __( 'Template', FEA_NS ),
        'archives'              => __( 'Template Archives', FEA_NS ),
        'all_items'             => __( 'Templates', FEA_NS ),
        'add_new_item'          => __( 'Add New Template', FEA_NS ),
        'add_new'               => __( 'Add New', FEA_NS ),
        'new_item'              => __( 'New Template', FEA_NS ),
        'edit_item'             => __( 'Edit Template', FEA_NS ),
        'update_item'           => __( 'Update Template', FEA_NS ),
        'view_item'             => __( 'View Template', FEA_NS ),
        'search_items'          => __( 'Search Template', FEA_NS ),
        'not_found'             => __( 'Not found', FEA_NS ),
        'not_found_in_trash'    => __( 'Not found in Trash', FEA_NS ),
        'items_list'            => __( 'Templates list', FEA_NS ),
        'item_published'        => __( 'Settings Saved', FEA_NS ),
        'item_updated'          => __( 'Settings Saved', FEA_NS ),
        'items_list_navigation' => __( 'Templates list navigation', FEA_NS ),
        'filter_items_list'     => __( 'Filter templates list', FEA_NS ),
    );
   
    $args = array(
        'label'                 => __( 'Template', FEA_NS ),
        'description'           => __( 'Template', FEA_NS ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'show_in_rest'          => false,
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => FEA_PRE.'-settings',
        'menu_position'         => 80,
        'show_in_admin_bar'     => true,
        'can_export'            => true,
        'rewrite'               => array( 
            'with_front' => true, 'slug' => $dashboard_slug
        ),
        'capability_type'       => 'page',
        'query_var'				=> false,
    );
    register_post_type( 'admin_template', $args );
 

    add_filter( 'post_updated_messages', function( $messages ){
        $messages['admin_template'] = array(
            '',
            __( 'Template updated.' ),
            __( 'Custom field updated.' ),
            __( 'Custom field deleted.' ),
            __( 'Template updated.' ),
            '',
            __( 'Template published.' ),
            __( 'Template saved.' ),
            __( 'Template submitted.' ),
            '',
            __( 'Template draft updated.' ),
        );
        return $messages;
    } ); */

    $permalinks = get_option( 'fea_flush_permalinks' );

    if( $permalinks ){
        global $wp_rewrite; 
	    update_option( "rewrite_rules", FALSE ); 
	    $wp_rewrite->flush_rules( true );

        delete_option( 'fea_flush_permalinks' );
    }

    do_action( FEA_PREFIX.'/post_types' );

endif;