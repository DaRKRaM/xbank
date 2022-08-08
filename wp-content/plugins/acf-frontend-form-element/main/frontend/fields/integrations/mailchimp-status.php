<?php
if ( ! class_exists( 'acf_field_mailchimp_status' ) ) :

	class acf_field_mailchimp_status extends acf_field {


		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   n/a
		*  @return  n/a
		*/

		function initialize() {

			// vars
			$this->name      = 'mailchimp_status';
			$this->label     = __( 'Mailchimp Status', FEA_NS );
			$this->category  = __( 'Mailchimp', FEA_NS );
			$this->defaults  = array(
				'default_value' => 0,
				'message'       => '',
				'ui'            => 0,
				'ui_on_text'    => '',
				'ui_off_text'   => '',
				'save_unsubscribed' => '',
			);
			add_filter( 'frontend_admin/add_to_record/'.$this->name, array( $this, 'add_to_record' ), 10, 3 );

		}

		function add_to_record( $record, $group, $field ){
			$record['mailchimp']['status'] = $group.':'.$field['name'];
			return $record;
		}

		function prepare_field( $field ) {
			$field['type'] = 'true_false';
			return $field;
		}

/* 		function render_field_settings( $field ) {
			acf_render_field_setting( $field, array(
				'label'			=> __('Appearance', FEA_NS),
				'name'			=> 'field_type',
				'type'			=> 'radio',
				'choices'		=> array(
					'true_false' => __( 'True/False', FEA_NS ),
					'select' => __( 'Select Option', FEA_NS ),
				),
			) ); 
	

		} */
		function render_field_settings( $field ) {

			// message
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Message', 'acf' ),
					'instructions' => __( 'Displays text alongside the checkbox', 'acf' ),
					'type'         => 'text',
					'name'         => 'message',
				)
			);

			// default_value
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Default Value', 'acf' ),
					'instructions' => '',
					'type'         => 'true_false',
					'name'         => 'default_value',
				)
			);

			// ui
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Stylised UI', 'acf' ),
					'instructions' => '',
					'type'         => 'true_false',
					'name'         => 'ui',
					'ui'           => 1,
					'class'        => 'acf-field-object-true-false-ui',
				)
			);

			// on_text
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Subscribe Text', FEA_NS ),
					'instructions' => __( 'Text shown when active', 'acf' ),
					'type'         => 'text',
					'name'         => 'ui_on_text',
					'placeholder'  => __( 'Yes', 'acf' ),
					'conditions'   => array(
						'field'    => 'ui',
						'operator' => '==',
						'value'    => 1,
					),
				)
			);

			// on_text
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Unsubscribe Text', 'acf' ),
					'instructions' => __( 'Text shown when inactive', 'acf' ),
					'type'         => 'text',
					'name'         => 'ui_off_text',
					'placeholder'  => __( 'No', 'acf' ),
					'conditions'   => array(
						'field'    => 'ui',
						'operator' => '==',
						'value'    => 1,
					),
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Save Unsubscribed', FEA_NS ),
					'instructions' => __( 'Save the email in Mailchimp as "Unsubscribed" if the user leaves unchecked.', FEA_NS ),
					'type'         => 'true_false',
					'ui'		   => 1,
					'name'         => 'save_unsubscribed',
				)
			);

		}

		
	
	}


	// initialize
	acf_register_field_type( 'acf_field_mailchimp_status' );

endif; // class_exists check
?>
