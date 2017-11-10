<?php

/**
 * Handling the save data functionality of the plugin.
 *
 * - Save data
 * - Get and update post meta
 *
 */

class Save_Data {
    function __construct(){
        add_action('save_post', 'save_slider_custom_meta');
    }
        
    function save_slider_custom_meta($post_id) {
        ////////////////////
        // Save the data
        ////////////////////
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
}