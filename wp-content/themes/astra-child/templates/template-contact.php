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
$contact_details = function_exists( 'floru_get_contact_details' ) ? floru_get_contact_details( $pid ) : array(
    'email'     => 'r.pruijss@floru.nl',
    'phone'     => '+31 6 42 58 75 15',
    'phone_raw' => '+31642587515',
    'address'   => 'De klerkplan 10',
    'city'      => '2728 EH Zoetermeer',
);

// --- Contact form handling ---
$form_submitted = false;
$form_error     = '';
$form_errors    = array();
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
    } else {
        if ( empty( $form_values['name'] ) ) {
            $form_errors['name'] = floru_t( 'Please enter your full name.' );
        }

        if ( empty( $form_values['email'] ) ) {
            $form_errors['email'] = floru_t( 'Please enter your email address.' );
        } elseif ( ! is_email( $form_values['email'] ) ) {
            $form_errors['email'] = floru_t( 'Please enter a valid email address.' );
        }

        if ( empty( $form_values['message'] ) ) {
            $form_errors['message'] = floru_t( 'Please enter a short message.' );
        }

        if ( $form_errors ) {
            $form_error = floru_t( 'Please review the highlighted fields and try again.' );
        }

        if ( ! $form_errors ) {
        $to       = ! empty( $contact_details['email'] ) ? $contact_details['email'] : get_option( 'admin_email' );
        $from     = get_option( 'admin_email' );
        $blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
        $subject = ! empty( $form_values['subject'] )
            ? '[Floru Contact] ' . $form_values['subject']
            : floru_t( '[Floru Contact] Message from ' ) . $form_values['name'];
        $body  = floru_t( 'Name: ' ) . $form_values['name'] . "\n";
        $body .= floru_t( 'Email: ' ) . $form_values['email'] . "\n";
        if ( $form_values['phone'] ) {
            $body .= floru_t( 'Phone: ' ) . $form_values['phone'] . "\n";
        }
        $body .= "\n" . floru_t( 'Message:' ) . "\n" . $form_values['message'];
        $headers = array(
            'From: ' . $blogname . ' <' . $from . '>',
            'Reply-To: ' . $form_values['name'] . ' <' . $form_values['email'] . '>',
        );

        $sent = wp_mail( $to, $subject, $body, $headers );
        if ( $sent ) {
            $form_submitted = true;
            $form_values = array( 'name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => '' );
        } else {
            $form_error = floru_t( 'Something went wrong. Please try again or contact us directly via email.' );
        }
        }
    }
}

$floru_contact_describedby = static function( $field, $required = false ) use ( $form_errors ) {
    $describedby = array();

    if ( $required ) {
        $describedby[] = 'floru_contact_required_note';
    }

    if ( isset( $form_errors[ $field ] ) ) {
        $describedby[] = 'floru_contact_' . $field . '_error';
    }

    return implode( ' ', $describedby );
};
?>

<!-- ========== PAGE HEADER ========== -->
<section class="floru-page-header floru-page-header--contact" data-animate="fade-in">
    <div class="floru-container">
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'Contact' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Get in Touch' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'We welcome your enquiry and look forward to discussing how we can support your objectives.' ) ); ?></p>
    </div>
</section>

