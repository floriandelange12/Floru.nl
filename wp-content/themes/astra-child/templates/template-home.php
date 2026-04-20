<?php
/**
 * Template Name: Floru — Home
 */

get_header();

$pid = get_the_ID();

$m = function( $key, $default = '' ) use ( $pid ) {
    return floru_get_meta( $pid, $key, $default );
};

$balanced_logo_columns = static function( $count, $max_columns = 6 ) {
    $count       = max( 0, (int) $count );
    $max_columns = max( 1, min( $count ?: 1, (int) $max_columns ) );

    if ( $count <= 1 ) {
        return $max_columns;
    }

    $best_columns    = 1;
    $best_fill_ratio = -1;
    $best_rows       = PHP_INT_MAX;

    for ( $columns = 2; $columns <= $max_columns; $columns++ ) {
        $rows           = (int) ceil( $count / $columns );
        $last_row_count = $count - ( ( $rows - 1 ) * $columns );
        $fill_ratio     = $last_row_count / $columns;

        if (
            $fill_ratio > $best_fill_ratio ||
            ( abs( $fill_ratio - $best_fill_ratio ) < 0.0001 && $rows < $best_rows ) ||
            ( abs( $fill_ratio - $best_fill_ratio ) < 0.0001 && $rows === $best_rows && $columns > $best_columns )
        ) {
            $best_columns    = $columns;
            $best_fill_ratio = $fill_ratio;
            $best_rows       = $rows;
        }
    }

    return $best_columns;
};
?>

<section class="floru-hero" data-animate="fade-in">
    <div class="floru-hero__inner">
        <div class="floru-hero__text">
            <span class="floru-hero__label"><?php echo esc_html( $m( '_floru_hero_label', 'Defence & Security Consultancy' ) ); ?></span>
            <h1><?php echo esc_html( $m( '_floru_hero_heading', 'Strategic Guidance for Defence and Security Markets' ) ); ?></h1>
            <p><?php echo esc_html( $m( '_floru_hero_description', 'We help international defence and security companies navigate government markets, build decisive stakeholder relationships, and win critical tenders across the Netherlands and Europe.' ) ); ?></p>
            <div class="floru-hero__actions">
                <?php
                $btn1_text = $m( '_floru_hero_btn1_text', 'Our Approach' );
                $btn1_url  = $m( '_floru_hero_btn1_url', home_url( '/about/' ) );
                $btn2_text = $m( '_floru_hero_btn2_text', 'Get in Touch' );
                $btn2_url  = $m( '_floru_hero_btn2_url', home_url( '/contact/' ) );
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

