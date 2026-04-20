<?php
/**
 * 404 error page template.
 *
 * @package Astra-Child-Floru
 */

get_header();

$team_url = function_exists( 'floru_get_team_url' ) ? floru_get_team_url() : home_url( '/our-team/' );
?>

<section class="floru-page-header floru-page-header--404">
    <div class="floru-container">
        <span class="floru-section-label"><?php echo esc_html( floru_t( 'Page Not Found' ) ); ?></span>
        <h1><?php echo esc_html( floru_t( 'We could not find that page.' ) ); ?></h1>
        <p><?php echo esc_html( floru_t( 'The address may be outdated, or the page may have moved within the Floru site.' ) ); ?></p>
    </div>
</section>

<section class="floru-section floru-section--404">
    <div class="floru-container">
        <div class="floru-404-shell">
            <div class="floru-404-layout">
                <div class="floru-404-copy">
                    <span class="floru-404__code"><?php echo esc_html( floru_t( 'Error 404' ) ); ?></span>
                    <h2 class="floru-404__heading"><?php echo esc_html( floru_t( 'Recovery routes' ) ); ?></h2>
                    <p class="floru-intro-text floru-404__intro">
                        <?php echo esc_html( floru_t( 'Return to the homepage, start a discreet conversation, or continue through one of Floru\'s core routes below.' ) ); ?>
                    </p>
                    <div class="floru-404__actions">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="floru-btn floru-btn--primary"><?php echo esc_html( floru_t( 'Back Home' ) ); ?></a>
                        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="floru-btn floru-btn--outline"><?php echo esc_html( floru_t( 'Contact Floru' ) ); ?></a>
                    </div>
                </div>

                <div class="floru-404-routes">
                    <span class="floru-section-label floru-404-routes__label"><?php echo esc_html( floru_t( 'Core routes' ) ); ?></span>
                    <div class="floru-services-grid floru-404__cards">
                        <?php
                        $pages = array(
                            array( 'Services', home_url( '/services/' ), 'trending-up' ),
                            array( 'Team',     $team_url,                'users' ),
                            array( 'Clients',  home_url( '/clients/' ),  'award' ),
                        );
                        foreach ( $pages as $page ) : ?>
                        <a href="<?php echo esc_url( $page[1] ); ?>" class="floru-service-card floru-service-card--compact">
                            <div class="floru-service-card__icon">
                                <?php echo floru_icon( $page[2] ); ?>
                            </div>
                            <h3><?php echo esc_html( floru_t( $page[0] ) ); ?></h3>
                            <span class="floru-service-card__link"><?php echo esc_html( floru_t( 'Visit page' ) ); ?> <span aria-hidden="true">&rarr;</span></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
