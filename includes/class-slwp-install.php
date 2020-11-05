<?php

defined( 'ABSPATH' ) || exit;

/**
 * SLWP_Install Class.
 */
class SLWP_Install {

    /**
     * Hook in tabs.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
    }

    public static function check_version() {
        if ( version_compare( SLWP_VERSION, get_option( 'slwp_version', 0 ), '>' ) ) {
            self::install();
        }
    }

    public static function install() {
        if ( ! is_blog_installed() ) {
            return;
        }

        // Check if we are not already running this routine.
        if ( 'yes' === get_transient( 'slwp_installing' ) ) {
            return;
        }

        // If we made it till here nothing is running yet, lets set the transient now.
        set_transient( 'slwp_installing', 'yes', MINUTE_IN_SECONDS * 10 );

        self::create_tables();
        self::update_version();
        self::maybe_update_db_version();
        self::setup_cron_jobs();

        delete_transient( 'slwp_installing' );
    }

    public static function create_tables() {
        global $wpdb;
        
        $sql = array();

        $slwp_db_version = get_option( 'slwp_db_version', 0 );

        if ( version_compare( $slwp_db_version, 0, '>' ) ) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();

        $sql[] = "CREATE TABLE slwp_tokens_refresh (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            athlete_id int(11) DEFAULT NULL,
            scope tinyint(1) DEFAULT NULL,
            refresh_token varchar(255) DEFAULT NULL,
            PRIMARY KEY (id)
    	) $charset_collate;";

        $sql[] = "CREATE TABLE slwp_tokens_sl (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            athlete_id int(11) DEFAULT NULL,
            scope tinyint(1) DEFAULT NULL,
            access_token varchar(255) DEFAULT NULL,
            expires_at int(11) DEFAULT NULL,
            PRIMARY KEY (id)
    	) $charset_collate;";

        $sql[] = "CREATE TABLE swlp_activities (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            activity_id int(11) DEFAULT NULL,
            athlete_id int(11) DEFAULT NULL,
            distance decimal(15,2) DEFAULT 0,
            date date,
            leaderboard_id int(11) DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE swlp_segments (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            activity_id int(11) DEFAULT NULL,
            athlete_id int(11) DEFAULT NULL,
            date date,
            distance decimal(15,2) DEFAULT 0,
            leaderboard_id int(11) DEFAULT NULL,
            segment_id int(11) DEFAULT NULL,
            segment_type varchar(15) DEFAULT NULL,
            time time,
            PRIMARY KEY (id)
        ) $charset_collate;";


        $sql[] = "CREATE TABLE swlp_athletes (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            age varchar(12) DEFAULT NULL,
            athlete_id int(11) DEFAULT NULL,
            first_name varchar(60) DEFAULT NULL,
            gender varchar(1) DEFAULT NULL,
            last_name varchar(64) DEFAULT NULL,
            time time,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option( 'slwp_db_version', SLWP_DB_VERSION );
    }

    public static function update_version() {
        update_option( 'slwp_version', SLWP_VERSION );
    }

    public static function maybe_update_db_version() {
        // no updates yet
    }

    public static function setup_cron_jobs() {
        // Use wp_next_scheduled to check if the event is already scheduled
        $timestamp = wp_next_scheduled( 'slwp_user_token_check' );

        // If $timestamp == false schedule daily backups since it hasn't been done previously
        if ( $timestamp == false ) {
            // Schedule the event for right now, then to repeat daily using the hook 'slwp_user_token_check'
            wp_schedule_event( time(), 'daily', 'slwp_user_token_check' );
        }
    }

}

SLWP_Install::init();
