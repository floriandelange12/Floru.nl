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
?>

<!-- ========== PAGE HEADER ========== -->
<section class="floru-page-header" data-animate="fade-in">
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

for ( $i = 1; $i <= 3; $i++ ) :
    $label = $m( '_floru_svc' . $i . '_label', $svc_defaults[ $i ]['label'] );
    $title = $m( '_floru_svc' . $i . '_title', $svc_defaults[ $i ]['title'] );
    $desc  = $m( '_floru_svc' . $i . '_desc',  $svc_defaults[ $i ]['desc'] );
    $image = $m( '_floru_svc' . $i . '_image' );
    $bg    = $svc_defaults[ $i ]['bg'];
    $reversed = ( $i === 2 );

    if ( ! $image ) {
        $svc_ver = '2';
        $svc_images = array(
            1 => get_stylesheet_directory_uri() . '/assets/images/illustration-business-dev.svg?v=' . $svc_ver,
            2 => get_stylesheet_directory_uri() . '/assets/images/illustration-stakeholder.svg?v=' . $svc_ver,
            3 => get_stylesheet_directory_uri() . '/assets/images/illustration-tender.svg?v=' . $svc_ver,
        );
        $image = $svc_images[ $i ];
    }
    if ( $title ) :
?>
<section class="floru-section <?php echo esc_attr( $bg ); ?>" data-animate>
    <div class="floru-container">
        <div class="floru-about-grid">
            <?php if ( $reversed ) : ?>
            <div>
                <div class="floru-intro__image-wrapper">
                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" class="floru-img-block">
                </div>
            </div>
            <?php endif; ?>
            <div>
                <span class="floru-section-label"><?php echo esc_html( $label ); ?></span>
                <h2><?php echo esc_html( $title ); ?></h2>
                <div class="floru-divider"></div>
                <?php echo wp_kses_post( $desc ); ?>
            </div>
            <?php if ( ! $reversed ) : ?>
            <div>
                <div class="floru-intro__image-wrapper">
                    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" class="floru-img-block">
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; endfor; ?>

<!-- ========== CTA ========== -->
<section class="floru-section floru-section--navy floru-cta" data-animate="fade-in">
    <div class="floru-container">
        <h2><?php echo esc_html( $m( '_floru_pcta_heading', 'Let Us Help You Succeed' ) ); ?></h2>
        <p><?php echo esc_html( $m( '_floru_pcta_description', 'Every client and project is unique. Contact us to discuss how our services can be tailored to your objectives.' ) ); ?></p>
        <div class="floru-cta__actions">
            <?php
            $cta_text = $m( '_floru_pcta_btn_text', 'Get in Touch' );
            $cta_url  = $m( '_floru_pcta_btn_url', home_url( '/contact/' ) );
            if ( $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_text ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
