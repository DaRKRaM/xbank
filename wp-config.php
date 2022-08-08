<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'alljersql' );

/** Database username */
define( 'DB_USER', 'alljeruser' );

/** Database password */
define( 'DB_PASSWORD', 'Ugoa43_0' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'rqv]@]J=Y&?[KFU7n3^]u|YryPs7il2!*fjvyO*~fb44`;(D{V.<N&tFrUzb=iCd' );
define( 'SECURE_AUTH_KEY',  'MO{QRJ`-cLxAZ@=tF2X9&i-h29!ji>#)#(doj0*N%XIqg:4E;&L** %qEH4$_!h}' );
define( 'LOGGED_IN_KEY',    'Zv~;[QUSW )P15{; !eo?k$WGB!!]Zjf-ZP35]hfCamD-LkIV[LXM8lz0%A{pg3~' );
define( 'NONCE_KEY',        '?oUFa?SBNVxE*Yal[&5y!HM5Po<f]_vdXCs8Bw5Tx.;BqKc#K:3)LAR*hexhb`b|' );
define( 'AUTH_SALT',        '4{4@-t]qap;:!aDDxk12QzXOI`R@^nR=%c6Z$JLGXQdEY.PHAW^fw9Z/sb4|6245' );
define( 'SECURE_AUTH_SALT', '-1>c54do<F:qXN~ZRuM/yU:PW+ QP)$wbXD=c{G8x0QG|:Rv(IJ-cL$D) >pL(H<' );
define( 'LOGGED_IN_SALT',   ']K1w|kJBQCI>gS$@xD2}HM/_=R4&vmhg/@d-SjUacoc&+kA-9mWX3?H_;S7cO*~i' );
define( 'NONCE_SALT',       'Vih[q)rM 7[J1G%S:7%:3_6l)j<vL1:rwE>>}{r5On-u&<iWlM7_yAI>7+.)3M+g' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
