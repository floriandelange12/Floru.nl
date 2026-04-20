<?php
/**
 * Floru Content Migration
 * Populates all editable fields, team members, and clients with the existing
 * default content from the templates so the admin is not blank.
 *
 * Runs once on admin_init, then sets an option flag to prevent re-running.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_switch_theme', 'floru_migrate_content', 30 );

/**
 * One-time migration to ensure all real team members exist.
 * Checks each member individually by title — safe to run alongside existing data.
 */
add_action( 'admin_init', 'floru_ensure_team_members' );
function floru_ensure_team_members() {
    if ( get_option( 'floru_team_members_v2' ) ) {
        return;
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $members = array(
        array(
            'title' => 'Ruud de Pruyssenaere de la Woestijne',
            'role'  => 'Director FLORU Consultancy — Army, Brigadier General ret.',
            'order' => 1,
        ),
        array(
            'title' => 'Jaap Willemse',
            'role'  => 'Air Force, Major General ret.',
            'order' => 2,
        ),
        array(
            'title' => 'Michael van der Klip',
            'role'  => 'Navy, Commander ret.',
            'order' => 3,
        ),
        array(
            'title' => 'Jan Zeggelaar',
            'role'  => 'Army, Major ret.',
            'order' => 4,
        ),
        array(
            'title' => 'Peter Lenselink',
            'role'  => 'Navy, Rear Admiral LH ret.',
            'order' => 5,
        ),
        array(
            'title' => 'Michiel Hijmans',
            'role'  => 'Navy, Rear Admiral LH ret.',
            'order' => 6,
        ),
        array(
            'title' => 'Marina Eppen-Pruyssenaere',
            'role'  => 'Technical Support',
            'order' => 7,
        ),
    );

    foreach ( $members as $member ) {
        $exists = new WP_Query( array(
            'post_type'      => 'floru_team',
            'title'          => $member['title'],
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ) );
        if ( ! $exists->have_posts() ) {
            $post_id = wp_insert_post( array(
                'post_type'   => 'floru_team',
                'post_title'  => $member['title'],
                'post_status' => 'publish',
                'menu_order'  => $member['order'],
            ) );
            if ( $post_id && ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, '_floru_team_role', $member['role'] );
            }
        }
    }

    update_option( 'floru_team_members_v2', true );
}

/**
 * One-time migration to replace dummy clients with real Floru clients.
 */
