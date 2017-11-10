<?php

/**
 * Handling the slider interface functionality of the plugin.
 *
 * - Hide Page Links to in Slider posts
 * - Create an interface showing each slide with a handle to sort
 * - Add Custom Post Title text for Slider CPT
 *
 */

class Slider_Interface {

    function __construct() {
        add_filter( 'page-links-to-post-types', 'remove_plt_from_slider' );
        add_action( 'admin_enqueue_scripts', 'slider_enqueue_scripts' );
        add_filter( 'enter_title_here', 'slider_title_text' );
    }

    function remove_plt_from_slider( $post_types ) {
        ///////////////////////////////////////
        // - Hide Page Links to in Slider posts
        ///////////////////////////////////////
        $key = array_search( 'slider',  $post_types );
        if( $key !== false ) {
            unset($post_types[$key]);
        }
        return $post_types;
    }

    function slider_enqueue_scripts() {
        ///////////////////////////////////////
        // - Create an interface showing each slide with a handle to sort
        ///////////////////////////////////////
	    wp_enqueue_script( 'jquery-ui-sortable' );
	    wp_enqueue_script( 'mayflower-admin-scripts', get_template_directory_uri() . '/js/sorting-v2.js' );
    }

    function slider_title_text( $title ){
        /////////////////////////////////////////
        // Custom Post Title text for Slider CPT
        /////////////////////////////////////////
        $screen = get_current_screen();
        if ( 'slider' == $screen->post_type ) {
            $title = 'Name of Slide';
        }
        return $title;
    }    
}