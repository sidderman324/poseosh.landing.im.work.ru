<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_poseosh');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         'v<87uzE[j3N&Z-aN`GM&L<n0k?/r4;<O;0Mt-$WVY[$74!|{0XZArY-U(%8`Jc16');
define('SECURE_AUTH_KEY',  'D6!uDbt[@yeI[)9^2PSB<#YdB016ED5?<$Y1cl?{]vn+B-?Eu82.W|DzeG`cx.%.');
define('LOGGED_IN_KEY',    'woza&s5E*]EMNg-_87B.?1i(xllAX[N;Ni7bLqc9g:eJ2a=ZGY7P;m7PfM+g1]qu');
define('NONCE_KEY',        'cD&/DPS9&x0r}n,LoJDq?o0FJ`1O0)7YH>HR#GUPpH/yC9=vQ0d+eb(21Hd(L2=N');
define('AUTH_SALT',        '2hQ03$y8P)0;`;5!G%Rh0wq{09~t*&LN;4e*1L0!UU7t?Q0xYEpajg@>?[2C)TN@');
define('SECURE_AUTH_SALT', '?N/~gWE]6Hxc,RbZI<d?&H)Yxz@q%IXf?;z.+.F0h!/U6M2)#[%I7(!m6E<S0`Fs');
define('LOGGED_IN_SALT',   '}WYv)p:F9/5DT7fdB]hr<4f^DJwW),3t&GfJ!t]jlZ%T4<dJJ1sqcWJmig5iJ0Pp');
define('NONCE_SALT',       '=o`>BMT!NDhgu4v&PuJ`5m^.0v0engLKjR-|`~#T7ke83R{uOKZm07xP(%L(LW@L');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'qih';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);



/* That's all, stop editing! Happy blogging. */
define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'poseosh.landing.im.work.ru');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
