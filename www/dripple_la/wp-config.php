<?php
	
// require_once(ABSPATH . '../../config.php');<?php
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
define('DB_NAME', 'beta1_home');

/** MySQL database username */
define('DB_USER', 'betamexicocity');

/** MySQL database password */
define('DB_PASSWORD', 'xIV37X3GznZtmXVYZ2');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


require_once('db-settings.php');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Xtk/oWaL/&v`04wRwzCJ}$-?z#`2oiD+1*o}5-F #6QMOTt/-~{ns]~FEVBj`;|=');
define('SECURE_AUTH_KEY',  '[a6:-IJc2h;)Wr=K]manI%,7~S9z(F|=vp#X/$]3?F9r943vV475f5|;Jh0ylpy;');
define('LOGGED_IN_KEY',    '9e|NPsdV(dd9jg|Xgmg*o#x]M[&z1<rQ=+B|WXB=5Y)a%-a?rr`$Q[%Sy!vw(wIf');
define('NONCE_KEY',        'wQP?Oq[+T7.wD%e8)R2A1{%9>1B|z_}p,c2^#a`Qf3~+HV.Sh%OhZSL-s2G2Jdyc');
define('AUTH_SALT',        'p9}E|g7:|FQ:)410T;wS9:Jl|b^9RC6d vMf@;@nF&LL6M|Yhsf~?-7Or-&Kv.b3');
define('SECURE_AUTH_SALT', '=g.K+uQ^/}bl=>[A|1^*M>Q6fcSBRu:!q5lO7kM_][VKK~JwiK5- H<upCf!};GY');
define('LOGGED_IN_SALT',   '22a?B|tl!cZ3HK#|8]bowMfq^vj93~qWZ#aso%jYBW?0LB1I)m00>4|P`r+:]]wB');
define('NONCE_SALT',       '}6w0(I>+L`VMN0NUl|K$6:~vbQ,BxX%Ok5~%0-+|.tM_3yCO$CLjEr{q#I/<m[k5');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'drip_';

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

define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'dripple.la');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/* That's all, stop editing! Happy blogging. */

// @ini_set('display_errors', 1);

/*VERY IMPORTANT FOR LINKEDPOST OR DRIPPLE*/
// define('WP_POST_REVISIONS', false);
// define('WP_DEFAULT_THEME', 'linkedpost');

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');