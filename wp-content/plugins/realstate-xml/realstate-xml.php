<?php
/*
Plugin Name: Real Estate XML
Plugin URI: http://www.3de.es
Description: Export Properties to a XML file. Build for WPResidence Theme.
Version: beta
Author: Fernando de Lera - 3DE MEDIA
*/

// Include mfp-functions.php, use require_once to stop the script if mfp-functions.php is not found
function my_admin_menu() {
		add_menu_page(
			__( 'Real Estate XML', 'realstate-xml' ),
			__( 'Real Estate XML', 'realstate-xml' ),
			'manage_options',
			'real-estate-xml',
			'my_admin_page_contents',
			'dashicons-schedule',
			3
		);
	}

add_action( 'admin_menu', 'my_admin_menu' );

function my_css() {
echo '<style>';
include( plugin_dir_path( __FILE__ ) . '/css/rsx-style.css' );
	echo '</style>';

}
add_action('admin_head', 'my_css');

function my_admin_page_contents() { ?>
			<h1>
				<?php esc_html_e( 'Bienvenido a Real Estate XML - IndieProp. ', 'realstate-xml' ); ?>
			</h1>
<div class="topini"><p>Esta aplicación te permite obtener un archivos XML o JSON con todas las propiedades para poder subirlo a tus plataformas inmobiliarias.</p></div>
<div class="contenepack">
	<div><img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>images/witei.png" alt="witei" /></div>
	<div class="plataforma">Archivo para Witei</div>
	<div class="txt">Formato: Fichero XML</div>
	<div class="conlink"><a href="https://www.indieprop.com/exml/" class="linko">Descargar</a></div>
	
</div>
<div class="contenepack">
	<div><img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); ?>images/idealista.png" alt="Idealista" /></div>
	<div class="plataforma">Archivo para idealista</div>
	<div class="txt">Formato: Fichero JSON</div>
	<div class="conlink"><a href="https://www.indieprop.com/json/" class="linko" target="_blank">Crear Nuevo</a></div>
	
</div>
<div class="faldi"><p>Plugin para Wordpress +5.8 desarrollado por <a href="http://www.3de.es" target="_blank">3DE MEDIA</a></p></div>
<?php 
}
