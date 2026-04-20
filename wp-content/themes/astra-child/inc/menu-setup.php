
<?php
/**
 * Floru Menu Setup
 * Creates the primary navigation menu on first activation.
 *
 * @package Astra-Child-Floru
 */

function floru_create_menus() {

    if ( get_option( 'floru_menus_created' ) ) {
        return;
    }

    // Create navigation menu
    $menu_name = 'Floru Primary';
    $menu_exists = wp_get_nav_menu_object( $menu_name );

    if ( ! $menu_exists ) {
        $menu_id = wp_create_nav_menu( $menu_name );

        if ( is_wp_error( $menu_id ) ) {
            return;
        }

        $pages = array(
            'home'     => 'Home',
            'about'    => 'About',
            'services' => 'Services',
            'our-team' => 'Team',
            'clients'  => 'Clients',
            'contact'  => 'Contact',
        );

        $order = 1;
        foreach ( $pages as $slug => $title ) {
            $page = get_page_by_path( $slug );
            if ( $page ) {
                wp_update_nav_menu_item( $menu_id, 0, array(
                    'menu-item-title'     => $title,
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => $page->ID,
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                    'menu-item-position'  => $order,
                ) );
                $order++;
            }
        }
    } else {
        $menu_id = $menu_exists->term_id;
    }

    // Always assign menu to locations (even if menu already existed)
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $locations['primary']       = $menu_id;
    $locations['floru-primary'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );

    update_option( 'floru_menus_created', true );
}
add_action( 'after_switch_theme', 'floru_create_menus', 20 );
