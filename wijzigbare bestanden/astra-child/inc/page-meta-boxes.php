<?php
/**
 * Floru Page Meta Boxes
 * Adds editable custom fields to each Floru page template.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Helper: Get a page meta value with fallback.
 */
function floru_get_meta( $post_id, $key, $default = '' ) {
    $value = get_post_meta( $post_id, $key, true );
    return ( $value !== '' && $value !== false ) ? $value : $default;
}

/**
 * Register meta boxes conditionally based on the page's assigned template.
 * Uses the add_meta_boxes_page hook which passes the $post object,
 * so we can check the template in PHP — works in both Classic and Gutenberg editors.
 */
add_action( 'add_meta_boxes_page', 'floru_page_meta_boxes', 10, 1 );
function floru_page_meta_boxes( $post ) {
    $template = get_post_meta( $post->ID, '_wp_page_template', true );

    switch ( $template ) {
        case 'templates/template-home.php':
            add_meta_box( 'floru_home_hero',     'Home — Hero Section',       'floru_home_hero_render',     'page', 'normal', 'high' );
            add_meta_box( 'floru_home_intro',    'Home — Intro Section',      'floru_home_intro_render',    'page', 'normal', 'high' );
            add_meta_box( 'floru_home_stats',    'Home — Stats Band',         'floru_home_stats_render',    'page', 'normal', 'high' );
            add_meta_box( 'floru_home_services', 'Home — Services Preview',   'floru_home_services_render', 'page', 'normal', 'high' );
            add_meta_box( 'floru_home_why',      'Home — Why Floru Section',  'floru_home_why_render',      'page', 'normal', 'high' );
            add_meta_box( 'floru_home_team',     'Home — Team Preview',       'floru_home_team_render',     'page', 'normal', 'default' );
            add_meta_box( 'floru_home_clients',  'Home — Clients Band',       'floru_home_clients_render',  'page', 'normal', 'default' );
            add_meta_box( 'floru_home_cta',      'Home — CTA Section',        'floru_home_cta_render',      'page', 'normal', 'default' );
            break;

        case 'templates/template-about.php':
            add_meta_box( 'floru_page_header_box', 'Page Header',             'floru_page_header_render',   'page', 'normal', 'high' );
            add_meta_box( 'floru_about_intro',     'About — Intro Section',   'floru_about_intro_render',   'page', 'normal', 'high' );
            add_meta_box( 'floru_about_approach',  'About — Approach Steps',  'floru_about_approach_render', 'page', 'normal', 'high' );
            add_meta_box( 'floru_about_values',    'About — Core Values',     'floru_about_values_render',  'page', 'normal', 'default' );
            add_meta_box( 'floru_page_cta_box',    'Page CTA Section',        'floru_page_cta_box_render',  'page', 'normal', 'default' );
            break;

        case 'templates/template-services.php':
            add_meta_box( 'floru_page_header_box', 'Page Header',             'floru_page_header_render',   'page', 'normal', 'high' );
            add_meta_box( 'floru_services_items',  'Services — Service Sections', 'floru_services_items_render', 'page', 'normal', 'high' );
            add_meta_box( 'floru_page_cta_box',    'Page CTA Section',        'floru_page_cta_box_render',  'page', 'normal', 'default' );
            break;

        case 'templates/template-team.php':
            add_meta_box( 'floru_page_header_box', 'Page Header',             'floru_page_header_render',   'page', 'normal', 'high' );
            add_meta_box( 'floru_page_cta_box',    'Page CTA Section',        'floru_page_cta_box_render',  'page', 'normal', 'default' );
            break;

        case 'templates/template-clients.php':
            add_meta_box( 'floru_page_header_box', 'Page Header',             'floru_page_header_render',   'page', 'normal', 'high' );
            add_meta_box( 'floru_page_cta_box',    'Page CTA Section',        'floru_page_cta_box_render',  'page', 'normal', 'default' );
            break;

        case 'templates/template-contact.php':
            add_meta_box( 'floru_page_header_box', 'Page Header',             'floru_page_header_render',   'page', 'normal', 'high' );
            add_meta_box( 'floru_contact_info',    'Contact — Contact Details', 'floru_contact_info_render', 'page', 'normal', 'high' );
            break;
    }
}

