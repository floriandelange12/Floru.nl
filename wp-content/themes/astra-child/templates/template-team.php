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
?>

<section class="floru-page-header" data-animate="fade-in">
    <div class="floru-container">
        <span class="floru-section-label"><?php echo esc_html( $m( '_floru_ph_label', 'Our People' ) ); ?></span>
        <h1><?php echo esc_html( $m( '_floru_ph_heading', 'Meet the Team' ) ); ?></h1>
        <p><?php echo esc_html( $m( '_floru_ph_description', 'Our strength lies in the experience, network, and commitment of our senior consultants.' ) ); ?></p>
    </div>
</section>

<section class="floru-section" data-animate>
    <div class="floru-container floru-container--narrow floru-text-center">
        <?php
        $content = get_the_content();
        if ( $content && trim( strip_tags( $content ) ) ) : ?>
            <div class="floru-intro-text">
                <?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>
            </div>
        <?php else : ?>
            <p class="floru-intro-text">Floru brings together a small, dedicated team of professionals with deep roots in the defence and security sector. Each of us has held positions within government, the armed forces, or the defence industry — and we draw on that combined experience to deliver results for our clients.</p>
        <?php endif; ?>
    </div>
</section>

<section class="floru-section floru-section--gray floru-section--flush-top" data-animate>
    <div class="floru-container">
        <div class="floru-team-grid" data-animate-stagger>
            <?php
            $team_members = new WP_Query( array(
                'post_type'      => 'floru_team',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ) );
            if ( $team_members->have_posts() ) :
                while ( $team_members->have_posts() ) : $team_members->the_post();
                    $role = get_post_meta( get_the_ID(), '_floru_team_role', true );
            ?>
            <div class="floru-team-card">
                <div class="floru-team-card__editorial-image">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large' ); ?>
                    <?php else : ?>
                        <div class="floru-team-card__empty-state"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg></div>
                    <?php endif; ?>
                </div>
                <div class="floru-team-card__body">
                    <h3><?php the_title(); ?></h3>
                    <?php if ( $role ) : ?>
                        <span class="floru-team-card__role"><?php echo esc_html( $role ); ?></span>
                    <?php endif; ?>
                    <?php if ( get_the_content() ) : ?>
                        <div class="floru-team-card__bio">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    $linkedin = get_post_meta( get_the_ID(), '_floru_team_linkedin', true );
                    if ( $linkedin ) : ?>
                        <a href="<?php echo esc_url( $linkedin ); ?>" class="floru-team-card__linkedin" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            LinkedIn
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
            <p>No team members have been added yet. Go to <strong>Team Members</strong> in the WordPress admin to add them.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="floru-cta" data-animate="fade-in">
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