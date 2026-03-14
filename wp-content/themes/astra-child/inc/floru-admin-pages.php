<?php
/**
 * Floru Admin Pages
 * Custom admin menu and premium editing experience for Floru site pages.
 * Each page gets a direct link in the sidebar — just like Teamleden & Opdrachtgevers.
 * Gutenberg is disabled; pages use the classic editor with premium meta box styling.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Floru page definitions: template → label + icon.
 */
function floru_get_page_definitions() {
    return array(
        'templates/template-home.php'     => array( 'label' => 'Home',           'icon' => 'dashicons-admin-home' ),
        'templates/template-about.php'    => array( 'label' => 'Over ons',       'icon' => 'dashicons-info' ),
        'templates/template-services.php' => array( 'label' => 'Diensten',       'icon' => 'dashicons-portfolio' ),
        'templates/template-team.php'     => array( 'label' => 'Ons team',       'icon' => 'dashicons-groups' ),
        'templates/template-clients.php'  => array( 'label' => 'Opdrachtgevers', 'icon' => 'dashicons-building' ),
        'templates/template-contact.php'  => array( 'label' => 'Contact',        'icon' => 'dashicons-email' ),
    );
}

/**
 * Check if a page uses a Floru template.
 */
function floru_is_floru_page( $post_id ) {
    $template = get_post_meta( $post_id, '_wp_page_template', true );
    return array_key_exists( $template, floru_get_page_definitions() );
}

/**
 * Find the WP page object for a given Floru template.
 */
function floru_find_page_by_template( $template_file ) {
    $pages = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => $template_file,
        'number'     => 1,
    ) );
    return ! empty( $pages ) ? $pages[0] : null;
}

/* ==========================================================================
   ADMIN MENU — "Floru Pagina's" with submenu per page (direct edit links)
   ========================================================================== */

add_action( 'admin_menu', 'floru_register_pages_menu' );
function floru_register_pages_menu() {
    $definitions = floru_get_page_definitions();

    // Find the first Floru page to use as the default landing.
    $first_page = null;
    foreach ( $definitions as $tpl => $info ) {
        $page = floru_find_page_by_template( $tpl );
        if ( $page ) {
            $first_page = $page;
            break;
        }
    }

    // Parent menu — redirects to the first page's edit screen.
    $parent_slug = 'floru-pages';
    add_menu_page(
        "Floru Pagina's",
        "Floru Pagina's",
        'edit_pages',
        $parent_slug,
        'floru_pages_overview_render',
        'dashicons-welcome-widgets-menus',
        24
    );

    // Add a submenu item for each Floru page — direct link to edit screen.
    foreach ( $definitions as $tpl => $info ) {
        $page = floru_find_page_by_template( $tpl );
        if ( ! $page ) {
            continue;
        }

        // Use the actual post.php edit URL as the submenu slug.
        $edit_slug = 'post.php?post=' . $page->ID . '&action=edit';

        add_submenu_page(
            $parent_slug,
            $info['label'] . ' bewerken',
            $info['label'],
            'edit_pages',
            $edit_slug
        );
    }

    // Remove the auto-created duplicate submenu item for the parent.
    global $submenu;
    if ( isset( $submenu[ $parent_slug ] ) ) {
        foreach ( $submenu[ $parent_slug ] as $key => $item ) {
            if ( $item[2] === $parent_slug ) {
                $submenu[ $parent_slug ][ $key ][0] = 'Overzicht';
                break;
            }
        }
    }
}

/**
 * Overview page — shown when clicking the parent "Floru Pagina's".
 */
function floru_pages_overview_render() {
    $definitions = floru_get_page_definitions();
    ?>
    <div class="wrap" style="max-width: 860px;">
        <h1>Floru Pagina's</h1>
        <p style="color: #50575e; font-size: 14px; margin: 4px 0 24px;">
            Beheer hier de pagina's van de Floru website. Klik op een pagina om de inhoud te bewerken.
        </p>
        <div style="display: flex; flex-direction: column; gap: 6px;">
        <?php foreach ( $definitions as $tpl => $info ) :
            $page = floru_find_page_by_template( $tpl );
            if ( ! $page ) continue;
            $edit_url = admin_url( 'post.php?post=' . $page->ID . '&action=edit' );
            $status   = get_post_status( $page->ID );
        ?>
            <a href="<?php echo esc_url( $edit_url ); ?>" style="display:flex;align-items:center;gap:16px;background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px 20px;text-decoration:none;color:inherit;transition:border-color .15s,box-shadow .15s;"
               onmouseover="this.style.borderColor='#2271b1';this.style.boxShadow='0 1px 6px rgba(0,0,0,.08)'"
               onmouseout="this.style.borderColor='#dcdcde';this.style.boxShadow='none'">
                <span style="flex:0 0 44px;height:44px;background:#f0f6fc;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="dashicons <?php echo esc_attr( $info['icon'] ); ?>" style="font-size:22px;width:22px;height:22px;color:#2271b1;"></span>
                </span>
                <span style="flex:1;">
                    <strong style="display:block;font-size:14px;color:#1e1e1e;"><?php echo esc_html( $page->post_title ); ?></strong>
                    <span style="font-size:12px;color:#a7aaad;"><?php echo esc_html( $info['label'] ); ?> pagina</span>
                </span>
                <?php if ( $status === 'publish' ) : ?>
                    <span style="font-size:11px;font-weight:500;padding:3px 10px;border-radius:10px;background:#edfaef;color:#1a7a2e;">Gepubliceerd</span>
                <?php else : ?>
                    <span style="font-size:11px;font-weight:500;padding:3px 10px;border-radius:10px;background:#fef3e8;color:#996800;">Concept</span>
                <?php endif; ?>
                <span class="dashicons dashicons-arrow-right-alt2" style="color:#c3c4c7;"></span>
            </a>
        <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/* ==========================================================================
   DISABLE GUTENBERG — Classic editor for all Floru pages (like Teamleden)
   ========================================================================== */

add_filter( 'use_block_editor_for_post', 'floru_pages_disable_gutenberg', 10, 2 );
function floru_pages_disable_gutenberg( $use, $post ) {
    if ( $post && $post->post_type === 'page' && floru_is_floru_page( $post->ID ) ) {
        return false;
    }
    return $use;
}

/**
 * Enqueue media library scripts for Floru page edit screens.
 */
add_action( 'admin_enqueue_scripts', 'floru_pages_admin_media_scripts' );
function floru_pages_admin_media_scripts( $hook ) {
    if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
        return;
    }

    global $post;
    if ( ! $post || $post->post_type !== 'page' ) {
        return;
    }

    if ( floru_is_floru_page( $post->ID ) ) {
        wp_enqueue_media();
    }
}