/* ==========================================================================
   HOME PAGE META BOXES
   ========================================================================== */

function floru_home_hero_render( $post ) {
    wp_nonce_field( 'floru_page_meta_nonce', 'floru_page_meta_nonce' );
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Label</label></th>
            <td><input type="text" name="floru_hero_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_label' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Heading</label></th>
            <td><input type="text" name="floru_hero_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_hero_description" rows="3" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hero_description' ) ); ?></textarea></td></tr>
        <tr><th><label>Button 1 Text</label></th>
            <td><input type="text" name="floru_hero_btn1_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn1_text' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Button 1 URL</label></th>
            <td><input type="text" name="floru_hero_btn1_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn1_url' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Button 2 Text</label></th>
            <td><input type="text" name="floru_hero_btn2_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn2_text' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Button 2 URL</label></th>
            <td><input type="text" name="floru_hero_btn2_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn2_url' ) ); ?>" class="regular-text"></td></tr>
    </table>
    <?php
}

function floru_home_intro_render( $post ) {
    $p = $post->ID;
    $img = floru_get_meta( $p, '_floru_intro_image' );
    ?>
    <table class="form-table">
        <tr><th><label>Label</label></th>
            <td><input type="text" name="floru_intro_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Heading</label></th>
            <td><input type="text" name="floru_intro_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Paragraph 1</label></th>
            <td><textarea name="floru_intro_text1" rows="3" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_intro_text1' ) ); ?></textarea></td></tr>
        <tr><th><label>Paragraph 2</label></th>
            <td><textarea name="floru_intro_text2" rows="3" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_intro_text2' ) ); ?></textarea></td></tr>
        <tr><th><label>Button Text</label></th>
            <td><input type="text" name="floru_intro_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_btn_text' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Button URL</label></th>
            <td><input type="text" name="floru_intro_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_btn_url' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Image URL</label></th>
            <td><input type="text" name="floru_intro_image" value="<?php echo esc_attr( $img ); ?>" class="large-text" id="floru_intro_image">
            <p class="description">Enter an image URL, or use the Featured Image for this page.</p></td></tr>
    </table>
    <?php
}

function floru_home_stats_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
        <tr><th><label>Stat <?php echo $i; ?> Number</label></th>
            <td><input type="text" name="floru_stat<?php echo $i; ?>_number" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat' . $i . '_number' ) ); ?>" class="regular-text" placeholder="e.g. 20+"></td></tr>
        <tr><th><label>Stat <?php echo $i; ?> Label</label></th>
            <td><input type="text" name="floru_stat<?php echo $i; ?>_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat' . $i . '_label' ) ); ?>" class="regular-text" placeholder="e.g. Years of Experience"></td></tr>
        <?php endfor; ?>
    </table>
    <?php
}

function floru_home_services_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Section Label</label></th>
            <td><input type="text" name="floru_hsvc_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Section Heading</label></th>
            <td><input type="text" name="floru_hsvc_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Section Description</label></th>
            <td><textarea name="floru_hsvc_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hsvc_description' ) ); ?></textarea></td></tr>
    </table>
    <hr>
    <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
    <h4>Service Card <?php echo $i; ?></h4>
    <table class="form-table">
        <tr><th><label>Title</label></th>
            <td><input type="text" name="floru_hsvc<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc' . $i . '_title' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_hsvc<?php echo $i; ?>_desc" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hsvc' . $i . '_desc' ) ); ?></textarea></td></tr>
        <tr><th><label>Icon name</label></th>
            <td><input type="text" name="floru_hsvc<?php echo $i; ?>_icon" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc' . $i . '_icon' ) ); ?>" class="regular-text">
            <p class="description">Available: trending-up, users, file-text, shield, globe, target, award, briefcase, check-circle</p></td></tr>
        <tr><th><label>Link URL</label></th>
            <td><input type="text" name="floru_hsvc<?php echo $i; ?>_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc' . $i . '_url' ) ); ?>" class="regular-text"></td></tr>
    </table>
    <?php endfor;
}

