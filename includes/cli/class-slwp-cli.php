<?php

class SLWP_CLI {

    public function bulk_add_athletes( $args, $assoc_args ) {
        global $wpdb;

        WP_CLI::log( 'bulk_add_athletes()' );

        // setup args.
        if ( isset( $args[0] ) ) {
            $athlete_ids = explode( ',', $args[0] );
        } else {
            $athlete_ids = $wpdb->get_col( 'SELECT athlete_id FROM slwp_tokens_sl' );
        }

        if ( isset( $args[1] ) ) {
            $leaderboard_ids = explode( ',', $args[1] );
        } else {
            $leaderboard_ids = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type' => 'leaderboard',
                    'fields' => 'ids',
                )
            );
        }

        // update db.
        foreach ( $athlete_ids as $athlete_id ) {
            foreach ( $leaderboard_ids as $leaderboard_id ) {
                $row_id = $wpdb->get_var( "SELECT id FROM slwp_leaderbpard_athletes WHERE athlete_id = $athlete_id AND leaderboard_id = $leaderboard_id" );

                if ( $row_id ) {
                    WP_CLI::log( 'Skipped' );
                } else {
                    $wpdb->insert(
                        'slwp_leaderbpard_athletes',
                        array(
                            'athlete_id' => $athlete_id,
                            'leaderboard_id' => $leaderboard_id,
                        )
                    );
                    WP_CLI::log( 'Added' );
                }
            }
        }
    }

    public function dbsync() {
        WP_CLI::log( 'DB Sync' );

        $scope = 'read,activity:read';
        $external_db = new wpdb( 'db217690_slwp', 'o4*S#g0u_xK', 'db217690_slwp', 'internal-db.s217690.gridserver.com' );

        $fields = array(
            'scope',
            'expires_at',
            'access_token',
        );
        $this->sync_db_info( 'slwp_tokens_sl', $external_db, $scope, $fields );

        $fields = array(
            'scope',
            'refresh_token',
        );
        $this->sync_db_info( 'slwp_tokens_refresh', $external_db, $scope, $fields );
    }

    protected function sync_db_info( $table = '', $external_db = '', $scope = '', $fields = '' ) {
        global $wpdb;

        if ( empty( $table ) || empty( $external_db ) || empty( $scope ) || empty( $fields ) ) {
            WP_CLI::warning( 'Missing param.' );

            return;
        }

        WP_CLI::log( 'DB Table: ' . $table );

        $rows = $external_db->get_results( "SELECT * FROM $table" );

        foreach ( $rows as $obj ) :
            $row_id = $wpdb->get_var( "SELECT id FROM $table WHERE athlete_id = " . $obj->athlete_id );
            $data = array();

            // map object data to fields.
            foreach ( $fields as $field ) :
                $data[ $field ] = $obj->$field;
            endforeach;

            $data['scope'] = $scope; // override scope.

            if ( $row_id ) {
                WP_CLI::log( 'Skip db row' );
            } else {
                $data['athlete_id'] = $obj->athlete_id;

                $id = $wpdb->insert( $table, $data );

                if ( $id ) {
                    WP_CLI::success( 'Data inserted.' );
                } else {
                    WP_CLI::warning( 'Failed to insert.' );
                }
            }
        endforeach;
    }

}

function slwp_cli_register_commands() {
    WP_CLI::add_command( 'slwp', 'SLWP_CLI' );
}

add_action( 'cli_init', 'slwp_cli_register_commands' );
