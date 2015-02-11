<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'elapcloud');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'L`}`OSff>}s+:HAW;tA=^#meriBZ4$vcjx*~Org+/<+:/&L8dygm 1b#p8b!8xg ');
define('SECURE_AUTH_KEY',  '5BV_H6YP**{7^DwZADb8bn/vv=_=l)I3zIBV/RZ[gR8TMFDMcKsy~j94nbU?BOc`');
define('LOGGED_IN_KEY',    '!$Cq97<v`;nG`xk [&~wCDL..+ z`4VKjZX4rYrw:Oazl]#BcWEHB+?5}t-11%#n');
define('NONCE_KEY',        'kv~Dssi<5j-.RCr7C,R<K[&}{3Hwmx-x_one5zgKX.~.WR^%byC_&/|q2&Cz,HUU');
define('AUTH_SALT',        't6:J_3A)0d%I)w|UM40K 9M9q-oJ,>+>bo{*(6y_lMQOy_&~l>)[ff=)f~:|^?+:');
define('SECURE_AUTH_SALT', '%@RCHf?(o8.1d>:3BhFX$k+8U3oI|M=#TX4N&e|DfTKRbK[y~bZ+P4*>_ndv|iN1');
define('LOGGED_IN_SALT',   'EoH~GL,$u][f4m%si<p]DN,@FI%=N_AC(6!r$4PIC-+6 88kEW%#/}Z]xmhSoyU;');
define('NONCE_SALT',       'Xai=|OEohE p,/hG)|0?R>[7K(37;_}jZX(z0v&tPY]x]h`YY&CO2`w~98yY!lz/');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/*
 *Force SSl
 *
*/
define('FORCE_SSL_ADMIN', false); 

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
