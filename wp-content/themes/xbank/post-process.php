<?php 

<?php
/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package HelloElementor
 Template: inicio
 */


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
		<?php
echo "all right";
	$allposts= get_posts( array('post_type'=>'dinero','numberposts'=>-1) );
foreach ($allposts as $eachpost) {
  wp_delete_post( $eachpost->ID, true );
}
echo "done";
?>
	</div>
    <?php
	
	get_footer(); ?>
