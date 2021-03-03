<?php
/**
 * SLWP admin class
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * SLWP_Admin class.
 */
final class SLWP_Admin {

    /**
     * _instance
     *
     * (default value: null)
     *
     * @var mixed
     * @access protected
     * @static
     */
    protected static $_instance = null;

    /**
     * Instance function.
     *
     * @access public
     * @static
     * @return instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Construct class.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define constants.
     *
     * @access private
     * @return void
     */
    private function define_constants() {
        $this->define( 'SLWP_ADMIN_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'SLWP_ADMIN_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Custom define function.
     *
     * @access private
     * @param mixed $name string.
     * @param mixed $value string.
     * @return void
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Include plugin files.
     *
     * @access public
     * @return void
     */
    public function includes() {

    }

    /**
     * Init hooks for plugin.
     *
     * @access private
     * @return void
     */
    private function init_hooks() {
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts_styles' ) );
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'admin_init', array( $this, 'update_settings' ) );
        add_action( 'init', array( $this, 'init' ), 1 );
        add_action( 'wp_ajax_edit_athlete_lb', array( $this, 'ajax_edit_athlete_lb' ) );
        add_action( 'wp_ajax_slwp_update_athlete_lbs', array( $this, 'ajax_slwp_update_athlete_lbs' ) );
    }

    /**
     * Add page to admin menu.
     *
     * @access public
     * @return void
     */
    public function menu() {
        add_menu_page(
            __( 'Strava Leaderboard', 'slwp' ),
            __( 'Strava Leaderboard', 'slwp' ),
            'manage_options',
            'slwp',
            array( $this, 'page' ),
            SLWP_ASSETS_URL . 'images/strava_symbol_white.png',
            89
        );

        add_submenu_page(
            'slwp',
            __( 'Athletes', 'slwp' ),
            __( 'Athletes', 'slwp' ),
            'manage_options',
            'slwp-athletes',
            array( $this, 'page_athlete' )
        );
    }


    /**
     * Page.
     *
     * @access public
     * @param string $page (default: '')
     * @param array  $args (default: array())
     * @return void
     */
    public function page( $page = '', $args = array() ) {
        if ( isset( $_GET['subpage'] ) ) :
            $this->get_page( $_GET['subpage'], $args );
        elseif ( ! empty( $page ) ) :
            $this->get_page( $page, $args );
        else :
            $this->get_page( 'main', $args );
        endif;
    }

    public function page_athlete() {
        $this->page( 'athlete' );
    }

    /**
     * Gets an admin page.
     *
     * @access private
     * @param string $path (default: '').
     * @param array  $args (default: array()).
     * @return void
     */
    private function get_page( $path = '', $args = array() ) {
        // allow view file name shortcut.
        if ( substr( $path, -4 ) !== '.php' ) {
            $path = SLWP_ABSPATH . "includes/admin/pages/{$path}.php";
        }

        // include.
        if ( file_exists( $path ) ) {
            extract( $args );
            include( $path );
        }
    }

    /**
     * Init function.
     *
     * @access public
     * @return void
     */
    public function init() {}

    /**
     * Include admin scripts and styles.
     *
     * @access public
     * @return void
     */
    public function scripts_styles() {
        wp_enqueue_script( 'slwp-admin-athletes-script', SLWP_URL . 'js/admin-athletes.js', array( 'jquery' ), SLWP_VERSION, true );
        wp_enqueue_style( 'slwp-admin-athletes-style', SLWP_URL . 'css/admin-athletes.css', '', SLWP_VERSION );
    }

    public function update_settings() {
        if ( ! isset( $_POST['update_settings'] ) || ! wp_verify_nonce( $_POST['update_settings'], 'slwp_update_settings' ) ) {
            return;
        }

        if ( ! isset( $_POST['slwp'] ) ) {
            return;
        }

        foreach ( $_POST['slwp'] as $key => $value ) {
            update_option( $key, $value );
        }
    }

    public function ajax_edit_athlete_lb() {
        $athlete_id = intval( $_POST['athlete_id'] );

        $args = array(
            'athlete_id' => $athlete_id,
            'athlete_leaderboards' => slwp_get_athlete_leaderboards_list( $athlete_id ),
            'leaderboards' => slwp_get_leaderboards(),
        );

        echo $this->page( 'athlete-leaderboards-box', $args );

        wp_die();
    }

    public function ajax_slwp_update_athlete_lbs() {
        $athlete_lb_db = new SLWP_DB_Leaderboard_Athletes();
        $form_data = array();
        $leaderboard_ids = slwp_get_leaderboards( array( 'fields' => 'ids' ) );

        parse_str( $_POST['form_data'], $form_data );

        $athlete_id = $form_data['athlete_id'];
        print_r( $form_data['lb'] );
        foreach ( $leaderboard_ids as $leaderboard_id ) {
            if ( isset( $form_data['lb'][ $leaderboard_id ] ) ) {
                echo 'update';
            } else {
                echo 'remove';
            }
            // value is always 1
            /*
            1   4334    41
            2   4334    40
            3   10388744    41
            4   10388744    40
            5   122066  41
            6   122066  40
            7   14719   41
            8   14719   40
            9   23000032    41
            10  23000032    40
            11  1182818 41
            12  1182818 40
            */
        }

        wp_die();
    }
}

/**
 * SLWP Admin function.
 *
 * @access public
 * @return instance
 */
function slwp_admin() {
    return SLWP_Admin::instance();
}

// Global for backwards compatibility.
$GLOBALS['slwp_admin'] = slwp_admin();

