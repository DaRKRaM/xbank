<?php

namespace Frontend_WP;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if( ! class_exists( 'Frontend_Admin_Oxygen' ) ) :
    class Frontend_Admin_Oxygen{
        public function global_settings_tab() {
  
            global $oxygen_toolbar;
            $oxygen_toolbar->settings_tab( __( FEA_TITLE, FEA_NS ), FEA_PREFIX, "panelsection-icons/styles.svg");
        }
        public function register_add_plus_section() {
            global $oxygen_toolbar;
            $oxygen_toolbar::oxygen_add_plus_accordion_section(FEA_PREFIX,__(FEA_TITLE, FEA_NS));
        }

        function register_add_plus_subsections() { ?>
        
            <?php do_action('oxygen_add_plus_'.FEA_PREFIX); ?>
        
        <?php }

        public function add_oxygen_elements() {		
            require_once( __DIR__ . "/elements/general/frontend-form.php" );
        }


        public function __construct() {		
            //add_action('oxygen_vsb_global_styles_tabs', array($this, 'global_settings_tab'));

            add_action('oxygen_add_plus_acf_frontend_section_content', array($this, 'register_add_plus_subsections'));

            add_action('oxygen_add_plus_sections', array($this, 'register_add_plus_section'));
            
            require_once( __DIR__ . "/elements/general/frontend-form.php" );
        }
    }

fea_instance()->oxygen = new Frontend_Admin_Oxygen();

endif;	