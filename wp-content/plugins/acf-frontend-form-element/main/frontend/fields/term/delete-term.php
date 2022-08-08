<?php

if( ! class_exists('acf_field_delete_term') ) :

class acf_field_delete_term extends acf_field_delete_object {
	
	
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
		$this->name = 'delete_term';
		$this->label = __("Delete Term",FEA_NS);
		$this->category = __( 'Term', FEA_NS );
		$this->object = 'term';
		$this->defaults = array(
			'button_text' 	=> __( 'Delete', FEA_NS ),
			'confirmation_text' => __( 'Are you sure you want to delete this term?', FEA_NS ),
            'field_label_hide'  => 1,
		);
		
	}
	
}


// initialize
acf_register_field_type( 'acf_field_delete_term' );

endif; // class_exists check

?>