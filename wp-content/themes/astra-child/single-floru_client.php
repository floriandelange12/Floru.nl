<?php
/**
 * Single Client Detail Page
 *
 * @package Astra-Child-Floru
 */

get_header();

$client_id   = get_the_ID();
$client_name = floru_get_translated_post_title_raw( $client_id );
$logo_url    = get_the_post_thumbnail_url( $client_id, 'medium' );
$website     = get_post_meta( $client_id, '_floru_client_link', true );
$tagline     = floru_get_translated_post_meta( $client_id, '_floru_client_tagline' );
$industry    = floru_get_translated_post_meta( $client_id, '_floru_client_industry' );
$video_url   = get_post_meta( $client_id, '_floru_client_video', true );
$video_url_2 = get_post_meta( $client_id, '_floru_client_video_2', true );
$highlights  = floru_get_translated_post_meta( $client_id, '_floru_client_highlights' );

$clients_page = get_page_by_path( 'clients' );
$clients_url  = $clients_page ? get_permalink( $clients_page ) : home_url( '/clients/' );

/* Self-hosted video paths (slug-based). */
$slug        = get_post_field( 'post_name', $client_id );
$video_base  = ABSPATH . 'wp-content/uploads/videos/';
$video_web   = content_url( 'uploads/videos/' );
$has_video_1 = $video_url  && file_exists( $video_base . $slug . '.mp4' );
$has_video_2 = $video_url_2 && file_exists( $video_base . $slug . '-2.mp4' );
$gallery_images = floru_get_client_gallery_images( $slug );

$highlight_items = $highlights ? array_filter( array_map( 'trim', explode( "\n", $highlights ) ) ) : array();
$highlight_count = count( $highlight_items );
$display_name    = $website ? wp_parse_url( $website, PHP_URL_HOST ) : '';
$initials        = floru_get_initials( $client_name );
$client_summary  = trim( wp_strip_all_tags( floru_get_translated_post_excerpt_raw( $client_id ) ) );

if ( ! $client_summary ) {
    $client_body_text = trim( wp_strip_all_tags( strip_shortcodes( floru_get_translated_post_content_raw( $client_id ) ) ) );

    if ( $client_body_text ) {
        $client_summary = wp_trim_words( $client_body_text, 28, '&hellip;' );
    }
}

$show_hero_meta = $industry || $website;
$highlight_grid_class = 'floru-client-detail__highlights';

if ( 1 === $highlight_count ) {
    $highlight_grid_class .= ' floru-client-detail__highlights--cols-1';
} elseif ( 2 === $highlight_count || 4 === $highlight_count ) {
    $highlight_grid_class .= ' floru-client-detail__highlights--cols-2';
} elseif ( $highlight_count > 0 ) {
    $highlight_grid_class .= ' floru-client-detail__highlights--cols-3';
}
?>

<!-- ========== HERO (breadcrumb integrated) ========== -->
<section class="floru-client-page-hero">
    <div class="floru-container">
        <nav class="floru-breadcrumb" aria-label="<?php echo esc_attr( floru_t( 'Breadcrumb' ) ); ?>">
            <a href="<?php echo esc_url( $clients_url ); ?>"><?php echo esc_html( floru_t( 'Clients' ) ); ?></a>
            <span class="floru-breadcrumb__sep" aria-hidden="true">/</span>
            <span class="floru-breadcrumb__current"><?php echo esc_html( $client_name ); ?></span>
        </nav>

        <div class="floru-client-hero-shell floru-client-hero-shell--single">
            <div class="floru-client-hero">
                <?php if ( $logo_url ) : ?>
                <div class="floru-client-hero__logo">
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $client_name ); ?>">
                </div>
                <?php else : ?>
                <div class="floru-client-hero__logo floru-client-hero__logo--placeholder" aria-hidden="true">
                    <span class="floru-client-hero__logo-fallback"><?php echo esc_html( $initials ); ?></span>
                </div>
                <?php endif; ?>
                <div class="floru-client-hero__info">
                    <h1 class="floru-client-hero__title"><?php echo esc_html( $client_name ); ?></h1>
                    <?php if ( $tagline ) : ?>
                        <p class="floru-client-hero__tagline"><?php echo esc_html( $tagline ); ?></p>
                    <?php endif; ?>
                    <?php if ( $show_hero_meta ) : ?>
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
                    <?php endif; ?>
                    <?php if ( $client_summary ) : ?>
                        <p class="floru-client-hero__summary"><?php echo esc_html( $client_summary ); ?></p>
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
        <div class="floru-client-highlights-section__intro">
            <p class="floru-client-highlights-section__label"><?php echo esc_html( floru_t( 'Strategic contribution' ) ); ?></p>
            <h2 class="floru-client-highlights-section__title"><?php echo esc_html( floru_t( 'Where Floru added focus and momentum' ) ); ?></h2>
            <p class="floru-client-highlights-section__text"><?php echo esc_html( floru_tf( 'A concise view of the themes and workstreams Floru supported for %s.', $client_name ) ); ?></p>
        </div>
        <div class="<?php echo esc_attr( $highlight_grid_class ); ?>">
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
        <div class="floru-client-media-shell">
            <div class="floru-client-media-shell__header">
                <p class="floru-client-media-shell__label"><?php echo esc_html( floru_t( 'Visual context' ) ); ?></p>
                <h2 class="floru-client-media-shell__title"><?php echo esc_html( floru_tf( 'A closer look at %s', $client_name ) ); ?></h2>
            </div>
            <div class="floru-video">
                <video autoplay muted loop playsinline preload="auto">
                    <source src="<?php echo esc_url( $video_web . $slug . '.mp4' ); ?>" type="video/mp4">
                </video>
                <button class="floru-video__mute-toggle" type="button" aria-label="<?php echo esc_attr( floru_t( 'Toggle sound' ) ); ?>">
                    <svg class="floru-icon-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                    <svg class="floru-icon-unmuted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    <span class="floru-mute-label"><?php echo esc_html( floru_t( 'Sound' ) ); ?></span>
                </button>
            </div>
        </div>
    </div>
