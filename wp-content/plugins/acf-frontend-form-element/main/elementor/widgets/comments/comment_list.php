<?php
namespace Frontend_WP\Widgets;

use Elementor\Controls_Manager;
use ElementorPro\Base\Base_Widget;
use Frontend_WP\Plugin;

use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Frontend_WP\Classes;
use Elementor\Widget_Base;
use ElementorPro\Modules\QueryControl\Module as Query_Module;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Comments_List_Widget extends Widget_Base{
	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'comments_list';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Comment List', FEA_NS );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-comments frontend-icon';
	}
	
	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the acf ele form widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['frontend-admin-lists'];
	}

	protected function register_controls() {
        $this->register_layout_section_controls();
		//$this->register_query_section_controls();
		//$this->register_pagination_section_controls();
	}

	protected function register_layout_section_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', FEA_NS ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);


		$this->add_control(
			'comments_per_page',
			[
				'label' => __( 'Comments Per Page', FEA_NS ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
        );
        
		$repeater = new \Elementor\Repeater();
		$comment_data_options = [
			'user' => [
				'label' => __( 'Author', FEA_NS ),
				'options' => [
					'display name'  => __( 'Display Name', FEA_NS ),
					'user email'  => __( 'Author Email', FEA_NS ),
					'first name' => __( 'First Name', FEA_NS ),
					'last name' => __( 'Last Name', FEA_NS ),
					'profile image'  => __( 'Profile Image', FEA_NS ),
				],
			],			
			'comment' => [
				'label' => __( 'Comment', FEA_NS ),
				'options' => [
					'comment content'  => __( 'Comment Body', FEA_NS ),
				],
			],			
			'custom' => [
				'label' => __( 'Custom', FEA_NS ),
				'options' => [
					'custom'  => __( 'Custom', FEA_NS ),
					'custom field'  => __( 'Custom Field Data', FEA_NS ),
				],
			],
			'layout' => [
				'label' => __( 'Layout', FEA_NS ),
				'options' => [
					'group'  => __( 'Group', FEA_NS ),
				],
			],
		];

		$repeater->start_controls_tabs( 'tabs_comment_data' );

		$repeater->start_controls_tab(
			'tab_comment_data_content',
			[
				'label' => __( 'Content', FEA_NS ),
			]
		);

        $repeater->add_control(
			'comment_data',
			[
				'label' => __( 'Comment Data', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'display_name',
				'groups' => $comment_data_options,
			]
		);
		$repeater->add_control(
			'group_end',
			[
				'label' => __( 'End Point', FEA_NS ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'true',
                'condition' => [ 
					'comment_data' => 'group',
				],
			]
		);

        $repeater->add_control(
			'data_type',
			[
				'label' => __( 'Type', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text'  => __( 'Text', FEA_NS ),
					'image' => __( 'Image', FEA_NS ),
                ],
                'condition' => [ 
					'comment_data' => ['custom'],
				],
			]
		);           
		$repeater->add_control(
			'data_field_type',
			[
				'label' => __( 'Type', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text'  => __( 'Text', FEA_NS ),
					'image' => __( 'Image', FEA_NS ),
					'choices' => __( 'Choices', FEA_NS ),
                ],
                'condition' => [ 
					'comment_data' => ['custom field'],
				],
			]
		);    
		$repeater->add_control(
			'custom_text', [
				'show_label' => false,
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Some Custom Text' , FEA_NS ),
                'condition' => [
                    'comment_data' => 'custom',
					'data_type' => 'text',
				],	
				'dynamic' => [
					'active' => true,
				],
            ]
		);
		$repeater->add_control(
			'custom_field_source',
			[
				'label' => __( 'Source', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'comment',
				'options' => [
					'comment'  => __( 'Comment', FEA_NS ),
					'author' => __( 'Author', FEA_NS ),
                ],
                'condition' => [ 
					'comment_data' => 'custom field',
				],
			]
        );       
    
        $repeater->add_control(
			'comment_custom_text',
			[
				'label' => __( 'Field Key', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => feadmin_get_field_data( 'text' ),
				'required' => 'true',
                'condition' => [ 
					'comment_data' => 'custom field', 
					'data_field_type' => 'text',
				],
			]
		);         
		$repeater->add_control(
			'comment_custom_choice',
			[
				'label' => __( 'Field Key', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => feadmin_get_field_data( ['radio', 'select', 'button_group'] ),
				'required' => 'true',
                'condition' => [ 
					'comment_data' => 'custom field', 
					'data_field_type' => 'choices',
				],
			]
		);            
		$repeater->add_control(
			'value_label',
			[
				'label' => __( 'Display', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'value' => __( 'value', FEA_NS ),
					'label' => __( 'Label', FEA_NS ),
				],
				'default' => 'value',
				'toggle' => false,
				'condition' => [ 
					'comment_data' => 'custom field', 
					'data_field_type' => 'choices',
				],
			]
		);    

		$text_based_fields = [
			[
				'name' => 'comment_data',
				'operator' => 'in',
				'value' => ['display name', 'first name', 'last name', 'user email', 'comment content'],
			],
			[
				'relation' => 'and',
				'terms' => [
					[
						'name' => 'comment_data',
						'operator' => 'in',
						'value' => ['custom']
					],
					[
						'name' => 'data_type',
						'operator' => 'in',
						'value' => ['text']
					]
				]
			],			
			[
				'relation' => 'and',
				'terms' => [
					[
						'name' => 'comment_data',
						'operator' => 'in',
						'value' => ['custom field']
					],
					[
						'name' => 'data_field_type',
						'operator' => 'in',
						'value' => ['text', 'choices']
					]
				]
			],
		];		
		$image_based_fields = [
			[
				'name' => 'comment_data',
				'operator' => 'in',
				'value' => ['profile image'],
			],
			[
				'relation' => 'and',
				'terms' => [
					[
						'name' => 'comment_data',
						'operator' => 'in',
						'value' => ['custom']
					],
					[
						'name' => 'data_type',
						'operator' => 'in',
						'value' => ['image']
					]
				]
			],			
			[
				'relation' => 'and',
				'terms' => [
					[
						'name' => 'comment_data',
						'operator' => 'in',
						'value' => ['custom field']
					],
					[
						'name' => 'data_field_type',
						'operator' => 'in',
						'value' => ['image']
					]
				]
			],
		];

		$repeater->add_control(
			'comment_before_text',
			[
				'label' => __( 'Before Text', FEA_NS ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);                
		$repeater->add_control(
			'comment_after_text',
			[
				'label' => __( 'After Text', FEA_NS ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);                
		$repeater->add_control(
			'comment_default_text',
			[
				'label' => __( 'Default Text', FEA_NS ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
        );        
		$repeater->add_control(
			'comment_custom_image',
			[
				'label' => __( 'Field Key', FEA_NS ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => feadmin_get_field_data( 'image' ),
				'required' => 'true',
                'condition' => [ 
					'comment_data' => 'custom field',
					'data_field_type' => 'image',
				],
			]
        );        
		$repeater->add_control(
			'text_html_tag',
			[
				'label' => __( 'HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);

		$repeater->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner' => 'text-align: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);


		$repeater->add_control(
			'custom_image', [
                'show_label' => false,
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'comment_data' => 'custom',
					'data_type' => 'image',
				],		
				'dynamic' => [
					'active' => true,
				],
            ]
		);		
		$repeater->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'custom', 
				'default' => 'medium',
                'condition' => [
                    'comment_data' => 'custom',
					'data_type' => 'image',
                ],		
			]
		);
		$repeater->add_control(
			'default_image', [
                'label' => __( 'Default Image' , FEA_NS ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
				'conditions' => [
					'relation' => 'or',
					'terms' => $image_based_fields,
				]	
            ]
		);
		$repeater->add_control(
			'image_size', [
				'label' => __( 'Custom Size (px)', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [ 
					'size' => 96,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 500,
						'step' => 1,
					],
				],
				'condition' => [
                    'comment_data' => 'profile image',
				],
			]
		);
		$repeater->add_control(
			'image_alt', [
				'label' => __( 'Alt Text', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => __( 'display_name' , FEA_NS ),
				'options' => [
					'display name'  => __( 'Author Display Name', FEA_NS ),
					'first name' => __( 'Author First Name', FEA_NS ),
					'last name' => __( 'Author Last Name', FEA_NS ),
				],
                'condition' => [
                    'comment_data' => 'profile image',
                ],	
            ]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_comment_data_style',
			[
				'label' => __( 'Style', FEA_NS ),
			]
		);
		$repeater->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner' => 'color: {{VALUE}};',
				],
				'dynamic' => [
					'active' => true,
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner',
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner',
				'conditions' => [
					'relation' => 'or',
					'terms' => $text_based_fields,
				]
			]
		);
		
		$repeater->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%', 'px', 'vw'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner' => 'width: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => $image_based_fields,
				]
			]
		);

		$repeater->add_responsive_control(
			'image_max_width',
			[
				'label' => __( 'Max Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['%', 'px', 'vw'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => $image_based_fields,
				]
			]
		);
		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'block_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner',
				'separator' => 'before',
			]
		);

		$repeater->add_responsive_control(
			'block_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .comment-block-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'block_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}  .comment-block-inner',
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_comment_data_advanced',
			[
				'label' => __( 'Advanced', FEA_NS ),
			]
		);
		$repeater->add_responsive_control(
			'block_width',
			[
				'label' => __( 'Width', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => [
					'full' => __( 'Full Width', 'elementor' ) . ' (100%)',
					'auto' => __( 'Inline', 'elementor' ) . ' (auto)',
					'initial' => __( 'Custom', 'elementor' ),
				],
				'selectors_dictionary' => [
					'full' => '100%',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.comment-content-block' => 'width: {{VALUE}}',
				],
			]
		);

		$repeater->add_responsive_control(
			'block_custom_width',
			[
				'label' => __( 'Custom Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'max' => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'block_width' => 'initial',
				],
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'condition' => [
							'_block_width_tablet' => ['initial'],
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'condition' => [
							'_block_width_mobile' => ['initial'],
						],
					],
				],
				'size_units' => ['px', '%', 'vw'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.comment-content-block' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
				],
			]
		);


		$repeater->add_control(
			'block_padding',
			[
				'label' => __( 'Padding', FEA_NS ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default' => [
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'true',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.comment-content-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);		

		$repeater->add_control(
			'block_margin',
			[
				'label' => __( 'Margin', FEA_NS ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'isLinked' => 'true',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.comment-content-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);				
	
		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();



		$this->add_control(
			'comment_list_data',
			[
				'label' => __( 'User Data', FEA_NS ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'comment_data' => 'profile image',
						'image_size' => [ 
							'size' => 50,
							'unit' => 'px',
						],
						'block_border_radius' => [
							'top' => 50,
							'right' => 50,
							'bottom' => 50,
							'left' => 50,
							'unit' => 'px',
						],
						'block_width' => 'auto',
					],
					[
						'comment_data' => 'display name',
						'block_width' => 'auto',
						'text_html_tag' => 'h4',
					],					
					[
						'comment_data' => 'comment content',
						'text_html_tag' => 'p',
					],
                ],
				'title_field' => '<span style="text-transform: capitalize;">{{{ comment_data }}}</span>',
			]
        );
        

		$this->end_controls_section();
		
		
		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => __( 'Layout', FEA_NS ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		
		$this->add_control(
			'comment_margin',
			[
				'label' => __( 'Comment Margin', FEA_NS ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default' => [
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'true',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-comment__single' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);				
		$this->add_control(
			'comment_space',
			[
				'label' => __( 'Comment Spacing', FEA_NS ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-comment__single:not(:first-of-type)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],	
			]
		);			
		$this->add_control(
			'reply_space',
			[
				'label' => __( 'Reply Spacing', FEA_NS ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-comment__single.comment-reply' => 'margin-top: {{SIZE}}{{UNIT}};',
				],	
			]
		);	
		$this->add_control(
			'comment_padding',
			[
				'label' => __( 'Comment Paddings', FEA_NS ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'false',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-comment__single-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'single_comment_border',
				'label' => __( 'Border', 'elementor' ),
				'selector' => '{{WRAPPER}} .elementor-comment__single-inner',
			]
		);
		$this->add_control(
			'comment_border_radius',
			[
				'label' => __( 'Comment Border Radius', FEA_NS ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default' => [
					'top' => 'o',
					'bottom' => 'o',
					'left' => 'o',
					'right' => 'o',
					'isLinked' => 'true',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-comment__single-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);

	
    

		$this->end_controls_section();

	}


	public function get_query() {
		global $post;
		return[
			'post_id' => $post->ID,
			'order' => 'ASC',
			'parent' => 0,
		];
	}

	public function render() {
		$settings = $this->get_settings();

		?>
		<div class="elementor-comments-container elementor-comments">
		<?php

		$list_comments = get_comments( $this->get_query() );
		
		if ( !empty( $list_comments ) ) {
			$comment_count = 0;
    		foreach ( $list_comments as $list_comment ){
				if( $settings['comments_per_page'] <= $comment_count ){
					break;
				}
				$this->render_comment( $list_comment, $settings );
				
				$comment_count++;
			}
		}

		?>
		</div>	
		<?php
    }
    
    protected function render_comment( $list_comment, $settings, $is_reply = false ) {
		$reply_class = '';
		if( $is_reply ){
			$reply_class = ' comment-reply';
		}

		?>
			<div class="elementor-comment__single<?php echo $reply_class ?>">
			   <div class="elementor-comment__single-inner comment-<?php echo $list_comment->comment_ID ?>">
               <?php $this->render_comment_content( $list_comment, $settings ); ?>
			   </div>
			<?php
			$reply_query = $this->get_query();
			$reply_query['parent'] = $list_comment->comment_ID;
			$replies = get_comments( $reply_query );
		
			if ( !empty( $replies ) ) {
				foreach ( $replies as $reply ){
					$this->render_comment( $reply, $settings, true );
				}
			}
            ?>
			</div>
		<?php
	
	}

	public function render_comment_content( $list_comment, $settings ){
		$group = false;
		$content = '';

		foreach( $settings['comment_list_data'] as $index => $comment_data ){	
			$block_data = str_replace( ' ', '_', $comment_data['comment_data'] );

			if( $block_data == 'group' ){
				if( $comment_data['group_end'] == 'true' ){
					if( $group ){
						$content = '</div>';
						$group = false;
					}
				}else{
					$close_group = '';
					if( $group ){
						$close_group = '</div>';
					}
					$content = $close_group . '<div class="elementor-repeater-item-' . $comment_data['_id'] . ' comment-content-block group-block">';
					$group = true;
				}
			}else{
			
				$html_tag = $comment_data['text_html_tag'];
				$open_tag = '<' . $html_tag . ' class="comment-block-inner">';
				$close_tag = '</' . $html_tag . '>';
				$author_object = get_user_by( 'ID', $list_comment->user_id );
				$block = $type = $text_to_display = '';

				switch ( $block_data ) {
					case 'profile_image':
						$type = 'image';
						$alt_text = __( 'Profile Image', FEA_NS );
						$alt_data = $comment_data['image_alt'];
						if( $author_object ){
							$alt_text = $author_object->$alt_data;
						}else{
							$alt_text = $list_comment->comment_author;
						}    
						$block = get_avatar( $list_comment, $comment_data['image_size']['size'], $comment_data['default_image']['url'], $alt_text, ['class' => 'comment-block-inner'] );
						break;
					case 'custom': 
						$custom_size = $comment_data['custom_size'];
						if( $custom_size == 'custom' ){
							$custom_size = $this->get_custom_dimensions( $comment_data['custom_custom_dimension'] );
						}
						if( $comment_data['data_type'] == 'image' ){
							$type = 'image';
							$image = $comment_data['custom_image']['id'];
							$block = wp_get_attachment_image( $image, $custom_size, false, ['class' => 'comment-block-inner'] );
						}
						if( $comment_data['data_type'] == 'text' ){
							$type = 'text';
							$text_to_display = $comment_data['custom_text'];
						}
						break;
					case 'display_name':
					case 'first_name':
					case 'last_name':
					case 'user_email':
						$type = 'text';
						$author_info = '';
						if( $author_object ){
							$author_info = $author_object->$block_data;
						}else{
							if( $text_data == 'user_email' ){
								$author_info = $list_comment->comment_author_email;
							}else{
								$author_info = $list_comment->comment_author;
							}
						}    
						$text_to_display = $author_info;
						break;
					case 'comment_content':
						$type = 'text';
						$text_to_display = $list_comment->comment_content;
						break;	
						case 'last_name':
					case 'custom_field':
						if( $comment_data['custom_field_source'] == 'comment' ){
							$source_id = 'comment_' . $list_comment->comment_ID;
						}
						if( $comment_data['custom_field_source'] == 'author' ){
							$source_id = 'user_' . $list_comment->user_id;
						}
						if( $source_id ){
							if( $comment_data['data_field_type'] == 'text' ){
								$comment_custom_field = get_field( $comment_data['comment_custom_text'], $source_id );
								$type = 'text';
								if( $comment_custom_field ){
									$text_to_display = $comment_custom_field;
								}
							}							
							if( $comment_data['data_field_type'] == 'image' ){
								$comment_custom_field = get_field( $comment_data['comment_custom_image'], $source_id );
								$custom_size = $comment_data['custom_size'];
								if( $custom_size == 'custom' ){
									$custom_size = $this->get_custom_dimensions( $comment_data['custom_custom_dimension'] );
								}
								if( $comment_custom_field ){
									$block = wp_get_attachment_image( $comment_custom_field['ID'], $custom_size, false, ['class' => 'comment-block-inner'] );
								}
							}
							if( $comment_data['data_field_type'] == 'choices' ){
								$choice_field = get_field_object( $comment_data['comment_custom_choice'], $source_id );
								if( $choice_field ){
									$choice_value = esc_attr( $choice_field['value'] );
									$choice_label = esc_html( $choice_field['choices'][ $choice_value ] );
								}
								$type = 'text';
									$choice_text = $comment_data['value_label'];
									if( $choice_text == 'label' ){
										$text_to_display = $choice_label;
									}else{
										$text_to_display = $choice_value;
									}
							}
						}
						break;
				}
				if( $type == 'text' ){
					if( ! $text_to_display ){
						$block = $open_tag . $comment_data['comment_default_text'] . $close_tag;
					}else{
						$block = $open_tag . $comment_data['comment_before_text'] . $text_to_display . $comment_data['comment_after_text'] . $close_tag;
					}
				}

				$content = '<div class="elementor-repeater-item-' . $comment_data['_id'] . ' comment-content-block">';
				$content .= $block;
				$content .= '</div>';
			}

			echo $content;
		}
	}

	public function get_custom_dimensions( $custom_dimension ){
		$attachment_size = [
			// Defaults sizes
			0 => null, // Width.
			1 => null, // Height.

			'bfi_thumb' => true,
			'crop' => true,
		];

		$has_custom_size = false;
		if ( ! empty( $custom_dimension['width'] ) ) {
			$has_custom_size = true;
			$attachment_size[0] = $custom_dimension['width'];
		}

		if ( ! empty( $custom_dimension['height'] ) ) {
			$has_custom_size = true;
			$attachment_size[1] = $custom_dimension['height'];
		}

		if ( ! $has_custom_size ) {
			$attachment_size = 'full';
		}
		return $attachment_size;
	}
}
