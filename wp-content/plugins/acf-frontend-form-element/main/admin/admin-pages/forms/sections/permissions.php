<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

$default_row = array( array(
    'rule_name'   => __( 'Administrators', FEA_NS ),
    'who_can_see' => 'logged_in',
    'by_role'     => array( 'administrator' ),
) );
$values = array(
    'who_can_see',
    'not_allowed',
    'not_allowed_message',
    'not_allowed_content',
    'email_verification',
    'by_role',
    'by_user_id',
    'dynamic'
);
foreach ( $values as $value ) {
    if ( isset( $form[$value] ) ) {
        $default_row[0][$value] = $form[$value];
    }
}
$fields = array( array(
    'key'          => 'no_kses',
    'label'        => __( 'Allow Unfiltered HTML', FEA_NS ),
    'type'         => 'true_false',
    'instructions' => '',
    'required'     => 0,
    'ui'           => 1,
    'wrapper'      => array(
    'width' => '50',
    'class' => '',
    'id'    => '',
),
), array(
    'key'           => 'wp_uploader',
    'label'         => __( 'WP Media Library', FEA_NS ),
    'type'          => 'true_false',
    'instructions'  => __( 'Whether to use the WordPress media library for file fields or just a basic upload button', FEA_NS ),
    'required'      => 0,
    'ui'            => 1,
    'default_value' => 1,
    'wrapper'       => array(
    'width' => '50',
    'class' => '',
    'id'    => '',
),
), array(
    'key'           => 'form_conditions',
    'label'         => __( 'Conditions', FEA_NS ),
    'type'          => 'list_items',
    'instructions'  => __( 'The form will show if any of these conditions are met.', FEA_NS ),
    'required'      => 0,
    'wrapper'       => array(
    'width' => '',
    'class' => '',
    'id'    => '',
),
    'collapsed'     => 'rule_name',
    'collapsable'   => true,
    'min'           => 1,
    'max'           => '',
    'layout'        => 'block',
    'button_label'  => __( 'Add Rule', FEA_NS ),
    'remove_label'  => __( 'Remove Rule', FEA_NS ),
    'default_value' => $default_row,
    'sub_fields'    => array(
    array(
    'key'               => 'rule_name',
    'label'             => __( 'Rule Name', FEA_NS ),
    'name'              => 'name',
    'type'              => 'text',
    'instructions'      => __( 'Give this rule an identifier', FEA_NS ),
    'required'          => 1,
    'conditional_logic' => 0,
    'wrapper'           => array(
    'width' => '70',
    'class' => '',
    'id'    => '',
),
    'default_value'     => __( 'Administrators', FEA_NS ),
    'prepend'           => '',
    'append'            => '',
    'maxlength'         => '',
),
    array(
    'key'           => 'applies_to',
    'label'         => __( 'Applies to...', FEA_NS ),
    'type'          => 'checkbox',
    'instructions'  => '',
    'required'      => 1,
    'default_value' => array( 'form', 'submissions' ),
    'choices'       => array(
    'form'        => __( 'Form', FEA_NS ),
    'submissions' => __( 'Submissions', FEA_NS ),
),
),
    array(
    'key'          => 'not_allowed',
    'label'        => __( 'No Permissions Message', FEA_NS ),
    'type'         => 'select',
    'instructions' => '',
    'required'     => 0,
    'choices'      => array(
    'show_nothing'   => __( 'None', FEA_NS ),
    'show_message'   => __( 'Message', FEA_NS ),
    'custom_content' => __( 'Custom Content', FEA_NS ),
),
),
    array(
    'key'               => 'not_allowed_message',
    'label'             => __( 'Message', FEA_NS ),
    'type'              => 'textarea',
    'instructions'      => '',
    'required'          => 0,
    'rows'              => 3,
    'placeholder'       => __( 'You do not have the proper permissions to view this form', FEA_NS ),
    'default_value'     => __( 'You do not have the proper permissions to view this form', FEA_NS ),
    'conditional_logic' => array( array( array(
    'field'    => 'not_allowed',
    'operator' => '==',
    'value'    => 'show_message',
) ) ),
),
    array(
    'key'               => 'not_allowed_content',
    'label'             => __( 'Content', FEA_NS ),
    'type'              => 'wysiwyg',
    'instructions'      => '',
    'required'          => 0,
    'conditional_logic' => array( array( array(
    'field'    => 'not_allowed',
    'operator' => '==',
    'value'    => 'custom_content',
) ) ),
),
    array(
    'key'          => 'who_can_see',
    'label'        => __( 'Who Can See This...', FEA_NS ),
    'type'         => 'select',
    'instructions' => '',
    'required'     => 0,
    'choices'      => array(
    'logged_in'  => __( 'Only Logged In Users', FEA_NS ),
    'logged_out' => __( 'Only Logged Out', FEA_NS ),
    'all'        => __( 'All Users', FEA_NS ),
),
),
    array(
    'key'               => 'email_verification',
    'label'             => __( 'Email Address', FEA_NS ),
    'type'              => 'select',
    'required'          => 0,
    'choices'           => array(
    'all'        => __( 'All', FEA_NS ),
    'verified'   => __( 'Verified', FEA_NS ),
    'unverified' => __( 'Unverified', FEA_NS ),
),
    'instructions'      => 'Only show to users who verified their email address or only to those who haven\'t.',
    'conditional_logic' => array( array( array(
    'field'    => 'who_can_see',
    'operator' => '==',
    'value'    => 'logged_in',
) ) ),
),
    array(
    'key'               => 'by_role',
    'label'             => __( 'Select By Role', FEA_NS ),
    'type'              => 'select',
    'instructions'      => '',
    'conditional_logic' => array( array( array(
    'field'    => 'who_can_see',
    'operator' => '==',
    'value'    => 'logged_in',
) ) ),
    'default_value'     => array( 'administrator' ),
    'multiple'          => 1,
    'ui'                => 1,
    'choices'           => feadmin_get_user_roles( array(), true ),
),
    array(
    'key'               => 'by_user_id',
    'label'             => __( 'Select By User', FEA_NS ),
    'type'              => 'user',
    'instructions'      => '',
    'conditional_logic' => array( array( array(
    'field'    => 'who_can_see',
    'operator' => '==',
    'value'    => 'logged_in',
) ) ),
    'allow_null'        => 0,
    'multiple'          => 1,
    'return_format'     => 'id',
),
    array(
    'key'               => 'dynamic',
    'label'             => __( 'Dynamic Permissions', FEA_NS ),
    'type'              => 'select',
    'instructions'      => '',
    'conditional_logic' => array( array( array(
    'field'    => 'who_can_see',
    'operator' => '==',
    'value'    => 'logged_in',
) ) ),
    'choices'           => feadmin_user_id_fields(),
    'allow_null'        => 1,
)
),
) );
return $fields;