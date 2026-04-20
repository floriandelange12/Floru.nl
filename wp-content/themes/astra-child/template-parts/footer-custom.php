<?php
/**
 * Footer template part for Floru child theme.
 * Content is editable via Floru Settings in WordPress admin.
 *
 * @package Astra-Child-Floru
 */

$footer_desc    = floru_get_translated_option( 'floru_footer_description', 'Strategic consultancy in defence and security. We help international companies navigate complex government markets, build relationships, and win tenders.' );
$footer_email   = get_option( 'floru_footer_email', 'info@floru.nl' );
$footer_tagline = floru_get_translated_option( 'floru_footer_tagline', 'Defence & Security Consultancy' );

$contact_page      = function_exists( 'floru_get_canonical_page_for_template' ) ? floru_get_canonical_page_for_template( 'templates/template-contact.php' ) : null;
$contact_page_id   = $contact_page ? $contact_page->ID : 0;
$contact_page_url  = $contact_page ? get_permalink( $contact_page ) : home_url( '/contact/' );
$contact_details   = function_exists( 'floru_get_contact_details' ) ? floru_get_contact_details( $contact_page_id ) : array(
    'email'     => 'r.pruijss@floru.nl',
    'phone'     => '+31 6 42 58 75 15',
    'phone_raw' => '+31642587515',
    'address'   => 'De klerkplan 10',
    'city'      => '2728 EH Zoetermeer',
);
$contact_email     = $contact_details['email'];
$contact_phone     = $contact_details['phone'];
$contact_phone_raw = $contact_details['phone_raw'];
$contact_address   = $contact_details['address'];
$contact_city      = $contact_details['city'];

if ( ! $footer_email && $contact_email ) {
    $footer_email = $contact_email;
}

$office_line = trim( implode( ', ', array_filter( array( $contact_address, $contact_city ) ) ) );
$current_language = function_exists( 'floru_get_current_language' ) ? floru_get_current_language() : 'en';
$supported_languages = function_exists( 'floru_get_supported_languages' ) ? floru_get_supported_languages() : array();
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
                <div class="floru-footer__brand-note">
                    <span class="floru-footer__eyebrow"><?php echo esc_html( floru_t( 'Trusted conversations' ) ); ?></span>
                    <p><?php echo esc_html( floru_t( 'Senior strategic guidance for discreet defence and security engagements, from market entry to stakeholder positioning.' ) ); ?></p>
                </div>
            </div>
            <div class="floru-footer__nav-group">
                <h4><?php echo esc_html( floru_t( 'Company' ) ); ?></h4>
                <?php
                $floru_team_url = function_exists( 'floru_get_team_url' ) ? floru_get_team_url() : home_url( '/our-team/' );
                wp_nav_menu( array(
                    'theme_location' => 'floru-footer',
                    'container'      => false,
                    'fallback_cb'    => function() use ( $floru_team_url ) {
                        echo '<ul>';
                        echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">' . esc_html( floru_t( 'About Us' ) ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/services/' ) ) . '">' . esc_html( floru_t( 'Services' ) ) . '</a></li>';
                        echo '<li><a href="' . esc_url( $floru_team_url ) . '">' . esc_html( floru_t( 'Team' ) ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/clients/' ) ) . '">' . esc_html( floru_t( 'Clients' ) ) . '</a></li>';
                        echo '</ul>';
                    },
                ) );
                ?>
            </div>
            <div class="floru-footer__nav-group">
                <h4><?php echo esc_html( floru_t( 'Services' ) ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php echo esc_html( floru_t( 'Business Development' ) ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php echo esc_html( floru_t( 'Stakeholder Engagement' ) ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php echo esc_html( floru_t( 'Tender Support' ) ); ?></a></li>
                </ul>
            </div>
            <div class="floru-footer__contact">
                <h4><?php echo esc_html( floru_t( 'Contact' ) ); ?></h4>
                <ul class="floru-footer__contact-list">
                    <?php if ( $footer_email ) : ?>
                        <li>
                            <span class="floru-footer__contact-icon"><?php echo floru_icon( 'mail' ); ?></span>
                            <div>
                                <span class="floru-footer__contact-label"><?php echo esc_html( floru_t( 'Email' ) ); ?></span>
                                <a href="mailto:<?php echo esc_attr( $footer_email ); ?>"><?php echo esc_html( $footer_email ); ?></a>
                            </div>
                        </li>
                    <?php endif; ?>
                    <?php if ( $contact_phone ) : ?>
                        <li>
                            <span class="floru-footer__contact-icon"><?php echo floru_icon( 'phone' ); ?></span>
                            <div>
                                <span class="floru-footer__contact-label"><?php echo esc_html( floru_t( 'Phone' ) ); ?></span>
                                <a href="tel:<?php echo esc_attr( $contact_phone_raw ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
                            </div>
                        </li>
                    <?php endif; ?>
                    <?php if ( $office_line ) : ?>
                        <li>
                            <span class="floru-footer__contact-icon"><?php echo floru_icon( 'map-pin' ); ?></span>
                            <div>
                                <span class="floru-footer__contact-label"><?php echo esc_html( floru_t( 'Office' ) ); ?></span>
                                <a href="<?php echo esc_url( $contact_page_url ); ?>"><?php echo esc_html( $office_line ); ?></a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
                <p class="floru-footer__contact-note"><?php echo esc_html( floru_t( 'Response within one business day. Dutch and international engagements handled discreetly.' ) ); ?></p>
                <a href="<?php echo esc_url( $contact_page_url ); ?>" class="floru-btn floru-btn--outline-white floru-btn--sm floru-footer__contact-cta"><?php echo esc_html( floru_t( 'Start a conversation' ) ); ?></a>
            </div>
        </div>
        <div class="floru-footer__bottom">
            <div class="floru-footer__bottom-meta">
                <span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Floru Consultancy. <?php echo esc_html( floru_t( 'All rights reserved.' ) ); ?></span>
                <?php if ( $footer_tagline ) : ?>
                    <span><?php echo esc_html( $footer_tagline ); ?></span>
                <?php endif; ?>
            </div>
            <div class="floru-footer__language" aria-label="<?php echo esc_attr( floru_t( 'Language' ) ); ?>">
                <span class="floru-footer__language-label"><?php echo esc_html( floru_t( 'Language' ) ); ?></span>
                <div class="floru-footer__language-options">
                    <?php foreach ( $supported_languages as $language_code => $language_details ) : ?>
                        <a href="<?php echo esc_url( floru_get_language_switch_url( $language_code ) ); ?>"
                           class="floru-footer__language-link<?php echo $current_language === $language_code ? ' is-active' : ''; ?>"
                           hreflang="<?php echo esc_attr( $language_details['hreflang'] ); ?>"
                           lang="<?php echo esc_attr( $language_details['html_lang'] ); ?>"
                           <?php if ( $current_language === $language_code ) : ?>aria-current="page"<?php endif; ?>>
                            <?php echo esc_html( $language_details['label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</footer>
