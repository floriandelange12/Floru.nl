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

    // Prefer a child-theme Inter file (InterVariable.woff2 from Inter 4.1),
    // with a bundled local fallback from Twenty Twenty-Four.
    $child_font    = wp_normalize_path( get_stylesheet_directory() . '/assets/fonts/InterVariable.woff2' );
    $fallback_font = wp_normalize_path( ABSPATH . 'wp-content/themes/twentytwentyfour/assets/fonts/inter/Inter-VariableFont_slnt,wght.woff2' );

    // Auto-copy: if the child-theme font is missing, copy it from the Inter 4.1
    // download or from Twenty Twenty-Four (one-time operation).
    if ( ! file_exists( $child_font ) ) {
        $sources = array(
            wp_normalize_path( ABSPATH . 'inter-4.1/inter-4.1/docs/font-files/InterVariable.woff2' ),
            $fallback_font,
        );
        foreach ( $sources as $source ) {
            if ( file_exists( $source ) && @copy( $source, $child_font ) ) {
                break;
            }
        }
    }

    if ( file_exists( $child_font ) || file_exists( $fallback_font ) ) {
        wp_enqueue_style(
            'floru-google-fonts',
            get_stylesheet_directory_uri() . '/assets/fonts/inter.css',
            array(),
            wp_get_theme()->get( 'Version' )
        );
    } else {
        wp_enqueue_style(
            'floru-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
            array(),
            null
        );
    }

    // Floru main JS — scroll animations, counter, smooth scroll
    wp_enqueue_script(
        'floru-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        array(),
        wp_get_theme()->get( 'Version' ),
        true
    );
}

/**
 * Shared list of Floru page templates.
 *
 * @return string[]
 */
function floru_get_template_slugs() {
    return array(
        'templates/template-home.php',
        'templates/template-about.php',
        'templates/template-services.php',
        'templates/template-team.php',
        'templates/template-clients.php',
        'templates/template-contact.php',
    );
}

/**
 * Canonical slug per Floru template.
 *
 * @return array<string, string>
 */
function floru_get_template_primary_slugs() {
    return array(
        'templates/template-home.php'     => 'home',
        'templates/template-about.php'    => 'about',
        'templates/template-services.php' => 'services',
        'templates/template-team.php'     => 'our-team',
        'templates/template-clients.php'  => 'clients',
        'templates/template-contact.php'  => 'contact',
    );
}

/**
 * Default public headings per Floru template.
 *
 * @return array<string, string>
 */
function floru_get_template_heading_defaults() {
    return array(
        'templates/template-home.php'     => 'Strategic Guidance for Defence and Security Markets',
        'templates/template-about.php'    => 'Our Modus Operandi',
        'templates/template-services.php' => 'How We Support Your Success',
        'templates/template-team.php'     => 'Meet the Team',
        'templates/template-clients.php'  => 'Clients & References',
        'templates/template-contact.php'  => 'Get in Touch',
    );
}

/**
 * Resolve the canonical WordPress page for a Floru template.
 *
 * @param string $template_file Template path relative to the child theme.
 * @return WP_Post|null
 */
function floru_get_canonical_page_for_template( $template_file ) {
    $primary_slugs = floru_get_template_primary_slugs();

    if ( isset( $primary_slugs[ $template_file ] ) ) {
        $page = get_page_by_path( $primary_slugs[ $template_file ] );
        if ( $page ) {
            return $page;
        }
    }

    if ( 'templates/template-team.php' === $template_file ) {
        $legacy_page = get_page_by_path( 'team' );
        if ( $legacy_page ) {
            return $legacy_page;
        }
    }

    $pages = get_pages(
        array(
            'meta_key'   => '_wp_page_template',
            'meta_value' => $template_file,
            'number'     => 1,
        )
    );

    return ! empty( $pages ) ? $pages[0] : null;
}

/**
 * Default public contact values used when Contact page meta is incomplete.
 *
 * @return array<string, string>
 */
function floru_get_contact_defaults() {
    return array(
        'email'     => 'r.pruijss@floru.nl',
        'phone'     => '+31 6 42 58 75 15',
        'phone_raw' => '+31642587515',
        'address'   => 'De klerkplan 10',
        'city'      => '2728 EH Zoetermeer',
    );
}

