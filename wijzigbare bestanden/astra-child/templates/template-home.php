<?php
/**
 * Template Name: Floru — Home
 */

get_header();

$pid = get_the_ID();

$m = function( $key, $default = '' ) use ( $pid ) {
    return floru_get_meta( $pid, $key, $default );
};
?>

<section class="floru-hero">
    <div class="floru-hero__inner">
        <div class="floru-hero__text">
            <span class="floru-hero__label"><?php echo esc_html( $m( '_floru_hero_label', 'Defence & Security Consultancy' ) ); ?></span>
            <h1><?php echo esc_html( $m( '_floru_hero_heading', 'Strategic Guidance for Defence and Security Markets' ) ); ?></h1>
            <p><?php echo esc_html( $m( '_floru_hero_description', 'We help international defence and security companies navigate government markets, build decisive stakeholder relationships, and win critical tenders across the Netherlands and Europe.' ) ); ?></p>
            <div class="floru-hero__actions">
                <?php
                $btn1_text = $m( '_floru_hero_btn1_text', 'Our Approach' );
                $btn1_url  = $m( '_floru_hero_btn1_url', '/about/' );
                $btn2_text = $m( '_floru_hero_btn2_text', 'Get in Touch' );
                $btn2_url  = $m( '_floru_hero_btn2_url', '/contact/' );
                if ( $btn1_text ) : ?>
                    <a href="<?php echo esc_url( $btn1_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $btn1_text ); ?></a>
                <?php endif; ?>
                <?php if ( $btn2_text ) : ?>
                    <a href="<?php echo esc_url( $btn2_url ); ?>" class="floru-btn floru-btn--outline-white floru-btn--lg"><?php echo esc_html( $btn2_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="floru-hero__visual" aria-hidden="true">
            <div class="floru-hero__graphic">
                <div class="floru-hero__graphic-line"></div>
                <div class="floru-hero__graphic-line"></div>
                <div class="floru-hero__graphic-line"></div>
                <div class="floru-hero__graphic-dot"></div>
                <div class="floru-hero__graphic-dot"></div>
            </div>
        </div>
    </div>
</section>

<section class="floru-section">
    <div class="floru-container">
        <div class="floru-intro">
            <div class="floru-intro__text">
                <span class="floru-section-label"><?php echo esc_html( $m( '_floru_intro_label', 'Who We Are' ) ); ?></span>
                <h2><?php echo esc_html( $m( '_floru_intro_heading', 'A Trusted Partner in Defence Business Development' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_intro_text1', 'Floru is a specialised consultancy supporting international defence, security, and high-technology companies entering or expanding in the Dutch and European markets.' ) ); ?></p>
                <p><?php echo esc_html( $m( '_floru_intro_text2', 'With decades of experience at the intersection of government, industry, and procurement, we provide the strategic insight and practical support our clients need to succeed.' ) ); ?></p>
                <?php
                $intro_btn_text = $m( '_floru_intro_btn_text', 'Learn More About Us' );
                $intro_btn_url  = $m( '_floru_intro_btn_url', '/about/' );
                if ( $intro_btn_text ) : ?>
                    <a href="<?php echo esc_url( $intro_btn_url ); ?>" class="floru-btn floru-btn--outline floru-btn--sm"><?php echo esc_html( $intro_btn_text ); ?></a>
                <?php endif; ?>
            </div>
            <div class="floru-intro__visual">
                <?php
                $intro_img = $m( '_floru_intro_image' );
                if ( ! $intro_img && has_post_thumbnail( $pid ) ) {
                    $intro_img = get_the_post_thumbnail_url( $pid, 'large' );
                }
                if ( ! $intro_img ) {
                    $intro_img = get_stylesheet_directory_uri() . '/assets/images/placeholder.svg';
                }
                ?>
                <img src="<?php echo esc_url( $intro_img ); ?>" alt="<?php echo esc_attr( $m( '_floru_intro_heading', 'Floru consultancy' ) ); ?>" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="floru-stats-band">
    <div class="floru-container">
        <div class="floru-stats-band__grid">
            <?php for ( $i = 1; $i <= 3; $i++ ) :
                $num   = $m( '_floru_stat' . $i . '_number' );
                $label = $m( '_floru_stat' . $i . '_label' );
                if ( ! $num && ! $label ) {
                    $defaults = array(
                        1 => array( '20+', 'Years of Experience' ),
                        2 => array( '50+', 'Completed Projects' ),
                        3 => array( '15+', 'International Clients' ),
                    );
                    $num   = $defaults[ $i ][0];
                    $label = $defaults[ $i ][1];
                }
                if ( $num || $label ) : ?>
                <div class="floru-stats-band__item">
                    <span class="floru-stats-band__number"><?php echo esc_html( $num ); ?></span>
                    <span class="floru-stats-band__label"><?php echo esc_html( $label ); ?></span>
                </div>
            <?php endif; endfor; ?>
        </div>
    </div>
</section>

<section class="floru-section">
    <div class="floru-container">
        <div class="floru-section-header">
            <span class="floru-section-label"><?php echo esc_html( $m( '_floru_hsvc_label', 'What We Do' ) ); ?></span>
            <h2><?php echo esc_html( $m( '_floru_hsvc_heading', 'Three Pillars of Support' ) ); ?></h2>
            <p><?php echo esc_html( $m( '_floru_hsvc_description', 'Strategic advisory, relationship management, and hands-on tender expertise — combined to give our clients a decisive edge.' ) ); ?></p>
        </div>
        <div class="floru-services-grid">
            <?php
            $svc_defaults = array(
                1 => array( 'Business Development', 'Market opportunity identification, procurement pipeline mapping, and go-to-market strategies tailored to the European defence landscape.', 'trending-up', '/services/' ),
                2 => array( 'Stakeholder Engagement', 'Connecting our clients with the right decision-makers across government, military, and industry — with the right message at the right time.', 'users', '/services/' ),
                3 => array( 'Tender Support', 'End-to-end tender lifecycle guidance — from positioning and pre-qualification through to proposal development and contract award.', 'file-text', '/services/' ),
            );
            for ( $i = 1; $i <= 3; $i++ ) :
                $title = $m( '_floru_hsvc' . $i . '_title', $svc_defaults[ $i ][0] );
                $desc  = $m( '_floru_hsvc' . $i . '_desc', $svc_defaults[ $i ][1] );
                $icon  = $m( '_floru_hsvc' . $i . '_icon', $svc_defaults[ $i ][2] );
                $url   = $m( '_floru_hsvc' . $i . '_url', $svc_defaults[ $i ][3] );
            ?>
            <a href="<?php echo esc_url( $url ); ?>" class="floru-service-card">
                <span class="floru-service-card__num"><?php echo esc_html( str_pad( $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
                <div class="floru-service-card__icon">
                    <?php echo floru_icon( $icon ); ?>
                </div>
                <h3><?php echo esc_html( $title ); ?></h3>
                <p><?php echo esc_html( $desc ); ?></p>
                <span class="floru-service-card__link">Learn more <span aria-hidden="true">&rarr;</span></span>
            </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="floru-section floru-section--gray">
    <div class="floru-container">
        <div class="floru-trust-layout">
            <div class="floru-trust-layout__header">
                <span class="floru-section-label"><?php echo esc_html( $m( '_floru_why_label', 'Why Floru' ) ); ?></span>
                <h2><?php echo wp_kses_post( $m( '_floru_why_heading', 'Built on Experience,<br>Driven by Results' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_why_description', 'We are not a large agency. We are a focused team of senior professionals with deep domain expertise and a strong track record in defence and security.' ) ); ?></p>
            </div>
            <div class="floru-trust-layout__items">
                <?php
                $why_defaults = array(
                    1 => array( 'Defence Domain Expertise', 'Hands-on knowledge of defence procurement, government structures, and the political context of European security markets.', 'shield' ),
                    2 => array( 'International Reach', 'Bridging international manufacturers and European end-users, understanding both sides of the conversation.', 'globe' ),
                    3 => array( 'Strategic Focus', 'Every engagement starts with a clear objective. We invest time where it matters most and avoid unnecessary complexity.', 'target' ),
                    4 => array( 'Proven Track Record', 'Award-winning tenders and successful market entries for defence companies in the Netherlands and beyond.', 'check-circle' ),
                );
                for ( $i = 1; $i <= 4; $i++ ) :
                    $title = $m( '_floru_why' . $i . '_title', $why_defaults[ $i ][0] );
                    $desc  = $m( '_floru_why' . $i . '_desc', $why_defaults[ $i ][1] );
                    $icon  = $m( '_floru_why' . $i . '_icon', $why_defaults[ $i ][2] );
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
    </div>
</section>

<section class="floru-section">
    <div class="floru-container">
        <div class="floru-section-header">
            <span class="floru-section-label"><?php echo esc_html( $m( '_floru_hteam_label', 'Our Team' ) ); ?></span>
            <h2><?php echo esc_html( $m( '_floru_hteam_heading', 'Senior Professionals, Personal Approach' ) ); ?></h2>
            <p><?php echo esc_html( $m( '_floru_hteam_description', 'Our consultants bring decades of experience in defence, government, international business, and procurement.' ) ); ?></p>
        </div>

        <div class="floru-team-grid">
            <?php
            $team_members = new WP_Query( array(
                'post_type'      => 'floru_team',
                'posts_per_page' => 4,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ) );
            if ( $team_members->have_posts() ) :
                while ( $team_members->have_posts() ) : $team_members->the_post();
                    $role         = get_post_meta( get_the_ID(), '_floru_team_role', true );
                    $profile_link = get_post_meta( get_the_ID(), '_floru_team_profile_link', true );
                    $team_page    = get_page_by_path( 'team' );
                    $team_url     = $team_page ? get_permalink( $team_page ) : '/team/';
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
                    <?php if ( has_excerpt() ) : ?>
                        <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $profile_link ? $profile_link : $team_url ); ?>" class="floru-team-card__link" aria-label="View profile of <?php the_title_attribute(); ?>">View Profile &rarr;</a>
                </div>
            </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
            <div class="floru-team-card">
                <div class="floru-team-card__editorial-image">
                    <div class="floru-team-card__empty-state"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg></div>
                </div>
                <div class="floru-team-card__body">
                    <h3>Team Member</h3>
                    <span class="floru-team-card__role">Consultant</span>
                    <p>Add team members in WordPress admin under Team Members.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php
        $team_btn_text = $m( '_floru_hteam_btn_text', 'Meet the Full Team' );
        $team_btn_url  = $m( '_floru_hteam_btn_url', '/team/' );
        if ( $team_btn_text ) : ?>
        <div class="floru-text-center" style="margin-top: 40px;">
            <a href="<?php echo esc_url( $team_btn_url ); ?>" class="floru-btn floru-btn--outline"><?php echo esc_html( $team_btn_text ); ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$clients = new WP_Query( array(
    'post_type'      => 'floru_client',
    'posts_per_page' => 20,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    ) );
if ( $clients->have_posts() ) : ?>
<section class="floru-section floru-section--gray" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="floru-container">
        <div class="floru-clients-band">
            <p class="floru-clients-band__label"><?php echo esc_html( $m( '_floru_hclients_label', 'Trusted by international defence companies including' ) ); ?></p>
            <span class="floru-clients-band__divider" aria-hidden="true"></span>
            <div class="floru-clients-grid">
                <?php while ( $clients->have_posts() ) : $clients->the_post();
                    $client_link = get_post_meta( get_the_ID(), '_floru_client_link', true );
                    $logo_url    = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                    if ( ! $logo_url ) continue;
                    if ( $client_link ) : ?>
                        <a href="<?php echo esc_url( $client_link ); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>" class="floru-client-logo">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php the_title_attribute(); ?>" class="floru-client-logo">
                    <?php endif; ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="floru-cta">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( $m( '_floru_hcta_heading', 'Ready to Discuss Your Next Opportunity?' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_hcta_description', 'Whether you are exploring market entry, preparing for a tender, or seeking strategic support — we welcome the conversation.' ) ); ?></p>
            </div>
            <div class="floru-cta__actions">
                <?php
                $cta_btn_text = $m( '_floru_hcta_btn_text', 'Contact Us' );
                $cta_btn_url  = $m( '_floru_hcta_btn_url', '/contact/' );
                if ( $cta_btn_text ) : ?>
                    <a href="<?php echo esc_url( $cta_btn_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_btn_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>