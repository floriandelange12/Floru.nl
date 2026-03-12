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
 * Client meta boxes: Logo, Website, Display Order — clean UI matching Team Members.
 */
add_action( 'add_meta_boxes', 'floru_client_meta_boxes' );
function floru_client_meta_boxes() {
    remove_meta_box( 'postimagediv', 'floru_client', 'side' );
    remove_meta_box( 'pageparentdiv', 'floru_client', 'side' );

    add_meta_box(
        'floru_client_details',
        'Website & Weergavevolgorde',
        'floru_client_details_render',
        'floru_client',
        'normal',
        'default'
    );
}

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
 * Inject Client profile section after the title — complete 10/10 editor experience.
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
    ?>

    <!-- ===== VISUAL PAGE MAP — shows where each field appears ===== -->
    <div class="floru-cp-pagemap" id="floru-cp-pagemap">
        <div class="floru-cp-pagemap__header">
            <span class="dashicons dashicons-layout"></span>
            <strong>Pagina-indeling overzicht</strong>
            <span class="floru-cp-pagemap__toggle" id="floru-cp-pagemap-toggle">Tonen / Verbergen</span>
        </div>
        <div class="floru-cp-pagemap__body" id="floru-cp-pagemap-body">
            <p class="floru-cp-pagemap__intro">Dit schema laat precies zien waar elk veld op de publieke pagina's verschijnt. Klik op een zone om naar dat veld te springen.</p>
            <div class="floru-cp-pagemap__columns">
                <!-- Overview Card -->
                <div class="floru-cp-pagemap__card">
                    <div class="floru-cp-pagemap__card-label">Overzichtskaart</div>
                    <div class="floru-cp-pagemap__card-art">
                        <div class="floru-cp-map-zone" data-target="floru-cp-logo-preview"><span class="floru-cp-map-nr">1</span> Logo</div>
                        <div class="floru-cp-map-zone" data-target="title"><span class="floru-cp-map-nr">2</span> Naam (Titel)</div>
                        <div class="floru-cp-map-zone floru-cp-map-zone--muted" data-target="content"><span class="floru-cp-map-nr">6</span> Korte samenvatting (auto)</div>
                        <div class="floru-cp-map-zone floru-cp-map-zone--link">Bekijk details &rarr;</div>
                    </div>
                </div>
                <!-- Detail Page -->
                <div class="floru-cp-pagemap__card floru-cp-pagemap__card--wide">
                    <div class="floru-cp-pagemap__card-label">Detailpagina</div>
                    <div class="floru-cp-pagemap__card-art floru-cp-pagemap__card-art--detail">
                        <div class="floru-cp-map-row">
                            <div class="floru-cp-map-zone" data-target="floru-cp-logo-preview"><span class="floru-cp-map-nr">1</span> Logo</div>
                            <div class="floru-cp-map-stack">
                                <div class="floru-cp-map-zone" data-target="title"><span class="floru-cp-map-nr">2</span> Naam (Titel)</div>
                                <div class="floru-cp-map-zone" data-target="floru_client_tagline"><span class="floru-cp-map-nr">3</span> Ondertitel</div>
                                <div class="floru-cp-map-row--inline">
                                    <div class="floru-cp-map-zone floru-cp-map-zone--sm" data-target="floru_client_industry"><span class="floru-cp-map-nr">4</span> Branche</div>
                                    <div class="floru-cp-map-zone floru-cp-map-zone--sm" data-target="floru_client_link"><span class="floru-cp-map-nr">5</span> Website</div>
                                </div>
                            </div>
                        </div>
                        <div class="floru-cp-map-zone" data-target="floru_client_highlights"><span class="floru-cp-map-nr">7</span> Belangrijkste punten (badges)</div>
                        <div class="floru-cp-map-zone floru-cp-map-zone--lg" data-target="content"><span class="floru-cp-map-nr">6</span> Volledige beschrijving</div>
                        <div class="floru-cp-map-zone" data-target="floru_client_video"><span class="floru-cp-map-nr">8</span> Ingesloten video's</div>
                    </div>
                </div>
                <!-- Sidebar -->
                <div class="floru-cp-pagemap__card">
                    <div class="floru-cp-pagemap__card-label">Zijbalk (Detailpagina)</div>
                    <div class="floru-cp-pagemap__card-art">
                        <div class="floru-cp-map-zone floru-cp-map-zone--sm" data-target="floru_client_industry"><span class="floru-cp-map-nr">4</span> Branche</div>
                        <div class="floru-cp-map-zone floru-cp-map-zone--sm" data-target="floru_client_link"><span class="floru-cp-map-nr">5</span> Website</div>
                        <div class="floru-cp-map-zone floru-cp-map-zone--link">Bezoek website &rarr;</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 1: Logo & Basic Info ===== -->
    <div class="floru-cp-section">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number">1</div>
            <div>
                <div class="floru-cp-section-title">Logo & Basisinformatie</div>
                <div class="floru-cp-section-subtitle">De kernidentiteit — wordt getoond op zowel de overzichtskaart als de detailpagina.</div>
            </div>
        </div>
        <div class="floru-cp-row">
            <div class="floru-cp-logo-col">
                <label class="floru-cp-label">Logo</label>
                <div class="floru-cp-badge-row">
                    <span class="floru-cp-badge floru-cp-badge--card" title="Wordt getoond op de overzichtskaart">Overzicht</span>
                    <span class="floru-cp-badge floru-cp-badge--detail" title="Wordt getoond op de detailpagina">Detail</span>
                </div>
                <div id="floru-cp-logo-preview" class="floru-cp-logo-preview <?php echo $thumbnail_url ? 'has-logo' : ''; ?>">
                    <?php if ( $thumbnail_url ) : ?>
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="">
                    <?php else : ?>
                        <span class="floru-cp-logo-placeholder">
                            <span class="dashicons dashicons-format-image"></span>
                            <span>Klik om logo toe te voegen</span>
                        </span>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="floru_client_thumbnail_id" name="_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ? $thumbnail_id : '-1' ); ?>">
                <div class="floru-cp-logo-actions">
                    <button type="button" class="button" id="floru-cp-logo-btn"><?php echo $thumbnail_id ? 'Logo wijzigen' : 'Logo uploaden'; ?></button>
                    <button type="button" class="button floru-cp-remove-btn" id="floru-cp-logo-remove" <?php echo ! $thumbnail_id ? 'style="display:none;"' : ''; ?>>Verwijderen</button>
                </div>
            </div>
            <div class="floru-cp-fields-col">
                <div class="floru-cp-field">
                    <label class="floru-cp-label" for="floru_client_tagline">
                        Ondertitel
                        <span class="floru-cp-badge floru-cp-badge--detail">Detail</span>
                    </label>
                    <span class="floru-cp-field-desc">Wordt als cursieve ondertitel direct onder de bedrijfsnaam getoond op de detailpagina.</span>
                    <input type="text" id="floru_client_tagline" name="floru_client_tagline" value="<?php echo esc_attr( $tagline ); ?>" class="widefat" placeholder="bijv. Innovate and Act">
                </div>
                <div class="floru-cp-field-row">
                    <div class="floru-cp-field">
                        <label class="floru-cp-label" for="floru_client_link">
                            Website-adres
                            <span class="floru-cp-badge floru-cp-badge--detail">Detail</span>
                            <span class="floru-cp-badge floru-cp-badge--sidebar">Zijbalk</span>
                        </label>
                        <span class="floru-cp-field-desc">Wordt als klikbare link in de header en als "Bezoek website"-knop in de zijbalk getoond.</span>
                        <input type="url" id="floru_client_link" name="floru_client_link" value="<?php echo esc_url( $link ); ?>" class="widefat" placeholder="https://www.example.com">
                    </div>
                    <div class="floru-cp-field">
                        <label class="floru-cp-label" for="floru_client_industry">
                            Branche / Sector
                            <span class="floru-cp-badge floru-cp-badge--detail">Detail</span>
                            <span class="floru-cp-badge floru-cp-badge--sidebar">Zijbalk</span>
                        </label>
                        <span class="floru-cp-field-desc">Wordt als badge naast de websitelink en als "Branche" in de zijbalk getoond.</span>
                        <input type="text" id="floru_client_industry" name="floru_client_industry" value="<?php echo esc_attr( $industry ); ?>" class="widefat" placeholder="bijv. Defence & Security">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 2: Key Highlights ===== -->
    <div class="floru-cp-section">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number">2</div>
            <div>
                <div class="floru-cp-section-title">
                    Belangrijkste punten
                    <span class="floru-cp-badge floru-cp-badge--detail">Detail</span>
                </div>
                <div class="floru-cp-section-subtitle">Worden als highlight-badges boven de beschrijving op de detailpagina getoond. Eén punt per regel.</div>
            </div>
        </div>
        <div class="floru-cp-field">
            <textarea id="floru_client_highlights" name="floru_client_highlights" rows="4" class="widefat" placeholder="bijv.&#10;Wereldleider in sensorsystemen&#10;20+ landen bediend&#10;Force protection specialist"><?php echo esc_textarea( $highlights ); ?></textarea>
            <span class="floru-cp-field-desc floru-cp-field-desc--below">
                <span class="dashicons dashicons-info-outline floru-cp-info-icon"></span>
                Elke regel wordt een aparte badge met een vinkje-icoon. Laat leeg om deze sectie te verbergen.
            </span>
        </div>
    </div>

    <!-- ===== SECTION 3: Videos ===== -->
    <div class="floru-cp-section">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number">3</div>
            <div>
                <div class="floru-cp-section-title">
                    Ingesloten video's
                    <span class="floru-cp-badge floru-cp-badge--detail">Detail</span>
                </div>
                <div class="floru-cp-section-subtitle">Wordt onder de beschrijving op de detailpagina getoond. Plak een YouTube- of Vimeo-URL — de video wordt automatisch ingesloten.</div>
            </div>
        </div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label" for="floru_client_video">Video 1</label>
                <input type="url" id="floru_client_video" name="floru_client_video" value="<?php echo esc_url( $video_url ); ?>" class="widefat" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            <div class="floru-cp-field">
                <label class="floru-cp-label" for="floru_client_video_2">Video 2 <span class="floru-cp-hint">— optioneel</span></label>
                <input type="url" id="floru_client_video_2" name="floru_client_video_2" value="<?php echo esc_url( $video_url_2 ); ?>" class="widefat" placeholder="https://www.youtube.com/watch?v=...">
            </div>
        </div>
    </div>

    <!-- ===== SECTION 4: Description header ===== -->
    <div class="floru-cp-section floru-cp-section--desc-header">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number">4</div>
            <div>
                <div class="floru-cp-section-title">
                    Volledige beschrijving
                    <span class="floru-cp-badge floru-cp-badge--card">Overzicht</span>
                    <span class="floru-cp-badge floru-cp-badge--detail">Detail</span>
                </div>
                <div class="floru-cp-section-subtitle">Het belangrijkste inhoudsveld. Op de <strong>overzichtskaart</strong> worden de eerste regels als korte samenvatting getoond. Op de <strong>detailpagina</strong> wordt de volledige inhoud weergegeven met opmaak, afbeeldingen en koppen.</div>
            </div>
        </div>
    </div>

    <style>
        /* ===== PAGE MAP ===== */
        .floru-cp-pagemap {
            background: linear-gradient(135deg, #f0f6fc 0%, #f6f7f7 100%);
            border: 1px solid #c3c4c7;
            border-radius: 6px;
            margin: 14px 0 0;
            overflow: hidden;
        }
        .floru-cp-pagemap__header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            background: #fff;
            border-bottom: 1px solid #dcdcde;
            cursor: pointer;
        }
        .floru-cp-pagemap__header .dashicons {
            color: #2271b1;
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
        .floru-cp-pagemap__header strong {
            font-size: 13px;
            color: #1d2327;
        }
        .floru-cp-pagemap__toggle {
            margin-left: auto;
            font-size: 11px;
            color: #2271b1;
            cursor: pointer;
        }
        .floru-cp-pagemap__toggle:hover {
            text-decoration: underline;
        }
        .floru-cp-pagemap__body {
            padding: 18px;
        }
        .floru-cp-pagemap__intro {
            font-size: 12px;
            color: #50575e;
            margin: 0 0 16px;
        }
        .floru-cp-pagemap__columns {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .floru-cp-pagemap__card {
            flex: 1;
            min-width: 160px;
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            overflow: hidden;
        }
        .floru-cp-pagemap__card--wide {
            flex: 2;
            min-width: 280px;
        }
        .floru-cp-pagemap__card-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #50575e;
            padding: 8px 12px;
            background: #f6f7f7;
            border-bottom: 1px solid #f0f0f1;
        }
        .floru-cp-pagemap__card-art {
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .floru-cp-map-zone {
            font-size: 11px;
            color: #1d2327;
            background: #f0f6fc;
            border: 1px dashed #72aee6;
            border-radius: 3px;
            padding: 5px 8px;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .floru-cp-map-zone:hover {
            background: #dceefb;
            border-color: #2271b1;
        }
        .floru-cp-map-zone--muted {
            background: #f6f7f7;
            border-color: #c3c4c7;
            color: #787c82;
        }
        .floru-cp-map-zone--sm {
            font-size: 10px;
            padding: 3px 6px;
        }
        .floru-cp-map-zone--lg {
            min-height: 36px;
        }
        .floru-cp-map-zone--link {
            background: none;
            border: none;
            color: #c3902f;
            font-size: 10px;
            font-weight: 600;
            cursor: default;
            padding: 2px 8px;
        }
        .floru-cp-map-nr {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #2271b1;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .floru-cp-map-row {
            display: flex;
            gap: 8px;
        }
        .floru-cp-map-row > .floru-cp-map-zone {
            flex: 0 0 60px;
            text-align: center;
            justify-content: center;
            min-height: 50px;
        }
        .floru-cp-map-stack {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .floru-cp-map-row--inline {
            display: flex;
            gap: 4px;
        }
        .floru-cp-map-row--inline .floru-cp-map-zone {
            flex: 1;
        }

        /* ===== SECTIONS ===== */
        .floru-cp-section {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            padding: 20px 24px;
            margin: 14px 0 0;
        }
        .floru-cp-section--desc-header {
            margin-bottom: 0;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom: none;
        }
        .floru-cp-section-header {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f0f0f1;
        }
        .floru-cp-section-number {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #2271b1;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }
        .floru-cp-section-title {
            font-weight: 600;
            font-size: 13px;
            color: #1d2327;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .floru-cp-section-subtitle {
            font-size: 12px;
            color: #787c82;
            margin-top: 3px;
            line-height: 1.5;
        }

        /* ===== BADGES: where-does-this-appear ===== */
        .floru-cp-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 2px 6px;
            border-radius: 3px;
            vertical-align: middle;
            line-height: 1.4;
        }
        .floru-cp-badge--card {
            background: #fef3cd;
            color: #856404;
        }
        .floru-cp-badge--detail {
            background: #d1ecf1;
            color: #0c5460;
        }
        .floru-cp-badge--sidebar {
            background: #e2e3e5;
            color: #383d41;
        }
        .floru-cp-badge-row {
            display: flex;
            gap: 4px;
            margin-bottom: 6px;
        }

        /* ===== FIELD DESCRIPTIONS ===== */
        .floru-cp-field-desc {
            display: block;
            font-size: 11px;
            color: #787c82;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .floru-cp-field-desc--below {
            margin-bottom: 0;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .floru-cp-info-icon {
            font-size: 14px !important;
            width: 14px !important;
            height: 14px !important;
            color: #a7aaad;
        }

        /* ===== BASE FIELD STYLES ===== */
        .floru-cp-row {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }
        .floru-cp-logo-col {
            flex: 0 0 170px;
        }
        .floru-cp-fields-col {
            flex: 1;
            min-width: 0;
        }
        .floru-cp-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
            color: #50575e;
            flex-wrap: wrap;
        }
        .floru-cp-hint {
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            color: #a7aaad;
        }
        .floru-cp-field {
            margin-bottom: 16px;
        }
        .floru-cp-field:last-child {
            margin-bottom: 0;
        }
        .floru-cp-field-row {
            display: flex;
            gap: 16px;
        }
        .floru-cp-field-row .floru-cp-field {
            flex: 1;
        }

        /* ===== LOGO PREVIEW ===== */
        .floru-cp-logo-preview {
            width: 170px;
            height: 126px;
            border-radius: 8px;
            border: 2px dashed #c3c4c7;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f6f7f7;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .floru-cp-logo-preview:hover {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
        }
        .floru-cp-logo-preview.has-logo {
            border-style: solid;
            border-color: #dcdcde;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            background: #fff;
        }
        .floru-cp-logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 10px;
        }
        .floru-cp-logo-placeholder {
            text-align: center;
            color: #a7aaad;
            transition: color 0.2s;
        }
        .floru-cp-logo-preview:hover .floru-cp-logo-placeholder {
            color: #2271b1;
        }
        .floru-cp-logo-placeholder .dashicons {
            display: block;
            font-size: 32px;
            width: 32px;
            height: 32px;
            margin: 0 auto 6px;
        }
        .floru-cp-logo-placeholder span:last-child {
            font-size: 11px;
            font-weight: 500;
        }
        .floru-cp-logo-actions {
            margin-top: 8px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .floru-cp-logo-actions .button {
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
            font-size: 11px !important;
        }
        .floru-cp-remove-btn:hover {
            color: #8a1c1c !important;
        }

        /* ===== CONTENT EDITOR CONNECTOR ===== */
        .post-type-floru_client #postdivrich {
            margin-top: 0;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .post-type-floru_client .floru-cp-section--desc-header + #postdivrich {
            border-top: 1px solid #dcdcde;
        }

        /* ===== BOTTOM META BOX ===== */
        #floru_client_details .inside {
            padding: 12px;
        }
        .floru-cp-bottom-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }
        .floru-cp-bottom-row .floru-cp-field {
            margin-bottom: 0;
        }
        .floru-cp-bottom-row .floru-cp-field:first-child {
            flex: 1;
        }
        .floru-cp-bottom-row .floru-cp-field:last-child {
            flex: 0 0 100px;
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
            .floru-cp-bottom-row {
                flex-direction: column;
            }
            .floru-cp-bottom-row .floru-cp-field:last-child {
                flex: none;
                width: 100%;
            }
            .floru-cp-pagemap__columns {
                flex-direction: column;
            }
            .floru-cp-section-header {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>

    <script>
    jQuery(function($) {
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
                $('#floru-cp-logo-btn').text('Logo wijzigen');
                $('#floru-cp-logo-remove').show();
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
                '<span>Klik om logo toe te voegen</span></span>'
            );
            $('#floru-cp-logo-btn').text('Logo uploaden');
            $(this).hide();
        });

        /* ----- Page map toggle ----- */
        var mapBody = $('#floru-cp-pagemap-body');
        var mapState = localStorage.getItem('floru_pagemap_open');
        if (mapState === 'closed') {
            mapBody.hide();
        }
        $('.floru-cp-pagemap__header').on('click', function() {
            mapBody.slideToggle(200);
            localStorage.setItem('floru_pagemap_open', mapBody.is(':visible') ? 'open' : 'closed');
        });

        /* ----- Click zone to jump to field ----- */
        $(document).on('click', '.floru-cp-map-zone[data-target]', function() {
            var target = $(this).data('target');
            var el;
            if (target === 'title') {
                el = $('#title');
            } else if (target === 'content') {
                el = $('#postdivrich');
            } else {
                el = $('#' + target);
            }
            if (el.length) {
                $('html, body').animate({ scrollTop: el.offset().top - 80 }, 300);
                el.css('box-shadow', '0 0 0 3px rgba(34,113,177,0.35)');
                setTimeout(function() {
                    el.css('box-shadow', '');
                }, 1500);
                if (el.is('input, textarea')) {
                    el.focus();
                }
            }
        });
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
    ?>
    <div class="floru-cp-bottom-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label" for="floru_client_order">Weergavevolgorde</label>
            <input type="number" id="floru_client_order" name="menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" min="0" step="1" style="width:100%;">
        </div>
    </div>
    <?php
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
            .post-type-floru_client #post-body-content { margin-bottom: 12px; }
            .post-type-floru_client #postdivrich { margin-top: 0; }
            .post-type-floru_client .floru-cp-section--desc-header { margin-bottom: -1px; position: relative; z-index: 1; }
            /* Hide default excerpt & custom fields for cleaner UI */
            .post-type-floru_client #postexcerpt,
            .post-type-floru_client #postcustom { display: none; }
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
