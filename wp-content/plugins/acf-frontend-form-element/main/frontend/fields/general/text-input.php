<?php

if( ! class_exists('acf_field_text_input') ) :

class acf_field_text_input extends acf_field_text {
	
	
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
		$this->name = 'text_input';
		$this->label = __("Text",FEA_NS);
        $this->public = false;
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
		);
		
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
		$html = '';
		
		// Prepend text.
		if( $field['prepend'] !== '' ) {
			$field['class'] .= ' acf-is-prepended';
			$html .= '<div class="acf-input-prepend">' . acf_esc_html($field['prepend']) . '</div>';
		}
		
		// Append text.
		if( $field['append'] !== '' ) {
			$field['class'] .= ' acf-is-appended';
			$html .= '<div class="acf-input-append">' . acf_esc_html($field['append']) . '</div>';
		}
		
		// Input.
		$input_attrs = array( 'type' => 'text' );
		$attr_keys = array( 'id', 'class', 'value', 'placeholder', 'maxlength', 'pattern', 'readonly', 'disabled', 'required' );

		if( empty( $field['sensitive'] ) ) $attr_keys[] = 'name';
		if( ! empty( $field['no_autocomplete'] ) ) $input_attrs['autocomplete'] = 'no';

		if( ! empty( $field['input_data'] ) ){
			foreach( $field['input_data'] as $k => $data ){
				$input_attrs[ 'data-'.$k ] = $data;
			}
		}

		foreach( $attr_keys as $k ) {
			if( isset($field[ $k ]) ) {
				$input_attrs[ $k ] = $field[ $k ];
			}
		}
		$html .= '<div class="acf-input-wrap">' . acf_get_text_input( acf_filter_attrs($input_attrs) ) . '</div>';
		
		// Display.
		echo $html;
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field_settings( $field ) {
		
		// default_value
		acf_render_field_setting( $field, array(
			'label'			=> __('Default Value',FEA_NS),
			'instructions'	=> __('Appears when creating a new post',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'default_value',
			'dynamic_value_choices' => 1,
		));
		
		
		// placeholder
		acf_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text',FEA_NS),
			'instructions'	=> __('Appears within the input',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// prepend
		acf_render_field_setting( $field, array(
			'label'			=> __('Prepend',FEA_NS),
			'instructions'	=> __('Appears before the input',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'prepend',
		));
		
		
		// append
		acf_render_field_setting( $field, array(
			'label'			=> __('Append',FEA_NS),
			'instructions'	=> __('Appears after the input',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'append',
		));
		
		
		// maxlength
		acf_render_field_setting( $field, array(
			'label'			=> __('Character Limit',FEA_NS),
			'instructions'	=> __('Leave blank for no limit',FEA_NS),
			'type'			=> 'number',
			'name'			=> 'maxlength',
		));
		
	}
	
	/**
	 * validate_value
	 *
	 * Validates a field's value.
	 *
	 * @date	29/1/19
	 * @since	5.7.11
	 *
	 * @param	(bool|string) Whether the value is vaid or not.
	 * @param	mixed $value The field value.
	 * @param	array $field The field array.
	 * @param	string $input The HTML input name.
	 * @return	(bool|string)
	 */
	function validate_value( $valid, $value, $field, $input ){
		
		// Check maxlength
		if( $field['maxlength'] && (acf_strlen($value) > $field['maxlength']) ) {
			return sprintf( __('Value must not exceed %d characters', FEA_NS), $field['maxlength'] );
		}
		
		// Return.
		return $valid;
	}
}


// initialize
acf_register_field_type( 'acf_field_text_input' );

endif; // class_exists check

?>