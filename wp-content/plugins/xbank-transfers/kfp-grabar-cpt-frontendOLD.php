<?php
/**
 * Plugin Name:Xbank
 * Description: Plugin de ejemplo para el artículo "Grabar Custom Post Type desde el frontend de WordPress". Utiliza el shortcode [xbank_form]
 * Author: KungFuPress
 * Author URI: https://kungfupress.com
 * Version: 0.1
 *
 * @package kfp_cpt
 */

// Evita que se llame directamente a este fichero sin pasar por WordPress.
defined( 'ABSPATH' ) || die();

// Crea el shortcode para mostrar el formulario de propuesta de ideas.
add_shortcode( 'xbank_form', 'xbank_form' );
// Agrega los action hooks para grabar el formulario:
// El primero para usuarios logeados y el otro para el resto.
// Lo que viene tras admin_post_ y admin_post_nopriv_ tiene que coincidir con -
// el value del campo input con name "action" del formulario.
add_action( 'admin_post_xbank_save', 'xbank_save' );
add_action( 'admin_post_nopriv_xbank_save', 'xbank_save' );


/**
 * Muestra el formulario para proponer ideas desde el frontend
 *
 * @return string
 */
function xbank_form() {
	if ( isset( $_GET['kfp_gcf_texto_aviso'] ) ) {
		echo "<h4>" . $_GET['kfp_gcf_texto_aviso'] . "</h4>";
	}
	ob_start();
	$previous_money = get_the_author_meta('money', $author_id);
	
   
   $argsen = array(
            'post_type' => 'dinero',
			'post_status' => 'publish', 
    'orderby' => 'date',
    'posts_per_page' => '-1',
    'order' => 'DESC');  

  $loop = new WP_Query( $argsen );
	
  if( $loop->have_posts() ):
	echo '<div>';
	echo '<div class="row50 titi">Account Holder: '. get_the_author_meta('display_name', $author_id) .' saldo: '. get_the_author_meta('money', $author_id) .'</div>';
	echo '<div class="row50">';
  	while( $loop->have_posts() ): $loop->the_post(); global $post;
	// Campo Cantidad
	$usrcant = pods_field_display( 'cantidad' );	
	echo '<div class="lineamon">'; 
	echo '<span class="canti">' . $usrcant .'</span><span class="concept">'. get_the_title() . '</span> <span class="date">'. get_the_date('Y-m-d') . '</span> <span class="devo"><a href="post-process/?pid=' . $post->ID .'" id="' . $post->ID .'">[withdraw]</a></span>';		
    echo '</div>';
	endwhile; ?>
</div></div>
<?php	
  endif;
   ?><div class="row50 titi" style="margin-top:30px;">
   <h2>Add Funds to your account</h2>
	<form name="xbank"action="<?php echo esc_url(admin_url('admin-post.php')); ?>" 
		method="post" id="xbank-form">
		<?php wp_nonce_field('xbank-form', 'xbank-form-nonce'); ?>
		<input type="hidden" name="action" value="xbank_save">
		<input type="hidden" name="kfp-gcf-url-origen" 
			value="<?php echo home_url( add_query_arg(array())); ?>">
            <input type="hidden" name="saldo" id="saldo" value="<?php echo $previous_money; ?>">
		<p>
			<label for="xbank-title">Concept</label>
			<input type="text" name="xbank-title" id="xbank-title" 
				placeholder="Pon un Concepto">
		</p>
		<p>
			<label for="xbank-content">Amount</label>
			<input type="number" name="xbank-content" id="xbank-content" 
				placeholder="Amount in Euros"></textarea>
		</p>
		<p>
			<input type="submit" name="kfp-gcf-submit" value="Add Funds">
		</p>
	</form></div> 
    
    

<!-- Modal HTML embedded directly into document -->
<div id="ex1" class="modal">
  <p>Withdraw amount <?php echo $usrcant ?> ? Are you sure</p>
  <form name="xbankdelete" action="/post-process" 
		method="post" id="xbank-delete">
        <input type="hidden" name="postid" id="postid" value="95">
	<input type="hidden" name="saldo" id="saldo" value="<?php echo $previous_money; ?>">
		
		<p>
			<input type="submit" name="kfp-gcf-submit" value="Delete Funds">
		</p>
	</form>
  <a href="/post-process" rel="modal:close">Yes </a> | 
  <a href="#" rel="modal:close">Cancel</a>
</div>

	<?php
	return ob_get_clean();
}

/**
 * Form process to Add funds
 *
 * @return void
 */
function xbank_save()
{
	if (filter_has_var(INPUT_POST, 'kfp-gcf-url-origen')) {
		$url_origen = filter_input(INPUT_POST, 'kfp-gfc-url-origen', FILTER_SANITIZE_URL);
	}

	if(empty($_POST['xbank-title']) || empty($_POST['xbank-content'])
		|| !wp_verify_nonce($_POST['xbank-form-nonce'], 'xbank-form')) {
		$aviso = "error";
		$texto_aviso = "Por favor, rellena los contenidos requeridos del formulario";
		wp_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'kfp_gcf_aviso' => $aviso,
						'kfp_gcf_texto_aviso' => $texto_aviso,
					),
					$url_origen
				)
			)
		);
		exit();
	}

	$args = array(
		'post_title'     => filter_input(INPUT_POST, 'xbank-title', FILTER_SANITIZE_STRING),
		'post_content'   => filter_input(INPUT_POST, 'xbank-content', FILTER_SANITIZE_STRING),
		'post_type'      => 'dinero',
		'post_status'    => 'publish',
		'comment_status' => 'closed',
		'ping_status'    => 'closed'
	);

	// Esta variable $post_id contiene el ID del nuevo registro 
	// Nos vendría de perlas para grabar los metadatos
	$post_id = wp_insert_post($args);
	//add_post_meta( $post_id, 'canti', filter_input(INPUT_POST, 'kfp-gcf-content', FILTER_SANITIZE_STRING), true );
	add_post_meta( $post_id, 'cantidad', filter_input(INPUT_POST, 'xbank-content', FILTER_SANITIZE_STRING), true );
	// update User money
	// try to find some `favorite_coffee` user meta 
$previous_money = $_POST['saldo'];
$new_money = $_POST['xbank-content'];
echo $previous_money;
$total = $previous_money + $new_money;
echo $total;

	add_user_meta( 1, 'money', $total, true );

	$aviso = "success";
	$texto_aviso = "Has registrado tu idea correctamente. ¡Gracias!";
	echo "ok";
	wp_redirect('https://www.aijer.org/');
	exit();
}

/**
 * Form process to Widthdraw funds
 *
 * @return void
 */
function xbank_delete()
{
	echo "all right";
	wp_delete_post(91, true); 
}
