<?php

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

function slwp_get_leaderboards( $args = array(), $active = true ) {
    $leaderboards = get_posts(
        array(
            'posts_per_page' => -1,
            'post_type' => 'leaderboard',
            // add meta check for active.
        )
    );

    // add acf (meta) data.
    foreach ( $leaderboards as $leaderboard ) {
        $leaderboard->fields = get_fields( $leaderboard->ID );
    }

    return $leaderboards;
}

function slwp_add_athlete( $access_token = '' ) {
    if ( empty( $access_token ) || '' == $access_token ) {
        return false;
    }

    $api_wrapper = new SLWP_Api_Wrapper();
    $athlete = $api_wrapper->get_athlete( $access_token );

    $athlete_db = new SLWP_DB_Athletes();

    $row_id = $athlete_db->get_column_by( 'id', 'athlete_id', $athlete->getID() );

    if ( $row_id ) {
        return $row_id;
    }

    return $athlete_db->insert(
        array(
            'age' => '',
            'athlete_id' => $athlete->getId(),
            'first_name' => $athlete->getFirstname(),
            'gender' => $athlete->getSex(),
            'last_name' => $athlete->getLastname(),
        )
    );
}

function slwp_get_athletes( $args = '' ) {
    global $wpdb;

    $athlete_db = new SLWP_DB_Athletes();
    $athletes = $athlete_db->get_athletes( $args );

    // clean this.
    foreach ( $athletes as $athlete ) {
        $athlete->access_token = $wpdb->get_var( 'SELECT access_token from slwp_tokens_sl WHERE athlete_id = ' . $athlete->athlete_id );
    }

    return $athletes;
}

function slwp_add_leaderboard_activities( $athlete = '', $leaderboard_id = 0, $activities = '', $type = 'Time' ) {
    if ( empty( $athlete ) || empty( $activities ) || ! $leaderboard_id ) {
        return false;
    }

    $activities_db = new SLWP_DB_Leaderboard_Activities();
    // replace below with count - run get activities func
    $db_activities = $activities_db->get_activities(
        array(
            'athlete_id' => $athlete->athlete_id,
            'leaderboard_id' => $leaderboard_id,
        )
    );

    $data = array(
        'activity_count' => $activities['activities_count'],
        'athlete_id' => $athlete->athlete_id,
        'distance' => $activities['total_distance'],
        'last_updated' => date( 'Y-m-d H:i:s' ),
        'leaderboard_id' => $leaderboard_id,
        'time' => $activities['total_time'],
    );

    if ( count( $db_activities ) ) {
        // update.
        $row_id = $db_activities[0]->id;

        return $activities_db->update( $row_id, $data ); // returns bool.
    } else {
        // add.
        return $activities_db->insert( $data );
    }

    return;
}

function slwp_get_leaderboard_activities( $args = array() ) {
    $db = new SLWP_DB_Leaderboard_Activities();
    $activities = $db->get_activities( $args );

    return $activities;
}

function slwp_add_segments( $athlete = '', $leaderboard_id = 0, $efforts = array(), $segment_id = 0, $type = 'Segment' ) {
    if ( empty( $athlete ) || empty( $efforts ) || ! $leaderboard_id || ! $segment_id ) {
        return false;
    }

    $api_wrapper = new SLWP_Api_Wrapper();
    $segment = $api_wrapper->get_segment( $athlete->access_token, $segment_id );
    $segments_db = new SLWP_DB_Segments();

    foreach ( $efforts as $effort ) {
        // replace below with count - run get segment func
        $db_segments = $segments_db->get_segments(
            array(
                'athlete_id' => $athlete->athlete_id,
                'leaderboard_id' => $leaderboard_id,
                'segment_id' => $segment->getId(),
            )
        );

        // below converts date for mysql - it's dirty.
        $effort_date_arr = explode( '-', $effort['date'] );
        $effort_date = $effort_date_arr[2] . '-' . $effort_date_arr[0] . '-' . $effort_date_arr[1];

        $data = array(
            'activity_id' => '', // we pass the url
            'athlete_id' => $athlete->athlete_id,
            'date' => $effort_date,
            'distance' => $segment->getDistance(),
            'last_updated' => date( 'Y-m-d H:i:s' ),
            'leaderboard_id' => $leaderboard_id,
            'segment_id' => $segment->getId(),
            'segment_type' => '', // need to get from leaderboard meta.
            'time' => $effort['time'],
        );

        if ( count( $db_segments ) ) {
            // update.
            $row_id = $db_segments[0]->id;

            return $segments_db->update( $row_id, $data ); // returns bool.
        } else {
            // add.
            return $segments_db->insert( $data );
        }
    }

    return;
}

