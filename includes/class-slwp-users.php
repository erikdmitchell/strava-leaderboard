<?php

class SLWP_Users {

    public function __construct() {
        $this->client_id = get_slwp_client_id();
    }

    public function check_users_token() {
        global $wpdb;
//echo "check_users_token<br>";
        $users = $wpdb->get_results( 'SELECT * from slwp_tokens_sl' );
//print_r($users);
        // check tokens.
        foreach ( $users as $user ) :
            $this->check_token( $user );
        endforeach;
    }

    private function check_token( $user = '' ) {
        if ( empty( $user ) ) {
            return 'error';
        }
//$this->refresh_token( $user );
/*
echo "check_token()<br>";
        $current_time = current_time( 'timestamp' );

        if ( $user->expires_at > $current_time ) {
            echo 'use existing token<br>';
        } else {
            echo 'update token<br>';
            $this->refresh_token( $user );
        }
*/
    }

    private function refresh_token( $user = '' ) {
        global $wpdb;

        $refresh_token = $wpdb->get_var( "SELECT refresh_token FROM slwp_tokens_refresh WHERE athlete_id = {$user->athlete_id}" );
        $return = array();
        $token_url = 'https://www.strava.com/api/v3/oauth/token';
        $client_secret = get_slwp_client_secret();
        $params =
            'client_id=' . $this->client_id
            . '&client_secret=' . $client_secret
            . '&grant_type=refresh_token'
            . '&refresh_token=' . $refresh_token;

        $curl = curl_init( $token_url );
        curl_setopt( $curl, CURLOPT_HEADER, false );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );

        $json_response = curl_exec( $curl );

        $status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        curl_close( $curl );

        $response = json_decode( $json_response, true );

        if ( 200 != $status ) {
            $resource = '';
            $code = '';
            $return['action'] = 'error';

            if ( isset( $response['errors']['resource'] ) ) {
                $resource = $response['errors']['resource'];
            }

            if ( isset( $response['errors']['code'] ) ) {
                $code = $response['errors']['code'];
            }

            $return['message'] = $response['message'] . ' for ' . $resource . ' code: ' . $code;

            return $return;
        }

        // update data.
        $data = array(
            'access_token' => $response['access_token'],
            'expires_at' => $response['expires_at'],
        );
        $where = array( 'athlete_id' => $user->athlete_id );

        $wpdb->update( 'slwp_tokens_sl', $data, $where );

        // update tokens_refresh
         $data = array(
             'refresh_token' => $response['refresh_token'],
         );
         $where = array( 'athlete_id' => $user->athlete_id );

         $wpdb->update( 'slwp_tokens_refresh', $data, $where );

         $return['action'] = 'success';
         $return['message'] = 'Token updated!';

         return $return;
    }

    public function get_users_data() {
        global $wpdb;

        return $wpdb->get_results( 'SELECT * from slwp_tokens_sl' );
    }

}
