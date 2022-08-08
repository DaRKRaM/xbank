<?php

if ( ! defined( 'ABSPATH' ) ) {
  // Exit if accessed directly.
  exit;
}

/**
* Handling all the AJAX calls in LoginPress.
*
* @since 1.5.8
* @class LoginPress_Static_Addons
*/

if ( ! class_exists( 'LoginPress_Static_Addons' ) ) :

	class LoginPress_Static_Addons {

		/**
		 * The constructor function
		 *
		 * @version 1.5.8
		 */
		function __construct() {
			include_once( LOGINPRESS_DIR_PATH . 'classes/class-loginpress-addons.php' );
		}

		/**
		 * Check addons status
		 *
		 * @version 1.5.8
		 */
		public static function loginpress_check_addon_status( $version, $slug ) {
			$slug_id      = $slug;
			$slug         = $slug.'/'.$slug.'.php';
			$plugins_list = get_plugins();
			if ( is_plugin_active( $slug ) ) { ?>

				<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'uninstall_' . $slug ); ?>">
				<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
				<input id="<?php echo $slug ?>" type="checkbox" checked class="loginpress-radio loginpress-radio-ios loginpress-uninstall-pro-addon" value="<?php echo $slug ?>">
				<label for="<?php echo $slug ?>" class="loginpress-radio-btn"></label>

				<?php
			} elseif ( array_key_exists( $slug , $plugins_list ) ) { ?>

				<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
				<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
				<input id="<?php echo $slug ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-active-pro-addon" value="<?php echo $slug ?>">
				<label for="<?php echo $slug ?>" class="loginpress-radio-btn"></label>
				<?php
			} else {
				if ( 'free' === $version &&  ! array_key_exists( $slug , $plugins_list ) ) {
					$action = 'install-plugin';
					if ( empty( get_option( 'loginpress_pro_license_key' ) ) ) {
						$slug = 'login-logout-menu';
					}
					$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' .$slug );
					?>
					<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
					<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
					<input name="loginpress_pro_addon_id" type="hidden" value="3536">
					<input id="<?php echo $slug_id; ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-install-pro-addon" value="<?php echo $slug_id; ?>">
					<label for="<?php echo $slug_id; ?>" class="loginpress-radio-btn"></label>
					<?php
				} else { ?>
					<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
					<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
					<input id="<?php echo $slug_id; ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-active-pro-addon" value="<?php echo $slug_id; ?>">
					<label for="<?php echo $slug_id; ?>" class="loginpress-radio-btn"></label>
					<?php
				}
			}
		}

		/**
		 * The Static addons cards for pro
		 *
		 * @version 1.5.8
		 */
		public static function pro_static_addon_cards() { 
			$obj_loginpress_addons = new LoginPress_Addons();
			?>
			<div class="addon_cards_wraper"> 
				<div class="loginpress-extension loginpress-free-add-ons">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/login_logout_menu_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Login Logout Menu</span></h3>
					</a>
					<p>Login Logout Menu is a handy plugin which allows you to add login, logout, register and profile menu items in your selected menu.</p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'free', 'login-logout-menu' );
					echo $obj_loginpress_addons->_ajax_responce('Login Logout Menu', 'login-logout-menu' ); ?>
				</div>

			
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/login_redirects_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Login Redirects</span></h3>
					</a>
					<p>Redirects users based on their roles. This is helpful, If you have an editor and want to redirect him to his editor stats page. Restrict your subscribers, guests or even customers to certain pages instead of wp-admin. This add-on has a cool UX/UI to manage all the login redirects you have created on your site.</p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'pro', 'loginpress-login-redirects');
					echo $obj_loginpress_addons->_ajax_responce('Login Redirects', 'loginpress-login-redirects' ); ?>
				</div>

			
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/social_login_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Social Login</span></h3>
					</a>
					<p>Social login from LoginPress is an add-on which provides facility your users to log in and Register via Facebook, Google, and Twitter. This add-on will eliminate the Spam and Bot registrations. This add-on will help your users to hassle-free registrations/logins on your site.</p>
					<p>
						<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'pro', 'loginpress-social-login');
					echo $obj_loginpress_addons->_ajax_responce('Social Login', 'loginpress-social-login' ); ?>
				</div>

			
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/login_widget_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Login Widget</span></h3>
					</a>
					<p>This LoginPress add-on is a widget you can use into your blog sidebar. It uses an Ajax way to login via the sidebar. You may need to know HTML/CSS to give it style according to your site even we have styled it in general.</p>
					<p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'pro', 'loginpress-login-widget');
					echo $obj_loginpress_addons->_ajax_responce('Login Widget', 'loginpress-login-widget' ); ?>
				</div>

			
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/limit_login_attempts_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Limit Login Attempts</span></h3>
					</a>
					<p>Everybody needs a control of their Login page. This will help you to track your login attempts by each user. You can limit the login attempts for each user. Brute force attacks are the most common way to gain access to your website. This add-on acts as a sheild to these hacking attacks and gives you control to set the time between each login attempts.</p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'pro', 'loginpress-limit-login-attempts');
					echo $obj_loginpress_addons->_ajax_responce('Limit Login Attempts', 'loginpress-limit-login-attempts' ); ?>
				</div>

			
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/auto_login_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Auto Login</span></h3>
					</a>
					<p>This LoginPress add-on lets you (Administrator) generates a unique URL for your certain users who you don't want to provide a password to login to your site. This Pro add-on gives you a list of all the users who you have given auto-generated login links. You can disable someone's access and delete certain users.</p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'pro', 'loginpress-auto-login');
					echo $obj_loginpress_addons->_ajax_responce('Auto Login', 'loginpress-auto-login' ); ?>
				</div>

			
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">
					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/hide_rename_login_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Hide Login</span></h3>
					</a>
					<p>This LoginPress add-on lets you change the login page URL to anything you want. It will give a hard time to spammers who keep hitting to your login page. This is helpful for Brute force attacks. One caution to use this add-on is you need to remember the custom login url after you change it. We have an option to email your custom login url so you remember it.</p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'pro', 'loginpress-hide-login');
					echo $obj_loginpress_addons->_ajax_responce('Limit Login Attempts', 'loginpress-hide-login' ); ?>
				</div>

			</div>
		<?php 	}

		/** The Static addons cards for pro
		*
		* @version 1.5.8
		*/
		public static function free_static_addon_cards() { 
			$obj_loginpress_addon = new LoginPress_Addons();
			?>

			<div class="addon_cards_wraper"> 
				<div class="loginpress-extension loginpress-free-add-ons">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/login_logout_menu_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Login Logout Menu</span></h3>
					</a>

					<p>Login Logout Menu is a handy plugin which allows you to add login, logout, register and profile menu items in your selected menu.</p>
					<?php LoginPress_Static_Addons::loginpress_check_addon_status( 'free', 'login-logout-menu' );
					echo $obj_loginpress_addon->_ajax_responce('Login Logout Menu', 'login-logout-menu' ); ?>
				
				</div>
				
				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/login_redirects_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Login Redirects</span></h3>
					</a>

					<p>Redirects users based on their roles. This is helpful, If you have an editor and want to redirect him to his editor stats page. Restrict your subscribers, guests or even customers to certain pages instead of wp-admin. This add-on has a cool UX/UI to manage all the login redirects you have created on your site.</p>
					<p>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					</p>
				</div>

				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/social_login_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Social Login</span></h3>
					</a>

					<p>Social login from LoginPress is an add-on which provides facility your users to log in and Register via Facebook, Google, and Twitter. This add-on will eliminate the Spam and Bot registrations. This add-on will help your users to hassle-free registrations/logins on your site.</p>
					<p>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					</p>
				</div>

				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/login_widget_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Login Widget</span></h3>
					</a>

					<p>This LoginPress add-on is a widget you can use into your blog sidebar. It uses an Ajax way to login via the sidebar. You may need to know HTML/CSS to give it style according to your site even we have styled it in general.</p>
					<p>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					</p>
					
				</div>

				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/limit_login_attempts_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Limit Login Attempts</span></h3>
					</a>

					<p>Everybody needs a control of their Login page. This will help you to track your login attempts by each user. You can limit the login attempts for each user. Brute force attacks are the most common way to gain access to your website. This add-on acts as a sheild to these hacking attacks and gives you control to set the time between each login attempts.</p>
					<p>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					</p>
				</div>

				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/auto_login_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Auto Login</span></h3>
					</a>

					<p>This LoginPress add-on lets you (Administrator) generates a unique URL for your certain users who you don't want to provide a password to login to your site. This Pro add-on gives you a list of all the users who you have given auto-generated login links. You can disable someone's access and delete certain users.</p>
					<p>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					</p>
				</div>

				<div class="loginpress-extension">
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="loginpress_addons_links">

					<h3><img src="https://wpbrigade.com/wp-content/uploads/edd/2018/01/hide_rename_login_300_x_300-150x150.png" class="loginpress_addons_thumbnails"><span>Hide Login</span></h3>
					</a>

					<p>This LoginPress add-on lets you change the login page URL to anything you want. It will give a hard time to spammers who keep hitting to your login page. This is helpful for Brute force attacks. One caution to use this add-on is you need to remember the custom login url after you change it. We have an option to email your custom login url so you remember it.</p>
					<p>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=addons-coming-soon&amp;utm_campaign=pro-upgrade" class="button-primary">UPGRADE NOW</a>
					</p>
				</div>
			</div>

		<?php }
	}
endif;

// new LoginPress_Static_Addons;