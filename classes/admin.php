<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * - Add a sub menu to the featured slider menu
 * - Create interface showing each slide with a handle to sort
 * - Create custom columns for slider post type
 * - Create custom columns for slider posts
 * - Add custom styles to columns
 * - Remove Unncessary Meta Boxes on Slider Admin Screen
 *
 */

class Admin {
    
    function __construct(){
        add_action( 'admin_menu', 'register_slider_sort_page' );
        add_filter( 'manage_edit-slider_columns', 'add_slider_columns'); // Add to admin_init function
        add_action( 'manage_slider_posts_custom_column', 'manage_slider_columns', 10, 2); // Add to admin_init function
        add_action( 'admin_head', 'slider_custom_styles');
        add_action( 'manage_slider_meta_boxes', 'register_slider_remove_meta' );
    }

    function register_slider_sort_page() {
        ///////////////////////////////////////
        // - Add a sub menu to the featured slider menu
        ///////////////////////////////////////
        add_submenu_page(
            'edit.php?post_type=slider',
            'Order Slides',
            'Re-Order',
            'edit_pages', 
            'slider-order',
            'slider_order_page'
        );
    }

    //The callback to register_slider_sort_page()
    function slider_order_page() {
        ///////////////////////////////////////
        // - Create an interface showing each slide with a handle to sort
        ///////////////////////////////////////
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

    function add_slider_columns($slider_columns) {
        ///////////////////////////////////////
        // Custom Columns for Slider Post type
        ///////////////////////////////////////
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

    function manage_slider_columns($column, $post_id) {
        ///////////////////////////////////////
        // Custom Columns for Slider Posts
        ///////////////////////////////////////
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

    function slider_custom_styles() {
        ///////////////////////////////////////
        // Custom Styles for Columns
        ///////////////////////////////////////
        $output_css = '<style type="text/css">
            .column-slider-thumbnail {
                width: 300px;
            }
        </style>';
        echo $output_css;
    }

    function register_slider_remove_meta(){
        ////////////////////////////////////////////////////
        // Call slider_remove_meta_boxes if it is an admin page on slider
        /////////////////////////////////////////////////////
        if (is_admin()) :
            $this->slider_remove_meta_boxes();
        endif;
    }

    function slider_remove_meta_boxes() {
        ////////////////////////////////////////////////////
        // Remove Unncessary Meta Boxes on Slider Admin Screen
        /////////////////////////////////////////////////////
        remove_meta_box('categorydiv', 'slider', 'normal');
        remove_meta_box('tagsdiv-post_tag', 'slider', 'normal');
        remove_meta_box('authordiv', 'slider', 'normal');
        remove_meta_box('commentstatusdiv', 'slider', 'normal');
        remove_meta_box('commentsdiv', 'slider', 'normal');
        remove_meta_box('revisionsdiv', 'slider', 'normal');
    }
}


