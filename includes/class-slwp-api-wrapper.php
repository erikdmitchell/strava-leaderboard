<?php

use GuzzleHttp\Client;
use Swagger\Client\Api;

class SLWP_Api_Wrapper {

    public function __construct() {}

    public function get_segment( $athlete_secret = '', $id = 580149 ) {
        $config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken( $athlete_secret );
        $apiInstance = new Api\SegmentsApi( new Client(), $config );

        try {
            $result = $apiInstance->getSegmentById( $id );

            return $result;
        } catch ( Exception $e ) {
            return 'Exception when calling SegmentsApi->getSegmentById: ' . $e->getMessage();
        }
    }

    public function get_segment_efforts( $athlete_secret = '', $id = 580149, $start_date = '2020-01-01', $end_date_local = '2020-10-21', $per_page = 30 ) {
        $config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken( $athlete_secret );

        $apiInstance = new Api\SegmentEffortsApi( new Client(), $config );

        $start_date_local = new \DateTime( $start_date ); // \DateTime | ISO 8601 formatted date time.
        $end_date_local = new \DateTime(); // \DateTime | ISO 8601 formatted date time.

        try {
            $result = $apiInstance->getEffortsBySegmentId( $id, $start_date_local, $end_date_local, $per_page );

            return $result;
        } catch ( Exception $e ) {
            return 'Exception when calling SegmentsApi->getSegmentById: ' . $e->getMessage();
        }
    }

    public function get_athlete( $athlete_secret = '' ) {
        $config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken( $athlete_secret );

        $apiInstance = new Api\AthletesApi( new Client(), $config );

        try {
            $result = $apiInstance->getLoggedInAthlete();

            return $result;
        } catch ( Exception $e ) {
            return 'Exception when calling AthletesApi->getLoggedInAthlete: ' . $e->getMessage();
        }
    }

    public function get_activity( $athlete_secret = '', $id = 3221463650, $include_all_efforts = true ) {
        $config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken( $athlete_secret );

        $apiInstance = new Api\ActivitiesApi( new Client(), $config );

        try {
            $result = $apiInstance->getActivityById( $id, $include_all_efforts );

            return $result;
        } catch ( Exception $e ) {
            return 'Exception when calling ActivitiesApi->getActivityById: ' . $e->getMessage();
        }
    }

    public function get_athlete_activities( $athlete_secret = '', $before = '', $after = '', $per_page = 30, $page = 1 ) {
        $error = new WP_Error();
        $config = Swagger\Client\Configuration::getDefaultConfiguration()->setAccessToken( $athlete_secret );

        $apiInstance = new Api\ActivitiesApi( new Client(), $config );

        try {
            $result = $apiInstance->getLoggedInAthleteActivities( $before, $after, $page, $per_page );

            return $result;
        } catch ( Exception $e ) {
            $parsed_error = $this->parse_error( $e->getMessage() );
            $error->add( 'exception', $parsed_error['message'], $parsed_error['data'] );

            return $error;
        }
    }

    public function get_activity_url_by_id( $id_obj = '' ) {
        return '<a href="https://www.strava.com/activities/' . $id_obj['id'] . '" target="_blank">View Activity</a>';
    }

    protected function parse_error( $error = '' ) {
        $exploded_error = explode( 'response:', $error );

        $arr['message'] = $exploded_error[0] . 'response'; // "response" is removed as part of split.
        $arr['data'] = json_decode( $exploded_error[1] );

        return $arr;
    }

}
