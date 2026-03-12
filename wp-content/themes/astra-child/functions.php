<?php
/**
 * Astra Child Theme - Floru
 * Strategic Consultancy in Defence & Security
 *
 * @package Astra-Child-Floru
 */

// Enqueue parent and child theme styles
add_action( 'wp_enqueue_scripts', 'floru_enqueue_styles', 15 );
function floru_enqueue_styles() {
    // Parent theme style
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'astra' )->get( 'Version' )
    );

    // Child theme style
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'astra-parent-style' ),
        wp_get_theme()->get( 'Version' )
    );

    // Google Fonts – Inter
    wp_enqueue_style(
        'floru-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

// Add custom body classes
add_filter( 'body_class', 'floru_body_classes' );
function floru_body_classes( $classes ) {
    $classes[] = 'floru-site';

    // Tell Astra to use page-builder mode (full-width, no flex sidebar layout)
    // so our template sections stack vertically instead of as flex items.
    if ( is_page_template( array(
        'templates/template-home.php',
        'templates/template-about.php',
        'templates/template-services.php',
        'templates/template-team.php',
        'templates/template-clients.php',
        'templates/template-contact.php',
    ) ) ) {
        $classes[] = 'ast-page-builder-template';
    }

    return $classes;
}

// Custom page templates directory support
add_filter( 'theme_page_templates', 'floru_add_page_templates' );
function floru_add_page_templates( $templates ) {
    $templates['templates/template-home.php']    = 'Floru — Home';
    $templates['templates/template-about.php']   = 'Floru — About';
    $templates['templates/template-services.php'] = 'Floru — Services';
    $templates['templates/template-team.php']    = 'Floru — Team';
    $templates['templates/template-clients.php'] = 'Floru — Clients';
    $templates['templates/template-contact.php'] = 'Floru — Contact';
    return $templates;
}

// Resolve template paths from child theme
add_filter( 'template_include', 'floru_template_include' );
function floru_template_include( $template ) {
    $page_template = get_page_template_slug();
    if ( $page_template ) {
        $child_template = get_stylesheet_directory() . '/' . $page_template;
        if ( file_exists( $child_template ) ) {
            return $child_template;
        }
    }
    return $template;
}

// Register custom navigation menus
add_action( 'after_setup_theme', 'floru_register_menus' );
function floru_register_menus() {
    register_nav_menus( array(
        'floru-primary'  => __( 'Floru Primary Menu', 'astra-child-floru' ),
        'floru-footer'   => __( 'Floru Footer Menu', 'astra-child-floru' ),
    ) );
}

// Custom excerpt length
add_filter( 'excerpt_length', 'floru_excerpt_length' );
function floru_excerpt_length( $length ) {
    return 24;
}

// Custom excerpt more text
add_filter( 'excerpt_more', 'floru_excerpt_more' );
function floru_excerpt_more( $more ) {
    return '&hellip;';
}

// SVG upload: use the "Safe SVG" plugin if SVG support is needed.
// Enabling SVG via upload_mimes without sanitization is a security risk.

/**
 * Helper: Render an inline SVG icon by name.
 * Keeps icons in code to avoid external dependencies.
 */
function floru_icon( $name ) {
    $icons = array(
        'briefcase'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
        'users'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'file-text'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        'shield'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
        'globe'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
        'target'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
        'award'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>',
        'handshake'    => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"/></svg>',
        'check-circle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'map-pin'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'mail'         => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'phone'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'arrow-right'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>',
        'trending-up'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
    );

    return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Disable Astra's default page title on pages using Floru templates.
 */
add_filter( 'astra_the_title_enabled', 'floru_disable_default_title' );
function floru_disable_default_title( $enabled ) {
    if ( is_page_template( array(
        'templates/template-home.php',
        'templates/template-about.php',
        'templates/template-services.php',
        'templates/template-team.php',
        'templates/template-clients.php',
        'templates/template-contact.php',
    ) ) ) {
        return false;
    }
    return $enabled;
}

/**
 * Disable Astra's default featured image on Floru template pages.
 */
add_filter( 'astra_featured_image_enabled', 'floru_disable_featured_image' );
function floru_disable_featured_image( $enabled ) {
    if ( is_page_template( array(
        'templates/template-home.php',
        'templates/template-about.php',
        'templates/template-services.php',
        'templates/template-team.php',
        'templates/template-clients.php',
        'templates/template-contact.php',
    ) ) ) {
        return false;
    }
    return $enabled;
}

// Include customizer defaults
require_once get_stylesheet_directory() . '/inc/customizer-defaults.php';

// Include custom post types (Team, Clients)
require_once get_stylesheet_directory() . '/inc/custom-post-types.php';

// Include page meta boxes (editable fields for all page templates)
require_once get_stylesheet_directory() . '/inc/page-meta-boxes.php';

// Include site options (footer settings)
require_once get_stylesheet_directory() . '/inc/site-options.php';

// Include content migration (populates editable fields with existing content — runs once)
require_once get_stylesheet_directory() . '/inc/content-migration.php';

// Include page setup (auto-creates required pages on first load)
require_once get_stylesheet_directory() . '/inc/page-setup.php';

// Include menu setup (auto-creates navigation menu on first load)
require_once get_stylesheet_directory() . '/inc/menu-setup.php';
