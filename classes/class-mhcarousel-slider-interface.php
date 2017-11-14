<?php

/**
 * Handling the slider interface functionality of the plugin.
 *
 * - Hide Page Links to in Slider posts
 * - Add Custom Post Title text for Slider CPT
 */

class Mhcarousel_Slider_Interface {

	public function __construct() {
		add_filter( 'page-links-to-post-types', array( $this, 'remove_plt_from_slider' ) );
		add_filter( 'enter_title_here', array( $this, 'slider_title_text' ) );
	}

	//
	// - Hide Page Links to in Slider posts
	//
	public function remove_plt_from_slider( $post_types ) {
		$key = array_search( 'slider', $post_types );
		if ( false !== $key ) {
			unset( $post_types[ $key ] );
		}
		return $post_types;
	}

	//
	// Custom Post Title text for Slider CPT
	//
	public function slider_title_text( $title ) {
		$screen = get_current_screen();
		if ( 'slider' == $screen->post_type ) {
			$title = 'Name of Slide';
		}
		return $title;
	}
}

$mhcarousel_class_slider = new Mhcarousel_Slider_Interface();
