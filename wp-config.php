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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'w5i643857886439' );

/** MySQL database username */
define( 'DB_USER', 'w5i643857886439' );

/** MySQL database password */
define( 'DB_PASSWORD', 'gAW1Y(*JMJ)T' );

/** MySQL hostname */
define( 'DB_HOST', 'w5i643857886439.db.43857886.c76.hostedresource.net:3307' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'LT_j65$BV7zg2* 82!Uh' );
define( 'SECURE_AUTH_KEY',  'Y&@9Kf1C43qT!N(E7*2!' );
define( 'LOGGED_IN_KEY',    'c6-#(zQ5Kpmxd&mPwPN)' );
define( 'NONCE_KEY',        'a#GW@MNpa_5Qwr_&JJ6A' );
define( 'AUTH_SALT',        'H_rVNM_(m07cKVGSzLzG' );
define( 'SECURE_AUTH_SALT', 'dY zPN1qAyqbn$CInh#C' );
define( 'LOGGED_IN_SALT',   'AYtv=F1%ZR9ndhYZ3J9a' );
define( 'NONCE_SALT',       'jPY_hsB-TkO9X6QIt12C' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_1y1mz0gh9a_';

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
//define( 'WP_CACHE', true );
require_once( dirname( __FILE__ ) . '/gd-config.php' );
define( 'FS_METHOD', 'direct' );
define( 'FS_CHMOD_DIR', (0705 & ~ umask()) );
define( 'FS_CHMOD_FILE', (0604 & ~ umask()) );


define( 'FORCE_SSL_ADMIN', true );
/* That's all, stop editing! Happy publishing. */

/**
 * 開發人員用： WordPress 偵錯模式。
 */
/** 開啟WordPress偵錯功能 */
define('WP_DEBUG', true);
/** 產生錯誤記錄檔，產生於wp-content/debug.log */
define('WP_DEBUG_LOG', true );
/** 顯示錯誤訊息於html上 */
define('WP_DEBUG_DISPLAY', false );
/** 腳本偵錯功能，如設為true，則載入非minified的js */
define('SCRIPT_DEBUG', false );


/** WordPress 目錄的絕對路徑。 */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** 設定 WordPress 變數和包含的檔案。 */
require_once(ABSPATH . 'wp-settings.php');