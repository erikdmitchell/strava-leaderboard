<?php
/**
 * SLWP class
 *
 * @package slwp
 * @since   0.1.0
 */

/**
 * Final SLWP class.
 *
 * @final
 */
final class SLWP {

    /**
     * Version
     *
     * (default value: '0.1.0')
     *
     * @var string
     * @access public
     */
    public $version = '0.1.0';

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
        $this->define( 'SLWP_VERSION', $this->version );
        $this->define( 'SLWP_PATH', plugin_dir_path( __FILE__ ) );
        $this->define( 'SLWP_URL', plugin_dir_url( __FILE__ ) );
        $this->define( 'SLWP_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
        $this->define( 'SLWP_DB_VERSION', '0.1.0' );
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
        include_once( SLWP_PATH . 'includes/class-slwp-api-format.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-logging.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-users.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-oauth.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-post-types.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-api-wrapper.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-install.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-template-loader.php' );
        include_once( SLWP_PATH . 'includes/class-slwp-url-rewrites.php' );
        include_once( SLWP_PATH . 'includes/cli/class-slwp-cli-dbsync.php' );        
        include_once( SLWP_PATH . 'includes/functions.php' );

        $this->format = new SLWP_Api_Format();
        $this->users = new SLWP_Users();

        // load if in admin.
        if ( is_admin() ) {
            include_once( SLWP_PATH . 'includes/admin/class-slwp-admin.php' );
        }
    }

    /**
     * Init hooks for plugin.
     *
     * @access private
     * @return void
     */
    private function init_hooks() {
        register_activation_hook( SLWP_PLUGIN_FILE, array( 'SLWP_Install', 'install' ) );

        add_action( 'init', array( $this, 'load_includes' ), 0 );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts_styles' ) );
    }

    /**
     * Frontend scripts and styles.
     *
     * @access public
     * @return void
     */
    public function frontend_scripts_styles() {
        wp_enqueue_style( 'slwp-styles', SLWP_ASSETS_URL . 'css/styles.min.css', '', $this->version );
    }

    /**
     * Load includes.
     *
     * @access public
     * @return void
     */
    public function load_includes() {
        $dirs = array( 'includes/shortcodes' );

        foreach ( $dirs as $dir ) :
            foreach ( glob( SLWP_PATH . $dir . '/*.php' ) as $file ) :
                include_once( $file );
            endforeach;
        endforeach;
    }

    /**
     * Add links to plugin action.
     *
     * @access public
     * @param mixed $links array.
     * @return array
     */
    public function plugin_action_links( $links ) {
        $links[] = sprintf( '<a href="%s" target="_blank">%s</a>', 'https://github.com/erikdmitchell/strava-leaderboard', __( 'GitHub', 'SLWP' ) );

        return $links;
    }

}

/**
 * SLWP function.
 *
 * @access public
 * @return instance
 */
function slwp() {
    return SLWP::instance();
}

// Global for backwards compatibility.
$GLOBALS['slwp'] = slwp();
