<?php
/**
 * The template for displaying the footer.
 * Overrides parent Astra footer with Floru custom footer.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<?php astra_content_bottom(); ?>
    </div> <!-- ast-container -->
    </div><!-- #content -->
<?php
    astra_content_after();
    astra_footer_before();

    // Load custom footer on Floru template pages
    $floru_templates = array(
        'templates/template-home.php',
        'templates/template-about.php',
        'templates/template-services.php',
        'templates/template-team.php',
        'templates/template-clients.php',
        'templates/template-contact.php',
    );

    if ( is_page_template( $floru_templates ) ) {
        get_template_part( 'template-parts/footer', 'custom' );
    } else {
        astra_footer();
    }

    astra_footer_after();
?>
    </div><!-- #page -->
<?php
    astra_body_bottom();
    wp_footer();
?>
    </body>
</html>
