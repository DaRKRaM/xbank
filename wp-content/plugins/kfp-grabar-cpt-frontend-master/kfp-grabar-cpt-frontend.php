<?php
/**
 * Plugin Name: KFP Grabar CPT Frontend
 * Description: Plugin de ejemplo para el artículo "Grabar Custom Post Type desde el frontend de WordPress". Utiliza el shortcode [kfp_gcf_form_idea]
 * Author: KungFuPress
 * Author URI: https://kungfupress.com
 * Version: 0.1
 *
 * @package kfp_cpt
 */

// Evita que se llame directamente a este fichero sin pasar por WordPress.
defined( 'ABSPATH' ) || die();
// Crea el CPT al activar el plugin.
add_action( 'init', 'kfp_cpt_idea', 10 );
// Crea el shortcode para mostrar el formulario de propuesta de ideas.
add_shortcode( 'kfp_gcf_form_idea', 'kfp_gcf_form_idea' );
// Agrega los action hooks para grabar el formulario:
// El primero para usuarios logeados y el otro para el resto.
// Lo que viene tras admin_post_ y admin_post_nopriv_ tiene que coincidir con -
// el value del campo input con name "action" del formulario.
add_action( 'admin_post_kfp-gcf-grabar-idea', 'kfp_gcf_grabar_idea' );
add_action( 'admin_post_nopriv_kfp-gcf-grabar-idea', 'kfp_gcf_grabar_idea' );

/**
 * Crea el CPT Idea con lo mínimo que se despacha en CPT
 *
 * @return void
 */
function kfp_cpt_idea() {
	$args = array(
		'public' => true,
		'label'  => 'Ideas',
	);
	register_post_type( 'idea', $args );
}

/**
 * Muestra el formulario para proponer ideas desde el frontend
 *
 * @return string
 */
function kfp_gcf_form_idea() {
	if ( isset( $_GET['kfp_gcf_texto_aviso'] ) ) {
		echo "<h4>" . $_GET['kfp_gcf_texto_aviso'] . "</h4>";
	}
	ob_start();
	?>
	<form name="idea"action="<?php echo esc_url(admin_url('admin-post.php')); ?>" 
		method="post" id="kfp-gcf-form-grabar-idea">
		<?php wp_nonce_field('kfp-gcf-form', 'kfp-gcf-form-nonce'); ?>
		<input type="hidden" name="action" value="kfp-gcf-grabar-idea">
		<input type="hidden" name="kfp-gcf-url-origen" 
			value="<?php echo home_url( add_query_arg(array())); ?>">
		<p>
			<label for="kfp-gcf-title">Idea</label>
			<input type="text" name="kfp-gcf-title" id="kfp-gcf-title" 
				placeholder="Pon un título breve pero descriptivo a tu idea">
		</p>
		<p>
			<label for="kfp-gcf-content">Descripción</label>
			<textarea name="kfp-gcf-content" id="kfp-gcf-content" 
				placeholder="Aquí puedes explicar mejor tu idea"></textarea>
		</p>
		<p>
			<input type="submit" name="kfp-gcf-submit" value="Enviar idea">
		</p>
	</form>
	<?php
	return ob_get_clean();
}

/**
 * Procesa el formulario para proponer ideas desde el frontend
 *
 * @return void
 */
function kfp_gcf_grabar_idea()
{
	if (filter_has_var(INPUT_POST, 'kfp-gcf-url-origen')) {
		$url_origen = filter_input(INPUT_POST, 'kfp-gfc-url-origen', FILTER_SANITIZE_URL);
	}

	if(empty($_POST['kfp-gcf-title']) || empty($_POST['kfp-gcf-content'])
		|| !wp_verify_nonce($_POST['kfp-gcf-form-nonce'], 'kfp-gcf-form')) {
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
		'post_title'     => filter_input(INPUT_POST, 'kfp-gcf-title', FILTER_SANITIZE_STRING),
		'post_content'   => filter_input(INPUT_POST, 'kfp-gcf-content', FILTER_SANITIZE_STRING),
		'post_type'      => 'dinero',
		'post_status'    => 'publish',
		'comment_status' => 'closed',
		'ping_status'    => 'closed'
	);

	// Esta variable $post_id contiene el ID del nuevo registro 
	// Nos vendría de perlas para grabar los metadatos
	$post_id = wp_insert_post($args);
	add_post_meta( $post_id, 'canti', filter_input(INPUT_POST, 'kfp-gcf-content', FILTER_SANITIZE_STRING), true );
	add_post_meta( $post_id, 'cantidad', filter_input(INPUT_POST, 'kfp-gcf-content', FILTER_SANITIZE_STRING), true );
	

	$aviso = "success";
	$texto_aviso = "Has registrado tu idea correctamente. ¡Gracias!";
	wp_redirect(
		esc_url_raw(
			add_query_arg(
				array(
					'kfp_gcf_aviso'       => $aviso,
					'kfp_gcf_texto_aviso' => $texto_aviso,
				),
				$url_origen
			)
		)
	);
	exit();
}
