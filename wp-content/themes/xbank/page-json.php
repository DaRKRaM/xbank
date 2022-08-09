<?php


add_action('rest_api_init', 'createJson');

function createJson(){
	register_rest_route('bank/v1','dinero', array(
	'methods' => WP_REST_SERVER::READABLE,
	'callback' => 'dineroresult'));
	}
function dineroresult(){
	$transfers = new WP^_Query(array(
		'post_type' => 'dinero'
		));
		return $transfers->posts;
	}


