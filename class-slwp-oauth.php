<?php

class SLWP_Oauth {

    public function __construct() {}

    public function authorize_url() {
        return $this->get_authorize_url();
    }

    private function get_authorize_url() {
        $url = 'https://www.strava.com/api/v3/oauth/authorize';
        $redirect_uri = esc_url( home_url( '/slwp/stravaAuth' ) );
        $params =
            '?client_id=' . get_slwp_client_id()
            . '&redirect_uri=' . $redirect_uri
            . '&response_type=code'
            . '&approval_prompt=force'
            . '&scope=read';

        $authorization_url = $url . $params;

        return $authorization_url;
    }

    public function validate_app() {
        $message = array(
            'action' => 'error',
            'message' => 'There was an error.',
        );

        if ( isset( $_GET['error'] ) && 'access_denied' == $_GET['error'] ) {
            $message['action'] = 'error';
            $message['message'] = 'Access denied.';
        }

        if ( isset( $_GET['code'] ) && '' != $_GET['code'] ) {
            return $this->token_exchange( $_GET['code'] );
        }

        return $message;
    }

    private function token_exchange( $code = '' ) {
        global $wpdb;

        $return = array();
        $token_url = 'https://www.strava.com/api/v3/oauth/token';
        $client_secret = get_slwp_client_secret();
        $params =
            'client_id=' . get_slwp_client_id()
            . '&client_secret=' . $client_secret
            . '&code=' . $code
            . '&grant_type=authorization_code';

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

        // store data
        $data = array(
            'athlete_id' => $response['athlete']['id'],
            'scope' => 'read',
            'expires_at' => $response['expires_at'],
            'access_token' => $response['access_token'],
        );

        $wpdb->insert( 'slwp_tokens_sl', $data );

        // update tokens_refresh
         $data = array(
             'athlete_id' => $response['athlete']['id'],
             'scope' => 'read',
             'refresh_token' => $response['refresh_token'],
         );

         $wpdb->insert( 'slwp_tokens_refresh', $data );

         $return['action'] = 'success';
         $return['message'] = 'User authorized!';

         return $return;
    }

}
