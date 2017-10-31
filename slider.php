<?php
/*
Plugin Name: Mayflower Featured Slider Plugin
Description: Add featured slider elements to Mayflower sites.
Version: 0.1
License: GPL
Author: Bellevue College DevCom
Author URI:
*/


########################################
## - Hide Page Links to in Slider posts
########################################


add_filter( 'page-links-to-post-types', 'mhcarousel_remove_plt_from_slider' );

function mhcarousel_remove_plt_from_slider( $post_types )
{
    $key = array_search( 'slider',  $post_types );
    if( $key !== false ) {
        unset($post_types[$key]);
    }

    return $post_types;
}

///////////////////////////////////////
// - Setup Slider Custom Post type - //
///////////////////////////////////////


add_action('init', 'mhcarousel_bc_slider_register');

function mhcarousel_bc_slider_register() {
		$labels = array(
			'name' => 'Featured Slider',
			'singular_name' => 'Slide', 
			'add_new' => 'Add New', 'Slide',
			'add_new_item' => 'Add New Slide',
			'edit_item' => 'Edit Slide',
			'new_item' => 'New Slide',
			'all_items' => 'Slide List',
			'view_item' => 'View Slide',
			'search_items' => 'Search Slides',
			'not_found' =>  'No Slides found',
			'not_found_in_trash' => 'No Slides found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'Featured Slider'
		);

        $args = array(
		    'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'hierarchical' => true,
            'has_archive' =>true,
            'rewrite' => true,
			'menu_position' => 4,
            'supports' => array('title', 'editor', 'thumbnail', 'category', 'author', 'revisions', /*'page-attributes',*/ 'author', 'comments'),
			'taxonomies' => array(/*'category', 'post_tag',*/) // this is IMPORTANT
           );

		register_post_type( 'slider' , $args );
	}
	
add_shortcode('mayflower-carousel', 'mhcarousel_bc_slider_register');

///////////////////////////////////////
// - Add a sub menu to the featured slider menu
///////////////////////////////////////

add_action( 'admin_menu', 'mhcarousel_register_slider_sort_page' );

function mhcarousel_register_slider_sort_page() {
	add_submenu_page(
		'edit.php?post_type=slider',
		'Order Slides',
		'Re-Order',
		'edit_pages', 
		'slider-order',
		'slider_order_page'
	);
}


///////////////////////////////////////
// - Create an interface showing each slide with a handle to sort
///////////////////////////////////////

function mhcarousel_slider_order_page() {
?>
	<div class="wrap">
		<h2>Sort Slides</h2>
		<p>Simply drag the slide up or down and it will be saved in that order.</p>
	<?php $slides = new WP_Query( array( 'post_type' => 'slider', 'posts_per_page' => -1, 'order' => 'ASC', 'orderby' => 'menu_order' ) ); ?>
	<?php if( $slides->have_posts() ) : ?>

		<table class="wp-list-table widefat fixed posts" id="sortable-table">
			<thead>
				<tr>
					<th class="column-order">Re-Order</th>
					<th class="column-thumbnail">Thumbnail</th>
					<th class="column-title">Title</th>
					<!-- <th class="column-title">Details</th> -->
				</tr>
			</thead>
			<tbody data-post-type="slider">
			<?php while( $slides->have_posts() ) : $slides->the_post(); ?>
				<tr id="post-<?php the_ID(); ?>">
					<td class="column-order"><img src="<?php echo get_template_directory_uri() . '/img/row-move.png'; ?>" title="" alt="Change Order" width="16" height="16" class="" aria-dropeffect="move" /></td>
					<td class="thumbnail column-thumbnail">
						<div class="item active">
							<div class="img-wrapper">
								<?php the_post_thumbnail( 'sort-screen-thumbnail' ); ?>



							</div><!-- img-wrapper -->
						</div><!-- item active -->
					</td>
					<td class="column-title"><strong><?php the_title(); ?></strong></td>
					<!-- <td class="column-details"><div class="excerpt"><?php the_excerpt(); ?></div></td> -->
				</tr>
			<?php endwhile; ?>
			</tbody>
			<tfoot>
				<tr>
					<th class="column-order">Order</th>
					<th class="column-thumbnail">Thumbnail</th>
					<th class="column-title">Title</th>
					<!-- <th class="column-title">Details</th> -->
				</tr>
			</tfoot>

		</table>

	<?php else: ?>

		<p>No slides found, why not <a href="post-new.php?post_type=slider">create one?</a></p>

	<?php endif; ?>
	<?php wp_reset_postdata(); // Don't forget to reset again! ?>

	<style>
		/* Dodgy CSS ^_^ */
		#sortable-table td { background: white; }
		#sortable-table .column-order { padding: 3px 10px; width: 60px; }
		#sortable-table .column-order img { cursor: move; }
		#sortable-table td.column-order { vertical-align: middle; text-align: center; }
		#sortable-table .column-thumbnail { width: auto; }
		#sortable-table tbody tr.ui-state-highlight {
		height:202px;
		width: 100%;
	    background:white !important;
	    -webkit-box-shadow: inset 0px 1px 2px 1px rgba(0, 0, 0, 0.1);
	    -moz-box-shadow: inset 0px 1px 2px 1px rgba(0, 0, 0, 0.1);
	    box-shadow: inset 0px 1px 2px 1px rgba(0, 0, 0, 0.1);
	    }
	</style>
	</div><!-- .wrap -->

