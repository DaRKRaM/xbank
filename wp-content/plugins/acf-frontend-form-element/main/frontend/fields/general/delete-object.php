<?php

if( ! class_exists('acf_field_delete_object') ) :

class acf_field_delete_object extends acf_field {	

	function initialize() {
		$this->public = false;
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
		$confirm = ! empty( $field['confirmation_text'] ) ? $field['confirmation_text'] : __( 'Are you sure you want to delete this '.$this->object.'?', FEA_NS ); 		

		if( ! empty( $field['button_icon'] ) ){
			$field['button_text'] = '<i class="'.$field['button_icon'].'"></i> ' . $field['button_text'];
		}
		// vars
		$m = '<button type="button" class="fea-delete-button button button-primary" data-confirm="' .$confirm. '" data-state="delete">' .$field['button_text']. '</button>';
				
		// wptexturize (improves "quotes")
		$m = wptexturize( $m );

		echo $m;
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	function load_field( $field ) {		
		// remove name to avoid caching issue
		$field['name'] = '';
		
		// remove instructions
		$field['instructions'] = '';
		
		// remove required to avoid JS issues
		$field['required'] = 0;
		
		// set value other than 'null' to avoid ACF loading / caching issue
		$field['value'] = false;

		$field['field_label_hide'] = 1;

		$field['no_data_collect'] = 1;
		
		// return
		return $field;
	}

	function prepare_field( $field ){
		if( empty( $GLOBALS['admin_form'][$this->object.'_id'] ) || ! is_numeric( $GLOBALS['admin_form'][$this->object.'_id'] ) ) return false;
		return $field;
	}

	function render_field_settings( $field ){
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Button Text', FEA_NS ),
			'type'			=> 'text',
			'name'			=> 'button_text',
			'class'			=> 'update-label',
		));
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Confirmation Text', FEA_NS ),
			'type'			=> 'text',
			'name'			=> 'confirmation_text',
		));

		if( $this->object == 'user' ){
			$choices = array();
			if( $field['reassign_posts'] ){
				$user = get_user_by( 'id', intval( $field['reassign_posts'] ) );
	
				if( isset( $user->ID ) ){
					echo $user->ID;
					$choices = array( $user->ID => $user->user_login );
				}
			}
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Reassign Posts to...', FEA_NS ),
				'type'			=> 'select',
				'ui'           => 1,
				'ajax'         => 1,
				'allow_null'   => 1,
				'choices'	   => $choices,
				'ajax_action'  => 'acf_frontend/fields/reassign_posts/query',
				'placeholder'  => __( 'Delete Posts', FEA_NS ),
				'name'			=> 'reassign_posts',
			));
		}

		if( $this->object == 'product' || $this->object == 'post' ){
			acf_render_field_setting( $field, array(
				'label'			=> __( 'Skip Trash', FEA_NS ),
				'type'			=> 'true_false',
				'ui' 			=> 1,
				'name'			=> 'force_delete',
			));
		}
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Show Delete Message', FEA_NS ),
			'type'			=> 'true_false',
			'ui' 			=> 1,
			'name'			=> 'show_delete_message',
		));
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Delete Message', FEA_NS ),
			'type'			=> 'textarea',
			'name'			=> 'delete_message',
			'rows'			=> 3,
			'conditions' => array(
                array(
                    array(
                        'field'     => 'show_delete_message',
                        'operator'  => '==',
                        'value'     => 1,
                    ),
                ),
            )
		));
		acf_render_field_setting( $field, array(
			'label'			=> __('Redirect After Delete',FEA_NS),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'redirect',
			'choices'		=> array(
				'current'  => __( 'Reload Current Url', FEA_NS ),
				'custom_url' => __( 'Custom Url', FEA_NS ),
				'referer_url' => __( 'Referer', FEA_NS ),
			),
		));
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Custom Url', FEA_NS ),
			'type'			=> 'url',
			'name'			=> 'custom_url',
			'conditions' => array(
                array(
                    array(
                        'field'     => 'redirect',
                        'operator'  => '==',
                        'value'     => 'custom_url',
                    ),
                ),
            )
		));
	}
	
}


// initialize
acf_register_field_type( 'acf_field_delete_object' );

endif; // class_exists check

?>