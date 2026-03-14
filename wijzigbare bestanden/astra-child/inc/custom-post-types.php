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

    // Flush rewrite rules once after CPT changes.
    if ( get_option( 'floru_flush_rewrites_v2' ) !== '1' ) {
        add_action( 'init', function() {
            flush_rewrite_rules( false );
            update_option( 'floru_flush_rewrites_v2', '1' );
        }, 99 );
    }

    // Team Members
    register_post_type( 'floru_team', array(
        'labels' => array(
            'name'               => 'Teamleden',
            'singular_name'      => 'Teamlid',
            'add_new'            => 'Nieuw teamlid',
            'add_new_item'       => 'Nieuw teamlid toevoegen',
            'edit_item'          => 'Teamlid bewerken',
            'new_item'           => 'Nieuw teamlid',
            'view_item'          => 'Teamlid bekijken',
            'search_items'       => 'Teamleden zoeken',
            'not_found'          => 'Geen teamleden gevonden',
            'not_found_in_trash' => 'Geen teamleden in prullenbak',
            'menu_name'          => 'Teamleden',
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

    // Clients / Opdrachtgevers
    register_post_type( 'floru_client', array(
        'labels' => array(
            'name'               => 'Opdrachtgevers',
            'singular_name'      => 'Opdrachtgever',
            'add_new'            => 'Nieuwe opdrachtgever',
            'add_new_item'       => 'Nieuwe opdrachtgever toevoegen',
            'edit_item'          => 'Opdrachtgever bewerken',
            'new_item'           => 'Nieuwe opdrachtgever',
            'view_item'          => 'Opdrachtgever bekijken',
            'search_items'       => 'Opdrachtgevers zoeken',
            'not_found'          => 'Geen opdrachtgevers gevonden',
            'not_found_in_trash' => 'Geen opdrachtgevers in prullenbak',
            'menu_name'          => 'Opdrachtgevers',
        ),
        'public'       => true,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-building',
        'menu_position'=> 26,
        'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'excerpt' ),
        'has_archive'  => false,
        'rewrite'      => array( 'slug' => 'client', 'with_front' => false ),
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
        'LinkedIn & Weergavevolgorde',
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
                <label class="floru-tp-label">Foto</label>
                <div id="floru-tp-photo-preview" class="floru-tp-photo-preview <?php echo $thumbnail_url ? 'has-photo' : ''; ?>">
                    <?php if ( $thumbnail_url ) : ?>
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
                    <?php else : ?>
                        <span class="floru-tp-photo-placeholder">
                            <span class="dashicons dashicons-camera"></span>
                            <span>Klik om foto toe te voegen</span>
                        </span>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="floru_thumbnail_id" name="_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ? $thumbnail_id : '-1' ); ?>">
                <div class="floru-tp-photo-actions">
                    <button type="button" class="button" id="floru-tp-photo-btn"><?php echo $thumbnail_id ? 'Foto wijzigen' : 'Foto uploaden'; ?></button>
                    <button type="button" class="button floru-tp-remove-btn" id="floru-tp-photo-remove" <?php echo ! $thumbnail_id ? 'style="display:none;"' : ''; ?>>Verwijderen</button>
                </div>
            </div>
            <!-- Role + Summary -->
            <div class="floru-tp-fields-col">
                <div class="floru-tp-field">
                    <label class="floru-tp-label" for="floru_team_role">Rol / Functie</label>
                    <input type="text" id="floru_team_role" name="floru_team_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" placeholder="bijv. Managing Director">
                </div>
                <div class="floru-tp-field">
                    <label class="floru-tp-label" for="floru_team_excerpt">Korte samenvatting <span class="floru-tp-hint">— wordt getoond op teamkaarten</span></label>
                    <textarea id="floru_team_excerpt" name="excerpt" rows="4" class="widefat" placeholder="Een korte beschrijving (1-2 zinnen) van de expertise van deze persoon..."><?php echo esc_textarea( $excerpt ); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <h3 class="floru-tp-bio-heading">Volledige biografie</h3>

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
                title: 'Selecteer foto teamlid',
                button: { text: 'Gebruik deze foto' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                $('#floru_thumbnail_id').val(attachment.id);
                $('#floru-tp-photo-preview').addClass('has-photo').html('<img src="' + url + '" alt="">');
                $('#floru-tp-photo-btn').text('Foto wijzigen');
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
                '<span>Klik om foto toe te voegen</span></span>'
            );
            $('#floru-tp-photo-btn').text('Foto uploaden');
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
            <label class="floru-tp-label" for="floru_team_profile_link">LinkedIn / Profiel URL</label>
            <input type="url" id="floru_team_profile_link" name="floru_team_profile_link" value="<?php echo esc_url( $profile_link ); ?>" class="widefat" placeholder="https://linkedin.com/in/...">
        </div>
        <div class="floru-tp-field">
            <label class="floru-tp-label" for="floru_team_order">Weergavevolgorde</label>
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
        return 'Volledige naam (bijv. Jan van der Berg)';
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
    $new['floru_photo']     = 'Foto';
    $new['title']           = 'Naam';
    $new['floru_role']      = 'Rol / Functie';
    $new['menu_order']      = 'Volgorde';
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
 * Client meta boxes: clean UI — remove unnecessary boxes, hide Astra settings.
 */
add_action( 'add_meta_boxes', 'floru_client_meta_boxes', 99 );
function floru_client_meta_boxes() {
    remove_meta_box( 'postimagediv', 'floru_client', 'side' );
    remove_meta_box( 'pageparentdiv', 'floru_client', 'side' );
    remove_meta_box( 'astra_settings_meta_box', 'floru_client', 'side' );
}

/**
 * Exclude floru_client from Astra meta registration entirely.
 */
add_filter( 'astra_excluded_meta_post_types', function( $types ) {
    $types[] = 'floru_client';
    return $types;
} );

/**
 * Disable Gutenberg for Clients — classic editor is cleaner for this CPT.
 */
add_filter( 'use_block_editor_for_post_type', 'floru_client_disable_gutenberg', 10, 2 );
function floru_client_disable_gutenberg( $use, $post_type ) {
    if ( $post_type === 'floru_client' ) {
        return false;
    }
    return $use;
}

/**
 * Enqueue media library scripts on Client edit screens.
 */
add_action( 'admin_enqueue_scripts', 'floru_client_admin_scripts' );
function floru_client_admin_scripts( $hook ) {
    if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
        return;
    }
    global $post;
    if ( isset( $post ) && $post->post_type === 'floru_client' ) {
        wp_enqueue_media();
    }
}

/**
 * Inject Client editor sections after the title — clean editorial flow.
 *
 * Structure:
 *   1. Basisinformatie (Logo, Ondertitel, Website, Branche)
 *   2. Overzichtskaart (samenvatting-uitleg)
 *   3. Detailpagina (Highlights + Beschrijving)
 *   4. Media (Video's — optioneel)
 *   5. Publicatie (Weergavevolgorde)
 *   + Compacte kaart-preview (zijpaneel)
 */
add_action( 'edit_form_after_title', 'floru_client_profile_section' );
function floru_client_profile_section( $post ) {
    if ( $post->post_type !== 'floru_client' ) {
        return;
    }

    wp_nonce_field( 'floru_client_details_nonce', 'floru_client_details_nonce' );

    $link          = get_post_meta( $post->ID, '_floru_client_link', true );
    $tagline       = get_post_meta( $post->ID, '_floru_client_tagline', true );
    $industry      = get_post_meta( $post->ID, '_floru_client_industry', true );
    $video_url     = get_post_meta( $post->ID, '_floru_client_video', true );
    $video_url_2   = get_post_meta( $post->ID, '_floru_client_video_2', true );
    $highlights    = get_post_meta( $post->ID, '_floru_client_highlights', true );
    $thumbnail_id  = get_post_thumbnail_id( $post->ID );
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';
    $content_raw   = $post->post_content;
    $summary_auto  = wp_trim_words( wp_strip_all_tags( $content_raw ), 24, '…' );
    ?>

    <!-- ===== SECTION 1: Basisinformatie ===== -->
    <div class="floru-cp-section">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number">1</div>
            <div class="floru-cp-section-title">Basisinformatie</div>
        </div>
        <div class="floru-cp-row">
            <div class="floru-cp-logo-col">
                <div id="floru-cp-logo-preview" class="floru-cp-logo-preview <?php echo $thumbnail_url ? 'has-logo' : ''; ?>">
                    <?php if ( $thumbnail_url ) : ?>
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
                    <?php else : ?>
                        <span class="floru-cp-logo-placeholder">
                            <span class="dashicons dashicons-format-image"></span>
                            <span>Logo toevoegen</span>
                        </span>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="floru_client_thumbnail_id" name="_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ? $thumbnail_id : '-1' ); ?>">
                <div class="floru-cp-logo-actions">
                    <button type="button" class="button" id="floru-cp-logo-btn"><?php echo $thumbnail_id ? 'Wijzigen' : 'Uploaden'; ?></button>
                    <button type="button" class="button floru-cp-remove-btn" id="floru-cp-logo-remove" <?php echo ! $thumbnail_id ? 'style="display:none;"' : ''; ?>>Verwijderen</button>
                </div>
            </div>
            <div class="floru-cp-fields-col">
                <div class="floru-cp-field">
                    <label class="floru-cp-label" for="floru_client_tagline">Ondertitel</label>
                    <input type="text" id="floru_client_tagline" name="floru_client_tagline" value="<?php echo esc_attr( $tagline ); ?>" class="widefat" placeholder="bijv. Innovate and Act">
                </div>
                <div class="floru-cp-field-row">
                    <div class="floru-cp-field">
                        <label class="floru-cp-label" for="floru_client_link">Website</label>
                        <input type="url" id="floru_client_link" name="floru_client_link" value="<?php echo esc_url( $link ); ?>" class="widefat" placeholder="https://www.example.com">
                    </div>
                    <div class="floru-cp-field">
                        <label class="floru-cp-label" for="floru_client_industry">Branche / Sector</label>
                        <input type="text" id="floru_client_industry" name="floru_client_industry" value="<?php echo esc_attr( $industry ); ?>" class="widefat" placeholder="bijv. Defence & Security">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 2: Detailpagina ===== -->
    <div class="floru-cp-section floru-cp-section--detail">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number">2</div>
            <div class="floru-cp-section-title">Detailpagina</div>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label" for="floru_client_highlights">Highlights</label>
            <span class="floru-cp-field-hint">Eén per regel — worden als badges getoond. Laat leeg om te verbergen.</span>
            <textarea id="floru_client_highlights" name="floru_client_highlights" rows="3" class="widefat" placeholder="bijv.&#10;Wereldleider in sensorsystemen&#10;20+ landen bediend"><?php echo esc_textarea( $highlights ); ?></textarea>
        </div>
        <div class="floru-cp-field floru-cp-field--desc">
            <label class="floru-cp-label">Beschrijving</label>
            <span class="floru-cp-field-hint">De eerste regels verschijnen ook als samenvatting op de overzichtskaart.</span>
        </div>
    </div>

    <!-- ===== SECTION 3: Extra's (injected after editor via JS) ===== -->
    <div class="floru-cp-section floru-cp-section--collapsed floru-cp-section--extras" id="floru-cp-extras-section" style="display:none;">
        <div class="floru-cp-section-header floru-cp-section-header--collapsible" id="floru-cp-extras-toggle">
            <div class="floru-cp-section-number floru-cp-section-number--muted">3</div>
            <div class="floru-cp-section-title">Extra's <span class="floru-cp-optional">video's & weergave</span></div>
            <span class="floru-cp-collapse-icon dashicons dashicons-arrow-down-alt2"></span>
        </div>
        <div class="floru-cp-section-body" id="floru-cp-extras-body">
            <div class="floru-cp-field-row">
                <div class="floru-cp-field">
                    <label class="floru-cp-label" for="floru_client_video">Video 1</label>
                    <input type="url" id="floru_client_video" name="floru_client_video" value="<?php echo esc_url( $video_url ); ?>" class="widefat" placeholder="YouTube- of Vimeo-URL">
                </div>
                <div class="floru-cp-field">
                    <label class="floru-cp-label" for="floru_client_video_2">Video 2</label>
                    <input type="url" id="floru_client_video_2" name="floru_client_video_2" value="<?php echo esc_url( $video_url_2 ); ?>" class="widefat" placeholder="YouTube- of Vimeo-URL">
                </div>
                <div class="floru-cp-field" style="flex:0 0 100px;">
                    <label class="floru-cp-label" for="floru_client_order">Volgorde</label>
                    <input type="number" id="floru_client_order" name="menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" min="0" step="1" class="widefat">
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Compact card preview (side panel) ===== -->
    <div id="floru-cp-preview-panel" class="floru-cp-preview-panel" style="display:none;">
        <div class="floru-cp-preview-label">Overzichtskaart preview</div>
        <div class="floru-cp-preview-card">
            <div class="floru-cp-preview-logo" id="floru-cp-preview-logo">
                <?php if ( $thumbnail_url ) : ?>
                    <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
                <?php else : ?>
                    <span class="dashicons dashicons-format-image"></span>
                <?php endif; ?>
            </div>
            <div class="floru-cp-preview-name" id="floru-cp-preview-name"><?php echo esc_html( $post->post_title ?: 'Bedrijfsnaam' ); ?></div>
            <div class="floru-cp-preview-summary" id="floru-cp-preview-summary"><?php echo esc_html( $summary_auto ?: 'Samenvatting…' ); ?></div>
        </div>
    </div>

    <style>
        /* ===== SECTIONS — Compact, premium ===== */
        .floru-cp-section {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            padding: 14px 18px;
            margin: 8px 0 0;
        }
        .floru-cp-section--detail {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .floru-cp-field--desc {
            margin: 10px -18px 0;
            padding: 10px 18px 0;
            border-top: 1px solid #f0f0f1;
        }
        .floru-cp-section-header {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f1;
        }
        .floru-cp-section-header:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .floru-cp-section-number {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #2271b1;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
        }
        .floru-cp-section-number--muted {
            background: #c3c4c7;
        }
        .floru-cp-section-title {
            font-weight: 600;
            font-size: 13px;
            color: #1d2327;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .floru-cp-optional {
            font-weight: 400;
            font-size: 11px;
            color: #a7aaad;
            font-style: italic;
        }

        /* ===== FIELD STYLES ===== */
        .floru-cp-label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
            color: #50575e;
        }
        .floru-cp-field-hint {
            display: block;
            font-size: 11px;
            color: #a7aaad;
            margin-bottom: 4px;
            line-height: 1.35;
        }
        .floru-cp-field {
            margin-bottom: 10px;
        }
        .floru-cp-field:last-child {
            margin-bottom: 0;
        }
        .floru-cp-field-row {
            display: flex;
            gap: 12px;
        }
        .floru-cp-field-row .floru-cp-field {
            flex: 1;
        }
        .floru-cp-row {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }
        .floru-cp-logo-col {
            flex: 0 0 140px;
        }
        .floru-cp-fields-col {
            flex: 1;
            min-width: 0;
        }

        /* ===== LOGO PREVIEW ===== */
        .floru-cp-logo-preview {
            width: 140px;
            height: 100px;
            border-radius: 6px;
            border: 2px dashed #c3c4c7;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f7f7;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .floru-cp-logo-preview:hover {
            border-color: #2271b1;
        }
        .floru-cp-logo-preview.has-logo {
            border-style: solid;
            border-color: #dcdcde;
            background: #fff;
        }
        .floru-cp-logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 8px;
        }
        .floru-cp-logo-placeholder {
            text-align: center;
            color: #a7aaad;
        }
        .floru-cp-logo-preview:hover .floru-cp-logo-placeholder {
            color: #2271b1;
        }
        .floru-cp-logo-placeholder .dashicons {
            display: block;
            font-size: 24px;
            width: 24px;
            height: 24px;
            margin: 0 auto 2px;
        }
        .floru-cp-logo-placeholder span:last-child {
            font-size: 10px;
        }
        .floru-cp-logo-actions {
            margin-top: 4px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .floru-cp-logo-actions .button {
            background: none;
            border: none;
            box-shadow: none;
            font-size: 11px;
            min-height: auto;
            line-height: 1;
            padding: 2px 0;
            color: #2271b1;
            cursor: pointer;
            text-decoration: none;
        }
        .floru-cp-logo-actions .button:hover {
            color: #135e96;
            text-decoration: underline;
        }
        .floru-cp-logo-actions .button:focus {
            outline: none;
            box-shadow: none;
        }
        .floru-cp-remove-btn {
            color: #b32d2e !important;
        }
        .floru-cp-remove-btn:hover {
            color: #8a1c1c !important;
        }

        /* ===== COLLAPSIBLE SECTIONS ===== */
        .floru-cp-section-header--collapsible {
            cursor: pointer;
            user-select: none;
        }
        .floru-cp-collapse-icon {
            margin-left: auto;
            color: #a7aaad;
            font-size: 16px;
            width: 16px;
            height: 16px;
            transition: transform 0.2s;
        }
        .floru-cp-section--collapsed .floru-cp-collapse-icon {
            transform: rotate(-90deg);
        }
        .floru-cp-section--collapsed .floru-cp-section-body {
            display: none;
        }
        .floru-cp-section--collapsed .floru-cp-section-header {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        /* ===== CONTENT EDITOR CONNECTOR ===== */
        .post-type-floru_client #postdivrich {
            margin-top: 0;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .post-type-floru_client .floru-cp-section--detail + #postdivrich {
            border-top: 1px solid #dcdcde;
        }

        /* ===== CARD PREVIEW PANEL (sidebar) ===== */
        .floru-cp-preview-panel {
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 10px;
        }
        .floru-cp-preview-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #a7aaad;
            margin-bottom: 5px;
        }
        .floru-cp-preview-card {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px 8px 8px;
            text-align: center;
            background: #fff;
        }
        .floru-cp-preview-logo {
            width: 48px;
            height: 30px;
            margin: 0 auto 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .floru-cp-preview-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .floru-cp-preview-logo .dashicons {
            font-size: 16px;
            color: #c3c4c7;
        }
        .floru-cp-preview-name {
            font-weight: 600;
            font-size: 11px;
            color: #1d2327;
            margin-bottom: 1px;
            line-height: 1.3;
        }
        .floru-cp-preview-summary {
            font-size: 9px;
            color: #a7aaad;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 782px) {
            .floru-cp-row,
            .floru-cp-field-row {
                flex-direction: column;
            }
            .floru-cp-row {
                align-items: center;
            }
            .floru-cp-logo-col {
                flex: none;
            }
            .floru-cp-fields-col {
                width: 100%;
            }
        }
    </style>

    <script>
    jQuery(function($) {
        /* ----- Move extras section after the editor ----- */
        var $editor = $('#postdivrich');
        if ($editor.length) {
            $('#floru-cp-extras-section').insertAfter($editor).show();
        }

        /* ----- Move preview panel to top of sidebar ----- */
        if ($('#side-sortables').length) {
            $('#floru-cp-preview-panel').prependTo('#side-sortables').show();
        }

        /* ----- Logo media picker ----- */
        var frame;
        function openMedia() {
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Selecteer bedrijfslogo',
                button: { text: 'Gebruik dit logo' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                $('#floru_client_thumbnail_id').val(attachment.id);
                $('#floru-cp-logo-preview').addClass('has-logo').html('<img src="' + url + '" alt="">');
                $('#floru-cp-logo-btn').text('Wijzigen');
                $('#floru-cp-logo-remove').show();
                $('#floru-cp-preview-logo').html('<img src="' + url + '" alt="">');
            });
            frame.open();
        }
        $('#floru-cp-logo-btn, #floru-cp-logo-preview').on('click', function(e) {
            e.preventDefault();
            openMedia();
        });
        $('#floru-cp-logo-remove').on('click', function(e) {
            e.preventDefault();
            $('#floru_client_thumbnail_id').val('-1');
            $('#floru-cp-logo-preview').removeClass('has-logo').html(
                '<span class="floru-cp-logo-placeholder">' +
                '<span class="dashicons dashicons-format-image"></span>' +
                '<span>Logo toevoegen</span></span>'
            );
            $('#floru-cp-logo-btn').text('Uploaden');
            $(this).hide();
            $('#floru-cp-preview-logo').html('<span class="dashicons dashicons-format-image"></span>');
        });

        /* ----- Collapsible extras section ----- */
        $('#floru-cp-extras-toggle').on('click', function() {
            $(this).closest('.floru-cp-section').toggleClass('floru-cp-section--collapsed');
        });

        /* ----- Live preview updates ----- */
        $('#title').on('input', function() {
            $('#floru-cp-preview-name').text($(this).val() || 'Bedrijfsnaam');
        });

        /* ----- Hide old meta boxes that might still be registered ----- */
        $('#floru_client_details, #astra_settings_meta_box').hide();
    });
    </script>
    <?php
}

/**
 * Custom title placeholder for Clients.
 */
add_filter( 'enter_title_here', 'floru_client_title_placeholder', 10, 2 );
function floru_client_title_placeholder( $title, $post ) {
    if ( $post->post_type === 'floru_client' ) {
        return 'Bedrijfsnaam (bijv. SAAB)';
    }
    return $title;
}

function floru_client_details_render( $post ) {
    // Kept for backward compat — fields moved into main flow.
    // This meta box is no longer registered but function remains safe.
}

/**
 * Client admin list: custom columns with logo.
 */
add_filter( 'manage_floru_client_posts_columns', 'floru_client_admin_columns' );
function floru_client_admin_columns( $columns ) {
    $new = array();
    $new['cb']           = $columns['cb'];
    $new['floru_logo']   = 'Logo';
    $new['title']        = 'Naam';
    $new['floru_link']   = 'Website';
    $new['floru_industry'] = 'Branche';
    $new['menu_order']   = 'Volgorde';
    return $new;
}

add_action( 'manage_floru_client_posts_custom_column', 'floru_client_admin_column_content', 10, 2 );
function floru_client_admin_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'floru_logo':
            if ( has_post_thumbnail( $post_id ) ) {
                echo '<div class="floru-admin-logo-wrap">' . get_the_post_thumbnail( $post_id, array( 120, 60 ), array( 'style' => 'display:block;max-width:100%;max-height:50px;width:auto;height:auto;object-fit:contain;' ) ) . '</div>';
            } else {
                echo '<div class="floru-admin-logo-wrap floru-admin-logo-wrap--empty"><span class="dashicons dashicons-format-image"></span></div>';
            }
            break;
        case 'floru_link':
            $link = get_post_meta( $post_id, '_floru_client_link', true );
            echo $link ? '<a href="' . esc_url( $link ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( wp_parse_url( $link, PHP_URL_HOST ) ) . ' <span class="dashicons dashicons-external" style="font-size:12px;width:12px;height:12px;vertical-align:middle;opacity:.5;"></span></a>' : '<span style="color:#a7aaad;">&mdash;</span>';
            break;
        case 'floru_industry':
            $industry = get_post_meta( $post_id, '_floru_client_industry', true );
            echo $industry ? '<span class="floru-admin-industry-badge">' . esc_html( $industry ) . '</span>' : '<span style="color:#a7aaad;">&mdash;</span>';
            break;
        case 'menu_order':
            echo '<span class="floru-admin-order">' . esc_html( get_post_field( 'menu_order', $post_id ) ) . '</span>';
            break;
    }
}

add_filter( 'manage_edit-floru_client_sortable_columns', 'floru_client_sortable_columns' );
function floru_client_sortable_columns( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}

/**
 * Admin CSS for Clients list & edit screen.
 */
add_action( 'admin_head', 'floru_client_admin_css' );
function floru_client_admin_css() {
    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }
    if ( $screen->id === 'edit-floru_client' ) {
        echo '<style>
            /* Column widths */
            .column-floru_logo { width: 120px; }
            .column-menu_order { width: 80px; text-align: center; }
            .column-floru_link { width: 180px; }
            .column-floru_industry { width: 160px; }

            /* Logo container */
            .floru-admin-logo-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100px;
                height: 56px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 6px;
                overflow: hidden;
            }
            .floru-admin-logo-wrap--empty {
                background: #f6f7f7;
                border-style: dashed;
                color: #c3c4c7;
            }
            .floru-admin-logo-wrap--empty .dashicons {
                font-size: 22px;
                width: 22px;
                height: 22px;
            }

            /* Industry badge */
            .floru-admin-industry-badge {
                display: inline-block;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                color: #50575e;
                background: #f0f0f1;
                padding: 3px 8px;
                border-radius: 3px;
            }

            /* Order number */
            .floru-admin-order {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #f0f0f1;
                font-weight: 600;
                font-size: 13px;
                color: #50575e;
            }

            /* Row hover */
            .post-type-floru_client .wp-list-table tbody tr:hover {
                background: #f8f9fa;
            }
            .post-type-floru_client .wp-list-table tbody tr:hover .floru-admin-logo-wrap {
                border-color: #c3c4c7;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            }

            /* Links */
            .post-type-floru_client .wp-list-table a[target="_blank"] {
                color: #2271b1;
            }
            .post-type-floru_client .wp-list-table a[target="_blank"]:hover {
                color: #135e96;
            }

            /* Centered volgorde header */
            .post-type-floru_client .column-menu_order { text-align: center; }
        </style>';
    }
    if ( $screen->id === 'floru_client' ) {
        echo '<style>
            .post-type-floru_client #titlewrap { margin-bottom: 0; }
            .post-type-floru_client #post-body-content { margin-bottom: 8px; }
            .post-type-floru_client #postdivrich { margin-top: 0; }
            .post-type-floru_client .floru-cp-section--desc-header { margin-bottom: -1px; position: relative; z-index: 1; }
            /* Hide excerpt, custom fields, Astra settings, old details box */
            .post-type-floru_client #postexcerpt,
            .post-type-floru_client #postcustom,
            .post-type-floru_client #astra_settings_meta_box,
            .post-type-floru_client #floru_client_details { display: none !important; }
            /* Tighter editor chrome */
            .post-type-floru_client #wp-content-editor-tools { padding-top: 0; }
        </style>';
    }
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
    if ( isset( $_POST['floru_client_tagline'] ) ) {
        update_post_meta( $post_id, '_floru_client_tagline', sanitize_text_field( $_POST['floru_client_tagline'] ) );
    }
    if ( isset( $_POST['floru_client_industry'] ) ) {
        update_post_meta( $post_id, '_floru_client_industry', sanitize_text_field( $_POST['floru_client_industry'] ) );
    }
    if ( isset( $_POST['floru_client_video'] ) ) {
        update_post_meta( $post_id, '_floru_client_video', esc_url_raw( $_POST['floru_client_video'] ) );
    }
    if ( isset( $_POST['floru_client_video_2'] ) ) {
        update_post_meta( $post_id, '_floru_client_video_2', esc_url_raw( $_POST['floru_client_video_2'] ) );
    }
    if ( isset( $_POST['floru_client_highlights'] ) ) {
        update_post_meta( $post_id, '_floru_client_highlights', sanitize_textarea_field( $_POST['floru_client_highlights'] ) );
    }
}