/**
 * Resolve public contact details from the canonical Contact page.
 *
 * Falls back to the same defaults the public Contact template exposes so
 * shared consumers such as the footer stay aligned even when specific meta
 * fields have not been filled in yet.
 *
 * @param int $page_id Optional contact page ID.
 * @return array<string, string>
 */
function floru_get_contact_details( $page_id = 0 ) {
    $defaults = floru_get_contact_defaults();
    $page_id  = (int) $page_id;

    if ( ! $page_id ) {
        $page    = floru_get_canonical_page_for_template( 'templates/template-contact.php' );
        $page_id = $page ? (int) $page->ID : 0;
    }

    $details = array(
        'email'     => $page_id ? floru_get_meta( $page_id, '_floru_contact_email', $defaults['email'] ) : $defaults['email'],
        'phone'     => $page_id ? floru_get_meta( $page_id, '_floru_contact_phone', $defaults['phone'] ) : $defaults['phone'],
        'phone_raw' => $page_id ? floru_get_meta( $page_id, '_floru_contact_phone_raw', $defaults['phone_raw'] ) : $defaults['phone_raw'],
        'address'   => $page_id ? floru_get_meta( $page_id, '_floru_contact_address', $defaults['address'] ) : $defaults['address'],
        'city'      => $page_id ? floru_get_meta( $page_id, '_floru_contact_city', $defaults['city'] ) : $defaults['city'],
    );

    if ( ! $details['phone_raw'] && $details['phone'] ) {
        $details['phone_raw'] = preg_replace( '/[^0-9+]+/', '', $details['phone'] );
    }

    return $details;
}

/**
 * Resolve the canonical Team page URL.
 *
 * Historically the team page existed under both 'team' and 'our-team' slugs.
 * Floru now treats the canonical template page as the source of truth.
 *
 * @return string
 */
function floru_get_team_url() {
    $page = floru_get_canonical_page_for_template( 'templates/template-team.php' );

    return $page ? get_permalink( $page ) : home_url( '/our-team/' );
}

/**
 * Normalize known Team URLs to the canonical Team page.
 *
 * @param string $url Source URL.
 * @return string
 */
function floru_normalize_team_url( $url = '' ) {
    $team_url = floru_get_team_url();
    $team_path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );

    if ( ! $url || in_array( $team_path, array( 'team', 'our-team', 'our-our-team' ), true ) ) {
        return $team_url;
    }

    return $url;
}

/**
 * Build a compact initials fallback for missing team/client imagery.
 *
 * @param string $text  Source label.
 * @param int    $limit Maximum initials to return.
 * @return string
 */
function floru_get_initials( $text, $limit = 2 ) {
    $text  = trim( wp_strip_all_tags( (string) $text ) );
    $words = preg_split( '/[\s\-]+/', $text, -1, PREG_SPLIT_NO_EMPTY );

    if ( empty( $words ) ) {
        return '';
    }

    $initials = '';
    foreach ( $words as $word ) {
        $initials .= function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $word, 0, 1 ) ) : strtoupper( substr( $word, 0, 1 ) );
        if ( strlen( $initials ) >= $limit ) {
            break;
        }
    }

    if ( ! $initials ) {
        $initials = function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $text, 0, $limit ) ) : strtoupper( substr( $text, 0, $limit ) );
    }

    return substr( $initials, 0, $limit );
}

/**
 * Reorder team collections for the public Floru presentation.
 *
 * The admin menu order remains the source of truth for content management,
 * while the public roster promotes Ruud first and Marina second.
 *
 * @param array    $items    Collection of team items.
 * @param callable $resolver Callback returning an array with optional slug/name keys.
 * @return array
 */
