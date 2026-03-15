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

// Add custom body classes
add_filter( 'body_class', 'floru_body_classes' );
function floru_body_classes( $classes ) {
    $classes[] = 'floru-site';

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
        'description' => 'Strategic consultancy in defence and security. We help international companies navigate government markets, build relationships, and win tenders.',
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
    $description = 'Strategic consultancy in defence and security. Floru helps international companies navigate government markets, build stakeholder relationships, and win tenders in the Netherlands and Europe.';
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
    echo '<a class="floru-skip-link" href="#content">Skip to content</a>';
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
 * Force English for Astra UI strings that get translated to Dutch.
 */
add_filter( 'gettext', 'floru_force_english_astra_strings', 20, 3 );
function floru_force_english_astra_strings( $translation, $text, $domain ) {
    if ( 'astra' !== $domain ) {
        return $translation;
    }
    if ( 'Scroll to Top' === $text ) {
        return 'Scroll to Top';
    }
    if ( 'Main menu toggle' === $text ) {
        return 'Main menu toggle';
    }
    if ( 'Menu Toggle' === $text ) {
        return 'Menu Toggle';
    }
    return $translation;
}

/**
 * Override the HTML lang attribute from nl-NL to en (site content is English).
 */
add_filter( 'language_attributes', 'floru_force_english_lang_attribute', 20 );
function floru_force_english_lang_attribute( $output ) {
    return str_replace( 'lang="nl-NL"', 'lang="en"', $output );
}

/**
 * Remove "Contact" from the primary nav menu because we already
 * have the orange CONTACT button in the header (Astra button-1).
 */
add_filter( 'wp_nav_menu_objects', 'floru_remove_contact_from_menu', 10, 2 );
function floru_remove_contact_from_menu( $items, $args ) {
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
                     . '<a href="' . esc_url( $contact_url ) . '" class="menu-link floru-mobile-contact-link">Contact</a>'
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
