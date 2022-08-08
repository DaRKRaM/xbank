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
if( ! class_exists( 'SendWebhook' ) ) :
	
class SendWebhook extends ActionBase{

	public $site_domain = '';

	public function get_name() {
		return 'webhook';
	}

	public function get_label() {
		return __( 'Webhook', FEA_NS );
	}

	public function admin_fields(){
		return array (
			array(
				'key' => 'webhooks',
				'label' => __( 'Webhooks', FEA_NS ),
				'type' => 'list_items',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'webhook_id',
				'collapsable' => true,
				'min' => '',
				'max' => '',
				'layout' => 'block',
				'button_label' => __( 'Add Webhook', FEA_NS ),
				'remove_label' => __( 'Remove Webhook', FEA_NS ),
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
		return array(
			array (
				'key' => 'webhook_url',
				'label' => __( 'Webhook URL', FEA_NS ),
				'name' => 'webhook_url',
				'type' => 'text',
				'instructions' => __( 'Enter the integration URL that will receive the form\'s submitted data.', FEA_NS ),
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '70',
					'class' => '',
					'id' => '',
				),
				'placeholder' => 'https://your-webhook-url.com?key=',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
		);
	}

	public function register_settings_section( $widget ) {

		$site_domain = feadmin_get_site_domain();
		
		$repeater = new \Elementor\Repeater();


		$widget->start_controls_section(
			 'section_webhook',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'more_actions' => $this->get_name(),
					'admin_forms_select' => '',
				],
			]
		);
        
        $repeater->add_control(
            'webhook_id',
           [
               'label' => __( 'Webhook Name', FEA_NS ),
               'type' => Controls_Manager::TEXT,
               'placeholder' => __( 'Webhook Name', FEA_NS ),
               'label_block' => true,
               'description' => __( 'Give this webhook an identifier', FEA_NS ),
			   'render_type' => 'none',
           ]
       );
	
		$repeater->add_control(
			 'webhook_url',
			[
				'label' => __( 'Webhook URL', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'https://your-webhook-url.com?key=',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter the integration URL that will receive the form\'s submitted data.', FEA_NS ),
				'render_type' => 'none',
			]
		);
		
		
		$widget->add_control(
			'webhooks_to_send',
			[
				'label' => __( 'Webhooks', FEA_NS ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ webhook_id }}}',
				'render_type' => 'none',
			]
		);

		$widget->end_controls_section();
	}
	
	public function run( $form, $step = false ){	

		if( ! empty( $form['webhooks'] ) ){
			$webhooks = $form['webhooks'];
		}else{
			if( ! empty( $form['submit_actions'] ) ){
				$actions = $form['submit_actions'];
				if( $actions ){
					$webhooks = [];
					foreach( $actions as $action ){
						if( $action['acf_frontend_blocks_layout'] == 'webhook' ){
							$webhooks[] = $action;
						}
					}
				}
			}
		}

		if( empty( $webhooks ) ) return;

		$record = $form['record'];

		foreach( $webhooks as $webhook ){
			if( empty( $webhook['url'] ) || ! filter_var( $webhook['url'], FILTER_SANITIZE_URL ) ) continue; 

			/**
			 * Forms webhook request arguments.
			 *
			 * Filters the request arguments delivered by the form webhook when executing
			 * an ajax request.
			 *
			 * @since 1.0.0
			 *
			 * @param array    $record   The submission's recorded data sent through the webhook .
			 */
			$data = apply_filters( FEA_PREFIX.'/forms/webhooks/request_data', $form );

			$response = wp_remote_post( $webhook['webhook_url'], $data );

			/**
			 * Form webhook response.
			 *
			 * Fires when the webhook response is retrieved.
			 *
			 * @since 1.0.0
			 *
			 * @param \WP_Error|array $response The response or WP_Error on failure.
			 * @param array     $record   An instance of the form record.
			 */
			do_action( FEA_PREFIX.'/forms/webhooks/response', $response, $record );

			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				throw new \Exception( __( 'Webhook Error', FEA_NS ) );
			}
		}

	}

}
fea_instance()->remote_actions['webhook'] = new SendWebhook();

endif;	