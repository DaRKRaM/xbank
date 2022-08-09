<?php 
/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package HelloElementor
 Template: post process
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
$pid2 = $_GET['pid']; 
$new_amount   = get_post_field( 'cantidad', $pid2 ); // Getting data for the withdraw calculation
$author_id = get_current_user_id();
$previous_money = get_the_author_meta('money', $author_id);
$total = $previous_money - $new_amount; // Withdraw calculation
  echo '<div class="row50 titi center"><h4>Confirmation</h4><p>Withdraw amount = '. $new_amount .'<br /> Account Balance = '. $previous_money.'</p>';
  echo '<p>Balance after withdraw = ' . $total.'</p>'; 
 
if ($_GET['run_func'] == 'yes') {
    withdrawFunction();
} else {
    echo '<div style="padding:40px 0px 40px 0px;"><a href=/post-process/?run_func=yes&pid='.$pid2.' class="linkbut">Are You Sure ? YES</a></div>';
	echo '<div style="padding:10px 0px 40px 0px;"><a href="https://www.aijer.org" class="linkgray"> No, cancel</a></div></div>';
}

function withdrawFunction() 
{
$pid2 = $_GET['pid'];
$new_amount   = get_post_field( 'cantidad', $pid2 ); // Getting data for the withdraw calcularion
$author_id = get_current_user_id();
$previous_money = get_the_author_meta('money', $author_id);
$total = $previous_money - $new_amount; // Withdraw calculation
$author_id = get_current_user_id();
	add_user_meta( $author_id, 'money', $total, true );
	wp_delete_post( $pid2, true );
    echo 'Withdraw done successfully.';
	wp_redirect('https://www.aijer.org/');
}
?>
</div>
<?php get_footer(); ?>
