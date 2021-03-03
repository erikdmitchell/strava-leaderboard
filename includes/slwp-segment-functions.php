<?php
/**
 * Strava Leaderboard Segement Functions
 *
 * @package slwp
 * @version 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function single_segment( $fields ) {
    $api_wrapper = new SLWP_Api_Wrapper();
    // $users_data = slwp()->users->get_users_data();
    echo 'users_data _deprecated<br>';
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




