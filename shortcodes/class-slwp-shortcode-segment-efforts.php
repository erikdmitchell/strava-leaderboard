<?php
/**
 * SLWP_Shortcode_Segment_Efforts class.
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * SLWP_Shortcode_Segment_Efforts class.
 */
class SLWP_Shortcode_Segment_Efforts {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'slwp_segment_efforts', array( $this, 'shortcode' ) );
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
            'slwp_segment_efforts'
        );
        // $html = '';

        $api_wrapper = new Swagger\Client\ApiWrapper();
        $emst_template_loader = new SLWP_Template_Loader();

        // App user data.
        $users = new SLWP_Users();
        $users->init();
        $users_data = $users->get_users_data();

        foreach ( $users_data as $user ) {
            $efforts = $api_wrapper->get_segment_efforts( $user->access_token ); // this needs to be a loop

            foreach ( $efforts as $effort ) :
                $args = array(
                    'time' => slwp()->format->format_time( $effort->getElapsedTime() ),
                    'iskom' => slwp()->format->is_kom( $effort->getIsKom() ),
                    'date' => slwp()->format->format_date( $effort->getStartDate() ),
                    'activityurl' => $api_wrapper->get_activity_url_by_id( $effort->getActivity() ),
                    'komrank' => slwp()->format->kom_rank( $effort->getKomRank() ),
                    'prrank' => slwp()->format->pr_rank( $effort->getPrRank() ),
                );

                $emst_template_loader->set_template_data( $args );
                $emst_template_loader->get_template_part( 'segment-efforts' );
            endforeach;
        }

        // return $html;
    }

}

new SLWP_Shortcode_Segment_Efforts();
