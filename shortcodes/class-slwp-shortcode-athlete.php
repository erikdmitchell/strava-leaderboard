<?php
/**
 * SLWP_Shortcode_Athlete class.
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * SLWP_Shortcode_Athlete class.
 */
class SLWP_Shortcode_Athlete {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'slwp_athlete', array( $this, 'shortcode' ) );
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
            'slwp_athlete'
        );
        // $html = '';

        $api_wrapper = new Swagger\Client\ApiWrapper();
        $emst_template_loader = new SLWP_Template_Loader();

        // App user data.
        $users = new SLWP_Users();
        $users->init();
        $users_data = $users->get_users_data();

        // load templates.
        foreach ( $users_data as $user ) {
            $athlete = $api_wrapper->get_athlete( $user->access_token );

            $args = array(
                'profile' => $athlete->getProfileMedium(),
                'firstname' => $athlete->getFirstName(),
                'lastname' => $athlete->getLastname(),
                'location' => slwp()->format->format_location( $athlete->getCity(), $athlete->getState(), $athlete->getCountry() ),
                'gender' => $athlete->getSex(),
            );

            $emst_template_loader->set_template_data( $args );
            $emst_template_loader->get_template_part( 'athlete' );
        }

        // return $html;
    }

}

new SLWP_Shortcode_Athlete();