/* ==========================================================================
   PREMIUM EDIT EXPERIENCE — badge, editor hiding, clean layout
   ========================================================================== */

/* ==========================================================================
   CLEAN UP META BOXES & EDITOR CSS
   ========================================================================== */

/**
 * Remove default boxes that clutter Floru pages + editor cosmetics.
 */
add_action( 'add_meta_boxes_page', 'floru_page_cleanup_meta_boxes', 99, 1 );
function floru_page_cleanup_meta_boxes( $post ) {
    if ( ! floru_is_floru_page( $post->ID ) ) {
        return;
    }
    // Remove slug, page attributes, comments — not needed.
    remove_meta_box( 'slugdiv',          'page', 'normal' );
    remove_meta_box( 'pageparentdiv',    'page', 'side' );
    remove_meta_box( 'commentsdiv',      'page', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
    remove_meta_box( 'postcustom',       'page', 'normal' );
    remove_meta_box( 'astra_settings_meta_box', 'page', 'side' );
}

/**
 * Admin CSS: clean up the classic editor for Floru pages.
 */
add_action( 'admin_head', 'floru_page_editor_css' );
function floru_page_editor_css() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'page' ) {
        return;
    }
    global $post;
    if ( ! $post || ! floru_is_floru_page( $post->ID ) ) {
        return;
    }

    $template = get_post_meta( $post->ID, '_wp_page_template', true );

    // Templates without a content editor (all fields are in meta boxes).
    $hide_editor = array(
        'templates/template-home.php',
        'templates/template-services.php',
        'templates/template-team.php',
        'templates/template-clients.php',
        'templates/template-contact.php',
    );
    ?>
    <style>
        /* ── Clean layout like Teamleden / Opdrachtgevers ── */
        #titlewrap { margin-bottom: 0; }
        #post-body-content { margin-bottom: 8px; }
        #normal-sortables .postbox .inside { padding: 12px; }
        #astra_settings_meta_box { display: none !important; }

        <?php if ( in_array( $template, $hide_editor, true ) ) : ?>
        /* Hide editor — this page uses only meta fields */
        #postdivrich, #wp-content-editor-container, #post-status-info, #wp-content-wrap {
            display: none !important;
        }
        <?php else : ?>
        /* Editor flows under the section header */
        #postdivrich { margin-top: 0; }
        <?php endif; ?>
    </style>
    <?php
}

add_filter( 'admin_body_class', 'floru_page_admin_body_class' );
function floru_page_admin_body_class( $classes ) {
    global $post;
    if ( ! $post || $post->post_type !== 'page' || ! floru_is_floru_page( $post->ID ) ) {
        return $classes;
    }
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    if ( $template === 'templates/template-about.php' ) {
        $classes .= ' floru-page-editor';
    }
    return $classes;
}

/* ==========================================================================
   MENU HIGHLIGHTING — "Floru Pagina's" stays active when editing a page
   ========================================================================== */

add_filter( 'parent_file', 'floru_pages_parent_file' );
function floru_pages_parent_file( $parent_file ) {
    global $post;
    if ( $post && $post->post_type === 'page' && floru_is_floru_page( $post->ID ) ) {
        return 'floru-pages';
    }
    return $parent_file;
}

add_filter( 'submenu_file', 'floru_pages_submenu_file', 10, 2 );
function floru_pages_submenu_file( $submenu_file, $parent_file ) {
    global $post;
    if ( $post && $post->post_type === 'page' && floru_is_floru_page( $post->ID ) ) {
        return 'post.php?post=' . $post->ID . '&action=edit';
    }
    return $submenu_file;
}

/* ==========================================================================
   HIDE DEFAULT "Pagina's" MENU — Floru pages replace it
   ========================================================================== */

add_action( 'admin_menu', 'floru_hide_default_pages_menu', 999 );
function floru_hide_default_pages_menu() {
    global $menu;
    if ( ! is_array( $menu ) ) {
        return;
    }
    foreach ( $menu as $key => $item ) {
        if ( isset( $item[2] ) && $item[2] === 'edit.php?post_type=page' ) {
            $menu[ $key ][4] = ( isset( $menu[ $key ][4] ) ? $menu[ $key ][4] . ' ' : '' ) . 'floru-hidden-menu';
            break;
        }
    }
}

add_action( 'admin_head', 'floru_hide_default_pages_menu_css' );
function floru_hide_default_pages_menu_css() {
    echo '<style>.floru-hidden-menu { display: none !important; }</style>';
}