</section>
<?php else :
    if ( $gallery_images ) : ?>
<section class="floru-client-gallery-section">
    <div class="floru-container">
        <div class="floru-client-media-shell">
            <div class="floru-client-media-shell__header">
                <p class="floru-client-media-shell__label"><?php echo esc_html( floru_t( 'Visual context' ) ); ?></p>
                <h2 class="floru-client-media-shell__title"><?php echo esc_html( floru_tf( 'A closer look at %s', $client_name ) ); ?></h2>
            </div>
            <div class="floru-client-gallery">
                <?php foreach ( $gallery_images as $idx => $img ) : ?>
                <figure class="floru-client-gallery__item floru-client-gallery__item--<?php echo (int) $idx; ?>">
                    <img src="<?php echo esc_url( $img['url'] ); ?>"
                         alt="<?php echo esc_attr( $img['alt'] ); ?>"
                         loading="lazy"
                         decoding="async">
                </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; endif; ?>

<!-- ========== DESCRIPTION + SIDEBAR ========== -->
<section class="floru-client-content-section">
    <div class="floru-container">
        <div class="floru-client-body">
            <div class="floru-client-body__content">
                <article class="floru-client-article">
                    <div class="floru-client-article__header">
                        <p class="floru-client-article__eyebrow"><?php echo esc_html( floru_t( 'Engagement overview' ) ); ?></p>
                        <h2 class="floru-client-article__title"><?php echo esc_html( floru_t( 'Context, positioning and delivery' ) ); ?></h2>
                    </div>
                    <div class="floru-client-article__prose">
                        <?php echo wp_kses_post( apply_filters( 'the_content', floru_get_translated_post_content_raw( $client_id ) ) ); ?>
                    </div>
                </article>
            </div>
            <aside class="floru-client-body__sidebar">
                <div class="floru-client-sidebar-card floru-client-sidebar-card--cta">
                    <span class="floru-client-sidebar-card__eyebrow"><?php echo esc_html( floru_t( 'Strategic fit' ) ); ?></span>
                    <h2 class="floru-client-sidebar-card__heading"><?php echo esc_html( floru_t( 'Comparable brief?' ) ); ?></h2>
                    <p><?php echo esc_html( floru_t( 'Floru supports defence and security organisations that need sharper positioning, stronger stakeholder alignment, and clearer routes to decision in the Dutch and European market.' ) ); ?></p>
                    <div class="floru-client-sidebar-card__actions">
                        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="floru-btn floru-btn--primary floru-btn--full"><?php echo esc_html( floru_t( 'Contact Floru' ) ); ?></a>
                    </div>
                    <a href="<?php echo esc_url( $clients_url ); ?>" class="floru-client-back-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        <?php echo esc_html( floru_t( 'Back to all Clients' ) ); ?>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ========== VIDEO 2 (optional) ========== -->
<?php if ( $has_video_2 ) : ?>
<section class="floru-client-video-section floru-client-video-section--alt" data-slug="<?php echo esc_attr( $slug ); ?>">
    <div class="floru-container">
        <div class="floru-client-media-shell floru-client-media-shell--secondary">
            <div class="floru-client-media-shell__header">
                <p class="floru-client-media-shell__label"><?php echo esc_html( floru_t( 'Additional visual' ) ); ?></p>
                <h2 class="floru-client-media-shell__title"><?php echo esc_html( floru_t( 'Further context from the engagement' ) ); ?></h2>
            </div>
            <div class="floru-video">
                <video autoplay muted loop playsinline preload="auto">
                    <source src="<?php echo esc_url( $video_web . $slug . '-2.mp4' ); ?>" type="video/mp4">
                </video>
                <button class="floru-video__mute-toggle" type="button" aria-label="<?php echo esc_attr( floru_t( 'Toggle sound' ) ); ?>">
                    <svg class="floru-icon-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                    <svg class="floru-icon-unmuted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    <span class="floru-mute-label"><?php echo esc_html( floru_t( 'Sound' ) ); ?></span>
                </button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========== CTA ========== -->
<section class="floru-cta">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( floru_t( 'Interested in Working With Us?' ) ); ?></h2>
                <p><?php echo esc_html( floru_t( 'We would be happy to discuss our experience and how we can support your goals.' ) ); ?></p>
            </div>
            <div class="floru-cta__actions">
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( floru_t( 'Contact Us' ) ); ?></a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
<script>
document.querySelectorAll('.floru-video').forEach(function(wrap){
    var v=wrap.querySelector('video');
    var btn=wrap.querySelector('.floru-video__mute-toggle');
    if(!v||!btn) return;

    /* Detect audio track once enough data is loaded */
    function checkAudio(){
        if(v.readyState>=2){
            var hasAudio=(typeof v.webkitAudioDecodedByteCount!=='undefined')
                ? v.webkitAudioDecodedByteCount>0
                : (typeof v.mozHasAudio!=='undefined')
                    ? v.mozHasAudio
                    : (v.audioTracks && v.audioTracks.length>0);
            if(hasAudio) wrap.classList.add('has-audio');
        }
    }
    v.addEventListener('loadeddata',checkAudio);
    /* Re-check after a short playback window (some browsers need frames decoded) */
    setTimeout(checkAudio, 1500);
    checkAudio();

    btn.addEventListener('click',function(){
        v.muted=!v.muted;
        wrap.classList.toggle('is-unmuted',!v.muted);
    });
});
</script>
