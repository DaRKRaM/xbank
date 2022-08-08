<?php
namespace Frontend_WP\Actions;

use Frontend_WP\Plugin;
use Frontend_WP\Classes\ActionBase;
use Frontend_WP\Widgets;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Module as Query_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'ActionProduct' ) ) :

class ActionProduct extends ActionBase {
	
	public function get_name() {
		return 'product';
	}

	public function get_label() {
		return __( 'Product', FEA_NS );
	}


	public function get_fields_display( $form_field, $local_field, $element = '', $sub_fields = false, $saving = false ){
		$field_appearance = isset( $form_field['field_taxonomy_appearance'] ) ? $form_field['field_taxonomy_appearance'] : 'checkbox';
		$field_add_term = isset( $form_field['field_add_term'] ) ? $form_field['field_add_term'] : 0;
		switch( $form_field['field_type'] ){
			case 'price':
				$local_field['type'] = 'product_price';
			break;
			case 'sale_price':
				$local_field['type'] = 'product_sale_price';
			break;
			case 'description':
				$local_field['type'] = 'product_description';
				$local_field['field_type'] = isset( $form_field['editor_type'] ) ? $form_field['editor_type'] : 'wysiwyg';
			break;
			case 'main_image':
				$local_field['type'] = 'main_image';
				$local_field['default_value'] = empty( $form_field['default_featured_image']['id'] ) ? '' : $form_field['default_featured_image']['id'];
			break;			
			case 'images':
				$local_field['type'] = 'product_images';
			break;
			case 'short_description':
				$local_field['type'] = 'product_short_description';
			break;
			case 'product_categories':
				$local_field['type'] = 'related_terms';
				$local_field['taxonomy'] = 'product_cat';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'product_tags':
				$local_field['type'] = 'related_terms';
				$local_field['taxonomy'] = 'product_tag';
				$local_field['field_type'] = $field_appearance;
				$local_field['allow_null'] = 0;
				$local_field['add_term'] = $field_add_term;
				$local_field['load_post_terms'] = 1;
				$local_field['save_terms'] = 1;
				$local_field['custom_taxonomy'] = true;
			break;
			case 'tax_class':
				$local_field['type'] = 'product_tax_class';
			break;
			case 'tax_status':
				$local_field['type'] = 'product_tax_status';
			break;
			case 'product_type':
				$local_field['type'] = 'product_types';
				$local_field['default_value'] = isset( $form_field['default_product_type'] ) ? $form_field['default_product_type'] : 'simple';	
				$local_field['field_type'] = isset( $form_field['role_appearance'] ) ? $form_field['role_appearance'] : 'radio';
				$local_field['layout'] = isset( $form_field['role_radio_layout'] ) ? $form_field['role_radio_layout'] : 'vertical'; 			
			break;
			case 'is_virtual':
			case 'is_downloadable':
			case 'manage_stock':
			case 'product_enable_reviews':
				$local_field['type'] = $form_field['field_type'];
				$local_field['ui_on_text'] = isset( $form_field['ui_on'] ) ? $form_field['ui_on'] : 'Yes';
				$local_field['ui_off_text'] = isset( $form_field['ui_off'] ) ? $form_field['ui_off'] : 'No';
			break;
			case 'attributes':
				$form_field = feadmin_parse_args( $form_field, array(
					'button_text' => '',
					'save_button_text' => '',
					'no_value_msg' => '',
				) );

				if( is_array( $sub_fields ) ){
					$sub_settings = array(
						'field_label_on' => 0,
						'label' => '',
						'instructions' => '',
						'placeholder' => '',
						'products_page' => '',
						'for_variations' => '',
						'button_label' => '',
					);
					foreach( $sub_fields as $i => $sub_field ){
						$sub_fields[$i] = feadmin_parse_args( $sub_fields[$i], $sub_settings );		
					}			
				}
				$local_field['type'] = 'product_attributes';
				$local_field['button_label'] = $form_field['button_text'];
				$local_field['save_text'] = $form_field['save_button_text'];
				$local_field['no_value_msg'] = $form_field['no_value_msg'];
				$local_field['fields_settings'] = array(
					'name' => array(
						'field_label_hide' => ! $sub_fields[0]['field_label_on'],
						'label' =>  $sub_fields[0]['label'],
						'placeholder' =>  $sub_fields[0]['placeholder'],
						'instructions' =>  $sub_fields[0]['instructions'],
					),
					'locations' => array(
						'field_label_hide' => ! $sub_fields[1]['field_label_on'],
						'label' =>  $sub_fields[1]['label'],
						'instructions' =>  $sub_fields[1]['instructions'],
						'choices' => array(
							'products_page' => $sub_fields[1]['products_page'],
							'for_variations' => $sub_fields[1]['for_variations'],
						),
					),
					'custom_terms' => array(
						'field_label_hide' => ! $sub_fields[2]['field_label_on'],
						'label' =>  $sub_fields[2]['label'],
						'instructions' =>  $sub_fields[2]['instructions'],
						'button_label' =>  $sub_fields[2]['button_label'],
					),
					'terms' => array(
						'field_label_hide' => ! $sub_fields[3]['field_label_on'],
						'label' =>  $sub_fields[3]['label'],
						'instructions' =>  $sub_fields[3]['instructions'],
						'button_label' =>  $sub_fields[3]['button_label'],
					),
				);
			break;
			case 'variations':
				$form_field = feadmin_parse_args( $form_field, array(
					'button_text' => '',
					'save_button_text' => '',
					'no_value_msg' => '',
					'no_attrs_msg' => '',
				) );
				$local_field['type'] = 'product_variations';
				$local_field['button_label'] = $form_field['button_text'];
				$local_field['save_text'] = $form_field['save_button_text'];
				$local_field['no_value_msg'] = $form_field['no_value_msg'];
				$local_field['no_attrs_msg'] = $form_field['no_attrs_msg'];
				$local_field['fields_settings'] = $sub_fields;
			break;
			case 'grouped_products':
				$group_field = true;
				$local_field['type'] = 'product_grouped';
			break;
			case 'upsells':
				$group_field = true;
				$local_field['type'] = 'product_upsells';
			break;
			case 'cross_sells':
				$group_field = true;
				$local_field['type'] = 'product_cross_sells';
			break;
			case 'sku':
				$local_field['type'] = 'product_sku';
			break;					
			case 'allow_backorders':
				$local_field['type'] = 'allow_backorders';
				 $local_field['choices'] = array(
					'no' => isset( $form_field['do_not_allow'] ) ? $form_field['do_not_allow'] :__( 'Do not allow', 'woocommerce' ),
					'notify' => isset( $form_field['notify'] ) ? $form_field['notify'] : __( 'Notify', 'woocommerce' ),
					'yes' => isset( $form_field['allow'] ) ? $form_field['allow'] : __( 'Allow', 'woocommerce' ),
				); 
				$local_field['field_type'] = isset( $form_field['role_appearance'] ) ? $form_field['role_appearance'] : 'radio';
				$local_field['layout'] = isset( $form_field['role_radio_layout'] ) ? $form_field['role_radio_layout'] : 'vertical'; 
			break;				
			case 'stock_status':
				$local_field['type'] = 'stock_status';
				$local_field['choices'] = array(
					'instock' => isset( $form_field['instock'] ) ? $form_field['instock'] : __( 'In stock', 'woocommerce' ),
					'outofstock' => isset( $form_field['outofstock'] ) ? $form_field['outofstock'] : __( 'Out of stock', 'woocommerce' ),
					'onbackorder' => isset( $form_field['backorder'] ) ? $form_field['backorder'] : __( 'On backorder', 'woocommerce' ),
				);
				$local_field['field_type'] = isset( $form_field['role_appearance'] ) ? $form_field['role_appearance'] : 'radio';
				$local_field['layout'] = isset( $form_field['role_radio_layout'] ) ? $form_field['role_radio_layout'] : 'vertical';
			break;	
			case 'sold_individually':
				$local_field['type'] = 'sold_individually';
				$local_field['ui'] = 1;
				$local_field['ui_on_text'] = isset( $form_field['ui_on'] ) ? $form_field['ui_on'] : 'Yes';
				$local_field['ui_off_text'] = isset( $form_field['ui_off'] ) ? $form_field['ui_off'] : 'No';
			break;	
			default:
				$local_field['type'] = $form_field['field_type'];
		}

		if( isset( $group_field ) ){
			if( ! empty( $form_field['add_edit_product'] ) ){
				$local_field['add_edit_post'] = 1;
				if( ! empty( $form_field['add_product_text'] ) ){
					$local_field['add_post_button'] = $form_field['add_product_text'];
				}
			}else{
				$local_field['add_edit_post'] = 0;
			}

			if( ! empty( $form_field['product_authors_to_filter'] ) ){
				$user_ids = str_replace( array( '[', ']' ), '', $form_field['product_authors_to_filter'] );
				$local_field['post_author'] = explode( ',', $user_ids );
			}else{
				$local_field['post_author'] = array();
			}
		}

		return $local_field;
	}
	

