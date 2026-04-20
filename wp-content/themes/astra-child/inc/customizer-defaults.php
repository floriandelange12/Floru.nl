<?php
/**
 * Floru Customizer Defaults
 * Sets up Astra Customizer options programmatically.
 *
 * @package Astra-Child-Floru
 */

/**
 * Set Astra theme options for Floru branding.
 * Run once via WP-CLI or functions.php on first load.
 */
function floru_set_customizer_defaults() {

    // Only run once
    if ( get_option( 'floru_customizer_initialized' ) ) {
        return;
    }

    // Global colors
    $astra_settings = get_option( 'astra-settings', array() );

    // Typography
    $astra_settings['body-font-family']    = "'Inter', sans-serif";
    $astra_settings['body-font-weight']    = '400';
    $astra_settings['body-line-height']    = '1.75';
    $astra_settings['body-font-size']      = array(
        'desktop'      => 16,
        'tablet'       => 15,
        'mobile'       => 15,
        'desktop-unit' => 'px',
        'tablet-unit'  => 'px',
        'mobile-unit'  => 'px',
    );

    $astra_settings['headings-font-family'] = "'Inter', sans-serif";
    $astra_settings['headings-font-weight'] = '600';

    // Colors
    $astra_settings['text-color']           = '#4A5568';
    $astra_settings['theme-color']          = '#C9913B';
    $astra_settings['link-color']           = '#1B2A4A';
    $astra_settings['link-h-color']         = '#C9913B';
    $astra_settings['heading-base-color']   = '#1B2A4A';

    // Container width
    $astra_settings['site-content-width'] = 1180;

    // Header
    $astra_settings['header-bg-obj-responsive'] = array(
        'desktop' => array(
            'background-color' => '#FFFFFF',
        ),
    );

    // Footer
    $astra_settings['footer-bg-obj'] = array(
        'background-color' => '#0F1B33',
    );
    $astra_settings['footer-color'] = 'rgba(255,255,255,0.6)';

    // Disable title on pages (we handle it in templates)
    $astra_settings['ast-dynamic-single-page-title'] = false;

    // Header Button 1 — contact button in top-right
    $astra_settings['header-button1-text']            = 'Contact';
    $astra_settings['header-button1-link-option']     = array(
        'url'      => '/contact/',
        'new_tab'  => false,
        'link_rel' => '',
    );
    $astra_settings['header-button1-font-size']       = array(
        'desktop'      => 13,
        'tablet'       => 13,
        'mobile'       => 13,
        'desktop-unit' => 'px',
        'tablet-unit'  => 'px',
        'mobile-unit'  => 'px',
    );
    $astra_settings['header-button1-font-weight']     = '600';
    $astra_settings['header-button1-text-color']      = array( 'desktop' => '#1B2A4A', 'tablet' => '', 'mobile' => '' );
    $astra_settings['header-button1-back-color']      = array( 'desktop' => '#C9913B', 'tablet' => '', 'mobile' => '' );
    $astra_settings['header-button1-text-h-color']    = array( 'desktop' => '#1B2A4A', 'tablet' => '', 'mobile' => '' );
    $astra_settings['header-button1-back-h-color']    = array( 'desktop' => '#D4A54A', 'tablet' => '', 'mobile' => '' );
    $astra_settings['header-button1-border-radius']   = 4;
    $astra_settings['header-button1-padding']         = array(
        'desktop' => array( 'top' => 12, 'right' => 28, 'bottom' => 12, 'left' => 28 ),
    );

    // Header Builder layout — desktop
    $astra_settings['header-desktop-items'] = array(
        'primary' => array(
            'primary_left'   => array( 'logo' ),
            'primary_center' => array(),
            'primary_right'  => array( 'menu-1' ),
        ),
    );

    // Header Builder layout — mobile
    $astra_settings['header-mobile-items'] = array(
        'popup'   => array(
            'popup_content' => array( 'menu-1' ),
        ),
        'primary' => array(
            'primary_left'   => array( 'logo' ),
            'primary_center' => array(),
            'primary_right'  => array( 'mobile-trigger' ),
        ),
    );

    update_option( 'astra-settings', $astra_settings );
    update_option( 'floru_customizer_initialized', true );
}
add_action( 'after_switch_theme', 'floru_set_customizer_defaults', 40 );

/**
 * Ensure Astra Header Builder and button are correctly configured.
 * Runs once on admin_init when settings are missing.
 */
function floru_ensure_header_settings() {
    $astra_settings = get_option( 'astra-settings', array() );
    $changed        = false;

    // Ensure header button link
    $current_link = isset( $astra_settings['header-button1-link-option']['url'] )
        ? $astra_settings['header-button1-link-option']['url']
        : '';

    if ( $current_link !== '/contact/' ) {
        $astra_settings['header-button1-text']        = 'Contact';
        $astra_settings['header-button1-link-option'] = array(
            'url'      => '/contact/',
            'new_tab'  => false,
            'link_rel' => '',
        );
        $changed = true;
    }

    // Ensure mobile header builder layout has logo + trigger
    $mobile_left = isset( $astra_settings['header-mobile-items']['primary']['primary_left'] )
        ? $astra_settings['header-mobile-items']['primary']['primary_left']
        : array();

    if ( ! in_array( 'logo', $mobile_left, true ) ) {
        $astra_settings['header-mobile-items'] = array(
            'popup'   => array(
                'popup_content' => array( 'menu-1' ),
            ),
            'primary' => array(
                'primary_left'   => array( 'logo' ),
                'primary_center' => array(),
                'primary_right'  => array( 'mobile-trigger' ),
            ),
        );
        $changed = true;
    }

    if ( $changed ) {
        update_option( 'astra-settings', $astra_settings );
    }
}
add_action( 'admin_init', 'floru_ensure_header_settings' );

/**
 * Filter astra-settings at runtime to guarantee mobile header has logo + trigger.
 */
function floru_filter_astra_settings( $settings ) {
    if ( ! is_array( $settings ) ) {
        return $settings;
    }

    $settings['header-button1-text'] = 'Contact';

    $mobile_left = isset( $settings['header-mobile-items']['primary']['primary_left'] )
        ? $settings['header-mobile-items']['primary']['primary_left']
        : array();

    if ( ! in_array( 'logo', $mobile_left, true ) ) {
        $settings['header-mobile-items'] = array(
            'popup'   => array(
                'popup_content' => array( 'menu-1' ),
            ),
            'primary' => array(
                'primary_left'   => array( 'logo' ),
                'primary_center' => array(),
                'primary_right'  => array( 'mobile-trigger' ),
            ),
        );
    }

    return $settings;
}
add_filter( 'option_astra-settings', 'floru_filter_astra_settings' );

/**
 * Also filter via Astra's own option getter.
 */
function floru_filter_mobile_header_items( $value ) {
    if ( ! is_array( $value ) || empty( $value['primary']['primary_left'] ) || ! in_array( 'logo', $value['primary']['primary_left'], true ) ) {
        return array(
            'popup'   => array(
                'popup_content' => array( 'menu-1' ),
            ),
            'primary' => array(
                'primary_left'   => array( 'logo' ),
                'primary_center' => array(),
                'primary_right'  => array( 'mobile-trigger' ),
            ),
        );
    }
    return $value;
}
add_filter( 'astra_get_option_header-mobile-items', 'floru_filter_mobile_header_items' );
