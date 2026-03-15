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

    // Load the custom footer on Floru template pages, client detail pages and 404.
    if ( is_page_template( floru_get_template_slugs() ) || is_404() || is_singular( 'floru_client' ) ) {
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

<!-- Cookie Consent Banner -->
<div class="floru-cookie-consent" id="floraCookieConsent" role="dialog" aria-label="Cookie consent" style="display:none;">
    <div class="floru-cookie-consent__inner">
        <p>We use cookies to ensure you get the best experience on our website. By continuing to browse, you agree to our use of cookies.</p>
        <div class="floru-cookie-consent__actions">
            <button type="button" class="floru-btn floru-btn--primary floru-btn--sm" id="floraCookieAccept">Accept</button>
            <button type="button" class="floru-btn floru-btn--outline floru-btn--sm" id="floraCookieDecline">Decline</button>
        </div>
    </div>
</div>
<script>
(function(){
    var consent = localStorage.getItem('floru_cookie_consent');
    if (!consent) {
        document.getElementById('floraCookieConsent').style.display = '';
    }
    document.getElementById('floraCookieAccept').addEventListener('click', function() {
        localStorage.setItem('floru_cookie_consent', 'accepted');
        document.getElementById('floraCookieConsent').style.display = 'none';
    });
    document.getElementById('floraCookieDecline').addEventListener('click', function() {
        localStorage.setItem('floru_cookie_consent', 'declined');
        document.getElementById('floraCookieConsent').style.display = 'none';
    });
})();
</script>
    </body>
</html>
