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

    update_option( 'astra-settings', $astra_settings );
    update_option( 'floru_customizer_initialized', true );
}
add_action( 'after_setup_theme', 'floru_set_customizer_defaults' );
