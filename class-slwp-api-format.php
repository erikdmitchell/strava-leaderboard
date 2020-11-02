<?php
/**
 * SLWP format class
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * SLWP_Api_Format class.
 */
class SLWP_Api_Format {

    public function __construct() {}

    public function format_distance( $distance = 0, $current_format = 'meters', $output_format = 'miles' ) {
        if ( 'feet' == $output_format ) {
            return ( round( $distance * 3.2808399, 0 ) );
        }

        return round( $distance * 0.000621371192, 2 );

        // m to mi
        // return i*1609.344;
    }

    public function format_grade( $grade = 0 ) {
        return $grade . '%';
    }

    public function format_location( $city = '', $state = '', $country = '' ) {
        return "{$city}, {$state}, {$country}";
    }

    public function format_climb_cat( $cat = 0 ) {
        $base_cat = 5;
        $actual_cat = $base_cat - $cat;

        if ( $actual_cat == 0 ) {
            $actual_cat = 'HC';
        }

        return $actual_cat;
    }

    public function format_time( $seconds = 0 ) {
        $hours = floor( $seconds / 3600 ) . ':';
        $mins = floor( $seconds / 60 % 60 ) . ':';
        $secs = floor( $seconds % 60 );

        $secs = str_pad( $secs, 2, 0 );

        if ( 0 == $hours ) {
            $hours = '';
        }

        $format = "{$hours}{$mins}{$secs}";

        return $format;
    }

    public function is_kom( $is_kom = false ) {
        if ( $is_kom ) {
            return 'Crown';
        }

        return '';
    }

    // only includes top 10 at time of upload.
    public function kom_rank( $rank = 0 ) {
        if ( 0 == $rank || '' == $rank || null == $rank ) {
            return '';
        }

        return "KOM Rank: $rank";
    }

    // only includes top 3 at time of upload.
    public function pr_rank( $rank = 0 ) {
        if ( 0 == $rank || '' == $rank || null == $rank ) {
            return '';
        }

        return "PR Rank: $rank";
    }

    public function format_date( $date = '', $format = 'm-d-Y', $type = 'dto' ) {
        return $date->format( $format );
    }

    public function get_activity_url_by_id( $id_obj = '' ) {
        return '<a href="https://www.strava.com/activities/' . $id_obj['id'] . '" target="_blank">View Activity</a>';
    }

}
