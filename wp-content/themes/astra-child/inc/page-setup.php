<?php
/**
 * Floru Page Setup
 * Creates the required pages on first setup and keeps template assignments in sync.
 *
 * @package Astra-Child-Floru
 */

function floru_create_pages() {

    // Only run full creation once, but always ensure templates are assigned.
    $pages_created = get_option( 'floru_pages_created' );

    $pages = array(
        array(
            'title'    => 'Home',
            'slug'     => 'home',
            'template' => 'templates/template-home.php',
            'content'  => '<!-- Content is rendered by the page template -->',
        ),
        array(
            'title'    => 'About',
            'slug'     => 'about',
            'template' => 'templates/template-about.php',
            'content'  => '<!-- Content is rendered by the page template -->',
        ),
        array(
            'title'    => 'Services',
            'slug'     => 'services',
            'template' => 'templates/template-services.php',
            'content'  => '<!-- Content is rendered by the page template -->',
        ),
        array(
            'title'    => 'Our Team',
            'slug'     => 'our-team',
            'template' => 'templates/template-team.php',
            'content'  => '<!-- Content is rendered by the page template -->',
        ),
        array(
            'title'    => 'Clients',
            'slug'     => 'clients',
            'template' => 'templates/template-clients.php',
            'content'  => '<!-- Content is rendered by the page template -->',
        ),
        array(
            'title'    => 'Contact',
            'slug'     => 'contact',
            'template' => 'templates/template-contact.php',
            'content'  => '<!-- Content is rendered by the page template -->',
        ),
    );

    foreach ( $pages as $page_data ) {
        // Check if page already exists by slug
        $existing = get_page_by_path( $page_data['slug'] );

        // For the team page, also check old "team" slug
        if ( ! $existing && $page_data['slug'] === 'our-team' ) {
            $existing = get_page_by_path( 'team' );
        }

        if ( $existing ) {
            // Always ensure correct template is assigned
            update_post_meta( $existing->ID, '_wp_page_template', $page_data['template'] );
            continue;
        }

        if ( $pages_created ) {
            continue; // Don't create new pages if initial setup already ran
        }

        // Create the page
        $page_id = wp_insert_post( array(
            'post_title'   => $page_data['title'],
            'post_name'    => $page_data['slug'],
            'post_content' => $page_data['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            update_post_meta( $page_id, '_wp_page_template', $page_data['template'] );
        }
    }

    // Set "Home" page as the front page
    $home_page = get_page_by_path( 'home' );
    if ( $home_page ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_page->ID );
    }

    update_option( 'floru_pages_created', true );
}
add_action( 'after_switch_theme', 'floru_create_pages' );
add_action( 'init', 'floru_create_pages', 20 );
