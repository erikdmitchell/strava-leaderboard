<?php

function slwp_get_activity_segment_ids( $segment_efforts = '' ) {
    $ids = array();

    foreach ( $segment_efforts as $effort ) {
        $ids[] = $effort->getSegment()->getId();
    }

    return $ids;
}

function slwp_is_activity_in_leaderboard( $args = array() ) { // change name to is_laderboard_activity
    $defaults = array(
        'lb_start_date' => '',
        'lb_end_date' => '',
        'activity_date' => '',
        'activity_type' => '', // not used.
    );

    $parsed_args = wp_parse_args( $args, $defaults ); 
    
    // convert dates.
    $lb_start_date = strtotime($parsed_args['lb_start_date']);
    $lb_end_date = strtotime($parsed_args['lb_end_date']);
    $activity_date = strtotime($parsed_args['activity_date']);
        
    // check date.
    if (($lb_start_date <= $activity_date) && ($activity_date <= $lb_end_date)) {
        return true;       
    }
    
    return false;
}

function slwp_is_segment_in_leaderboard( $args = array() ) { // change name is_leaderboard_segment
    $defaults = array(
        'lb_start_date' => '',
        'lb_end_date' => '',
        'activity_date' => '',
        'activity_type' => '',
        'segment_ids' => '',
        'lb_segment_ids' => '',
    );

    $parsed_args = wp_parse_args( $args, $defaults );
    
    if (!is_array($parsed_args['segment_ids'])) {
        $parsed_args['segment_ids'] = explode(' ', $parsed_args['segment_ids']);
    } 
    
    // clean up acf/meta segments.
    $lb_segment_ids_arr = array();
    
    if (empty($parsed_args['lb_segment_ids']) || empty($parsed_args['segment_ids']))
        return false;
        
    foreach ($parsed_args['lb_segment_ids'] as $segment_ids) {
        $lb_segment_ids_arr[] = $segment_ids['segment'];
    }
   
    $result = array_intersect($lb_segment_ids_arr, $parsed_args['segment_ids']);

    if (!empty($result))
        return true;

    return false;    
}

function slwp_is_leaderboard_activity( $args = array() ) {
    $defaults = array(
        'activity_id' => 0,
        'activity_type' => '',
        'activity_date' => '',
        'segment_id' => 0, // can be array.
    );

    $parsed_args = wp_parse_args( $args, $defaults );
    
    if (!is_array($parsed_args['segment_id'])) {
        $parsed_args['segment_id'] = explode(' ', $parsed_args['segment_id']);
    }

    $leaderboards = slwp_get_leaderboards();
    $valid_leaderboards = array();
   
    foreach ($leaderboards as $leaderboard) {
        switch ($leaderboard->fields['type']) {
            case 'Time' :
                $result = slwp_is_activity_in_leaderboard( array(
                    'lb_start_date' => $leaderboard->fields['start_date'],
                    'lb_end_date' => $leaderboard->fields['end_date'],
                    'activity_date' => $parsed_args['activity_date'],
                    'activity_type' => $parsed_args['activity_type'], // not used
                    'lb_activity_type' => $leaderboard->fields['activity_type'], // not used
                ) );           
                break;
            case 'Segment' :
                $result = slwp_is_segment_in_leaderboard( array(
                    'lb_start_date' => $leaderboard->fields['start_date'],
                    'lb_end_date' => $leaderboard->fields['end_date'],
                    'activity_date' => $parsed_args['activity_date'],
                    'activity_type' => $parsed_args['activity_type'], // not used
                    'segment_ids' => $parsed_args['segment_id'],
                    'lb_segment_ids' => $leaderboard->fields['segments'],
                ) );             
                break;
            default :
                $result = false;
        }
        
        if ($result) {
            $valid_leaderboards[] = $leaderboard->ID;
        }
    }
    
    if (empty($valid_leaderboards))
        return false;
    
    //return $valid_leaderboards;
    return true;
}

class SLWP_CLI {

