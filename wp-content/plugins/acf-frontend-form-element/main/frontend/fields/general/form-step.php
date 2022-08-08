<?php

if( ! class_exists('acf_field_form_step') ) :

class acf_field_form_step extends acf_field {	

	function initialize() {
		$this->name = 'form_step';
       // $this->public = false;
		$this->label = __("Step",FEA_NS);
		$this->category = __( 'Form', FEA_NS );
		$this->defaults = array(
			'next_button_text' => '',
			'prev_button_text' => __( 'Previous', FEA_NS ),
		);
	}

	function render_field_settings( $field ){
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Previous Button Text', FEA_NS ),
			'type'			=> 'text',
			'name'			=> 'prev_button_text',
			'placeholder' 	=> __( 'Previous', FEA_NS ),
		));
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Next Button Text', FEA_NS ),
			'type'			=> 'text',
			'name'			=> 'next_button_text',
			'placeholder' 	=> __( 'Next', FEA_NS ),
		));
	
	}
	
}


// initialize
acf_register_field_type( 'acf_field_form_step' );

endif; // class_exists check

?>