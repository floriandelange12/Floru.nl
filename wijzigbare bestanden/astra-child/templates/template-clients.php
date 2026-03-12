<?php
/**
 * Template Name: Floru — Clients
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
<section class="floru-page-header">
    <div class="floru-container">
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'Our Clients' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Clients & References' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'We are proud to work with leading organisations in the international defence and security industry.' ) ); ?></p>
    </div>
</section>

<!-- ========== CLIENTS INTRO ========== -->
<section class="floru-section">
    <div class="floru-container floru-container--narrow floru-text-center">
        <?php
        $content = get_the_content();
        if ( $content && trim( strip_tags( $content ) ) ) : ?>
            <div class="floru-intro-text">
                <?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>
            </div>
        <?php else : ?>
            <p class="floru-intro-text">Over the years, Floru has supported a range of international defence and security companies — from established primes to innovative mid-tier manufacturers. Our client relationships are built on trust, discretion, and a shared commitment to achieving concrete results.</p>
            <p class="floru-intro-text">Below you will find a selection of the organisations we have had the privilege of working with.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ========== CLIENT LOGOS ========== -->
<section class="floru-section floru-section--gray">
    <div class="floru-container">
        <div class="floru-clients-grid">
            <?php
            $clients = new WP_Query( array(
                'post_type'      => 'floru_client',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ) );
            if ( $clients->have_posts() ) :
                while ( $clients->have_posts() ) : $clients->the_post();
                    $client_link = get_post_meta( get_the_ID(), '_floru_client_link', true );
                    $logo_url    = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                    $desc        = get_the_content();
                    if ( ! $logo_url ) continue;
                    if ( $client_link ) : ?>
                        <a href="<?php echo esc_url( $client_link ); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>" class="floru-client-logo">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>" class="floru-client-logo">
                    <?php endif; ?>
                <?php endwhile; wp_reset_postdata();
            else : ?>
                <p>No clients have been added yet. Go to <strong>Clients</strong> in the WordPress admin to add them.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ========== CTA ========== -->
<section class="floru-section floru-section--navy floru-cta">
    <div class="floru-container">
        <h2><?php echo esc_html( $m( '_floru_pcta_heading', 'Interested in Working With Us?' ) ); ?></h2>
        <p><?php echo esc_html( $m( '_floru_pcta_description', 'We would be happy to discuss our experience and how we can support your goals. References are available upon request.' ) ); ?></p>
        <div class="floru-cta__actions">
            <?php
            $cta_text = $m( '_floru_pcta_btn_text', 'Contact Us' );
            $cta_url  = $m( '_floru_pcta_btn_url', home_url( '/contact/' ) );
            if ( $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_text ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
