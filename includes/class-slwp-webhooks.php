<?php

class SLWP_Webhooks {

    public function __construct() {
        // nothing yet.
    }

    public function create_subscription() {
        $this->request_subscription();
    }

    private function request_subscription() {
        $url = 'https://www.strava.com/api/v3/push_subscriptions';
        $client_id = get_slwp_client_id();
        $client_secret = get_slwp_client_secret();
        // $callback_url = esc_url( home_url( '/slwp/stravaWebhooks' ) );
        $callback_url = esc_url( home_url( '/slwp/stravaWebhooks' ) );
        $verify_token = 'slwplb';

        $params = "client_id=$client_id&client_secret=$client_secret&callback_url=$callback_url&verify_token=$verify_token";
        echo $params . '<br>';
        // . "&redirect_uri=" . urlencode(SF_REDIRECT_URI);

        $curl = curl_init( $url );

        curl_setopt( $curl, CURLOPT_HEADER, false );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );

        $json_response = curl_exec( $curl );

        $status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        print_r( $status );
        if ( $status != 200 ) :
        endif;

        curl_close( $curl );

        $response = json_decode( $json_response, true );
        print_r( $json_response );
    }

    public function validation() {
        echo 'validate webhooks<br>';
    }

    public function validate() {
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

}
