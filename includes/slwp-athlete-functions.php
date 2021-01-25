<?php
/**
 * Strava Leaderboard Athlete Functions
 *
 * @package slwp
 * @version 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function slwp_add_athlete($access_token = '') {
    if (empty($access_token) || '' == $access_token)
        return false;
    
    $api_wrapper = new SLWP_Api_Wrapper();
    $athlete = $api_wrapper->get_athlete( $access_token );    

    $athlete_db = new SLWP_DB_Athletes();
    
    $row_id = $athlete_db->get_column_by( 'id', 'athlete_id', $athlete->getID());
    
    if ($row_id)
        return $row_id;

    return $athlete_db->insert(array(
        'age' => '',
        'athlete_id' => $athlete->getId(),
        'first_name' => $athlete->getFirstname(),
        'gender' => $athlete->getSex(),
        'last_name' => $athlete->getLastname(),                
    ));
}

function slwp_get_athletes($args = '') {
    $athlete_db = new SLWP_DB_Athletes();
    $athletes = $athlete_db->get_athletes( $args );
    
    return $athletes;
}