<?php

add_action('rest_api_init', 'createJson');

function createJson(){
	register_rest_route('bank/v1','dinero', array(
	'methods' => WP_REST_SERVER::READABLE,
	'callback' => 'dineroresult'));
	}
function dineroresult(){
	$transfers = new WP_Query(array(
		'post_type' => 'dinero'
		));
	$transfersResult = array();
	
	while($transfers->have_posts()){
		$transfers->the_post();
		array_push($transfersResult, array (
			
			'title' => get_the_title(),
			'amount' => pods_field_display('cantidad'),
			'date' => get_the_date()
			));		
			
		}
	
		return $transfersResult;
	}
