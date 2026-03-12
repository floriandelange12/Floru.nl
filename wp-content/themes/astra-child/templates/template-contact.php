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

// --- Contact form handling ---
$form_submitted = false;
$form_error     = '';
$form_values    = array( 'name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => '' );

if ( isset( $_POST['floru_contact_submit'] ) && wp_verify_nonce( $_POST['floru_contact_nonce'] ?? '', 'floru_contact_form' ) ) {
    $form_values['name']    = sanitize_text_field( $_POST['floru_contact_name'] ?? '' );
    $form_values['email']   = sanitize_email( $_POST['floru_contact_email'] ?? '' );
    $form_values['phone']   = sanitize_text_field( $_POST['floru_contact_phone'] ?? '' );
    $form_values['subject'] = sanitize_text_field( $_POST['floru_contact_subject'] ?? '' );
    $form_values['message'] = sanitize_textarea_field( $_POST['floru_contact_message'] ?? '' );

    // Honeypot check
    if ( ! empty( $_POST['floru_contact_website'] ?? '' ) ) {
        $form_submitted = true; // Silently succeed for bots
    } elseif ( empty( $form_values['name'] ) || empty( $form_values['email'] ) || empty( $form_values['message'] ) ) {
        $form_error = 'Please fill in all required fields.';
    } elseif ( ! is_email( $form_values['email'] ) ) {
        $form_error = 'Please enter a valid email address.';
    } else {
        $to      = $m( '_floru_contact_email', 'r.pruijss@floru.nl' );
        $subject = ! empty( $form_values['subject'] )
            ? '[Floru Contact] ' . $form_values['subject']
            : '[Floru Contact] Message from ' . $form_values['name'];
        $body  = "Name: " . $form_values['name'] . "\n";
        $body .= "Email: " . $form_values['email'] . "\n";
        if ( $form_values['phone'] ) {
            $body .= "Phone: " . $form_values['phone'] . "\n";
        }
        $body .= "\nMessage:\n" . $form_values['message'];
        $headers = array(
            'From: ' . $form_values['name'] . ' <' . $to . '>',
            'Reply-To: ' . $form_values['email'],
        );

        $sent = wp_mail( $to, $subject, $body, $headers );
        if ( $sent ) {
            $form_submitted = true;
            $form_values = array( 'name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => '' );
        } else {
            $form_error = 'Something went wrong. Please try again or contact us directly via email.';
        }
    }
}
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
            <div class="floru-contact-info-col">
                <h2 class="floru-mb-8"><?php echo esc_html( $m( '_floru_contact_heading', 'Contact Information' ) ); ?></h2>
                <p class="floru-text-muted floru-mb-32"><?php echo esc_html( $m( '_floru_contact_description', 'Reach out directly or use the form to start a conversation. We typically respond within one business day.' ) ); ?></p>

                <?php $email = $m( '_floru_contact_email', 'r.pruijss@floru.nl' ); if ( $email ) : ?>
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
                $phone     = $m( '_floru_contact_phone', '+31 6 42 58 75 15' );
                $phone_raw = $m( '_floru_contact_phone_raw', '+31642587515' );
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

                <?php
                $address = $m( '_floru_contact_address', 'De klerkplan 10' );
                $city    = $m( '_floru_contact_city', '2728 EH Zoetermeer' );
                if ( $address ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'map-pin' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h4>Office</h4>
                        <p><?php echo esc_html( $address ); ?><br><?php echo esc_html( $city ); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php endif; ?>
            </div>

            <!-- Contact Form Area -->
            <div class="floru-contact-form-col">
                <div class="floru-card floru-card--form">
                    <?php if ( $form_submitted ) : ?>
                        <div class="floru-form-success">
                            <div class="floru-form-success__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <h3>Thank you!</h3>
                            <p>Your message has been sent successfully. We will get back to you within one business day.</p>
                        </div>
                    <?php else : ?>
                        <h3 class="floru-mb-24">Send Us a Message</h3>

                        <?php if ( $form_error ) : ?>
                            <div class="floru-form-error">
                                <p><?php echo esc_html( $form_error ); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php
                        $form_shortcode = $m( '_floru_contact_form_shortcode' );
                        if ( $form_shortcode ) :
                            echo do_shortcode( $form_shortcode );
                        else : ?>
                        <form method="post" class="floru-form" novalidate>
                            <?php wp_nonce_field( 'floru_contact_form', 'floru_contact_nonce' ); ?>

                            <!-- Honeypot -->
                            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                                <input type="text" name="floru_contact_website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="floru-form__row">
                                <div class="floru-form__group">
                                    <label for="floru_contact_name">Full Name <span class="floru-form__required">*</span></label>
                                    <input type="text" id="floru_contact_name" name="floru_contact_name" value="<?php echo esc_attr( $form_values['name'] ); ?>" required placeholder="Your name">
                                </div>
                                <div class="floru-form__group">
                                    <label for="floru_contact_email">Email Address <span class="floru-form__required">*</span></label>
                                    <input type="email" id="floru_contact_email" name="floru_contact_email" value="<?php echo esc_attr( $form_values['email'] ); ?>" required placeholder="your@email.com">
                                </div>
                            </div>

                            <div class="floru-form__row">
                                <div class="floru-form__group">
                                    <label for="floru_contact_phone">Phone Number</label>
                                    <input type="tel" id="floru_contact_phone" name="floru_contact_phone" value="<?php echo esc_attr( $form_values['phone'] ); ?>" placeholder="+31 6 1234 5678">
                                </div>
                                <div class="floru-form__group">
                                    <label for="floru_contact_subject">Subject</label>
                                    <input type="text" id="floru_contact_subject" name="floru_contact_subject" value="<?php echo esc_attr( $form_values['subject'] ); ?>" placeholder="How can we help?">
                                </div>
                            </div>

                            <div class="floru-form__group">
                                <label for="floru_contact_message">Message <span class="floru-form__required">*</span></label>
                                <textarea id="floru_contact_message" name="floru_contact_message" rows="6" required placeholder="Tell us about your project or question..."><?php echo esc_textarea( $form_values['message'] ); ?></textarea>
                            </div>

                            <div class="floru-form__footer">
                                <button type="submit" name="floru_contact_submit" class="floru-btn floru-btn--primary floru-btn--lg">
                                    Send Message
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                </button>
                                <p class="floru-form__privacy">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    Your information is secure and will not be shared with third parties.
                                </p>
                            </div>
                        </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ========== CTA ========== -->
<section class="floru-cta">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2>Prefer a Direct Conversation?</h2>
                <p>Our team is available by phone during business hours. We speak Dutch, English, and German.</p>
            </div>
            <div class="floru-cta__actions">
                <?php $cta_phone = $m( '_floru_contact_phone_raw', '+31642587515' ); ?>
                <a href="tel:<?php echo esc_attr( $cta_phone ); ?>" class="floru-btn floru-btn--primary floru-btn--lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Call Us Now
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
