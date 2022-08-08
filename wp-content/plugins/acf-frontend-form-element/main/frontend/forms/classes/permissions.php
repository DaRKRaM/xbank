<?php
namespace Frontend_WP\Classes;


use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class PermissionsTab{
    
    public function register_permissions_section( $widget, $form = false ){
        $section_settings = [
            'label' => __( 'Permissions', FEA_NS ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ];
        if( $form ){
            $section_settings['condition'] = [
                'admin_forms_select' => '',
            ];
        }
        $widget->start_controls_section( 'permissions_section', $section_settings );
		$condition = [];
      
        $widget->add_control( 'not_allowed', [
            'label'       => __( 'No Permissions Message', FEA_NS ),
            'type'        => Controls_Manager::SELECT,
            'label_block' => true,
            'default'     => 'show_nothing',
            'options'     => [
				'show_nothing'   => __( 'None', FEA_NS ),
				'show_message'   => __( 'Message', FEA_NS ),
				'custom_content' => __( 'Custom Content', FEA_NS ),
			],
        ] );
        $condition['not_allowed'] = 'show_message';
        $widget->add_control( 'not_allowed_message', [
            'label'       => __( 'Message', FEA_NS ),
            'type'        => Controls_Manager::TEXTAREA,
            'label_block' => true,
            'rows'        => 4,
            'default'     => __( 'You do not have the proper permissions to view this form', FEA_NS ),
            'placeholder' => __( 'You do not have the proper permissions to view this form', FEA_NS ),
            'condition'   => $condition,
        ] );
        $condition['not_allowed'] = 'custom_content';
        $widget->add_control( 'not_allowed_content', [
            'label'       => __( 'Content', FEA_NS ),
            'type'        => Controls_Manager::WYSIWYG,
            'label_block' => true,
            'render_type' => 'none',
            'condition'   => $condition,
        ] );
        unset( $condition['not_allowed'] );
        $who_can_see = array(
            'logged_in'  => __( 'Only Logged In Users', FEA_NS ),
            'logged_out' => __( 'Only Logged Out', FEA_NS ),
            'all'        => __( 'All Users', FEA_NS ),
        );
        //get all user role choices
        $user_roles = feadmin_get_user_roles( array(), true );
        $user_caps = feadmin_get_user_caps( array(), true );

        $widget->add_control( 'who_can_see', [
            'label'       => __( 'Who Can See This...', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'default'     => 'logged_in',
            'options'     => $who_can_see,
            'condition'   => $condition,
        ] );
        $condition['who_can_see'] = 'logged_in';
        $widget->add_control( 'by_role', [
            'label'       => __( 'Select By Role', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'default'     => ['administrator'],
            'options'     => $user_roles,
            'condition'   => $condition,
        ] );
        $widget->add_control( 'by_cap', [
            'label'       => __( 'Select By Capabilities', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'options'     => $user_caps,
            'condition'   => $condition,
        ] );
        if ( !class_exists( 'ElementorPro\\Modules\\QueryControl\\Module' ) ) {
            $widget->add_control( 'by_user_id', [
                'label'       => __( 'Select By User', FEA_NS ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __( '18, 12, 11', FEA_NS ),
                'description' => __( 'Enter the a comma-seperated list of user ids', FEA_NS ),
                'condition'   => $condition,
            ] );
        } else {
            $widget->add_control( 'by_user_id', [
                'label'        => __( 'Select By User', FEA_NS ),
                'label_block'  => true,
                'type'         => Query_Module::QUERY_CONTROL_ID,
                'autocomplete' => [
                'object'  => Query_Module::QUERY_OBJECT_USER,
                'display' => 'detailed',
            ],
                'multiple'     => true,
                'condition'    => $condition,
            ] );
        }
        
        if( $widget->get_name() !== 'edit_button' ){
            $condition['save_to_post'] = ['edit_post','duplicate_post','delete_post'];
        }
        $widget->add_control( 'dynamic', [
            'label'       => __( 'Dynamic Permissions', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'description' => 'Use a dynamic acf user field that returns a user ID to filter the form for that user dynamically. You may also select the post\'s author',
            'options'     => feadmin_user_id_fields(),
            'condition'   => $condition,
        ] );
        $condition['save_to_user'] = 'edit_user';
        $widget->add_control( 'dynamic_manager', [
            'label'       => __( 'Dynamic Permissions', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'options'     => [
            'manager' => __( 'User Manager', FEA_NS ),
        ],
            'condition'   => $condition,
        ] );

        if( $form ){
            $widget->add_control( 'wp_uploader', [
                'label'        => __( 'WP Media Library', FEA_NS ),
                'type'         => Controls_Manager::SWITCHER,
                'description'  => 'Whether to use the WordPress media library for file fields or just a basic upload button',
                'label_on'     => __( 'Yes', FEA_NS ),
                'label_off'    => __( 'No', FEA_NS ),
                'default'      => 'true',
                'return_value' => 'true',
            ] );
            $widget->add_control( 'media_privacy_note', [
                'label'           => __( '<h3>Media Privacy</h3>', FEA_NS ),
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => __( '<p align="left">Click <a target="_blank" href="' . admin_url( '?page='.FEA_PRE.'-settings&tab=uploads-privacy' ) . '">here</a> to limit the files displayed in the media library to the user who uploaded them.</p>', FEA_NS ),
                'content_classes' => 'media-privacy-note',
            ] );
        }
        $widget->end_controls_section();
    }

	public function show_form( $settings ){
        return $this->conditions_logic( $settings, 'form' );
    }
    public function show_submissions( $settings ){
        return $this->conditions_logic( $settings, 'submissions' );
    }
    public function conditions_logic( $settings, $type ){
        global $post;

        if( isset( $settings['form_conditions'] ) ){
            $conditions = $settings['form_conditions'];
        }else{
            $conditions = array();
            $values = array( 
                'who_can_see' => 'logged_in',
                'not_allowed' => 'show_nothing',
                'not_allowed_message' => '',
                'not_allowed_content' => '',
                'email_verification' => 'all',
                'by_role' => array( 'administrator' ),
                'by_user_id' => '',
                'dynamic' => '',
            );
            
            foreach( $values as $key => $value ){
                if( isset( $settings[$key] ) ){
                    $conditions[0][$key] = $settings[$key];
                }else{
                    $conditions[0][$key] = $value;
                }
            }
        }

        if( empty( $conditions ) ){
            return $settings;
        }
        $active_user = wp_get_current_user();
        $is_logged_in = is_user_logged_in();
        foreach( $conditions as $condition ){           
            if( ! isset( $condition['applies_to'] ) ){
                $condition['applies_to'] = array( 'form' );
            }

            if( empty( $condition['applies_to'] ) || ! in_array( $type, $condition['applies_to'] ) ) continue;
            
            if ( 'all' == $condition['who_can_see'] ) {
                $settings['display'] = true;
                return $settings;
            }
            if ( 'logged_out' == $condition['who_can_see'] ) {
                $settings['display'] = !$is_logged_in;
            }
            if ( 'logged_in' == $condition['who_can_see'] ) {            
                if ( !$is_logged_in ) {
                    $settings['display'] = false;
                } else {
                    $by_role = $by_cap = $specific_user = $dynamic = false;
                    $user_roles = $condition['by_role'];
                    
                   if ( $user_roles ) {
                        if ( is_array( $condition['by_role'] ) ) {
                            if ( count( array_intersect( $condition['by_role'], (array) $active_user->roles ) ) != false || in_array( 'all', $condition['by_role'] ) ) {
                                $by_role = true;
                            }
                        }
                    } 
    
                    if( ! empty( $condition['by_cap'] ) ){
                        foreach( $condition['by_cap'] as $cap ){
                            if( current_user_can( $cap ) ) $by_cap = true;
                        }
                    }
                                    
                    if ( ! empty( $condition['by_user_id'] ) ) {
                        $user_ids = $condition['by_user_id'];
                        if ( ! is_array( $user_ids ) ) {
                            $user_ids = explode( ',', $user_ids );
                        }
                         if ( is_array( $user_ids ) ) {
                            if ( in_array( $active_user->ID, $user_ids ) ) {
                                $specific_user = true;
                            }
                        } 
                    } 
    
                    $save = isset( $settings['save_to_post'] ) ? $settings['save_to_post'] : '';
                    if( $save == 'edit_post' || $save == 'delete_post' || $save == 'duplicate_post' ) $post_action = true;
    
                    if( ! empty( $condition['dynamic'] ) ){
                        if( ! empty( $settings['post_id'] ) ){
                            $post_id = $settings['post_id'];
                        }elseif( ! empty( $settings['product_id'] ) ){
                           $post_id = $settings['product_id'];
                        }else{                
                            $post_id = get_the_ID();
                            if( isset( $post_action ) ) {
                                if( $settings['post_to_edit'] == 'select_post' && ! empty( $settings['post_select'] ) ){
                                    $post_id = $settings['post_select'];
                                }elseif( $settings['post_to_edit'] == 'url_query' && isset( $_GET[ $settings['url_query_post'] ] ) ){
                                    $post_id = $_GET[ $settings['url_query_post'] ];
                                }
                            }
                        }
                            
                        if( '[author]' == $condition['dynamic'] ) {
                            $author_id = get_post_field( 'post_author', $post_id );
                        }else{
                            $author_id = get_post_meta( $post_id, $condition['dynamic'], true );
                        }
    
                        if( ! is_numeric( $author_id ) ){
                            $authors = acf_decode_choices( $author_id );
                            if( in_array( $active_user->ID, $authors ) ) $dynamic = true;
                        }else{
                            if( $author_id == $active_user->ID ) $dynamic = true;
                        }
                        
                    }
                    $save = isset( $settings['save_to_user'] ) ? $settings['save_to_user'] : '';
                    if( $save == 'edit_user' || $save == 'delete_user' ) $user_action = true;
                    if( isset( $condition['dynamic_manager'] ) && isset( $user_action ) ){
                        if( $settings['user_to_edit'] == 'current_user' ){
                            $user_id = $active_user->ID; 
                        }elseif( $settings['user_to_edit'] == 'select_user' ){
                            $user_id = $settings['user_select'];
                        }elseif( $settings['user_to_edit'] == 'url_query' && isset( $_GET[ $settings['url_query_user'] ] ) ){
                            $user_id = $_GET[ $settings['url_query_user'] ];
                        }
            
                        if( $condition['dynamic_manager'] && isset( $user_id[1] ) ){
                            $manager_id = false;
                            
                            if( 'manager' == $condition['dynamic_manager'] ) {
                                $manager_id = get_user_meta( $user_id, 'frontend_admin_manager', true );
                            }else{
                                $manager_id = get_user_meta( $user_id, $condition['dynamic_manager'], true );
                            }
                            
                            if( $manager_id == $active_user->ID ) {
                                $dynamic = true;
                            }
                        }
                    }
                                    
                    if ( $by_role || $by_cap || $specific_user || $dynamic ){
                        if( isset( $condition['email_verification'] ) && $condition['email_verification'] != 'all' ){
                            $required = $condition['email_verification'] == 'verified' ? 1 : 0;
                            $email_verified = get_user_meta( $active_user, 'frontend_admin_email_verified', true );
        
                            if( ( $email_verified == $required )  ){
                                $settings['display'] = true;
                            }else{
                                $settings['display'] = false;
                            }
                        }else{
                            $settings['display'] = true;
                        }
                        if( ! empty( $condition['allowed_submits'] ) ){
                            $submits = (int)$condition['allowed_submits'];

                            $submitted = get_user_meta( $active_user->ID, 'submitted::'.$settings['id'], true );
                            if( $submits - (int)$submitted <= 0 ){
                                $settings['display'] = false;

                                if( $condition['limit_reached'] == 'show_message' ){
                                    $settings['message'] = '<div class="acf-notice -limit frontend-admin-limit-message"><p>' . $condition['limit_reached_message'] . '</p></div>';
                                }
                                elseif( $condition['limit_reached'] == 'custom_content' ){
                                    $settings['message'] = $condition['limit_reached_content'];
                                }
                                else{
                                    $settings['message'] = 'NOTHING';
                                }
                            }
                        }                       
                        return $settings;
                    }
    
                    $settings['display'] = false;
                     
                }
            }      
            if( $condition['not_allowed'] == 'show_message' ){
                $settings['message'] = '<div class="acf-notice -limit frontend-admin-limit-message"><p>' . $condition['not_allowed_message'] . '</p></div>';
            }
            elseif( $condition['not_allowed'] == 'custom_content' ){
                $settings['message'] = $condition['not_allowed_content'];
            }
            else{
                $settings['message'] = 'NOTHING';
            }
        }
        return $settings;
    }
    
	public function __construct() {
		add_action( FEA_PREFIX.'/permissions_section', [ $this, 'register_permissions_section'], 10, 2 );
		add_filter( FEA_PREFIX.'/show_form', [ $this, 'show_form'], 10 );
		add_filter( FEA_PREFIX.'/show_submissions', [ $this, 'show_submissions'], 10 );		
	}

}

new PermissionsTab();
