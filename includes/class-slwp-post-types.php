<?php

/**
 * Post SLWP_Post_Types Class.
 */
class SLWP_Post_Types {

    public function __construct() {
        add_action( 'init', array( $this, 'register_post_types' ), 0 );
        add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
        add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
    }

    /**
     * Register core post types.
     */
    public function register_post_types() {
        do_action( 'slwp_register_post_type' );

        register_post_type(
            'leaderboard',
            array(
                'labels'                => array(
                    'name'                  => __( 'Leaderboards', 'slwp' ),
                    'singular_name'         => __( 'Leaderboard', 'slwp' ),
                    'all_items'             => __( 'All Leaderboards', 'slwp' ),
                    'archives'              => __( 'Leaderboard Archives', 'slwp' ),
                    'attributes'            => __( 'Leaderboard Attributes', 'slwp' ),
                    'insert_into_item'      => __( 'Insert into leaderboard', 'slwp' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this leaderboard', 'slwp' ),
                    'featured_image'        => _x( 'Featured Image', 'leaderboard', 'slwp' ),
                    'set_featured_image'    => _x( 'Set featured image', 'leaderboard', 'slwp' ),
                    'remove_featured_image' => _x( 'Remove featured image', 'leaderboard', 'slwp' ),
                    'use_featured_image'    => _x( 'Use as featured image', 'leaderboard', 'slwp' ),
                    'filter_items_list'     => __( 'Filter leaderboards list', 'slwp' ),
                    'items_list_navigation' => __( 'Leaderboards list navigation', 'slwp' ),
                    'items_list'            => __( 'Leaderboards list', 'slwp' ),
                    'new_item'              => __( 'New Leaderboard', 'slwp' ),
                    'add_new'               => __( 'Add New', 'slwp' ),
                    'add_new_item'          => __( 'Add New Leaderboard', 'slwp' ),
                    'edit_item'             => __( 'Edit Leaderboard', 'slwp' ),
                    'view_item'             => __( 'View Leaderboard', 'slwp' ),
                    'view_items'            => __( 'View Leaderboards', 'slwp' ),
                    'search_items'          => __( 'Search leaderboards', 'slwp' ),
                    'not_found'             => __( 'No leaderboards found', 'slwp' ),
                    'not_found_in_trash'    => __( 'No leaderboards found in trash', 'slwp' ),
                    'parent_item_colon'     => __( 'Parent Leaderboard:', 'slwp' ),
                    'menu_name'             => __( 'Leaderboards', 'slwp' ),
                ),
                'public'                => true,
                'hierarchical'          => false,
                'show_ui'               => true,
                'show_in_nav_menus'     => true,
                'supports'              => array( 'title', 'editor' ),
                'has_archive'           => true,
                'rewrite'               => true,
                'query_var'             => true,
                'menu_position'         => 90,
                'menu_icon'             => 'dashicons-clipboard',
                'show_in_rest'          => true,
                'rest_base'             => 'leaderboard',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
            )
        );

        do_action( 'slwp_after_register_post_type' );
    }

    /**
     * Sets the post updated messages for the `leaderboard` post type.
     *
     * @param  array $messages Post updated messages.
     * @return array Messages for the `leaderboard` post type.
     */
    public function updated_messages( $messages ) {
        global $post;

        $permalink = get_permalink( $post );

        $messages['leaderboard'] = array(
            0  => '', // Unused. Messages start at index 1.
            /* translators: %s: post permalink */
            1  => sprintf( __( 'Leaderboard updated. <a target="_blank" href="%s">View leaderboard</a>', 'slwp' ), esc_url( $permalink ) ),
            2  => __( 'Custom field updated.', 'slwp' ),
            3  => __( 'Custom field deleted.', 'slwp' ),
            4  => __( 'Leaderboard updated.', 'slwp' ),
            /* translators: %s: date and time of the revision */
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Leaderboard restored to revision from %s', 'slwp' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            /* translators: %s: post permalink */
            6  => sprintf( __( 'Leaderboard published. <a href="%s">View leaderboard</a>', 'slwp' ), esc_url( $permalink ) ),
            7  => __( 'Leaderboard saved.', 'slwp' ),
            /* translators: %s: post permalink */
            8  => sprintf( __( 'Leaderboard submitted. <a target="_blank" href="%s">Preview leaderboard</a>', 'slwp' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
            /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
            9  => sprintf( __( 'Leaderboard scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview leaderboard</a>', 'slwp' ), date_i18n( __( 'M j, Y @ G:i', 'slwp' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
            /* translators: %s: post permalink */
            10 => sprintf( __( 'Leaderboard draft updated. <a target="_blank" href="%s">Preview leaderboard</a>', 'slwp' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
        );

        return $messages;
    }


    /**
     * Sets the bulk post updated messages for the `leaderboard` post type.
     *
     * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
     *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
     * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
     * @return array Bulk messages for the `leaderboard` post type.
     */
    public function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
        global $post;

        $bulk_messages['leaderboard'] = array(
            /* translators: %s: Number of leaderboards. */
            'updated'   => _n( '%s leaderboard updated.', '%s leaderboards updated.', $bulk_counts['updated'], 'slwp' ),
            'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 leaderboard not updated, somebody is editing it.', 'slwp' ) :
                            /* translators: %s: Number of leaderboards. */
                            _n( '%s leaderboard not updated, somebody is editing it.', '%s leaderboards not updated, somebody is editing them.', $bulk_counts['locked'], 'slwp' ),
            /* translators: %s: Number of leaderboards. */
            'deleted'   => _n( '%s leaderboard permanently deleted.', '%s leaderboards permanently deleted.', $bulk_counts['deleted'], 'slwp' ),
            /* translators: %s: Number of leaderboards. */
            'trashed'   => _n( '%s leaderboard moved to the Trash.', '%s leaderboards moved to the Trash.', $bulk_counts['trashed'], 'slwp' ),
            /* translators: %s: Number of leaderboards. */
            'untrashed' => _n( '%s leaderboard restored from the Trash.', '%s leaderboards restored from the Trash.', $bulk_counts['untrashed'], 'slwp' ),
        );

        return $bulk_messages;
    }

}

new SLWP_Post_Types();