function floru_home_why_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Section Label</label></th>
            <td><input type="text" name="floru_why_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Heading</label></th>
            <td><input type="text" name="floru_why_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_why_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_why_description' ) ); ?></textarea></td></tr>
    </table>
    <hr>
    <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
    <h4>Trust Item <?php echo $i; ?></h4>
    <table class="form-table">
        <tr><th><label>Title</label></th>
            <td><input type="text" name="floru_why<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why' . $i . '_title' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_why<?php echo $i; ?>_desc" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_why' . $i . '_desc' ) ); ?></textarea></td></tr>
        <tr><th><label>Icon name</label></th>
            <td><input type="text" name="floru_why<?php echo $i; ?>_icon" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why' . $i . '_icon' ) ); ?>" class="regular-text">
            <p class="description">Available: shield, globe, target, check-circle, briefcase, users, award</p></td></tr>
    </table>
    <?php endfor;
}

function floru_home_team_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Section Label</label></th>
            <td><input type="text" name="floru_hteam_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Heading</label></th>
            <td><input type="text" name="floru_hteam_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_hteam_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hteam_description' ) ); ?></textarea></td></tr>
        <tr><th><label>Button Text</label></th>
            <td><input type="text" name="floru_hteam_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_btn_text' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Button URL</label></th>
            <td><input type="text" name="floru_hteam_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_btn_url' ) ); ?>" class="regular-text"></td></tr>
    </table>
    <p class="description">Team members shown here are pulled automatically from the Team Members post type (latest 3, ordered by menu order).</p>
    <?php
}

function floru_home_clients_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Label Text</label></th>
            <td><input type="text" name="floru_hclients_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hclients_label' ) ); ?>" class="large-text"></td></tr>
    </table>
    <p class="description">Client logos are pulled automatically from the Clients post type.</p>
    <?php
}

function floru_home_cta_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Heading</label></th>
            <td><input type="text" name="floru_hcta_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hcta_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_hcta_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hcta_description' ) ); ?></textarea></td></tr>
        <tr><th><label>Button Text</label></th>
            <td><input type="text" name="floru_hcta_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hcta_btn_text' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Button URL</label></th>
            <td><input type="text" name="floru_hcta_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hcta_btn_url' ) ); ?>" class="regular-text"></td></tr>
    </table>
    <?php
}

/* ==========================================================================
   SHARED PAGE HEADER & CTA META BOXES (About, Services, Team, Clients, Contact)
   ========================================================================== */

function floru_page_header_render( $post ) {
    wp_nonce_field( 'floru_page_meta_nonce', 'floru_page_meta_nonce' );
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Label</label></th>
            <td><input type="text" name="floru_ph_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Heading</label></th>
            <td><input type="text" name="floru_ph_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_ph_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_ph_description' ) ); ?></textarea></td></tr>
    </table>
    <?php
}

function floru_page_cta_box_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>CTA Heading</label></th>
            <td><input type="text" name="floru_pcta_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>CTA Description</label></th>
            <td><textarea name="floru_pcta_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_pcta_description' ) ); ?></textarea></td></tr>
        <tr><th><label>CTA Button Text</label></th>
            <td><input type="text" name="floru_pcta_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_btn_text' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>CTA Button URL</label></th>
            <td><input type="text" name="floru_pcta_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_btn_url' ) ); ?>" class="regular-text"></td></tr>
    </table>
    <?php
}

/* ==========================================================================
   ABOUT PAGE META BOXES
   ========================================================================== */

function floru_about_intro_render( $post ) {
    $p = $post->ID;
    ?>
    <p class="description">The About intro text is pulled from the page's main content (the WordPress editor above). The image uses the page's Featured Image. The fields below are for the "Our Story" label/heading.</p>
    <table class="form-table">
        <tr><th><label>Intro Label</label></th>
            <td><input type="text" name="floru_about_intro_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_about_intro_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Intro Heading</label></th>
            <td><input type="text" name="floru_about_intro_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_about_intro_heading' ) ); ?>" class="large-text"></td></tr>
    </table>
    <?php
}

