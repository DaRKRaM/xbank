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
class Posts_List_Widget extends Widget_Base{
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
		return 'posts_list';
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
		return __( 'Post List', FEA_NS );
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
		return 'fas fa-list frontend-icon';
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
		return array('frontend-admin-posts');
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
			'posts_per_page',
			[
				'label' => __( 'Posts Per Page', FEA_NS ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
        );
        
		$acf_frontend_groups = feadmin_get_field_type_groups();

		$repeater = new \Elementor\Repeater();

		/* $post_data_options = [
			'user' => $acf_frontend_groups['user'],			
			'post' => $acf_frontend_groups['post'],			
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
					
				],
			],
		]; */

		$repeater->start_controls_tabs( 'tabs_post_data' );

		$repeater->start_controls_tab(
			'tab_post_data_content',
			[
				'label' => __( 'Content', FEA_NS ),
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
					'group'  => __( 'Group', FEA_NS ),
                ],
			]
		);   

        $repeater->add_control(
			'render_text',
			[
				'label' => __( 'Text', FEA_NS ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Post Text',
                'dynamic' => [
                    'active' => true
                ],
				'condition' => [ 
					'data_type' => ['text'],
				],
			]
		);
     
		$repeater->add_control(
			'group_end',
			[
				'label' => __( 'End Point', FEA_NS ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'true',
                'condition' => [ 
					'data_type' => ['group'],
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
				'condition' => [ 
					'data_type' => ['text'],
				],
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner' => 'text-align: {{VALUE}};',
				],
				'condition' => [ 
					'data_type' => ['text'],
				],
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
					'data_type' => ['image'],
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
					'data_type' => ['image'],
				],	
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
					'data_type' => ['image'],
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_post_data_style',
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner' => 'color: {{VALUE}};',
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [ 
					'data_type' => ['text'],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner',
				'condition' => [ 
					'data_type' => ['text'],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner',
				'condition' => [ 
					'data_type' => ['text'],
				],
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'data_type' => ['image'],
				],
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'data_type' => ['image'],
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'block_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner',
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .post-block-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}  .post-block-inner',
			]
		);
		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_post_data_advanced',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}.post-content-block' => 'width: {{VALUE}}',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}.post-content-block' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}.post-content-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}.post-content-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);				
	
		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();



