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
	$new_amount   = get_post_field( 'cantidad', $pid2 );; // Where 123 is the ID
  $author_id = get_current_user_id();
$previous_money = get_the_author_meta('money', $author_id);
 $total = $previous_money - $new_amount;
  echo '<p>Withdraw amount = '. $new_amount .' Account Balance = '. $previous_money.'</p>';
  echo '<p>Your balance will be = ' . $total.'</p>'; 
 
 if ($_GET['run_func'] == 'yes') {
    myFunction();
} else {
    echo '<div style="padding:40px"><a href=/post-process/?run_func=yes&pid='.$pid2.'>Are You Sure ? YES</a></div>';
	 echo '<div style="padding:40px; background-color: #fafafa;"><a href="https://www.aijer.org"> No, cancel</a></div>';
}

function myFunction() 
{
	$pid2 = $_GET['pid'];
	$new_amount   = get_post_field( 'cantidad', $pid2 );; // Where 123 is the ID
  $author_id = get_current_user_id();
$previous_money = get_the_author_meta('money', $author_id);
 $total = $previous_money - $new_amount;
 $author_id = get_current_user_id();
	add_user_meta( $author_id, 'money', $total, true );
	wp_delete_post( $pid2, true );
    echo 'Withdraw done successfully.';
	wp_redirect('https://www.aijer.org/');
}

?>
	</div>
    <?php
	
	get_footer(); ?>
