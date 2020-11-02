<?php
/**
 * Plugin Name: Strava Leaderboard
 * Plugin URI:
 * Description:
 * Version: 0.1.0
 * Author: Erik Mitchell
 * Author URI:
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: slwp
 * Domain Path: /languages
 *
 * @package emsa
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Load composer autloader.
require_once( __DIR__ . '/vendor/autoload.php' );

// Import the autoloader.
include_once 'src/autoload.php';

if ( ! defined( 'SLWP_PLUGIN_FILE' ) ) {
    define( 'SLWP_PLUGIN_FILE', __FILE__ );
}

// Include the main SLWP class.
if ( ! class_exists( 'SLWP' ) ) {
    include_once dirname( __FILE__ ) . '/class-strava-leaderboard.php';
}
