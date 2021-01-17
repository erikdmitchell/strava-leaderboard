<?php

/**
 * SLWP_Url_Rewrites class.
 */
class SLWP_Url_Rewrites {

    /**
     * Construct class.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        add_action( 'wp_loaded', array( $this, 'flush_rules' ) );
        add_filter( 'generate_rewrite_rules', array( $this, 'generate_rewrite_rules' ) );
        add_filter( 'query_vars', array( $this, 'query_vars' ) );
    }

    public function generate_rewrite_rules( $wp_rewrite ) {
        // Strava OAuth callback URL.
        $wp_rewrite->rules = array_merge(
            array( 'slwp/stravaAuth/?$' => 'index.php?custom=1' ),
            $wp_rewrite->rules
        );

        // Strava Webhooks callback URL.
        $wp_rewrite->rules = array_merge(
            array( 'slwp/stravaWebhooks/?$' => 'index.php?webhook=1' ),
            $wp_rewrite->rules
        );

        $wp_rewrite->rules = array_merge(
            array( 'slwp/stravaWebhooksJ/?$' => 'index.php?webhookj=1' ),
            $wp_rewrite->rules
        );
    }

    public function query_vars( $query_vars ) {
        $query_vars[] = 'custom';
        $query_vars[] = 'webhook'; // webhooks.
        $query_vars[] = 'webhookj';

        return $query_vars;
    }

    public function template_redirect() {
        $custom = intval( get_query_var( 'custom' ) );
        $webhook = intval( get_query_var( 'webhook' ) );
        $webhookj = intval( get_query_var( 'webhookj' ) );
        
        // Strava OAuth.
        if ( $custom ) {
            $oauth = new SLWP_Oauth();
            $message = $oauth->validate_app();

            $message_html = '<div class="validate-app ' . $message['action'] . '">' . $message['message'] . '</div>';

            $message = apply_filters( 'slwp_auth_message', $$message_html, $message );

            wp_redirect( home_url() . '?message=' . urlencode_deep( $message ) );
            die;
        } 
        
        // Strava Webhooks
        if ( $webhook ) {
slwp_log('url rewrite: webhook');            
            slwp()->webhooks->validation();                     
            die;
        }

        if ( $webhookj ) {
slwp_log('url rewrite: webhookj');            
            slwp()->webhooks->json_validate();                     
            die;
        }
    }

    /**
     * Flushes rewrites if our project rule isn't yet added.
     */
    function flush_rules() {
        $rules = get_option( 'rewrite_rules' );

        if ( ! isset( $rules['slwp/stravaAuth/?$'] ) || ! isset( $rules['slwp/stravaWebhooks/?$'] ) || ! isset( $rules['slwp/stravaWebhooksJ/?$'] ) ) {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }
    }

}

new SLWP_Url_Rewrites();
