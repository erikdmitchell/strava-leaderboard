<?php
/**
 * Strava Leaderboard ACF Functions
 *
 * @package slwp
 * @version 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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



