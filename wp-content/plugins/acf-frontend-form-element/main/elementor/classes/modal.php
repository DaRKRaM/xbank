<?php
namespace Frontend_WP\Classes;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class ModalWindow{

	public function get_icon( $icon, $attributes = [], $tag = 'i' ){
		if ( empty( $icon['library'] ) ) {
			return false;
		}
		$output = '';
		// handler SVG Icon
		if ( 'svg' === $icon['library'] ) {
			$output = \Elementor\Icons_Manager::render_svg_icon( $icon['value'] );
		} else {
			$output = $this->render_icon_html( $icon, $attributes, $tag );
		}

		return $output . ' ';
	}

	public function render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
		$icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
		if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
			return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
		}

		if ( empty( $attributes['class'] ) ) {
			$attributes['class'] = $icon['value'];
		} else {
			if ( is_array( $attributes['class'] ) ) {
				$attributes['class'][] = $icon['value'];
			} else {
				$attributes['class'] .= ' ' . $icon['value'];
			}
		}
		return '<' . $tag . ' ' . \Elementor\Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
	}

	public function before_render( $settings ){
		if( empty( $settings['show_in_modal'] ) ) return;
		
		global $hide_modal;
		if( ! $hide_modal ){
				echo '<style>
				.modal{display:none}.show{display:block}
			</style>'; 
			wp_enqueue_style( 'fea-modal' );	
			wp_enqueue_style( 'acf-global' );	
			wp_enqueue_script( 'fea-modal' ); 
			$hide_modal = true;
		}
		$show_modal = 'hide';

		$modal_num = feadmin_get_random_string();
		
		$before = '<div class="modal-button-container"><button class="modal-button open-modal" data-modal="' .$modal_num. '" >'; 
		if( ! empty( $settings['modal_button_icon']['value'] ) ){
			$before .= $this->get_icon( $settings['modal_button_icon'], ['aria-hidden' => 'true'] );
		}
		$before .= $settings['modal_button_text']. '</button></div>';
		
		$before .= '<div id="modal_' .$modal_num. '" class="fea-modal edit-modal">
				<div class="fea-modal-content"> 
					<div class="fea-modal-inner"> 
					<span data-modal="' .$modal_num. '" class="acf-icon -cancel close-modal"></span>
						<div class="content-container">';

		echo $before;
					
	}
	public function after_render( $settings ){
		if( empty( $settings['show_in_modal'] ) ) return;

		$after = '</div>
			</div>
		</div>
		</div>';

		echo $after;
	}
		
	public function modal_controls( $element ) { 
		$element->start_controls_section(
			'modal_section',
			[
				'label' => __( 'Modal Window', FEA_NS ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'admin_forms_select' => '',
				],
			]
		);
		
		$element->add_control(
			'show_in_modal',
			[
				'label' => __( 'Show in Modal', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', FEA_NS ),
				'label_off' => __( 'No',FEA_NS ),
				'return_value' => 'true',
			]
		);
			
		$default_text = __( 'Open Modal', FEA_NS );

		$element->add_control(
			'modal_button_text',
			[
				'label' => __( 'Modal Button Text', FEA_NS ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => $default_text,
				'placeholder' => $default_text,
				'condition' => [
					'show_in_modal' => 'true',
				],
				'dynamic' => [
					'active' => true,
				],		
			]
		);		
		$element->add_control(
			'modal_button_icon',
			[
				'label' => __( 'Modal Button Icon', FEA_NS ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'condition' => [
					'show_in_modal' => 'true',
				],
			]
		);
				
		$element->end_controls_section();
		
		
	}

	public function __construct() {
		add_action( FEA_PREFIX.'/elementor_widget/content_controls', array( $this, 'modal_controls' ), 10 );
		add_action( FEA_PREFIX.'/elementor/before_render', array( $this, 'before_render' ), 10 );	
		add_action( FEA_PREFIX.'/elementor/after_render', array( $this, 'after_render' ), 10 );	
	}

}

new ModalWindow();

