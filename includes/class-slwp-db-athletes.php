<?php

class SLWP_DB_Athletes extends SLWP_DB {

    /**
     * Get things started
     *
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {
        $this->table_name  = 'slwp_athletes';
        $this->primary_key = 'order_id';
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
            'age' => '%s',
            'athlete_id' => '%d',
            'first_name' => '%s',
            'gender' => '%s',
            'last_name' => '%s',
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
            'age' => '',
            'athlete_id' => 0,
            'first_name' => '',
            'gender' => '',
            'last_name' => '',
        );
    }

    /**
     * Retrieve athletes from the database
     *
     * @access  public
     * @since   0.1.0
     * @param   array $args
     * @param   bool  $count  Return only the total number of results found (optional)
     */
    public function get_athletes( $args = array(), $count = false ) {
        global $wpdb;

        $defaults = array(
            'number' => 20,
            'offset' => 0,
            'athlete_id' => 0,
            'age' => '',
            'gender' => '',
            'orderby' => 'first_name',
            'order' => 'DESC',
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

        // athlete age (should accept a range via an array).
        if ( ! empty( $args['age'] ) ) {

            if ( empty( $where ) ) {
                $where .= ' WHERE';
            } else {
                $where .= ' AND';
            }

            if ( is_array( $args['age'] ) ) {
                $where .= " `age` IN('" . implode( "','", $args['age'] ) . "') ";
            } else {
                $where .= " `age` = '" . $args['age'] . "' ";
            }
        }

        if ( ! empty( $args['gender'] ) ) {

            if ( empty( $where ) ) {
                $where .= ' WHERE';
            } else {
                $where .= ' AND';
            }

            $where .= " `gender` = '" . $args['gender'] . "' ";

        }

        $args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? $this->primary_key : $args['orderby'];

        $cache_key = ( true === $count ) ? md5( 'slwp_athletes_count' . serialize( $args ) ) : md5( 'slwp_athletes_' . serialize( $args ) );

        $results = wp_cache_get( $cache_key, 'athletes' );

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

            wp_cache_set( $cache_key, $results, 'athletes', 3600 );

        }
        
        // if we have a singular result, we return it as just the object -- applys to athlete id only.
        if (1 == count($results) && 0 != $args['athlete_id']) {
            $results = $results[0];
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
        return $this->get_athletes( $args, true );
    }

    /**
     * Create the table
     *
     * @access  public
     * @since   0.1.0
     */
    public function create_table() {}
}
