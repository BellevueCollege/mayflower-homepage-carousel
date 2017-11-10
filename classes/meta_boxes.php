<?php

/**
 * Handling the meta boxes functionality of the plugin.
 *
 * - Add Meta Box for External URL
 * - Add Meta Fields
 *
 */

class Meta_Boxes {

    function __construct() {
        // Fire our meta box setup function on the post editor screen. 
        add_action( 'load-post.php', 'add_slider_ext_url_mb' );
        add_action( 'load-post-new.php', 'add_slider_ext_url_mb' );
        // Register meta boxes
        add_action( 'add_meta_boxes', 'add_slider_ext_url_mb');
        add_action( 'add_meta_fields', 'add_slider_custom_mf');
    }

    function add_slider_ext_url_mb() {
        ///////////////////////////////////////
        // Add Meta Box for External URL
        ///////////////////////////////////////
        add_meta_box(
            'slider_external_url', // $id
            'Slide URL', // $title
            'show_slider_ext_url', // $callback
            'slider', // $page
            'normal', // $context
            'high'); // $priority
    }

    // The Callback to add_slider_ext_url_mb() 
    function show_slider_ext_url() {
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

    function add_slider_custom_mf(){
        ///////////////////////////////////////
        // Add Meta Fields
        ///////////////////////////////////////
        $prefix = '_slider_';
        $slider_custom_meta_fields = array(
            array(
                'label'=> 'Slide URL',
                'desc'	=> 'Enter the URL associated with this ad.',
                'id'	=> $prefix.'url',
                'type'	=> 'url'
            ),
        );
    }
}