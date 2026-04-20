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

$balanced_logo_columns = static function( $count, $max_columns = 6 ) {
    $count       = max( 0, (int) $count );
    $max_columns = max( 1, min( $count ?: 1, (int) $max_columns ) );

    if ( $count <= 1 ) {
        return $max_columns;
    }

    $best_columns    = 1;
    $best_fill_ratio = -1;
    $best_rows       = PHP_INT_MAX;

    for ( $columns = 2; $columns <= $max_columns; $columns++ ) {
        $rows           = (int) ceil( $count / $columns );
        $last_row_count = $count - ( ( $rows - 1 ) * $columns );
        $fill_ratio     = $last_row_count / $columns;

        if (
            $fill_ratio > $best_fill_ratio ||
            ( abs( $fill_ratio - $best_fill_ratio ) < 0.0001 && $rows < $best_rows ) ||
            ( abs( $fill_ratio - $best_fill_ratio ) < 0.0001 && $rows === $best_rows && $columns > $best_columns )
        ) {
            $best_columns    = $columns;
            $best_fill_ratio = $fill_ratio;
            $best_rows       = $rows;
        }
    }

    return $best_columns;
};

$clients = new WP_Query( array(
    'post_type'      => 'floru_client',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
) );

$clients_total     = (int) $clients->found_posts;
$client_industries = array();

if ( ! empty( $clients->posts ) ) {
    foreach ( $clients->posts as $client_post ) {
        $industry = floru_get_translated_post_meta( $client_post->ID, '_floru_client_industry' );
        if ( $industry ) {
            $client_industries[ sanitize_title( $industry ) ] = $industry;
        }
    }
}

$client_sector_labels = array_slice( array_values( $client_industries ), 0, 4 );
$client_logo_items    = array();

if ( ! empty( $clients->posts ) ) {
    foreach ( $clients->posts as $client_post ) {
        $logo_url = get_the_post_thumbnail_url( $client_post->ID, 'medium' );

        if ( ! $logo_url ) {
            continue;
        }

        if ( count( $client_logo_items ) >= 10 ) {
            break;
        }

        $client_logo_items[] = array(
            'slug'     => get_post_field( 'post_name', $client_post->ID ),
            'logo_url' => $logo_url,
            'title'    => floru_get_translated_post_title_raw( $client_post->ID ),
        );
    }
}

$client_logo_columns = $balanced_logo_columns( count( $client_logo_items ) );
?>

<!-- ========== PAGE HEADER ========== -->
<section class="floru-page-header floru-page-header--clients" data-animate="fade-in">
    <div class="floru-container">
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'Our Clients' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Clients & References' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'We are proud to work with leading organisations in the international defence and security industry.' ) ); ?></p>
    </div>
</section>

<!-- ========== CLIENTS INTRO ========== -->
<section class="floru-section floru-section--clients-intro" data-animate>
    <div class="floru-container floru-container--narrow">
        <div class="floru-clients-intro">
            <div class="floru-clients-intro__layout">
                <div class="floru-clients-intro__lead">
                    <?php
                    $content = floru_get_translated_post_content_raw( $pid );
                    if ( $content && trim( strip_tags( $content ) ) ) : ?>
                        <div class="floru-intro-text">
                            <?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>
                        </div>
                    <?php else : ?>
                        <p class="floru-intro-text"><?php echo esc_html( floru_t( 'Over the years, Floru has supported a range of international defence and security companies — from established primes to innovative mid-tier manufacturers. Our client relationships are built on trust, discretion, and a shared commitment to achieving concrete results.' ) ); ?></p>
                    <?php endif; ?>

                    <div class="floru-clients-intro__note">
                        <span class="floru-clients-intro__eyebrow"><?php echo esc_html( floru_t( 'How Floru is engaged' ) ); ?></span>
                        <p><?php echo esc_html( floru_t( 'Support ranges from market-entry and stakeholder positioning to tender preparation, local coordination, and selective long-term advisory mandates.' ) ); ?></p>
                    </div>
                </div>

                <aside class="floru-clients-intro__aside">
                    <?php if ( $clients_total || $client_sector_labels ) : ?>
                        <div class="floru-clients-intro__evidence">
                            <span class="floru-clients-intro__eyebrow"><?php echo esc_html( floru_t( 'Client overview' ) ); ?></span>
                            <?php if ( $clients_total ) : ?>
                                <div class="floru-clients-intro__metrics" aria-label="<?php echo esc_attr__( 'Client overview', 'astra-child-floru' ); ?>">
                                    <div class="floru-clients-intro__metric">
                                        <strong><?php echo esc_html( $clients_total ); ?></strong>
                                        <span><?php echo esc_html( floru_t( 'Profiled organisations' ) ); ?></span>
                                    </div>
                                    <div class="floru-clients-intro__metric">
                                        <strong><?php echo esc_html( max( 1, count( $client_industries ) ) ); ?></strong>
                                        <span><?php echo esc_html( floru_t( 'Industry segments' ) ); ?></span>
                                    </div>
                                </div>
                                <p class="floru-clients-intro__evidence-copy"><?php echo esc_html( floru_t( 'Discreet, long-term client relationships built around trust, continuity, and selective advisory mandates.' ) ); ?></p>
                            <?php endif; ?>

                            <?php if ( $client_sector_labels ) : ?>
                                <div class="floru-clients-intro__sectors">
                                    <span class="floru-clients-intro__eyebrow"><?php echo esc_html( floru_t( 'Active across' ) ); ?></span>
                                    <ul class="floru-clients-intro__sector-list" aria-label="<?php echo esc_attr__( 'Client sectors', 'astra-child-floru' ); ?>">
                                        <?php foreach ( $client_sector_labels as $client_sector_label ) : ?>
                                            <li><?php echo esc_html( $client_sector_label ); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>
