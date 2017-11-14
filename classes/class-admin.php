<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * - Add a sub menu to the featured slider menu
 * - Create interface showing each slide with a handle to sort
 * - Remove Unncessary Meta Boxes on Slider Admin Screen
 * - Create custom columns for slider post type
 * - Create custom columns for slider posts
 * - Add custom styles to columns
 */

class Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_slider_sort_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'slider_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'slider_remove_meta_boxes' ) );
		add_filter( 'manage_edit-slider_columns', array( $this, 'add_slider_columns' ) );
		add_action( 'manage_slider_posts_custom_column', array( $this, 'manage_slider_columns' ), 10, 2 );
		add_action( 'admin_head', array( $this, 'slider_custom_styles' ) );
	}

	//
	// - Add a sub menu to the featured slider menu
	//
	public function register_slider_sort_page() {
		add_submenu_page(
			'edit.php?post_type=slider',
			'Order Slides',
			'Re-Order',
			'edit_pages',
			'slider-order',
			array( $this, 'slider_order_page' )
		);
	}

	//
	// - Create an interface showing each slide with a handle to sort
	//
	// The callback to register_slider_sort_page()
	public function slider_order_page() {
		?>
		<div class="wrap">
		<h2>Sort Slides</h2>
		<p>Simply drag the slide up or down and it will be saved in that order.</p>
		<?php
		$slides = new WP_Query(
			array(
				'post_type'      => 'slider',
				'posts_per_page' => -1,
				'order'          => 'ASC',
				'orderby'        => 'menu_order',
			)
		);
		?>
		<?php if ( $slides->have_posts() ) : ?>
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
				<?php
				while ( $slides->have_posts() ) :
					$slides->the_post();
				?>
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

		<?php else : ?>
			<p>No slides found, why not <a href="post-new.php?post_type=slider">create one?</a></p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?> <!-- Don't forget to reset again! -->

		</div> <!-- .wrap -->
		<?php
	}

	//
	// - Create an interface showing each slide with a handle to sort
	//
	public function slider_enqueue_scripts( $hook ) {
		if ( 'slider_page_slider-order' === $hook ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'mayflower-admin-scripts', plugin_dir_url( dirname( __FILE__ ) ) . 'js/sorting-v2.js' );
			wp_enqueue_style( 'mayflower-style-scripts', plugin_dir_url( dirname( __FILE__ ) ) . 'css/sorting.css' );
		}
	}

	//
	// Remove Unncessary Meta Boxes on Slider Admin Screen
	//
	public function slider_remove_meta_boxes() {
		if ( is_admin() ) {
			remove_meta_box( 'categorydiv', 'slider', 'normal' );
			remove_meta_box( 'tagsdiv-post_tag', 'slider', 'normal' );
			remove_meta_box( 'authordiv', 'slider', 'normal' );
			remove_meta_box( 'commentstatusdiv', 'slider', 'normal' );
			remove_meta_box( 'commentsdiv', 'slider', 'normal' );
			remove_meta_box( 'revisionsdiv', 'slider', 'normal' );
		}
	}

	//
	// Custom Columns for Slider Post type
	//
	public function add_slider_columns( $slider_columns ) {
		$slider_columns = array(
			'cb'               => '<input type="checkbox" />',
			'slider-thumbnail' => 'Featured Image',
			'title'            => 'Title',
			'slider_link_to'   => 'External URL',
		);
		// remove unwanted default columns
		unset( $slider_columns['author'] );
		unset( $slider_columns['comments'] );

		return $slider_columns;
	}

	//
	// Custom Columns for Slider Posts
	//
	public function manage_slider_columns( $column, $post_id ) {
		global $post;

		switch ( $column ) {
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

	//
	// Custom Styles for Columns
	//
	public function slider_custom_styles() {
		$output_css = '<style type="text/css">
			.column-slider-thumbnail {
				width: 300px;
			}
		</style>';
		echo $output_css;
	}
}

$mh_class_admin = new Admin();
