<?php
/**
 * SLWP_Shortcode_Auth class.
 *
 * @package slwp
 * @since   0.1.0
 */

use EMST\Oauth;

/**
 * SLWP_Shortcode_Auth class.
 */
class SLWP_Shortcode_Auth {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'slwp_auth', array( $this, 'shortcode' ) );
    }

    /**
     * Shortcode.
     *
     * @access public
     * @param mixed $atts (array).
     * @return html
     */
    public function shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'text' => 'Authorize App',
            ),
            $atts,
            'slwp_auth'
        );

        $oauth = new Oauth\Auth();
        $url = $oauth->authorize_url();

        $url_html = '<a href="' . $url . '">' . $atts['text'] . '</a>';

        return apply_filters( 'slwp_auth_url', $url_html, $url );
    }

}

new SLWP_Shortcode_Auth();
