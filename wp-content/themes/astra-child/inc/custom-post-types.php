<?php
/**
 * Floru Custom Post Types
 * Registers Team Members and Clients post types.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'floru_register_post_types' );
function floru_register_post_types() {

    // Team Members
    register_post_type( 'floru_team', array(
        'labels' => array(
            'name'               => 'Team Members',
            'singular_name'      => 'Team Member',
            'add_new'            => 'Add Team Member',
            'add_new_item'       => 'Add New Team Member',
            'edit_item'          => 'Edit Team Member',
            'new_item'           => 'New Team Member',
            'view_item'          => 'View Team Member',
            'search_items'       => 'Search Team Members',
            'not_found'          => 'No team members found',
            'not_found_in_trash' => 'No team members found in Trash',
            'menu_name'          => 'Team Members',
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-groups',
        'menu_position'=> 25,
        'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'excerpt' ),
        'has_archive'  => false,
        'rewrite'      => false,
    ) );

    // Clients / References
    register_post_type( 'floru_client', array(
        'labels' => array(
            'name'               => 'Clients',
            'singular_name'      => 'Client',
            'add_new'            => 'Add Client',
            'add_new_item'       => 'Add New Client',
            'edit_item'          => 'Edit Client',
            'new_item'           => 'New Client',
            'view_item'          => 'View Client',
            'search_items'       => 'Search Clients',
            'not_found'          => 'No clients found',
            'not_found_in_trash' => 'No clients found in Trash',
            'menu_name'          => 'Clients',
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-building',
        'menu_position'=> 26,
        'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
        'has_archive'  => false,
        'rewrite'      => false,
    ) );
}

/**
 * Team Member meta boxes: Role, Profile Link
 */
add_action( 'add_meta_boxes', 'floru_team_meta_boxes' );
function floru_team_meta_boxes() {
    // Remove default boxes we're replacing with our own UI.
    remove_meta_box( 'postimagediv', 'floru_team', 'side' );
    remove_meta_box( 'postexcerpt', 'floru_team', 'normal' );
    remove_meta_box( 'pageparentdiv', 'floru_team', 'side' );

    add_meta_box(
        'floru_team_details',
        'LinkedIn & Display Order',
        'floru_team_details_render',
        'floru_team',
        'normal',
        'default'
    );
}

/**
 * Disable Gutenberg for Team Members — classic editor is cleaner for this CPT.
 */
add_filter( 'use_block_editor_for_post_type', 'floru_team_disable_gutenberg', 10, 2 );
function floru_team_disable_gutenberg( $use, $post_type ) {
    if ( $post_type === 'floru_team' ) {
        return false;
    }
    return $use;
}

/**
 * Enqueue media library scripts on Team Member edit screens.
 */
add_action( 'admin_enqueue_scripts', 'floru_team_admin_scripts' );
function floru_team_admin_scripts( $hook ) {
    if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
        return;
    }
    global $post;
    if ( isset( $post ) && $post->post_type === 'floru_team' ) {
        wp_enqueue_media();
    }
}

/**
 * Inject the Team Member Profile section directly after the title field.
 * Contains: Photo, Role, Short Summary — all in one clean block.
 */