function floru_about_approach_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Section Label</label></th>
            <td><input type="text" name="floru_approach_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_approach_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Section Heading</label></th>
            <td><input type="text" name="floru_approach_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_approach_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Section Description</label></th>
            <td><textarea name="floru_approach_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_approach_description' ) ); ?></textarea></td></tr>
    </table>
    <hr>
    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
    <h4>Step <?php echo $i; ?></h4>
    <table class="form-table">
        <tr><th><label>Title</label></th>
            <td><input type="text" name="floru_step<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_step' . $i . '_title' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_step<?php echo $i; ?>_desc" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_step' . $i . '_desc' ) ); ?></textarea></td></tr>
    </table>
    <?php endfor;
}

function floru_about_values_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Section Label</label></th>
            <td><input type="text" name="floru_values_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_values_label' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Section Heading</label></th>
            <td><input type="text" name="floru_values_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_values_heading' ) ); ?>" class="large-text"></td></tr>
    </table>
    <hr>
    <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
    <h4>Value <?php echo $i; ?></h4>
    <table class="form-table">
        <tr><th><label>Title</label></th>
            <td><input type="text" name="floru_value<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_value' . $i . '_title' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Description</label></th>
            <td><textarea name="floru_value<?php echo $i; ?>_desc" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_value' . $i . '_desc' ) ); ?></textarea></td></tr>
        <tr><th><label>Icon name</label></th>
            <td><input type="text" name="floru_value<?php echo $i; ?>_icon" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_value' . $i . '_icon' ) ); ?>" class="regular-text">
            <p class="description">Available: shield, target, users, globe, briefcase, check-circle, award</p></td></tr>
    </table>
    <?php endfor;
}

/* ==========================================================================
   SERVICES PAGE META BOXES
   ========================================================================== */

function floru_services_items_render( $post ) {
    $p = $post->ID;
    ?>
    <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
    <h3>Service <?php echo $i; ?></h3>
    <table class="form-table">
        <tr><th><label>Label</label></th>
            <td><input type="text" name="floru_svc<?php echo $i; ?>_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_svc' . $i . '_label' ) ); ?>" class="regular-text" placeholder="e.g. Service 01"></td></tr>
        <tr><th><label>Title</label></th>
            <td><input type="text" name="floru_svc<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_svc' . $i . '_title' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Description (HTML allowed)</label></th>
            <td><textarea name="floru_svc<?php echo $i; ?>_desc" rows="6" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_svc' . $i . '_desc' ) ); ?></textarea>
            <p class="description">You can use &lt;p&gt;, &lt;h4&gt;, &lt;ul&gt;, &lt;li&gt; tags.</p></td></tr>
        <tr><th><label>Image URL</label></th>
            <td><input type="text" name="floru_svc<?php echo $i; ?>_image" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_svc' . $i . '_image' ) ); ?>" class="large-text"></td></tr>
    </table>
    <hr>
    <?php endfor;
}

/* ==========================================================================
   CONTACT PAGE META BOXES
   ========================================================================== */

function floru_contact_info_render( $post ) {
    $p = $post->ID;
    ?>
    <table class="form-table">
        <tr><th><label>Section Heading</label></th>
            <td><input type="text" name="floru_contact_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_heading' ) ); ?>" class="large-text"></td></tr>
        <tr><th><label>Section Description</label></th>
            <td><textarea name="floru_contact_description" rows="2" class="large-text"><?php echo esc_textarea( floru_get_meta( $p, '_floru_contact_description' ) ); ?></textarea></td></tr>
        <tr><th><label>Email</label></th>
            <td><input type="email" name="floru_contact_email" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_email' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Phone</label></th>
            <td><input type="text" name="floru_contact_phone" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_phone' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Phone (clean, for tel: link)</label></th>
            <td><input type="text" name="floru_contact_phone_raw" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_phone_raw' ) ); ?>" class="regular-text" placeholder="+31000000000"></td></tr>
        <tr><th><label>Office Location</label></th>
            <td><input type="text" name="floru_contact_office" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_office' ) ); ?>" class="regular-text"></td></tr>
        <tr><th><label>Form Shortcode</label></th>
            <td><input type="text" name="floru_contact_form_shortcode" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_form_shortcode' ) ); ?>" class="large-text" placeholder='[wpforms id="123"]'>
            <p class="description">Paste a form plugin shortcode here. If empty, the default placeholder form will be shown.</p></td></tr>
    </table>
    <?php
}

