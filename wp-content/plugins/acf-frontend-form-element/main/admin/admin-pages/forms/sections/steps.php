<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$multi = array(
    'field' => 'multi',
    'operator' => '==',
    'value' => 1,
);
$tabs = array(
    'field' => 'steps_display',
    'operator' => '==contains',
    'value' => 'tabs',
);
$counter = array(
    'field' => 'steps_display',
    'operator' => '==contains',
    'value' => 'counter',
);
$fields = array(	   
    array(
        'key' => 'multi',
        'label' => __( 'Enable Multi Step', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
        'wrapper' => array(
            'width' => '50',
            'class' => '',
            'id' => '',
        ),
    ), 
    array(
        'key' => 'validate_steps',
        'label' => __( 'Validate Each Step', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
        'wrapper' => array(
            'width' => '50',
            'class' => '',
            'id' => '',
        ),
        'conditional_logic' => array(
            array(
               $multi
            ),
        ),
    ), 
    array(
        'key' => 'steps_display',
        'label' => __( 'Steps Display', FEA_NS ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'multiple' => 1,
        'ui' => 1,
        'allow_null' => 1,
        'choices' => [
            'tabs' => __( 'Tabs', FEA_NS ),
            'counter' => __( 'Counter', FEA_NS ),
        ],
       'layout' => 'horizontal',
       'conditional_logic' => array(
            array(
               $multi
            ),
        ),
    ),
    array(
        'key' => 'steps_tabs_display',
        'label' => __( 'Display Tabs On...', FEA_NS ),
        'type' => 'checkbox',
        'instructions' => '',
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'choices' => [
            'desktop' => __( 'Desktop', FEA_NS ),
            'tablet' => __( 'Tablet', FEA_NS ),
            'phone' => __( 'Mobile', FEA_NS ),
        ],
       'layout' => 'horizontal',
       'conditional_logic' => array(
            array(
            $multi,
            $tabs
            ),
        ),
    ),
    array(
        'key' => 'steps_counter_display',
        'label' => __( 'Display Counter On...', FEA_NS ),
        'type' => 'checkbox',
        'instructions' => '',
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'choices' => [
            'desktop' => __( 'Desktop', FEA_NS ),
            'tablet' => __( 'Tablet', FEA_NS ),
            'phone' => __( 'Mobile', FEA_NS ),
        ],
       'layout' => 'horizontal',
       'conditional_logic' => array(
            array(
            $multi,
            $counter
            ),
        ),
    ),
    array(
        'key' => 'tabs_align',
        'label' => __( 'Tabs Align', FEA_NS ),
        'type' => 'radio',
        'instructions' => '',
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'choices' => [
            'horizontal' => __( 'Top', 'elementor' ),
			'vertical' => __( 'Side', 'elementor' ),
        ],
        'layout' => 'horizontal',
        'conditional_logic' => array(
            array(
               $multi,
               $tabs,
            ),
        ),
    ),
    array(
        'key' => 'counter_text',
        'label' => __( 'Counter Text', FEA_NS ),
        'type' => 'text',
        'instructions' => __( 'Use [current_step] to display the current step and [total_steps] to show the total amount of steps', FEA_NS ),
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'placeholder' => '',
        'conditional_logic' => array(
            array(
               $multi,
               $counter,
            ),
        ),
    ),
    array(
        'key' => 'step_number',
        'label' => __( 'Step Number In Tabs', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                $multi,
                $tabs,
            ),
        ),
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
    ),
    array(
        'key' => 'tab_links',
        'label' => __( 'Link to Step in Tabs', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                $multi,
                $tabs,
            ),
        ),
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
    ),
);

return $fields;