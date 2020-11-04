<?php

/**
 * Template loader class.
 */
class SLWP_Template_Loader {

    protected $template_path = SLWP_PATH . 'templates/';

    public function __construct() {
        add_filter( 'template_include', array( $this, 'template_loader' ) );
    }

    public function template_loader( $template ) {
        if ( is_embed() ) {
            return $template;
        }

        if ( is_singular( 'leaderboard' ) ) {
            $default_file = 'leaderboard.php';
        } else {
            $default_file = false;
        }

        if ( $default_file ) {
            $template_files = $this->get_template_loader_files( $default_file );
            $template = $this->locate_template( $template_files );
        }

        return $template;
    }

    private function get_template_loader_files( $default_file ) {
        $templates   = apply_filters( 'slwp_template_loader_files', array(), $default_file );

        if ( is_singular( 'leaderboard' ) ) {
            $object = get_queried_object();
            $name_decoded = urldecode( $object->post_name );

            if ( $name_decoded !== $object->post_name ) {
                $templates[] = "leaderboard-{$name_decoded}.php";
            }

            $templates[] = "leaderboard-{$object->post_name}.php";
        }

        $templates[] = $default_file;

        $templates[] = $this->template_path . $default_file;

        return array_unique( $templates );
    }

    public function locate_template( $template_names, $load = false, $require_once = true, $args = array() ) {
        $located = '';
        foreach ( (array) $template_names as $template_name ) {
            if ( ! $template_name ) {
                continue;
            }
            if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
                $located = STYLESHEETPATH . '/' . $template_name;
                break;
            } elseif ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
                $located = TEMPLATEPATH . '/' . $template_name;
                break;
            } elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
                $located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
                break;
            } elseif ( file_exists( $template_name ) ) {
                $located = $template_name;
                break;
            }
        }

        if ( $load && '' !== $located ) {
            load_template( $located, $require_once, $args );
        }

        return $located;
    }

}

new SLWP_Template_Loader();
