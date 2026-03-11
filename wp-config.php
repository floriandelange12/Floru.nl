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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'iKYtqB0-T7w M,AfGYaofn2+><.C1&AupL5 4CRTbeKU564Y(InJ4,b8EB>hOtdv' );
define( 'SECURE_AUTH_KEY',   '7/X+<0keM0FSih/4&6A,qFTv(x=TL5qI-X42G^cu+mg&:+Hj;TWW3eXx5{Hwff2I' );
define( 'LOGGED_IN_KEY',     'P6T;.B%OiB2T/ @O+wJT4AI6BbohP>IP6a@/c.ID]{{A[=f[PzW}Q}^e_&3K9PGa' );
define( 'NONCE_KEY',         'CVV2.A#IMMHSts=|22[}hbj]mR!};HQxOaZB7`?Tg@rx 6a^RPPB]S<hvC3U[%uO' );
define( 'AUTH_SALT',         'z]U@%4}z#G~oO#iM7xg%.N|.UnJYL-%/,r)(8-=df)bzABlL|x_6bps>z;Y$J]%,' );
define( 'SECURE_AUTH_SALT',  '%Wm{N0U[|:`8j 7E1--)OPs0#(Y)86D(Zs4rBrQx8$&H{h[t)#ZA9sL-CzZxS<FQ' );
define( 'LOGGED_IN_SALT',    'k@Y7`5E7_}eWE%b9l2#LUm[w^WAsb8) .!|[8+;9P$cPS@i+K&MUZ;d=75o3)-3H' );
define( 'NONCE_SALT',        'gY+X=aV3*GO?dq@-mNM*#(xH5Z :2wGF`AJ_p!RzV[#_!`HFhco=V Q5C_@b$3|_' );
define( 'WP_CACHE_KEY_SALT', 'GsZYUs[QYleLN:PF%A!8LUt;WHlFfS(@9 mh]l|.t{zpa{.}J*+xOQc>=Cl=[3rP' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
