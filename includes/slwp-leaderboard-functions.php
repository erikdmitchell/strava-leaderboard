<?php
/**
 * Strava Leaderboard Functions
 *
 * @package slwp
 * @version 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function time_lb( $fields ) {
    $api_wrapper = new SLWP_Api_Wrapper();
    $users_data = slwp()->users->get_users_data();
    echo 'users_data _deprecated<br>';
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

function slwp_get_leaderboards( $args = '' ) {
    $defaults = array( 'post_type' => 'leaderboard' );
    $args = wp_parse_args( $args, $defaults );
    $leaderboards = get_posts( $args );

    return $leaderboards;
}


