<?php
/**
 * Template Name: Floru — About
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
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'About Floru' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Our Modus Operandi' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'We combine strategic advisory with hands-on support to help defence and security companies achieve measurable results.' ) ); ?></p>
    </div>
</section>

<!-- ========== ABOUT INTRO ========== -->
<section class="floru-section">
    <div class="floru-container">
        <div class="floru-about-grid">
            <div>
                <span class="floru-section-label"><?php echo esc_html( $m( '_floru_about_intro_label', 'Our Story' ) ); ?></span>
                <h2><?php echo esc_html( $m( '_floru_about_intro_heading', 'Founded on Experience, Focused on Outcomes' ) ); ?></h2>
                <?php
                $content = get_the_content();
                if ( $content && trim( strip_tags( $content ) ) ) :
                    echo wp_kses_post( apply_filters( 'the_content', $content ) );
                else : ?>
                    <p>Floru was founded to address a clear need in the European defence market: international companies entering or expanding in the Netherlands and broader European markets need a trusted local partner who understands both the business environment and the government landscape.</p>
                    <p>Our team brings together decades of experience at the intersection of defence, government affairs, and international business development. We have held senior positions within government, defence organisations, and industry — giving us a unique perspective that benefits our clients.</p>
                    <p>We work closely with our clients as an extension of their team. We do not believe in generic, arms-length advisory. Our approach is hands-on, results-oriented, and built on trust.</p>
                <?php endif; ?>
            </div>
            <div>
                <div class="floru-intro__image-wrapper">
                    <?php if ( has_post_thumbnail( $pid ) ) : ?>
                        <?php echo get_the_post_thumbnail( $pid, 'large', array( 'class' => 'floru-img-block', 'loading' => 'lazy' ) ); ?>
                    <?php else : ?>
                        <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/illustration-experience.svg" alt="<?php echo esc_attr( $m( '_floru_about_intro_heading', 'Floru consultancy approach' ) ); ?>" loading="lazy" class="floru-img-block">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== APPROACH STEPS ========== -->
<section class="floru-section floru-section--gray">
    <div class="floru-container floru-container--narrow">
        <div class="floru-section-header">
            <span class="floru-section-label"><?php echo esc_html( $m( '_floru_approach_label', 'How We Work' ) ); ?></span>
            <h2><?php echo esc_html( $m( '_floru_approach_heading', 'A Structured Approach to Every Engagement' ) ); ?></h2>
            <p><?php echo esc_html( $m( '_floru_approach_description', 'Our methodology is designed to maximise clarity, efficiency, and results at every stage.' ) ); ?></p>
        </div>

        <ol class="floru-approach-steps">
            <?php
            $step_defaults = array(
                1 => array( 'Understand & Assess', 'We start by thoroughly understanding your company, your product or capability, and your strategic objectives. We assess the market landscape, procurement environment, and competitive dynamics.' ),
                2 => array( 'Develop Strategy', 'We formulate a clear, actionable strategy for market entry, growth, or tender response — including stakeholder mapping, positioning, and timeline planning.' ),
                3 => array( 'Engage & Position', 'We facilitate introductions to key decision-makers, support your messaging and positioning, and help establish your presence in relevant networks and fora.' ),
                4 => array( 'Execute & Support', 'Whether it is a tender submission, a partnership development, or a long-term market presence — we work alongside you through execution, providing practical and strategic support.' ),
                5 => array( 'Review & Adapt', 'After every engagement phase, we review progress, refine the approach, and ensure continued alignment with your business objectives.' ),
            );
            for ( $i = 1; $i <= 5; $i++ ) :
                $title = $m( '_floru_step' . $i . '_title', $step_defaults[ $i ][0] );
                $desc  = $m( '_floru_step' . $i . '_desc', $step_defaults[ $i ][1] );
                if ( $title ) : ?>
            <li>
                <h4><?php echo esc_html( $title ); ?></h4>
                <p><?php echo esc_html( $desc ); ?></p>
            </li>
            <?php endif; endfor; ?>
        </ol>
    </div>
</section>

<!-- ========== VALUES ========== -->
<section class="floru-section">
    <div class="floru-container">
        <div class="floru-section-header">
            <span class="floru-section-label"><?php echo esc_html( $m( '_floru_values_label', 'What Guides Us' ) ); ?></span>
            <h2><?php echo esc_html( $m( '_floru_values_heading', 'Our Core Values' ) ); ?></h2>
        </div>
        <div class="floru-trust-grid">
            <?php
            $value_defaults = array(
                1 => array( 'Integrity', 'We maintain the highest ethical standards. Our clients trust us because we are straightforward, discreet, and reliable.', 'shield' ),
                2 => array( 'Results-Oriented', 'We measure our success by our clients\' outcomes. Every action we take is directed towards clear, tangible results.', 'target' ),
                3 => array( 'Partnership', 'We work as part of your team — not as outsiders. Our engagements are collaborative, trust-based, and long-term.', 'users' ),
                4 => array( 'Expertise', 'Our deep sector knowledge, network, and market understanding set us apart in the defence and security domain.', 'globe' ),
            );
            for ( $i = 1; $i <= 4; $i++ ) :
                $title = $m( '_floru_value' . $i . '_title', $value_defaults[ $i ][0] );
                $desc  = $m( '_floru_value' . $i . '_desc', $value_defaults[ $i ][1] );
                $icon  = $m( '_floru_value' . $i . '_icon', $value_defaults[ $i ][2] );
                if ( $title ) :
            ?>
            <div class="floru-trust-item">
                <div class="floru-trust-item__icon">
                    <?php echo floru_icon( $icon ); ?>
                </div>
                <div class="floru-trust-item__text">
                    <h4><?php echo esc_html( $title ); ?></h4>
                    <p><?php echo esc_html( $desc ); ?></p>
                </div>
            </div>
            <?php endif; endfor; ?>
        </div>
    </div>
</section>

<!-- ========== CTA ========== -->
<section class="floru-section floru-section--navy floru-cta">
    <div class="floru-container">
        <h2><?php echo esc_html( $m( '_floru_pcta_heading', 'Interested in Working Together?' ) ); ?></h2>
        <p><?php echo esc_html( $m( '_floru_pcta_description', 'We welcome the opportunity to discuss how we can support your objectives in the European defence market.' ) ); ?></p>
        <div class="floru-cta__actions">
            <?php
            $cta_text = $m( '_floru_pcta_btn_text', 'Contact Us' );
            $cta_url  = $m( '_floru_pcta_btn_url', home_url( '/contact/' ) );
            if ( $cta_text ) : ?>
                <a href="<?php echo esc_url( $cta_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_text ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