add_action( 'edit_form_after_title', 'floru_team_profile_section' );
function floru_team_profile_section( $post ) {
    if ( $post->post_type !== 'floru_team' ) {
        return;
    }

    wp_nonce_field( 'floru_team_details_nonce', 'floru_team_details_nonce' );

    $role          = get_post_meta( $post->ID, '_floru_team_role', true );
    $thumbnail_id  = get_post_thumbnail_id( $post->ID );
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';
    $excerpt       = $post->post_excerpt;
    ?>
    <div id="floru-team-profile-section">
        <div class="floru-tp-row">
            <!-- Photo -->
            <div class="floru-tp-photo-col">
                <label class="floru-tp-label">Photo</label>
                <div id="floru-tp-photo-preview" class="floru-tp-photo-preview <?php echo $thumbnail_url ? 'has-photo' : ''; ?>">
                    <?php if ( $thumbnail_url ) : ?>
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
                    <?php else : ?>
                        <span class="floru-tp-photo-placeholder">
                            <span class="dashicons dashicons-camera"></span>
                            <span>Add photo</span>
                        </span>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="floru_thumbnail_id" name="_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ? $thumbnail_id : '-1' ); ?>">
                <div class="floru-tp-photo-actions">
                    <button type="button" class="button" id="floru-tp-photo-btn"><?php echo $thumbnail_id ? 'Change Photo' : 'Upload Photo'; ?></button>
                    <button type="button" class="button floru-tp-remove-btn" id="floru-tp-photo-remove" <?php echo ! $thumbnail_id ? 'style="display:none;"' : ''; ?>>Remove</button>
                </div>
            </div>
            <!-- Role + Summary -->
            <div class="floru-tp-fields-col">
                <div class="floru-tp-field">
                    <label class="floru-tp-label" for="floru_team_role">Role / Function</label>
                    <input type="text" id="floru_team_role" name="floru_team_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" placeholder="e.g. Managing Director">
                </div>
                <div class="floru-tp-field">
                    <label class="floru-tp-label" for="floru_team_excerpt">Short Summary <span class="floru-tp-hint">— shown on team cards</span></label>
                    <textarea id="floru_team_excerpt" name="excerpt" rows="4" class="widefat" placeholder="A brief 1-2 sentence description of this person's expertise..."><?php echo esc_textarea( $excerpt ); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <h3 class="floru-tp-bio-heading">Full Bio</h3>

    <style>
        #floru-team-profile-section {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            padding: 18px 22px;
            margin: 12px 0 0;
        }
        .floru-tp-row {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }
        .floru-tp-photo-col {
            flex: 0 0 160px;
        }
        .floru-tp-fields-col {
            flex: 1;
            min-width: 0;
        }
        .floru-tp-label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 5px;
            color: #50575e;
        }
        .floru-tp-hint {
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            color: #a7aaad;
        }
        .floru-tp-field {
            margin-bottom: 14px;
        }
        .floru-tp-field:last-child {
            margin-bottom: 0;
        }
        .floru-tp-photo-preview {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 2px dashed #c3c4c7;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f7f7;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .floru-tp-photo-preview:hover {
            border-color: #2271b1;
        }
        .floru-tp-photo-preview.has-photo {
            border-style: solid;
            border-color: #dcdcde;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .floru-tp-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .floru-tp-photo-placeholder {
            text-align: center;
            color: #a7aaad;
        }
        .floru-tp-photo-placeholder .dashicons {
            display: block;
            font-size: 28px;
            width: 28px;
            height: 28px;
            margin: 0 auto 4px;
        }
        .floru-tp-photo-placeholder span:last-child {
            font-size: 11px;
        }
        .floru-tp-photo-actions {
            margin-top: 8px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .floru-tp-photo-actions .button {
            background: none;
            border: none;
            box-shadow: none;
            font-size: 12px;
            min-height: auto;
            line-height: 1;
            padding: 2px 0;
            color: #2271b1;
            cursor: pointer;
            text-decoration: none;
        }
        .floru-tp-photo-actions .button:hover {
            color: #135e96;
            text-decoration: underline;
        }
        .floru-tp-photo-actions .button:focus {
            outline: none;
            box-shadow: none;
        }
        .floru-tp-remove-btn {
            color: #b32d2e !important;
            font-size: 11px !important;
        }
        .floru-tp-remove-btn:hover {
            color: #8a1c1c !important;
        }
        .floru-tp-bio-heading {
            margin: 18px 0 0;
            padding: 0;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #50575e;
        }
        .post-type-floru_team #postdivrich {
            margin-top: 6px;
        }
        #floru_team_details .inside {
            padding: 12px;
        }
        .floru-tp-bottom-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }
        .floru-tp-bottom-row .floru-tp-field {
            margin-bottom: 0;
        }
        .floru-tp-bottom-row .floru-tp-field:first-child {
            flex: 1;
        }
        .floru-tp-bottom-row .floru-tp-field:last-child {
            flex: 0 0 100px;
        }
        @media (max-width: 782px) {
            .floru-tp-row {
                flex-direction: column;
                align-items: center;
            }
            .floru-tp-photo-col {
                flex: none;
            }
            .floru-tp-fields-col {
                width: 100%;
            }
            .floru-tp-bottom-row {
                flex-direction: column;
            }
            .floru-tp-bottom-row .floru-tp-field:last-child {
                flex: none;
                width: 100%;
            }
        }
    </style>

    <script>
    jQuery(function($) {
        var frame;
        function openMedia() {
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Select Team Member Photo',
                button: { text: 'Use This Photo' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                $('#floru_thumbnail_id').val(attachment.id);
                $('#floru-tp-photo-preview').addClass('has-photo').html('<img src="' + url + '" alt="">');
                $('#floru-tp-photo-btn').text('Change Photo');
                $('#floru-tp-photo-remove').show();
            });
            frame.open();
        }
        $('#floru-tp-photo-btn, #floru-tp-photo-preview').on('click', function(e) {
            e.preventDefault();
            openMedia();
        });
        $('#floru-tp-photo-remove').on('click', function(e) {
            e.preventDefault();
            $('#floru_thumbnail_id').val('-1');
            $('#floru-tp-photo-preview').removeClass('has-photo').html(
                '<span class="floru-tp-photo-placeholder">' +
                '<span class="dashicons dashicons-camera"></span>' +
                '<span>Add photo</span></span>'
            );
            $('#floru-tp-photo-btn').text('Upload Photo');
            $(this).hide();
        });
    });
    </script>
    <?php
}

