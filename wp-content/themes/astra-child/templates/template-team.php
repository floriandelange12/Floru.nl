<?php
/**
 * Template Name: Floru — Team
 *
 * @package Astra-Child-Floru
 */

get_header();

$pid = get_the_ID();
$m = function( $key, $default = '' ) use ( $pid ) {
    return floru_get_meta( $pid, $key, $default );
};

$team_initials = static function( $name ) {
    if ( function_exists( 'floru_get_initials' ) ) {
        return floru_get_initials( $name );
    }

    $letters = '';
    $parts   = preg_split( '/\s+/', trim( $name ) );

    foreach ( $parts as $part ) {
        if ( '' === $part ) {
            continue;
        }

        $letters .= strtoupper( substr( $part, 0, 1 ) );

        if ( strlen( $letters ) >= 2 ) {
            break;
        }
    }

    return $letters ?: 'F';
};

$team_profiles = array();
$team_members  = new WP_Query( array(
    'post_type'      => 'floru_team',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
) );

if ( $team_members->have_posts() ) {
    while ( $team_members->have_posts() ) {
        $team_members->the_post();

        $member_id = get_the_ID();
        $name      = floru_get_translated_post_title_raw( $member_id );
        $role      = floru_get_translated_post_meta( $member_id, '_floru_team_role' );
        $linkedin  = get_post_meta( $member_id, '_floru_team_profile_link', true );
        $excerpt   = has_excerpt( $member_id ) ? floru_get_translated_post_excerpt_raw( $member_id ) : '';

        if ( ! $excerpt ) {
            $raw = wp_strip_all_tags( strip_shortcodes( floru_get_translated_post_content_raw( $member_id ) ) );

            if ( $raw ) {
                $excerpt = wp_trim_words( $raw, 28, '&hellip;' );
            }
        }

        $portrait_html = has_post_thumbnail( $member_id ) ? get_the_post_thumbnail(
            $member_id,
            'floru-team-portrait',
            array(
                'loading'  => 'lazy',
                'decoding' => 'async',
                'sizes'    => '(min-width: 1280px) 320px, (min-width: 1024px) 26vw, (min-width: 640px) 42vw, 90vw',
                'alt'      => sprintf( /* translators: %s = team member name */ esc_attr__( 'Portret van %s', 'astra-child-floru' ), $name ),
            )
        ) : '';

        $team_profiles[] = array(
            'id'           => $member_id,
            'slug'         => get_post_field( 'post_name', $member_id ),
            'name'         => $name,
            'role'         => $role,
            'linkedin'     => $linkedin,
            'excerpt'      => $excerpt,
            'portrait'     => $portrait_html,
            'has_portrait' => '' !== $portrait_html,
            'initials'     => $team_initials( $name ),
        );
    }

    wp_reset_postdata();
}

if ( function_exists( 'floru_prioritize_team_collection' ) ) {
    $team_profiles = floru_prioritize_team_collection(
        $team_profiles,
        static function( $profile ) {
            return array(
                'slug' => isset( $profile['slug'] ) ? $profile['slug'] : '',
                'name' => isset( $profile['name'] ) ? $profile['name'] : '',
            );
        }
    );
}

$team_count = count( $team_profiles );
?>

<section class="floru-page-header floru-page-header--team" data-animate="fade-in">
    <div class="floru-container">
        <span class="floru-section-label floru-section-label--lined">
            <?php echo esc_html( $m( '_floru_ph_label', 'Our People' ) ); ?>
        </span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Meet the Team' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'Our strength lies in the experience, network, and commitment of our senior consultants.' ) ); ?></p>
    </div>
</section>

