<?php

/**
 * SLWP_Shortcode_Leaderboards class.
 */
class SLWP_Shortcode_Leaderboards {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_shortcode( 'slwp_leaderboards', array( $this, 'shortcode' ) );
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
            'slwp_leaderboards'
        );
        $html = '';
        $leaderboards = get_posts( array(
            'posts_per_page' => -1,
            'post_type' => 'leaderboard',    
        ) );
        
        if (empty($leaderboards))
            return $html;
            
        $html .= '<h3>Leaderboards</h3>';
            
        $html .= '<ul>';
            foreach ($leaderboards as $leaderboard) :
                $html .= '<li><a href="'.get_permalink( $leaderboard->ID ).'">'.$leaderboard->post_title.'</a></li>';
            endforeach;
        $html .= '</ul>';

        return apply_filters( 'slwp_leaderboards', $html, $html );
    }

}

new SLWP_Shortcode_Leaderboards();
