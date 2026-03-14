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

/* Self-hosted video paths (slug-based). */
$slug        = get_post_field( 'post_name', $client_id );
$video_base  = ABSPATH . 'wp-content/uploads/videos/';
$video_web   = content_url( 'uploads/videos/' );
$has_video_1 = $video_url  && file_exists( $video_base . $slug . '.mp4' );
$has_video_2 = $video_url_2 && file_exists( $video_base . $slug . '-2.mp4' );

$highlight_items = $highlights ? array_filter( array_map( 'trim', explode( "\n", $highlights ) ) ) : array();
$display_name    = $website ? wp_parse_url( $website, PHP_URL_HOST ) : '';
?>

<!-- ========== HERO (breadcrumb integrated) ========== -->
<section class="floru-client-page-hero">
    <div class="floru-container">
        <nav class="floru-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( $clients_url ); ?>">Clients</a>
            <span class="floru-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="floru-breadcrumb__current"><?php the_title(); ?></span>
        </nav>

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
                            <?php echo esc_html( $display_name ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== HIGHLIGHTS ========== -->
<?php if ( $highlight_items ) : ?>
<section class="floru-client-highlights-section">
    <div class="floru-container">
        <p class="floru-client-highlights-section__label">Key Capabilities</p>
        <div class="floru-client-detail__highlights">
            <?php foreach ( $highlight_items as $item ) : ?>
                <div class="floru-client-highlight">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span><?php echo esc_html( $item ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== VIDEO (primary — cinematic) ========== -->
<?php if ( $has_video_1 ) : ?>
<section class="floru-client-video-section" data-slug="<?php echo esc_attr( $slug ); ?>">
    <div class="floru-container">
        <div class="floru-video">
            <video autoplay muted loop playsinline preload="auto">
                <source src="<?php echo esc_url( $video_web . $slug . '.mp4' ); ?>" type="video/mp4">
            </video>
            <button class="floru-video__mute-toggle" type="button" aria-label="Toggle sound">
                <svg class="floru-icon-muted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                <svg class="floru-icon-unmuted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== DESCRIPTION + SIDEBAR ========== -->
<section class="floru-client-content-section">
    <div class="floru-container">
        <div class="floru-client-body">
            <div class="floru-client-body__content">
                <?php the_content(); ?>
            </div>
            <aside class="floru-client-body__sidebar">
                <div class="floru-client-sidebar-card">
                    <h4 class="floru-client-sidebar-card__heading">At a Glance</h4>
                    <dl class="floru-client-sidebar-card__list">
                        <?php if ( $industry ) : ?>
                        <dt>Sector</dt>
                        <dd><?php echo esc_html( $industry ); ?></dd>
                        <?php endif; ?>
                        <?php if ( $highlight_items ) : ?>
                        <dt>Highlights</dt>
                        <dd><?php echo count( $highlight_items ); ?> key capabilities</dd>
                        <?php endif; ?>
                        <?php if ( $has_video_1 ) : ?>
                        <dt>Media</dt>
                        <dd><?php echo $has_video_2 ? '2 videos' : '1 video'; ?></dd>
                        <?php endif; ?>
                        <?php if ( $website ) : ?>
                        <dt>Website</dt>
                        <dd><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $display_name ); ?></a></dd>
                        <?php endif; ?>
                    </dl>
                    <?php if ( $website ) : ?>
                    <a href="<?php echo esc_url( $website ); ?>" class="floru-btn floru-btn--primary floru-btn--full" target="_blank" rel="noopener noreferrer">Visit Website</a>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ========== VIDEO 2 (optional) ========== -->
<?php if ( $has_video_2 ) : ?>
<section class="floru-client-video-section floru-client-video-section--alt" data-slug="<?php echo esc_attr( $slug ); ?>">
    <div class="floru-container">
        <div class="floru-video">
            <video autoplay muted loop playsinline preload="auto">
                <source src="<?php echo esc_url( $video_web . $slug . '-2.mp4' ); ?>" type="video/mp4">
            </video>
            <button class="floru-video__mute-toggle" type="button" aria-label="Toggle sound">
                <svg class="floru-icon-muted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                <svg class="floru-icon-unmuted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== CTA ========== -->
<section class="floru-cta">
    <div class="floru-container">
        <div class="floru-client-back-link-wrapper">
            <a href="<?php echo esc_url( $clients_url ); ?>" class="floru-client-back-link floru-client-back-link--light">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to all Clients
            </a>
        </div>
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
<script>
document.querySelectorAll('.floru-video__mute-toggle').forEach(function(btn){
    btn.addEventListener('click',function(){
        var v=this.closest('.floru-video').querySelector('video');
        v.muted=!v.muted;
        this.closest('.floru-video').classList.toggle('is-unmuted',!v.muted);
    });
});
</script>
