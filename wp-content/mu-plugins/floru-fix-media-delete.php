<?php
/**
 * Plugin Name: Floru – Fix Media Delete
 * Description: Ensures attachments can be permanently deleted from the media library on this local Windows setup.
 *
 * MU-plugin so it always loads regardless of theme status or recovery mode.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_delete-post', 'floru_mu_handle_attachment_delete', 0 );

function floru_mu_handle_attachment_delete() {
    $id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;

    error_log( '[floru-fix] handler called. id=' . $id
        . ' POST=' . wp_json_encode( $_POST )
    );

    if ( ! $id ) {
        return;
    }

    $post = get_post( $id );
    if ( ! $post || 'attachment' !== $post->post_type ) {
        error_log( '[floru-fix] not an attachment or not found, handing off.' );
        return;
    }

    // Log the nonce we received versus what we expect.
    $sent_nonce  = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '(missing)';
    $expected    = 'delete-post_' . $id;
    $nonce_valid = wp_verify_nonce( $sent_nonce, $expected );
    error_log( '[floru-fix] nonce sent=' . $sent_nonce
        . ' action=' . $expected
        . ' valid=' . var_export( $nonce_valid, true )
    );

    if ( ! $nonce_valid ) {
        error_log( '[floru-fix] nonce verification FAILED. Dying -1.' );
        wp_die( -1 );
    }

    $can_delete = current_user_can( 'delete_post', $id );
    error_log( '[floru-fix] current_user_can(delete_post, ' . $id . ')=' . var_export( $can_delete, true )
        . ' user=' . get_current_user_id()
    );

    if ( ! $can_delete ) {
        wp_die( -1 );
    }

    // Force-delete (skip trash).
    $file = get_attached_file( $id );
    error_log( '[floru-fix] about to delete attachment #' . $id . ' file=' . $file );

    $result = wp_delete_attachment( $id, true );

    error_log( '[floru-fix] wp_delete_attachment result=' . var_export( (bool) $result, true ) );

    if ( $result ) {
        // WordPress Backbone.js verwacht een JSON response `{ success: true, data: 1 }` of via wp_send_json_success(1)
        wp_send_json_success( 1 );
    }

    global $wpdb;
    error_log( '[floru-fix] FAILED. db_error=' . ( $wpdb->last_error ?: 'none' ) );
    wp_send_json_error();
}
