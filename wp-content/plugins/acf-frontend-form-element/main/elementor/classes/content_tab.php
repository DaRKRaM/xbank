<?php

namespace Frontend_WP\Module\Classes;

use  Elementor\Controls_Manager ;
use  Elementor\Controls_Stack ;
use  Elementor\Group_Control_Typography ;
use  Elementor\Group_Control_Background ;
use  Elementor\Group_Control_Border ;
use  Elementor\Group_Control_Text_Shadow ;
use  Elementor\Group_Control_Box_Shadow ;
use  ElementorPro\Modules\QueryControl\Module as Query_Module ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class ContentTab
{
    public function fields_controls( $widget, $steps = true )
    {
        $GLOBALS['only_acf_field_groups'] = 1;
        $field_group_choices = feadmin_get_acf_group_choices();
        $field_choices = feadmin_get_acf_field_choices();
        $GLOBALS['only_acf_field_groups'] = 0;
        $widget->add_control( 'save_form_submissions', [
            'label'        => __( 'Save Form Submissions', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'yes', FEA_NS ),
            'label_off'    => __( 'no', FEA_NS ),
            'return_value' => 'true',
            'default'      => get_option( 'frontend_admin_save_submissions' ),
            'condition'    => [
            'admin_forms_select' => '',
        ],
        ] );
        $widget->add_control( 'save_all_data', [
            'label'     => __( 'Save Data After...', FEA_NS ),
            'type'      => Controls_Manager::SELECT2,
            'multiple'  => true,
            'default'   => '',
            'options'   => [
            'require_approval' => __( 'Admin Approval', FEA_NS ),
            'verify_email'     => __( 'Email is Verified', FEA_NS ),
        ],
            'condition' => [
            'save_form_submissions' => 'true',
            'admin_forms_select'    => '',
        ],
        ] );
        $widget->add_control( 'multi', [
            'type'          => Controls_Manager::HIDDEN,
            'default_value' => 'true',
            'condition'     => [
            'admin_forms_select' => '',
        ],
        ] );
        if ( $widget->get_name() != 'acf_form_fields' ) {
            $widget->add_control( 'multi_step_promo', [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go pro</b></a> to unlock multi step forms.</p>', FEA_NS ),
                'content_classes' => 'acf-fields-note',
            ] );
        }
        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'field_type', [
            'label'       => __( 'Field Type', FEA_NS ),
            'type'        => Controls_Manager::SELECT,
            'label_block' => true,
            'default'     => 'ACF_fields',
            'groups'      => $widget->get_field_type_options(),
        ] );
        if ( $steps ) {
            $this->register_step_controls( $repeater, $widget );
        }
        $repeater->add_control( 'dynamic_acf_fields', [
            'label'        => __( 'Current ACF fields', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => __( 'Dynamically pull in any field groups from the current post, user, or other data that you are editing.', FEA_NS ),
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => [ 'ACF_field_groups' ],
        ],
        ] );
        $repeater->add_control( 'field_groups_select', [
            'label'       => __( 'ACF Field Groups', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'options'     => $field_group_choices,
            'condition'   => [
            'field_type'          => 'ACF_field_groups',
            'dynamic_acf_fields!' => 'true',
        ],
        ] );
        $repeater->add_control( 'fields_select', [
            'label'       => __( 'ACF Fields', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'options'     => $field_choices,
            'condition'   => [
            'field_type' => 'ACF_fields',
        ],
        ] );
        $repeater->add_control( 'fields_select_exclude', [
            'label'       => __( 'Exclude Specific Fields', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'options'     => $field_choices,
            'condition'   => [
            'field_type'          => [ 'ACF_field_groups' ],
            'dynamic_acf_fields!' => 'true',
        ],
        ] );
        $repeater->add_control( 'endpoint', [
            'label'        => __( 'End Point', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => __( 'End the previous column here. By default, the previous column will be stay opened until the next column or until the end of the form.', FEA_NS ),
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => [ 'column', 'tab' ],
        ],
        ] );
        $repeater->add_control( 'nested', [
            'label'        => __( 'Nested', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => __( 'Nest this column within the previous column. By default, the previous column will be closed before this one begins.', FEA_NS ),
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => 'column',
            'endpoint!'  => 'true',
        ],
        ] );
        $custom_layouts = [
            'ACF_field_groups',
            'ACF_fields',
            'recaptcha',
            'step',
            'column',
            'tab'
        ];
        $base_text_fields = array(
            'term_name',
            'username',
            'email',
            'first_name',
            'last_name',
            'nickname',
            'display_name',
            'title',
            'sku',
            'product_title',
            'author',
            'author_email',
            'site_title',
            'site_tagline'
        );
        $text_fields = array(
            'term_name',
            'username',
            'email',
            'first_name',
            'last_name',
            'nickname',
            'display_name',
            'bio',
            'title',
            'slug',
            'content',
            'excerpt',
            'sku',
            'product_title',
            'description',
            'short_description',
            'comment',
            'author',
            'author_email',
            'site_title',
            'site_tagline'
        );
        $number_fields = array( 'price', 'sale_price' );
        $repeater->add_control( 'field_label_on', [
            'label'        => __( 'Show Label', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'default'      => 'true',
            'condition'    => [
            'field_type!' => $custom_layouts,
        ],
        ] );
        $repeater->add_control( 'field_label', [
            'label'       => __( 'Label', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => __( 'Field Label', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type!'    => $custom_layouts,
            'field_label_on' => 'true',
        ],
        ] );
        $repeater->add_control( 'field_placeholder', [
            'label'       => __( 'Placeholder', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => __( 'Field Placeholder', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => $base_text_fields,
        ],
        ] );
        $repeater->add_control( 'field_default_value', [
            'label'       => __( 'Default Value', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'description' => __( 'This will populate a field if no value has been given yet. You can use shortcodes from other text fields. For example: [acf:field_name]', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => $text_fields,
        ],
        ] );
        $repeater->add_control( 'number_placeholder', [
            'label'       => __( 'Placeholder', FEA_NS ),
            'type'        => Controls_Manager::NUMBER,
            'placeholder' => __( 'Field Placeholder', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => $number_fields,
        ],
        ] );
        $repeater->add_control( 'number_default_value', [
            'label'     => __( 'Default Value', FEA_NS ),
            'type'      => Controls_Manager::NUMBER,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => $number_fields,
        ],
        ] );
        $repeater->add_control( 'default_featured_image', [
            'label'     => __( 'Default', FEA_NS ),
            'type'      => \Elementor\Controls_Manager::MEDIA,
            'condition' => [
            'field_type' => array( 'featured_image', 'main_image' ),
        ],
        ] );
        $repeater->add_control( 'editor_type', [
            'label'     => __( 'Type', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'wysiwyg'  => __( 'Text Editor', FEA_NS ),
            'textarea' => __( 'Text Area', FEA_NS ),
        ],
            'default'   => 'wysiwyg',
            'condition' => [
            'field_type' => [ 'content', 'description' ],
        ],
        ] );
        $repeater->add_control( 'button_text', [
            'label'     => __( 'Button Text', FEA_NS ),
            'type'      => Controls_Manager::TEXT,
            'condition' => [
            'field_type' => [
            'main_image',
            'featured_image',
            'images',
            'variations',
            'attributes'
        ],
        ],
        ] );
        $repeater->add_control( 'save_button_text', [
            'label'     => __( 'Save Changes Text', FEA_NS ),
            'type'      => Controls_Manager::TEXT,
            'condition' => [
            'field_type' => [ 'variations', 'attributes' ],
        ],
        ] );
        $repeater->add_control( 'product_authors_to_filter', [
            'label'       => __( 'Filter by Users', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => __( '18, 12, 11', FEA_NS ),
            'default'     => '[current_user]',
            'description' => __( 'Enter the a comma-seperated list of user ids. Dynamic Options: ', FEA_NS ) . ' [current_user]',
            'condition'   => [
            'field_type' => [ 'grouped_products', 'cross_sells', 'upsells' ],
        ],
        ] );
        $repeater->add_control( 'add_edit_product', [
            'label'        => __( 'Add/Edit Product', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => [ 'grouped_products', 'cross_sells', 'upsells' ],
        ],
        ] );
        $repeater->add_control( 'new_product_text', [
            'label'     => __( 'New Product Text', FEA_NS ),
            'type'      => Controls_Manager::TEXT,
            'condition' => [
            'field_type'       => [ 'grouped_products', 'cross_sells', 'upsells' ],
            'add_edit_product' => 'true',
        ],
        ] );
        $repeater->add_control( 'no_value_msg', [
            'label'       => __( 'No Value Message', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => [ 'variations', 'attributes' ],
        ],
            'render_type' => 'none',
        ] );
        $repeater->add_control( 'no_attrs_msg', [
            'label'       => __( 'No Attributes Message', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => [ 'variations' ],
        ],
            'render_type' => 'none',
        ] );
        $repeater->add_control( 'field_instruction', [
            'label'       => __( 'Instructions', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => __( 'Field Instruction', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type!' => $custom_layouts,
        ],
        ] );
        $repeater->add_control( 'prepend', [
            'label'     => __( 'Prepend', FEA_NS ),
            'type'      => Controls_Manager::TEXT,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => array_merge( $base_text_fields, $number_fields ),
        ],
        ] );
        $repeater->add_control( 'append', [
            'label'     => __( 'Append', FEA_NS ),
            'type'      => Controls_Manager::TEXT,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => array_merge( $base_text_fields, $number_fields ),
        ],
        ] );
        /* $repeater->add_control(
        			'character_limit',
        			[
        				'label' => __( 'Character Limit', FEA_NS ),
        				'type' => Controls_Manager::NUMBER,
        				'dynamic' => [
        					'active' => true,
        				],		
        				'condition' => [
        					'field_type' => $text_fields,
        				],
        			]
        		);		 */
        $repeater->add_control( 'minimum', [
            'label'     => __( 'Minimum Value', FEA_NS ),
            'type'      => Controls_Manager::NUMBER,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => $number_fields,
        ],
        ] );
        $repeater->add_control( 'maximum', [
            'label'     => __( 'Maximum Value', FEA_NS ),
            'type'      => Controls_Manager::NUMBER,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => $number_fields,
        ],
        ] );
        $repeater->add_control( 'field_required', [
            'label'        => __( 'Required', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type!' => $custom_layouts,
        ],
        ] );
        $repeater->add_control( 'field_hidden', [
            'label'        => __( 'Hidden', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type!' => $custom_layouts,
        ],
        ] );
        $repeater->add_control( 'field_disabled', [
            'label'        => __( 'Disabled', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => __( 'This will prevent users from editing the field and the data will not be sent.', FEA_NS ),
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type!' => $custom_layouts,
        ],
        ] );
        $repeater->add_control( 'field_readonly', [
            'label'        => __( 'Readonly', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'description'  => __( 'This will prevent users from editing the field.', FEA_NS ),
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => $base_text_fields,
        ],
        ] );
        if ( class_exists( 'woocommerce' ) ) {
            $repeater->add_control( 'default_product_type', [
                'label'     => __( 'Default', FEA_NS ),
                'type'      => Controls_Manager::SELECT,
                'options'   => wc_get_product_types(),
                'condition' => [
                'field_type' => 'product_type',
            ],
            ] );
        }
        $repeater->add_control( 'field_message', [
            'label'       => __( 'Message', FEA_NS ),
            'type'        => \Elementor\Controls_Manager::WYSIWYG,
            'default'     => __( 'You can add here text, images template shortcodes, and more', FEA_NS ),
            'placeholder' => __( 'Type your message here', FEA_NS ),
            'condition'   => [
            'field_type' => 'message',
        ],
        ] );
        $repeater->add_control( 'post_type_field_options', [
            'label'       => __( 'Post Types to Choose From', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'default'     => [ 'subscriber' ],
            'options'     => acf_get_pretty_post_types(),
            'condition'   => [
            'field_type' => 'post_type',
        ],
        ] );
        $repeater->add_control( 'default_post_type', [
            'label'       => __( 'Default Post Type Option', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'default'     => [ 'subscriber' ],
            'options'     => acf_get_pretty_post_types(),
            'condition'   => [
            'field_type' => 'post_type',
        ],
        ] );
        $repeater->add_control( 'role_field_options', [
            'label'       => __( 'Roles to Choose From', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'default'     => [ 'subscriber' ],
            'options'     => feadmin_get_user_roles(),
            'condition'   => [
            'field_type' => 'role',
        ],
        ] );
        $repeater->add_control( 'role_appearance', [
            'label'     => __( 'Appearance', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'radio',
            'options'   => [
            'radio'  => __( 'Radio Buttons', FEA_NS ),
            'select' => __( 'Select', FEA_NS ),
        ],
            'condition' => [
            'field_type' => [
            'role',
            'allow_backorders',
            'stock_status',
            'post_type',
            'product_type'
        ],
        ],
        ] );
        $repeater->add_control( 'role_radio_layout', [
            'label'     => __( 'Layout', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'vertical',
            'options'   => [
            'vertical'   => __( 'Vertical', FEA_NS ),
            'horizontal' => __( 'Horizontal', FEA_NS ),
        ],
            'condition' => [
            'field_type'      => [
            'role',
            'allow_backorders',
            'stock_status',
            'post_type'
        ],
            'role_appearance' => 'radio',
        ],
        ] );
        $repeater->add_control( 'default_role', [
            'label'       => __( 'Default Role Option', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'default'     => [ 'subscriber' ],
            'options'     => feadmin_get_user_roles(),
            'condition'   => [
            'field_type' => 'role',
        ],
        ] );
        $repeater->add_control( 'password_strength', [
            'label'       => __( 'Password Strength', FEA_NS ),
            'type'        => Controls_Manager::SELECT,
            'label_block' => true,
            'default'     => '3',
            'options'     => [
            '1' => __( 'Very Weak', FEA_NS ),
            '2' => __( 'Weak', FEA_NS ),
            '3' => __( 'Medium', FEA_NS ),
            '4' => __( 'Strong', FEA_NS ),
        ],
            'condition'   => [
            'field_type' => 'password',
        ],
        ] );
        
        if ( !class_exists( 'ElementorPro\\Modules\\QueryControl\\Module' ) ) {
            $repeater->add_control( 'default_terms', [
                'label'       => __( 'Default Terms', FEA_NS ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __( '18, 12, 11', FEA_NS ),
                'description' => __( 'Enter the a comma-seperated list of term ids', FEA_NS ),
                'condition'   => [
                'field_type' => [
                'taxonomy',
                'categories',
                'tags',
                'product_categories',
                'product_tags'
            ],
            ],
            ] );
        } else {
            $repeater->add_control( 'default_terms', [
                'label'        => __( 'Default Terms', FEA_NS ),
                'type'         => Query_Module::QUERY_CONTROL_ID,
                'label_block'  => true,
                'autocomplete' => [
                'object'  => Query_Module::QUERY_OBJECT_TAX,
                'display' => 'detailed',
            ],
                'multiple'     => true,
                'condition'    => [
                'field_type' => [
                'taxonomy',
                'categories',
                'tags',
                'product_categories',
                'product_tags'
            ],
            ],
            ] );
        }
        
        $repeater->add_control( 'field_taxonomy', [
            'label'       => __( 'Taxonomy', FEA_NS ),
            'type'        => Controls_Manager::SELECT,
            'label_block' => true,
            'default'     => 'category',
            'options'     => acf_get_taxonomy_labels(),
            'condition'   => [
            'field_type' => 'taxonomy',
        ],
        ] );
        $repeater->add_control( 'field_taxonomy_appearance', [
            'label'     => __( 'Appearance', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'checkbox',
            'groups'    => [
            'multi'  => [
            'label'   => __( 'Multiple Value', FEA_NS ),
            'options' => [
            'checkbox'     => __( 'Checkboxes', FEA_NS ),
            'multi_select' => __( 'Multi Select', FEA_NS ),
        ],
        ],
            'single' => [
            'label'   => __( 'Single Value', FEA_NS ),
            'options' => [
            'radio'  => __( 'Radio Buttons', FEA_NS ),
            'select' => __( 'Select', FEA_NS ),
        ],
        ],
        ],
            'condition' => [
            'field_type' => [
            'taxonomy',
            'categories',
            'tags',
            'product_categories',
            'product_tags'
        ],
        ],
        ] );
        $repeater->add_control( 'field_add_term', [
            'label'        => __( 'Add Term', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => [
            'taxonomy',
            'categories',
            'tags',
            'product_categories',
            'product_tags'
        ],
        ],
        ] );
        $repeater->add_control( 'set_as_username', [
            'label'        => __( 'Set as username', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => 'email',
        ],
        ] );
        $repeater->add_control( 'change_slug', [
            'label'        => __( 'Change Slug', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'description'  => __( 'WARNING: allowing your users to change term slugs might affect your existing urls and their SEO rating', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => 'term_name',
        ],
        ] );
        $repeater->add_control( 'allow_edit', [
            'label'        => __( 'Allow Edit', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'description'  => __( 'WARNING: allowing your users to change their username might affect your existing urls and their SEO rating', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => 'username',
        ],
        ] );
        $repeater->add_control( 'force_edit_password', [
            'label'        => __( 'Force Edit', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type' => 'password',
        ],
        ] );
        $repeater->add_control( 'edit_password', [
            'label'       => __( 'Edit Password Button', FEA_NS ),
            'label_block' => true,
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Edit Password', FEA_NS ),
            'placeholder' => __( 'Edit Password', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type'           => [ 'password' ],
            'force_edit_password!' => 'true',
        ],
        ] );
        $repeater->add_control( 'cancel_edit_password', [
            'label'       => __( 'Cancel Edit Button', FEA_NS ),
            'label_block' => true,
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Cancel', FEA_NS ),
            'placeholder' => __( 'Cancel', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type'           => [ 'password' ],
            'force_edit_password!' => 'true',
        ],
        ] );
        if ( class_exists( 'woocommerce' ) ) {
            $this->inventory_controls( $repeater );
        }
        $repeater->add_control( 'recaptcha_version', [
            'label'     => __( 'Version', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'v2' => __( 'Version 2', FEA_NS ),
            'v3' => __( 'Version 3', FEA_NS ),
        ],
            'default'   => 'v2',
            'condition' => [
            'field_type' => 'recaptcha',
        ],
        ] );
        $repeater->add_control( 'recaptcha_site_key', [
            'label'       => __( 'Site Key', FEA_NS ),
            'label_block' => true,
            'type'        => Controls_Manager::TEXT,
            'dynamic'     => [
            'active' => true,
        ],
            'default'     => get_option( 'frontend_admin_google_recaptcha_site' ),
            'condition'   => [
            'field_type' => 'recaptcha',
        ],
        ] );
        $repeater->add_control( 'recaptcha_secret_key', [
            'label'       => __( 'Secret Key', FEA_NS ),
            'label_block' => true,
            'type'        => Controls_Manager::TEXT,
            'dynamic'     => [
            'active' => true,
        ],
            'default'     => get_option( 'frontend_admin_google_recaptcha_secret' ),
            'condition'   => [
            'field_type' => 'recaptcha',
        ],
        ] );
        $repeater->add_control( 'recaptcha_note', [
            'show_label' => false,
            'type'       => \Elementor\Controls_Manager::RAW_HTML,
            'raw'        => '<br>' . __( 'If you don\'t already have a site key and a secret, you may generate them here:', FEA_NS ) . ' <a href="https://www.google.com/recaptcha/admin"> reCaptcha API Admin </a>',
            'condition'  => [
            'field_type' => 'recaptcha',
        ],
        ] );
        $repeater->add_control( 'field_styles_heading', [
            'label'     => __( 'Styles', FEA_NS ),
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );
        /* 		$repeater->add_control(
        			'field_label_styles',
        			[
        				'label' => __( 'Label', FEA_NS ),
        				'type' => \Elementor\Controls_Manager::HEADING,
        			]
        		);	
        		$repeater->add_control(
        			'label_spacing',
        			[
        				'label' => __( 'Spacing', 'elementor-pro' ),
        				'type' => Controls_Manager::SLIDER,
        				'default' => [
        					'size' => 0,
        				],
        				'range' => [
        					'px' => [
        						'min' => 0,
        						'max' => 60,
        					],
        				],
        				'selectors' => [
        					'body.rtl {{WRAPPER}} {{CURRENT_ITEM}} .acf-form-fields.-left .acf-field label' => 'padding-left: {{SIZE}}{{UNIT}};',
        					// for the label position = inline option
        					'body:not(.rtl) {{WRAPPER}} {{CURRENT_ITEM}} .acf-form-fields.-left .acf-field label' => 'padding-right: {{SIZE}}{{UNIT}};',
        					// for the label position = inline option
        					'body {{WRAPPER}} {{CURRENT_ITEM}} .acf-form-fields.-top .acf-field label' => 'padding-bottom: {{SIZE}}{{UNIT}};',
        					// for the label position = above option
        				],
        			]
        		);
        
        		$repeater->add_control(
        			'label_text_color',
        			[
        				'label' => __( 'Text Color', 'elementor-pro' ),
        				'type' => Controls_Manager::COLOR,
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-field label, {{WRAPPER}} .acf-field label' => 'color: {{VALUE}};',
        				],
        			]
        		);
        
        		$repeater->add_control(
        			'mark_required_color',
        			[
        				'label' => __( 'Mark Color', 'elementor-pro' ),
        				'type' => Controls_Manager::COLOR,
        				'default' => '',
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-required' => 'color: {{COLOR}};',
        				],
        				'condition' => [
        					'show_mark_required' => 'yes',
        				],
        			]
        		);
        
        		$repeater->add_group_control(
        			Group_Control_Typography::get_type(),
        			[
        				'name' => 'label_typography',
        				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .acf-field label',
        			]
        		);
        
        		$repeater->add_control(
        			'field_input_styles',
        			[
        				'label' => __( 'Input', FEA_NS ),
        				'type' => \Elementor\Controls_Manager::HEADING,
        			]
        		);	
        
        		$repeater->add_control(
        			'field_text_color',
        			[
        				'label' => __( 'Text Color', 'elementor-pro' ),
        				'type' => Controls_Manager::COLOR,
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}} input' => 'color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} textarea' => 'color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} select' => 'color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} span.select2-selection__rendered' => 'color: {{VALUE}};',
        				],
        			]
        		);
        		$repeater->add_control(
        			'field_placeholder_text_color',
        			[
        				'label' => __( 'Placeholder Text Color', 'elementor-pro' ),
        				'type' => Controls_Manager::COLOR,
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}} input::placeholder' => 'color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} textarea::placeholder' => 'color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} select::placeholder' => 'color: {{VALUE}};',
        				],
        			]
        		);
        
        		$repeater->add_group_control(
        			Group_Control_Typography::get_type(),
        			[
        				'name' => 'field_typography',
        				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} input, {{WRAPPER}} {{CURRENT_ITEM}} textarea, {{WRAPPER}} {{CURRENT_ITEM}} select, {{WRAPPER}} .select2-selection__rendered, {{WRAPPER}} .input-subgroup label',
        			]
        		);
        
        		$repeater->add_control(
        			'field_background_color',
        			[
        				'label' => __( 'Background Color', 'elementor-pro' ),
        				'type' => Controls_Manager::COLOR,
        				'default' => '#ffffff',
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}} input' => 'background-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} textarea' => 'background-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input select' => 'background-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input .select2-selection' => 'background-color: {{VALUE}};',
        				],
        			]
        		);
        		
        		$repeater->add_responsive_control(
        			'field_text_padding',
        			[
        				'label' => __( 'Padding', 'elementor-pro' ),
        				'type' => Controls_Manager::DIMENSIONS,
        				'size_units' => ['px', '%', 'em'],
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}} input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input select *' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input .select2-selection' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        				],
        			]
        		);		
        		$repeater->add_control(
        			'field_border_styles',
        			[
        				'label' => __( 'Border', FEA_NS ),
        				'type' => \Elementor\Controls_Manager::HEADING,
        			]
        		);	
        		$repeater->add_control(
        			'field_border_color',
        			[
        				'label' => __( 'Border Color', 'elementor-pro' ),
        				'type' => Controls_Manager::COLOR,
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}}  input' => 'border-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input select' => 'border-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input .select2-selection' => 'border-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input::before' => 'border-color: {{VALUE}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} textarea' => 'border-color: {{VALUE}};',
        				],
        				'separator' => 'before',
        			]
        		);
        
        		$repeater->add_control(
        			'field_border_width',
        			[
        				'label' => __( 'Border Width', 'elementor-pro' ),
        				'type' => Controls_Manager::DIMENSIONS,
        				'placeholder' => '1',
        				'size_units' => ['px'],
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}}  input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input .select2-selection' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',		
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input textarea' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        				],
        			]
        		);
        
        		$repeater->add_control(
        			'field_border_radius',
        			[
        				'label' => __( 'Border Radius', 'elementor-pro' ),
        				'type' => Controls_Manager::DIMENSIONS,
        				'size_units' => ['px', '%'],
        				'selectors' => [
        					'{{WRAPPER}} {{CURRENT_ITEM}}  input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        					'{{WRAPPER}} {{CURRENT_ITEM}} .acf-input .select2-selection' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
        					'{{WRAPPER}} {{CURRENT_ITEM}} textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        				],
        			]
        		);
        
         */
        $repeater->add_responsive_control( 'field_width', [
            'label'               => __( 'Width', FEA_NS ) . ' (%)',
            'type'                => Controls_Manager::NUMBER,
            'min'                 => 10,
            'max'                 => 100,
            'default'             => 100,
            'required'            => true,
            'device_args'         => [
            Controls_Stack::RESPONSIVE_TABLET => [
            'max'      => 100,
            'required' => false,
        ],
            Controls_Stack::RESPONSIVE_MOBILE => [
            'default'  => 100,
            'required' => false,
        ],
        ],
            'min_affected_device' => [
            Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
            Controls_Stack::RESPONSIVE_TABLET  => Controls_Stack::RESPONSIVE_TABLET,
        ],
            'selectors'           => [
            '{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}}%',
        ],
            'condition'           => [
            'field_type!' => [ 'step' ],
            'endpoint!'   => 'true',
        ],
        ] );
        $repeater->add_responsive_control( 'field_margin', [
            'label'               => __( 'Margin', 'elementor-pro' ),
            'type'                => Controls_Manager::DIMENSIONS,
            'size_units'          => [ '%', 'px', 'em' ],
            'default'             => [
            'unit'     => '%',
            'top'      => 'o',
            'bottom'   => 'o',
            'left'     => 'o',
            'right'    => 'o',
            'isLinked' => 'false',
        ],
            'isLinked'            => 'false',
            'min_affected_device' => [
            Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
            Controls_Stack::RESPONSIVE_TABLET  => Controls_Stack::RESPONSIVE_TABLET,
        ],
            'selectors'           => [
            '{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
            'condition'           => [
            'field_type!' => [ 'step' ],
            'endpoint!'   => 'true',
        ],
        ] );
        $repeater->add_responsive_control( 'field_padding', [
            'label'               => __( 'Padding', 'elementor-pro' ),
            'type'                => Controls_Manager::DIMENSIONS,
            'size_units'          => [ '%', 'px', 'em' ],
            'default'             => [
            'top'      => 'o',
            'bottom'   => 'o',
            'left'     => 'o',
            'right'    => 'o',
            'isLinked' => 'false',
            'unit'     => '%',
        ],
            'min'                 => 0,
            'isLinked'            => 'false',
            'min_affected_device' => [
            Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
            Controls_Stack::RESPONSIVE_TABLET  => Controls_Stack::RESPONSIVE_TABLET,
        ],
            'selectors'           => [
            '{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
            'condition'           => [
            'field_type!' => [ 'step' ],
            'endpoint!'   => 'true',
        ],
        ] );
        $repeater->add_control( 'recaptcha_theme', [
            'label'     => __( 'Version', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'light' => __( 'Light', FEA_NS ),
            'dark'  => __( 'Dark', FEA_NS ),
        ],
            'default'   => 'light',
            'condition' => [
            'field_type'        => 'recaptcha',
            'recaptcha_version' => 'v2',
        ],
        ] );
        $repeater->add_control( 'recaptcha_size', [
            'label'     => __( 'Version', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [
            'normal'  => __( 'Normal', FEA_NS ),
            'compact' => __( 'Compact', FEA_NS ),
        ],
            'default'   => 'normal',
            'condition' => [
            'field_type'        => 'recaptcha',
            'recaptcha_version' => 'v2',
        ],
        ] );
        $repeater->add_control( 'recaptcha_hide_logo', [
            'label'        => __( 'Hide Logo', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'field_type'        => 'recaptcha',
            'recaptcha_version' => 'v3',
        ],
        ] );
        
        if ( class_exists( 'woocommerce' ) ) {
            $repeater->add_control( 'attributes_sub_fields', [
                'show_label' => false,
                'type'       => Controls_Manager::RAW_HTML,
                'raw'        => '<button class="sub-fields-open edit-icon" type="button" data-type="attribute">
						<span class="elementor-repeater__add-button__text">' . __( 'Manage Fields', FEA_NS ) . '</span>
					</button>',
                'condition'  => [
                'field_type' => 'attributes',
            ],
            ] );
            $repeater->add_control( 'variations_sub_fields', [
                'show_label' => false,
                'type'       => Controls_Manager::RAW_HTML,
                'raw'        => '<button class="sub-fields-open edit-icon" type="button" data-type="variable">
						<span class="elementor-repeater__add-button__text">' . __( 'Manage Fields', FEA_NS ) . '</span>
					</button>',
                'condition'  => [
                'field_type' => 'variations',
            ],
            ] );
        }
        
        $widget->add_control( 'fields_selection', [
            'show_label'  => false,
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'condition'   => [
            'admin_forms_select' => '',
        ],
            'title_field' => '<span style="text-transform: capitalize;">{{{ field_type.replace(/_/g, " ") }}}</span>',
            'default'     => array(),
            'separator'   => 'after',
        ] );
        
        if ( class_exists( 'woocommerce' ) && isset( $widget->form_defaults['custom_fields_save'] ) ) {
            $save_action = $widget->form_defaults['custom_fields_save'];
            
            if ( $save_action == 'all' || $save_action == 'product' ) {
                $repeater = new \Elementor\Repeater();
                $repeater->add_control( 'field_type', [
                    'type'    => Controls_Manager::HIDDEN,
                    'default' => '',
                ] );
                $repeater->add_control( 'field_label_on', [
                    'label'        => __( 'Show Label', FEA_NS ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', FEA_NS ),
                    'label_off'    => __( 'No', FEA_NS ),
                    'return_value' => 'true',
                    'dynamic'      => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'label', [
                    'label'     => __( 'Label', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'condition' => [
                    'field_label_on' => 'true',
                ],
                    'dynamic'   => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'instructions', [
                    'label'   => __( 'Instructions', FEA_NS ),
                    'type'    => Controls_Manager::TEXTAREA,
                    'dynamic' => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'placeholder', [
                    'label'     => __( 'Placeholder', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => 'name',
                ],
                ] );
                $repeater->add_control( 'products_page', [
                    'label'     => __( 'Products Page', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => 'locations',
                ],
                ] );
                $repeater->add_control( 'for_variations', [
                    'label'     => __( 'Placeholder', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => 'locations',
                ],
                ] );
                $repeater->add_control( 'button_label', [
                    'label'     => __( 'Button Text', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => 'custom_terms',
                ],
                ] );
                $widget->add_control( 'attribute_fields', [
                    'show_label'    => false,
                    'type'          => Controls_Manager::REPEATER,
                    'fields'        => $repeater->get_controls(),
                    'prevent_empty' => true,
                    'item_actions'  => [
                    'add'       => false,
                    'duplicate' => false,
                    'remove'    => false,
                    'sort'      => false,
                ],
                    'default'       => [
                    [
                    'field_type'     => 'name',
                    'field_label_on' => 'true',
                    'label'          => __( 'Name', FEA_NS ),
                    'instructions'   => '',
                    'placeholder'    => __( 'Name', FEA_NS ),
                ],
                    [
                    'field_type'     => 'locations',
                    'field_label_on' => '',
                    'label'          => __( 'Locations', FEA_NS ),
                    'instructions'   => '',
                    'products_page'  => __( 'Visible on the product page', FEA_NS ),
                    'for_variations' => __( 'Used for variations', FEA_NS ),
                ],
                    [
                    'field_type'     => 'custom_terms',
                    'field_label_on' => 'true',
                    'label'          => __( 'Value(s)', FEA_NS ),
                    'instructions'   => '',
                    'button_label'   => __( 'Add Value', FEA_NS ),
                ],
                    [
                    'field_type'     => 'global_terms',
                    'field_label_on' => 'true',
                    'label'          => __( 'Terms', FEA_NS ),
                    'instructions'   => '',
                    'button_label'   => __( 'Add Value', FEA_NS ),
                ]
                ],
                    'title_field'   => '<span style="text-transform: capitalize;">{{{ field_type.replace(/_/g, " ") }}}</span>',
                    'condition'     => [
                    'admin_forms_select' => '',
                ],
                ] );
                $repeater = new \Elementor\Repeater();
                $repeater->add_control( 'field_type', [
                    'label'       => __( 'Field Type', FEA_NS ),
                    'type'        => Controls_Manager::SELECT,
                    'label_block' => true,
                    'placeholder' => __( 'Select Type', FEA_NS ),
                    'groups'      => array(
                    'basic'     => array(
                    'label'   => __( 'Product', FEA_NS ),
                    'options' => array(
                    'description' => __( 'Description', FEA_NS ),
                    'image'       => __( 'Image', FEA_NS ),
                    'price'       => __( 'Price', FEA_NS ),
                    'sale_price'  => __( 'Sale Price', FEA_NS ),
                    'sku'         => __( 'SKU', FEA_NS ),
                ),
                ),
                    'inventory' => array(
                    'label'   => __( 'Product Inventory', FEA_NS ),
                    'options' => array(
                    'stock_status'     => __( 'Stock Status', FEA_NS ),
                    'manage_stock'     => __( 'Manage Stock', FEA_NS ),
                    'stock_quantity'   => __( 'Stock Quantity', FEA_NS ),
                    'allow_backorders' => __( 'Allow Backorders', FEA_NS ),
                ),
                ),
                ),
                ] );
                $repeater->add_control( 'field_label_on', [
                    'label'        => __( 'Show Label', FEA_NS ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', FEA_NS ),
                    'label_off'    => __( 'No', FEA_NS ),
                    'return_value' => 'true',
                    'default'      => 'true',
                    'dynamic'      => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'label', [
                    'label'     => __( 'Label', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'condition' => [
                    'field_label_on' => 'true',
                ],
                    'dynamic'   => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'instructions', [
                    'label'   => __( 'Instructions', FEA_NS ),
                    'type'    => Controls_Manager::TEXTAREA,
                    'dynamic' => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'default_value', [
                    'label'     => __( 'Default Value', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'sku', 'description' ],
                ],
                ] );
                $repeater->add_control( 'default_number_value', [
                    'label'     => __( 'Default Value', FEA_NS ),
                    'type'      => Controls_Manager::NUMBER,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price' ],
                ],
                ] );
                $repeater->add_control( 'default_image_value', [
                    'label'     => __( 'Default Featured Image', FEA_NS ),
                    'type'      => \Elementor\Controls_Manager::MEDIA,
                    'condition' => [
                    'field_type' => [ 'image' ],
                ],
                ] );
                $repeater->add_control( 'placeholder', [
                    'label'     => __( 'Placeholder', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'sku', 'description' ],
                ],
                ] );
                $repeater->add_control( 'number_placeholder', [
                    'label'     => __( 'Placeholder', FEA_NS ),
                    'type'      => Controls_Manager::NUMBER,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price' ],
                ],
                ] );
                $repeater->add_control( 'prepend', [
                    'label'     => __( 'Prepend', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price', 'sku' ],
                ],
                ] );
                $repeater->add_control( 'save_prepend', [
                    'label'     => __( 'Save Prepend', FEA_NS ),
                    'type'      => Controls_Manager::SWITCHER,
                    'label_on'  => __( 'Yes', FEA_NS ),
                    'label_off' => __( 'No', FEA_NS ),
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price', 'sku' ],
                    'prepend!'   => '',
                ],
                ] );
                $repeater->add_control( 'append', [
                    'label'     => __( 'Append', FEA_NS ),
                    'type'      => Controls_Manager::TEXT,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price', 'sku' ],
                ],
                ] );
                $repeater->add_control( 'save_append', [
                    'label'     => __( 'Save Append', FEA_NS ),
                    'type'      => Controls_Manager::SWITCHER,
                    'label_on'  => __( 'Yes', FEA_NS ),
                    'label_off' => __( 'No', FEA_NS ),
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price', 'sku' ],
                    'append!'    => '',
                ],
                ] );
                $repeater->add_control( 'minimum', [
                    'label'     => __( 'Minimum Value', FEA_NS ),
                    'type'      => Controls_Manager::NUMBER,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price' ],
                ],
                ] );
                $repeater->add_control( 'maximum', [
                    'label'     => __( 'Maximum Value', FEA_NS ),
                    'type'      => Controls_Manager::NUMBER,
                    'dynamic'   => [
                    'active' => true,
                ],
                    'condition' => [
                    'field_type' => [ 'price', 'sale_price' ],
                ],
                ] );
                $repeater->add_control( 'required', [
                    'label'        => __( 'Required', FEA_NS ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', FEA_NS ),
                    'label_off'    => __( 'No', FEA_NS ),
                    'return_value' => 'true',
                    'dynamic'      => [
                    'active' => true,
                ],
                ] );
                $repeater->add_control( 'hidden', [
                    'label'        => __( 'Hidden', FEA_NS ),
                    'type'         => Controls_Manager::SWITCHER,
                    'label_on'     => __( 'Yes', FEA_NS ),
                    'label_off'    => __( 'No', FEA_NS ),
                    'return_value' => 'true',
                ] );
                $repeater->add_control( 'disabled', [
                    'label'        => __( 'Disabled', FEA_NS ),
                    'type'         => Controls_Manager::SWITCHER,
                    'description'  => __( 'This will prevent users from editing the field and the data will not be sent.', FEA_NS ),
                    'label_on'     => __( 'Yes', FEA_NS ),
                    'label_off'    => __( 'No', FEA_NS ),
                    'return_value' => 'true',
                ] );
                $this->inventory_controls( $repeater );
                $variable_fields = array(
                    'description',
                    'image',
                    'price',
                    'sale_price',
                    'sku',
                    'stock_status',
                    'manage_stock',
                    'stock_quantity',
                    'allow_backorders'
                );
                $default_vfs = array();
                foreach ( $variable_fields as $field_type ) {
                    $field_label = ucwords( str_replace( "_", " ", $field_type ) );
                    $default_vfs[] = [
                        'field_type'     => $field_type,
                        'field_label_on' => 'true',
                        'required'       => '',
                        'label'          => __( $field_label, FEA_NS ),
                        'instructions'   => '',
                    ];
                }
                $widget->add_control( 'variable_fields', [
                    'show_label'    => false,
                    'type'          => Controls_Manager::REPEATER,
                    'fields'        => $repeater->get_controls(),
                    'prevent_empty' => true,
                    'default'       => $default_vfs,
                    'item_actions'  => [
                    'add'       => false,
                    'duplicate' => true,
                    'remove'    => true,
                    'sort'      => true,
                ],
                    'title_field'   => '<span style="text-transform: capitalize;">{{{ field_type.replace(/_/g, " ") || \'' . __( 'Select Field Type', FEA_NS ) . '\'}}}</span>',
                    'condition'     => [
                    'admin_forms_select' => '',
                ],
                ] );
            }
        
        }
    
    }
    
    public function inventory_controls( $repeater )
    {
        $repeater->add_control( 'ui_on', [
            'label'     => __( 'On Text', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Yes', 'woocommerce' ),
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => [
            'manage_stock',
            'sold_individually',
            'virtual',
            'downloadable'
        ],
        ],
        ] );
        $repeater->add_control( 'ui_off', [
            'label'     => __( 'Off Text', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'No', 'woocommerce' ),
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => [ 'manage_stock', 'sold_individually' ],
        ],
        ] );
        $repeater->add_control( 'stock_choices', [
            'show_label' => false,
            'type'       => Controls_Manager::RAW_HTML,
            'seperator'  => 'before',
            'raw'        => "<h3>Choices</h3>",
            'condition'  => [
            'field_type' => 'stock_status',
        ],
        ] );
        $repeater->add_control( 'instock', [
            'label'     => __( 'In stock', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'In stock', 'woocommerce' ),
            'required'  => true,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => 'stock_status',
        ],
        ] );
        $repeater->add_control( 'outofstock', [
            'label'     => __( 'Out of stock', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Out of stock', 'woocommerce' ),
            'required'  => true,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => 'stock_status',
        ],
        ] );
        $repeater->add_control( 'backorder', [
            'label'     => __( 'On backorder', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'On backorder', 'woocommerce' ),
            'required'  => true,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => 'stock_status',
        ],
        ] );
        $repeater->add_control( 'backorder_choices', [
            'show_label' => false,
            'type'       => Controls_Manager::RAW_HTML,
            'seperator'  => 'before',
            'raw'        => "<h4>Choices</h4>",
            'condition'  => [
            'field_type' => 'allow_backorders',
        ],
        ] );
        $repeater->add_control( 'do_not_allow', [
            'label'     => __( 'Do not allow', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Do not allow', 'woocommerce' ),
            'required'  => true,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => 'allow_backorders',
        ],
        ] );
        $repeater->add_control( 'notify', [
            'label'     => __( 'Notify', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Allow, but notify customers', 'woocommerce' ),
            'required'  => true,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => 'allow_backorders',
        ],
        ] );
        $repeater->add_control( 'allow', [
            'label'     => __( 'Allow', 'woocommerce' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Allow', 'woocommerce' ),
            'required'  => true,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'field_type' => 'allow_backorders',
        ],
        ] );
    }
    
    public function register_step_controls( $repeater, $widget, $first = false )
    {
        $repeater->add_control( 'form_title', [
            'label'       => __( 'Step Title', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => $widget->form_defaults['form_title'],
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => 'step',
        ],
        ] );
        $repeater->add_control( 'step_tab_text', [
            'label'       => __( 'Step Tab Text', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => $widget->form_defaults['form_title'],
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => 'step',
        ],
        ] );
        if ( !$first ) {
            $repeater->add_control( 'prev_button_text', [
                'label'       => __( 'Previous Button', FEA_NS ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Previous', FEA_NS ),
                'placeholder' => __( 'Previous', FEA_NS ),
                'dynamic'     => [
                'active' => true,
            ],
                'condition'   => [
                'field_type' => 'step',
            ],
            ] );
        }
        $repeater->add_control( 'next_button_text', [
            'label'       => __( 'Next Button', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Next', FEA_NS ),
            'placeholder' => __( 'Next', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'field_type' => 'step',
        ],
        ] );
        $repeater->end_controls_tabs();
    }
    
    public function multi_step_settings( $widget )
    {
        $post_type_choices = feadmin_get_post_type_choices();
        $widget->add_control( 'validate_steps', [
            'label'        => __( 'Validate Each Step', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
        ] );
        $widget->add_control( 'steps_display', [
            'label'       => __( 'Steps Display', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'default'     => [ 'tabs' ],
            'multiple'    => 'true',
            'options'     => [
            'tabs'    => __( 'Tabs', FEA_NS ),
            'counter' => __( 'Counter', FEA_NS ),
        ],
        ] );
        $widget->add_control( 'responsive_description', [
            'raw'             => __( 'Responsive visibility will take effect only on preview or live page, and not while editing in Elementor.', 'elementor' ),
            'type'            => Controls_Manager::RAW_HTML,
            'content_classes' => 'elementor-descriptor',
        ] );
        $widget->add_control( 'steps_tabs_display', [
            'label'       => __( 'Step Tabs Display', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => 'true',
            'default'     => [ 'desktop', 'tablet' ],
            'multiple'    => 'true',
            'options'     => [
            'desktop' => __( 'Desktop', FEA_NS ),
            'tablet'  => __( 'Tablet', FEA_NS ),
            'phone'   => __( 'Mobile', FEA_NS ),
        ],
            'condition'   => [
            'steps_display' => 'tabs',
        ],
        ] );
        $widget->add_control( 'tabs_align', [
            'label'     => __( 'Tabs Position', 'elementor' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'horizontal',
            'options'   => [
            'horizontal' => __( 'Top', 'elementor' ),
            'vertical'   => __( 'Side', 'elementor' ),
        ],
            'condition' => [
            'steps_display' => 'tabs',
        ],
        ] );
        $widget->add_control( 'steps_counter_display', [
            'label'       => __( 'Step Counter Display', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => 'true',
            'default'     => [ 'desktop', 'tablet', 'phone' ],
            'multiple'    => 'true',
            'options'     => [
            'desktop' => __( 'Desktop', FEA_NS ),
            'tablet'  => __( 'Tablet', FEA_NS ),
            'phone'   => __( 'Mobile', FEA_NS ),
        ],
            'condition'   => [
            'steps_display' => 'counter',
        ],
        ] );
        $widget->add_control( 'counter_prefix', [
            'label'       => __( 'Counter Prefix', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => __( 'Step ', FEA_NS ),
            'default'     => __( 'Step ', FEA_NS ),
            'dynamic'     => [
            'active' => true,
        ],
            'condition'   => [
            'steps_display' => 'counter',
        ],
        ] );
        $widget->add_control( 'counter_suffix', [
            'label'     => __( 'Counter Suffix', FEA_NS ),
            'type'      => Controls_Manager::TEXT,
            'dynamic'   => [
            'active' => true,
        ],
            'condition' => [
            'steps_display' => 'counter',
        ],
        ] );
        $widget->add_control( 'step_number', [
            'label'        => __( 'Step Number in Tabs', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'show', FEA_NS ),
            'label_off'    => __( 'hide', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'steps_display' => 'tabs',
        ],
        ] );
        $widget->add_control( 'tab_links', [
            'label'        => __( 'Link to Step in Tabs', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
            'condition'    => [
            'steps_display' => 'tabs',
        ],
        ] );
    }
    
    public function submit_limit_setting( $widget )
    {
        $widget->add_control( 'limit_reached', [
            'label'       => __( 'Limit Reached Message', FEA_NS ),
            'type'        => Controls_Manager::SELECT,
            'label_block' => true,
            'default'     => 'show_message',
            'options'     => [
            'show_message'   => __( 'Limit Message', FEA_NS ),
            'custom_content' => __( 'Custom Content', FEA_NS ),
            'show_nothing'   => __( 'Nothing', FEA_NS ),
        ],
        ] );
        $widget->add_control( 'limit_submit_message', [
            'label'       => __( 'Reached Limit Message', FEA_NS ),
            'type'        => Controls_Manager::TEXTAREA,
            'label_block' => true,
            'rows'        => 4,
            'default'     => __( 'You have already submitted this form the maximum amount of times that you are allowed', FEA_NS ),
            'placeholder' => __( 'you have already submitted this form the maximum amount of times that you are allowed', FEA_NS ),
            'condition'   => [
            'limit_reached' => 'show_message',
        ],
        ] );
        $widget->add_control( 'limit_submit_content', [
            'label'       => __( 'Reached Limit Content', FEA_NS ),
            'type'        => Controls_Manager::WYSIWYG,
            'placeholder' => 'You have already submitted this form the maximum amount of times that you are allowed',
            'label_block' => true,
            'render_type' => 'none',
            'condition'   => [
            'limit_reached' => 'custom_content',
        ],
        ] );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'rule_name', [
            'label'       => __( 'Rule Name', FEA_NS ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => __( 'Rule Name', FEA_NS ),
            'label_block' => true,
        ] );
        $repeater->add_control( 'allowed_submits', [
            'label'   => __( 'Allowed Submissions', FEA_NS ),
            'type'    => Controls_Manager::NUMBER,
            'default' => '',
        ] );
        $repeater->add_control( 'limit_to_everyone', [
            'label'        => __( 'Limit For Everyone', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'return_value' => 'true',
        ] );
        $user_roles = feadmin_get_user_roles();
        $repeater->add_control( 'limit_by_role', [
            'label'       => __( 'Limit By Role', FEA_NS ),
            'type'        => Controls_Manager::SELECT2,
            'label_block' => true,
            'multiple'    => true,
            'default'     => 'subscriber',
            'options'     => $user_roles,
            'condition'   => [
            'limit_to_everyone' => '',
        ],
        ] );
        
        if ( !class_exists( 'ElementorPro\\Modules\\QueryControl\\Module' ) ) {
            $repeater->add_control( 'limit_by_user', [
                'label'       => __( 'Limit By User', FEA_NS ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __( '18', FEA_NS ),
                'description' => __( 'Enter a commma seperated list of user ids', FEA_NS ),
                'condition'   => [
                'limit_to_everyone' => '',
            ],
            ] );
        } else {
            $repeater->add_control( 'limit_by_user', [
                'label'        => __( 'Limit By User', FEA_NS ),
                'type'         => Query_Module::QUERY_CONTROL_ID,
                'label_block'  => true,
                'autocomplete' => [
                'object'  => Query_Module::QUERY_OBJECT_USER,
                'display' => 'detailed',
            ],
                'multiple'     => true,
                'condition'    => [
                'limit_to_everyone' => '',
            ],
            ] );
        }
        
        $widget->add_control( 'limiting_rules', [
            'label'         => __( 'Add Limiting Rules', FEA_NS ),
            'type'          => Controls_Manager::REPEATER,
            'fields'        => $repeater->get_controls(),
            'prevent_empty' => false,
            'default'       => [ [
            'rule_name' => __( 'Subscribers', FEA_NS ),
        ] ],
            'title_field'   => '{{{ rule_name }}}',
        ] );
    }
    
    public function __construct()
    {
        add_action( FEA_PREFIX . '/display_section', [ $this, 'register_display_section' ] );
        add_action( FEA_PREFIX . '/fields_controls', [ $this, 'fields_controls' ] );
    }

}
new ContentTab();