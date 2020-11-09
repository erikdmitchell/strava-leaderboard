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

function slwp_get_leaderboards( $args = array() ) {
    return get_posts(
        array(
            'posts_per_page' => -1,
            'post_type' => 'leaderboard',
        // add meta check for active.
        )
    );
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

function slwp_add_activities( $athlete = '', $leaderboard_id = 0, $activities = '', $type = 'Time' ) {
    if ( empty( $athlete ) || empty( $activities ) || empty( $type ) || ! $leaderboard_id ) {
        return false;
    }

    switch ( $type ) {
        case 'Segment':
            echo "slwp_add_activities -> Segment\n";
            break;
        case 'Time':
            $activities_db = new SLWP_DB_Activities();
            // replace below with count
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
                'leaderboard_id' => $leaderboard_id,
                'last_updated' => date( 'Y-m-d H:i:s' ),
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
            break;
    }

    return;
}

function slwp_get_activities() {}

function slwp_add_segments() {}

function slwp_get_segments() {}

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
            $args = single_segment( $fields );
            $args['content_type'] = 'segment';
            break;
        case 'Time':
            $args = time_lb( $fields );
            $args['content_type'] = 'time';
            break;
    }

    return $args;
}

function single_segment( $fields ) {
    $api_wrapper = new SLWP_Api_Wrapper();
    $users_data = slwp_get_athletes();
    $data = array();
    $data['name'] = $fields['name'];

    foreach ( $users_data as $user ) {
        // we are setting per page to 1. I think this wil lalways return the fastest time.
        $efforts = $api_wrapper->get_segment_efforts( $user->access_token, $fields['segments'][0]['segment'], $fields['start_date'], $fields['end_date'], 1 );
        $athlete = $api_wrapper->get_athlete( $user->access_token );
        $athlete_data = array();

        $athlete_data['athlete_id'] = $user->athlete_id;
        $athlete_data['firstname'] = $athlete->getFirstname();
        $athlete_data['lastname'] = $athlete->getLastname();

        if ( empty( $efforts ) || ! is_array( $efforts ) ) {
            continue;
        }
        // limit to 1?
        foreach ( $efforts as $effort ) :
            $athlete_data['efforts'][] = array(
                'time' => slwp()->format->format_time( $effort->getElapsedTime() ),
                'iskom' => slwp()->format->is_kom( $effort->getIsKom() ),
                'date' => slwp()->format->format_date( $effort->getStartDate() ),
                'activityurl' => $api_wrapper->get_activity_url_by_id( $effort->getActivity() ),
                'komrank' => slwp()->format->kom_rank( $effort->getKomRank() ),
                'prrank' => slwp()->format->pr_rank( $effort->getPrRank() ),
            );
        endforeach;

        $data['athletes'][] = $athlete_data;
    }
    // run sort data here
    return $data;
}

function time_lb( $fields ) {
    $api_wrapper = new SLWP_Api_Wrapper();
    $users_data = slwp_get_athletes();
    $data = array();
    $data['name'] = $fields['name'];

    foreach ( $users_data as $user ) {
        $activities = $api_wrapper->get_athlete_activities( $user->access_token, strtotime( $fields['end_date'] ), strtotime( $fields['start_date'] ) );
        $total_distance = 0;
        $total_time = 0;
        $activities_count = 0;
        $athlete = $api_wrapper->get_athlete( $user->access_token );
        $athlete_data = array();

        $athlete_data['athlete_id'] = $user->athlete_id;
        $athlete_data['firstname'] = $athlete->getFirstname();
        $athlete_data['lastname'] = $athlete->getLastname();

        if ( empty( $activities ) || ! is_array( $activities ) ) {
            continue;
        }

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

        $data['athletes'][] = $athlete_data;
    }

    return $data;
}

function slwp_clean_time_distance_data( $activities = array() ) {
    if ( empty( $activities ) || ! is_array( $activities ) ) {
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

function is_field_group_exists( $value, $type = 'post_title' ) {
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

