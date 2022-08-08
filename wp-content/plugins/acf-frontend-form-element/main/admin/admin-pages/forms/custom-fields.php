<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( function_exists('acf_add_local_field') ):

acf_add_local_field(
	array(
		'key' => 'frontend_admin_title',
		'label' => __( 'Title', FEA_NS ),
		'required' => true,
		'name' => 'frontend_admin_title',
		'type' => 'post_title',
	)
);	
acf_add_local_field(
	array(
		'key' => 'frontend_admin_term_name',
		'label' => __( 'Name', FEA_NS ),
		'required' => true,
		'name' => 'frontend_admin_term_name',
		'type' => 'term_name',
	)
);	

acf_add_local_field(
	array(
		'key' => 'acf_frontend_custom_term',
		'label' => __( 'Value', FEA_NS ),
		'required' => true,
		'name' => 'acf_frontend_custom_term',
		'type' => 'text',
	)
);	


endif;
