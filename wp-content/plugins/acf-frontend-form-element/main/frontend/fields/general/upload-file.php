<?php

if( ! class_exists('acf_field_upload_file') ) :

class acf_field_upload_file extends acf_field_file {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'upload_file';
		$this->label = __("Upload File",FEA_NS);
		$this->public = false;
		$this->defaults = array(
			'return_format'	=> 'array',
			'preview_size'	=> 'thumbnail',
			'library'		=> 'all',
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> '',
            'button_text'   => __( 'Add File', FEA_NS ),
            'no_file_text'  => __( 'No file selected', FEA_NS ),
		);

		//actions
		add_action('wp_ajax_acf/fields/upload_file/add_attachment',				array($this, 'ajax_add_attachment'));
		add_action('wp_ajax_nopriv_acf/fields/upload_file/add_attachment',		array($this, 'ajax_add_attachment'));
	

		$single_file = array( 'file', 'image', 'upload_file', 'upload_image', 'featured_image', 'main_image', 'site_logo' );
		foreach( $single_file as $type ){
			add_filter( 'acf/prepare_field/type=' .$type,  [ $this, 'prepare_image_or_file_field'], 5 );
			add_filter( 'acf/update_value/type=' .$type, [ $this, 'update_attachment_value'], 8, 3 );
			add_action( 'acf/render_field_settings/type=' .$type,  [ $this, 'upload_button_text_setting'] );	
		}

