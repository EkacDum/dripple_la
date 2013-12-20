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
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/var/zpanel/hostdata/beta1/public_html/drippost_com/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'beta1_home');

/** MySQL database username */
define('DB_USER', 'boss9i4kr0');

/** MySQL database password */
define('DB_PASSWORD', 'b87163FBn40Q7O8JwI');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
require( 'db-settings.php' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'x5f]$})ds~>-iiZ!iG*%aF94|Q4S7qgJi])4_g&$X:XZ1Bbxp1[T5H&(t#V$ZB:]');
define('SECURE_AUTH_KEY',  'B+~i|YG>#kHxOSF-KH.b8S:j+#y|~/g?q*9,IE4~s %B#fwUylg4!*``+-OK&^@8');
define('LOGGED_IN_KEY',    'X*->0%Yy1}QJ93>#QoBl2`GF+B{&]K($pvNug)w|fP.LX<n[5]E+~8/d-^Y*8Lwq');
define('NONCE_KEY',        '%$r/=$sHp8b@Y_MAm>~rD((^du9C-t8i7TpXA#kHVSe)*Na-5|wd/]pQqu$mo+?C');
define('AUTH_SALT',        '+mM+]7^kMamSHCQVTI@~S]`oWCLNTDUK YZtL|c&E$vj dj:Y:_#Nxp;I&yV17ZB');
define('SECURE_AUTH_SALT', '`~[~Qt!#^55W ;G7m)-J|0<0Zrg~1^5ZNY5&LHo0HDv2`-[u-oEgJjiPaV5dyIKI');
define('LOGGED_IN_SALT',   'LiabI+sMyU$Jw8ZwD#pqU*Gf&K3(eP,q[b|-]u&gP|EtT.ch4&5n:Kce0d)I;|uc');
define('NONCE_SALT',       'w_x|SP2v;*wS!+-UL89`l1}iZ]ahx_pin_*(^y*i}RbE|6qWbM$,b3Ul6j0MnE8M');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'dip_';

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
define('WP_DEBUG', true);
define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'www.drippost.com');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);


/* That's all, stop editing! Happy blogging. */

define('WP_DEFAULT_THEME', 'LinkedPOST');
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
