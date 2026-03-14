<?php
/**
 * Footer template part for Floru child theme.
 * Content is editable via Floru Settings in WordPress admin.
 *
 * @package Astra-Child-Floru
 */

$footer_desc    = get_option( 'floru_footer_description', 'Strategic consultancy in defence and security. We help international companies navigate complex government markets, build relationships, and win tenders.' );
$footer_email   = get_option( 'floru_footer_email', 'info@floru.nl' );
$footer_tagline = get_option( 'floru_footer_tagline', 'Defence & Security Consultancy' );
?>
<footer class="floru-footer">
    <div class="floru-container">
        <div class="floru-footer__grid">
            <div class="floru-footer__brand">
                <?php
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                if ( $custom_logo_id ) :
                    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="floru-footer__logo--light">
                <?php else : ?>
                    <span class="floru-footer__brand-name"><?php bloginfo( 'name' ); ?></span>
                <?php endif; ?>
                <?php if ( $footer_desc ) : ?>
                    <p><?php echo esc_html( $footer_desc ); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <h4>Company</h4>
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'floru-footer',
                    'container'      => false,
                    'fallback_cb'    => function() {
                        echo '<ul>';
                        echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About Us</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/services/' ) ) . '">Services</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/team/' ) ) . '">Team</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/clients/' ) ) . '">Clients</a></li>';
                        echo '</ul>';
                    },
                ) );
                ?>
            </div>
            <div>
                <h4>Services</h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Business Development</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Stakeholder Engagement</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Tender Support</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <?php if ( $footer_email ) : ?>
                        <li><a href="mailto:<?php echo esc_attr( $footer_email ); ?>"><?php echo esc_html( $footer_email ); ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact Page</a></li>
                </ul>
            </div>
        </div>
        <div class="floru-footer__bottom">
            <span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Floru Consultancy. All rights reserved.</span>
            <?php if ( $footer_tagline ) : ?>
                <span><?php echo esc_html( $footer_tagline ); ?></span>
            <?php endif; ?>
        </div>
    </div>
</footer>
