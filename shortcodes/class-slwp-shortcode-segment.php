<?php
/**
 * SLWP_Shortcode_Segment class.
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * SLWP_Shortcode_Segment class.
 */
class SLWP_Shortcode_Segment {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'slwp_segment', array( $this, 'shortcode' ) );
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
            'slwp_segment'
        );
        // $html = '';

        $api_wrapper = new Swagger\Client\ApiWrapper();
        $emst_template_loader = new SLWP_Template_Loader();

        // App user data.
        $users = new EMST\Users();
        $users->init();
        $users_data = $users->get_users_data();

        // load templates.
        foreach ( $users_data as $user ) { // this should only be once (not based on user)
            $segment = $api_wrapper->get_segment( $user->access_token );

            $args = array(
                'name' => $segment->getName(),
                'distance' => slwp()->format->format_distance( $segment->getDistance() ),
                'avggrade' => slwp()->format->format_grade( $segment->getAverageGrade() ),
                'maxgrade' => slwp()->format->format_grade( $segment->getMaximumGrade() ),
                'elevgain' => slwp()->format->format_distance( $segment->getTotalElevationGain(), 'meters', 'feet' ),
                'category' => slwp()->format->format_climb_cat( $segment->getClimbCategory() ),
                'location' => slwp()->format->format_location( $segment->getCity(), $segment->getState(), $segment->getCountry() ),
            );

            $emst_template_loader->set_template_data( $args );
            $emst_template_loader->get_template_part( 'segment' );
        }

        // return $html;
    }

}

new SLWP_Shortcode_Segment();
