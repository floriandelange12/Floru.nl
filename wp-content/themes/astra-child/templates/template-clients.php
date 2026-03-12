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
        <?php endif; ?>
    </div>
</section>

<?php
$clients = new WP_Query( array(
    'post_type'      => 'floru_client',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
) );
?>

<?php if ( $clients->have_posts() ) : ?>
<!-- ========== LOGO BAND ========== -->
<section class="floru-section floru-section--gray floru-section--compact">
    <div class="floru-container">
        <div class="floru-clients-grid">
            <?php
            while ( $clients->have_posts() ) : $clients->the_post();
                $logo_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                if ( ! $logo_url ) continue;
            ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>" class="floru-client-logo">
            <?php endwhile; $clients->rewind_posts(); ?>
        </div>
    </div>
</section>

<!-- ========== CLIENT DETAIL CARDS ========== -->
<section class="floru-section">
    <div class="floru-container">
        <div class="floru-client-cards">
            <?php
            while ( $clients->have_posts() ) : $clients->the_post();
                $detail_url  = get_permalink();
                $logo_url    = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                $desc        = get_the_excerpt();
                $industry    = get_post_meta( get_the_ID(), '_floru_client_industry', true );
            ?>
            <a href="<?php echo esc_url( $detail_url ); ?>" class="floru-client-card floru-client-card--link">
                <?php if ( $logo_url ) : ?>
                <div class="floru-client-card__logo">
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>">
                </div>
                <?php endif; ?>
                <div class="floru-client-card__body">
                    <h3 class="floru-client-card__name"><?php the_title(); ?></h3>
                    <?php if ( $industry ) : ?>
                        <span class="floru-client-card__industry"><?php echo esc_html( $industry ); ?></span>
                    <?php endif; ?>
                    <?php if ( $desc && trim( strip_tags( $desc ) ) ) : ?>
                        <div class="floru-client-card__desc">
                            <p><?php echo esc_html( wp_trim_words( strip_tags( $desc ), 30, '…' ) ); ?></p>
                        </div>
                    <?php endif; ?>
                    <span class="floru-client-card__link">
                        View details <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php else : ?>
<section class="floru-section floru-section--gray">
    <div class="floru-container">
        <p>No clients have been added yet. Go to <strong>Clients</strong> in the WordPress admin to add them.</p>
    </div>
</section>
<?php endif; ?>

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
