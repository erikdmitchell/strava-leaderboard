<?php
/**
 * SLWP_Shortcode_Main class.
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * SLWP_Shortcode_Main class.
 */
class SLWP_Shortcode_Main {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'slwp', array( $this, 'shortcode' ) );
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
            array(),
            $atts,
            'slwp'
        );
        $html = '';

        $html .= 'FooBar';

        return $html;
    }

}

new SLWP_Shortcode_Main();