function slwp_get_segments( $args = array() ) {
    $db = new SLWP_DB_Segments();
    $segments = $db->get_segments( $args );

    return $segments;
}

function slwp_clean_time_distance_data( $activities = '' ) {
    if ( empty( $activities ) ) {
        return;
    }

    $total_distance = 0;
    $total_time = 0;
    $activities_count = 0;
    $athlete_data = array();

    foreach ( $activities as $activity ) :
        $athlete_data['activities'][] = array(
            'id' => $activity->getId(),
            'distance' => slwp()->format->format_distance( $activity->getDistance() ),
            'time' => slwp()->format->format_time( $activity->getMovingTime() ),
            'date' => slwp()->format->format_date( $activity->getStartDate() ),
            'type' => $activity->getType(),
        );

        $total_time = $total_time + $activity->getMovingTime(); // seconds.
        $total_distance = $total_distance + $activity->getDistance(); // meters.
        $activities_count++;
    endforeach;

    $athlete_data['total_time'] = slwp()->format->format_time( $total_time );
    $athlete_data['total_distance'] = slwp()->format->format_distance( $total_distance );
    $athlete_data['activities_count'] = $activities_count;

    return $athlete_data;
}

function slwp_clean_segments_data( $segment_efforts = '' ) {
    if ( empty( $segment_efforts ) ) {
        return;
    }

    $api_wrapper = new SLWP_Api_Wrapper();
    $athlete_data = array();

    foreach ( $segment_efforts as $effort ) :
        $athlete_data[] = array(
            'time' => slwp()->format->format_time( $effort->getElapsedTime() ),
            'iskom' => slwp()->format->is_kom( $effort->getIsKom() ),
            'date' => slwp()->format->format_date( $effort->getStartDate() ),
            'activityurl' => $api_wrapper->get_activity_url_by_id( $effort->getActivity() ),
            'komrank' => slwp()->format->kom_rank( $effort->getKomRank() ),
            'prrank' => slwp()->format->pr_rank( $effort->getPrRank() ),
        );
    endforeach;

    return $athlete_data;
}

function slwp_get_athlete_name( $athlete_id = 0 ) {
    $db = new SLWP_DB_Athletes();

    $first = $db->get_column_by( 'first_name', 'athlete_id', $athlete_id );
    $last = $db->get_column_by( 'last_name', 'athlete_id', $athlete_id );

    return $first . ' ' . $last; // add filter.
}

// GENERAL WORKFLOW
/*
    Webhooks (to be added)

    Manual (WP CLI)

    get activity details

    match to leaderboard

    update/insert into db

    le fin

*/

function slwp_workflow( $activities = array() ) {
    echo 'slwp_workflow()<br>';
    print_r( $activities );
    echo '<br>';

    if ( empty( $activities ) ) {
        echo 'no activities found<br>';
    }

}

// slwp_workflow();

// acf
function check_acf( $post_id = 0 ) {
    $fields = get_fields( $post_id );

    if ( ! $fields ) {
        return false;
    }

    switch ( $fields['type'] ) {
        case 'Segment':
            $args['segments'] = slwp_get_segments(
                array(
                    'leaderboard_id' => $post_id,
                    'segment_id' => 1354300, // would come from post meta.
                )
            );
            $args['content_type'] = 'segment';
            break;
        case 'Time':
            $args['activities'] = slwp_get_leaderboard_activities(
                array(
                    'leaderboard_id' => $post_id,
                    'orderby' => 'time',
                )
            );
            $args['content_type'] = 'time';
            break;
    }

    return $args;
}

function acf_is_field_group_exists( $value, $type = 'post_title' ) {
    $exists = false;

    if ( $field_groups = get_posts( array( 'post_type' => 'acf-field-group' ) ) ) {
        foreach ( $field_groups as $field_group ) {
            if ( $field_group->$type == $value ) {
                $exists = true;
            }
        }
    }

    return $exists;
}

function slwp_check_user_tokens() {
    // slwp()->users->check_users_token();
    // DOES NOT WORK
    $utr = new SLWP_Users_Token_Refresh();
    $utr->check_users_token();
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

// admin func?
function slwp_setup_webhooks() {
    slwp()->webhooks->create_subscription();
}

// slwp_setup_webhooks();
