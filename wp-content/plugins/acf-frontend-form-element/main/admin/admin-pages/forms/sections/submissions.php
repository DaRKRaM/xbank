<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;
$form_shortcode = "[".FEA_PREFIX." submissions=\"".$post->ID."\"]";
$icon_path = '<span class="dashicons dashicons-admin-page"></span>';

$save_submissions = array(
    array(
        array(
            'field' => 'save_form_submissions',
            'operator' => '==',
            'value' => '1',
        ),
    ),
);

$fields = array(
    array(
        'key' => 'save_form_submissions',
        'label' => __( 'Save Form Submissions', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'default_value' => get_option( 'frontend_admin_save_submissions' ),
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
    ),
    array(
        'key' => 'save_all_data',
        'label' =>  __( 'Submission Requirements', FEA_NS ),
        'type' => 'select',
        'instructions' => __( 'Data will not be saved until these requirements are met.', FEA_NS ),
        'required' => 0,
        'conditional_logic' => $save_submissions,
        'choices' => array(
            'require_approval' => __( 'Admin Approval', FEA_NS ),
            'verify_email' => __( 'Email is Verified', FEA_NS ),	
        ),
        'allow_null' => 1,
        'multiple' => 1,
        'ui' => 1,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => __( 'None', FEA_NS ),
    ),	
    array(
        'key' => 'submissions_list_shortcode',
        'label' =>  __( 'Submissions Approval Shortcode', FEA_NS ),
        'type' => 'message',
        'instructions' => __( 'Use this shortcode to show a list of this form\'s submissions.', FEA_NS ),
        'message' => sprintf('<code>%s</code> ', $form_shortcode ) . '<button type="button" data-prefix="' .FEA_PREFIX. ' submissions" data-form="' .$post->ID. '" class="copy-shortcode"> '. $icon_path . 
        ' '.__( 'Copy Code', FEA_NS ).'</button>',
        'conditional_logic' => $save_submissions,

    ),
    array(
        'key' => 'no_submissions_message',
        'label' => __( 'No Submissions Message', FEA_NS ),
        'type' => 'textarea',
        'instructions' => __( 'Show a message if no submissions have been received yet. Leave blank for no message.', FEA_NS ),
        'required' => 0,
        'rows' => 3,
        'placeholder' => __( 'There are no submissions for this form.', FEA_NS ),
        'conditional_logic' => $save_submissions,
    ),	
    array (
        'key' => 'total_submissions',
        'label' => __( 'Total Submissions', FEA_NS ),
        'type' => 'number',
        'instructions' => __( 'Limit the amount of shown in total.', FEA_NS ),
        'conditional_logic' => $save_submissions,
        'placeholder' => __( 'All', FEA_NS ),
        'min' => 1,
        'wrapper' => array (
            'width' => '30',
            'class' => '',
            'id' => '',
        ),
    ),
    array (
        'key' => 'submissions_per_page',
        'label' => __( 'Number of Submissions Per Load', FEA_NS ),
        'type' => 'number',
        'instructions' => __( 'Limit the amount of submissions loaded each time. Default is 10', FEA_NS ),
        'conditional_logic' => $save_submissions,
        'placeholder' => 10,
        'min' => 1,
        'wrapper' => array (
            'width' => '30',
            'class' => '',
            'id' => '',
        ),
    ),
);


return $fields;