function floru_prioritize_team_collection( array $items, callable $resolver ) {
    if ( count( $items ) < 2 ) {
        return $items;
    }

    $priority_map = array(
        'ruud-de-pruyssenaere-de-la-woestijne' => 0,
        'marina-eppen-pruyssenaere' => 1,
    );

    $decorated = array();

    foreach ( $items as $index => $item ) {
        $resolved = (array) call_user_func( $resolver, $item );
        $slug     = isset( $resolved['slug'] ) ? sanitize_title( wp_strip_all_tags( (string) $resolved['slug'] ) ) : '';
        $name     = isset( $resolved['name'] ) ? sanitize_title( wp_strip_all_tags( (string) $resolved['name'] ) ) : '';
        $priority = 1000 + (int) $index;

        if ( $slug && isset( $priority_map[ $slug ] ) ) {
            $priority = $priority_map[ $slug ];
        } elseif ( $name && isset( $priority_map[ $name ] ) ) {
            $priority = $priority_map[ $name ];
        }

        $decorated[] = array(
            'priority' => $priority,
            'index'    => (int) $index,
            'item'     => $item,
        );
    }

    usort(
        $decorated,
        static function( $left, $right ) {
            if ( $left['priority'] === $right['priority'] ) {
                return $left['index'] <=> $right['index'];
            }

            return $left['priority'] <=> $right['priority'];
        }
    );

    return array_column( $decorated, 'item' );
}

/**
 * Return the meaningful Floru page heading for document titles.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function floru_get_contextual_page_title( $post_id = 0 ) {
    $post_id = $post_id ? (int) $post_id : get_queried_object_id();
    if ( ! $post_id ) {
        return '';
    }

    $template = get_page_template_slug( $post_id );
    $defaults = floru_get_template_heading_defaults();

    if ( 'templates/template-home.php' === $template ) {
        $default_title = isset( $defaults[ $template ] ) ? $defaults[ $template ] : floru_get_translated_post_title_raw( $post_id );

        return floru_get_meta( $post_id, '_floru_hero_heading', $default_title );
    }

    if ( in_array( $template, floru_get_template_slugs(), true ) ) {
        $default_title = isset( $defaults[ $template ] ) ? $defaults[ $template ] : floru_get_translated_post_title_raw( $post_id );

        return floru_get_meta( $post_id, '_floru_ph_heading', $default_title );
    }

    return floru_get_translated_post_title_raw( $post_id );
}

/**
 * Downshift stored rich-text headings when a section's hierarchy requires it.
 *
 * @param string $html HTML fragment.
 * @param string $from Source heading tag.
 * @param string $to   Target heading tag.
 * @return string
 */
function floru_normalize_heading_html( $html, $from = 'h4', $to = 'h3' ) {
    if ( ! $html || $from === $to ) {
        return $html;
    }

    $open_pattern  = sprintf( '#<%1$s(\\b[^>]*)>#i', preg_quote( $from, '#' ) );
    $close_pattern = sprintf( '#</%1$s>#i', preg_quote( $from, '#' ) );

    $html = preg_replace( $open_pattern, '<' . $to . '$1>', $html );

    return preg_replace( $close_pattern, '</' . $to . '>', $html );
}

