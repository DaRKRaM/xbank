<?php

if( ! class_exists('acf_field_url_upload') ) :

class acf_field_url_upload extends acf_field_file {
	
	
	/*
	*  initialize
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
		$this->name = 'url_upload';
		$this->label = __("File",FEA_NS);
		$this->public = false;
		$this->defaults = array(
			'return_format'	=> 'array',
			'library' 		=> 'all',
			'min_size'		=> 0,
			'max_size'		=> 0,
			'mime_types'	=> '',
            'no_file_text'  => __('No file selected',FEA_NS), 
            'button_text'  => __('Add File',FEA_NS), 
            'destination'  => '', 
		);
		
		// filters
		add_filter('get_media_item_args', array($this, 'get_media_item_args'));
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
		
		// vars
		$uploader = acf_get_setting('uploader');
		
		
		// allow custom uploader
		$uploader = acf_maybe_get($field, 'uploader', $uploader);
		
		
		// enqueue
		if( $uploader == 'wp' ) {
			acf_enqueue_uploader();
		}
		
		if( empty( $field['no_file_text'] ) ) $field['no_file_text'] = '';
		if( empty( $field['button_text'] ) ) $field['button_text'] = __('Add File',FEA_NS);
        if( empty( $field['destination'] ) ) $field['destination'] = '';

		// vars
		$o = array(
			'icon'		=> '',
			'title'		=> '',
			'url'		=> '',
			'filename'	=> '',
			'filesize'	=> ''
		);
		

		$div = array(
			'class'				=> 'acf-file-uploader',
			'data-library' 		=> $field['library'],
			'data-mime_types'	=> $field['mime_types'],
			'data-uploader'		=> $uploader,
            'data-destination'  => $field['destination'],
		);
		
		
		// has value?
		if( $field['value'] ) {
			
			$attachment = acf_get_attachment($field['value']);
			if( $attachment ) {
				
				// has value
				$div['class'] .= ' has-value';
				
				// update
				$o['icon'] = $attachment['icon'];
				$o['title']	= $attachment['title'];
				$o['url'] = $attachment['url'];
				$o['filename'] = $attachment['filename'];
				if( $attachment['filesize'] ) {
					$o['filesize'] = size_format($attachment['filesize']);
				}
			}		
		}

        
?>
<div <?php acf_esc_attr_e( $div ); ?>>
	<div class="show-if-value file-wrap">
		<div class="file-info">
			<p>
				<strong data-name="title"><?php echo esc_html($o['title']); ?></strong>
			</p>
			<p>
				<strong><?php _e('File name', FEA_NS); ?>:</strong>
				<a data-name="filename" href="<?php echo esc_url($o['url']); ?>" target="_blank"><?php echo esc_html($o['filename']); ?></a>
			</p>
			<p>
				<strong><?php _e('File size', FEA_NS); ?>:</strong>
				<span data-name="filesize"><?php echo esc_html($o['filesize']); ?></span>
			</p>
		</div>
		<div class="acf-actions -hover">
			<?php if( $uploader != 'basic' ): ?>
			<a class="acf-icon -pencil dark" data-name="edit" href="#" title="<?php _e('Edit', FEA_NS); ?>"></a>
			<?php endif; ?>
			<a class="acf-icon -cancel dark" data-name="remove" href="#" title="<?php _e('Remove', FEA_NS); ?>"></a>
		</div>
	</div>
	<div class="hide-if-value">
		<?php if( $uploader == 'basic' ): ?>
			
			<?php if( $field['value'] && !is_numeric($field['value']) ): ?>
				<div class="acf-error-message"><p><?php echo acf_esc_html($field['value']); ?></p></div>
			<?php endif; ?>
			
			<label class="acf-basic-uploader">
				<?php acf_file_input(array( 'name' => $field['name'], 'id' => $field['id'] )); ?>
			</label>
			
		<?php else: ?>
			
			<p><?php echo $field['no_file_text'] ?> <a data-name="add" class="acf-button button" href="#"><?php echo $field['button_text'] ?></a></p>
			
		<?php endif; ?>
		
	</div>
</div>
<?php
		
	}
	
}


// initialize
acf_register_field_type( 'acf_field_url_upload' );

endif; // class_exists check

?>