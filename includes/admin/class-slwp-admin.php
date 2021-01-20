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
     * @param string $page (default: '').
     * @return void
     */
    public function page( $page = '' ) {
        if ( isset( $_GET['subpage'] ) ) :
            $this->get_page( $_GET['subpage'] );
        elseif ( ! empty( $page ) ) :
            $this->get_page( $page );
        else :
            $this->get_page( 'main' );
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
            $path = SLWP_PATH . "includes/admin/pages/{$path}.php";
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