// Add custom body classes
add_filter( 'body_class', 'floru_body_classes' );
function floru_body_classes( $classes ) {
    $classes[] = 'floru-site';
    $classes[] = 'floru-lang-' . floru_get_current_language();

    // Tell Astra to use page-builder mode (full-width, no flex sidebar layout)
    // so our template sections stack vertically instead of as flex items.
    if ( is_page_template( floru_get_template_slugs() ) || is_singular( 'floru_client' ) ) {
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

// Register custom image sizes (consistent portrait crop for team cards)
add_action( 'after_setup_theme', 'floru_register_image_sizes' );
function floru_register_image_sizes() {
    // 4:5 portrait, top-cropped so faces sit nicely in frame.
    add_image_size( 'floru-team-portrait', 720, 900, array( 'center', 'top' ) );
}

// Ensure we use GD for image editing on local Windows sites to avoid file lock issues preventing deletion.
add_filter( 'wp_image_editors', 'floru_use_gd_editor' );
function floru_use_gd_editor( $editors ) {
    $gd_editor = 'WP_Image_Editor_GD';
    $editors = array_diff( $editors, array( 'WP_Image_Editor_Imagick' ) );
    array_unshift( $editors, $gd_editor );
    return $editors;
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

    if ( ! isset( $icons[ $name ] ) ) {
        return '';
    }

    return preg_replace( '/^<svg\\b/', '<svg aria-hidden="true" focusable="false"', $icons[ $name ], 1 );
}

/**
 * Disable Astra's default page title on pages using Floru templates.
 */
add_filter( 'astra_the_title_enabled', 'floru_disable_default_title' );
function floru_disable_default_title( $enabled ) {
    if ( is_page_template( floru_get_template_slugs() ) ) {
        return false;
    }
    return $enabled;
}

/**
 * Disable Astra's default featured image on Floru template pages.
 */
add_filter( 'astra_featured_image_enabled', 'floru_disable_featured_image' );
function floru_disable_featured_image( $enabled ) {
    if ( is_page_template( floru_get_template_slugs() ) ) {
        return false;
    }
    return $enabled;
}

// Include frontend language handling
require_once get_stylesheet_directory() . '/inc/language.php';

// Include customizer defaults
require_once get_stylesheet_directory() . '/inc/customizer-defaults.php';

// Include custom post types (Team, Clients)
require_once get_stylesheet_directory() . '/inc/custom-post-types.php';

// Include Floru admin pages (sidebar menu, Gutenberg disable, premium page editor)
require_once get_stylesheet_directory() . '/inc/floru-admin-pages.php';

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

/**
 * JSON-LD Organization structured data.
 */
add_action( 'wp_head', 'floru_structured_data' );
function floru_structured_data() {
    if ( ! is_front_page() && ! is_page_template( floru_get_template_slugs() ) ) {
        return;
    }
    $logo = '';
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }
    $data = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => 'Floru Consultancy',
        'description' => floru_t( 'Strategic consultancy in defence and security. We help international companies navigate complex government markets, build relationships, and win tenders.' ),
        'url'         => home_url( '/' ),
        'email'       => 'info@floru.nl',
        'telephone'   => '+31642587515',
        'address'     => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'De klerkplan 10',
            'addressLocality' => 'Zoetermeer',
            'postalCode'      => '2728 EH',
            'addressCountry'  => 'NL',
        ),
        'sameAs'      => array(),
    );
    if ( $logo ) {
        $data['logo'] = $logo;
    }
    echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

/**
 * Open Graph and basic SEO meta tags.
 */
add_action( 'wp_head', 'floru_og_meta_tags', 5 );
function floru_og_meta_tags() {
    $title       = wp_get_document_title();
    $description = floru_t( 'Strategic consultancy in defence and security. Floru helps international companies navigate government markets, build stakeholder relationships, and win tenders in the Netherlands and Europe.' );
    $url         = is_front_page() ? home_url( '/' ) : get_permalink();
    $image       = '';

    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $image = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }

    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}

/**
 * Skip-to-content accessibility link.
 */
add_action( 'astra_body_top', 'floru_skip_to_content' );
function floru_skip_to_content() {
    echo '<a class="floru-skip-link" href="#content">' . esc_html( floru_t( 'Skip to content' ) ) . '</a>';
}

/**
 * Hide the duplicate Astra skip link (we use our own English one above).
 */
add_filter( 'astra_default_strings', 'floru_override_astra_strings' );
function floru_override_astra_strings( $defaults ) {
    $defaults['string-header-skip-link'] = '';
    return $defaults;
}
add_action( 'wp_head', 'floru_hide_astra_skip_link' );
function floru_hide_astra_skip_link() {
    echo '<style>a.skip-link.screen-reader-text:empty{display:none}</style>' . "\n";
}

/**
 * Normalize a few Astra UI strings through Floru's frontend language layer.
 */
add_filter( 'gettext', 'floru_force_english_astra_strings', 20, 3 );
function floru_force_english_astra_strings( $translation, $text, $domain ) {
    if ( 'astra' !== $domain ) {
        return $translation;
    }
    if ( in_array( $text, array( 'Scroll to Top', 'Main menu toggle', 'Menu Toggle' ), true ) ) {
        return floru_t( $text );
    }
    return $translation;
}

/**
 * Expose the selected frontend language in the HTML lang attribute.
 */
