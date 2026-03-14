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
        'Floru Instellingen',
        'Floru Instellingen',
        'manage_options',
        'floru-settings',
        'floru_site_options_page',
        'dashicons-admin-settings',
        27
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
    <div class="wrap" style="max-width: 700px;">
        <h1>Floru Instellingen</h1>
        <p style="color: #50575e; font-size: 14px; margin: 4px 0 24px;">Beheer hier de algemene site-instellingen zoals footer teksten.</p>
        <form method="post" action="options.php">
            <?php settings_fields( 'floru_site_options' ); ?>
            <div class="floru-mb" style="background: #fff; border: 1px solid #dcdcde; border-radius: 8px; padding: 20px 24px;">
                <div class="floru-mb-field" style="margin-bottom: 16px;">
                    <label class="floru-mb-label" for="floru_footer_description" style="display:block;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;color:#50575e;">Footer omschrijving</label>
                    <textarea id="floru_footer_description" name="floru_footer_description" rows="3" class="widefat" style="border-radius:4px;"><?php echo esc_textarea( get_option( 'floru_footer_description', '' ) ); ?></textarea>
                    <span style="display:block;font-size:12px;color:#a7aaad;margin-top:4px;">Korte bedrijfsomschrijving getoond in de footer.</span>
                </div>
                <div style="display:flex;gap:16px;margin-bottom:16px;">
                    <div style="flex:1;">
                        <label class="floru-mb-label" for="floru_footer_email" style="display:block;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;color:#50575e;">Footer e-mailadres</label>
                        <input type="email" id="floru_footer_email" name="floru_footer_email" value="<?php echo esc_attr( get_option( 'floru_footer_email', '' ) ); ?>" class="widefat" style="border-radius:4px;">
                    </div>
                    <div style="flex:1;">
                        <label class="floru-mb-label" for="floru_footer_tagline" style="display:block;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;color:#50575e;">Footer tagline</label>
                        <input type="text" id="floru_footer_tagline" name="floru_footer_tagline" value="<?php echo esc_attr( get_option( 'floru_footer_tagline', '' ) ); ?>" class="widefat" style="border-radius:4px;">
                        <span style="display:block;font-size:12px;color:#a7aaad;margin-top:4px;">Wordt onderaan de footer getoond, bijv. "Defence &amp; Security Consultancy".</span>
                    </div>
                </div>
            </div>
            <?php submit_button( 'Opslaan' ); ?>
        </form>
    </div>
    <?php
}