/* ==========================================================================
   SAVE ALL PAGE META
   ========================================================================== */

add_action( 'save_post', 'floru_save_page_meta' );
function floru_save_page_meta( $post_id ) {
    if ( ! isset( $_POST['floru_page_meta_nonce'] ) || ! wp_verify_nonce( $_POST['floru_page_meta_nonce'], 'floru_page_meta_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'page' || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Map of POST field name => meta key
    $text_fields = array(
        // Home hero
        'floru_hero_label'       => '_floru_hero_label',
        'floru_hero_heading'     => '_floru_hero_heading',
        'floru_hero_description' => '_floru_hero_description',
        'floru_hero_btn1_text'   => '_floru_hero_btn1_text',
        'floru_hero_btn1_url'    => '_floru_hero_btn1_url',
        'floru_hero_btn2_text'   => '_floru_hero_btn2_text',
        'floru_hero_btn2_url'    => '_floru_hero_btn2_url',
        // Home intro
        'floru_intro_label'     => '_floru_intro_label',
        'floru_intro_heading'   => '_floru_intro_heading',
        'floru_intro_text1'     => '_floru_intro_text1',
        'floru_intro_text2'     => '_floru_intro_text2',
        'floru_intro_btn_text'  => '_floru_intro_btn_text',
        'floru_intro_btn_url'   => '_floru_intro_btn_url',
        'floru_intro_image'     => '_floru_intro_image',
        // Home stats
        'floru_stat1_number'    => '_floru_stat1_number',
        'floru_stat1_label'     => '_floru_stat1_label',
        'floru_stat2_number'    => '_floru_stat2_number',
        'floru_stat2_label'     => '_floru_stat2_label',
        'floru_stat3_number'    => '_floru_stat3_number',
        'floru_stat3_label'     => '_floru_stat3_label',
        // Home services
        'floru_hsvc_label'      => '_floru_hsvc_label',
        'floru_hsvc_heading'    => '_floru_hsvc_heading',
        'floru_hsvc_description'=> '_floru_hsvc_description',
        'floru_hsvc1_title'     => '_floru_hsvc1_title',
        'floru_hsvc1_desc'      => '_floru_hsvc1_desc',
        'floru_hsvc1_icon'      => '_floru_hsvc1_icon',
        'floru_hsvc1_url'       => '_floru_hsvc1_url',
        'floru_hsvc2_title'     => '_floru_hsvc2_title',
        'floru_hsvc2_desc'      => '_floru_hsvc2_desc',
        'floru_hsvc2_icon'      => '_floru_hsvc2_icon',
        'floru_hsvc2_url'       => '_floru_hsvc2_url',
        'floru_hsvc3_title'     => '_floru_hsvc3_title',
        'floru_hsvc3_desc'      => '_floru_hsvc3_desc',
        'floru_hsvc3_icon'      => '_floru_hsvc3_icon',
        'floru_hsvc3_url'       => '_floru_hsvc3_url',
        // Home why
        'floru_why_label'       => '_floru_why_label',
        'floru_why_heading'     => '_floru_why_heading',
        'floru_why_description' => '_floru_why_description',
        'floru_why1_title'      => '_floru_why1_title',
        'floru_why1_desc'       => '_floru_why1_desc',
        'floru_why1_icon'       => '_floru_why1_icon',
        'floru_why2_title'      => '_floru_why2_title',
        'floru_why2_desc'       => '_floru_why2_desc',
        'floru_why2_icon'       => '_floru_why2_icon',
        'floru_why3_title'      => '_floru_why3_title',
        'floru_why3_desc'       => '_floru_why3_desc',
        'floru_why3_icon'       => '_floru_why3_icon',
        'floru_why4_title'      => '_floru_why4_title',
        'floru_why4_desc'       => '_floru_why4_desc',
        'floru_why4_icon'       => '_floru_why4_icon',
        // Home team
        'floru_hteam_label'     => '_floru_hteam_label',
        'floru_hteam_heading'   => '_floru_hteam_heading',
        'floru_hteam_description'=> '_floru_hteam_description',
        'floru_hteam_btn_text'  => '_floru_hteam_btn_text',
        'floru_hteam_btn_url'   => '_floru_hteam_btn_url',
        // Home clients
        'floru_hclients_label'  => '_floru_hclients_label',
        // Home CTA
        'floru_hcta_heading'    => '_floru_hcta_heading',
        'floru_hcta_description'=> '_floru_hcta_description',
        'floru_hcta_btn_text'   => '_floru_hcta_btn_text',
        'floru_hcta_btn_url'    => '_floru_hcta_btn_url',
        // Page header (shared)
        'floru_ph_label'        => '_floru_ph_label',
        'floru_ph_heading'      => '_floru_ph_heading',
        'floru_ph_description'  => '_floru_ph_description',
        // Page CTA (shared)
        'floru_pcta_heading'    => '_floru_pcta_heading',
        'floru_pcta_description'=> '_floru_pcta_description',
        'floru_pcta_btn_text'   => '_floru_pcta_btn_text',
        'floru_pcta_btn_url'    => '_floru_pcta_btn_url',
        // About
        'floru_about_intro_label'   => '_floru_about_intro_label',
        'floru_about_intro_heading' => '_floru_about_intro_heading',
        'floru_approach_label'      => '_floru_approach_label',
        'floru_approach_heading'    => '_floru_approach_heading',
        'floru_approach_description'=> '_floru_approach_description',
        'floru_values_label'        => '_floru_values_label',
        'floru_values_heading'      => '_floru_values_heading',
        // Contact
        'floru_contact_heading'     => '_floru_contact_heading',
        'floru_contact_description' => '_floru_contact_description',
        'floru_contact_email'       => '_floru_contact_email',
        'floru_contact_phone'       => '_floru_contact_phone',
        'floru_contact_phone_raw'   => '_floru_contact_phone_raw',
        'floru_contact_office'      => '_floru_contact_office',
        'floru_contact_form_shortcode' => '_floru_contact_form_shortcode',
    );

    // About steps (1-5)
    for ( $i = 1; $i <= 5; $i++ ) {
        $text_fields[ 'floru_step' . $i . '_title' ] = '_floru_step' . $i . '_title';
        $text_fields[ 'floru_step' . $i . '_desc' ]  = '_floru_step' . $i . '_desc';
    }

    // About values (1-4)
    for ( $i = 1; $i <= 4; $i++ ) {
        $text_fields[ 'floru_value' . $i . '_title' ] = '_floru_value' . $i . '_title';
        $text_fields[ 'floru_value' . $i . '_desc' ]  = '_floru_value' . $i . '_desc';
        $text_fields[ 'floru_value' . $i . '_icon' ]  = '_floru_value' . $i . '_icon';
    }

    // Services (1-3)
    for ( $i = 1; $i <= 3; $i++ ) {
        $text_fields[ 'floru_svc' . $i . '_label' ] = '_floru_svc' . $i . '_label';
        $text_fields[ 'floru_svc' . $i . '_title' ] = '_floru_svc' . $i . '_title';
        $text_fields[ 'floru_svc' . $i . '_image' ] = '_floru_svc' . $i . '_image';
    }

    foreach ( $text_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) ) );
        }
    }

    // HTML-allowed fields (service descriptions)
    $html_fields = array();
    for ( $i = 1; $i <= 3; $i++ ) {
        $html_fields[ 'floru_svc' . $i . '_desc' ] = '_floru_svc' . $i . '_desc';
    }

    foreach ( $html_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, wp_kses_post( wp_unslash( $_POST[ $post_key ] ) ) );
        }
    }
}