		if( defined( 'HAPPYFILES_VERSION' ) ){
			$file_fields = array( 'image', 'file', 'gallery', 'featured_image', 'main_image', 'product_images', 'upload_file', 'upload_image' );
			foreach( $file_fields as $type ){
				add_action( 'acf/render_field_settings/type=' .$type,  [ $this, 'file_folders_setting'] );
			}				
			add_filter( 'ajax_query_attachments_args', [ $this, 'happy_files_folder'] );
		}
	
	}

	function file_folders_setting( $field ) {
		acf_render_field_setting( $field, array(
			'label'			=> __('Happy Files Folder',FEA_NS),
			'instructions'	=> __('Limit the media library choice to specific Happy Files Categories',FEA_NS),
			'type'			=> 'radio',
			'name'			=> 'happy_files_folder',
			'layout'		=> 'horizontal',
			'default_value' => 'all',
			'choices' 		=> $this->get_happy_files_folders(),
		));	
	}

	function get_happy_files_folders(){
		$folders = ['all' => __( 'All Folders', FEA_NS ) ];
		$taxonomies = get_terms( array(
			'taxonomy' => 'happyfiles_category',
			'hide_empty' => false
		) );
	
		if ( empty($taxonomies) ) {
			return $folders;
		}
		
		foreach( $taxonomies as $category ) {
			$folders[ $category->name ] = ucfirst( esc_html( $category->name ) );	
		}
	
		return $folders;
	}

	function happy_files_folder( $query ) {
		if( empty( $query['_acfuploader'] ) ) {
			return $query;
		}
		
		// load field
		$field = acf_get_field( $query['_acfuploader'] );
		if( !$field ) {
			return $query;
		}
		
		if( !isset( $field['happy_files_folder'] ) || $field['happy_files_folder'] == 'all' ){
			return $query;
		}

		
		if( isset( $query['tax_query'] ) ){
			$tax_query = $query['tax_query'];
		}else{
			$tax_query = [];
		}
		
		$tax_query[] = array(
			'taxonomy' => 'happyfiles_category',
			'field' => 'name',
			'terms' => $field['happy_files_folder'],
		);
		$query['tax_query'] = $tax_query;
		
		return $query;
	}


	function ajax_add_attachment() {

		$args = acf_parse_args( $_POST, array(
			'field_key'		=> '',
			'nonce'			=> '',
		));
		
		// validate nonce
		if( !wp_verify_nonce($args['nonce'], 'fea_form') ) {
			wp_send_json_error( __( 'Invalid Nonce', FEA_NS ) );			
		} 
				
		// bail early if no attachments
		if( empty($_FILES['file']['name']) ) {		
			wp_send_json_error( __( 'Missing file name', FEA_NS ) );
		}

		//TO dos: validate file types, sizes, and dimensions
		//Add loading bar for each image

		if( isset( $args['field_key'] ) ){
			$field = get_field_object( $args['field_key'] );
		}else{
			wp_send_json_error( __( 'Invalid Key', FEA_NS ) );
		}
		
		// get errors
		$errors = acf_validate_attachment( $_FILES['file'], $field, 'upload' );
		
		// append error
		if( !empty($errors) ) {				
			$data = implode("\n", $errors);
			wp_send_json_error( $data );
		}		

		/* Getting file name */
		$upload = wp_upload_bits( $_FILES['file']['name'], null, file_get_contents( $_FILES['file']['tmp_name'] ) );

		$wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );
	
		$wp_upload_dir = wp_upload_dir();
	
		$attachment = array(
			'guid' => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename( $upload['file'] )),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		
		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
		update_post_meta( $attach_id, 'hide_from_lib', 1 );

		require_once(ABSPATH . 'wp-admin/includes/image.php');
	
		$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		$return_data = array( 'id' => $attach_id, 'title' => $attachment['post_title'] );

		if( $wp_filetype['type'] == 'application/pdf' )	$return_data['src'] = wp_mime_type_icon( $wp_filetype['type'] );
		
		wp_send_json_success( $return_data );
    }

	function prepare_image_or_file_field( $field ) {
		$uploader = acf_get_setting('uploader');		
		$field_type = 'file';
		if( in_array( $field['type'], [ 'image', 'featured_image', 'main_image', 'site_logo' ] ) ) $field_type = 'image';
		
		if( $uploader == 'basic' || ! empty( $field['button_text'] ) || ! empty( $field['no_file_text'] ) ) {
			$field['wrapper']['data-field_type'] = $field_type;
			$field['type'] = 'upload_' . $field_type;
		}
		
		if( $uploader == 'basic' ){
			$field['wrapper']['class'] .= ' acf-uploads';
		}
		$field['wrapper']['class'] .= ' image-field';
		
		return $field;
	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		if( empty( $field['field_type'] ) ){
			$field['field_type'] = 'file';
		}

		// vars
		$uploader = acf_get_setting( 'uploader' );

		// allow custom uploader
		$uploader = acf_maybe_get( $field, 'uploader', $uploader );

		// enqueue
		if ( $uploader == 'wp' ) {
			acf_enqueue_uploader();
		}

		// vars
		$o = array(
			'icon'     => '',
			'title'    => '',
			'url'      => '',
			'filename' => '',
			'filesize' => '',
		);

		$div = array(
			'class'           => 'acf-file-uploader',
			'data-library'    => $field['library'],
			'data-mime_types' => $field['mime_types'],
			'data-uploader'   => $uploader,
		);
		if( ! empty( $field['button_text'] ) ){
			$button_text = $field['button_text'];
		}else{
			$button_text = __( 'Add File', FEA_NS );	
		}

		// has value?
		if ( $field['value'] ) {

			$attachment = acf_get_attachment( $field['value'] );
			if ( $attachment ) {

				// has value
				$div['class'] .= ' has-value';

				// update
				$o['icon']     = $attachment['icon'];
				$o['title']    = $attachment['title'];
				$o['url']      = $attachment['url'];
				$o['filename'] = $attachment['filename'];
				if ( $attachment['filesize'] ) {
					$o['filesize'] = size_format( $attachment['filesize'] );
				}
			}
		}

		?>
<div <?php acf_esc_attr_e( $div ); ?>>
		<?php
		acf_hidden_input(
			array(
				'name'      => $field['name'],
				'value'     => $field['value'],
				'data-name' => 'id',
			)
		);
		?>
<div class="show-if-value file-wrap">
	<div class="file-icon">
		<img data-name="icon" src="<?php echo esc_url( $o['icon'] ); ?>" alt=""/>
	</div>
	<div class="file-info">
		<p>
			<strong data-name="title"><?php echo esc_html( $o['title'] ); ?></strong>
		</p>
		<p>
			<strong><?php _e( 'File name', 'acf' ); ?>:</strong>
			<a data-name="filename" href="<?php echo esc_url( $o['url'] ); ?>" target="_blank"><?php echo esc_html( $o['filename'] ); ?></a>
		</p>
		<p>
			<strong><?php _e( 'File size', 'acf' ); ?>:</strong>
			<span data-name="filesize"><?php echo esc_html( $o['filesize'] ); ?></span>
		</p>
	</div>
	<div class="acf-actions -hover">
		<?php if ( $uploader != 'basic' ) : ?>
		<a class="acf-icon -pencil dark" data-name="edit" href="#" title="<?php _e( 'Edit', 'acf' ); ?>"></a>
		<?php endif; ?>
		<a class="acf-icon -cancel dark" data-name="remove" href="#" title="<?php _e( 'Remove', 'acf' ); ?>"></a>
	</div>
</div>
<div class="frontend-admin-hidden uploads-progress"><div class="percent">0%</div><div class="bar"></div></div>
<div class="hide-if-value">
		<?php 
			$empty_text =  __( 'No file selected', FEA_NS );
			if( isset( $field['no_file_text'] ) ){
				$empty_text = $field['no_file_text'];
			}
		if ( $uploader == 'basic' ) : ?>
			<?php if ( $field['value'] && ! is_numeric( $field['value'] ) ) : ?>
				<div class="acf-error-message"><p><?php echo acf_esc_html( $field['value'] ); ?></p></div>
			<?php endif; ?>
		
		<label class="acf-basic-uploader file-drop">
			<?php 
			$input_args = array( 'name' => 'upload_file_input', 'id' => $field['id'], 'class' => 'file-preview' );
			if( $field['field_type'] == 'image' ) $input_args['accept'] = "image/*"; 
			acf_file_input( $input_args ); ?>
			<div class="file-custom">
				<?php echo $empty_text; ?>
				<div class="acf-button button">
					<?php echo $button_text; ?>
				</div>
			</div>
		</label>
		
	<?php else : ?>
		<p><?php echo $empty_text; ?> <a data-name="add" class="acf-button button" href="#"><?php echo $button_text; ?></a></p>
		
	<?php endif; ?>
	
</div>
</div>
		<?php

	}
	
	
	function upload_button_text_setting( $field ) {
		acf_render_field_setting( $field, array(
			'label'			=> __('No File Text'),
			'name'			=> 'no_file_text',
			'type'			=> 'text',
			'placeholder'	=> __( 'No file selected', FEA_NS ),
		) );
		acf_render_field_setting( $field, array(
			'label'			=> __('Button Text'),
			'name'			=> 'button_text',
			'type'			=> 'text',
			'placeholder'	=> __( 'Add File', FEA_NS ),
		) );
	}
	

	function update_attachment_value( $value, $post_id = false, $field = false ){									
		if( is_numeric( $post_id ) ){  				
			remove_filter( 'acf/update_value/type=' . $field['type'], [ $this, 'update_attachment_value'], 8, 3 );
			$value = (int) $value;
			$post = get_post( $post_id );
			if( wp_is_post_revision( $post ) ){
				$post_id = $post->post_parent;
			}
			acf_connect_attachment_to_post( $value, $post_id );

			add_filter( 'acf/update_value/type=' . $field['type'], [ $this, 'update_attachment_value'], 8, 3 );
		}	
		delete_post_meta( $value, 'hide_from_lib' );
		return $value;
	}
		
}


// initialize
acf_register_field_type( 'acf_field_upload_file' );

endif; // class_exists check

?>