<!-- ========== CONTACT GRID ========== -->
<section class="floru-section floru-section--contact-main" data-animate>
    <div class="floru-container">
        <div class="floru-contact-grid">

            <!-- Contact Information -->
            <div class="floru-contact-info-col">
                <h2 class="floru-mb-8"><?php echo esc_html( $m( '_floru_contact_heading', 'Contact Information' ) ); ?></h2>
                <p class="floru-text-muted floru-mb-32"><?php echo esc_html( $m( '_floru_contact_description', 'Reach out directly or use the form to start a conversation. We typically respond within one business day.' ) ); ?></p>
                <ul class="floru-contact-signals" aria-label="<?php echo esc_attr__( 'Contact signals', 'astra-child-floru' ); ?>">
                    <li><?php echo esc_html( floru_t( 'Response within one business day' ) ); ?></li>
                    <li><?php echo esc_html( floru_t( 'Dutch, English, and German' ) ); ?></li>
                    <li><?php echo esc_html( floru_t( 'Discreet by default' ) ); ?></li>
                </ul>

                <?php $email = $contact_details['email']; if ( $email ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'mail' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h3><?php echo esc_html( floru_t( 'Email' ) ); ?></h3>
                        <p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                $phone     = $contact_details['phone'];
                $phone_raw = $contact_details['phone_raw'];
                if ( $phone ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'phone' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h3><?php echo esc_html( floru_t( 'Phone' ) ); ?></h3>
                        <p><a href="tel:<?php echo esc_attr( $phone_raw ); ?>"><?php echo esc_html( $phone ); ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                $address = $contact_details['address'];
                $city    = $contact_details['city'];
                if ( $address ) : ?>
                <div class="floru-contact-info__item">
                    <div class="floru-contact-info__icon">
                        <?php echo floru_icon( 'map-pin' ); ?>
                    </div>
                    <div class="floru-contact-info__text">
                        <h3><?php echo esc_html( floru_t( 'Office' ) ); ?></h3>
                        <p><?php echo esc_html( $address ); ?><br><?php echo esc_html( $city ); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="floru-contact-note">
                    <h3><?php echo esc_html( floru_t( 'Direct and discreet' ) ); ?></h3>
                    <p><?php echo esc_html( floru_t( 'We keep our first response concise, senior, and focused on whether Floru is the right fit for your brief.' ) ); ?></p>
                </div>

                <div class="floru-contact-protocol">
                    <span class="floru-contact-protocol__eyebrow"><?php echo esc_html( floru_t( 'First conversation' ) ); ?></span>
                    <ol class="floru-contact-protocol__steps">
                        <li><strong><?php echo esc_html( floru_t( 'Initial fit.' ) ); ?></strong> <?php echo esc_html( floru_t( 'We assess the scope, timing, and decision context behind your brief.' ) ); ?></li>
                        <li><strong><?php echo esc_html( floru_t( 'Brief review.' ) ); ?></strong> <?php echo esc_html( floru_t( 'We identify where Floru adds value and where a lighter route is more appropriate.' ) ); ?></li>
                        <li><strong><?php echo esc_html( floru_t( 'Next step.' ) ); ?></strong> <?php echo esc_html( floru_t( 'We respond with a pragmatic direction, typically within one business day.' ) ); ?></li>
                    </ol>
                </div>
            </div>

            <!-- Contact Form Area -->
            <div class="floru-contact-form-col">
                <div class="floru-card floru-card--form floru-card--contact-form">
                    <?php if ( $form_submitted ) : ?>
                        <div class="floru-form-success" role="status" aria-live="polite">
                            <div class="floru-form-success__icon">
                                <?php echo floru_icon( 'check-circle' ); ?>
                            </div>
                            <h3><?php echo esc_html( floru_t( 'Thank you!' ) ); ?></h3>
                            <p><?php echo esc_html( floru_t( 'Your message has been sent successfully. We will get back to you within one business day.' ) ); ?></p>
                        </div>
                    <?php else : ?>
                        <div class="floru-form-panel__intro">
                            <span class="floru-section-label"><?php echo esc_html( floru_t( 'Start the conversation' ) ); ?></span>
                            <h3><?php echo esc_html( floru_t( 'Send Us a Message' ) ); ?></h3>
                            <p><?php echo esc_html( floru_t( 'Share a short outline of your objective, tender, or market-entry question and we will route it to the right person.' ) ); ?></p>
                        </div>

                        <?php if ( $form_error ) : ?>
                            <div class="floru-form-error" id="floru_contact_form_error" role="alert" aria-live="assertive">
                                <p><?php echo esc_html( $form_error ); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php
                        $form_shortcode = $m( '_floru_contact_form_shortcode' );
                        if ( $form_shortcode ) :
                            echo do_shortcode( $form_shortcode );
                        else : ?>
                        <form method="post" class="floru-form" novalidate aria-describedby="<?php echo esc_attr( trim( $form_error ? 'floru_contact_form_error floru_contact_required_note' : 'floru_contact_required_note' ) ); ?>">
                            <?php wp_nonce_field( 'floru_contact_form', 'floru_contact_nonce' ); ?>

                            <!-- Honeypot -->
                            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                                <input type="text" name="floru_contact_website" tabindex="-1" autocomplete="off">
                            </div>

                            <p class="floru-form__hint" id="floru_contact_required_note"><?php echo esc_html( floru_t( 'Fields marked with * are required.' ) ); ?></p>

                            <div class="floru-form__row">
                                <div class="floru-form__group">
                                    <label for="floru_contact_name"><?php echo esc_html( floru_t( 'Full Name' ) ); ?> <span class="floru-form__required">*</span></label>
                                    <input type="text" id="floru_contact_name" name="floru_contact_name" value="<?php echo esc_attr( $form_values['name'] ); ?>" required aria-required="true" aria-invalid="<?php echo isset( $form_errors['name'] ) ? 'true' : 'false'; ?>" aria-describedby="<?php echo esc_attr( $floru_contact_describedby( 'name', true ) ); ?>" autocomplete="name" placeholder="<?php echo esc_attr( floru_t( 'Your name' ) ); ?>">
                                    <?php if ( isset( $form_errors['name'] ) ) : ?>
                                        <p class="floru-form__error-text" id="floru_contact_name_error"><?php echo esc_html( $form_errors['name'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="floru-form__group">
                                    <label for="floru_contact_email"><?php echo esc_html( floru_t( 'Email Address' ) ); ?> <span class="floru-form__required">*</span></label>
                                    <input type="email" id="floru_contact_email" name="floru_contact_email" value="<?php echo esc_attr( $form_values['email'] ); ?>" required aria-required="true" aria-invalid="<?php echo isset( $form_errors['email'] ) ? 'true' : 'false'; ?>" aria-describedby="<?php echo esc_attr( $floru_contact_describedby( 'email', true ) ); ?>" autocomplete="email" inputmode="email" placeholder="your@email.com">
                                    <?php if ( isset( $form_errors['email'] ) ) : ?>
                                        <p class="floru-form__error-text" id="floru_contact_email_error"><?php echo esc_html( $form_errors['email'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="floru-form__row">
                                <div class="floru-form__group">
                                    <label for="floru_contact_phone"><?php echo esc_html( floru_t( 'Phone Number' ) ); ?></label>
                                    <input type="tel" id="floru_contact_phone" name="floru_contact_phone" value="<?php echo esc_attr( $form_values['phone'] ); ?>" aria-invalid="false" autocomplete="tel" inputmode="tel" placeholder="+31 6 1234 5678">
                                </div>
                                <div class="floru-form__group">
                                    <label for="floru_contact_subject"><?php echo esc_html( floru_t( 'Subject' ) ); ?></label>
                                    <input type="text" id="floru_contact_subject" name="floru_contact_subject" value="<?php echo esc_attr( $form_values['subject'] ); ?>" aria-invalid="false" autocomplete="off" placeholder="<?php echo esc_attr( floru_t( 'How can we help?' ) ); ?>">
                                </div>
                            </div>

                            <div class="floru-form__group">
                                <label for="floru_contact_message"><?php echo esc_html( floru_t( 'Message' ) ); ?> <span class="floru-form__required">*</span></label>
                                <textarea id="floru_contact_message" name="floru_contact_message" rows="6" required aria-required="true" aria-invalid="<?php echo isset( $form_errors['message'] ) ? 'true' : 'false'; ?>" aria-describedby="<?php echo esc_attr( $floru_contact_describedby( 'message', true ) ); ?>" placeholder="<?php echo esc_attr( floru_t( 'Tell us about your project or question...' ) ); ?>"><?php echo esc_textarea( $form_values['message'] ); ?></textarea>
                                <?php if ( isset( $form_errors['message'] ) ) : ?>
                                    <p class="floru-form__error-text" id="floru_contact_message_error"><?php echo esc_html( $form_errors['message'] ); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="floru-form__footer">
                                <button type="submit" name="floru_contact_submit" class="floru-btn floru-btn--primary floru-btn--lg">
                                    <?php echo esc_html( floru_t( 'Send Message' ) ); ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                </button>
                                <p class="floru-form__privacy" id="floru_contact_privacy_note">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    <?php echo esc_html( floru_t( 'Your information is secure and will not be shared with third parties.' ) ); ?>
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
<section class="floru-cta floru-cta--contact" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( floru_t( 'Prefer a Direct Conversation?' ) ); ?></h2>
                <p><?php echo esc_html( floru_t( 'Our team is available by phone during business hours. We speak Dutch, English, and German.' ) ); ?></p>
            </div>
            <div class="floru-cta__actions">
                <?php $cta_phone = $contact_details['phone_raw']; ?>
                <a href="tel:<?php echo esc_attr( $cta_phone ); ?>" class="floru-btn floru-btn--primary floru-btn--lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <?php echo esc_html( floru_t( 'Call Us Now' ) ); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