    public function workflow( $args, $assoc_args ) {
        global $wpdb;

        $api_wrapper = new SLWP_Api_Wrapper();
        //$leaderboards = slwp_get_leaderboards(); // use only active.

        WP_CLI::log( 'Begin Workflow' );
        WP_CLI::warning( 'This is where we would get notification via webhook.' );

        if ( ! isset( $args[0] ) ) {
            WP_CLI::error( 'You need to pass an athlete id.' );
        }

        $athlete_id = $args[0];

        WP_CLI::log( 'Athlete: ' . $athlete_id );

        // get athlete token
        $access_token = $wpdb->get_var( "SELECT access_token from slwp_tokens_sl WHERE athlete_id = $athlete_id" );

        // slwp_get_athletes()

        // can add as vars - default is yesterday.
        $start_date = date( 'Y/m/d', strtotime( '-1 days' ) );
        $end_date = date( 'Y/m/d' );

        WP_CLI::log( 'Activity date range: ' . $start_date . ' - ' . $end_date );

/*
        $activities = $api_wrapper->get_athlete_activities( $access_token, strtotime( $end_date ), strtotime( $start_date ) );

        if ( is_wp_error( $activities ) ) {
            WP_CLI::error( $activities->get_error_message() );
        }
*/

WP_CLI::log('Begin activity test hack');

$foo = slwp_is_leaderboard_activity( array( 
    'activity_id' => 4377045590,
    'activity_type' => 'Ride',
    'activity_date' => '11-22-2020',
    'segment_id' => array(
        17919534,
        11573052,
        1408723,
        9133178,
        13179431,
        5295945,
        2401193,
        9307730,
        20350503,
        1139795,
        653984,
        653985,
        4105927,
        778835,
        653987,
        2530133,
        14877411,
        7769994,
        1533949,
        12665915,
        8078888,
        4837427,
        652309,
        15414892,
        2014333,
        15460216,       
    ),   
) );
print_r($foo); // array
WP_CLI::log('End activity test hack');
return;
        foreach ( $activities as $activity ) {
            // $activity brings in a bunch of data, but if we get it by the id, we get more data including segments.
            $activity_id = $activity->getId();
            $detailed_activity = $api_wrapper->get_activity( $access_token, $activity_id );
            $activity_start_date = slwp()->format->format_date( $activity->getStartDate() );
            $activity_type = $activity->getType();
            $segment_ids = slwp_get_activity_segment_ids( $detailed_activity->getSegmentEfforts() );

            // basic data we need to pass.
            $return = slwp_is_leaderboard_activity( array(
                'activity_id' => $activity_id,
                'activity_type' => $activity_type,
                'activity_date' => $activity_start_date,
                'segment_id' => $segment_ids,               
            ) );
            
print_r($return);

            // is this a vaild activity
            // leaderboards?

            // check activity, add, update or continue.
            // print_r($detailed_activity->getSegmentEfforts());

            /*
            $data = array(
                'activity_id' => $activity->getId(),
                'external_id' => $activity->getExternalId(),
                'upload_id' => $activity->getUploadId(),
                'athlete_id' => $activity->getAthlete()->getId(),
                'name' => $activity->getName(),
                'distance' => $activity->getDistance(),
                'moving_time' => $activity->getMovingTime(),
                'total_elevation_gain' => $activity->getTotalElevationGain(),
                'type' => $activity->getType(),
                'start_date' => $activity->getStartDate(),
                'trainer' => $activity->getTrainer(),
                'commute' => $activity->getCommute(),
                'manual' => $activity->getManual(),
                'private' => $activity->getPrivate(), // we need to remove it i think
                'flagged' => $activity->getFlagged(),
                'workout_type' => $activity->getWorkoutType(),
                'upload_id_str' => $activity->getUploadIdStr(),
                'average_speed' => $activity->getAverageSpeed(),
                'last_updated' => date( 'Y-m-d H:i:s' ),
            );
            */
        }
WP_CLI::log('le fin');
    }

    /**
     * Update Strava API user tokens.
     *
     * @since 0.1.0
     * @author erikdmitchell
     */    
    public function update_user_tokens() {
        slwp_check_user_tokens();
        
        WP_CLI::log( 'Updated user tokens.' );   
    }

    // TEMP
    public function add_leaderboard_activity( $args, $assoc_args ) {
        $api_wrapper = new SLWP_Api_Wrapper();
        $athletes = slwp_get_athletes();

        WP_CLI::log( 'add_leaderboard_activity()' );

        if ( ! isset( $args[0] ) ) {
            WP_CLI::error( 'You need to pass a leaderboard id.' );
        }

        $leaderboard_id = $args[0];

        WP_CLI::log( "Leaderboard ID - $leaderboard_id" );

        if ( 41 == $leaderboard_id ) {
            // hardcodes -> Oct Challenge.
            $start_date = '10/01/2020';
            $end_date = '10/31/2020';
            $leaderboard_id = 41;
            $type = 'td';
        } elseif ( 40 == $leaderboard_id ) {
            // hardcodes -> segment.
            $start_date = '01/01/2020';
            $end_date = date( 'm/d/Y' );
            $leaderboard_id = 40;
            $segment_id = 1354300;
            $type = 'segment';
        } else {
            WP_CLI::error( 'Bad leaderboard id.' );
        }

        switch ( $type ) {
            case 'td':
                foreach ( $athletes as $athlete ) {
                    $activities = $api_wrapper->get_athlete_activities( $athlete->access_token, strtotime( $end_date ), strtotime( $start_date ) );
                    $activities_clean = slwp_clean_time_distance_data( $activities );

                    slwp_add_leaderboard_activities( $athlete, $leaderboard_id, $activities_clean );
                }
                break;
            case 'segment':
                foreach ( $athletes as $athlete ) {
                    $efforts = $api_wrapper->get_segment_efforts( $athlete->access_token, $segment_id, $start_date, $end_date, 1 );
                    $efforts_clean = slwp_clean_segments_data( $efforts );

                    slwp_add_segments( $athlete, $leaderboard_id, $efforts_clean, $segment_id );
                }
                break;
        }

        WP_CLI::success( "$type added." );
    }

    public function bulk_add_athletes_to_leaderboards( $args, $assoc_args ) {
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

    public function bulk_add_athletes() {
        global $wpdb;

        WP_CLI::log( 'bulk_add_athletes()' );

        $athlete_data = $wpdb->get_results( 'SELECT * FROM slwp_tokens_sl' );
        $api_wrapper = new SLWP_Api_Wrapper();

        foreach ( $athlete_data as $db_athlete ) {
            $athlete = $api_wrapper->get_athlete( $db_athlete->access_token );

            $row_id = $wpdb->get_var( "SELECT id FROM slwp_athletes WHERE athlete_id = $db_athlete->athlete_id" );

            if ( $row_id ) {
                WP_CLI::log( 'Skipped' );
            } else { // below should be run via custom db
                $wpdb->insert(
                    'slwp_athletes',
                    array(
                        'athlete_id' => $db_athlete->athlete_id,
                        'first_name' => $athlete->getFirstname(),
                        'last_name' => $athlete->getLastname(),
                        'gender' => $athlete->getSex(),
                        // 'age' => '', // not provided.
                    )
                );

                WP_CLI::log( 'Added' );
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
