<?php
/**
 * Plugin Name:Xbank
 * Description: Xbank sistema de muestra movimientos bancarios [xbank_form]
 */

defined( 'ABSPATH' ) || die();

// shortcode for the Main Form (account balance + add funds).
add_shortcode( 'xbank_form', 'xbank_form' );
// hooks for saving the form
add_action( 'admin_post_xbank_save', 'xbank_save' );
add_action( 'admin_post_nopriv_xbank_save', 'xbank_save' );


// the Main Balance
function xbank_form() {
	if ( isset( $_GET['kfp_gcf_texto_aviso'] ) ) {
		echo '<div class="row50"><h4>' . $_GET['kfp_gcf_texto_aviso'] . '</h4></div>';
	}
	ob_start();
	if ( is_user_logged_in() ) {
		// get the User and his Money
	$author_id = get_current_user_id();
	$previous_money = get_the_author_meta('money', $author_id);
	
    $args = array(
    'post_type' => 'dinero',
	'post_status' => 'publish',
	'author__in'=> $author_id,
    'orderby' => 'date',
    'posts_per_page' => '-1',
    'order' => 'DESC');  

  $loop = new WP_Query( $args );
	
  if( $loop->have_posts() ):
	echo '<div>';
	echo '<div class="row50 titi"><p><strong>Account Holder:</strong> '. get_the_author_meta('display_name', $author_id) .'<br /><strong>Balance:</strong> '. get_the_author_meta('money', $author_id) .'€</p></div>';
	echo '<div class="row50">';
  	while( $loop->have_posts() ): $loop->the_post(); global $post;
	// Campo Cantidad
	$usrcant = pods_field_display( 'cantidad' );
	$type = pods_field_display( 'withdraw' );
	if ($type == "Yes") { $ttype = "-"; } else { $ttype = ""; }
	
	echo '<div class="lineamon">'; 
	echo '<span class="canti">' . $ttype . $usrcant .'</span><span class="concept">'. get_the_title() . '</span> <span class="date">'. get_the_date('Y-m-d') . '</span>';
	if ($type != "Yes") {
		echo '<span class="devo"><a href="post-process/?pid=' . $post->ID .'" id="' . $post->ID .'">[revoke]</a></span>';
	}
    echo '</div>';
	endwhile;	
	 ?>
</div></div>
<?php	
else :
{
	 echo '<p class="nofondos">You have not Funds on your account.</p>';
}
endif;
// FORM FOR DEPOSITS
   ?><div class="row50 adding">
   <h4>Manage your account</h4>
	<form name="xbank"action="<?php echo esc_url(admin_url('admin-post.php')); ?>" 
		method="post" id="xbank-form">
		<?php wp_nonce_field('xbank-form', 'xbank-form-nonce'); ?>
		<input type="hidden" name="action" value="xbank_save">
		<input type="hidden" name="kfp-gcf-url-origen" 
			value="<?php echo home_url( add_query_arg(array())); ?>">
            <input type="hidden" name="saldo" id="saldo" value="<?php echo $previous_money; ?>">
             <p>
			<label for="xbank-type">Choose: </label></p>
			<p><input type="radio" name="withdraw" value="No" checked="checked" />Add Funds
			<input type='radio' name="withdraw" value="Yes" /> Withdraw
		</p>
		<p>
			<label for="xbank-title">Description</label>
			<input type="text" name="xbank-title" id="xbank-title" 
				placeholder="Write something...">
		</p>
		<p>
			<label for="xbank-content">Amount</label>
			<input type="number" min="0.00" max="10000.00" step="0.01" name="xbank-content" id="xbank-content" 
				placeholder="Amount in Euros">
		</p>
       
		<p>
			<input type="submit" name="kfp-gcf-submit" value="Submit">
		</p>
	</form></div>
	<?php
	} else {
		// IF NOT USER GO TO LOGIN
    echo '<div class="um um-login um-19 uimob500"><h2>Hello visitor!</h2></div>';
	
	echo do_shortcode('[ultimatemember form_id="19"]');
}
	return ob_get_clean();
}

/**
 * Form process to Add funds
 *
 * @return void
 */
function xbank_save()
{
	// Form validation
	if (filter_has_var(INPUT_POST, 'kfp-gcf-url-origen')) {
		$url_origen = filter_input(INPUT_POST, 'kfp-gfc-url-origen', FILTER_SANITIZE_URL);
	}

	if(empty($_POST['xbank-title']) || empty($_POST['xbank-content'])
		|| !wp_verify_nonce($_POST['xbank-form-nonce'], 'xbank-form')) {
		$aviso = "error";
		$texto_aviso = "Por favor, rellena los contenidos requeridos del formulario.";
		wp_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'kfp_gcf_aviso' => $aviso,
						'kfp_gcf_texto_aviso' => $texto_aviso,
					),
					'https://www.aijer.org'
				)
			)
		);
		exit();
	}
// EOF Form validation
// WP QUERY FOR SAVE DINERO CONTENT TYPE
	$args = array(
		'post_title'     => filter_input(INPUT_POST, 'xbank-title', FILTER_SANITIZE_STRING),
		'post_content'   => filter_input(INPUT_POST, 'xbank-content', FILTER_SANITIZE_STRING),
		'post_type'      => 'dinero',
		'post_status'    => 'publish',
		'comment_status' => 'closed',
		'ping_status'    => 'closed'
	);

	$post_id = wp_insert_post($args);  // save 
	add_post_meta( $post_id, 'cantidad', filter_input(INPUT_POST, 'xbank-content', FILTER_SANITIZE_STRING), true );
	// update User's money
	// balances calculations
$actiotype = $_POST['withdraw'];
$previous_money = $_POST['saldo'];
$new_money = $_POST['xbank-content'];

if ($actiotype == "No") { $total = $previous_money + $new_money; } else { $total = $previous_money - $new_money; 
add_post_meta( $post_id, 'withdraw', $actiotype, true ); }

$author_id = get_current_user_id();
	add_user_meta( $author_id, 'money', $total, true );
	$aviso = "success";
	$texto_aviso = "Operation successful!";
	echo "ok";
	wp_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'kfp_gcf_aviso' => $aviso,
						'kfp_gcf_texto_aviso' => $texto_aviso,
					),
					'https://www.aijer.org'
				)
			)
		);
	exit();
}