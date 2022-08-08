<div class="acf-field-list-wrap">
	
	<ul class="acf-hl acf-thead">
		<li class="li-field-order"><?php 
_e( 'Order', 'acf' );
?></li>
		<li class="li-field-label"><?php 
_e( 'Label', 'acf' );
?></li>
		<li class="li-field-name"><?php 
_e( 'Name', 'acf' );
?></li>
		<li class="li-field-key"><?php 
_e( 'Key', 'acf' );
?></li>
		<li class="li-field-type"><?php 
_e( 'Type', 'acf' );
?></li>
	</ul>
	
	<div class="acf-field-list
	<?php 
if ( !$fields ) {
    echo  ' -empty' ;
}
?>
	">
		
		<div class="no-fields-message">
			<?php 
_e( 'No fields. Click the <strong>+ Add Field</strong> button to create your first field.', 'acf' );
?>
		</div>
		
		<?php 

if ( $fields ) {
    $step = 0;
    foreach ( $fields as $i => $field ) {
        if ( $field['type'] == 'form_step' ) {
            $step++;
        }
        fea_instance()->form_builder->get_view( 'form-field-object', array(
            'field' => $field,
            'i'     => $i,
            'step'  => $step,
        ) );
    }
}

?>
		
	</div>
	
	<ul class="acf-hl acf-tfoot">
		<li class="acf-fr">
			<a href="#" class="button button-primary button-large add-field"><?php 
_e( '+ Add Field', FEA_NS );
?></a>
		</li>
		<?php 
?>
	</ul>
	
<?php 

if ( !$parent ) {
    $clone_args = array(
        'ID'    => 'acfcloneindex',
        'key'   => 'acfcloneindex',
        'label' => '',
        'name'  => '',
        'type'  => 'text',
    );
    // get clone
    $field = acf_get_valid_field( $clone_args );
    ?>
	<script type="text/html" id="tmpl-acf-field">
	<?php 
    fea_instance()->form_builder->get_view( 'form-field-object', array(
        'field' => $field,
        'i'     => 0,
        'step'  => 0,
    ) );
    ?>
	</script>
	<?php 
    $clone_args['type'] = 'form_step';
    $clone_args['label'] = __( 'Step', FEA_NS );
    $step = acf_get_valid_field( $clone_args );
    ?>
	<script type="text/html" id="tmpl-acf-step">
	<?php 
    fea_instance()->form_builder->get_view( 'form-field-object', array(
        'field' => $step,
        'i'     => 0,
        'step'  => 0,
    ) );
    ?>
	</script>
<?php 
}

?>
	
</div>
