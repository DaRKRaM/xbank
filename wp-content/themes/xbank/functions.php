<?php 
	 add_action( 'wp_enqueue_scripts', 'xbank_enqueue_styles', 11 );
	 function xbank_enqueue_styles() {
 		  
		 wp_enqueue_style( 'child-style', get_stylesheet_uri() );
	 }

?>