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

    // App user data.
    $users = new SLWP_Users();
    $users->init();
    $users_data = $users->get_users_data();

    foreach ( $users_data as $user ) {
        $efforts = $api_wrapper->get_segment_efforts( $user->access_token, $fields['segments'][0]['segment'], $fields['start_date'], $fields['end_date'] );

        $args['name'] = $fields['name'];

        foreach ( $efforts as $effort ) :
            $args['efforts'][] = array(
                'time' => slwp()->format->format_time( $effort->getElapsedTime() ),
                'iskom' => slwp()->format->is_kom( $effort->getIsKom() ),
                'date' => slwp()->format->format_date( $effort->getStartDate() ),
                'activityurl' => $api_wrapper->get_activity_url_by_id( $effort->getActivity() ),
                'komrank' => slwp()->format->kom_rank( $effort->getKomRank() ),
                'prrank' => slwp()->format->pr_rank( $effort->getPrRank() ),
            );
        endforeach;
    }

    return $args;
}

function time_lb( $fields ) {
    $api_wrapper = new SLWP_Api_Wrapper();

    // App user data.
    $users = new SLWP_Users();
    $users->init();
    $users_data = $users->get_users_data();

    foreach ( $users_data as $user ) {
        $activities = $api_wrapper->get_athlete_activities( $user->access_token, strtotime( $fields['end_date'] ), strtotime( $fields['start_date'] ) );
        $args['name'] = $fields['name'];
        $total_distance = 0;
        $total_time = 0;
        $activities_count = 0;

        if ( empty( $activities ) || ! is_array( $activities ) ) {
            return false;
        }

        foreach ( $activities as $activity ) :
            $args['activities'][] = array(
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

        $args['total_time'] = slwp()->format->format_time( $total_time );
        $args['total_distance'] = slwp()->format->format_distance( $total_distance );
        $args['activities_count'] = $activities_count;
    }

    return $args;
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
