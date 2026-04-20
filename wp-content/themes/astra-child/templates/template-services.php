<?php
/**
 * Template Name: Floru — Services
 *
 * @package Astra-Child-Floru
 */

get_header();

$pid = get_the_ID();
$m = function( $key, $default = '' ) use ( $pid ) {
    return floru_get_meta( $pid, $key, $default );
};

$split_service_content = static function( $html ) {
    $html         = (string) $html;
    $intro_html   = '';
    $detail_html  = $html;
    $intro_blocks = array();

    if ( preg_match_all( '/<p\b[^>]*>.*?<\/p>/is', $html, $paragraph_matches ) ) {
        $intro_blocks = array_slice( $paragraph_matches[0], 0, 2 );
    }

    if ( ! empty( $intro_blocks ) ) {
        $intro_html = implode( '', $intro_blocks );

        foreach ( $intro_blocks as $intro_block ) {
            $detail_html = preg_replace( '/' . preg_quote( $intro_block, '/' ) . '/', '', $detail_html, 1 );
        }
    }

    $detail_html = trim( $detail_html );

    if ( ! $intro_html ) {
        $intro_html  = $html;
        $detail_html = '';
    }

    return array(
        'intro'  => $intro_html,
        'detail' => $detail_html,
    );
};
?>

<!-- ========== PAGE HEADER ========== -->
<section class="floru-page-header floru-page-header--services" data-animate="fade-in">
    <div class="floru-container">
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'Our Services' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'How We Support Your Success' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'Comprehensive strategic advisory, stakeholder management, and tender support tailored to the defence and security sector.' ) ); ?></p>
    </div>
</section>

<?php
$svc_defaults = array(
    1 => array(
        'label' => 'Service 01',
        'title' => 'Business Development',
        'desc'  => '<p>Entering or expanding in European defence markets requires more than a good product — it demands an understanding of political dynamics, procurement cycles, and institutional relationships.</p><p>Floru provides strategic business development support for companies looking to grow their presence in the Dutch and European defence and security market. We help you identify opportunities, understand the competitive landscape, and develop a clear path to engagement.</p><h4>What we deliver:</h4><ul><li>Market analysis and opportunity identification</li><li>Go-to-market strategy for the Netherlands and Europe</li><li>Competitive landscape and positioning</li><li>Procurement pipeline monitoring</li><li>Strategic advisory on partnerships and teaming</li></ul>',
        'image' => '',
        'bg'    => '',
    ),
    2 => array(
        'label' => 'Service 02',
        'title' => 'Stakeholder Engagement',
        'desc'  => '<p>Defence procurement decisions involve multiple layers of stakeholders — military end-users, programme managers, political decision-makers, and procurement officials. Reaching the right people with the right message at the right time is critical.</p><p>Floru leverages its established network and institutional knowledge to connect our clients with the stakeholders who matter. We facilitate introductions, support relationship-building, and help our clients navigate complex organisational structures.</p><h4>What we deliver:</h4><ul><li>Stakeholder mapping and analysis</li><li>Introductions to key government and military contacts</li><li>Event and exhibition support</li><li>Communication and messaging strategy</li><li>Government relations advisory</li></ul>',
        'image' => '',
        'bg'    => 'floru-section--gray',
    ),
    3 => array(
        'label' => 'Service 03',
        'title' => 'Tender Support',
        'desc'  => '<p>Government procurement in defence and security is complex, time-sensitive, and highly competitive. A strong tender response requires not only technical excellence but also strategic positioning, clear communication, and full compliance with procurement requirements.</p><p>Floru supports clients throughout the tender process — from early identification and pre-qualification through to proposal development, pricing strategy, and post-submission negotiation. We bring in-depth knowledge of Dutch and European procurement practices.</p><h4>What we deliver:</h4><ul><li>Tender identification and tracking</li><li>Pre-qualification and compliance review</li><li>Proposal strategy and management</li><li>Win theme development</li><li>Post-submission support and debrief guidance</li></ul>',
        'image' => '',
        'bg'    => '',
    ),
);

$svc_support_defaults = array(
    1 => array(
        'eyebrow' => __( 'Strategic note', 'astra-child-floru' ),
        'title'   => __( 'Clarify where to commit before resources are spread too thin.', 'astra-child-floru' ),
        'visual_label' => __( 'Market-entry map', 'astra-child-floru' ),
        'meta'    => array(
            __( 'Focus', 'astra-child-floru' )          => __( 'Market-entry sequencing', 'astra-child-floru' ),
            __( 'Typical support', 'astra-child-floru' ) => __( 'Targeting, positioning, pipeline review', 'astra-child-floru' ),
            __( 'Working style', 'astra-child-floru' )   => __( 'Senior advisory, selective execution', 'astra-child-floru' ),
        ),
    ),
    2 => array(
        'eyebrow' => __( 'Strategic note', 'astra-child-floru' ),
        'title'   => __( 'Map institutions, shape the message, and build the right cadence with the right people.', 'astra-child-floru' ),
        'visual_label' => __( 'Stakeholder field', 'astra-child-floru' ),
        'meta'    => array(
            __( 'Focus', 'astra-child-floru' )          => __( 'Institutional mapping', 'astra-child-floru' ),
            __( 'Typical support', 'astra-child-floru' ) => __( 'Introductions, narrative calibration, stakeholder cadence', 'astra-child-floru' ),
            __( 'Working style', 'astra-child-floru' )   => __( 'Senior-led, discreet, relationship-based', 'astra-child-floru' ),
        ),
    ),
    3 => array(
        'eyebrow' => __( 'Strategic note', 'astra-child-floru' ),
        'title'   => __( 'Strengthen bid positioning early so submissions are sharper and more defensible under pressure.', 'astra-child-floru' ),
        'visual_label' => __( 'Tender structure', 'astra-child-floru' ),
        'meta'    => array(
            __( 'Focus', 'astra-child-floru' )          => __( 'Bid positioning and discipline', 'astra-child-floru' ),
            __( 'Typical support', 'astra-child-floru' ) => __( 'Pre-qualification, win themes, submission review', 'astra-child-floru' ),
            __( 'Working style', 'astra-child-floru' )   => __( 'Deadline-driven and collaborative', 'astra-child-floru' ),
        ),
    ),
);