<?php

}


///////////////////////////////////////
// - Create an interface showing each slide with a handle to sort
///////////////////////////////////////

add_action( 'admin_enqueue_scripts', 'mhcarousel_slider_enqueue_scripts' );

function mhcarousel_slider_enqueue_scripts() {
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'mayflower-admin-scripts', get_template_directory_uri() . '/js/sorting-v2.js' );
}


###########################
// - Remove Title & WYSIWYG Editor from Slider Post type
// - http://codex.wordpress.org/Function_Reference/remove_post_type_support

//	add_action('init', 'remove_slider_meta');

//	function remove_slider_meta() {
		//remove_post_type_support( 'slider', 'title' );
		//remove_post_type_support( 'slider', 'editor' );
//	}

//

////////////////////////////////////////////////////
// Remove Unncessary Meta Boxes on Slider Admin Screen
/////////////////////////////////////////////////////


if (is_admin()) :
function mhcarousel_slider_remove_meta_boxes() {
  remove_meta_box('categorydiv', 'slider', 'normal');
  remove_meta_box('tagsdiv-post_tag', 'slider', 'normal');
  remove_meta_box('authordiv', 'slider', 'normal');
  remove_meta_box('commentstatusdiv', 'slider', 'normal');
  remove_meta_box('commentsdiv', 'slider', 'normal');
  remove_meta_box('revisionsdiv', 'slider', 'normal');
}
add_action( 'admin_menu', 'slider_remove_meta_boxes' );
endif;

/////////////////////////////////////////
// Custom Post Title text for Slider CPT
/////////////////////////////////////////

function mhcarousel_slider_title_text( $title ){
$screen = get_current_screen();
if ( 'slider' == $screen->post_type ) {
$title = 'Name of Slide';
}
return $title;
}

add_filter( 'enter_title_here', 'mhcarousel_slider_title_text' );


///////////////////////////////////////
// Custom Columns for Slider Post type
///////////////////////////////////////

// Add to admin_init function
add_filter('manage_edit-slider_columns', 'mhcarousel_add_slider_columns');

function mhcarousel_add_slider_columns($slider_columns) {
	$slider_columns = array (
		'cb' => '<input type="checkbox" />',
		'slider-thumbnail' => 'Featured Image',
		'title' => 'Title',
		'slider_link_to' => 'External URL',
	);
	//remove unwanted default columns
	unset($slider_columns['author']);
	unset($slider_columns['comments']);

	return $slider_columns;
}

// Add to admin_init function
add_action('manage_slider_posts_custom_column', 'mhcarousel_manage_slider_columns', 10, 2);

function mhcarousel_manage_slider_columns($column, $post_id) {
	global $post;

	switch( $column ) {

		case 'slider-thumbnail':
			echo get_the_post_thumbnail( $post->ID, 'sort-screen-thumbnail' );
			break;
		case 'slider_link_to':
			/* Get the post meta. */
			$slider_ext_url = get_post_meta( $post->ID, '_slider_url', true );
			echo $slider_ext_url;
			break;
		default:

	} //end switch

} //end function

add_action('admin_head', 'mhcarousel_slider_custom_styles');
function mhcarousel_slider_custom_styles() {
	$output_css = '<style type="text/css">
		.column-slider-thumbnail {
			width: 300px;
		}
	</style>';
	echo $output_css;
}

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'mhcarousel_add_slider_ext_url_mb' );
add_action( 'load-post-new.php', 'mhcarousel_add_slider_ext_url_mb' );


///////////////////////////////////////
// Add Meta Box for External URL
///////////////////////////////////////

// Add the Meta Box
function mhcarousel_add_slider_ext_url_mb() {
    add_meta_box(
		'slider_external_url', // $id
		'Slide URL', // $title
		'show_slider_ext_url', // $callback
		'slider', // $page
		'normal', // $context
		'high'); // $priority
}
add_action('add_meta_boxes', 'mhcarousel_add_slider_ext_url_mb');

// Field Array
$prefix = '_slider_';
$slider_custom_meta_fields = array(
	array(
		'label'=> 'Slide URL',
		'desc'	=> 'Enter the URL associated with this ad.',
		'id'	=> $prefix.'url',
		'type'	=> 'url'
	),
);

// The Callback
function mhcarousel_show_slider_ext_url() {
global $slider_custom_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($slider_custom_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
                switch($field['type']) {
                    // case items will go here
					case 'url':
					    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="' . esc_url($meta) . '" size="30" class="widefat" placeholder="https://" />
					        <br /><span class="description">'.$field['desc'].'</span>';
					break;

                } //end switch
        echo '</td></tr>';
    } // end foreach
    echo '</table>'; // end table
}

// Save the Data
function mhcarousel_save_slider_custom_meta($post_id) {
    global $slider_custom_meta_fields;

	// verify nonce
	if ( !isset( $_POST['custom_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
	}
	// loop through fields and save the data
	foreach ($slider_custom_meta_fields as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	} // end foreach
}
add_action('save_post', 'mhcarousel_save_slider_custom_meta');
