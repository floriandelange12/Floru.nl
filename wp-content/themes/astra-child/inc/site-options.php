<?php
/**
 * Floru Site Options
 * Adds a settings page for footer and site-wide content.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'floru_site_options_menu' );
function floru_site_options_menu() {
    add_menu_page(
        'Floru Settings',
        'Floru Settings',
        'manage_options',
        'floru-settings',
        'floru_site_options_page',
        'dashicons-admin-settings',
        80
    );
}

add_action( 'admin_init', 'floru_site_options_register' );
function floru_site_options_register() {
    register_setting( 'floru_site_options', 'floru_footer_description', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    register_setting( 'floru_site_options', 'floru_footer_email', array( 'sanitize_callback' => 'sanitize_email' ) );
    register_setting( 'floru_site_options', 'floru_footer_tagline', array( 'sanitize_callback' => 'sanitize_text_field' ) );
}

function floru_site_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Floru Site Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'floru_site_options' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="floru_footer_description">Footer Description</label></th>
                    <td><textarea id="floru_footer_description" name="floru_footer_description" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'floru_footer_description', '' ) ); ?></textarea>
                    <p class="description">Short company description shown in the footer.</p></td>
                </tr>
                <tr>
                    <th><label for="floru_footer_email">Footer Email</label></th>
                    <td><input type="email" id="floru_footer_email" name="floru_footer_email" value="<?php echo esc_attr( get_option( 'floru_footer_email', '' ) ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="floru_footer_tagline">Footer Tagline</label></th>
                    <td><input type="text" id="floru_footer_tagline" name="floru_footer_tagline" value="<?php echo esc_attr( get_option( 'floru_footer_tagline', '' ) ); ?>" class="regular-text">
                    <p class="description">Shown at the bottom of the footer, e.g. "Defence &amp; Security Consultancy".</p></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
