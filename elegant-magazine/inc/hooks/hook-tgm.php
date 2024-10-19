<?php
/**
 * Recommended plugins
 *
 * @package Surya_Chandra
 */

if ( ! function_exists( 'elegant_magazine_recommended_plugins' ) ) :

    /**
     * Recommend plugins.
     *
     * @since 1.0.0
     */
    function elegant_magazine_recommended_plugins() {

        $plugins = array(
            array(
                'name'     => esc_html__( 'AF Companion', 'elegant-magazine' ),
                'slug'     => 'af-companion',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'Templatespare', 'elegant-magazine' ),
                'slug'     => 'templatespare',
                'required' => false,
            
            ),
            array(
                'name'     => esc_html__( 'Elespare', 'elegant-magazine' ),
                'slug'     => 'elespare',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'Blockspare', 'elegant-magazine' ),
                'slug'     => 'blockspare',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'Latest Posts Block', 'elegant-magazine' ),
                'slug'     => 'latest-posts-block-lite',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'Magic Content Box', 'elegant-magazine' ),
                'slug'     => 'magic-content-box-lite',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'WP Post Author', 'elegant-magazine' ),
                'slug'     => 'wp-post-author',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'Frontend Post Submission', 'elegant-magazine' ),
                'slug'     => 'frontend-post-submission-manager-lite',
                'required' => false,
            ),
            array(
                'name'     => esc_html__( 'Free Live Chat using 3CX', 'elegant-magazine' ),
                'slug'     => 'wp-live-chat-support',
                'required' => false,
            )
        );

        tgmpa( $plugins );

    }

endif;

add_action( 'tgmpa_register', 'elegant_magazine_recommended_plugins' );