add_filter( 'language_attributes', 'floru_force_english_lang_attribute', 20 );
function floru_force_english_lang_attribute( $output ) {
    $locale = floru_get_current_locale();
    $lang   = floru_get_current_language();

    $output = preg_replace( '/lang="[^"]+"/i', 'lang="' . esc_attr( $locale ) . '"', $output, 1 );
    $output = preg_replace( '/xml:lang="[^"]+"/i', 'xml:lang="' . esc_attr( $lang ) . '"', $output, 1 );

    return $output;
}

/**
 * Remove "Contact" from the primary nav menu because we already
 * have the orange CONTACT button in the header (Astra button-1).
 */
add_filter( 'wp_nav_menu_objects', 'floru_remove_contact_from_menu', 10, 2 );
function floru_remove_contact_from_menu( $items, $args ) {
    $is_primary_menu = ! empty( $args->theme_location ) && in_array( $args->theme_location, array( 'primary', 'floru-primary' ), true );
    $is_mobile_menu  = ! empty( $args->menu_id ) && strpos( $args->menu_id, '-mobile' ) !== false;

    if ( ! $is_primary_menu && ! $is_mobile_menu ) {
        return $items;
    }

    $contact_page = get_page_by_path( 'contact' );
    if ( ! $contact_page ) {
        return $items;
    }
    foreach ( $items as $key => $item ) {
        if ( (int) $item->object_id === $contact_page->ID && $item->object === 'page' ) {
            unset( $items[ $key ] );
        }
    }
    return $items;
}

/**
 * Normalize Team menu links to the canonical Team page and keep the active
 * state coherent even while legacy routes are still present in the database.
 */
add_filter( 'wp_nav_menu_objects', 'floru_normalize_team_menu_urls', 20, 2 );
function floru_normalize_team_menu_urls( $items, $args ) {
    $team_page = floru_get_canonical_page_for_template( 'templates/template-team.php' );
    if ( ! $team_page ) {
        return $items;
    }

    $team_url        = get_permalink( $team_page );
    $is_team_context = is_page_template( 'templates/template-team.php' ) || is_page( $team_page->ID ) || is_page( 'team' ) || is_page( 'our-team' );

    foreach ( $items as $item ) {
        $item_template = '';
        if ( 'page' === $item->object && ! empty( $item->object_id ) ) {
            $item_template = get_post_meta( (int) $item->object_id, '_wp_page_template', true );
        }

        $item_path = trim( (string) wp_parse_url( $item->url, PHP_URL_PATH ), '/' );
        if ( 'templates/template-team.php' === $item_template || in_array( $item_path, array( 'team', 'our-team', 'our-our-team' ), true ) ) {
            $item->url = $team_url;

            if ( $is_team_context ) {
                $item->classes = array_values(
                    array_unique(
                        array_merge(
                            (array) $item->classes,
                            array( 'current-menu-item', 'current_page_item' )
                        )
                    )
                );
            }
        }
    }

    return $items;
}

/**
 * Redirect the legacy /team/ page to the canonical Team URL.
 */
add_action( 'template_redirect', 'floru_redirect_legacy_team_route' );
function floru_redirect_legacy_team_route() {
    if ( is_admin() || is_preview() || wp_doing_ajax() || ! is_page( 'team' ) ) {
        return;
    }

    $team_url       = floru_get_team_url();
    $canonical_path = trim( (string) wp_parse_url( $team_url, PHP_URL_PATH ), '/' );
    if ( 'team' === $canonical_path ) {
        return;
    }

    wp_safe_redirect( $team_url, 301 );
    exit;
}

/**
 * Append a styled CONTACT link at the bottom of the mobile nav menu.
 * The desktop header uses a separate Astra button component, but that
 * component is not present in the mobile header builder, so we add it here.
 */
