<?php

if( ! class_exists('acf_field_delete_post') ) :

class acf_field_delete_post extends acf_field_delete_object {
	
	
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
		$this->name = 'delete_post';
		$this->label = __("Delete Post",FEA_NS);
		$this->category = __( 'Post', FEA_NS );
		$this->object = 'post';
		$this->defaults = array(
			'button_text' 	=> __( 'Delete', FEA_NS ),
			'confirmation_text' => __( 'Are you sure you want to delete this post?', FEA_NS ),
            'field_label_hide'  => 1,
			'force_delete' => 0,
		);
		
	}
		
}


// initialize
acf_register_field_type( 'acf_field_delete_post' );

endif; // class_exists check

?>