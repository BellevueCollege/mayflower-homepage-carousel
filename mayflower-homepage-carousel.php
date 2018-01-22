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
			'name'               => 'Featured Slider',
			'singular_name'      => 'Slide',
			'add_new'            => 'Add New',
			'Slide',
			'add_new_item'       => 'Add New Slide',
			'edit_item'          => 'Edit Slide',
			'new_item'           => 'New Slide',
			'all_items'          => 'Slide List',
			'view_item'          => 'View Slide',
			'search_items'       => 'Search Slides',
			'not_found'          => 'No Slides found',
			'not_found_in_trash' => 'No Slides found in Trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Featured Slider',
		);

		$args = array(
			'labels'        => $labels,
			'public'        => true,
			'show_ui'       => true,
			'hierarchical'  => true,
			'has_archive'   => true,
			'rewrite'       => true,
			'menu_position' => 4,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'category', 'author', 'revisions', /*'page-attributes',*/ 'author', 'comments' ),
			'taxonomies'    => array( /*'category', 'post_tag',*/ ), // this is IMPORTANT
		);

		register_post_type( 'mhcarousel', $args );
	}

	public function image_sizes() {
		add_image_size( 'mhcarousel-featured-size', 1680, 350, true );
		add_image_size( 'mhcarousel-thumbnail-size', 300, 125, true );
	}
}

$mhcarousel = new Mayflower_Homepage_Carousel();
