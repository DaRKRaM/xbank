<?php

/**
 * Plugin Name: Frontend Admin (for ACF)
 * Plugin URI: https://wordpress.org/plugins/acf-frontend-form-element/
 * Description: This awesome plugin allows you to easily display admin forms to the frontend of your site so your clients can easily edit content on their own from the frontend.
 * Version:     3.7.11
 * Author:      Shabti Kaplan
 * Author URI:  https://kaplanwebdev.com/
 * Text Domain: acf-frontend-form-element
 * Domain Path: /languages/
 *
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'fea_instance' ) ) {
    fea_instance()->set_basename( false, __FILE__ );
} else {
    define( 'FEA_VERSION', '3.7.11' );
    define( 'FEA_PATH', __FILE__ );
    define( 'FEA_NAME', plugin_basename( __FILE__ ) );
    define( 'FEA_URL', plugin_dir_url( __FILE__ ) );
    define( 'FEA_TITLE', 'Frontend Admin for ACF' );
    define( 'FEA_NS', 'acf-frontend-form-element' );
    define( 'FEA_PRO', 'https://frontendform.com/acff-pro/' );
    define( 'FEA_PREFIX', 'acf_frontend' );
    define( 'ALT_PREFIX', 'frontend_admin' );
    define( 'FEA_PRE', 'acf-frontend' );
    // Create a helper function for easy SDK access.
    function fea_instance()
    {
        global  $fea_instance ;
        
        if ( !isset( $fea_instance ) ) {
            if ( !defined( 'WP_FS__PRODUCT_5212_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_5212_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
            $fea_instance = fs_dynamic_init( array(
                'id'              => '5212',
                'slug'            => FEA_NS,
                'premium_slug'    => FEA_NS . '-pro',
                'type'            => 'plugin',
                'public_key'      => 'pk_771aff8259bcf0305b376eceb7637',
                'is_premium'      => false,
                'premium_suffix'  => '',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'trial'           => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'has_affiliation' => 'selected',
                'menu'            => array(
                'slug'        => 'acf-frontend-settings',
                'contact'     => false,
                'support'     => false,
                'affiliation' => false,
            ),
                'is_live'         => true,
            ) );
            do_action( 'front_end_admin_loaded' );
        }
        
        return $fea_instance;
    }
    
    // Init Freemius.
    fea_instance();
    
    if ( !class_exists( 'Front_End_Admin' ) ) {
        /**
         * Main Frontend Admin Class
         *
         * The main class that initiates and runs the plugin.
         *
         * @since 1.0.0
         */
        final class Front_End_Admin
        {
            /**
             * Minimum PHP Version
             *
             * @since 1.0.0
             *
             * @var string Minimum PHP version required to run the plugin.
             */
            const  MINIMUM_PHP_VERSION = '5.2.4' ;
            /**
             * Instance
             *
             * @since 1.0.0
             *
             * @access private
             * @static
             *
             * @var Front_End_Admin The single instance of the class.
             */
            private static  $_instance = null ;
            /**
             * Instance
             *
             * Ensures only one instance of the class is loaded or can be loaded.
             *
             * @since 1.0.0
             *
             * @access public
             * @static
             *
             * @return Front_End_Admin An instance of the class.
             */
            public static function instance()
            {
                if ( is_null( self::$_instance ) ) {
                    self::$_instance = new self();
                }
                return self::$_instance;
            }
            
            /**
             * Constructor
             *
             * @since 1.0.0
             *
             * @access public
             */
            public function __construct()
            {
                add_action( 'init', [ $this, 'i18n' ] );
                add_action( 'after_setup_theme', [ $this, 'init' ] );
                fea_instance()->add_filter(
                    'connect_message',
                    array( $this, 'custom_connect_message' ),
                    10,
                    6
                );
            }
            
            public function custom_connect_message(
                $message,
                $user_first_name,
                $plugin_title,
                $user_login,
                $site_link,
                $freemius_link
            )
            {
                return sprintf(
                    __( 'Hey %1$s' ) . ',<br>' . __( 'Welcome to %2$s! Opt in to our email list here to start receiving our essential onboarding email series, walking you through the best ways to use and benefit from our plugin. You’ll also be opting into feature update notifications and non-sensitive diagnostic tracking from our partner over at %3$s.', FEA_NS ),
                    $user_first_name,
                    '<b>' . $plugin_title . '</b>',
                    $freemius_link
                );
            }
            
            /**
             * Load Textdomain
             *
             * Load plugin localization files.
             *
             * Fired by `init` action hook.
             *
             * @since 1.0.0
             *
             * @access public
             */
            public function i18n()
            {
                load_plugin_textdomain( FEA_NS, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            }
            
            /**
             * Initialize the plugin
             *
             * Load the plugin only after ACF is loaded.
             * Checks for basic plugin requirements, if one check fail don't continue,
             * If all checks have passed load the files required to run the plugin.
             *
             * Fired by `plugins_loaded` action hook.
             *
             * @since 1.0.0
             *
             * @access public
             */
            public function init()
            {
                
                if ( !class_exists( 'ACF' ) ) {
                    add_action( 'admin_notices', [ $this, 'admin_notice_missing_acf_plugin' ] );
                    return;
                }
                
                // Check for required PHP version
                
                if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
                    add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
                    return;
                }
                
                add_action( 'admin_notices', [ $this, 'admin_notice_get_pro' ] );
                add_action( 'wp_ajax_acff-upgrade-pro-dismiss', array( $this, 'ajax_upgrade_pro_dismiss' ) );
                add_action( 'admin_notices', [ $this, 'admin_notice_review_plugin' ] );
                add_action( 'wp_ajax_acff-rate-plugin', array( $this, 'ajax_rate_the_plugin' ) );
                add_filter(
                    'plugin_row_meta',
                    [ $this, 'frontend_admin_row_meta' ],
                    10,
                    2
                );
                $this->plugin_includes();
            }
            
            public function plugin_includes()
            {
                if ( did_action( 'elementor/loaded' ) ) {
                    require_once __DIR__ . '/main/elementor/module.php';
                }
                if ( class_exists( 'OxygenElement' ) ) {
                    require_once __DIR__ . '/main/oxygen/module.php';
                }
                require_once __DIR__ . '/main/gutenberg/module.php';
                require_once __DIR__ . '/main/frontend/module.php';
                require_once __DIR__ . '/main/admin/module.php';
            }
            
            /**
             * Admin notice
             *
             * Warning when the site doesn't have ACF installed or activated.
             *
             * @since 1.0.0
             *
             * @access public
             */
            public function admin_notice_missing_acf_plugin()
            {
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
                $message = sprintf(
                    /* translators: 1: Plugin name 2: Advanced Custom Fields */
                    esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', FEA_NS ),
                    '<strong>' . esc_html__( FEA_TITLE, FEA_NS ) . '</strong>',
                    '<strong>' . esc_html__( 'Advanced Custom Fields', FEA_NS ) . '</strong>'
                );
                printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
            }
            
            public function admin_notice_get_pro()
            {
                if ( !is_admin() ) {
                    return;
                }
                if ( get_option( 'acff_pro_trial_dismiss' ) ) {
                    return;
                }
                if ( get_option( 'frontend_admin_submissions_all_time', 0 ) < 150 ) {
                    return;
                }
                $image = '<img width="130px" src="https://www.frontendform.com/wp-content/uploads/2020/05/acf_logo-opt.png" style="width:130px;margin:10px"/>';
                ?>

                <div class="notice notice-info acff-upgrade-pro-action" style="padding-right: 38px; position: relative;">
				  <p> <?php 
                printf( __( 'Try %s <b>Pro</b> free for 7 days!', FEA_NS ), FEA_TITLE );
                ?> <a href="https://frontendform.com/acff-pro/" target="_blank">Check it out!</a> <a class="button button-primary" style="margin-left:20px;" href="https://frontendform.com/acff-pro/" target="_blank"><?php 
                echo  __( 'Free trial!', FEA_NS ) ;
                ?></a></p>
                    <div><?php 
                echo  $image ;
                ?></div>
				<a href="#" style="position:absolute;top:5px;right:5px;" type="button" data-nonce="<?php 
                echo  wp_create_nonce( 'acff_dismiss_pro_nonce' ) ;
                ?>" class="acff-dismiss-notice"><?php 
                echo  __( 'Dismiss notice', FEA_NS ) ;
                ?></a>
				</div>
                <?php 
                $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min' );
                wp_enqueue_script(
                    'fea-try-pro-plugin',
                    FEA_URL . 'assets/js/try-pro' . $min . '.js',
                    array( 'acf' ),
                    FEA_VERSION,
                    true
                );
                wp_localize_script( 'fea-try-pro-plugin', 'fa', array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                ) );
            }
            
            public function ajax_upgrade_pro_dismiss()
            {
                check_admin_referer( 'acff_dismiss_pro_nonce', '_n' );
                update_option( 'acff_pro_trial_dismiss', true );
                echo  1 ;
                exit;
            }
            
            public function admin_notice_review_plugin()
            {
                if ( !is_admin() ) {
                    return;
                }
                $min_submits = get_option( 'acff_min_submits_trigger', 10 );
                $submits_count = get_option( 'frontend_admin_submissions_all_time', 0 );
                if ( $min_submits == -1 || $submits_count < $min_submits ) {
                    return;
                }
                $img_path = FEA_URL . 'assets/plugin-logo.png';
                $image = '<img width="130px" src="https://www.frontendform.com/wp-content/uploads/2020/05/acf_logo-opt.png" style="width:130px;margin-top:10px"/>';
                $review_url = 'https://wordpress.org/support/view/plugin-reviews/' . FEA_NS . '?rate=5#postform';
                ?>
                <div class="notice notice-info acff-rate-action" style="padding-right: 48px">
                <?php 
                printf( __( "Hey, I noticed you've received over %d submissions on %s already - that's awesome! I am so glad you are enjoying my plugin! Please take a minute to help our business grow by leaving a review.", FEA_NS ), $min_submits, FEA_TITLE );
                ?>
                <strong><em>~ Shabti Kaplan</em></strong>
                <ul data-nonce="<?php 
                echo  wp_create_nonce( 'acff_rate_action_nonce' ) ;
                ?>">
                    <li><a data-rate-action="do-rate" href="#" data-href="<?php 
                echo  $review_url ;
                ?>"><?php 
                echo  _e( 'Ok, you deserve it', FEA_NS ) ;
                ?></a>
                    </li>
                    <li><a data-rate-action="done-rating" href="#"><?php 
                _e( 'I already did', FEA_NS );
                ?></a></li>
                    <li><a data-rate-action="not-yet" href="#"><?php 
                _e( 'Nope, maybe later', FEA_NS );
                ?></a></li>
                </ul>
                <div><?php 
                echo  $image ;
                ?></div>
				</div>
                <?php 
                $min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min' );
                wp_enqueue_script(
                    'fea-rate-plugin',
                    FEA_URL . 'assets/js/rate-plugin' . $min . '.js',
                    array( 'acf' ),
                    FEA_VERSION,
                    true
                );
                wp_localize_script( 'fea-rate-plugin', 'fa', array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                ) );
            }
            
            public function ajax_rate_the_plugin()
            {
                // Continue only if the nonce is correct
                check_admin_referer( 'acff_rate_action_nonce', '_n' );
                $min_submits = get_option( 'acff_min_submits_trigger', 10 );
                if ( -1 === $min_submits ) {
                    exit;
                }
                $rate_action = $_POST['rate_action'];
                
                if ( 'do-rate' === $rate_action ) {
                    $min_submits = -1;
                } else {
                    
                    if ( $min_submits === 10 ) {
                        $min_submits = 100;
                    } else {
                        
                        if ( $min_submits === 100 ) {
                            $min_submits = 1000;
                        } else {
                            $min_submits = -1;
                        }
                    
                    }
                
                }
                
                update_option( 'acff_min_submits_trigger', $min_submits );
                echo  1 ;
                exit;
            }
            
            /**
             * Admin notice
             *
             * Warning when the site doesn't have a minimum required PHP version.
             *
             * @since 1.0.0
             *
             * @access public
             */
            public function admin_notice_minimum_php_version()
            {
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
                $message = sprintf(
                    /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                    esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', FEA_NS ),
                    '<strong>' . esc_html__( FEA_TITLE, FEA_NS ) . '</strong>',
                    '<strong>' . esc_html__( 'PHP', FEA_NS ) . '</strong>',
                    self::MINIMUM_PHP_VERSION
                );
                printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
            }
            
            public function frontend_admin_row_meta( $links, $file )
            {
                
                if ( FEA_NAME == $file ) {
                    $row_meta = array(
                        'video' => '<a href="' . esc_url( 'https://www.youtube.com/channel/UC8ykyD--K6pJmGmFcYsaD-w/playlists' ) . '" target="_blank" aria-label="' . esc_attr__( 'Video Tutorials', FEA_NS ) . '" >' . esc_html__( 'Video Tutorials', FEA_NS ) . '</a>',
                    );
                    return array_merge( $links, $row_meta );
                }
                
                return (array) $links;
            }
        
        }
        Front_End_Admin::instance();
    }

}
