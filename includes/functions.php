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
    $users_data = slwp()->users->get_users_data();
    $data = array();
    $data['name'] = $fields['name'];

    foreach ( $users_data as $user ) {
        $efforts = $api_wrapper->get_segment_efforts( $user->access_token, $fields['segments'][0]['segment'], $fields['start_date'], $fields['end_date'] );
        $athlete = $api_wrapper->get_athlete( $user->access_token );
        $athlete_data = array();
print_r($athlete);        
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

    return $data;    
}

function time_lb( $fields ) {
    $api_wrapper = new SLWP_Api_Wrapper();
    $users_data = slwp()->users->get_users_data();
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

/*
register_deactivation_hook( __FILE__, 'slwp_remove_user_token_check' );
function slwp_remove_user_token_check() {
    wp_clear_scheduled_hook( 'slwp_user_token_check' );
}
*/