add_filter( 'wp_nav_menu_items', 'floru_add_contact_to_mobile_menu', 10, 2 );
function floru_add_contact_to_mobile_menu( $items, $args ) {
    if ( ! empty( $args->menu_id ) && strpos( $args->menu_id, '-mobile' ) !== false ) {
        $contact_url = home_url( '/contact/' );
        $active      = is_page( 'contact' ) ? ' current-menu-item' : '';
        $items      .= '<li class="menu-item floru-mobile-contact-item' . $active . '">'
                     . '<a href="' . esc_url( $contact_url ) . '" class="menu-link floru-mobile-contact-link">' . esc_html( floru_t( 'Contact' ) ) . '</a>'
                     . '</li>';
    }
    return $items;
}

/**
 * Prevent Astra from outputting frontend Google Fonts.
 * Floru loads Inter locally and the remaining Astra font request is unnecessary.
 *
 * @param array $fonts Selected Astra Google fonts.
 * @return array
 */
add_filter( 'astra_google_fonts_selected', 'floru_disable_astra_google_fonts', 999 );
function floru_disable_astra_google_fonts( $fonts ) {
    if ( is_admin() ) {
        return $fonts;
    }

    return array();
}

/**
 * Ensure frontend document titles stay meaningful even when editor-facing page
 * titles diverge from the public Floru headings.
 */
add_filter( 'pre_get_document_title', 'floru_document_title' );
function floru_document_title( $title ) {
    if ( is_admin() ) {
        return $title;
    }

    $site_name = get_bloginfo( 'name' );
    if ( ! $site_name ) {
        $site_name = 'Floru Consultancy';
    }

    if ( is_front_page() || is_page_template( floru_get_template_slugs() ) ) {
        $page_title = floru_get_contextual_page_title();
        return $page_title ? $page_title . ' | ' . $site_name : $site_name;
    }

    if ( is_singular( 'floru_client' ) ) {
        return floru_get_translated_post_title_raw( get_the_ID() ) . ' | ' . floru_t( 'Clients' ) . ' | ' . $site_name;
    }

    if ( is_404() ) {
        return floru_t( 'Page Not Found' ) . ' | ' . $site_name;
    }

    return $title;
}

/**
 * Curated gallery images for client pages without videos.
 * Uses Unsplash CDN (free license, attribution appreciated).
 *
 * @param  string $slug Client post slug.
 * @return array  Array of image arrays with 'url', 'alt', 'credit' keys (empty if no images).
 */
function floru_get_client_gallery_images( $slug ) {
    $base = 'https://images.unsplash.com/photo-';
    $galleries = array(
        'dujardin' => array(
            array(
                'id'     => '1582139329536-e7284fece509',
                'alt'    => 'High-security vault lock mechanism',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
            array(
                'id'     => '1732384001863-59ecbb9dd827',
                'alt'    => 'Reinforced vault door with multiple locking bolts',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
            array(
                'id'     => '1707960190026-e0fb6f03ceae',
                'alt'    => 'Secured ATM terminal with physical protection',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
        ),
        'nu-security-consultancy' => array(
            array(
                'id'     => '1596835090344-b57279fac184',
                'alt'    => 'Security surveillance camera on building exterior',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
            array(
                'id'     => '1662638600476-d563fffbb072',
                'alt'    => 'Security operations center with monitoring screens',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
            array(
                'id'     => '1675627453084-505806a00406',
                'alt'    => 'Cybersecurity monitoring and threat analysis display',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
        ),
        'x-systems' => array(
            array(
                'id'     => '1626984260797-5372e4c668b3',
                'alt'    => 'Ultra-secure smartphone on dark surface',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
            array(
                'id'     => '1559819614-c5bdc6c7191e',
                'alt'    => 'Encrypted circuit board technology',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
            array(
                'id'     => '1580062329559-9d512477e711',
                'alt'    => 'Secure hardware encryption components',
                'credit' => 'Unsplash',
                'crop'   => '',
            ),
        ),
    );

    if ( ! isset( $galleries[ $slug ] ) ) {
        return array();
    }

    $images = array();
    foreach ( $galleries[ $slug ] as $img ) {
        $params = 'w=800&q=80&auto=format&fit=crop';
        if ( $img['crop'] ) {
            $params .= '&' . $img['crop'];
        }
        $images[] = array(
            'url'    => $base . $img['id'] . '?' . $params,
            'alt'    => $img['alt'],
            'credit' => $img['credit'],
        );
    }
    return $images;
}
