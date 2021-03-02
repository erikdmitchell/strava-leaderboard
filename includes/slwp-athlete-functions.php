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

function slwp_athlete_name($id = 0) {
    if (empty($id) || !$id) {
        echo '';
    }
    
    $athlete = slwp_get_athletes(array('athlete_id' => $id));
    $name = $athlete->first_name . ' ' . $athlete->last_name;

    echo $name;
}

function slwp_get_athlete_leaderboards( $args = '' ) {
    $athlete_lb_db = new SLWP_DB_Leaderboard_Athletes();
    $athlete_leaderboards = $athlete_lb_db->get_athlete_leaderboards( $args );
    
    return $athlete_leaderboards;    
}

function slwp_get_athlete_leaderboards_list($athlete_id = 0) {
    $athlete_leaderboards = array();
    $athlete_lb_data = slwp_get_athlete_leaderboards( array('athlete_id' => $athlete_id));
    
    if (empty($athlete_lb_data))
        return $athlete_leaderboards;
        
    foreach ($athlete_lb_data as $obj) {
        $athlete_leaderboards[] = $obj->leaderboard_id;
    }
    
    return $athlete_leaderboards;
}

function slwp_athlete_leaderboards_list($athlete_id = 0) {
    $lb_list = slwp_get_athlete_leaderboards_list( $athlete_id );
    
    echo implode(', ', $lb_list);
}