	public function get_default_fields( $form, $action = '' ){
		switch( $action ){
			case 'delete':
				$default_fields = array(
					'delete_product'
				);
			break;
			case 'status':
				$default_fields = array(
					'product_status', 'submit_button'
				);
			break;
			case 'new':
				$default_fields = array(
					'product_title', 'product_description','product_short_description', 'main_image', 'product_images', 'product_status', 'submit_button'		
				);
			break;
			default:
				$default_fields = array(
					'product_to_edit', 'product_title', 'product_short_description', 'main_image', 'product_images', 'product_status', 'submit_button'		
				);
		}
		return $this->get_valid_defaults( $default_fields, $form );	
	}

	public function get_form_builder_options( $form ){
		return array(	
			array(
				'key' => 'save_to_product',
				'field_label_hide' => 0,
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'choices' => array(            
					'edit_product' => __( 'Edit Product', FEA_NS ),
					'new_product' => __( 'New Product', FEA_NS ),
					'duplicate_product' => __( 'Duplicate Product', FEA_NS ),
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),	
			array(
				'key' => 'product_to_edit',
				'label' => __( 'Product', FEA_NS ),
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'save_to_product',
							'operator' => '!=',
							'value' => 'new_product',
						),
					),
				),
				'choices' => array(
					'current_product' => __( 'Current Product', FEA_NS ),
					'url_query' => __( 'URL Query', FEA_NS ),
					'select_product' => __( 'Specific Product', FEA_NS ),
				),
				'default_value' => false,
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'url_query_product',
				'label' => __( 'URL Query Key', FEA_NS ),
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'save_to_product',
							'operator' => '!=',
							'value' => 'new_product',
						),
						array(
							'field' => 'product_to_edit',
							'operator' => '==',
							'value' => 'url_query',
						),
					),
				),
				'placeholder' => '',
			),
			array(
				'key' => 'select_product',
				'label' => __( 'Specific Product', FEA_NS ),
				'name' => 'select_product',
				'prefix' => 'form',
				'type' => 'post_object',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'save_to_product',
							'operator' => '!=',
							'value' => 'new_product',
						),
						array(
							'field' => 'product_to_edit',
							'operator' => '==',
							'value' => 'select_product',
						),
					),
				),
				'post_type' => 'product',
				'taxonomy' => '',
				'allow_null' => 0,
				'multiple' => 0,
				'return_format' => 'object',
				'ui' => 1,
			),
		);
	}

	public function load_data( $form ){
		switch( $form['save_to_product'] ){
			case 'new_product':
				if( isset( $objects['product'] ) && frontend_admin_can_edit_post( $objects['product'], $form ) ){
					$form['product_id'] = $objects['product'];
					$form['save_to_product'] = 'edit_product';
				}else{
					$status = 'no_change';
					if( isset( $form['new_product_status'] ) ){
						$status = $form['new_product_status'];
					}
					if( $status == 'no_change' ) $status = 'publish';
		
					$form['product_id'] = 'add_product';
				
					if( ! empty( $form['new_product_terms'] ) ){
						if( $form['new_product_terms'] == 'select_terms' ){
							$form['product_terms'] = $form['new_product_terms_select'];
						}
						if( $form['new_product_terms'] == 'current_term' ){
							$form['product_terms'] = get_queried_object()->term_id;
						} 
					}
				}
				break;
				case 'edit_product':
				case 'duplicate_product':
				case 'delete_product':
					global $post;
					if( $form['product_to_edit'] == 'select_product' ){
						if( ! empty( $form['select_product'] ) ){
							$form['product_id'] = $form['select_product'];
						}else{
							if( isset( $form['product_select'] ) ) $form['product_id'] = $form['product_select'];
						}
					}
					if( $form['product_to_edit'] == 'url_query' ){
						if( isset( $_GET[ $form['url_query_product'] ] ) ){
							$form['product_id'] = $_GET[ $form['url_query_product'] ];
						}
					}
					if( $form['product_to_edit'] == 'current_product' && get_post_type( $post ) == 'product' ){
						$form['product_id'] = $post->ID;
					}

					if( empty( $form['product_id'] ) || ! get_post_status( $form['product_id'] ) ) $form['product_id'] = 'none';
				break;
		}
		return $form;
	}

	public function register_settings_section( $widget ) {
						
		$widget->start_controls_section(
			'section_edit_product',
			[
				'label' => $this->get_label(),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'admin_forms_select' => '',
				],
			]
		);			
		$this->action_controls( $widget );

		$widget->add_control(
			'delete_button_deprecated_product',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'The delete button option is now a different widget. Search for the "Trash Button"', FEA_NS ),
				'content_classes' => 'acf-fields-note',
			]
		);

		$widget->end_controls_section();
		
	}
	
	
	public function action_controls( $widget, $step = false, $type = '' ){
		if( ! empty( $widget->form_defaults['save_to_product'] ) ){
			$type = $widget->form_defaults['save_to_product'];
		}
		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
		}
		$args = [
			'label' => __( 'Product', FEA_NS ),
            'type'      => Controls_Manager::SELECT,
            'options'   => [				
				'edit_product' => __( 'Edit Product', FEA_NS ),
				'new_product' => __( 'New Product', FEA_NS ),
				'duplicate_product' => __( 'Duplicate Product', FEA_NS ),
			],
            'default'   => $widget->get_name(),
        ];
		if( $step ){
			$condition = [
				'field_type' => 'step',
				'overwrite_settings' => 'true',
			];
			$args['condition'] = $condition;
		}else{
			$condition = array();
		}
		if( $type ){
			$args = [
				'type' => Controls_Manager::HIDDEN,
				'default' => $type,
			];
		}
	
		$widget->add_control( 'save_to_product', $args );
		$condition['save_to_product'] = ['edit_product', 'new_product', 'duplicate_product'];


		$condition['save_to_product'] = [ 'edit_product', 'duplicate_product', 'delete_product' ];

		$widget->add_control(
			'product_to_edit',
			[
				'label' => __( 'Specific Product', FEA_NS ),
				'type' => Controls_Manager::SELECT,
				'default' => 'current_product',
				'options' => [
					'current_product'  => __( 'Current Product', FEA_NS ),
					'url_query' => __( 'Url Query', FEA_NS ),
					'select_product' => __( 'Specific Product', FEA_NS ),
				],
				'condition' => $condition,
			]
		);
		$condition['product_to_edit'] = 'url_query';
		$widget->add_control(
			'url_query_product',
			[
				'label' => __( 'URL Query', FEA_NS ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'product_id', FEA_NS ),
				'default' => __( 'product_id', FEA_NS ),
				'required' => true,
				'description' => __( 'Enter the URL query parameter containing the id of the product you want to edit', FEA_NS ),
				'condition' => $condition,
			]
		);	
		$condition['product_to_edit'] = 'select_product';
			$widget->add_control(
				'product_select',
				[
					'label' => __( 'Product', FEA_NS ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18', FEA_NS ),
					'description' => __( 'Enter the product ID', FEA_NS ),
					'condition' => $condition,
				]
			);		
	

		unset( $condition['product_to_edit'] );
		
		$condition['save_to_product'] = 'new_product';
	
		$widget->add_control(
			'new_product_terms',
			[
				'label' => __( 'New Product Terms', FEA_NS ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'product',
				'options' => [
					'current_term'  => __( 'Current Term', FEA_NS ),
					'select_terms' => __( 'Specific Term', FEA_NS ),
				],
				'condition' => $condition,
			]
		);
		$condition['new_product_terms'] = 'select_terms';
		if( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ){
			$widget->add_control(
				'new_product_terms_select',
				[
					'label' => __( 'Terms', FEA_NS ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( '18, 12, 11', FEA_NS ),
					'description' => __( 'Enter the a comma-seperated list of term ids', FEA_NS ),
					'condition' => $condition,
				]
			);		
		}else{		
			$widget->add_control(
				'new_product_terms_select',
				[
					'label' => __( 'Terms', FEA_NS ),
					'type' => Query_Module::QUERY_CONTROL_ID,
					'label_block' => true,
					'autocomplete' => [
						'object' => Query_Module::QUERY_OBJECT_TAX,
						'display' => 'detailed',
					],		
					'multiple' => true,
					'condition' => $condition,
				]
			);
		}
		unset( $condition['save_to_product'] );
		unset( $condition['new_product_terms'] );
		$widget->add_control(
			'new_product_status',
			[
				'label' => __( 'Product Status', FEA_NS ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => 'no_change',
				'options' => [
					'draft' => __( 'Draft', FEA_NS ),
					'private' => __( 'Private', FEA_NS ),
					'pending' => __( 'Pending Review', FEA_NS ),
					'publish'  => __( 'Published', FEA_NS ),
				],
				'condition' => $condition,
			]
		);
	}
	

/* 	public function create_product( $args ){	
		// Get an empty instance of the product object (defining it's type)
		$product = $this->get_product_object_type( $args['type'] );
		if( ! $product )
			return false;
	
		// Product name (Title) and slug
		$product->set_name( $args['name'] ); // Name (title).
		if( isset( $args['slug'] ) )
			$product->set_name( $args['slug'] );
	
		// Description and short description:
		$product->set_description( $args['description'] );
		$product->set_short_description( $args['short_description'] );
	
		// Status ('publish', 'pending', 'draft' or 'trash')
		$product->set_status( isset($args['status']) ? $args['status'] : 'publish' );
	
		// Visibility ('hidden', 'visible', 'search' or 'catalog')
		$product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );
	
		// Featured (boolean)
		$product->set_featured(  isset($args['featured']) ? $args['featured'] : false );
	
		// Virtual (boolean)
		$product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );
	
		// Prices
		$product->set_regular_price( $args['regular_price'] );
		$product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
		$product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
		if( isset( $args['sale_price'] ) ){
			$product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
			$product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
		}
	
		// Downloadable (boolean)
		$product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
		if( isset($args['downloadable']) && $args['downloadable'] ) {
			$product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
			$product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
			$product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
		}
	
		// Taxes
		if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
			$product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
			$product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
		}
	
		// SKU and Stock (Not a virtual product)
		if( isset($args['virtual']) && ! $args['virtual'] ) {
			$product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
			$product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
			$product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
			if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
				$product->set_stock_status( $args['stock_qty'] );
				$product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
			}
		}
	
		// Sold Individually
		$product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );
	
		// Weight, dimensions and shipping class
		$product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
		$product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
		$product->set_width( isset(  $args['width'] ) ?  $args['width']  : '' );
		$product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
		if( isset( $args['shipping_class_id'] ) )
			$product->set_shipping_class_id( $args['shipping_class_id'] );
	
		// Upsell and Cross sell (IDs)
		$product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
		$product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );
	
		// Attributes et default attributes
		if( isset( $args['attributes'] ) )
			$product->set_attributes( $this->prepare_product_attributes($args['attributes']) );
		if( isset( $args['default_attributes'] ) )
			$product->set_default_attributes( $args['default_attributes'] ); // Needs a special formatting
	
		// Reviews, purchase note and menu order
		$product->set_reviews_allowed( isset( $args['reviews'] ) ? $args['reviews'] : false );
		$product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
		if( isset( $args['menu_order'] ) )
			$product->set_menu_order( $args['menu_order'] );
	
		// Product categories and Tags
		if( isset( $args['category_ids'] ) )
			$product->set_category_ids( $args['category_ids'] );
		if( isset( $args['tag_ids'] ) )
			$product->set_tag_ids( $args['tag_ids'] );
	
	
		// Images and Gallery
		$product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
		$product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );
	
		## --- SAVE PRODUCT --- ##
		$product_id = $product->save();
	
		return $product_id;
	}
	
	// Utility function that returns the correct product object instance
	public function get_product_object_type( $type ) {
		// Get an instance of the WC_Product object (depending on his type)
		if( isset($args['type']) && $args['type'] === 'variable' ){
			$product = new WC_Product_Variable();
		} elseif( isset($args['type']) && $args['type'] === 'grouped' ){
			$product = new WC_Product_Grouped();
		} elseif( isset($args['type']) && $args['type'] === 'external' ){
			$product = new WC_Product_External();
		} else {
			$product = new WC_Product_Simple(); // "simple" By default
		} 
		
		if( ! is_a( $product, 'WC_Product' ) )
			return false;
		else
			return $product;
	}
	
	// Utility function that prepare product attributes before saving
	public function prepare_product_attributes( $attributes ){
		global $woocommerce;
	
		$data = array();
		$position = 0;
	
		foreach( $attributes as $taxonomy => $values ){
			if( ! taxonomy_exists( $taxonomy ) )
				continue;
	
			// Get an instance of the WC_Product_Attribute Object
			$attribute = new WC_Product_Attribute();
	
			$term_ids = array();
	
			// Loop through the term names
			foreach( $values['term_names'] as $term_name ){
				if( term_exists( $term_name, $taxonomy ) )
					// Get and set the term ID in the array from the term name
					$term_ids[] = get_term_by( 'name', $term_name, $taxonomy )->term_id;
				else
					continue;
			}
	
			$taxonomy_id = wc_attribute_taxonomy_id_by_name( $taxonomy ); // Get taxonomy ID
	
			$attribute->set_id( $taxonomy_id );
			$attribute->set_name( $taxonomy );
			$attribute->set_options( $term_ids );
			$attribute->set_position( $position );
			$attribute->set_visible( $values['is_visible'] );
			$attribute->set_variation( $values['for_variation'] );
	
			$data[$taxonomy] = $attribute; // Set in an array
	
			$position++; // Increase position
		}
		return $data;
	} */

	public function get_core_fields(){
		return array(
			'product_title' => 'post_title',
			'product_slug' => 'post_name',
			'product_description' => 'post_content',
			'product_short_description' => 'post_excerpt',
			'product_date' => 'post_date',
			'product_author' => 'post_author',
			'product_menu_order' => 'menu_order',
			'product_allow_comments' => 'allow_comments',
		);
	}

	public function run( $form, $step = false ){	
		$record = $form['record'];
		if( empty( $record['_acf_product'] ) || empty( $record['fields']['woo_product'] ) ) return $form;

		$product_id = wp_kses( $record['_acf_product'], 'strip' );

		// allow for custom save
		$product_id = apply_filters('acf/pre_save_product', $product_id, $form);
				
		switch( $form['save_to_product'] ){
			case 'edit_product':
				$product_to_edit['ID'] = $product_id;
			break;	
			case 'new_product':
				$product_to_edit['ID'] = 0;	
				$product_to_edit['post_type'] = 'product';					
			break;
			case 'duplicate_product':
				$product_to_duplicate = get_post( $product_id );
				$product_to_edit = get_object_vars( $product_to_duplicate );	
				$product_to_edit['ID'] = 0;	
				$product_to_edit['post_author'] = get_current_user_id();
			break;
			default:
				return $form;	
		}
		
		$core_fields = $this->get_core_fields();
		$product_type = 'simple';

		if( ! empty( $record['fields']['woo_product'] ) ){
			foreach( $record['fields']['woo_product'] as $name => $field ){
				if( ! is_array( $field ) ) continue;

				$field_type = $field['type'];
				$field['value'] = $field['_input'];

				if( ! in_array( $field_type, array_keys( $core_fields ) ) ){
					if( $field_type == 'product_types' ){
						$product_type = $field['value'];
						$pt_field = $field;
					}else{
						$metas[$field['key']] = $field; 
					}
					continue;
				} 

				$product_to_edit[ $core_fields[$field_type] ] = $field['value'];
			}
		}

		if( $form['save_to_product'] == 'duplicate_product' ){
			if( $product_to_edit[ 'post_name' ] == $product_to_duplicate->post_name ){
				$product_name = sanitize_title( $product_to_edit['post_title'] );
				if( ! feadmin_slug_exists( $product_name ) ){				
					$product_to_edit['post_name'] = $product_name;
				}else{
					$product_to_edit['post_name'] = feadmin_duplicate_slug( $product_to_duplicate->post_name );
				}
			}
		}

		if( isset( $record['_acf_status'] ) && $record['_acf_status'] == 'draft' ){
			$product_to_edit['post_status'] = 'draft';
		}else{
			$status = $form['new_product_status'];

			if( ! empty( $current_step['overwrite_settings'] ) ) $status = $current_step['new_product_status'];

			if( $status != 'no_change' ){
				$product_to_edit['post_status'] = $status;
			}elseif( $form['save_to_product'] == 'new_product' ){
				$product_to_edit['post_status'] = 'publish';
			}elseif( $form['save_to_product'] == 'edit_product' ){
				$product = wc_get_product( $product_id );
				$status = $product->get_status();
				if( $status == 'auto-draft' ) $product_to_edit['post_status'] = 'publish';
			}
			
		}	
			
		if( $product_to_edit['ID'] == 0 ){
			if( empty( $product_to_edit['post_title'] ) ){
				$product_to_edit['post_title'] = '(no-name)';
			}
			$product_id = wp_insert_post( $product_to_edit );
			update_metadata( 'post', $product_id, 'admin_form_source', $form['id'] );
		}else{
			wp_update_post( $product_to_edit );
			update_metadata( 'post', $product_id, 'admin_form_edited', $form['id'] );
		}

		if( isset( $form['product_terms'] ) && $form['product_terms'] != '' ){
			$new_terms = $form['product_terms'];					
			if( is_string( $new_terms ) ){
				$new_terms = explode( ',', $new_terms );
			}
			if( is_array( $new_terms ) ){
				foreach( $new_terms as $term_id ){
					$term = get_term( $term_id );
					if( $term ){
						wp_set_object_terms( $product_id, $term->term_id, $term->taxonomy, true );
					}
				}
			}
		}

		if( isset( $pt_field ) ){
			acf_update_value( $product_type, $product_id, $pt_field );
		}
		
		if( $form['save_to_product'] == 'duplicate_product' ){
			$taxonomies = get_object_taxonomies('product');
			foreach ($taxonomies as $taxonomy) {
			  $product_terms = wp_get_object_terms($product_to_duplicate->ID, $taxonomy, array('fields' => 'slugs'));
			  wp_set_object_terms($product_id, $product_terms, $taxonomy, false);		
			}
 
			global $wpdb;
			$product_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$product_to_duplicate->ID");
			if( count($product_meta_infos) != 0 ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
				foreach($product_meta_infos as $meta_info) {
					$meta_key        = $meta_info->meta_key;
					$meta_value      = addslashes($meta_info->meta_value);
					$sql_query_sel[] = "SELECT $product_id, '$meta_key', '$meta_value'";
				}
				$sql_query .= implode(" UNION ALL ", $sql_query_sel);
				$wpdb->query($sql_query);
			}
		}

		if( ! empty( $metas ) ){
			foreach( $metas as $meta ){
				acf_update_value( $meta['_input'], $product_id, $meta );
			}
		}

		$form['record']['product'] = $product_id;
		
		

		do_action( FEA_PREFIX.'/save_product', $form, $product_id );
		do_action( ALT_PREFIX.'/save_product', $form, $product_id );
		return $form;
	}

	public function __construct(){
		add_filter( 'acf_frontend/save_form', array( $this, 'save_form' ), 4 );
	}

}

fea_instance()->local_actions['product'] = new ActionProduct();

endif;	