<?php

namespace Frontend_WP\Widgets;

use  Frontend_WP\Plugin ;
use  Frontend_WP\Classes ;
use  Elementor\Controls_Manager ;
use  Elementor\Widget_Base ;
use  ElementorPro\Modules\QueryControl\Module as Query_Module ;
/**
 *
 * @since 1.0.0
 */
class Delete_Post_Widget extends Widget_Base
{
    /**
     * Get widget name.
     *
     * Retrieve acf ele form widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'delete_post';
    }
    
    /**
     * Get widget title.
     *
     * Retrieve acf ele form widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __( 'Trash/Delete Post', FEA_NS );
    }
    
    /**
     * Get widget icon.
     *
     * Retrieve acf ele form widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fas fa-trash-alt frontend-icon';
    }
    
    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the acf ele form widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return array( '' );
    }
    
    /**
     * Register acf ele form widget controls.
     *
     * Adds different input fields to allow the post to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls()
    {
        $this->start_controls_section( 'delete_button_section', [
            'label' => __( 'Trash Button', FEA_NS ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'delete_button_text', [
            'label'       => __( 'Delete Button Text', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Delete', FEA_NS ),
            'placeholder' => __( 'Delete', FEA_NS ),
        ] );
        $this->add_control( 'delete_button_icon', [
            'label' => __( 'Delete Button Icon', FEA_NS ),
            'type'  => Controls_Manager::ICONS,
        ] );
        $this->add_control( 'confirm_delete_message', [
            'label'       => __( 'Confirm Delete Message', FEA_NS ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'The post will be deleted. Are you sure?', FEA_NS ),
            'placeholder' => __( 'The post will be deleted. Are you sure?', FEA_NS ),
        ] );
        $this->add_control( 'show_delete_message', [
            'label'        => __( 'Show Success Message', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', FEA_NS ),
            'label_off'    => __( 'No', FEA_NS ),
            'default'      => 'true',
            'return_value' => 'true',
        ] );
        $this->add_control( 'delete_message', [
            'label'       => __( 'Success Message', FEA_NS ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => __( 'You have deleted this post', FEA_NS ),
            'placeholder' => __( 'You have deleted this post', FEA_NS ),
            'dynamic'     => [
            'active'    => true,
            'condition' => [
            'show_delete_message' => 'true',
        ],
        ],
        ] );
        $this->add_control( 'force_delete', [
            'label'        => __( 'Force Delete', FEA_NS ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'true',
            'return_value' => 'true',
            'description'  => __( 'Whether or not to completely delete the posts right away.' ),
        ] );
        $this->add_control( 'delete_redirect', [
            'label'   => __( 'Redirect After Delete', FEA_NS ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'custom_url',
            'options' => [
            'current'     => __( 'Reload Current Url', FEA_NS ),
            'custom_url'  => __( 'Custom Url', FEA_NS ),
            'referer_url' => __( 'Referer', FEA_NS ),
        ],
        ] );
        $this->add_control( 'redirect_after_delete', [
            'label'         => __( 'Custom URL', FEA_NS ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => __( 'Enter Url Here', FEA_NS ),
            'show_external' => false,
            'dynamic'       => [
            'active' => true,
        ],
            'condition'     => [
            'delete_redirect' => 'custom_url',
        ],
        ] );
        $this->end_controls_section();
        $this->start_controls_section( 'post_section', [
            'label' => __( 'Post', FEA_NS ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );
        fea_instance()->local_actions['post']->action_controls( $this, false, 'delete_post' );
        $this->end_controls_section();
        do_action( FEA_PREFIX . '/permissions_section', $this );
        $this->start_controls_section( 'style_promo_section', [
            'label' => __( 'Styles', FEA_NS ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );
        $this->add_control( 'styles_promo', [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => __( '<p><a target="_blank" href="https://www.frontendform.com/"><b>Go Pro</b></a> to unlock styles.</p>', FEA_NS ),
            'content_classes' => 'acf-fields-note',
        ] );
        $this->end_controls_section();
    }
    
    /**
     * Render acf ele form widget output on the frontend.
     *
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $wg_id = $this->get_id();
        $current_post_id = fea_instance()->elementor->get_current_post_id();
        global  $post ;
        $settings = $this->get_settings_for_display();
        $custom_url = '';
        if ( isset( $settings['redirect_after_delete']['url'] ) ) {
            $custom_url = $settings['redirect_after_delete']['url'];
        }
        $local_field = array(
            'key'                 => 'button_' . $wg_id,
            'name'                => 'button_' . $wg_id,
            'type'                => 'delete_post',
            'label'               => '',
            'field_label_hide'    => 1,
            'button_text'         => $settings['delete_button_text'],
            'button_icon'         => $settings['delete_button_icon']['value'],
            'confirmation_text'   => $settings['confirm_delete_message'],
            'show_delete_message' => $settings['show_delete_message'],
            'delete_message'      => $settings['delete_message'],
            'force_delete'        => $settings['force_delete'],
            'wrapper'             => [
            'class' => '',
            'id'    => '',
            'width' => '',
        ],
            'instructions'        => '',
            'required'            => '',
            'redirect'            => $settings['delete_redirect'],
            'custom_url'          => $custom_url,
        );
        
        if ( feadmin_edit_mode() ) {
            acf_add_local_field( $local_field );
            $field_key = $local_field['key'];
        } else {
            $local_field['key'] = 'field_' . $local_field['key'];
            $field_obj = acf_get_field( $local_field['key'] );
            if ( $field_obj ) {
                $local_field = array_merge( $field_obj, $local_field );
            }
            acf_update_field( $local_field );
            $field_key = $local_field['key'];
        }
        
        $form_args = array(
            'fields' => array( $local_field['key'] ),
        );
        $form_args = $this->get_settings_to_pass( $form_args, $settings );
        fea_instance()->form_display->render_form( $form_args );
    }
    
    public function get_settings_to_pass( $form_args, $settings )
    {
        $settings_to_pass = [
            'who_can_see',
            'by_role',
            'by_user_id',
            'dynamic',
            'dynamic_manager',
            'not_allowed',
            'not_allowed_message',
            'not_allowed_content',
            'save_all_data'
        ];
        $types = array(
            'post',
            'user',
            'term',
            'product'
        );
        foreach ( $types as $type ) {
            $settings_to_pass[] = "save_to_{$type}";
            $settings_to_pass[] = "{$type}_to_edit";
            $settings_to_pass[] = "url_query_{$type}";
            $settings_to_pass[] = "{$type}_select";
        }
        foreach ( $settings_to_pass as $setting ) {
            if ( isset( $settings[$setting] ) ) {
                $form_args[$setting] = $settings[$setting];
            }
        }
        return $form_args;
    }

}