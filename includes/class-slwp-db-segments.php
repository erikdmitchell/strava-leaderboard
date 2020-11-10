<?php

class SLWP_DB_Segments extends SLWP_DB {

    /**
     * Get things started
     *
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {
        $this->table_name  = 'slwp_segments';
        $this->primary_key = 'id';
        $this->version     = '0.1.0';
    }

    /**
     * Get columns and formats
     *
     * @access  public
     * @since   0.1.0
     */
    public function get_columns() {
        return array(
            'id' => '%d',
            'activity_id' => '%d',
            'athlete_id' => '%d',
            'date' => '%s',
            'distance' => '%s',
            'last_updated' => '%s',
            'leaderboard_id' => '%d',
            'segment_id' => '%d',
            'segment_type' => '%s',
            'time' => '%s',
        );
    }

    /**
     * Get default column values
     *
     * @access  public
     * @since   0.1.0
     */
    public function get_column_defaults() {
        return array(
            'activity_id' => '',
            'athlete_id' => '',
            'date' => '',
            'distance' => '0.00',
            'last_updated' => '',
            'leaderboard_id' => '',
            'segment_id' => '',
            'segment_type' => '',
            'time' => '',
        );
    }

    /**
     * Retrieve segments from the database
     *
     * @access  public
     * @since   0.1.0
     * @param   array $args
     * @param   bool  $count  Return only the total number of results found (optional)
     */
    public function get_segments( $args = array(), $count = false ) {
        global $wpdb;

        $defaults = array(
            'number' => 20,
            'offset' => 0,
            'athlete_id' => 0,
            'activity_id' => 0,
            'date' => '', // not supported
            'distance' => '', // not supported
            'leaderboard_id' => 0,
            'segment_id' => 0,
            'segment_type' => '', // not supported
            'time' => '',
            'orderby' => 'time',
            'order' => 'ASC',
        );

        $args  = wp_parse_args( $args, $defaults );

        if ( $args['number'] < 1 ) {
            $args['number'] = 999999999999;
        }

        $where = '';

        // athlete id(s).
        if ( ! empty( $args['athlete_id'] ) ) {

            if ( is_array( $args['athlete_id'] ) ) {
                $athlete_ids = implode( ',', $args['athlete_id'] );
            } else {
                $athlete_ids = intval( $args['athlete_id'] );
            }

            $where .= "WHERE `athlete_id` IN( {$athlete_ids} ) ";

        }

        // activities id(s).
        if ( ! empty( $args['activity_id'] ) ) {

            if ( empty( $where ) ) {
                $where .= ' WHERE';
            } else {
                $where .= ' AND';
            }

            if ( is_array( $args['activity_id'] ) ) {
                $where .= " `activity_id`  IN('" . implode( ',', $args['activity_id'] ) . "') ";
            } else {
                $where .= " `activity_id` = '" . intval( $args['activity_id'] ) . "' ";
            }
        }

        // date
        // distance

        // leaderboard id(s).
        if ( ! empty( $args['leaderboard_id'] ) ) {

            if ( empty( $where ) ) {
                $where .= ' WHERE';
            } else {
                $where .= ' AND';
            }

            if ( is_array( $args['leaderboard_id'] ) ) {
                $where .= " `leaderboard_id`  IN('" . implode( ',', $args['leaderboard_id'] ) . "') ";
            } else {
                $where .= " `leaderboard_id` = '" . intval( $args['leaderboard_id'] ) . "' ";
            }
        }

        // segment id(s)
        if ( ! empty( $args['segment_id'] ) ) {

            if ( empty( $where ) ) {
                $where .= ' WHERE';
            } else {
                $where .= ' AND';
            }

            if ( is_array( $args['segment_id'] ) ) {
                $where .= " `segment_id`  IN('" . implode( ',', $args['segment_id'] ) . "') ";
            } else {
                $where .= " `segment_id` = '" . intval( $args['segment_id'] ) . "' ";
            }
        }

        // segment type
        // time

        $args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? $this->primary_key : $args['orderby'];

        $cache_key = ( true === $count ) ? md5( 'slwp_segments_count' . serialize( $args ) ) : md5( 'slwp_segments_' . serialize( $args ) );

        $results = wp_cache_get( $cache_key, 'segments' );

        if ( false === $results ) {

            if ( true === $count ) {

                $results = absint( $wpdb->get_var( "SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$where};" ) );

            } else {

                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d;",
                        absint( $args['offset'] ),
                        absint( $args['number'] )
                    )
                );

            }

            wp_cache_set( $cache_key, $results, 'segments', 3600 );

        }

        return $results;
    }

    /**
     * Return the number of results found for a given query
     *
     * @param  array $args
     * @return int
     */
    public function count( $args = array() ) {
        return $this->get_segments( $args, true );
    }

    /**
     * Create the table
     *
     * @access  public
     * @since   0.1.0
     */
    public function create_table() {}
}
