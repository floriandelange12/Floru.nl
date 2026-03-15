<?php
/**
 * 404 error page template.
 *
 * @package Astra-Child-Floru
 */

get_header();
?>

<section class="floru-page-header">
    <div class="floru-container">
        <span class="floru-section-label">Page Not Found</span>
        <h1>404</h1>
        <p>The page you are looking for does not exist or has been moved.</p>
    </div>
</section>

<section class="floru-section">
    <div class="floru-container floru-container--narrow floru-text-center">
        <p class="floru-intro-text" style="margin-bottom: 48px;">
            Let us help you find what you need. Visit one of our main pages below, or use the navigation menu above.
        </p>

        <div class="floru-services-grid" style="margin-bottom: 48px;">
            <?php
            $pages = array(
                array( 'Home',     home_url( '/' ),          'briefcase' ),
                array( 'Services', home_url( '/services/' ), 'trending-up' ),
                array( 'Contact',  home_url( '/contact/' ),  'mail' ),
            );
            foreach ( $pages as $i => $page ) : ?>
            <a href="<?php echo esc_url( $page[1] ); ?>" class="floru-service-card">
                <div class="floru-service-card__icon">
                    <?php echo floru_icon( $page[2] ); ?>
                </div>
                <h3><?php echo esc_html( $page[0] ); ?></h3>
                <span class="floru-service-card__link">Visit page <span aria-hidden="true">&rarr;</span></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
