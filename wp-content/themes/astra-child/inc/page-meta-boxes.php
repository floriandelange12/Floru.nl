<?php
/**
 * Floru Page Meta — Premium Admin UI
 * Section-based editor matching Teamleden/Opdrachtgevers quality.
 * Page map, numbered sections, badges, click-to-jump.
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
    $value = ( $value !== '' && $value !== false ) ? $value : $default;

    if ( function_exists( 'floru_get_current_language' ) && 'en' !== floru_get_current_language() ) {
        $translated_value = get_post_meta( $post_id, $key . '_' . floru_get_current_language(), true );
        if ( $translated_value !== '' && $translated_value !== false ) {
            return $translated_value;
        }

        if ( function_exists( 'floru_get_post_translation_override' ) ) {
            $hardcoded_override = floru_get_post_translation_override( $post_id, $key );
            if ( '' !== $hardcoded_override ) {
                return $hardcoded_override;
            }
        }
    }

    return function_exists( 'floru_translate_text' ) && is_string( $value ) ? floru_translate_text( $value ) : $value;
}

/* ==========================================================================
   PREMIUM CSS — shared .floru-cp-* classes (identical to Opdrachtgevers)
   ========================================================================== */

add_action( 'admin_head', 'floru_page_meta_admin_css' );
function floru_page_meta_admin_css() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'page' ) {
        return;
    }
    global $post;
    if ( ! $post || ! function_exists( 'floru_is_floru_page' ) || ! floru_is_floru_page( $post->ID ) ) {
        return;
    }
    ?>
    <style>
        /* ===== SECTIONS — compact CPT style ===== */
        .floru-cp-section {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            padding: 14px 18px;
            margin: 8px 0 0;
        }
        .floru-cp-section--editor-header {
            margin-bottom: 0;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom: none;
        }
        .floru-cp-section-header {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f1;
        }
        .floru-cp-section-header:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .floru-cp-section-number {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #2271b1;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
        }
        .floru-cp-section-number--muted { background: #c3c4c7; }
        .floru-cp-section-title {
            font-weight: 600;
            font-size: 13px;
            color: #1d2327;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .floru-cp-optional {
            font-weight: 400;
            font-size: 11px;
            color: #a7aaad;
            font-style: italic;
        }

        /* ===== FIELDS ===== */
        .floru-cp-field { margin-bottom: 10px; }
        .floru-cp-field:last-child { margin-bottom: 0; }
        .floru-cp-label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
            color: #50575e;
        }
        .floru-cp-field-hint {
            display: block;
            font-size: 11px;
            color: #a7aaad;
            margin-bottom: 4px;
            line-height: 1.35;
        }
        .floru-cp-hint { font-weight: 400; text-transform: none; letter-spacing: 0; color: #a7aaad; }
        .floru-cp-field-desc { display: block; font-size: 11px; color: #787c82; margin-bottom: 6px; line-height: 1.4; }
        .floru-cp-field-desc--below { margin-bottom: 0; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
        .floru-cp-info-icon { font-size: 14px !important; width: 14px !important; height: 14px !important; color: #a7aaad; }
        .floru-cp-field-row { display: flex; gap: 12px; }
        .floru-cp-field-row .floru-cp-field { flex: 1; }

        /* ===== MEDIA PICKER ===== */
        .floru-cp-media-picker {
            border: 1px solid #dcdcde;
            border-radius: 4px;
            background: #fff;
            padding: 10px;
        }
        .floru-cp-media-preview-wrap {
            width: 180px;
            height: 120px;
            border: 1px dashed #c3c4c7;
            border-radius: 4px;
            background: #f6f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .floru-cp-media-preview-wrap img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .floru-cp-media-empty {
            font-size: 11px;
            color: #787c82;
            padding: 0 10px;
            text-align: center;
            line-height: 1.35;
        }
        .floru-cp-media-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ===== HOME GROUPS ===== */
        .floru-cp-group-label {
            margin-top: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #8c8f94;
        }
        .floru-cp-group-label:first-child { margin-top: 0; }

        /* ===== COLLAPSIBLE ===== */
        .floru-cp-section-header--collapsible {
            cursor: pointer;
            user-select: none;
        }
        .floru-cp-collapse-icon {
            margin-left: auto;
            color: #a7aaad;
            font-size: 16px;
            width: 16px;
            height: 16px;
            transition: transform 0.2s;
        }
        .floru-cp-section--collapsed .floru-cp-collapse-icon { transform: rotate(-90deg); }
        .floru-cp-section--collapsed .floru-cp-section-body { display: none; }
        .floru-cp-section--collapsed .floru-cp-section-header {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        /* ===== CARDS (sub-groups inside sections) ===== */
        .floru-cp-card {
            background: #f8f9fa; border: 1px solid #e2e4e7;
            border-radius: 6px; padding: 16px 18px; margin-bottom: 12px;
        }
        .floru-cp-card:last-child { margin-bottom: 0; }
        .floru-cp-card-title {
            font-weight: 600; font-size: 13px; color: #1e1e1e;
            margin: 0 0 12px; padding-bottom: 8px; border-bottom: 1px solid #e2e4e7;
        }

        /* ===== INPUT STYLES ===== */
        .floru-cp-section input[type="text"],
        .floru-cp-section input[type="email"],
        .floru-cp-section input[type="url"],
        .floru-cp-section input[type="number"],
        .floru-cp-section textarea,
        .floru-cp-section select { border-radius: 4px; border-color: #c3c4c7; }
        .floru-cp-section input[type="text"]:focus,
        .floru-cp-section input[type="email"]:focus,
        .floru-cp-section input[type="url"]:focus,
        .floru-cp-section textarea:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }

        /* ===== HOME SIDEBAR STRUCTURE PANEL ===== */
        .floru-cp-preview-panel {
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            padding: 8px;
            margin-bottom: 10px;
        }
        .floru-cp-preview-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #a7aaad;
            margin-bottom: 5px;
        }
        .floru-cp-preview-card {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            background: #fff;
        }
        .floru-cp-structure-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .floru-cp-structure-list li {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #1d2327;
            line-height: 1.35;
        }
        .floru-cp-structure-list .floru-cp-map-nr {
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
        .floru-cp-structure-list .floru-cp-map-nr--muted { background: #c3c4c7; }
        .floru-cp-structure-list .floru-cp-optional {
            margin-left: auto;
            font-style: normal;
        }

        /* ===== SECTION SUBTITLE ===== */
        .floru-cp-section-subtitle {
            display: block;
            font-size: 11px;
            color: #787c82;
            line-height: 1.4;
            margin: -4px 0 8px 28px;
        }

        /* ===== CTA META BOX ===== */
        #floru_page_cta_box { border-radius: 6px; border-color: #dcdcde; }
        #floru_page_cta_box .postbox-header { border-bottom-color: #f0f0f1; }
        #floru_page_cta_box .postbox-header h2 { font-size: 13px; font-weight: 600; color: #1d2327; }
        #floru_page_cta_box .inside { padding: 12px 18px; }
        #floru_page_cta_box .inside .floru-cp-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; color: #50575e; font-weight: 600; }
        #floru_page_cta_box .inside input[type="text"],
        #floru_page_cta_box .inside input[type="url"],
        #floru_page_cta_box .inside textarea { border-radius: 4px; border-color: #c3c4c7; }
        #floru_page_cta_box .inside input[type="text"]:focus,
        #floru_page_cta_box .inside input[type="url"]:focus,
        #floru_page_cta_box .inside textarea:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }

        /* ===== CLEAN ADMIN — hide WP noise ===== */
        .floru-page-editor #titlediv { display: none; }
        .floru-page-editor #submitdiv .misc-pub-section { display: none; }
        .floru-page-editor #submitdiv #minor-publishing-actions { display: none; }
        .floru-page-editor #submitdiv #delete-action { display: none; }
        .floru-page-editor #submitdiv #major-publishing-actions { border-top: none; }
        .floru-page-editor .notice.notice-success { border-left-color: #2271b1; background: #f0f6fc; }

        /* ===== STATUS BAR ===== */
        .floru-cp-status-bar {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: #787c82; margin: 8px 0 4px; line-height: 1;
        }
        .floru-cp-status-dot {
            display: inline-block; width: 8px; height: 8px;
            border-radius: 50%; background: #a7aaad;
        }
        .floru-cp-status-dot--publish { background: #00a32a; }
        .floru-cp-status-dot--draft { background: #dba617; }
        .floru-cp-status-sep { color: #c3c4c7; }

        /* ===== VIEW LINK (publish box) ===== */
        .floru-cp-view-link {
            display: flex; align-items: center; gap: 4px;
            font-size: 12px; text-decoration: none; color: #2271b1; margin-bottom: 8px;
        }
        .floru-cp-view-link:hover { color: #135e96; }
        .floru-cp-view-link .dashicons { font-size: 14px; width: 14px; height: 14px; }

        /* ===== PAGEMAP PREVIEW LINK ===== */
        .floru-cp-pagemap__preview-link {
            font-size: 11px; font-weight: 400; text-decoration: none;
            color: #2271b1; margin-left: 8px;
        }
        .floru-cp-pagemap__preview-link:hover { color: #135e96; text-decoration: underline; }

        /* ===== CHARACTER COUNTER ===== */
        .floru-cp-char-counter { display: block; font-size: 10px; text-align: right; margin-top: 2px; line-height: 1; }

        /* ===== URL WARNING ===== */
        .floru-cp-url-warning { display: block; color: #d63638; font-size: 11px; margin-top: 2px; line-height: 1.3; }

        /* ===== CARD MOVE BUTTONS ===== */
        .floru-cp-card-move { display: inline-flex; gap: 2px; margin-left: auto; float: right; }
        .floru-cp-move-btn {
            background: none; border: 1px solid #c3c4c7; border-radius: 3px;
            width: 22px; height: 22px; font-size: 12px; line-height: 20px;
            text-align: center; cursor: pointer; color: #50575e; padding: 0;
        }
        .floru-cp-move-btn:hover { background: #f0f0f1; border-color: #8c8f94; }

        /* ===== EMPTY CARD HINT ===== */
        .floru-cp-card--empty { opacity: 0.7; }
        .floru-cp-card--empty .floru-cp-card-title::after {
            content: ' \2014  vul titel in om te activeren';
            font-weight: 400; font-style: italic; color: #a7aaad; font-size: 11px;
        }

        /* ===== EDITOR CONNECTOR ===== */
        .floru-page-editor #postdivrich { margin-top: 0; border-top-left-radius: 0; border-top-right-radius: 0; }
        .floru-page-editor .floru-cp-section--editor-header + #postdivrich { border-top: 1px solid #dcdcde; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 782px) {
            .floru-cp-field-row { flex-direction: column; gap: 0; }
            .floru-cp-section-header { flex-direction: column; gap: 8px; }
            .floru-cp-section-subtitle { margin-left: 0; }
            .floru-cp-status-bar { flex-wrap: wrap; }
            .floru-cp-card-move { float: none; margin-left: 0; margin-top: 4px; }
        }
    </style>
    <?php
}

/* ==========================================================================
   HELPERS — page map & section wrappers
   ========================================================================== */

function floru_page_render_map( $sections ) {
    global $post;
    $map_permalink = $post ? get_permalink( $post->ID ) : '';
    ?>
    <div class="floru-cp-pagemap" id="floru-page-map">
        <div class="floru-cp-pagemap__header">
            <span class="dashicons dashicons-layout"></span>
            <strong>Pagina-indeling overzicht</strong>
            <?php if ( $map_permalink ) : ?>
            <a href="<?php echo esc_url( $map_permalink ); ?>" target="_blank" class="floru-cp-pagemap__preview-link">&#8599; Bekijk pagina</a>
            <?php endif; ?>
            <span class="floru-cp-pagemap__toggle" id="floru-page-map-toggle">Tonen / Verbergen</span>
        </div>
        <div class="floru-cp-pagemap__body" id="floru-page-map-body">
            <div class="floru-cp-pagemap__columns">
                <div class="floru-cp-pagemap__card">
                    <div class="floru-cp-pagemap__card-label">Paginastructuur</div>
                    <div class="floru-cp-pagemap__card-art">
                        <?php foreach ( $sections as $s ) : ?>
                        <div class="floru-cp-map-zone<?php echo ! empty( $s['muted'] ) ? ' floru-cp-map-zone--muted' : ''; ?>" data-target="<?php echo esc_attr( $s['target'] ); ?>">
                            <span class="floru-cp-map-nr"><?php echo (int) $s['nr']; ?></span>
                            <?php echo esc_html( $s['label'] ); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function floru_home_render_sidebar_structure() {
    ?>
    <div id="floru-home-structure-panel" class="floru-cp-preview-panel" style="display:none;">
        <div class="floru-cp-preview-label">Home structuur</div>
        <div class="floru-cp-preview-card">
            <ul class="floru-cp-structure-list">
                <li><span class="floru-cp-map-nr">1</span>Hero</li>
                <li><span class="floru-cp-map-nr">2</span>Introductie</li>
                <li><span class="floru-cp-map-nr">3</span>Statistieken</li>
                <li><span class="floru-cp-map-nr">4</span>Diensten preview <span class="floru-cp-optional">ingeklapt</span></li>
                <li><span class="floru-cp-map-nr">5</span>Waarom Floru <span class="floru-cp-optional">ingeklapt</span></li>
                <li><span class="floru-cp-map-nr floru-cp-map-nr--muted">6</span>Team preview <span class="floru-cp-optional">auto</span></li>
                <li><span class="floru-cp-map-nr floru-cp-map-nr--muted">7</span>Opdrachtgevers <span class="floru-cp-optional">auto</span></li>
                <li><span class="floru-cp-map-nr">8</span>CTA</li>
            </ul>
        </div>
    </div>
    <?php
}

function floru_section_start( $number, $title, $subtitle = '', $id = '', $extra_class = '' ) {
    $cls = 'floru-cp-section' . ( $extra_class ? ' ' . $extra_class : '' );
    ?>
    <div class="<?php echo esc_attr( $cls ); ?>"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number"><?php echo (int) $number; ?></div>
            <div class="floru-cp-section-title"><?php echo esc_html( $title ); ?></div>
        </div>
        <?php if ( $subtitle ) : ?>
        <p class="floru-cp-section-subtitle"><?php echo esc_html( $subtitle ); ?></p>
        <?php endif; ?>
    <?php
}

function floru_section_end() {
    echo '</div>';
}

/**
 * Shared inline CTA section for pages without the WP editor.
 */
function floru_render_inline_cta( $post_id, $section_number ) {
    floru_section_start( $section_number, 'CTA', 'Slotblok met call-to-action.', 'floru-s-cta' );
    $p = $post_id;
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_pcta_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_pcta_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_pcta_description' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knoptekst</label>
            <input type="text" name="floru_pcta_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_btn_text' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop URL</label>
            <input type="url" name="floru_pcta_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_btn_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <?php
    floru_section_end();
}

/**
 * Shared media picker field that stores image URL in an existing meta key.
 */
function floru_render_image_picker_field( $args ) {
    $name         = isset( $args['name'] ) ? $args['name'] : '';
    $value        = isset( $args['value'] ) ? $args['value'] : '';
    $field_id     = isset( $args['field_id'] ) ? $args['field_id'] : '';
    $button_text  = isset( $args['button_text'] ) ? $args['button_text'] : 'Kies afbeelding';
    $title        = isset( $args['title'] ) ? $args['title'] : 'Selecteer afbeelding';
    $button_label = isset( $args['button_label'] ) ? $args['button_label'] : 'Gebruik deze afbeelding';

    if ( ! $name || ! $field_id ) {
        return;
    }
    ?>
    <div class="floru-cp-media-picker" data-frame-title="<?php echo esc_attr( $title ); ?>" data-button-label="<?php echo esc_attr( $button_label ); ?>">
        <input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $value ); ?>">
        <div class="floru-cp-media-preview-wrap">
            <img src="<?php echo $value ? esc_url( $value ) : ''; ?>" alt="" class="floru-cp-media-preview"<?php echo $value ? '' : ' style="display:none;"'; ?>>
            <span class="floru-cp-media-empty"<?php echo $value ? ' style="display:none;"' : ''; ?>>Geen afbeelding geselecteerd.</span>
        </div>
        <div class="floru-cp-media-actions">
            <button type="button" class="button floru-cp-media-select"><?php echo esc_html( $button_text ); ?></button>
            <button type="button" class="button-link floru-cp-media-remove"<?php echo $value ? '' : ' style="display:none;"'; ?>>Verwijderen</button>
        </div>
    </div>
    <?php
}

/**
 * Shared icon select field — renders a <select> with predefined Feather icon options.
 */
function floru_render_icon_select_field( $args ) {
    $name  = isset( $args['name'] ) ? $args['name'] : '';
    $value = isset( $args['value'] ) ? $args['value'] : '';

    if ( ! $name ) {
        return;
    }

    $options = array(
        'shield'       => 'Schild (integriteit)',
        'target'       => 'Doel (resultaatgericht)',
        'users'        => 'Gebruikers (samenwerking)',
        'globe'        => 'Wereldbol (expertise)',
        'briefcase'    => 'Aktentas (professioneel)',
        'check-circle' => 'Vinkje (kwaliteit)',
        'award'        => 'Trofee (onderscheiding)',
        'trending-up'  => 'Trendlijn (groei)',
        'file-text'    => 'Document (publicaties)',
    );
    ?>
    <select name="<?php echo esc_attr( $name ); ?>" class="widefat">
        <?php foreach ( $options as $val => $label ) : ?>
            <option value="<?php echo esc_attr( $val ); ?>"<?php selected( $value, $val ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

/* ==========================================================================
   MAIN DISPATCHER — edit_form_after_title renders page map + all sections
   ========================================================================== */

add_action( 'edit_form_after_title', 'floru_page_sections_render', 5 );
function floru_page_sections_render( $post ) {
    if ( $post->post_type !== 'page' ) {
        return;
    }
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    if ( ! function_exists( 'floru_get_page_definitions' ) ) {
        return;
    }
    $defs = floru_get_page_definitions();
    if ( ! array_key_exists( $template, $defs ) ) {
        return;
    }

    wp_nonce_field( 'floru_page_meta_nonce', 'floru_page_meta_nonce' );

    $floru_status = get_post_status( $post );
    $floru_modified = get_the_modified_date( 'd M Y, H:i', $post );
    $floru_status_label = $floru_status === 'publish' ? 'Gepubliceerd' : ( $floru_status === 'draft' ? 'Concept' : ucfirst( $floru_status ) );
    $floru_permalink = get_permalink( $post->ID );
    ?>
    <div class="floru-cp-status-bar" data-permalink="<?php echo esc_attr( $floru_permalink ); ?>">
        <span class="floru-cp-status-dot floru-cp-status-dot--<?php echo esc_attr( $floru_status ); ?>"></span>
        <?php echo esc_html( $floru_status_label ); ?>
        <span class="floru-cp-status-sep">|</span>
        Laatste wijziging: <?php echo esc_html( $floru_modified ); ?>
    </div>
    <?php

    switch ( $template ) {
        case 'templates/template-home.php':
            floru_render_home_sections( $post );
            break;
        case 'templates/template-about.php':
            floru_render_about_sections( $post );
            break;
        case 'templates/template-services.php':
            floru_render_services_sections( $post );
            break;
        case 'templates/template-team.php':
            floru_render_team_sections( $post );
            break;
        case 'templates/template-clients.php':
            floru_render_clients_sections( $post );
            break;
        case 'templates/template-contact.php':
            floru_render_contact_sections( $post );
            break;
    }

    // Page map toggle + click-to-jump JS
    ?>
    <script>
    jQuery(function($){
        function initFloruMediaPicker($picker){
            if (!$picker.length || typeof wp === 'undefined' || !wp.media) {
                return;
            }

            var $input = $picker.find('input[type="hidden"]');
            var $preview = $picker.find('.floru-cp-media-preview');
            var $empty = $picker.find('.floru-cp-media-empty');
            var $remove = $picker.find('.floru-cp-media-remove');
            var frame;

            function setImage(url){
                if (url) {
                    $input.val(url);
                    $preview.attr('src', url).show();
                    $empty.hide();
                    $remove.show();
                } else {
                    $input.val('');
                    $preview.attr('src', '').hide();
                    $empty.show();
                    $remove.hide();
                }
            }

            $picker.on('click', '.floru-cp-media-select', function(e){
                e.preventDefault();
                if (!frame) {
                    frame = wp.media({
                        title: $picker.data('frame-title') || 'Selecteer afbeelding',
                        button: { text: $picker.data('button-label') || 'Gebruik deze afbeelding' },
                        multiple: false,
                        library: { type: 'image' }
                    });

                    frame.on('select', function(){
                        var attachment = frame.state().get('selection').first().toJSON();
                        var url = attachment.sizes && attachment.sizes.large ? attachment.sizes.large.url : attachment.url;
                        setImage(url);
                    });
                }
                frame.open();
            });

            $picker.on('click', '.floru-cp-media-remove', function(e){
                e.preventDefault();
                setImage('');
            });
        }

        $('.floru-cp-media-picker').each(function(){
            initFloruMediaPicker($(this));
        });

        // Dynamic card titles: update .floru-cp-card-title from first text input
        $('.floru-cp-card[data-card-prefix]').each(function(){
            var $card = $(this);
            var prefix = $card.data('card-prefix');
            var $input = $card.find('input[type="text"]').first();
            function updateTitle() {
                var val = $.trim($input.val());
                $card.find('.floru-cp-card-title').first().text(val ? prefix + ' \u2014 ' + val : prefix);
            }
            $input.on('input', updateTitle);
            updateTitle();
        });

        // B1: URL field validation
        $('input[type="url"]').on('blur', function(){
            var $inp = $(this), val = $.trim($inp.val());
            $inp.next('.floru-cp-url-warning').remove();
            if (val && !/^https?:\/\//i.test(val)) {
                $inp.after('<span class="floru-cp-url-warning">\u26A0 URL mist https:// \u2014 link werkt mogelijk niet.</span>');
            }
        });

        // B2: Character counters on strategic textareas
        var charLimits = {
            'floru_hero_description': 200,
            'floru_ph_description': 150,
            'floru_hcta_description': 150,
            'floru_pcta_description': 150
        };
        $.each(charLimits, function(name, max){
            $('textarea[name="' + name + '"]').each(function(){
                var $ta = $(this);
                var $counter = $('<span class="floru-cp-char-counter"></span>');
                $ta.after($counter);
                function update(){
                    var len = $ta.val().length;
                    $counter.text(len + ' / ' + max);
                    $counter.css('color', len > max ? '#d63638' : (len > max*0.85 ? '#dba617' : '#a7aaad'));
                }
                $ta.on('input', update);
                update();
            });
        });

        // B3: Card move buttons (up/down value swap)
        $('.floru-cp-card[data-card-prefix]').each(function(){
            var $card = $(this);
            if ($card.find('.wp-editor-wrap').length) return;
            var $siblings = $card.parent().children('.floru-cp-card[data-card-prefix]').not(':has(.wp-editor-wrap)');
            if ($siblings.length <= 1) return;
            var idx = $siblings.index($card);
            var $btns = $('<span class="floru-cp-card-move"></span>');
            if (idx > 0) $btns.append('<button type="button" class="floru-cp-move-btn floru-cp-move-up" title="Omhoog">\u2191</button>');
            if (idx < $siblings.length - 1) $btns.append('<button type="button" class="floru-cp-move-btn floru-cp-move-down" title="Omlaag">\u2193</button>');
            $card.find('.floru-cp-card-title').first().append($btns);
        });
        function floruSwapCardValues($a, $b) {
            var aF = $a.find('input:not([type="hidden"]), textarea, select');
            var bF = $b.find('input:not([type="hidden"]), textarea, select');
            aF.each(function(i){
                var aVal = $(this).val(), $bField = bF.eq(i);
                if ($bField.length) { $(this).val($bField.val()).trigger('input'); $bField.val(aVal).trigger('input'); }
            });
        }
        $(document).on('click', '.floru-cp-move-up', function(e){
            e.preventDefault();
            var $c = $(this).closest('.floru-cp-card');
            var $prev = $c.prevAll('.floru-cp-card[data-card-prefix]:first');
            if ($prev.length) floruSwapCardValues($c, $prev);
        });
        $(document).on('click', '.floru-cp-move-down', function(e){
            e.preventDefault();
            var $c = $(this).closest('.floru-cp-card');
            var $next = $c.nextAll('.floru-cp-card[data-card-prefix]:first');
            if ($next.length) floruSwapCardValues($c, $next);
        });

        // C2: Empty card visual hints
        $('.floru-cp-card[data-card-prefix]').each(function(){
            var $card = $(this), $inp = $card.find('input[type="text"]').first();
            function checkEmpty() { $card.toggleClass('floru-cp-card--empty', !$.trim($inp.val())); }
            $inp.on('input', checkEmpty);
            checkEmpty();
        });

        // B4: Unsaved changes warning
        var floruDirty = false;
        $(document).on('input change', '.floru-cp-section input, .floru-cp-section textarea, .floru-cp-section select, .floru-cp-cta-inner input, .floru-cp-cta-inner textarea', function(){
            floruDirty = true;
        });
        $('#post').on('submit', function(){ floruDirty = false; });
        $(window).on('beforeunload', function(e){ if (floruDirty) { e.preventDefault(); return ''; } });

        // A2: Add "Bekijk pagina" link to publish box
        var permalink = $('.floru-cp-status-bar').data('permalink');
        if (permalink && $('#major-publishing-actions').length) {
            $('#major-publishing-actions').prepend('<a href="' + permalink + '" target="_blank" class="floru-cp-view-link"><span class="dashicons dashicons-external"></span> Bekijk pagina</a>');
        }

        if ('<?php echo esc_js( $template ); ?>' === 'templates/template-home.php') {
            if ($('#side-sortables').length) {
                $('#floru-home-structure-panel').prependTo('#side-sortables').show();
            }

            $('.floru-cp-section-header--collapsible').each(function(){
                var key = $(this).data('collapse-key');
                if (!key) return;
                if (localStorage.getItem(key) === 'open') {
                    $(this).closest('.floru-cp-section').removeClass('floru-cp-section--collapsed');
                }
            });

            $(document).on('click', '.floru-cp-section-header--collapsible', function(){
                var $section = $(this).closest('.floru-cp-section');
                var key = $(this).data('collapse-key');
                $section.toggleClass('floru-cp-section--collapsed');
                if (key) {
                    localStorage.setItem(key, $section.hasClass('floru-cp-section--collapsed') ? 'closed' : 'open');
                }
            });
            return;
        }

        var mapBody = $('#floru-page-map-body');
        var mapState = localStorage.getItem('floru_pagemap_open');
        if (mapState === 'closed') mapBody.hide();
        $('.floru-cp-pagemap__header').on('click', function(){
            mapBody.slideToggle(200);
            localStorage.setItem('floru_pagemap_open', mapBody.is(':visible') ? 'open' : 'closed');
        });
        $(document).on('click', '.floru-cp-map-zone[data-target]', function(){
            var el = $('#' + $(this).data('target'));
            if (el.length) {
                $('html, body').animate({ scrollTop: el.offset().top - 80 }, 300);
                el.css('box-shadow', '0 0 0 3px rgba(34,113,177,0.35)');
                setTimeout(function(){ el.css('box-shadow', ''); }, 1500);
            }
        });
    });
    </script>
    <?php
}

/* ==========================================================================
   HOME PAGE — 8 sections
   ========================================================================== */

function floru_render_home_sections( $post ) {
    $p = $post->ID;
    floru_home_render_sidebar_structure();
    ?>
    <div class="floru-cp-group-label">Hoofdcontent</div>
    <?php

    floru_section_start( 1, 'Hero', 'Label, hoofdtekst, omschrijving en twee knoppen.', 'floru-s-hero' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_hero_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_hero_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_hero_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hero_description' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop 1 tekst</label>
            <input type="text" name="floru_hero_btn1_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn1_text' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop 1 URL</label>
            <input type="url" name="floru_hero_btn1_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn1_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop 2 tekst</label>
            <input type="text" name="floru_hero_btn2_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn2_text' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop 2 URL</label>
            <input type="url" name="floru_hero_btn2_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hero_btn2_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <?php
    floru_section_end();

    floru_section_start( 2, 'Introductie', 'Tekstblok met afbeelding, twee alineas en een knop.', 'floru-s-intro' );
    ?>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Label</label>
            <input type="text" name="floru_intro_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_label' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field" style="flex:2;">
            <label class="floru-cp-label">Koptekst</label>
            <input type="text" name="floru_intro_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_heading' ) ); ?>" class="widefat">
        </div>
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Alinea 1</label>
        <textarea name="floru_intro_text1" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_intro_text1' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Alinea 2</label>
        <textarea name="floru_intro_text2" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_intro_text2' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knoptekst</label>
            <input type="text" name="floru_intro_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_btn_text' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop URL</label>
            <input type="url" name="floru_intro_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_intro_btn_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Afbeelding</label>
        <span class="floru-cp-field-hint">Kies een afbeelding uit de mediabibliotheek. Er wordt een URL opgeslagen voor compatibiliteit.</span>
        <?php
        floru_render_image_picker_field( array(
            'name'         => 'floru_intro_image',
            'value'        => floru_get_meta( $p, '_floru_intro_image' ),
            'field_id'     => 'floru-intro-image',
            'button_text'  => 'Kies afbeelding',
            'title'        => 'Selecteer introductie-afbeelding',
            'button_label' => 'Gebruik deze afbeelding',
        ) );
        ?>
    </div>
    <?php
    floru_section_end();

    floru_section_start( 3, 'Statistieken', 'Drie cijfers die de schaal van Floru illustreren.', 'floru-s-stats' );
    ?>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Cijfer 1 - waarde</label>
            <input type="text" name="floru_stat1_number" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat1_number' ) ); ?>" class="widefat" placeholder="bijv. 20+">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Cijfer 1 - label</label>
            <input type="text" name="floru_stat1_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat1_label' ) ); ?>" class="widefat" placeholder="bijv. Jaren ervaring">
        </div>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Cijfer 2 - waarde</label>
            <input type="text" name="floru_stat2_number" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat2_number' ) ); ?>" class="widefat" placeholder="bijv. 85+">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Cijfer 2 - label</label>
            <input type="text" name="floru_stat2_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat2_label' ) ); ?>" class="widefat" placeholder="bijv. Projecten">
        </div>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Cijfer 3 - waarde</label>
            <input type="text" name="floru_stat3_number" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat3_number' ) ); ?>" class="widefat" placeholder="bijv. 40+">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Cijfer 3 - label</label>
            <input type="text" name="floru_stat3_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_stat3_label' ) ); ?>" class="widefat" placeholder="bijv. Opdrachtgevers">
        </div>
    </div>
    <?php
    floru_section_end();

    ?>
    <div class="floru-cp-group-label">Secties</div>
    <div class="floru-cp-section floru-cp-section--collapsed" id="floru-s-services">
        <div class="floru-cp-section-header floru-cp-section-header--collapsible" data-collapse-key="floru_home_services_open">
            <div class="floru-cp-section-number">4</div>
            <div class="floru-cp-section-title">Diensten preview <span class="floru-cp-optional">ingeklapt</span></div>
            <span class="floru-cp-collapse-icon dashicons dashicons-arrow-down-alt2"></span>
        </div>
        <div class="floru-cp-section-body">
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Supertitel</label>
        <input type="text" name="floru_hsvc_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_hsvc_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_hsvc_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hsvc_description' ) ); ?></textarea>
    </div>

    <?php for ( $hi = 1; $hi <= 3; $hi++ ) : ?>
    <div class="floru-cp-card" data-card-prefix="Dienstkaart <?php echo $hi; ?>">
        <div class="floru-cp-card-title">Dienstkaart <?php echo $hi; ?></div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label">Titel</label>
                <input type="text" name="floru_hsvc<?php echo $hi; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc' . $hi . '_title' ) ); ?>" class="widefat">
            </div>
            <div class="floru-cp-field">
                <label class="floru-cp-label">Icoon</label>
                <?php floru_render_icon_select_field( array( 'name' => 'floru_hsvc' . $hi . '_icon', 'value' => floru_get_meta( $p, '_floru_hsvc' . $hi . '_icon' ) ) ); ?>
            </div>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Omschrijving</label>
            <textarea name="floru_hsvc<?php echo $hi; ?>_desc" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hsvc' . $hi . '_desc' ) ); ?></textarea>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Link URL</label>
            <input type="url" name="floru_hsvc<?php echo $hi; ?>_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hsvc' . $hi . '_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <?php endfor; ?>
    </div>
    </div>

    <div class="floru-cp-section floru-cp-section--collapsed" id="floru-s-why">
        <div class="floru-cp-section-header floru-cp-section-header--collapsible" data-collapse-key="floru_home_why_open">
            <div class="floru-cp-section-number">5</div>
            <div class="floru-cp-section-title">Waarom Floru <span class="floru-cp-optional">ingeklapt</span></div>
            <span class="floru-cp-collapse-icon dashicons dashicons-arrow-down-alt2"></span>
        </div>
        <div class="floru-cp-section-body">
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Supertitel</label>
        <input type="text" name="floru_why_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_why_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_why_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_why_description' ) ); ?></textarea>
    </div>

    <?php for ( $wi = 1; $wi <= 4; $wi++ ) : ?>
    <div class="floru-cp-card" data-card-prefix="Vertrouwensitem <?php echo $wi; ?>">
        <div class="floru-cp-card-title">Vertrouwensitem <?php echo $wi; ?></div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label">Titel</label>
                <input type="text" name="floru_why<?php echo $wi; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_why' . $wi . '_title' ) ); ?>" class="widefat">
            </div>
            <div class="floru-cp-field">
                <label class="floru-cp-label">Icoon</label>
                <?php floru_render_icon_select_field( array( 'name' => 'floru_why' . $wi . '_icon', 'value' => floru_get_meta( $p, '_floru_why' . $wi . '_icon' ) ) ); ?>
            </div>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Omschrijving</label>
            <textarea name="floru_why<?php echo $wi; ?>_desc" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_why' . $wi . '_desc' ) ); ?></textarea>
        </div>
    </div>
    <?php endfor; ?>
    </div>
    </div>

    <div class="floru-cp-group-label">Verwijzingen &amp; CTA</div>

    <div class="floru-cp-section" id="floru-s-team">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number floru-cp-section-number--muted">6</div>
            <div class="floru-cp-section-title">Team preview <span class="floru-cp-optional">auto-geladen</span></div>
        </div>
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Supertitel</label>
        <input type="text" name="floru_hteam_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_hteam_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_hteam_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hteam_description' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knoptekst</label>
            <input type="text" name="floru_hteam_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_btn_text' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop URL</label>
            <input type="url" name="floru_hteam_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hteam_btn_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <span class="floru-cp-field-hint">Teamleden worden automatisch opgehaald (laatste 3, gesorteerd op volgorde).</span>
    </div>

    <div class="floru-cp-section" id="floru-s-clients">
        <div class="floru-cp-section-header">
            <div class="floru-cp-section-number floru-cp-section-number--muted">7</div>
            <div class="floru-cp-section-title">Opdrachtgevers <span class="floru-cp-optional">auto-geladen</span></div>
        </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_hclients_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hclients_label' ) ); ?>" class="widefat">
    </div>
    <span class="floru-cp-field-hint">Logo's worden automatisch opgehaald uit het berichttype Opdrachtgevers.</span>
    </div>

    <?php
    floru_section_start( 8, 'CTA', 'Slotblok met uitnodigend kopje en knop.', 'floru-s-cta' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_hcta_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hcta_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_hcta_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_hcta_description' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knoptekst</label>
            <input type="text" name="floru_hcta_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hcta_btn_text' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Knop URL</label>
            <input type="url" name="floru_hcta_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_hcta_btn_url' ) ); ?>" class="widefat">
        </div>
    </div>
    <?php
    floru_section_end();
}

/* ==========================================================================
   ABOUT PAGE — 5 sections (+ editor for page content)
   ========================================================================== */

function floru_render_about_sections( $post ) {
    $p = $post->ID;
    floru_page_render_map( array(
        array( 'nr' => 1, 'label' => 'Paginakop',       'target' => 'floru-s-header' ),
        array( 'nr' => 2, 'label' => 'Introductie',      'target' => 'floru-s-intro' ),
        array( 'nr' => 3, 'label' => 'Aanpak stappen',   'target' => 'floru-s-approach' ),
        array( 'nr' => 4, 'label' => 'Kernwaarden',      'target' => 'floru-s-values' ),
        array( 'nr' => 5, 'label' => 'Pagina-inhoud',    'target' => 'floru-s-content', 'muted' => true ),
        array( 'nr' => 6, 'label' => 'CTA sectie',       'target' => 'floru_page_cta_box' ),
    ) );

    /* ── Section 1: Paginakop ── */
    floru_section_start( 1, 'Paginakop', 'Header bovenaan de pagina met label, koptekst en omschrijving.', 'floru-s-header' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_ph_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_ph_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_ph_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_ph_description' ) ); ?></textarea>
    </div>
    <?php
    floru_section_end();

    /* ── Section 2: Introductie ── */
    floru_section_start( 2, 'Introductie', 'Label en koptekst. De introductietekst komt uit de paginainhoud (de WordPress editor onderaan).', 'floru-s-intro' );
    ?>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Label</label>
            <input type="text" name="floru_about_intro_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_about_intro_label' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field" style="flex:2;">
            <label class="floru-cp-label">Koptekst</label>
            <input type="text" name="floru_about_intro_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_about_intro_heading' ) ); ?>" class="widefat">
        </div>
    </div>
    <?php
    floru_section_end();

    /* ── Section 3: Aanpak stappen ── */
    floru_section_start( 3, 'Aanpak stappen', 'Vijf stappen die de Floru-aanpak beschrijven.', 'floru-s-approach' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Supertitel</label>
        <input type="text" name="floru_approach_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_approach_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_approach_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_approach_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_approach_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_approach_description' ) ); ?></textarea>
    </div>
    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
    <div class="floru-cp-card" data-card-prefix="Stap <?php echo $i; ?>">
        <div class="floru-cp-card-title">Stap <?php echo $i; ?></div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label">Titel</label>
                <input type="text" name="floru_step<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_step' . $i . '_title' ) ); ?>" class="widefat">
            </div>
            <div class="floru-cp-field" style="flex:2;">
                <label class="floru-cp-label">Omschrijving</label>
                <textarea name="floru_step<?php echo $i; ?>_desc" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_step' . $i . '_desc' ) ); ?></textarea>
            </div>
        </div>
    </div>
    <?php endfor;
    floru_section_end();

    /* ── Section 4: Kernwaarden ── */
    floru_section_start( 4, 'Kernwaarden', 'Vier kernwaarden met icoon en omschrijving.', 'floru-s-values' );
    ?>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Supertitel</label>
            <input type="text" name="floru_values_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_values_label' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field" style="flex:2;">
            <label class="floru-cp-label">Koptekst</label>
            <input type="text" name="floru_values_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_values_heading' ) ); ?>" class="widefat">
        </div>
    </div>
    <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
    <div class="floru-cp-card" data-card-prefix="Kernwaarde <?php echo $i; ?>">
        <div class="floru-cp-card-title">Kernwaarde <?php echo $i; ?></div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label">Titel</label>
                <input type="text" name="floru_value<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_value' . $i . '_title' ) ); ?>" class="widefat">
            </div>
            <div class="floru-cp-field">
                <label class="floru-cp-label">Icoon</label>
                <?php floru_render_icon_select_field( array( 'name' => 'floru_value' . $i . '_icon', 'value' => floru_get_meta( $p, '_floru_value' . $i . '_icon' ) ) ); ?>
            </div>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Omschrijving</label>
            <textarea name="floru_value<?php echo $i; ?>_desc" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_value' . $i . '_desc' ) ); ?></textarea>
        </div>
    </div>
    <?php endfor;
    floru_section_end();

    /* ── Section 5: Editor header (connects to WP editor below) ── */
    floru_section_start( 5, 'Pagina-inhoud', 'De standaard WordPress editor hieronder bevat de introductietekst van de pagina. De afbeelding gebruikt de Uitgelichte afbeelding.', 'floru-s-content', 'floru-cp-section--editor-header' );
    floru_section_end();

    /* ── Section 6: CTA — rendered as meta box below editor ── */
    // CTA is added as a meta box below the editor for correct positioning
}

/* ==========================================================================
   SERVICES PAGE — 3 sections
   ========================================================================== */

function floru_render_services_sections( $post ) {
    $p = $post->ID;
    floru_page_render_map( array(
        array( 'nr' => 1, 'label' => 'Paginakop',   'target' => 'floru-s-header' ),
        array( 'nr' => 2, 'label' => 'Diensten',     'target' => 'floru-s-services' ),
        array( 'nr' => 3, 'label' => 'CTA sectie',   'target' => 'floru-s-cta' ),
    ) );

    /* ── Section 1: Paginakop ── */
    floru_section_start( 1, 'Paginakop', 'Header bovenaan de pagina.', 'floru-s-header' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_ph_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_ph_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_ph_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_ph_description' ) ); ?></textarea>
    </div>
    <?php
    floru_section_end();

    /* ── Section 2: Diensten ── */
    floru_section_start( 2, 'Dienstsecties', 'Drie uitgebreide dienstblokken met afbeelding.', 'floru-s-services' );
    for ( $i = 1; $i <= 3; $i++ ) :
    ?>
    <div class="floru-cp-card" data-card-prefix="Dienst <?php echo $i; ?>">
        <div class="floru-cp-card-title">Dienst <?php echo $i; ?></div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label">Label</label>
                <input type="text" name="floru_svc<?php echo $i; ?>_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_svc' . $i . '_label' ) ); ?>" class="widefat" placeholder="bijv. Dienst 01">
            </div>
            <div class="floru-cp-field" style="flex:2;">
                <label class="floru-cp-label">Titel</label>
                <input type="text" name="floru_svc<?php echo $i; ?>_title" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_svc' . $i . '_title' ) ); ?>" class="widefat">
            </div>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Omschrijving</label>
            <span class="floru-cp-field-hint">Gebruik de editor voor opmaak zoals paragrafen, koppen en lijsten.</span>
            <?php
            wp_editor(
                floru_get_meta( $p, '_floru_svc' . $i . '_desc' ),
                'florusvc' . $i . 'desc',
                array(
                    'textarea_name' => 'floru_svc' . $i . '_desc',
                    'textarea_rows' => 8,
                    'media_buttons' => false,
                    'teeny'         => true,
                    'quicktags'     => true,
                    'tinymce'       => array(
                        'toolbar1'      => 'bold,italic,bullist,numlist,link,unlink,undo,redo',
                        'toolbar2'      => '',
                        'block_formats' => 'Paragraaf=p;Kop 4=h4',
                    ),
                )
            );
            ?>
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Afbeelding</label>
            <?php
            floru_render_image_picker_field( array(
                'name'         => 'floru_svc' . $i . '_image',
                'value'        => floru_get_meta( $p, '_floru_svc' . $i . '_image' ),
                'field_id'     => 'floru-svc' . $i . '-image',
                'button_text'  => 'Kies afbeelding',
                'title'        => 'Selecteer afbeelding voor Dienst ' . $i,
                'button_label' => 'Gebruik deze afbeelding',
            ) );
            ?>
        </div>
    </div>
    <?php
    endfor;
    floru_section_end();

    /* ── Section 3: CTA ── */
    floru_render_inline_cta( $p, 3 );
}

/* ==========================================================================
   TEAM PAGE — 2 sections
   ========================================================================== */

function floru_render_team_sections( $post ) {
    $p = $post->ID;

    /* ── Section 1: Paginakop ── */
    floru_section_start( 1, 'Paginakop', 'Header bovenaan de pagina.', 'floru-s-header' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_ph_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_ph_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_ph_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_ph_description' ) ); ?></textarea>
    </div>
    <?php
    floru_section_end();

    /* ── Section 2: Auto-loaded team ── */
    floru_section_start( 2, 'Teamleden', 'Teamleden worden automatisch opgehaald uit het berichttype Teamleden, gesorteerd op volgorde.', 'floru-s-team' );
    ?>
    <span class="floru-cp-field-desc floru-cp-field-desc--below">
        <span class="dashicons dashicons-info-outline floru-cp-info-icon"></span>
        Ga naar <strong>Teamleden</strong> in het zijmenu om teamleden toe te voegen of te bewerken.
    </span>
    <?php
    floru_section_end();

    /* ── Section 3: CTA ── */
    floru_render_inline_cta( $p, 3 );
}

/* ==========================================================================
   CLIENTS PAGE — 2 sections
   ========================================================================== */

function floru_render_clients_sections( $post ) {
    $p = $post->ID;

    /* ── Section 1: Paginakop ── */
    floru_section_start( 1, 'Paginakop', 'Header bovenaan de pagina.', 'floru-s-header' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_ph_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_ph_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_ph_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_ph_description' ) ); ?></textarea>
    </div>
    <?php
    floru_section_end();

    /* ── Section 2: Auto-loaded clients ── */
    floru_section_start( 2, 'Opdrachtgevers', 'Opdrachtgevers worden automatisch opgehaald uit het berichttype Opdrachtgevers, gesorteerd op volgorde.', 'floru-s-clients' );
    ?>
    <span class="floru-cp-field-desc floru-cp-field-desc--below">
        <span class="dashicons dashicons-info-outline floru-cp-info-icon"></span>
        Ga naar <strong>Opdrachtgevers</strong> in het zijmenu om opdrachtgevers toe te voegen of te bewerken.
    </span>
    <?php
    floru_section_end();

    /* ── Section 3: CTA ── */
    floru_render_inline_cta( $p, 3 );
}

/* ==========================================================================
   CONTACT PAGE — 2 sections
   ========================================================================== */

function floru_render_contact_sections( $post ) {
    $p = $post->ID;

    /* ── Section 1: Paginakop ── */
    floru_section_start( 1, 'Paginakop', 'Header bovenaan de pagina.', 'floru-s-header' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Label</label>
        <input type="text" name="floru_ph_label" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_label' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_ph_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_ph_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_ph_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_ph_description' ) ); ?></textarea>
    </div>
    <?php
    floru_section_end();

    /* ── Section 2: Contactgegevens ── */
    floru_section_start( 2, 'Contactgegevens', 'E-mail, telefoon, adres en optioneel een formulier-shortcode.', 'floru-s-contact' );
    ?>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Koptekst</label>
        <input type="text" name="floru_contact_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_heading' ) ); ?>" class="widefat">
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Omschrijving</label>
        <textarea name="floru_contact_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_contact_description' ) ); ?></textarea>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">E-mailadres</label>
            <input type="email" name="floru_contact_email" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_email' ) ); ?>" class="widefat" placeholder="r.pruijss@floru.nl">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Telefoonnummer</label>
            <input type="text" name="floru_contact_phone" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_phone' ) ); ?>" class="widefat" placeholder="+31 6 42 58 75 15">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Telefoon (schoon) <span class="floru-cp-hint">— voor tel: link</span></label>
            <input type="text" name="floru_contact_phone_raw" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_phone_raw' ) ); ?>" class="widefat" placeholder="+31642587515">
        </div>
    </div>
    <div class="floru-cp-field-row">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Adres</label>
            <input type="text" name="floru_contact_address" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_address' ) ); ?>" class="widefat" placeholder="De klerkplan 10">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Postcode &amp; Plaats</label>
            <input type="text" name="floru_contact_city" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_city' ) ); ?>" class="widefat" placeholder="2728 EH Zoetermeer">
        </div>
    </div>
    <div class="floru-cp-field">
        <label class="floru-cp-label">Formulier shortcode</label>
        <input type="text" name="floru_contact_form_shortcode" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_contact_form_shortcode' ) ); ?>" class="widefat" placeholder='[wpforms id="123"]'>
        <span class="floru-cp-field-desc floru-cp-field-desc--below">
            <span class="dashicons dashicons-info-outline floru-cp-info-icon"></span>
            Plak hier een formulier-shortcode, bijv. uit <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpforms-overview' ) ); ?>" target="_blank">WPForms</a>. Laat leeg voor het ingebouwde formulier.
        </span>
    </div>
    <?php
    floru_section_end();
}

/* ==========================================================================
   META BOXES — CTA for pages that need it (shown below editor)
   ========================================================================== */

add_action( 'add_meta_boxes_page', 'floru_page_cta_meta_box', 10, 1 );
function floru_page_cta_meta_box( $post ) {
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    // About only — Services, Team, Clients now render CTA inline
    if ( $template === 'templates/template-about.php' ) {
        add_meta_box( 'floru_page_cta_box', 'CTA sectie', 'floru_page_cta_box_render', 'page', 'normal', 'default' );
    }
}

function floru_page_cta_box_render( $post ) {
    $p = $post->ID;
    ?>
    <div class="floru-cp-cta-inner">
        <div class="floru-cp-field">
            <label class="floru-cp-label">Koptekst</label>
            <input type="text" name="floru_pcta_heading" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_heading' ) ); ?>" class="widefat">
        </div>
        <div class="floru-cp-field">
            <label class="floru-cp-label">Omschrijving</label>
            <textarea name="floru_pcta_description" rows="3" class="widefat"><?php echo esc_textarea( floru_get_meta( $p, '_floru_pcta_description' ) ); ?></textarea>
        </div>
        <div class="floru-cp-field-row">
            <div class="floru-cp-field">
                <label class="floru-cp-label">Knoptekst</label>
                <input type="text" name="floru_pcta_btn_text" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_btn_text' ) ); ?>" class="widefat">
            </div>
            <div class="floru-cp-field">
                <label class="floru-cp-label">Knop URL</label>
                <input type="url" name="floru_pcta_btn_url" value="<?php echo esc_attr( floru_get_meta( $p, '_floru_pcta_btn_url' ) ); ?>" class="widefat">
            </div>
        </div>
    </div>
    <?php
}

/* ==========================================================================
   ADMIN CLEANUP — remove irrelevant WP meta boxes on Floru pages
   ========================================================================== */

add_action( 'add_meta_boxes_page', 'floru_page_clean_admin', 20, 1 );
function floru_page_clean_admin( $post ) {
    if ( ! function_exists( 'floru_is_floru_page' ) || ! floru_is_floru_page( $post->ID ) ) {
        return;
    }
    remove_meta_box( 'pageparentdiv', 'page', 'side' );
    remove_meta_box( 'slugdiv', 'page', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
}

/* ==========================================================================
   SAVE ALL PAGE META — identical field mapping, unchanged save logic
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

    $text_fields = array(
        'floru_hero_label'       => '_floru_hero_label',
        'floru_hero_heading'     => '_floru_hero_heading',
        'floru_hero_description' => '_floru_hero_description',
        'floru_hero_btn1_text'   => '_floru_hero_btn1_text',
        'floru_hero_btn1_url'    => '_floru_hero_btn1_url',
        'floru_hero_btn2_text'   => '_floru_hero_btn2_text',
        'floru_hero_btn2_url'    => '_floru_hero_btn2_url',
        'floru_intro_label'     => '_floru_intro_label',
        'floru_intro_heading'   => '_floru_intro_heading',
        'floru_intro_text1'     => '_floru_intro_text1',
        'floru_intro_text2'     => '_floru_intro_text2',
        'floru_intro_btn_text'  => '_floru_intro_btn_text',
        'floru_intro_btn_url'   => '_floru_intro_btn_url',
        'floru_intro_image'     => '_floru_intro_image',
        'floru_stat1_number'    => '_floru_stat1_number',
        'floru_stat1_label'     => '_floru_stat1_label',
        'floru_stat2_number'    => '_floru_stat2_number',
        'floru_stat2_label'     => '_floru_stat2_label',
        'floru_stat3_number'    => '_floru_stat3_number',
        'floru_stat3_label'     => '_floru_stat3_label',
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
        'floru_hteam_label'     => '_floru_hteam_label',
        'floru_hteam_heading'   => '_floru_hteam_heading',
        'floru_hteam_description'=> '_floru_hteam_description',
        'floru_hteam_btn_text'  => '_floru_hteam_btn_text',
        'floru_hteam_btn_url'   => '_floru_hteam_btn_url',
        'floru_hclients_label'  => '_floru_hclients_label',
        'floru_hcta_heading'    => '_floru_hcta_heading',
        'floru_hcta_description'=> '_floru_hcta_description',
        'floru_hcta_btn_text'   => '_floru_hcta_btn_text',
        'floru_hcta_btn_url'    => '_floru_hcta_btn_url',
        'floru_ph_label'        => '_floru_ph_label',
        'floru_ph_heading'      => '_floru_ph_heading',
        'floru_ph_description'  => '_floru_ph_description',
        'floru_pcta_heading'    => '_floru_pcta_heading',
        'floru_pcta_description'=> '_floru_pcta_description',
        'floru_pcta_btn_text'   => '_floru_pcta_btn_text',
        'floru_pcta_btn_url'    => '_floru_pcta_btn_url',
        'floru_about_intro_label'   => '_floru_about_intro_label',
        'floru_about_intro_heading' => '_floru_about_intro_heading',
        'floru_approach_label'      => '_floru_approach_label',
        'floru_approach_heading'    => '_floru_approach_heading',
        'floru_approach_description'=> '_floru_approach_description',
        'floru_values_label'        => '_floru_values_label',
        'floru_values_heading'      => '_floru_values_heading',
        'floru_contact_heading'     => '_floru_contact_heading',
        'floru_contact_description' => '_floru_contact_description',
        'floru_contact_email'       => '_floru_contact_email',
        'floru_contact_phone'       => '_floru_contact_phone',
        'floru_contact_phone_raw'   => '_floru_contact_phone_raw',
        'floru_contact_address'     => '_floru_contact_address',
        'floru_contact_city'        => '_floru_contact_city',
        'floru_contact_form_shortcode' => '_floru_contact_form_shortcode',
    );

    for ( $i = 1; $i <= 5; $i++ ) {
        $text_fields[ 'floru_step' . $i . '_title' ] = '_floru_step' . $i . '_title';
        $text_fields[ 'floru_step' . $i . '_desc' ]  = '_floru_step' . $i . '_desc';
    }
    for ( $i = 1; $i <= 4; $i++ ) {
        $text_fields[ 'floru_value' . $i . '_title' ] = '_floru_value' . $i . '_title';
        $text_fields[ 'floru_value' . $i . '_desc' ]  = '_floru_value' . $i . '_desc';
        $text_fields[ 'floru_value' . $i . '_icon' ]  = '_floru_value' . $i . '_icon';
    }
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

    $html_fields = array();
    for ( $i = 1; $i <= 3; $i++ ) {
        $html_fields[ 'floru_svc' . $i . '_desc' ] = '_floru_svc' . $i . '_desc';
    }
    foreach ( $html_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, wp_kses_post( wp_unslash( $_POST[ $post_key ] ) ) );
        }
    }

    // URL fields — re-save with esc_url_raw for proper URL sanitization
    $url_fields = array(
        'floru_hero_btn1_url' => '_floru_hero_btn1_url',
        'floru_hero_btn2_url' => '_floru_hero_btn2_url',
        'floru_intro_btn_url' => '_floru_intro_btn_url',
        'floru_intro_image'   => '_floru_intro_image',
        'floru_hsvc1_url'     => '_floru_hsvc1_url',
        'floru_hsvc2_url'     => '_floru_hsvc2_url',
        'floru_hsvc3_url'     => '_floru_hsvc3_url',
        'floru_hteam_btn_url' => '_floru_hteam_btn_url',
        'floru_hcta_btn_url'  => '_floru_hcta_btn_url',
        'floru_pcta_btn_url'  => '_floru_pcta_btn_url',
    );
    for ( $i = 1; $i <= 3; $i++ ) {
        $url_fields[ 'floru_svc' . $i . '_image' ] = '_floru_svc' . $i . '_image';
    }
    foreach ( $url_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, esc_url_raw( wp_unslash( $_POST[ $post_key ] ) ) );
        }
    }

    // Sync page title with Floru heading for clean page lists
    $heading = isset( $_POST['floru_ph_heading'] ) ? sanitize_text_field( wp_unslash( $_POST['floru_ph_heading'] ) ) : '';
    if ( ! $heading ) {
        $heading = isset( $_POST['floru_hero_heading'] ) ? sanitize_text_field( wp_unslash( $_POST['floru_hero_heading'] ) ) : '';
    }
    if ( $heading && get_the_title( $post_id ) !== $heading ) {
        remove_action( 'save_post', 'floru_save_page_meta' );
        wp_update_post( array( 'ID' => $post_id, 'post_title' => $heading ) );
        add_action( 'save_post', 'floru_save_page_meta' );
    }
}

