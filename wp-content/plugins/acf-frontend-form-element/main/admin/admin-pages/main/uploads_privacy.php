<?php
namespace Frontend_WP;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FEA_Uploads_Privacy_Settings{
	
	public function get_name() {
		return 'uploads_privacy';
	}
	
	
	function filter_media_author( $query ){
    	if ( get_option( 'filter_media_author' ) == '1' ) {
			$user_id = get_current_user_id();
			if ( $user_id && ! current_user_can( 'activate_plugins' ) ) {
				$query['author'] = $user_id;
			}
		}
		return $query;
	}

	public function get_settings_fields( $field_keys ){
		$default = get_option( 'local_avatar' ) ? get_option( 'local_avatar' ) : 'none';

		$local_fields = array(
			'filter_media_author' => array(
				'label' => __( 'Media Uploads Privacy', FEA_NS ),
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		);

		return $local_fields;
	} 
	
	public function __construct() {	
		add_filter( 'ajax_query_attachments_args', [ $this, 'filter_media_author'] );
		add_filter( FEA_PREFIX.'/uploads_privacy_fields', [ $this, 'get_settings_fields'] );
	}
	
}
new FEA_Uploads_Privacy_Settings( $this );