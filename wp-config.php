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
$db_name = getenv('MYSQL_DB_NAME');  
$db_user = getenv('MYSQL_USERNAME');
$password = getenv('MYSQL_PASSWORD'); 
$db_host= getenv('MYSQL_DB_HOST');

if ($_SERVER["HTTP_HOST"] === 'toolbox') {
	// LOCAL
	define("ENVIRONMENT", "local");
}else if ($_SERVER["HTTP_HOST"] === 'staging') { 
	// STAGING
	// define("ENVIRONMENT", "staging");
} else if ($_SERVER["HTTP_HOST"] === 'toolbox.phpfogapp.com' || $_SERVER["HTTP_HOST"] === 'thetoolbox.cc' || $_SERVER["HTTP_HOST"] === 'www.thetoolbox.cc'){
	// PRODUCTION
	define("ENVIRONMENT", "production");
}

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', $db_name);

/** MySQL database username */
define('DB_USER', $db_user);

/** MySQL database password */
define('DB_PASSWORD', $password);

/** MySQL hostname */
define('DB_HOST', $db_host);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** --- PHP Fog --- Set WordPress to cache requests. For use with Varnish. */
define('WP_CACHE', true); //Added by WP-Cache Manager

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '0.896039211573522');
define('SECURE_AUTH_KEY',  '0.896039211573522');
define('LOGGED_IN_KEY',    '0.896039211573522');
define('NONCE_KEY',        '0.896039211573522');
define('AUTH_SALT',        '0.896039211573522');
define('SECURE_AUTH_SALT', '0.896039211573522');
define('LOGGED_IN_SALT',   '0.896039211573522');
define('NONCE_SALT',       '0.896039211573522');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/* PHPFOG edit to patch a few issues of file saves, plugins, etc. */
define('FS_METHOD', 'direct');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
