<?php

class Frontend_Form_Element extends OxyEl {

    var $js_added = true;

    function name() {
        return __( 'Frontend Form', FEA_NS );
    }

    function enableFullPresets() {
        return true;
    }

    function icon() {
        return CT_FW_URI.'/toolbar/UI/oxygen-icons/add-icons/pro-menu.svg';
    }

    function button_place() {
        return "acf::frontend";
    }

    function button_priority() {
        return 9;
    }

    function init() {
        fea_instance()->wp_hooks->enqueue_scripts( 'frontend_admin_form' );
		fea_instance()->wp_hooks->acfdata( true );
    }

    function presetsDefaults($defaults) {

        $default_pro_menu_presets = array();
        
        $defaults = array_merge($defaults, $default_pro_menu_presets);

        return $defaults;
    }

    function afterInit() {
        $this->removeApplyParamsButton();
    }

    function controls() {

        // Menu list custom control. TODO: Do we need an easy API way of adding this type of control?

        $forms = get_posts( array( 'hide_empty' => false, 'post_type' => 'admin_form' ) ); 

        if( $forms ){ 
               
            // prepare a list of id:name pairs
            $forms_list = array(); 
            foreach ( $forms as $key => $form ) {
                $forms_list[$form->ID] = $form->post_title;
            } 
            $forms_list = json_encode( $forms_list );
            $forms_list = htmlspecialchars( $forms_list, ENT_QUOTES );

            ob_start(); ?>

                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Form","acf-frontend-form-element"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current"
                                        ng-init="formsList=<?php echo $forms_list; ?>">{{formsList[iframeScope.getOption('form_id')]}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    <?php foreach( $forms as $key => $form ) : ?>
                                    <div class="oxygen-select-box-option" 
                                        ng-click="iframeScope.setOptionModel('form_id','<?php echo $form->ID; ?>')"><?php echo $form->post_title; ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 

            $html = ob_get_clean();

            $this->addCustomControl($html, 'form_id')->rebuildElementOnChange();
            
        }
        
        // Label Section
        $label_section = $this->
            addControlSection("labels", __("Labels"), "assets/icon.png", $this);
            
        // Alignment control
        $label_text_align = $label_section->
            addControl("buttons-list", "label_text_align", __("Text Align") );
        $label_text_align->setValue( array("Left", "Center", "Right") );
        $label_text_align->setValueCSS( array(
            "Left" => "
                .acf-label {
                    text-align: left;
                    justify-content: flex-start;
                }
            ",
            "Center" => "
                .acf-label {
                    text-align: center;
                    justify-content: center;
                }
            ",
            "Right" => "
                .acf-label {
                    text-align: right;
                    justify-content: flex-end;
                }
            ",
        ) );
        $label_text_align->whiteList();
        
        $label_text_selector  = ".acf-label";
        
        $label_section->addStyleControls(
            array(
                array(
                    "name" => __('Spacing'),
                    "selector" => $label_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'padding-bottom',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Text Color'),
                    "selector" => $label_text_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => __('Background Color'),
                    "selector" => $label_text_selector,
                    "property" => 'background-color',
                ),
            )
        );
        
        $label_typography_section = $label_section->
            addControlSection("label_typography", __("Typography"), "assets/icon.png", $this);
        
        $label_typography_section->addStyleControls(
            array(
                array(
                    "name" => __('Font Family'),
                    "selector" => $label_text_selector,
                    "property" => 'font-family',
                ),
                array(
                    "name" => __('Font Size'),
                    "selector" => $label_text_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                ),
                array(
                    "name" => __('Font Weight'),
                    "selector" => $label_text_selector,
                    "control_type" => 'dropdown',
                    "property" => 'font-weight',
                ),
                array(
                    "name" => __('Text Transform'),
                    "selector" => $label_text_selector,
                    "property" => 'text-transform',
                ),
                /*array(
                    "name" => __('Font Style'),
                    "selector" => $label_text_selector,
                    "property" => 'font-style',
                ),*/
                array(
                    "name" => __('Text Decoration'),
                    "selector" => $label_text_selector,
                    "property" => 'text-decoration',
                ),
                array(
                    "name" => __('Line Height'),
                    "selector" => $label_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'line-height',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Letter Spacing'),
                    "selector" => $label_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'letter-spacing',
                ),
            )
        );
        // End Labels section
        
        // Instruction Section
        $instruction_section = $this->
            addControlSection("instructions", __("Instructions"), "assets/icon.png", $this);
        $instruction_text_selector  = ".acf-input .description";    
        
        // Alignment control
        $instruction_text_align = $instruction_section->
            addControl("buttons-list", "instruction_text_align", __("Text Align") );
        $instruction_text_align->setValue( array("Left", "Center", "Right") );
        $instruction_text_align->setValueCSS( array(
            "Left" => "
                .acf-input .description {
                    text-align: left;
                    justify-content: flex-start;
                }
            ",
            "Center" => "
                .acf-input .description {
                    text-align: center;
                    justify-content: center;
                }
            ",
            "Right" => "
                .acf-input .description {
                    text-align: right;
                    justify-content: flex-end;
                }
            ",
        ) );
        $instruction_text_align->whiteList();
        
        $instruction_section->addStyleControls(
            array(
                array(
                    "name" => __('Spacing'),
                    "selector" => $instruction_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'padding-bottom',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Text Color'),
                    "selector" => $instruction_text_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => __('Background Color'),
                    "selector" => $instruction_text_selector,
                    "property" => 'background-color',
                ),
            )
        );
        
        $instruction_typography_section = $instruction_section->addControlSection("instruction_typography", __("Typography"), "assets/icon.png", $this);
        
        $instruction_typography_section->addStyleControls(
            array(
                array(
                    "name" => __('Font Family'),
                    "selector" => $instruction_text_selector,
                    "property" => 'font-family',
                ),
                array(
                    "name" => __('Font Size'),
                    "selector" => $instruction_text_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                ),
                array(
                    "name" => __('Font Weight'),
                    "selector" => $instruction_text_selector,
                    "control_type" => 'dropdown',
                    "property" => 'font-weight',
                ),
                array(
                    "name" => __('Text Transform'),
                    "selector" => $instruction_text_selector,
                    "property" => 'text-transform',
                ),
                /*array(
                    "name" => __('Font Style'),
                    "selector" => $instruction_text_selector,
                    "property" => 'font-style',
                ),*/
                array(
                    "name" => __('Text Decoration'),
                    "selector" => $instruction_text_selector,
                    "property" => 'text-decoration',
                ),
                array(
                    "name" => __('Line Height'),
                    "selector" => $instruction_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'line-height',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Letter Spacing'),
                    "selector" => $instruction_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'letter-spacing',
                ),
            )
        );
        // End Instructions section
        
        // Fields Section
        $field_section = $this->addControlSection("field", __("Fields"), "assets/icon.png", $this);
        $field_text_selector  = "input, textarea";    
        
        // Field text Alignment control
        $field_text_align = $field_section->addControl("buttons-list", "field_text_align", __("Text Align") );
        $field_text_align->setValue( array("Left", "Center", "Right") );
        $field_text_align->setValueCSS( array(
            "Left" => "
                $field_text_selector {
                    text-align: left;
                }
            ",
            "Center" => "
                $field_text_selector {
                    text-align: center;
                }
            ",
            "Right" => "
                $field_text_selector {
                    text-align: right;
                }
            ",
        ) );
        $field_text_align->whiteList();

        $field_section->addStyleControls(
            array(
                array(
                    "name" => __('Text Color'),
                    "selector" => $field_text_selector,
                    "property" => 'color',
                ),
                array(
                    "name" => __('Background Color'),
                    "selector" => $field_text_selector,
                    "property" => 'background-color',
                ),
            )
        );
        
        $field_section->addPreset(
            "padding",
            "field_padding",
            __("Field Padding"),
            $field_text_selector
        )->whiteList();
        
        $field_section->addPreset(
            "margin",
            "field_margin",
            __("Field Margin"),
            $field_text_selector
        )->whiteList();
        
        $field_border_section = $field_section->addControlSection("field_border", __("Field Border"), "assets/icon.png", $this);
        $field_border_section->addStyleControls(
            array(
                array(
                    "name" => __('Border Width'),
                    "selector" => $field_text_selector,
                    "property" => 'border-width',
                    "control_type" => 'measurebox',
                    "unit" => 'px',
                ),
                array( 
                    "name" => __('Field Border Radius'),
                    "selector" => $field_text_selector,
                    "property" => 'border-radius',
                    "control_type" => "measurebox",
                    "unit" => "px"
                ),
                array(
                    "name" => __('Border Color'),
                    "selector" => $field_text_selector,
                    "property" => 'border-color',
                ),
                array(
                    "name" => __('Border Style'),
                    "selector" => $field_text_selector,
                    "property" => 'border-style',
                    "control_type" => 'buttons-list',
                    "value" => array('solid','dashed','dotted'),
                ),
            )
        );
                
        $field_typography_section = $field_section->addControlSection("field_typography", __("Typography"), "assets/icon.png", $this);
        
        $field_typography_section->addStyleControls(
            array(
                array(
                    "name" => __('Font Family'),
                    "selector" => $field_text_selector,
                    "property" => 'font-family',
                ),
                array(
                    "name" => __('Font Size'),
                    "selector" => $field_text_selector,
                    "control_type" => 'slider-measurebox',
                    "value" => '24',
                    "property" => 'font-size',
                ),
                array(
                    "name" => __('Font Weight'),
                    "selector" => $field_text_selector,
                    "control_type" => 'dropdown',
                    "property" => 'font-weight',
                ),
                array(
                    "name" => __('Text Transform'),
                    "selector" => $field_text_selector,
                    "property" => 'text-transform',
                ),
                /*array(
                    "name" => __('Font Style'),
                    "selector" => $field_text_selector,
                    "property" => 'font-style',
                ),*/
                array(
                    "name" => __('Text Decoration'),
                    "selector" => $field_text_selector,
                    "property" => 'text-decoration',
                ),
                array(
                    "name" => __('Line Height'),
                    "selector" => $field_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'line-height',
                    "unit" => 'px',
                ),
                array(
                    "name" => __('Letter Spacing'),
                    "selector" => $field_text_selector,
                    "control_type" => 'slider-measurebox',
                    "property" => 'letter-spacing',
                ),
            )
        );
        // End fields section
        
         // Submit button section
         $submit_button_section = $this->
         addControlSection("submit_button", __("Submit Button"), "assets/icon.png", $this);
     $submit_button_selector  = ".fea-submit-button";
     $submit_button_selector_hover  = ".fea-submit-button:hover";
     $submit_button_container  = ".acf-field-submit-button";

     //acff-submit-button acf-button button button-primary

     $submit_button_align = $submit_button_section->
         addControl("buttons-list", "submit_button_align", __("Button Align") );
     $submit_button_align->setValue( array("Left", "Center", "Right") );
     $submit_button_align->setValueCSS( array(
    //      "Left" => "
    //          $submit_button_container {
    //             display: flex, 
    //             justify-content: flex-start;
    //          }
    //      ",
    //      "Center" => "
    //          $submit_button_container {
    //             display: flex, 
    //             justify-content: center;
    //          }
    //      ",
    //      "Right" => "
    //          $submit_button_container {
    //             display: flex, 
    //             justify-content: flex-end;
    //          }
    //      ",
    //  ) );
     
        "Left" => "
        $submit_button_container {
            text-align: left;
        }
    ",
    "Center" => "
        $submit_button_container {
            text-align: center;
        }
    ",
    "Right" => "
        $submit_button_container {
            text-align: right;
        }
    ",
    ) );
     
     $submit_button_align->whiteList();

     // This defines which of the controls below will be shown to the user
     // Each control has a condition attibute which looks at the value of 
     // this control and updates accordingly
     $submit_button_hover_normal = $submit_button_section->addOptionControl(
         array(
             "type" => 'buttons-list',
             "name" => __('Button styles for hover or normal?','oxygen'),
             "slug" => 'button_style_hover_normal',
             "value" => array('Normal','Hover'),
             "default" => 'Normal',
         )
     );

     $submit_button_normal_style = $submit_button_section->
         addControlSection("submit_button_normal_style", __("Button Style"), "assets/icon.png", $this);

     $submit_button_normal_style->addStyleControls(
         array(
             // When "Normal" is selected in the button list above these 
             // settings will act on the normal style and the hover controls
             // wil be hidden. The opposite will happen when "Hover" is selected
             array(
                 "name" => __('Text Color - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'color',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Button Color - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'background-color',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Text Color - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'color',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Button Color - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'background-color',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
         )
     );

     // How do we add conditions here???
     // In the meantime I am adding both controls, both will always be visible,
     // but they are labeled in the front end so the user knows which class 
     // they are editing.
     $submit_button_normal_style->addPreset(
         "padding",
         "submit_button_padding_normal",
         __("Submit Button Padding Normal"),
         $submit_button_selector
         //'button_style_hover_normal=Normal',
     )->whiteList();

     $submit_button_normal_style->addPreset(
         "padding",
         "submit_button_padding_hover",
         __("Submit Button Padding Hover"),
         $submit_button_selector_hover
         //'button_style_hover_normal=Hover',
     )->whiteList();

     $submit_button_normal_style->addPreset(
         "margin",
         "submit_button_margin_normal",
         __("Submit Button Margin Normal"),
         $submit_button_selector
         //'button_style_hover_normal=Normal',
     )->whiteList();

     $submit_button_normal_style->addPreset(
         "margin",
         "submit_button_margin_hover",
         __("Submit Button Margin Hover"),
         $submit_button_selector_hover
         //'button_style_hover_normal=Normal',
     )->whiteList();

     $submit_button_border = $submit_button_section->
         addControlSection("submit_button_border", __("Button Border"), "assets/icon.png", $this);
     $submit_button_border->addStyleControls(
         array(
             // Add the normal controls (condition ... =Normal)
             array(    
                 "name" => __('Button Border Normal'),
                 "control_type" => 'heading',
                 "property" => '',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Border Width - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'border-width',
                 "control_type" => 'measurebox',
                 "unit" => 'px',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array( 
                 "name" => __('Border Radius - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'border-radius',
                 "control_type" => "measurebox",
                 "unit" => "px",
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __(' Border Color - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'border-color',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Border Style - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'border-style',
                 "control_type" => 'buttons-list',
                 "value" => array('solid','dashed','dotted'),
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             // Add the hover controls (condition ... =Hover)
             array(    
                 "name" => __('Button Border on Hover'),
                 "control_type" => 'heading',
                 "property" => '',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Border Width - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'border-width',
                 "control_type" => 'measurebox',
                 "unit" => 'px',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array( 
                 "name" => __('Border Radius - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'border-radius',
                 "control_type" => "measurebox",
                 "unit" => "px",
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Border Color - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'border-color',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Border Style - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'border-style',
                 "control_type" => 'buttons-list',
                 "value" => array('solid','dashed','dotted'),
                 "condition" => 'button_style_hover_normal=Hover',
             ),
         )
     );

     $submit_button_typography_section = $submit_button_section->
         addControlSection("submit_button_typography", __("Typography"), "assets/icon.png", $this);

     $submit_button_typography_section->addStyleControls(
         array(
             // Add the normal controls (condition ... =Normal)
             array(    
                 "name" => __('Typography Normal'),
                 "control_type" => 'heading',
                 "property" => '',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Font Family - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'font-family',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Font Size - Normal'),
                 "selector" => $submit_button_selector,
                 "control_type" => 'slider-measurebox',
                 "value" => '24',
                 "property" => 'font-size',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Font Weight - Normal'),
                 "selector" => $submit_button_selector,
                 "control_type" => 'dropdown',
                 "property" => 'font-weight',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Text Transform - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'text-transform',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Font Style - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'font-style',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Text Decoration - Normal'),
                 "selector" => $submit_button_selector,
                 "property" => 'text-decoration',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Line Height - Normal'),
                 "selector" => $submit_button_selector,
                 "control_type" => 'slider-measurebox',
                 "property" => 'line-height',
                 "unit" => 'px',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             array(
                 "name" => __('Letter Spacing - Normal'),
                 "selector" => $submit_button_selector,
                 "control_type" => 'slider-measurebox',
                 "property" => 'letter-spacing',
                 "condition" => 'button_style_hover_normal=Normal',
             ),
             // Add the hover controls (condition ... =Hover)
             array(    
                 "name" => __('Typography on Hover'),
                 "control_type" => 'heading',
                 "property" => '',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Font Family - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'font-family',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Font Size - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "control_type" => 'slider-measurebox',
                 "value" => '24',
                 "property" => 'font-size',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Font Weight - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "control_type" => 'dropdown',
                 "property" => 'font-weight',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Text Transform - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'text-transform',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Font Style - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'font-style',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Text Decoration - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "property" => 'text-decoration',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Line Height - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "control_type" => 'slider-measurebox',
                 "property" => 'line-height',
                 "unit" => 'px',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
             array(
                 "name" => __('Letter Spacing - Hover'),
                 "selector" => $submit_button_selector_hover,
                 "control_type" => 'slider-measurebox',
                 "property" => 'letter-spacing',
                 "condition" => 'button_style_hover_normal=Hover',
             ),
         )
     );
        
    } //End controls function

    function render($options, $defaults, $content) {
        
        if ( $options['form_id'] == 0 ){
			echo __( 'Please Select a Form', FEA_NS );
		}
		if ( get_post_type( $options['form_id'] ) == 'admin_form' ){
			fea_instance()->form_display->render_form( $options['form_id'] );
		}
      
    }

}

new Frontend_Form_Element();