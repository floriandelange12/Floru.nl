<?php
/**
 * Single Client Detail Page
 *
 * @package Astra-Child-Floru
 */

get_header();

$client_id   = get_the_ID();
$logo_url    = get_the_post_thumbnail_url( $client_id, 'medium' );
$website     = get_post_meta( $client_id, '_floru_client_link', true );
$tagline     = get_post_meta( $client_id, '_floru_client_tagline', true );
$industry    = get_post_meta( $client_id, '_floru_client_industry', true );
$video_url   = get_post_meta( $client_id, '_floru_client_video', true );
$video_url_2 = get_post_meta( $client_id, '_floru_client_video_2', true );
$highlights  = get_post_meta( $client_id, '_floru_client_highlights', true );

$clients_page = get_page_by_path( 'clients' );
$clients_url  = $clients_page ? get_permalink( $clients_page ) : home_url( '/clients/' );
?>

<!-- ========== BREADCRUMB ========== -->
<section class="floru-section floru-section--compact floru-section--gray">
    <div class="floru-container">
        <nav class="floru-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( $clients_url ); ?>">Clients</a>
            <span class="floru-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="floru-breadcrumb__current"><?php the_title(); ?></span>
        </nav>
    </div>
</section>

<!-- ========== CLIENT HERO ========== -->
<section class="floru-section">
    <div class="floru-container">
        <div class="floru-client-hero">
            <?php if ( $logo_url ) : ?>
            <div class="floru-client-hero__logo">
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>">
            </div>
            <?php endif; ?>
            <div class="floru-client-hero__info">
                <h1 class="floru-client-hero__title"><?php the_title(); ?></h1>
                <?php if ( $tagline ) : ?>
                    <p class="floru-client-hero__tagline"><?php echo esc_html( $tagline ); ?></p>
                <?php endif; ?>
                <div class="floru-client-hero__meta">
                    <?php if ( $industry ) : ?>
                        <span class="floru-client-hero__badge"><?php echo esc_html( $industry ); ?></span>
                    <?php endif; ?>
                    <?php if ( $website ) : ?>
                        <a href="<?php echo esc_url( $website ); ?>" class="floru-client-hero__website" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <?php echo esc_html( wp_parse_url( $website, PHP_URL_HOST ) ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== CLIENT CONTENT ========== -->
<section class="floru-section floru-section--gray">
    <div class="floru-container">
        <div class="floru-client-detail">
            <div class="floru-client-detail__main">
                <?php if ( $highlights ) : ?>
                <div class="floru-client-detail__highlights">
                    <?php
                    $items = array_filter( array_map( 'trim', explode( "\n", $highlights ) ) );
                    foreach ( $items as $item ) : ?>
                        <div class="floru-client-highlight">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span><?php echo esc_html( $item ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="floru-client-detail__content">
                    <?php the_content(); ?>
                </div>

                <?php if ( $video_url ) : ?>
                <div class="floru-client-detail__video">
                    <?php echo wp_oembed_get( esc_url( $video_url ) ); ?>
                </div>
                <?php endif; ?>

                <?php if ( $video_url_2 ) : ?>
                <div class="floru-client-detail__video">
                    <?php echo wp_oembed_get( esc_url( $video_url_2 ) ); ?>
                </div>
                <?php endif; ?>
            </div>

            <aside class="floru-client-detail__sidebar">
                <div class="floru-client-sidebar-card">
                    <h4 class="floru-client-sidebar-card__heading">At a Glance</h4>
                    <dl class="floru-client-sidebar-card__list">
                        <?php if ( $industry ) : ?>
                        <dt>Sector</dt>
                        <dd><?php echo esc_html( $industry ); ?></dd>
                        <?php endif; ?>
                        <?php if ( $website ) : ?>
                        <dt>Website</dt>
                        <dd><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( wp_parse_url( $website, PHP_URL_HOST ) ); ?></a></dd>
                        <?php endif; ?>
                    </dl>
                    <?php if ( $website ) : ?>
                    <a href="<?php echo esc_url( $website ); ?>" class="floru-btn floru-btn--primary floru-btn--full" target="_blank" rel="noopener noreferrer">Visit Website</a>
                    <?php endif; ?>
                </div>

                <a href="<?php echo esc_url( $clients_url ); ?>" class="floru-client-back-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Back to all Clients
                </a>
            </aside>
        </div>
    </div>
</section>

<!-- ========== CTA ========== -->
<section class="floru-cta">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2>Interested in Working With Us?</h2>
                <p>We would be happy to discuss our experience and how we can support your goals.</p>
            </div>
            <div class="floru-cta__actions">
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="floru-btn floru-btn--primary floru-btn--lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