		$this->add_control(
			'post_list_data',
			[
				'label' => __( 'User Data', FEA_NS ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'data_type' => 'text',
					],
					[
						'data_type' => 'image',
					],		
                ],
				'title_field' => '<span style="text-transform: capitalize;">{{{ data_type }}}</span>',
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
			'post_margin',
			[
				'label' => __( 'Post Margin', FEA_NS ),
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
					'{{WRAPPER}} .elementor-post__single' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);				
		$this->add_control(
			'post_space',
			[
				'label' => __( 'Post Spacing', FEA_NS ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__single:not(:first-of-type)' => 'margin-top: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .elementor-post__single.post-reply' => 'margin-top: {{SIZE}}{{UNIT}};',
				],	
			]
		);	
		$this->add_control(
			'post_padding',
			[
				'label' => __( 'Post Paddings', FEA_NS ),
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
					'{{WRAPPER}} .elementor-post__single-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'single_post_border',
				'label' => __( 'Border', 'elementor' ),
				'selector' => '{{WRAPPER}} .elementor-post__single-inner',
			]
		);
		$this->add_control(
			'post_border_radius',
			[
				'label' => __( 'Post Border Radius', FEA_NS ),
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
					'{{WRAPPER}} .elementor-post__single-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);

	
    

		$this->end_controls_section();

	}


	public function get_query() {
		global $post;
		/* return[
			'post_id' => $post->ID,
			'order' => 'ASC',
			'parent' => 0,
		]; */
        return [
            'numberposts' => 10,
            'post_type'   => 'post'
        ];
	}

	public function render() {
		$settings = $this->get_settings();

		?>
		<div class="elementor-posts-container elementor-posts">
		<?php

		$post_query = new \WP_Query( $this->get_query() );
		
        if($post_query->have_posts() ) {
            $post_count = 1;
            while($post_query->have_posts() ) {
                $post_query->the_post();
    		//foreach ( $list_posts as $post ){
                $post = get_post( get_the_ID() );
                //setup_postdata( $post ); 
				if( $settings['posts_per_page'] <= $post_count ){
					break;
				}
				$this->render_post( $post, $settings, $post_count );
				
				$post_count++;
			}
		}
        wp_reset_query();

		?>
		</div>	
		<?php
    }
    
    protected function render_post( $list_post, $settings, $is_reply = false ) {
		$reply_class = '';
		if( $is_reply ){
			$reply_class = ' post-reply';
		}

		?>
			<div class="elementor-post__single<?php echo $reply_class ?>">
			   <div class="elementor-post__single-inner post-<?php echo $list_post->post_ID ?>">
               <?php $this->render_post_content( $list_post, $settings ); ?>
			   </div>
			<?php
			/* $reply_query = $this->get_query();
			$reply_query['parent'] = $list_post->post_ID;
			$replies = get_posts( $reply_query );
		
			if ( !empty( $replies ) ) {
				foreach ( $replies as $reply ){
					$this->render_post( $reply, $settings, true );
				}
			} */
            ?>
			</div>
		<?php
	
	}

	public function render_post_content( $list_post, $settings ){
		$group = false;
		$content = '';

		foreach( $settings['post_list_data'] as $index => $post_data ){	
            if( isset( $post_data['__dynamic__']['post_render_text'] ) ){
                $setting = $post_data['__dynamic__']['post_render_text'];
                $post_render_text = \Elementor\Plugin::$instance->dynamic_tags->parse_tags_text( $setting, $post_data, [ \Elementor\Plugin::$instance->dynamic_tags, 'get_tag_data_content'] );
            }else{ 
                $post_render_text = $post_data['post_render_text'];
            }

            
            echo $post_render_text;
			/* $block_data = str_replace( ' ', '_', $post_data['post_data'] );

			if( $block_data == 'group' ){
				if( $post_data['group_end'] == 'true' ){
					if( $group ){
						$content = '</div>';
						$group = false;
					}
				}else{
					$close_group = '';
					if( $group ){
						$close_group = '</div>';
					}
					$content = $close_group . '<div class="elementor-repeater-item-' . $post_data['_id'] . ' post-content-block group-block">';
					$group = true;
				}
			}else{
			
				$html_tag = $post_data['text_html_tag'];
				$open_tag = '<' . $html_tag . ' class="post-block-inner">';
				$close_tag = '</' . $html_tag . '>';
				$author_object = get_user_by( 'ID', $list_post->user_id );
				$block = $type = $text_to_display = '';

				switch ( $block_data ) {
					case 'profile_image':
						$type = 'image';
						$alt_text = __( 'Profile Image', FEA_NS );
						$alt_data = $post_data['image_alt'];
						if( $author_object ){
							$alt_text = $author_object->$alt_data;
						}else{
							$alt_text = $list_post->post_author;
						}    
						$block = get_avatar( $list_post, $post_data['image_size']['size'], $post_data['default_image']['url'], $alt_text, ['class' => 'post-block-inner'] );
						break;
					case 'custom': 
						$custom_size = $post_data['custom_size'];
						if( $custom_size == 'custom' ){
							$custom_size = $this->get_custom_dimensions( $post_data['custom_custom_dimension'] );
						}
						if( $post_data['data_type'] == 'image' ){
							$type = 'image';
							$image = $post_data['custom_image']['id'];
							$block = wp_get_attachment_image( $image, $custom_size, false, ['class' => 'post-block-inner'] );
						}
						if( $post_data['data_type'] == 'text' ){
							$type = 'text';
							$text_to_display = $post_data['custom_text'];
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
							if( $block_data == 'user_email' ){
								$author_info = $list_post->post_author_email;
							}else{
								$author_info = $list_post->post_author;
							}
						}    
						$text_to_display = $author_info;
						break;
					case 'post_content':
						$type = 'text';
						$text_to_display = $list_post->post_content;
						break;	
						case 'last_name':
					case 'custom_field':
						if( $post_data['custom_field_source'] == 'post' ){
							$source_id = 'post_' . $list_post->post_ID;
						}
						if( $post_data['custom_field_source'] == 'author' ){
							$source_id = 'user_' . $list_post->user_id;
						}
						if( $source_id ){
							if( $post_data['data_field_type'] == 'text' ){
								$post_custom_field = get_field( $post_data['post_custom_text'], $source_id );
								$type = 'text';
								if( $post_custom_field ){
									$text_to_display = $post_custom_field;
								}
							}							
							if( $post_data['data_field_type'] == 'image' ){
								$post_custom_field = get_field( $post_data['post_custom_image'], $source_id );
								$custom_size = $post_data['custom_size'];
								if( $custom_size == 'custom' ){
									$custom_size = $this->get_custom_dimensions( $post_data['custom_custom_dimension'] );
								}
								if( $post_custom_field ){
									$block = wp_get_attachment_image( $post_custom_field['ID'], $custom_size, false, ['class' => 'post-block-inner'] );
								}
							}
							if( $post_data['data_field_type'] == 'choices' ){
								$choice_field = get_field_object( $post_data['post_custom_choice'], $source_id );
								if( $choice_field ){
									$choice_value = esc_attr( $choice_field['value'] );
									$choice_label = esc_html( $choice_field['choices'][ $choice_value ] );
								}
								$type = 'text';
									$choice_text = $post_data['value_label'];
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
						$block = $open_tag . $post_data['post_default_text'] . $close_tag;
					}else{
						$block = $open_tag . $post_data['post_before_text'] . $text_to_display . $post_data['post_after_text'] . $close_tag;
					}
				}

				$content = '<div class="elementor-repeater-item-' . $post_data['_id'] . ' post-content-block">';
				$content .= $block;
				$content .= '</div>'; 
			}
                */
			//echo $content;

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
