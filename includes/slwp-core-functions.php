<?php
/**
 * Strava Leaderboard Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package slwp
 * @version 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend).
require SLWP_ABSPATH . 'includes/slwp-acf-functions.php';
require SLWP_ABSPATH . 'includes/slwp-athlete-functions.php';
require SLWP_ABSPATH . 'includes/slwp-segment-functions.php';
require SLWP_ABSPATH . 'includes/slwp-time-leaderboard-functions.php';

/**
 * Get client ID.
 * 
 * @access public
 * @return string
 */
function get_slwp_client_id() {
    return get_option( 'slwp_client_id', '' );
}

function get_slwp_client_secret() {
    return get_option( 'slwp_client_secret', '' );
}

function slwp_check_url_message() {
    if ( isset( $_GET['message'] ) && '' != $_GET['message'] ) {
        echo urldecode_deep( $_GET['message'] );
    }
}
add_action( 'wp_footer', 'slwp_check_url_message' );

function slwp_get_template_part( $slug, $name = '', $args = null ) {
    $template = false; // this needs to check for cache at some point.

    if ( ! $template ) {
        if ( $name ) {
            $template = locate_template(
                array(
                    "{$slug}-{$name}.php",
                    SLWP_PATH . "{$slug}-{$name}.php",
                )
            );

            if ( ! $template ) {
                $fallback = SLWP_PATH . "/templates/{$slug}-{$name}.php";
                $template = file_exists( $fallback ) ? $fallback : '';
            }
        }

        if ( ! $template ) {
            // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/slwp/slug.php.
            $template = locate_template(
                array(
                    "{$slug}.php",
                    $template_path . "{$slug}.php",
                )
            );
        }
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters( 'slwp_get_template_part', $template, $slug, $name );

    if ( $template ) {
        load_template( $template, false, $args );
    }
}

function slwp_check_user_tokens() {
    slwp()->users->check_users_token();
}

// Hook our function , slwp_check_user_tokens(), into the action slwp_user_token_check
add_action( 'slwp_user_token_check', 'slwp_check_user_tokens' );

/*
add_filter( 'cron_schedules', 'slwp_add_weekly_schedule' );
function slwp_add_weekly_schedule( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 7 * 24 * 60 * 60, // 7 days * 24 hours * 60 minutes * 60 seconds
        'display' => __( 'Once Weekly', 'slwp' ),
    );

    return $schedules;
}
*/