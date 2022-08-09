<?php

add_action('rest_api_init', 'createJson');

function createJson(){
	
	}


// JSON ARRAY 1
 $arrayjson = array();
$arrayjson['customerCountry'] = 'Spain';
$arrayjson['customerCode'] = 'ilc077ef5bcb04494b428b44d485ea1070a0bcce3b0';
$arrayjson['customerReference'] = 'IndieProp';
$arrayjson['customerSendDate'] = '2021/10/19 16:50:12';


// JSON ARRAY 1 - EOF -
// JSON ARRAY customerProperties
$agent_wid = get_current_user_id();

	
   $args = array(
            'post_type' => 'dinero',
			'post_status' => 'publish',
			'author__in'=> $author_id,
    'orderby' => 'date',
    'posts_per_page' => '-1',
    'order' => 'DESC');  
			
  $loop = new WP_Query( $args );
  if( $loop->have_posts() ):
  while( $loop->have_posts() ): $loop->the_post();	

	// Campos
	$post_id = strval($post->ID);
	$post_date = get_the_date("Y-m-d H:i:s");
	$post_title     = get_post_meta($post->ID, 'title', true);
	$post_cantidad     =  get_post_meta($post->ID, 'cantidad', true);
	
	// AGENTS
	$name                = get_the_author_meta( 'first_name' ,$agent_wid);
		  
// ARRAY CASAS
	$casas[] = array(
   		'PostId'=> $property_id,
		'PostTitle'=> $post_title,
		'PostCantidad'=> $post_cantidad,
		'PostDate'=> $post_date,
		
	);	
  endwhile; 
  endif;

$arrayjson["customerProperties"]=array_filter($casas);
	
$json_string = json_encode(array_filter($arrayjson),JSON_PRETTY_PRINT);
$file = 'ilc077ef5bcb04494b428b44d485ea1070a0bcce3b0.json';
file_put_contents($file, $json_string);




// Sigle - Blog post
// Wp Estate Pack


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<main id="content" <?php post_class( 'site-main' ); ?> role="main">
	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<header class="page-header">
			
		</header>
	<?php endif; ?>
	<div class="page-content">
		<?php the_content(); ?>
		<div class="post-tags">
			<?php the_tags( '<span class="tag-links">' . __( 'Tagged ', 'hello-elementor' ), null, '</span>' ); ?>
		</div>
		<?php wp_link_pages(); ?>
	</div>
    <?php
	
	get_footer(); ?>

