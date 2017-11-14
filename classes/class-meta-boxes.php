<?php

/**
 * Handling the meta boxes functionality of the plugin.
 *
 * - Add Meta Box for External URL
 * - Save Meta Data
 * - Add Meta Fields
 *
 */

class Meta_Boxes {

    private $slider_custom_meta_fields = array(
        array(
            'label'=> 'Slide URL',
            'desc'	=> 'Enter the URL associated with this ad.',
            'id'	=> '_slider_url',
            'type'	=> 'url'
        ),
    );

    public function __construct() {
        // Fire our meta box setup function on the post editor screen. 
        add_action( 'load-post.php', array ($this, 'add_slider_ext_url_mb' ));
        add_action( 'load-post-new.php', array ($this, 'add_slider_ext_url_mb' ));
        // Register meta boxes
        add_action( 'add_meta_boxes', array ($this, 'add_slider_ext_url_mb'));
        add_action('save_post', array ($this, 'save_slider_custom_meta')); 
    }

    ///////////////////////////////////////
    // Add Meta Box for External URL
    ///////////////////////////////////////
    public function add_slider_ext_url_mb() {
        add_meta_box(
            'slider_external_url', // $id
            'Slide URL', // $title
            array($this, 'show_slider_ext_url'), // $callback
            'slider', // $page
            'normal', // $context
            'high'); // $priority
    }

    // The Callback to add_slider_ext_url_mb() 
    public function show_slider_ext_url() {
        global $post;
        // Use nonce for verification
        echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
        // Begin the field table and loop
        echo '<table class="form-table">';
        foreach ($this->slider_custom_meta_fields as $field) {
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

    ////////////////////
    // Save the data
    ////////////////////
    public function save_slider_custom_meta($post_id) {

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
        foreach ($this->slider_custom_meta_fields as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];
            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], $new);
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        } // end foreach
    }
}



$mh_class_meta = new Meta_Boxes();