<section class="floru-section" data-animate>
    <div class="floru-container">
        <?php
        $intro_img = $m( '_floru_intro_image' );
        if ( ! $intro_img && has_post_thumbnail( $pid ) ) {
            $intro_img = get_the_post_thumbnail_url( $pid, 'large' );
        }
        $intro_has_custom_visual = (bool) $intro_img;
        $intro_visual_src        = $intro_has_custom_visual
            ? $intro_img
            : get_stylesheet_directory_uri() . '/assets/images/floru-institutions.jpg';
        $intro_visual_alt        = $intro_has_custom_visual
            ? $m( '_floru_intro_heading', 'Floru consultancy' )
            : __( 'European institutional alignment', 'astra-child-floru' );
        $intro_support_items = array(
            __( 'Market-entry direction', 'astra-child-floru' ),
            __( 'Stakeholder positioning', 'astra-child-floru' ),
            __( 'Tender discipline', 'astra-child-floru' ),
        );
        $intro_support_note = __( 'Small senior team. Direct involvement where decision quality matters most.', 'astra-child-floru' );

        ?>
        <div class="floru-intro">
            <div class="floru-intro__text">
                <span class="floru-section-label"><?php echo esc_html( $m( '_floru_intro_label', 'Who We Are' ) ); ?></span>
                <h2><?php echo esc_html( $m( '_floru_intro_heading', 'A Trusted Partner in Defence Business Development' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_intro_text1', 'Floru is a specialised consultancy supporting international defence, security, and high-technology companies entering or expanding in the Dutch and European markets.' ) ); ?></p>
                <p><?php echo esc_html( $m( '_floru_intro_text2', 'With decades of experience at the intersection of government, industry, and procurement, we provide the strategic insight and practical support our clients need to succeed.' ) ); ?></p>
                <?php
                $intro_btn_text = $m( '_floru_intro_btn_text', 'Learn More About Us' );
                $intro_btn_url  = $m( '_floru_intro_btn_url', home_url( '/about/' ) );
                if ( $intro_btn_text ) : ?>
                    <a href="<?php echo esc_url( $intro_btn_url ); ?>" class="floru-btn floru-btn--outline floru-btn--sm"><?php echo esc_html( $intro_btn_text ); ?></a>
                <?php endif; ?>
            </div>
            <div class="floru-intro__support">
                <figure class="floru-context-frame floru-context-frame--home<?php echo $intro_has_custom_visual ? '' : ' floru-context-frame--photo'; ?>">
                    <div class="floru-context-frame__media">
                        <img src="<?php echo esc_url( $intro_visual_src ); ?>" alt="<?php echo esc_attr( $intro_visual_alt ); ?>" loading="lazy" class="floru-img-block">
                    </div>
                </figure>

                <aside class="floru-context-panel floru-context-panel--home">
                    <span class="floru-context-panel__eyebrow"><?php esc_html_e( 'Where Floru adds value', 'astra-child-floru' ); ?></span>
                    <h3 class="floru-context-panel__title"><?php esc_html_e( 'Strategic support shaped for institutional buying cycles.', 'astra-child-floru' ); ?></h3>
                    <ul class="floru-context-panel__list" aria-label="<?php echo esc_attr__( 'Core support themes', 'astra-child-floru' ); ?>">
                        <?php foreach ( $intro_support_items as $intro_support_item ) : ?>
                            <li><?php echo esc_html( $intro_support_item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="floru-context-panel__note"><?php echo esc_html( $intro_support_note ); ?></p>
                </aside>
            </div>
        </div>
    </div>
</section>

<section class="floru-stats-band" data-animate="fade-in">
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

<section class="floru-section" data-animate>
    <div class="floru-container">
        <div class="floru-section-header">
            <span class="floru-section-label"><?php echo esc_html( $m( '_floru_hsvc_label', 'What We Do' ) ); ?></span>
            <h2><?php echo esc_html( $m( '_floru_hsvc_heading', 'Three Pillars of Support' ) ); ?></h2>
            <p><?php echo esc_html( $m( '_floru_hsvc_description', 'Strategic advisory, relationship management, and hands-on tender expertise — combined to give our clients a decisive edge.' ) ); ?></p>
        </div>
        <div class="floru-services-grid" data-animate-stagger data-animate>
            <?php
            $svc_defaults = array(
                1 => array( 'Business Development', 'Market opportunity identification, procurement pipeline mapping, and go-to-market strategies tailored to the European defence landscape.', 'trending-up', home_url( '/services/' ) ),
                2 => array( 'Stakeholder Engagement', 'Connecting our clients with the right decision-makers across government, military, and industry — with the right message at the right time.', 'users', home_url( '/services/' ) ),
                3 => array( 'Tender Support', 'End-to-end tender lifecycle guidance — from positioning and pre-qualification through to proposal development and contract award.', 'file-text', home_url( '/services/' ) ),
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
                <span class="floru-service-card__link"><?php echo esc_html( floru_t( 'Learn more' ) ); ?> <span aria-hidden="true">&rarr;</span></span>
            </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="floru-section floru-section--gray" data-animate>
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
                            <h3><?php echo esc_html( $title ); ?></h3>
                        <p><?php echo esc_html( $desc ); ?></p>
                    </div>
                </div>
                <?php endif; endfor; ?>
            </div>
        </div>
    </div>
</section>

<section class="floru-section" data-animate>
    <div class="floru-container">
        <div class="floru-section-header">
            <span class="floru-section-label"><?php echo esc_html( $m( '_floru_hteam_label', 'Our Team' ) ); ?></span>
            <h2><?php echo esc_html( $m( '_floru_hteam_heading', 'Senior Professionals, Personal Approach' ) ); ?></h2>
            <p><?php echo esc_html( $m( '_floru_hteam_description', 'Our consultants bring decades of experience in defence, government, international business, and procurement.' ) ); ?></p>
        </div>

        <div class="floru-team-grid">
            <?php
            $team_url = floru_get_team_url();
            $team_cards = array();

            $team_members = new WP_Query( array(
                'post_type'      => 'floru_team',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            ) );
            if ( $team_members->have_posts() ) :
                while ( $team_members->have_posts() ) : $team_members->the_post();
                    $member_id    = get_the_ID();
                    $name         = floru_get_translated_post_title_raw( $member_id );
                    $role         = floru_get_translated_post_meta( $member_id, '_floru_team_role' );
                    $profile_link = get_post_meta( $member_id, '_floru_team_profile_link', true );
                    $excerpt      = has_excerpt( $member_id ) ? floru_get_translated_post_excerpt_raw( $member_id ) : '';
                    if ( ! $excerpt ) {
                        $raw = wp_strip_all_tags( strip_shortcodes( floru_get_translated_post_content_raw( $member_id ) ) );
                        if ( $raw ) {
                            $excerpt = wp_trim_words( $raw, 24, '&hellip;' );
                        }
                    }
                    $member_slug = get_post_field( 'post_name', $member_id );
                    $portrait    = has_post_thumbnail( $member_id ) ? get_the_post_thumbnail(
                        $member_id,
                        'floru-team-portrait',
                        array(
                            'loading'  => 'lazy',
                            'decoding' => 'async',
                            'sizes'    => '(min-width: 1280px) 360px, (min-width: 1024px) 30vw, (min-width: 640px) 45vw, 90vw',
                            'alt'      => sprintf( /* translators: %s = team member name */ esc_attr__( 'Portret van %s', 'astra-child-floru' ), $name ),
                        )
                    ) : '';

                    $team_cards[] = array(
                        'slug'         => $member_slug,
                        'name'         => $name,
                        'role'         => $role,
                        'excerpt'      => $excerpt,
                        'profile_link' => $profile_link,
                        'card_url'     => $profile_link ? $profile_link : trailingslashit( $team_url ) . '#team-' . $member_slug,
                        'portrait'     => $portrait,
                    );
                endwhile;
                wp_reset_postdata();

                if ( function_exists( 'floru_prioritize_team_collection' ) ) {
                    $team_cards = floru_prioritize_team_collection(
                        $team_cards,
                        static function( $card ) {
                            return array(
                                'slug' => isset( $card['slug'] ) ? $card['slug'] : '',
                                'name' => isset( $card['name'] ) ? $card['name'] : '',
                            );
                        }
                    );
                }

                $team_cards = array_slice( $team_cards, 0, 3 );

                foreach ( $team_cards as $card ) :
            ?>
            <article class="floru-team-card">
                <figure class="floru-team-card__media">
                    <?php if ( $card['portrait'] ) : ?>
                        <?php echo wp_kses_post( $card['portrait'] ); ?>
                    <?php else : ?>
                        <div class="floru-team-card__placeholder" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        </div>
                    <?php endif; ?>
                </figure>
                <div class="floru-team-card__body">
                    <header class="floru-team-card__header">
                        <?php if ( $card['role'] ) : ?>
                            <span class="floru-team-card__role"><?php echo esc_html( $card['role'] ); ?></span>
                        <?php endif; ?>
                        <h3 class="floru-team-card__name"><?php echo esc_html( $card['name'] ); ?></h3>
                    </header>
                    <?php if ( $card['excerpt'] ) : ?>
                        <p class="floru-team-card__bio"><?php echo esc_html( $card['excerpt'] ); ?></p>
                    <?php endif; ?>
                    <footer class="floru-team-card__footer">
                        <a class="floru-team-card__action"
                           href="<?php echo esc_url( $card['card_url'] ); ?>"
                           <?php if ( $card['profile_link'] ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                           aria-label="<?php echo esc_attr( sprintf( /* translators: %s = team member name */ __( 'Bekijk profiel van %s', 'astra-child-floru' ), $card['name'] ) ); ?>">
                            <span class="floru-team-card__action-label"><?php $card['profile_link'] ? esc_html_e( 'Profiel op LinkedIn', 'astra-child-floru' ) : esc_html_e( 'Bekijk profiel', 'astra-child-floru' ); ?></span>
                            <span class="floru-team-card__action-arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    </footer>
                </div>
            </article>
            <?php
                endforeach;
            else :
            ?>
            <article class="floru-team-card">
                <figure class="floru-team-card__media">
                    <div class="floru-team-card__placeholder" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                    </div>
                </figure>
                <div class="floru-team-card__body">
                    <header class="floru-team-card__header">
                        <span class="floru-team-card__role"><?php echo esc_html( floru_t( 'Consultant' ) ); ?></span>
                        <h3 class="floru-team-card__name"><?php echo esc_html( floru_t( 'Team Member' ) ); ?></h3>
                    </header>
                    <p class="floru-team-card__bio"><?php echo esc_html( floru_t( 'Add team members in WordPress admin under Team Members.' ) ); ?></p>
                </div>
            </article>
            <?php endif; ?>
        </div>

        <?php
        $team_btn_text = $m( '_floru_hteam_btn_text', 'Meet the Full Team' );
        $team_btn_url  = floru_normalize_team_url( $m( '_floru_hteam_btn_url', $team_url ) );
        if ( $team_btn_text ) : ?>
        <div class="floru-text-center floru-mt-40">
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
$home_client_logos = array();

if ( ! empty( $clients->posts ) ) {
    foreach ( $clients->posts as $client_post ) {
        $client_link = get_post_meta( $client_post->ID, '_floru_client_link', true );
        $thumb_id    = get_post_thumbnail_id( $client_post->ID );
        $thumb_src   = wp_get_attachment_image_src( $thumb_id, 'medium' );

        if ( ! $thumb_src ) {
            continue;
        }

        $logo_url = $thumb_src[0];
        $orig_w   = $thumb_src[1];
        $orig_h   = max( $thumb_src[2], 1 );
        $ratio    = $orig_w / $orig_h;
        $h        = round( sqrt( 2400 / max( $ratio, 0.5 ) ) );
        $h        = max( 28, min( 42, $h ) );
        $w        = round( $h * $ratio );
        $w        = max( 40, min( 110, $w ) );

        $home_client_logos[] = array(
            'link'     => $client_link,
            'logo_url' => $logo_url,
            'title'    => floru_get_translated_post_title_raw( $client_post->ID ),
            'style'    => 'width:' . $w . 'px;height:' . $h . 'px;',
        );
    }
}

$home_client_logo_columns = $balanced_logo_columns( count( $home_client_logos ) );

if ( ! empty( $home_client_logos ) ) : ?>
<section class="floru-section floru-section--gray floru-section--compact" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-clients-band">
            <p class="floru-clients-band__label"><?php echo esc_html( floru_t( $m( '_floru_hclients_label', 'Trusted by international defence companies including' ) ) ); ?></p>
            <span class="floru-clients-band__divider" aria-hidden="true"></span>
            <div class="floru-clients-grid" style="<?php echo esc_attr( '--floru-clients-grid-columns-tablet:' . $home_client_logo_columns . ';' ); ?>">
                <?php foreach ( $home_client_logos as $home_client_logo ) : ?>
                    <?php if ( $home_client_logo['link'] ) : ?>
                        <a href="<?php echo esc_url( $home_client_logo['link'] ); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo esc_url( $home_client_logo['logo_url'] ); ?>" alt="<?php echo esc_attr( $home_client_logo['title'] ); ?>" class="floru-client-logo" style="<?php echo esc_attr( $home_client_logo['style'] ); ?>">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo esc_url( $home_client_logo['logo_url'] ); ?>" alt="<?php echo esc_attr( $home_client_logo['title'] ); ?>" class="floru-client-logo" style="<?php echo esc_attr( $home_client_logo['style'] ); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="floru-cta" data-animate="fade-in">
    <div class="floru-container">
        <div class="floru-cta__inner">
            <div class="floru-cta__text">
                <h2><?php echo esc_html( $m( '_floru_hcta_heading', 'Ready to Discuss Your Next Opportunity?' ) ); ?></h2>
                <p><?php echo esc_html( $m( '_floru_hcta_description', 'Whether you are exploring market entry, preparing for a tender, or seeking strategic support — we welcome the conversation.' ) ); ?></p>
            </div>
            <div class="floru-cta__actions">
                <?php
                $cta_btn_text = $m( '_floru_hcta_btn_text', 'Contact Us' );
                $cta_btn_url  = $m( '_floru_hcta_btn_url', home_url( '/contact/' ) );
                if ( $cta_btn_text ) : ?>
                    <a href="<?php echo esc_url( $cta_btn_url ); ?>" class="floru-btn floru-btn--primary floru-btn--lg"><?php echo esc_html( $cta_btn_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>