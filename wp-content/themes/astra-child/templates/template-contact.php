<?php
/**
 * Template Name: Floru — Contact
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
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'Contact' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Get in Touch' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'We welcome your enquiry and look forward to discussing how we can support your objectives.' ) ); ?></p>
    </div>
</section>

<!-- ========== CONTACT GRID ========== -->
<section class="floru-section">
    <div class="floru-container">
        <div class="floru-contact-grid">

            <!-- Contact Information -->
            <div>
                <h2 class="floru-mb-8"><?php echo esc_html( $m( '_floru_contact_heading', 'Contact Information' ) ); ?></h2>
                <p class="floru-text-muted floru-mb-32"><?php echo esc_html( $m( '_floru_contact_description', 'Reach out directly or use the form to start a conversation. We typically respond within one business day.' ) ); ?></p>

                <?php $email = $m( '_floru_contact_email', 'info@floru.nl' ); if ( $email ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'mail' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h4>Email</h4>
                        <p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                $phone     = $m( '_floru_contact_phone', '+31 (0) 00 000 0000' );
                $phone_raw = $m( '_floru_contact_phone_raw', '+31000000000' );
                if ( $phone ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'phone' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h4>Phone</h4>
                        <p><a href="tel:<?php echo esc_attr( $phone_raw ); ?>"><?php echo esc_html( $phone ); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php $office = $m( '_floru_contact_office', 'The Netherlands' ); if ( $office ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'map-pin' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h4>Office</h4>
                        <p><?php echo esc_html( $office ); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Contact Form Area -->
            <div>
                <div class="floru-card floru-card--form">
                    <h3 class="floru-mb-24">Send Us a Message</h3>
                    <?php
                    $form_shortcode = $m( '_floru_contact_form_shortcode' );
                    if ( $form_shortcode ) :
                        echo do_shortcode( $form_shortcode );
                    else :
                        if ( current_user_can( 'manage_options' ) ) : ?>
                            <div class="floru-admin-notice">
                                <p><strong>Admin:</strong> No contact form configured. Edit this page and enter a form plugin shortcode (e.g. WPForms, Contact Form 7) in the &ldquo;Form Shortcode&rdquo; field.</p>
                            </div>
                        <?php else :
                            $fallback_email = $m( '_floru_contact_email', 'info@floru.nl' );
                            if ( $fallback_email ) : ?>
                                <p>Please contact us directly at <a href="mailto:<?php echo esc_attr( $fallback_email ); ?>"><?php echo esc_html( $fallback_email ); ?></a>. We typically respond within one business day.</p>
                            <?php endif;
                        endif;
                    endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php get_footer(); ?>