<section class="floru-section floru-team-intro" data-animate>
    <div class="floru-container">
        <div class="floru-team-intro__grid floru-team-intro__grid--single">
            <div class="floru-team-intro__inner">
                <span class="floru-team-intro__rule" aria-hidden="true"></span>
                <?php
                $content = floru_get_translated_post_content_raw( $pid );
                if ( $content && trim( strip_tags( $content ) ) ) : ?>
                    <div class="floru-team-intro__text">
                        <?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>
                    </div>
                <?php else : ?>
                    <p class="floru-team-intro__text"><?php echo esc_html( floru_t( 'Floru brings together a small, dedicated team of professionals with deep roots in the defence and security sector. Each of us has held positions within government, the armed forces, or the defence industry — and we draw on that combined experience to deliver results for our clients.' ) ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="floru-section floru-section--gray floru-team-grid-section" data-animate>
    <div class="floru-container">
        <?php if ( $team_count ) : ?>
            <div class="floru-team-roster__header">
                <div class="floru-team-roster__title-block">
                    <span class="floru-section-label"><?php esc_html_e( 'Senior roster', 'astra-child-floru' ); ?></span>
                    <h2><?php esc_html_e( 'Leadership, networks, and sector knowledge in one clear team view', 'astra-child-floru' ); ?></h2>
                </div>
                <p class="floru-team-roster__lead"><?php esc_html_e( 'A single, premium roster of Floru consultants with direct experience across defence, government, and industry.', 'astra-child-floru' ); ?></p>
            </div>

            <div class="floru-team-roster" data-animate-stagger>
                <?php foreach ( $team_profiles as $index => $profile ) : ?>
                    <?php
                    $profile_classes = array( 'floru-team-profile' );

                    if ( $index < 2 ) {
                        $profile_classes[] = 'floru-team-profile--featured';
                    }

                    if ( ! $profile['excerpt'] ) {
                        $profile_classes[] = 'floru-team-profile--minimal';
                    }
                    ?>
                    <article class="<?php echo esc_attr( implode( ' ', $profile_classes ) ); ?>" id="team-<?php echo esc_attr( $profile['slug'] ); ?>">
                        <figure class="floru-team-profile__media">
                            <?php if ( $profile['portrait'] ) : ?>
                                <?php echo wp_kses_post( $profile['portrait'] ); ?>
                            <?php else : ?>
                                <div class="floru-team-profile__placeholder" aria-hidden="true">
                                    <span><?php echo esc_html( $profile['initials'] ); ?></span>
                                </div>
                                <figcaption class="floru-sr-only"><?php esc_html_e( 'Portret volgt', 'astra-child-floru' ); ?></figcaption>
                            <?php endif; ?>
                        </figure>

                        <div class="floru-team-profile__body">
                            <header class="floru-team-profile__header">
                                <?php if ( $profile['role'] ) : ?>
                                    <span class="floru-team-profile__role"><?php echo esc_html( $profile['role'] ); ?></span>
                                <?php endif; ?>
                                <h3 class="floru-team-profile__name"><?php echo esc_html( $profile['name'] ); ?></h3>
                            </header>

                            <?php if ( $profile['excerpt'] ) : ?>
                                <p class="floru-team-profile__bio"><?php echo esc_html( $profile['excerpt'] ); ?></p>
                            <?php endif; ?>

                            <?php if ( $profile['linkedin'] ) : ?>
                                <footer class="floru-team-profile__footer">
                                    <a class="floru-team-profile__action"
                                       href="<?php echo esc_url( $profile['linkedin'] ); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       aria-label="<?php echo esc_attr( sprintf( /* translators: %s = team member name */ __( '%s op LinkedIn (opent in nieuw venster)', 'astra-child-floru' ), $profile['name'] ) ); ?>">
                                        <svg class="floru-team-profile__action-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                        <span class="floru-team-profile__action-label"><?php esc_html_e( 'LinkedIn profile', 'astra-child-floru' ); ?></span>
                                        <span class="floru-team-profile__action-arrow" aria-hidden="true">&rarr;</span>
                                    </a>
                                </footer>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="floru-admin-notice"><?php echo wp_kses_post( floru_t( 'No team members have been added yet. Go to <strong>Team Members</strong> in the WordPress admin to add them.' ) ); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="floru-cta floru-cta--team" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( $m( '_floru_pcta_heading', 'Work With Our Team' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_pcta_description', 'We bring a personal, senior-level approach to every engagement. Get in touch to discuss your objectives.' ) ); ?></p>
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
