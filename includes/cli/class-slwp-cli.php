<?php

class SLWP_CLI {

    public function bulk_add_athletes() {
        WP_CLI::log( 'bulk_add_athletes()' );
        WP_CLI::log( 'we need to get each athlete and then add them to the leaderboard id - can we have it pass the ids?' );
    }
    
/*
public function display_arguments( $args, $assoc_args ) {

	// Run command wp wds display_arguments John Doe 'Jane Doe' 32 --title='Moby Dick' --author='Herman Melville' --published=1851 --publish --no-archive

	// Examples of Arguments.
	WP_CLI::line( var_export($args[0]) ); // John
	WP_CLI::line( var_export($args[1]) ); // Doe
	WP_CLI::line( var_export($args[2]) ); // Jane Doe
	WP_CLI::line( var_export($args[3]) ); // 32

	// Example of Associated Arguments
	WP_CLI::line( var_export($assoc_args['title']) );  // Moby Dick
	WP_CLI::line( var_export($assoc_args['author']) ); // Herman Melville
	WP_CLI::line( var_export($assoc_args['published']) ); // 1851

	// Example of Associated Arguments as flag
	WP_CLI::line( var_export($assoc_args['publish']) );  // True
	WP_CLI::line( var_export($assoc_args['archive']) );  // False

}
*/    

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