$svc_fallback_visuals = array(
    1 => get_stylesheet_directory_uri() . '/assets/images/floru-defense-bizdev.jpg',
    2 => get_stylesheet_directory_uri() . '/assets/images/floru-stakeholders.jpg',
    3 => get_stylesheet_directory_uri() . '/assets/images/floru-tender-docs.jpg',
);

for ( $i = 1; $i <= 3; $i++ ) :
    $label = $m( '_floru_svc' . $i . '_label', $svc_defaults[ $i ]['label'] );
    $title = $m( '_floru_svc' . $i . '_title', $svc_defaults[ $i ]['title'] );
    $desc  = $m( '_floru_svc' . $i . '_desc',  $svc_defaults[ $i ]['desc'] );
    $image = $m( '_floru_svc' . $i . '_image' );
    $bg    = $svc_defaults[ $i ]['bg'];
    $eyebrow_label = trim( preg_replace( '/\s*\d+\s*$/', '', (string) $label ) );
    $svc_image_custom = (bool) $image;

    if ( $title ) :
    $section_classes = trim( 'floru-section floru-service-section ' . $bg );
    if ( ! $eyebrow_label ) {
        $eyebrow_label = floru_t( 'Service' );
    }

    $normalized_desc      = floru_normalize_heading_html( $desc, 'h4', 'h3' );
    $service_content_parts = $split_service_content( $normalized_desc );
    $service_support       = $svc_support_defaults[ $i ];
    $service_visual_src    = $svc_image_custom ? $image : $svc_fallback_visuals[ $i ];
    $service_visual_alt    = $svc_image_custom ? $title : $service_support['visual_label'];
?>
<section class="<?php echo esc_attr( $section_classes ); ?>" data-animate>
    <div class="floru-container">
        <div class="floru-service-panel">
            <div class="floru-service-panel__content">
                <div class="floru-service-panel__eyebrow">
                    <span class="floru-section-label"><?php echo esc_html( $eyebrow_label ); ?></span>
                    <span class="floru-service-panel__count"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
                </div>
                <h2><?php echo esc_html( $title ); ?></h2>
                <div class="floru-divider"></div>
                <div class="floru-service-rich-text floru-service-rich-text--intro">
                    <?php echo wp_kses_post( $service_content_parts['intro'] ); ?>
                </div>

                <?php if ( $service_content_parts['detail'] ) : ?>
                    <div class="floru-service-panel__details">
                        <div class="floru-service-rich-text floru-service-rich-text--details">
                            <?php echo wp_kses_post( $service_content_parts['detail'] ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <aside class="floru-service-panel__rail">
                <figure class="floru-context-frame floru-context-frame--service<?php echo $svc_image_custom ? '' : ' floru-context-frame--photo'; ?>">
                    <div class="floru-context-frame__media">
                        <img src="<?php echo esc_url( $service_visual_src ); ?>" alt="<?php echo esc_attr( $service_visual_alt ); ?>" loading="lazy" class="floru-img-block">
                    </div>
                </figure>

                <div class="floru-context-panel floru-context-panel--service">
                    <span class="floru-context-panel__eyebrow"><?php echo esc_html( $service_support['eyebrow'] ); ?></span>
                    <h3 class="floru-context-panel__title"><?php echo esc_html( $service_support['title'] ); ?></h3>
                    <dl class="floru-context-panel__meta" aria-label="<?php echo esc_attr( sprintf( __( '%s support profile', 'astra-child-floru' ), $title ) ); ?>">
                        <?php foreach ( $service_support['meta'] as $service_support_label => $service_support_value ) : ?>
                            <div>
                                <dt><?php echo esc_html( $service_support_label ); ?></dt>
                                <dd><?php echo esc_html( $service_support_value ); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php endif; endfor; ?>

<!-- ========== CTA ========== -->
<section class="floru-section floru-section--navy floru-cta" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( $m( '_floru_pcta_heading', 'Let Us Help You Succeed' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_pcta_description', 'Every client and project is unique. Contact us to discuss how our services can be tailored to your objectives.' ) ); ?></p>
            </div>
            <div class="floru-cta__actions">
                <?php
                $cta_text = $m( '_floru_pcta_btn_text', 'Get in Touch' );
                $cta_url  = $m( '_floru_pcta_btn_url', home_url( '/contact/' ) );
                if ( $cta_text ) : ?>
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