add_action( 'admin_init', 'floru_ensure_real_clients' );
function floru_ensure_real_clients() {
    if ( get_option( 'floru_clients_v2' ) ) {
        return;
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Remove existing dummy clients.
    $dummy_clients = get_posts( array(
        'post_type'      => 'floru_client',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ) );
    foreach ( $dummy_clients as $dummy_id ) {
        wp_delete_post( $dummy_id, true );
    }

    // Real clients from floru.nl.
    $clients = array(
        array(
            'title'   => 'SAAB',
            'content' => 'SAAB serves the global market with world-leading products, services and solutions from military defence to civil security. With operations on every continent, Saab continuously develops, adapts and improves new technology to meet customers\' changing needs.',
            'order'   => 1,
        ),
        array(
            'title'   => 'NU Security Consultancy',
            'content' => 'NU Security Consultancy (NUSC) offers all-round security services. Theft, sabotage, vandalism, industrial espionage, etc. are examples of threats and risks for every organization. NUSC maps out every risk and provides clear recommendations for organizational, structural and electronic measures to be taken. NUSC provides customization.',
            'order'   => 2,
        ),
        array(
            'title'   => 'CNIM',
            'content' => 'CNIM is a French industrial engineering contractor and equipment manufacturer. We operate in the environment, energy, defense and high technology sectors. CNIM designs and manufactures equipment and offers solutions for a safer, better protected, more energy-efficient and environmentally sound world.',
            'order'   => 3,
        ),
        array(
            'title'   => 'Exensor',
            'content' => 'The world leader in the design, development, integration and supply of networked unattended ground sensor systems for homeland security and military applications. We focus on customer needs by adapting our solutions to meet their specific requirements.',
            'order'   => 4,
        ),
        array(
            'title'   => 'Pro Systems',
            'content' => 'Pro Systems. Synonymous with audio-visual solutions. Creates the most amazing virtual environments, from which you can safely train all imaginable actions. Furnished any desired space with audio-visual total solutions.',
            'order'   => 5,
        ),
        array(
            'title'   => 'Stratego',
            'content' => 'Stratego is a recruitment agency which focuses on former military personnel. It supports companies and organizations in Recruitment & selection, secondment / project management and other capacity solutions. Stratego is located at the interface of Defence and the business community and distinguishes itself – just like the candidates – on knowledge, quality, mentality and result orientation.',
            'order'   => 6,
        ),
        array(
            'title'   => 'X-Systems',
            'content' => 'X-SYSTEMS is a Dutch Mobile & IoT Security company, located in The Hague (city of Justice and Peace) and Eindhoven (city of Technology) and focusses itself entirely on end-to-end and end-point information and communication security.',
            'order'   => 7,
        ),
        array(
            'title'   => 'Dujardin',
            'content' => 'Dujardin has been successful in the market for architectural security solutions for decades. Our specialization is safe-related with safe rooms, security doors, turnstiles and burglar-resistant walls (both compact and lightweight). Dujardin is in charge of all activities. From advice to transport, installation and fault handling. We provide solutions and not just products. This service and the willingness to contribute ideas form the basis for a lasting relationship.',
            'order'   => 8,
        ),
        array(
            'title'   => 'VREE',
            'content' => 'VREE\'s mission is to make multiplayer VR available to everyone. Creating full body vr applications have a few hurdles that require smart solutions. Developing these solutions is difficult and often time consuming. The VREE Platform provides content developers with a solid software foundation.',
            'order'   => 9,
        ),
        array(
            'title'   => 'G4S',
            'content' => 'G4S Whether it concerns the safety of people, or the security of buildings, goods or processes: every situation requires its own approach. When it comes to security and safety, G4S is at home in all markets. G4S analyzes the situation, thinks along and provides you with the right people and the latest technique.',
            'order'   => 10,
        ),
        array(
            'title'   => 'Everbridge',
            'content' => 'Everbridge supports organizations in the field of crisis management, alerting, upscaling and incident management. Respond supplies distinctive and appropriate ICT solutions that reduce the risk profile, complexity and costs of crisis management processes. Hence, disruptions are resolved faster, have fewer consequences and can even be prevented. Respond provides customization.',
            'order'   => 11,
        ),
        array(
            'title'   => 'TBM',
            'content' => 'TBM supplies high-quality security goods and services to government organisations in the Benelux. TBM is the exclusive representative of a number of qualified international partners and suppliers in the security domain. TBM offers a range of products, services and solutions in the field of weapons, accessories, tactical equipment and technological applications. In addition, TBM also focuses on the development of innovative products and services. Consider for instance the design and integration of robotics and technological applications.',
            'order'   => 12,
        ),
    );

    foreach ( $clients as $client ) {
        wp_insert_post( array(
            'post_type'    => 'floru_client',
            'post_title'   => $client['title'],
            'post_content' => $client['content'],
            'post_status'  => 'publish',
            'menu_order'   => $client['order'],
        ) );
    }

    update_option( 'floru_clients_v2', true );
}

function floru_migrate_content() {
    if ( get_option( 'floru_content_migrated' ) ) {
        return;
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    /* ======================================================================
       HOME PAGE
       ====================================================================== */
    $home = get_page_by_path( 'home' );
    if ( $home ) {
        $h = $home->ID;
        $home_meta = array(
            // Hero
            '_floru_hero_label'       => 'Defence & Security Consultancy',
            '_floru_hero_heading'     => 'Strategic Guidance for Defence and Security Markets',
            '_floru_hero_description' => 'We help international defence and security companies navigate government markets, build decisive stakeholder relationships, and win critical tenders across the Netherlands and Europe.',
            '_floru_hero_btn1_text'   => 'Our Approach',
            '_floru_hero_btn1_url'    => home_url( '/about/' ),
            '_floru_hero_btn2_text'   => 'Get in Touch',
            '_floru_hero_btn2_url'    => home_url( '/contact/' ),
            // Intro
            '_floru_intro_label'     => 'Who We Are',
            '_floru_intro_heading'   => 'A Trusted Partner in Defence Business Development',
            '_floru_intro_text1'     => 'Floru is a specialised consultancy supporting international defence, security, and high-technology companies entering or expanding in the Dutch and European markets.',
            '_floru_intro_text2'     => 'With decades of experience at the intersection of government, industry, and procurement, we provide the strategic insight and practical support our clients need to succeed.',
            '_floru_intro_btn_text'  => 'Learn More About Us',
            '_floru_intro_btn_url'   => home_url( '/about/' ),
            // Stats
            '_floru_stat1_number'    => '20+',
            '_floru_stat1_label'     => 'Years of Experience',
            '_floru_stat2_number'    => '50+',
            '_floru_stat2_label'     => 'Completed Projects',
            '_floru_stat3_number'    => '15+',
            '_floru_stat3_label'     => 'International Clients',
            // Services section
            '_floru_hsvc_label'       => 'What We Do',
            '_floru_hsvc_heading'     => 'Three Pillars of Support',
            '_floru_hsvc_description' => 'Strategic advisory, relationship management, and hands-on tender expertise — combined to give our clients a decisive edge.',
            '_floru_hsvc1_title'     => 'Business Development',
            '_floru_hsvc1_desc'      => 'Market opportunity identification, procurement pipeline mapping, and go-to-market strategies tailored to the European defence landscape.',
            '_floru_hsvc1_icon'      => 'trending-up',
            '_floru_hsvc1_url'       => home_url( '/services/' ),
            '_floru_hsvc2_title'     => 'Stakeholder Engagement',
            '_floru_hsvc2_desc'      => 'Connecting our clients with the right decision-makers across government, military, and industry — with the right message at the right time.',
            '_floru_hsvc2_icon'      => 'users',
            '_floru_hsvc2_url'       => home_url( '/services/' ),
            '_floru_hsvc3_title'     => 'Tender Support',
            '_floru_hsvc3_desc'      => 'End-to-end tender lifecycle guidance — from positioning and pre-qualification through to proposal development and contract award.',
            '_floru_hsvc3_icon'      => 'file-text',
            '_floru_hsvc3_url'       => home_url( '/services/' ),
            // Why Floru
            '_floru_why_label'       => 'Why Floru',
            '_floru_why_heading'     => 'Built on Experience,<br>Driven by Results',
            '_floru_why_description' => 'We are not a large agency. We are a focused team of senior professionals with deep domain expertise and a strong track record in defence and security.',
            '_floru_why1_title'      => 'Defence Domain Expertise',
            '_floru_why1_desc'       => 'Hands-on knowledge of defence procurement, government structures, and the political context of European security markets.',
            '_floru_why1_icon'       => 'shield',
            '_floru_why2_title'      => 'International Reach',
            '_floru_why2_desc'       => 'Bridging international manufacturers and European end-users, understanding both sides of the conversation.',
            '_floru_why2_icon'       => 'globe',
            '_floru_why3_title'      => 'Strategic Focus',
            '_floru_why3_desc'       => 'Every engagement starts with a clear objective. We invest time where it matters most and avoid unnecessary complexity.',
            '_floru_why3_icon'       => 'target',
            '_floru_why4_title'      => 'Proven Track Record',
            '_floru_why4_desc'       => 'Award-winning tenders and successful market entries for defence companies in the Netherlands and beyond.',
            '_floru_why4_icon'       => 'check-circle',
            // Team preview
            '_floru_hteam_label'       => 'Our Team',
            '_floru_hteam_heading'     => 'Senior Professionals, Personal Approach',
            '_floru_hteam_description' => 'Our consultants bring decades of experience in defence, government, international business, and procurement.',
            '_floru_hteam_btn_text'    => 'Meet the Full Team',
            '_floru_hteam_btn_url'     => home_url( '/our-team/' ),
            // Clients band
            '_floru_hclients_label' => 'Trusted by international defence companies including',
            // CTA
            '_floru_hcta_heading'     => 'Ready to Discuss Your Next Opportunity?',
            '_floru_hcta_description' => 'Whether you are exploring market entry, preparing for a tender, or seeking strategic support — we welcome the conversation.',
            '_floru_hcta_btn_text'    => 'Contact Us',
            '_floru_hcta_btn_url'     => home_url( '/contact/' ),
        );
        foreach ( $home_meta as $key => $value ) {
            if ( get_post_meta( $h, $key, true ) === '' ) {
                update_post_meta( $h, $key, $value );
            }
        }
    }

    /* ======================================================================
       ABOUT PAGE
       ====================================================================== */
    $about = get_page_by_path( 'about' );
    if ( $about ) {
        $a = $about->ID;

        // Set page content if currently empty (the body text for about intro)
        $current_content = $about->post_content;
        if ( ! $current_content || ! trim( strip_tags( $current_content ) ) || strpos( $current_content, 'Content is rendered' ) !== false ) {
            wp_update_post( array(
                'ID'           => $a,
                'post_content' => '<p>Floru was founded to address a clear need in the European defence market: international companies entering or expanding in the Netherlands and broader European markets need a trusted local partner who understands both the business environment and the government landscape.</p>
<p>Our team brings together decades of experience at the intersection of defence, government affairs, and international business development. We have held senior positions within government, defence organisations, and industry — giving us a unique perspective that benefits our clients.</p>
<p>We work closely with our clients as an extension of their team. We do not believe in generic, arms-length advisory. Our approach is hands-on, results-oriented, and built on trust.</p>',
            ) );
        }

        $about_meta = array(
            // Page header
            '_floru_ph_label'       => 'About Floru',
            '_floru_ph_heading'     => 'Our Modus Operandi',
            '_floru_ph_description' => 'We combine strategic advisory with hands-on support to help defence and security companies achieve measurable results.',
            // Intro
            '_floru_about_intro_label'   => 'Our Story',
            '_floru_about_intro_heading' => 'Founded on Experience, Focused on Outcomes',
            // Approach steps
            '_floru_approach_label'       => 'How We Work',
            '_floru_approach_heading'     => 'A Structured Approach to Every Engagement',
            '_floru_approach_description' => 'Our methodology is designed to maximise clarity, efficiency, and results at every stage.',
            '_floru_step1_title' => 'Understand & Assess',
            '_floru_step1_desc'  => 'We start by thoroughly understanding your company, your product or capability, and your strategic objectives. We assess the market landscape, procurement environment, and competitive dynamics.',
            '_floru_step2_title' => 'Develop Strategy',
            '_floru_step2_desc'  => 'We formulate a clear, actionable strategy for market entry, growth, or tender response — including stakeholder mapping, positioning, and timeline planning.',
            '_floru_step3_title' => 'Engage & Position',
            '_floru_step3_desc'  => 'We facilitate introductions to key decision-makers, support your messaging and positioning, and help establish your presence in relevant networks and fora.',
            '_floru_step4_title' => 'Execute & Support',
            '_floru_step4_desc'  => 'Whether it is a tender submission, a partnership development, or a long-term market presence — we work alongside you through execution, providing practical and strategic support.',
            '_floru_step5_title' => 'Review & Adapt',
            '_floru_step5_desc'  => 'After every engagement phase, we review progress, refine the approach, and ensure continued alignment with your business objectives.',
            // Values
            '_floru_values_label'   => 'What Guides Us',
            '_floru_values_heading' => 'Our Core Values',
            '_floru_value1_title' => 'Integrity',
            '_floru_value1_desc'  => 'We maintain the highest ethical standards. Our clients trust us because we are straightforward, discreet, and reliable.',
            '_floru_value1_icon'  => 'shield',
            '_floru_value2_title' => 'Results-Oriented',
            '_floru_value2_desc'  => 'We measure our success by our clients\' outcomes. Every action we take is directed towards clear, tangible results.',
            '_floru_value2_icon'  => 'target',
            '_floru_value3_title' => 'Partnership',
            '_floru_value3_desc'  => 'We work as part of your team — not as outsiders. Our engagements are collaborative, trust-based, and long-term.',
            '_floru_value3_icon'  => 'users',
            '_floru_value4_title' => 'Expertise',
            '_floru_value4_desc'  => 'Our deep sector knowledge, network, and market understanding set us apart in the defence and security domain.',
            '_floru_value4_icon'  => 'globe',
            // CTA
            '_floru_pcta_heading'     => 'Interested in Working Together?',
            '_floru_pcta_description' => 'We welcome the opportunity to discuss how we can support your objectives in the European defence market.',
            '_floru_pcta_btn_text'    => 'Contact Us',
            '_floru_pcta_btn_url'     => home_url( '/contact/' ),
        );
        foreach ( $about_meta as $key => $value ) {
            if ( get_post_meta( $a, $key, true ) === '' ) {
                update_post_meta( $a, $key, $value );
            }
        }
    }

    /* ======================================================================
       SERVICES PAGE
       ====================================================================== */
    $services = get_page_by_path( 'services' );
    if ( $services ) {
        $s = $services->ID;
        $services_meta = array(
            // Page header
            '_floru_ph_label'       => 'Our Services',
            '_floru_ph_heading'     => 'How We Support Your Success',
            '_floru_ph_description' => 'Comprehensive strategic advisory, stakeholder management, and tender support tailored to the defence and security sector.',
            // Service 1
            '_floru_svc1_label' => 'Service 01',
            '_floru_svc1_title' => 'Business Development',
            '_floru_svc1_desc'  => '<p>Entering or expanding in European defence markets requires more than a good product — it demands an understanding of political dynamics, procurement cycles, and institutional relationships.</p><p>Floru provides strategic business development support for companies looking to grow their presence in the Dutch and European defence and security market. We help you identify opportunities, understand the competitive landscape, and develop a clear path to engagement.</p><h4>What we deliver:</h4><ul><li>Market analysis and opportunity identification</li><li>Go-to-market strategy for the Netherlands and Europe</li><li>Competitive landscape and positioning</li><li>Procurement pipeline monitoring</li><li>Strategic advisory on partnerships and teaming</li></ul>',
            // Service 2
            '_floru_svc2_label' => 'Service 02',
            '_floru_svc2_title' => 'Stakeholder Engagement',
            '_floru_svc2_desc'  => '<p>Defence procurement decisions involve multiple layers of stakeholders — military end-users, programme managers, political decision-makers, and procurement officials. Reaching the right people with the right message at the right time is critical.</p><p>Floru leverages its established network and institutional knowledge to connect our clients with the stakeholders who matter. We facilitate introductions, support relationship-building, and help our clients navigate complex organisational structures.</p><h4>What we deliver:</h4><ul><li>Stakeholder mapping and analysis</li><li>Introductions to key government and military contacts</li><li>Event and exhibition support</li><li>Communication and messaging strategy</li><li>Government relations advisory</li></ul>',
            // Service 3
            '_floru_svc3_label' => 'Service 03',
            '_floru_svc3_title' => 'Tender Support',
            '_floru_svc3_desc'  => '<p>Government procurement in defence and security is complex, time-sensitive, and highly competitive. A strong tender response requires not only technical excellence but also strategic positioning, clear communication, and full compliance with procurement requirements.</p><p>Floru supports clients throughout the tender process — from early identification and pre-qualification through to proposal development, pricing strategy, and post-submission negotiation. We bring in-depth knowledge of Dutch and European procurement practices.</p><h4>What we deliver:</h4><ul><li>Tender identification and tracking</li><li>Pre-qualification and compliance review</li><li>Proposal strategy and management</li><li>Win theme development</li><li>Post-submission support and debrief guidance</li></ul>',
            // CTA
            '_floru_pcta_heading'     => 'Let Us Help You Succeed',
            '_floru_pcta_description' => 'Every client and project is unique. Contact us to discuss how our services can be tailored to your objectives.',
            '_floru_pcta_btn_text'    => 'Get in Touch',
            '_floru_pcta_btn_url'     => home_url( '/contact/' ),
        );
        foreach ( $services_meta as $key => $value ) {
            if ( get_post_meta( $s, $key, true ) === '' ) {
                update_post_meta( $s, $key, $value );
            }
        }
    }

    /* ======================================================================
       CONTACT PAGE
       ====================================================================== */
    $contact = get_page_by_path( 'contact' );
    if ( $contact ) {
        $c = $contact->ID;
        $contact_meta = array(
            '_floru_ph_label'              => 'Contact',
            '_floru_ph_heading'            => 'Get in Touch',
            '_floru_ph_description'        => 'We welcome your enquiry and look forward to discussing how we can support your objectives.',
            '_floru_contact_heading'       => 'Contact Information',
            '_floru_contact_description'   => 'Reach out directly or use the form to start a conversation. We typically respond within one business day.',
            '_floru_contact_email'         => 'info@floru.nl',
            '_floru_contact_phone'         => '+31 (0) 00 000 0000',
            '_floru_contact_phone_raw'     => '+31000000000',
            '_floru_contact_office'        => 'The Netherlands',
        );
        foreach ( $contact_meta as $key => $value ) {
            if ( get_post_meta( $c, $key, true ) === '' ) {
                update_post_meta( $c, $key, $value );
            }
        }
    }

    /* ======================================================================
       TEAM PAGE
       ====================================================================== */
    $team_page = get_page_by_path( 'our-team' );
    if ( ! $team_page ) {
        $team_page = get_page_by_path( 'team' );
    }
    if ( $team_page ) {
        $t = $team_page->ID;

        // Set page content if currently empty
        $current_content = $team_page->post_content;
        if ( ! $current_content || ! trim( strip_tags( $current_content ) ) || strpos( $current_content, 'Content is rendered' ) !== false ) {
            wp_update_post( array(
                'ID'           => $t,
                'post_content' => '<p>Floru brings together a small, dedicated team of professionals with deep roots in the defence and security sector. Each of us has held positions within government, the armed forces, or the defence industry — and we draw on that combined experience to deliver results for our clients.</p>',
            ) );
        }

        $team_meta = array(
            '_floru_ph_label'         => 'Our People',
            '_floru_ph_heading'       => 'Meet the Team',
            '_floru_ph_description'   => 'Our strength lies in the experience, network, and commitment of our senior consultants.',
            '_floru_pcta_heading'     => 'Work With Our Team',
            '_floru_pcta_description' => 'We bring a personal, senior-level approach to every engagement. Get in touch to discuss your objectives.',
            '_floru_pcta_btn_text'    => 'Contact Us',
            '_floru_pcta_btn_url'     => home_url( '/contact/' ),
        );
        foreach ( $team_meta as $key => $value ) {
            if ( get_post_meta( $t, $key, true ) === '' ) {
                update_post_meta( $t, $key, $value );
            }
        }

        // Create team members if none exist yet.
        $existing_team = new WP_Query( array(
            'post_type'      => 'floru_team',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ) );
        if ( ! $existing_team->have_posts() ) {
            $team_members = array(
                array(
                    'title' => 'Ruud de Pruyssenaere de la Woestijne',
                    'role'  => 'Director FLORU Consultancy — Army, Brigadier General ret.',
                    'order' => 1,
                ),
                array(
                    'title' => 'Jaap Willemse',
                    'role'  => 'Air Force, Major General ret.',
                    'order' => 2,
                ),
                array(
                    'title' => 'Michael van der Klip',
                    'role'  => 'Navy, Commander ret.',
                    'order' => 3,
                ),
                array(
                    'title' => 'Jan Zeggelaar',
                    'role'  => 'Army, Major ret.',
                    'order' => 4,
                ),
                array(
                    'title' => 'Peter Lenselink',
                    'role'  => 'Navy, Rear Admiral LH ret.',
                    'order' => 5,
                ),
                array(
                    'title' => 'Michiel Hijmans',
                    'role'  => 'Navy, Rear Admiral LH ret.',
                    'order' => 6,
                ),
                array(
                    'title' => 'Marina Eppen-Pruyssenaere',
                    'role'  => 'Technical Support',
                    'order' => 7,
                ),
            );
            foreach ( $team_members as $member ) {
                $post_id = wp_insert_post( array(
                    'post_type'   => 'floru_team',
                    'post_title'  => $member['title'],
                    'post_status' => 'publish',
                    'menu_order'  => $member['order'],
                ) );
                if ( $post_id && ! is_wp_error( $post_id ) ) {
                    update_post_meta( $post_id, '_floru_team_role', $member['role'] );
                }
            }
        }
    }

    /* ======================================================================
       CLIENTS PAGE
       ====================================================================== */
    $clients_page = get_page_by_path( 'clients' );
    if ( $clients_page ) {
        $cl = $clients_page->ID;

        // Set page content if currently empty
        $current_content = $clients_page->post_content;
        if ( ! $current_content || ! trim( strip_tags( $current_content ) ) || strpos( $current_content, 'Content is rendered' ) !== false ) {
            wp_update_post( array(
                'ID'           => $cl,
                'post_content' => '<p>Over the years, Floru has supported a range of international defence and security companies — from established primes to innovative mid-tier manufacturers. Our client relationships are built on trust, discretion, and a shared commitment to achieving concrete results.</p>
<p>Below you will find a selection of the organisations we have had the privilege of working with.</p>',
            ) );
        }

        $clients_meta = array(
            '_floru_ph_label'         => 'Our Clients',
            '_floru_ph_heading'       => 'Clients & References',
            '_floru_ph_description'   => 'We are proud to work with leading organisations in the international defence and security industry.',
            '_floru_pcta_heading'     => 'Interested in Working With Us?',
            '_floru_pcta_description' => 'We would be happy to discuss our experience and how we can support your goals. References are available upon request.',
            '_floru_pcta_btn_text'    => 'Contact Us',
            '_floru_pcta_btn_url'     => home_url( '/contact/' ),
        );
        foreach ( $clients_meta as $key => $value ) {
            if ( get_post_meta( $cl, $key, true ) === '' ) {
                update_post_meta( $cl, $key, $value );
            }
        }
    }

    /* ======================================================================
       FOOTER OPTIONS (wp_options)
       ====================================================================== */
    if ( get_option( 'floru_footer_description' ) === false || get_option( 'floru_footer_description' ) === '' ) {
        update_option( 'floru_footer_description', 'Strategic consultancy in defence and security. We help international companies navigate complex government markets, build relationships, and win tenders.' );
    }
    if ( get_option( 'floru_footer_email' ) === false || get_option( 'floru_footer_email' ) === '' ) {
        update_option( 'floru_footer_email', 'info@floru.nl' );
    }
    if ( get_option( 'floru_footer_tagline' ) === false || get_option( 'floru_footer_tagline' ) === '' ) {
        update_option( 'floru_footer_tagline', 'Defence & Security Consultancy' );
    }

    /* ======================================================================
       MARK MIGRATION AS COMPLETE
       ====================================================================== */
    update_option( 'floru_content_migrated', true );
}
