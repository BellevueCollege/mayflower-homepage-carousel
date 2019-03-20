<?php

/*
Plugin Name: Mayflower Homepage Carousel
Plugin URI: https://github.com/BellevueCollege/mayflower-homepage-carousel
Description: Add featured slider elements to Mayflower sites.
Version: 0.2
Author: Bellevue College DevCom
Author URI: http://www.bellevuecollege.edu/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * The plugin bootstrap file
 *
 * - Generates the plugin information in the plugin admin area.
 * - Includes all of the dependencies used by the plugin
 * - Registers the plugin
 */

// Include classes
require_once plugin_dir_path( __FILE__ ) . 'classes/class-mhcarousel-slider-interface.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/class-mhcarousel-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/class-mhcarousel-meta-boxes.php';

class Mayflower_Homepage_Carousel {

	public function __construct() {
		add_action( 'init', array( $this, 'bc_slider_register' ) );
		add_action( 'after_setup_theme', array( $this, 'image_sizes' ) );
	}

	public function bc_slider_register() {
		$labels = array(
			'name'               => 'Featured Stories (Carousel)',
			'singular_name'      => 'Story',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Story',
			'edit_item'          => 'Edit Story',
			'new_item'           => 'New Story',
			'all_items'          => 'Story List',
			'view_item'          => 'View Story',
			'search_items'       => 'Search Story',
			'not_found'          => 'No Story found',
			'not_found_in_trash' => 'No Story found in Trash',
			'menu_name'          => 'Featured Story',
		);

		$args = array(
			'labels'        => $labels,
			'menu_icon'     => 'dashicons-align-center',
			'public'        => false,
			'show_ui'       => true,
			'hierarchical'  => false,
			'has_archive'   => false,
			'rewrite'       => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'author', 'revisions' ),
			'show_in_rest'  => true,
		);

		register_post_type( 'mhcarousel', $args );
	}

	public function image_sizes() {
		add_image_size( 'mhcarousel-featured-size', 1680, 350, true );
		add_image_size( 'mhcarousel-thumbnail-size', 300, 125, true );
	}
}

$mhcarousel = new Mayflower_Homepage_Carousel();