</section>

<?php if ( $clients->have_posts() ) : ?>
<!-- ========== LOGO BAND ========== -->
<section class="floru-section floru-section--gray floru-section--compact" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-section-header floru-section-header--clients-logos">
            <span class="floru-section-label"><?php esc_html_e( 'Selected organisations', 'astra-child-floru' ); ?></span>
            <h2><?php esc_html_e( 'Trusted by specialist defence and security organisations', 'astra-child-floru' ); ?></h2>
            <p><?php esc_html_e( 'A representative selection of companies Floru has supported across stakeholder engagement, market positioning, and tender preparation.', 'astra-child-floru' ); ?></p>
        </div>
        <div class="floru-clients-grid" style="<?php echo esc_attr( '--floru-clients-grid-columns-tablet:' . $client_logo_columns . ';' ); ?>">
            <?php foreach ( $client_logo_items as $client_logo_item ) : ?>
                <div class="floru-client-logo-tile floru-client-logo-tile--<?php echo esc_attr( $client_logo_item['slug'] ); ?>">
                    <img src="<?php echo esc_url( $client_logo_item['logo_url'] ); ?>" alt="<?php echo esc_attr( $client_logo_item['title'] ); ?>" class="floru-client-logo floru-client-logo--<?php echo esc_attr( $client_logo_item['slug'] ); ?>">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== CLIENT DETAIL CARDS ========== -->
<section class="floru-section" data-animate>
    <div class="floru-container">
        <div class="floru-section-header floru-section-header--clients-profiles">
            <span class="floru-section-label"><?php esc_html_e( 'Profiled references', 'astra-child-floru' ); ?></span>
            <h2><?php esc_html_e( 'Where Floru has supported positioning, growth, and engagement', 'astra-child-floru' ); ?></h2>
            <p><?php esc_html_e( 'Each profile gives a short view of the organisations Floru has supported and the strategic context in which those relationships sit.', 'astra-child-floru' ); ?></p>
        </div>
        <div class="floru-client-cards" data-animate-stagger>
            <?php
            while ( $clients->have_posts() ) : $clients->the_post();
                $detail_url  = get_permalink();
                $logo_url    = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                $desc        = floru_get_translated_post_excerpt_raw( get_the_ID() );
                $industry    = floru_get_translated_post_meta( get_the_ID(), '_floru_client_industry' );
                $initials    = floru_get_initials( floru_get_translated_post_title_raw( get_the_ID() ) );
                if ( ! $desc ) {
                    $raw = wp_strip_all_tags( strip_shortcodes( floru_get_translated_post_content_raw( get_the_ID() ) ) );
                    if ( $raw ) {
                        $desc = wp_trim_words( $raw, 30, '&hellip;' );
                    }
                }
            ?>
            <a href="<?php echo esc_url( $detail_url ); ?>" class="floru-client-card floru-client-card--link">
                <div class="floru-client-card__logo<?php echo $logo_url ? '' : ' floru-client-card__logo--placeholder'; ?>">
                <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>">
                <?php else : ?>
                    <span class="floru-client-card__logo-fallback" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
                <?php endif; ?>
                </div>
                <div class="floru-client-card__body">
                    <h3 class="floru-client-card__name"><?php echo esc_html( floru_get_translated_post_title_raw( get_the_ID() ) ); ?></h3>
                    <?php if ( $industry ) : ?>
                        <span class="floru-client-card__industry"><?php echo esc_html( $industry ); ?></span>
                    <?php endif; ?>
                    <?php if ( $desc && trim( strip_tags( $desc ) ) ) : ?>
                        <div class="floru-client-card__desc">
                            <p><?php echo esc_html( wp_trim_words( strip_tags( $desc ), 30, '…' ) ); ?></p>
                        </div>
                    <?php endif; ?>
                    <span class="floru-client-card__link">
                        <?php echo esc_html( floru_t( 'View details' ) ); ?> <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
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
        <p><?php echo wp_kses_post( floru_t( 'No clients have been added yet. Go to <strong>Clients</strong> in the WordPress admin to add them.' ) ); ?></p>
    </div>
</section>
<?php endif; ?>

<!-- ========== CTA ========== -->
<section class="floru-section floru-section--navy floru-cta" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( $m( '_floru_pcta_heading', 'Interested in Working With Us?' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_pcta_description', 'We would be happy to discuss our experience and how we can support your goals. References are available upon request.' ) ); ?></p>
            </div>
            <div class="floru-cta__actions">
                <?php
                $cta_text = $m( '_floru_pcta_btn_text', 'Contact Us' );
                $cta_url  = $m( '_floru_pcta_btn_url', home_url( '/contact/' ) );
                if ( $cta_text ) : ?>
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