function floru_team_details_render( $post ) {
    $profile_link = get_post_meta( $post->ID, '_floru_team_profile_link', true );
    ?>
    <div class="floru-tp-bottom-row">
        <div class="floru-tp-field">
            <label class="floru-tp-label" for="floru_team_profile_link">LinkedIn / Profile URL</label>
            <input type="url" id="floru_team_profile_link" name="floru_team_profile_link" value="<?php echo esc_url( $profile_link ); ?>" class="widefat" placeholder="https://linkedin.com/in/...">
        </div>
        <div class="floru-tp-field">
            <label class="floru-tp-label" for="floru_team_order">Display Order</label>
            <input type="number" id="floru_team_order" name="menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" min="0" step="1" style="width:100%;">
        </div>
    </div>
    <?php
}

/**
 * Custom title placeholder for Team Members.
 */
add_filter( 'enter_title_here', 'floru_team_title_placeholder', 10, 2 );
function floru_team_title_placeholder( $title, $post ) {
    if ( $post->post_type === 'floru_team' ) {
        return 'Full name (e.g. Jan van der Berg)';
    }
    return $title;
}

/**
 * Team Members admin list: custom columns with thumbnail and role.
 */
add_filter( 'manage_floru_team_posts_columns', 'floru_team_admin_columns' );
function floru_team_admin_columns( $columns ) {
    $new = array();
    $new['cb']              = $columns['cb'];
    $new['floru_photo']     = 'Photo';
    $new['title']           = 'Name';
    $new['floru_role']      = 'Role / Function';
    $new['menu_order']      = 'Order';
    return $new;
}

add_action( 'manage_floru_team_posts_custom_column', 'floru_team_admin_column_content', 10, 2 );
function floru_team_admin_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'floru_photo':
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail( $post_id, array( 48, 48 ), array( 'style' => 'border-radius:50%;object-fit:cover;width:48px;height:48px;' ) );
            } else {
                echo '<span style="display:inline-block;width:48px;height:48px;border-radius:50%;background:#e5e7eb;"></span>';
            }
            break;
        case 'floru_role':
            $role = get_post_meta( $post_id, '_floru_team_role', true );
            echo esc_html( $role ? $role : '—' );
            break;
        case 'menu_order':
            echo esc_html( get_post_field( 'menu_order', $post_id ) );
            break;
    }
}

add_filter( 'manage_edit-floru_team_sortable_columns', 'floru_team_sortable_columns' );
function floru_team_sortable_columns( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}

/**
 * Admin CSS for Team Members.
 */
add_action( 'admin_head', 'floru_team_admin_css' );
function floru_team_admin_css() {
    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }
    if ( $screen->id === 'edit-floru_team' ) {
        echo '<style>
            .column-floru_photo { width: 60px; }
            .column-menu_order { width: 60px; }
            .column-floru_role { width: 200px; }
        </style>';
    }
    if ( $screen->id === 'floru_team' ) {
        echo '<style>
            /* Reduce whitespace on edit screen */
            .post-type-floru_team #titlewrap { margin-bottom: 0; }
            .post-type-floru_team #post-body-content { margin-bottom: 12px; }
        </style>';
    }
}

add_action( 'save_post_floru_team', 'floru_team_details_save' );
function floru_team_details_save( $post_id ) {
    if ( ! isset( $_POST['floru_team_details_nonce'] ) || ! wp_verify_nonce( $_POST['floru_team_details_nonce'], 'floru_team_details_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['floru_team_role'] ) ) {
        update_post_meta( $post_id, '_floru_team_role', sanitize_text_field( $_POST['floru_team_role'] ) );
    }
    if ( isset( $_POST['floru_team_profile_link'] ) ) {
        update_post_meta( $post_id, '_floru_team_profile_link', esc_url_raw( $_POST['floru_team_profile_link'] ) );
    }
}

/**
 * Client meta boxes: Website Link
 */
add_action( 'add_meta_boxes', 'floru_client_meta_boxes' );
function floru_client_meta_boxes() {
    add_meta_box(
        'floru_client_details',
        'Client Details',
        'floru_client_details_render',
        'floru_client',
        'normal',
        'high'
    );
}

function floru_client_details_render( $post ) {
    wp_nonce_field( 'floru_client_details_nonce', 'floru_client_details_nonce' );
    $link = get_post_meta( $post->ID, '_floru_client_link', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="floru_client_link">Website URL (optional)</label></th>
            <td><input type="url" id="floru_client_link" name="floru_client_link" value="<?php echo esc_url( $link ); ?>" class="regular-text" placeholder="https://www.example.com"></td>
        </tr>
    </table>
    <p class="description">Set the Featured Image as the client's logo. Use the editor for an optional short description.</p>
    <?php
}

add_action( 'save_post_floru_client', 'floru_client_details_save' );
function floru_client_details_save( $post_id ) {
    if ( ! isset( $_POST['floru_client_details_nonce'] ) || ! wp_verify_nonce( $_POST['floru_client_details_nonce'], 'floru_client_details_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['floru_client_link'] ) ) {
        update_post_meta( $post_id, '_floru_client_link', esc_url_raw( $_POST['floru_client_link'] ) );
    }
}
