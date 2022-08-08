<?php

namespace Frontend_WP;

use  Elementor\Core\Base\Module ;

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

class FEA_Google_API_Settings
{
    /**
     * Redirect non-admin users to home page
     *
     * This function is attached to the ‘admin_init’ action hook.
     */
    public function get_settings_fields( $field_keys )
    {
        $local_fields = array(
            'frontend_admin_google_maps_api' => array(
            'label'        => __( 'Google Maps API Key', FEA_NS ),
            'type'         => 'text',
            'instructions' => '',
            'required'     => 0,
            'wrapper'      => array(
            'width' => '50.1',
            'class' => '',
            'id'    => '',
        ),
        ),
        );
        return $local_fields;
    }
    
    public function frontend_admin_update_maps_api()
    {
        acf_update_setting( 'google_api_key', get_option( 'frontend_admin_google_maps_api' ) );
    }
    
    public function __construct()
    {
        add_filter( FEA_PREFIX . '/apis_fields', [ $this, 'get_settings_fields' ] );
        add_action( 'acf/init', [ $this, 'frontend_admin_update_maps_api' ] );
    }

}
new FEA_Google_API_Settings( $this );