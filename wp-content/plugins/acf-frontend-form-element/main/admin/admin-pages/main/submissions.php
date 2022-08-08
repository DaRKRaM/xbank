<?php
namespace Frontend_WP;

use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FEA_Submissions_Settings{
		
	function hide_admin_area_update_action($user_id) {
		$hide_admin = isset( $_POST['hide_admin_area'] );
	  	update_user_meta( $user_id, 'hide_admin_area', $hide_admin );
	}

	public function get_settings_fields( $field_keys ){
		$local_fields = array(
			'frontend_admin_save_submissions' => array(
				'label' => __( 'Save Form Submissions', FEA_NS ),
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '15',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			/* 'frontend_admin_submissions_deletetion' => array(
				'label' => __( 'Delete Submissions After...', FEA_NS ),
				'type' => 'number',
				'min' => 1,
				'instructions' => '',
				'append' => __( 'Days', FEA_NS ),
				'placeholder' => __( 'Never', FEA_NS ),
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'frontend_admin_save_submissions',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '20',
					'class' => '',
					'id' => '',
				),
			), */
		);

		return $local_fields;
	} 
	
	public function __construct() {
		//add_action( 'init', [ $this, 'hide_admin_bar'] );
		
		add_filter( FEA_PREFIX.'/submissions_fields', [ $this, 'get_settings_fields'] );

	}
	
}
new FEA_Submissions_Settings( $this );	