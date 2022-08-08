<?php
namespace Frontend_WP\Actions;

use Frontend_WP\Plugin;
use Frontend_WP;
use Frontend_WP\Classes\ActionBase;
use Frontend_WP\Widgets;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'SendEmail' ) ) :

class SendEmail extends ActionBase{

	public $site_domain = '';

	public function get_name() {
		return 'email';
	}

	public function get_label() {
		return __( 'Email', FEA_NS );
	}

	public function admin_fields(){
		return array (
			array(
				'key' => 'emails',
				'label' => __( 'Emails', FEA_NS ),
				'type' => 'list_items',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'email_id',
				'collapsable' => true,
				'min' => '',
				'max' => '',
				'layout' => 'block',
				'button_label' => __( 'Add Email', FEA_NS ),
				'remove_label' => __( 'Remove Email', FEA_NS ),
				'conditional_logic' => array(
					array(
						array(
							'field' => 'more_actions',
							'operator' => '==contains',
							'value' => $this->get_name(),
						),
					),
				),
				'sub_fields' => $this->action_options(),
			),
		);
	}

	public function action_options(){
		return array (
			array (
				'key' => 'email_to_message',
				'label' => __( 'Email Addresses', FEA_NS ),
				'name' => 'recipient_custom',
				'type' => 'message',
				'message' =>  __( 'Separate emails with commas. <br> To display the names and addresses, use the following format: Name&lt;address&gt;.', FEA_NS ),
			),
		   
			array (
				'key' => 'email_to',
				'label' => __( 'To', FEA_NS ),
				'name' => 'recipient_custom',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '33.33',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_to_cc',
				'label' => __( 'CC', FEA_NS ),
				'name' => 'recipient_custom',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '33.33',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_to_bcc',
				'label' => __( 'BCC', FEA_NS ),
				'name' => 'recipient_custom',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '33.33',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_from',
				'label' => __( 'From Email', FEA_NS ),
				'name' => 'from',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_from_name',
				'label' => __( 'From Name', FEA_NS ),
				'name' => 'from_name',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_reply_to',
				'label' => __( 'Reply To Email', FEA_NS ),
				'name' => 'reply_to',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_reply_to_name',
				'label' => __( 'Reply To Name', FEA_NS ),
				'name' => 'reply_to_name',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_subject',
				'label' => __( 'Subject', FEA_NS ),
				'name' => 'subject',
				'type' => 'text',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'email_content',
				'label' => __( 'Content', FEA_NS ),
				'type' => 'wysiwyg',
				'dynamic_value_choices' => 1,
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'tabs' => 'all',
				'toolbar' => 'full',
				'media_upload' => 1,
			),
			array(
				'key' => 'email_content_type',
				'label' => __( 'Send As', FEA_NS ),
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array(
					'width' => '25',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'html',
				'choices' => array(
					'html' => __( 'HTML', FEA_NS ),
					'plain' => __( 'Plain', FEA_NS ),
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'form_metadata',
				'label' => __( 'Meta Data', FEA_NS ),
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array(
					'width' => '75',
					'class' => '',
					'id' => '',
				),
				'default_value' => [
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
					'credit',
				],
				'choices' => array(
					'date' => __( 'Date', FEA_NS ),
					'time' => __( 'Time', FEA_NS ),
					'page_url' => __( 'Page URL', 'elementor-pro' ),
					'user_agent' => __( 'User Agent', FEA_NS ),
					'remote_ip' => __( 'Remote IP', FEA_NS ),
					'credit' => __( 'Credit', FEA_NS ),
				),
				'allow_null' => 0,
				'multiple' => 1,
				'ui' => 1,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
		);
	}

	public function register_settings_section( $widget ) {

		$site_domain = feadmin_get_site_domain();
		
		$repeater = new \Elementor\Repeater();


		$widget->start_controls_section(
			 'section_email',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'more_actions' => $this->get_name(),
					'admin_forms_select' => '',
				],
			]
		);
				
		$widget->add_control(
			'steps_emails_message',
			[
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => "In each step, enter the email names you want to be sent upon completing that step.",
				'separator' => 'after',
				'condition' => [
					'multi' => 'true',
				],
			]
		);
		$repeater->add_control(
			 'email_id',
			[
				'label' => __( 'Email Name', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Email Name', FEA_NS ),
				'default' => __( 'Email Name', FEA_NS ),
				'label_block' => true,
				'description' => __( 'Give this email an identifier', FEA_NS ),
				'render_type' => 'none',
			]
		);
		
		$repeater->start_controls_tabs( 'tabs_to_emails' );

		$repeater->start_controls_tab(
			'tab_to_email',
			[
				'label' => __( 'To', 'elementor-pro' ),
			]
		);

		$repeater->add_control(
			 'email_to',
			[
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'show_label' => false,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'description' => __( 'Separate emails with commas. <br> To display the names and addresses, use the following format: Name&lt;address&gt;.', FEA_NS ),
				'render_type' => 'none',
			]
		);
		
		$repeater->end_controls_tab();
		
		$repeater->start_controls_tab(
			'tab_to_cc_email',
			[
				'label' => __( 'Cc', 'elementor-pro' ),
			]
		);
		
		$repeater->add_control(
			 'email_to_cc',
			[
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,				
				'show_label' => false,
				'default' => '',
				'description' => __( 'Separate emails with commas. <br> To display the names and addresses, use the following format: Name&lt;address&gt;.', FEA_NS ),
				'render_type' => 'none',
			]
		);
		
		$repeater->end_controls_tab();
		
		$repeater->start_controls_tab(
			'tab_to_bcc_email',
			[
				'label' => __( 'Bcc', 'elementor-pro' ),
			]
		);

		$repeater->add_control(
			 'email_to_bcc',
			[
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,				
				'show_label' => false,	
				'default' => '',
				'description' => __( 'Separate emails with commas. <br> To display the names and addresses, use the following format: Name&lt;address&gt;.', FEA_NS ),
				'render_type' => 'none',
			]
		);
		
		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();
				
		$repeater->add_control(
			'tabs_email_to_end',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		
		$repeater->start_controls_tabs( 'tabs_from_emails' );

		$repeater->start_controls_tab(
			'tab_from_email',
			[
				'label' => __( 'From', 'elementor-pro' ),
			]
		);
		
		$repeater->add_control(
			 'email_from',
			[
				'label' => __( 'From Email', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,				
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
			]
		);

		$repeater->add_control(
			 'email_from_name',
			[
				'label' => __( 'From Name', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,			
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
			]
		);		
		
		$repeater->end_controls_tab();
		
		$repeater->start_controls_tab(
			'tab_reply_to_email',
			[
				'label' => __( 'Reply-To', 'elementor-pro' ),
			]
		);
		
		$repeater->add_control(
			 'email_reply_to',
			[
				'label' => __( 'Reply-To Email', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,				
				'render_type' => 'none',
			]
		);
		
		$repeater->add_control(
			 'email_reply_to_name',
			[
				'label' => __( 'Reply-To Name', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,				
				'render_type' => 'none',
			]
		);		
		
		$repeater->end_controls_tab();		

		$repeater->end_controls_tabs();
		
		$repeater->add_control(
			'tabs_email_from_end',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$repeater->add_control(
			 'email_subject',
			[
				'label' => __( 'Subject', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'New message from [user:username]', FEA_NS ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$repeater->add_control(
			 'email_content',
			[
				'label' => __( 'Message', FEA_NS ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => 'Hello, this is [user:username]...',
				'label_block' => true,
				'render_type' => 'none',
			]
		);


		$repeater->add_control(
			 'form_metadata',
			[
				'label' => __( 'Meta Data', FEA_NS ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'default' => [
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
					'credit',
				],
				'options' => [
					'date' => __( 'Date', FEA_NS ),
					'time' => __( 'Time', FEA_NS ),
					'page_url' => __( 'Page URL', 'elementor-pro' ),
					'user_agent' => __( 'User Agent', FEA_NS ),
					'remote_ip' => __( 'Remote IP', FEA_NS ),
					'credit' => __( 'Credit', FEA_NS ),
				],
				'render_type' => 'none',
			]
		);

		$repeater->add_control(
			 'email_content_type',
			[
				'label' => __( 'Send As', FEA_NS ),
				'type' => Controls_Manager::SELECT,
				'default' => 'html',
				'render_type' => 'none',
				'options' => [
					'html' => __( 'HTML', FEA_NS ),
					'plain' => __( 'Plain', FEA_NS ),
				],
				'render_type' => 'none',
			]
		);
		
		$widget->add_control(
			'emails_to_send',
			[
				'label' => __( 'Emails', FEA_NS ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ email_id }}}',
			]
		);

		$widget->end_controls_section();
	}
	
	public function run( $form, $step = false ){	
		if( ! empty( $form['emails'] ) ){
			$emails = $form['emails'];
		}else{	
			if( empty( $emails ) && ! empty( $form['submit_actions'] ) ){
				$actions = $form['submit_actions'];
				if( $actions ){
					$emails = [];
					foreach( $actions as $action ){
						if( $action['acf_frontend_blocks_layout'] == 'email' ){
							$emails[] = $action;
						}
					}
				}
			}
		}

		if( empty( $emails ) ) return;
		
		foreach( $emails as $email ){
			$send_email = true;
			
			/* if( $step !== false ){
				$step_emails = explode( ',', $step['emails_to_send'] );
				if( ! in_array( $email['email_id'], $step_emails ) ){
					continue;
				}
			} */
			
			$send_html = 'plain' !== $email['email_content_type'];
			$line_break = $send_html ? '<br>' : "\n";

			$fields = [
				'email_to' => get_option( 'admin_email' ),
				'email_to_cc' => '',
				'email_to_bcc' => '',					
				'email_from' => get_bloginfo( 'admin_email' ),
				'email_from_name' => get_bloginfo( 'name' ),
				'email_reply_to' => 'noreply@' . feadmin_get_site_domain(),
				'email_reply_to_name' => '',
				/* translators: %s: Site title. */
				'email_subject' => sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_bloginfo( 'name' ) ),
				'email_content' => __( 'A form has been filled out on your site', FEA_NS ),

			];

			foreach ( $fields as $key => $default ) {
				$setting = trim( $email[ $key ] );
				$setting = fea_instance()->dynamic_values->get_dynamic_values( $setting, $form );
				if ( ! empty( $setting ) ) {
					$fields[ $key ] = $setting;
				}
			}

			$email_meta = $this->get_meta( $form, $email['form_metadata'], $line_break );


			if ( ! empty( $email_meta ) ) {
				$fields['email_content'] .= $line_break . '---' . $line_break . $line_break . $email_meta;
			}

			$headers = sprintf( 'From: %s <%s>' . "\r\n", $fields['email_from_name'], $fields['email_from'] );
			$headers .= sprintf( 'Reply-To: %s <%s>' . "\r\n", $fields['email_reply_to_name'], $fields['email_reply_to'] );

			if ( $send_html ) {
				$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
			}

			$cc_header = '';
			if ( ! empty( $fields['email_to_cc'] ) ) {
				$cc_header = 'Cc: ' . $fields['email_to_cc'] . "\r\n";
			}

			/**
			 * Email headers.
			 *
			 * Filters the additional headers sent when the form send an email.
			 *
			 * @since 1.0.0
			 *
			 * @param string|array $headers Additional headers.
			 */
			$headers = apply_filters( FEA_PREFIX.'/wp_mail_headers', $headers );

		
			/**
			 * Email content.
			 *
			 * Filters the content of the email sent by the form.
			 *
			 * @since 1.0.0
			 *
			 * @param string $email_content Email content.
			 */
			$fields['email_content'] = apply_filters( FEA_PREFIX.'/wp_mail_message', $fields['email_content'] );

			$email_sent = wp_mail( $fields['email_to'], $fields['email_subject'], $fields['email_content'], $headers . $cc_header );

			if ( ! empty( $fields['email_to_bcc'] ) ) {
				$bcc_emails = explode( ',', $fields['email_to_bcc'] );
				foreach ( $bcc_emails as $bcc_email ) {
					wp_mail( trim( $bcc_email ), $fields['email_subject'], $fields['email_content'], $headers );
				}
			}
	
			/**
			 * Mail sent.
			 *
			 * Fires when an email was sent successfully.
			 *
			 * @since 1.0.0
			 *
			 * @param $email      array of settings of this email.
			 * @param $form       array of form settings
			 * @param $step       array of settings of the current step
			 */
			do_action( FEA_PREFIX.'/mail_sent', $email, $form, $step );
		}

	}	
	
	
	private function get_meta( $form, $form_metadata, $line_break ){
		if ( empty( $form_metadata ) ) {
			return;
		}
		
		$email_meta = '';
		$meta = [];
		foreach ( $form_metadata as $type ) {
			switch ( $type ) {
				case "date":
					$meta = [
						'title' => __( 'Date', 'elementor-pro' ),
						'value' => date_i18n( get_option( 'date_format' ) ),
					];
					break;
				case "time":
					$meta = [
						'title' => __( 'Time', 'elementor-pro' ),
						'value' => date_i18n( get_option( 'time_format' ) ),
					];
					break;
				case "page_url":
					$meta = [
						'title' => __( 'Page URL', 'elementor-pro' ),
						'value' => wp_get_referer(),
					];
					break;	
				case "user_agent":
					$meta = [
						'title' => __( 'User Agent', 'elementor-pro' ),
						'value' => $_SERVER['HTTP_USER_AGENT'],
					];
					break;
				case "remote_ip":
					$meta = [
						'title' => __( 'Remote IP', 'elementor-pro' ),
						'value' => feadmin_get_client_ip(),
					];
					break;
				case "credit":
					$meta = [
						'title' => __( 'Powered by', 'elementor-pro' ),
						'value' => 'Frontend Admin',
					];
					break;
			}			
			$email_meta .= $this->field_formatted( $meta ) . $line_break;
		}

		return $email_meta;
	}
	
	private function field_formatted( $field ) {
		$formatted = '';
		if ( ! empty( $field['title'] ) ) {
			$formatted = sprintf( '%s: %s', $field['title'], $field['value'] );
		} elseif ( ! empty( $field['value'] ) ) {
			$formatted = sprintf( '%s', $field['value'] );
		}

		return $formatted;
	}

}
fea_instance()->remote_actions['email'] = new SendEmail();

endif;	