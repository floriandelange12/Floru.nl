<?php
/**
 * Floru frontend language handling.
 *
 * @package Astra-Child-Floru
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Supported frontend languages.
 *
 * @return array<string, array<string, string>>
 */
function floru_get_supported_languages() {
    return array(
        'en' => array(
            'label'     => 'English',
            'locale'    => 'en',
            'hreflang'  => 'en',
            'html_lang' => 'en',
        ),
        'nl' => array(
            'label'     => 'Nederlands',
            'locale'    => 'nl-NL',
            'hreflang'  => 'nl',
            'html_lang' => 'nl',
        ),
    );
}

/**
 * Validate a language code.
 *
 * @param string $language Language code.
 * @return string
 */
function floru_normalize_language( $language ) {
    $language  = strtolower( trim( (string) $language ) );
    $languages = floru_get_supported_languages();

    return isset( $languages[ $language ] ) ? $language : 'en';
}

/**
 * Current frontend language.
 *
 * @return string
 */
function floru_get_current_language() {
    static $language = null;

    if ( null !== $language ) {
        return $language;
    }

    if ( isset( $_GET['lang'] ) ) {
        $language = floru_normalize_language( wp_unslash( $_GET['lang'] ) );
        return $language;
    }

    if ( isset( $_COOKIE['floru_lang'] ) ) {
        $language = floru_normalize_language( wp_unslash( $_COOKIE['floru_lang'] ) );
        return $language;
    }

    $language = 'en';

    return $language;
}

/**
 * Current frontend locale for HTML metadata.
 *
 * @return string
 */
function floru_get_current_locale() {
    $languages = floru_get_supported_languages();
    $language  = floru_get_current_language();

    return isset( $languages[ $language ]['locale'] ) ? $languages[ $language ]['locale'] : 'en';
}

/**
 * Whether the current request should render frontend translations.
 *
 * @return bool
 */
function floru_is_public_request() {
    if ( is_admin() || wp_doing_ajax() ) {
        return false;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return false;
    }

    if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
        return false;
    }

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return false;
    }

    return true;
}

/**
 * Persist a language choice and redirect to a clean URL.
 */
add_action( 'template_redirect', 'floru_handle_language_switch', 0 );
function floru_handle_language_switch() {
    if ( ! floru_is_public_request() || ! isset( $_GET['lang'] ) ) {
        return;
    }

    $language = floru_normalize_language( wp_unslash( $_GET['lang'] ) );
    $expires  = time() + YEAR_IN_SECONDS;
    $path     = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
    $domain   = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';

    setcookie( 'floru_lang', $language, $expires, $path, $domain, is_ssl(), true );
    $_COOKIE['floru_lang'] = $language;

    $redirect_url = remove_query_arg( 'lang' );
    if ( $redirect_url ) {
        wp_safe_redirect( $redirect_url );
        exit;
    }
}

/**
 * Build the current request URL.
 *
 * @return string
 */
function floru_get_current_request_url() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';

    return home_url( $request_uri );
}

/**
 * Build a switch URL for the requested language.
 *
 * @param string $language Language code.
 * @param string $url      Base URL.
 * @return string
 */
function floru_get_language_switch_url( $language, $url = '' ) {
    $base_url = $url ? $url : floru_get_current_request_url();
    $base_url = remove_query_arg( 'lang', $base_url );

    return add_query_arg( 'lang', floru_normalize_language( $language ), $base_url );
}

/**
 * Frontend translation catalog.
 *
 * English source strings only need a Dutch variant. Dutch source strings only
 * need an English variant.
 *
 * @return array<string, array<string, string>>
 */
function floru_get_translation_catalog() {
    static $catalog = null;

    if ( null !== $catalog ) {
        return $catalog;
    }

    $about_content_en = <<<'HTML'
<p>Floru was founded to address a clear need in the European defence market: international companies entering or expanding in the Netherlands and broader European markets need a trusted local partner who understands both the business environment and the government landscape.</p>
<p>Our team brings together decades of experience at the intersection of defence, government affairs, and international business development. We have held senior positions within government, defence organisations, and industry — giving us a unique perspective that benefits our clients.</p>
<p>We work closely with our clients as an extension of their team. We do not believe in generic, arms-length advisory. Our approach is hands-on, results-oriented, and built on trust.</p>
HTML;

    $about_content_nl = <<<'HTML'
<p>Floru is opgericht om in te spelen op een duidelijke behoefte in de Europese defensiemarkt: internationale bedrijven die de Nederlandse en bredere Europese markt betreden of uitbreiden, hebben een betrouwbare lokale partner nodig die zowel de zakelijke context als het overheidslandschap begrijpt.</p>
<p>Ons team brengt tientallen jaren ervaring samen op het snijvlak van defensie, overheidsrelaties en internationale marktontwikkeling. Wij hebben senior functies bekleed binnen de overheid, defensieorganisaties en de industrie, wat onze klanten een uniek perspectief oplevert.</p>
<p>Wij werken nauw met onze klanten samen als verlengstuk van hun team. Wij geloven niet in generiek advies op afstand. Onze aanpak is praktisch, resultaatgericht en gebouwd op vertrouwen.</p>
HTML;

    $team_content_en = <<<'HTML'
<p>Floru brings together a small, dedicated team of professionals with deep roots in the defence and security sector. Each of us has held positions within government, the armed forces, or the defence industry — and we draw on that combined experience to deliver results for our clients.</p>
HTML;

    $team_content_nl = <<<'HTML'
<p>Floru brengt een klein, toegewijd team van professionals samen met diepe wortels in de defensie- en veiligheidssector. Ieder van ons heeft functies vervuld binnen de overheid, de krijgsmacht of de defensie-industrie, en juist die gecombineerde ervaring zetten wij in om resultaten voor onze klanten te realiseren.</p>
HTML;

    $service_1_en = '<p>Entering or expanding within European defence markets requires more than a strong product. It requires insight into political dynamics, procurement cycles, and institutional relationships.</p><p>Floru provides strategic business development support to companies seeking to strengthen their position in the Dutch and European defence and security market. We help you identify opportunities, understand the competitive landscape, and develop a clear route to engagement.</p><h4>What we deliver:</h4><ul><li>Market analysis and opportunity identification</li><li>Market strategy for the Netherlands and Europe</li><li>Competitive analysis and positioning</li><li>Procurement pipeline monitoring</li><li>Strategic advice on partnerships and cooperation</li></ul>';
    $service_1_nl = '<p>Toetreden tot of groeien binnen Europese defensiemarkten vraagt om meer dan een goed product. Het vraagt om inzicht in politieke dynamiek, aanbestedingscycli en institutionele verhoudingen.</p><p>Floru biedt strategische ondersteuning bij bedrijfsontwikkeling aan bedrijven die hun positie in de Nederlandse en Europese defensie- en veiligheidsmarkt willen versterken. Wij helpen u kansen te identificeren, het concurrentielandschap te begrijpen en een heldere route naar engagement te ontwikkelen.</p><h4>Wat wij leveren:</h4><ul><li>Marktanalyse en identificatie van kansen</li><li>Marktstrategie voor Nederland en Europa</li><li>Concurrentieanalyse en positionering</li><li>Monitoring van aanbestedingspijplijnen</li><li>Strategisch advies over partnerschappen en samenwerking</li></ul>';

    $service_2_en = '<p>Defence procurement decisions involve multiple layers of stakeholders — military end-users, programme managers, political decision-makers, and procurement officials. Reaching the right people with the right message at the right time is critical.</p><p>Floru leverages its established network and institutional knowledge to connect our clients with the stakeholders who matter. We facilitate introductions, support relationship-building, and help our clients navigate complex organisational structures.</p><h4>What we deliver:</h4><ul><li>Stakeholder mapping and analysis</li><li>Introductions to key government and military contacts</li><li>Event and exhibition support</li><li>Communication and messaging strategy</li><li>Government relations advisory</li></ul>';
    $service_2_nl = '<p>Beslissingen over defensieaanbestedingen kennen meerdere lagen van stakeholders: militaire eindgebruikers, programmamanagers, politieke besluitvormers en inkoopfunctionarissen. De juiste mensen bereiken met de juiste boodschap op het juiste moment is cruciaal.</p><p>Floru benut zijn opgebouwde netwerk en institutionele kennis om onze klanten te verbinden met de stakeholders die ertoe doen. Wij faciliteren introducties, ondersteunen relatieopbouw en helpen onze klanten door complexe organisatiestructuren te navigeren.</p><h4>Wat wij leveren:</h4><ul><li>Stakeholdermapping en analyse</li><li>Introducties bij sleutelcontacten binnen overheid en krijgsmacht</li><li>Ondersteuning bij events en beurzen</li><li>Communicatie- en boodschapstrategie</li><li>Advies over overheidsrelaties</li></ul>';

    $service_3_en = '<p>Government procurement in defence and security is complex, time-critical, and highly competitive. A strong tender response requires not only technical quality, but also strategic positioning, clear communication, and full compliance with procurement requirements.</p><p>Floru supports clients throughout the tender process: from early identification and pre-qualification to proposal development, pricing strategy, and post-submission support. We bring deep knowledge of Dutch and European procurement practices.</p><h4>What we deliver:</h4><ul><li>Tender identification and tracking</li><li>Pre-qualification and compliance review</li><li>Bid strategy and management</li><li>Development of win themes</li><li>Post-submission support and debrief guidance</li></ul>';
    $service_3_nl = '<p>Overheidsinkoop in defensie en veiligheid is complex, tijdkritisch en sterk concurrerend. Een krachtige tenderrespons vraagt niet alleen om technische kwaliteit, maar ook om strategische positionering, heldere communicatie en volledige naleving van aanbestedingseisen.</p><p>Floru ondersteunt klanten gedurende het hele tenderproces: van vroege signalering en prekwalificatie tot uitwerking van de inschrijving, prijsstrategie en ondersteuning na indiening. Wij brengen diepgaande kennis mee van Nederlandse en Europese aanbestedingspraktijken.</p><h4>Wat wij leveren:</h4><ul><li>Signalering en opvolging van tenders</li><li>Prekwalificatie en compliancereview</li><li>Inschrijfstrategie en -management</li><li>Ontwikkeling van winthema\'s</li><li>Ondersteuning na indiening en begeleiding bij debriefs</li></ul>';

    $clients_content_en = <<<'HTML'
<p>Over the years, Floru has supported a range of international defence and security companies — from established primes to innovative mid-tier manufacturers. Our client relationships are built on trust, discretion, and a shared commitment to achieving concrete results.</p>
<p>Below you will find a selection of the organisations we have had the privilege of working with.</p>
HTML;

    $clients_content_nl = <<<'HTML'
<p>Door de jaren heen heeft Floru een breed scala aan internationale defensie- en veiligheidsbedrijven ondersteund, van gevestigde hoofdaannemers tot innovatieve middelgrote fabrikanten. Onze klantrelaties zijn gebouwd op vertrouwen, discretie en een gedeelde inzet voor concrete resultaten.</p>
<p>Hieronder vindt u een selectie van de organisaties waarmee wij het voorrecht hebben gehad samen te werken.</p>
HTML;

    $catalog = array(
        'Skip to content' => array( 'nl' => 'Ga naar inhoud' ),
        'Cookie consent' => array( 'nl' => 'Cookietoestemming' ),
        'We use cookies to ensure you get the best experience on our website. By continuing to browse, you agree to our use of cookies.' => array( 'nl' => 'We gebruiken cookies om ervoor te zorgen dat u de beste ervaring op onze website krijgt. Door verder te browsen gaat u akkoord met ons gebruik van cookies.' ),
        'Accept' => array( 'nl' => 'Accepteren' ),
        'Decline' => array( 'nl' => 'Weigeren' ),
        'About' => array( 'nl' => 'Over Floru' ),
        'Services' => array( 'nl' => 'Diensten' ),
        'Clients' => array( 'nl' => 'Opdrachtgevers' ),
        'Company' => array( 'nl' => 'Bedrijf' ),
        'About Us' => array( 'nl' => 'Over ons' ),
        'Language' => array( 'nl' => 'Taal' ),
        'Strategic consultancy in defence and security. We help international companies navigate complex government markets, build relationships, and win tenders.' => array( 'nl' => 'Strategisch advies in defensie en veiligheid. Wij helpen internationale bedrijven om complexe overheidsmarkten te navigeren, relaties op te bouwen en tenders te winnen.' ),
        'Strategic consultancy in defence and security. Floru helps international companies navigate government markets, build stakeholder relationships, and win tenders in the Netherlands and Europe.' => array( 'nl' => 'Strategisch advies in defensie en veiligheid. Floru helpt internationale bedrijven om overheidsmarkten te navigeren, stakeholderrelaties op te bouwen en tenders te winnen in Nederland en Europa.' ),
        'Trusted conversations' => array( 'nl' => 'Vertrouwde gesprekken' ),
        'Senior strategic guidance for discreet defence and security engagements, from market entry to stakeholder positioning.' => array( 'nl' => 'Senior strategische begeleiding voor discrete defensie- en veiligheidstrajecten, van markttoetreding tot stakeholderpositionering.' ),
        'Business Development' => array( 'nl' => 'Bedrijfsontwikkeling' ),
        'Stakeholder Engagement' => array( 'nl' => 'Stakeholdermanagement' ),
        'Tender Support' => array( 'nl' => 'Tenderondersteuning' ),
        'Email' => array( 'nl' => 'E-mail' ),
        'Phone' => array( 'nl' => 'Telefoon' ),
        'Office' => array( 'nl' => 'Kantoor' ),
        'Response within one business day. Dutch and international engagements handled discreetly.' => array( 'nl' => 'Reactie binnen één werkdag. Nederlandse en internationale trajecten worden discreet behandeld.' ),
        'Start a conversation' => array( 'nl' => 'Start een gesprek' ),
        'All rights reserved.' => array( 'nl' => 'Alle rechten voorbehouden.' ),
        'Scroll to Top' => array( 'nl' => 'Terug naar boven' ),
        'Main menu toggle' => array( 'nl' => 'Hoofdmenu openen' ),
        'Menu Toggle' => array( 'nl' => 'Menu openen' ),
        'Defence & Security Consultancy' => array( 'nl' => 'Defensie- en veiligheidsconsultancy' ),
        'Strategic Guidance for Defence and Security Markets' => array( 'nl' => 'Strategische begeleiding voor defensie- en veiligheidsmarkten' ),
        'We help international defence and security companies navigate government markets, build decisive stakeholder relationships, and win critical tenders across the Netherlands and Europe.' => array( 'nl' => 'Wij helpen internationale defensie- en veiligheidsbedrijven om overheidsmarkten te navigeren, beslissende stakeholderrelaties op te bouwen en cruciale tenders te winnen in Nederland en Europa.' ),
        'Our Approach' => array( 'nl' => 'Onze aanpak' ),
        'Get in Touch' => array( 'nl' => 'Neem contact op' ),
        'Who We Are' => array( 'nl' => 'Wie wij zijn' ),
        'A Trusted Partner in Defence Business Development' => array( 'nl' => 'Een betrouwbare partner in defensiegerichte bedrijfsontwikkeling' ),
        'Floru is a specialised consultancy supporting international defence, security, and high-technology companies entering or expanding in the Dutch and European markets.' => array( 'nl' => 'Floru is een gespecialiseerd adviesbureau dat internationale defensie-, veiligheids- en hightechbedrijven ondersteunt bij toetreding tot en uitbreiding in de Nederlandse en Europese markt.' ),
        'With decades of experience at the intersection of government, industry, and procurement, we provide the strategic insight and practical support our clients need to succeed.' => array( 'nl' => 'Met tientallen jaren ervaring op het snijvlak van overheid, industrie en aanbestedingen bieden wij het strategische inzicht en de praktische ondersteuning die onze klanten nodig hebben om succesvol te zijn.' ),
        'Learn More About Us' => array( 'nl' => 'Meer over ons' ),
        'European institutional alignment' => array( 'nl' => 'Europese institutionele afstemming' ),
        'Where Floru adds value' => array( 'nl' => 'Waar Floru waarde toevoegt' ),
        'Strategic support shaped for institutional buying cycles.' => array( 'nl' => 'Strategische ondersteuning, gevormd rond institutionele inkoopcycli.' ),
        'Core support themes' => array( 'nl' => 'Kerngebieden van ondersteuning' ),
        'Market-entry direction' => array( 'nl' => 'Richting voor markttoetreding' ),
        'Stakeholder positioning' => array( 'nl' => 'Stakeholderpositionering' ),
        'Tender discipline' => array( 'nl' => 'Tenderdiscipline' ),
        'Small senior team. Direct involvement where decision quality matters most.' => array( 'nl' => 'Klein senior team. Directe betrokkenheid waar de kwaliteit van besluitvorming het meest telt.' ),
        'Years of Experience' => array( 'nl' => 'Jaar ervaring' ),
        'Completed Projects' => array( 'nl' => 'Afgeronde projecten' ),
        'International Clients' => array( 'nl' => 'Internationale cliënten' ),
        'What We Do' => array( 'nl' => 'Wat wij doen' ),
        'Three Pillars of Support' => array( 'nl' => 'Drie pijlers van ondersteuning' ),
        'Strategic advisory, relationship management, and hands-on tender expertise — combined to give our clients a decisive edge.' => array( 'nl' => 'Strategisch advies, relatiemanagement en praktische tenderexpertise, gecombineerd om onze klanten een beslissende voorsprong te geven.' ),
        'Market opportunity identification, procurement pipeline mapping, and go-to-market strategies tailored to the European defence landscape.' => array( 'nl' => 'Identificatie van marktkansen, inzicht in aanbestedingspijplijnen en marktstrategieën afgestemd op het Europese defensielandschap.' ),
        'Connecting our clients with the right decision-makers across government, military, and industry — with the right message at the right time.' => array( 'nl' => 'Onze klanten verbinden met de juiste beslissers binnen overheid, krijgsmacht en industrie, met de juiste boodschap op het juiste moment.' ),
        'End-to-end tender lifecycle guidance — from positioning and pre-qualification through to proposal development and contract award.' => array( 'nl' => 'Begeleiding over de volledige tenderlevenscyclus, van positionering en prekwalificatie tot uitwerking van de inschrijving en contractgunning.' ),
        'Learn more' => array( 'nl' => 'Lees meer' ),
        'Why Floru' => array( 'nl' => 'Waarom Floru' ),
        'Built on Experience,<br>Driven by Results' => array( 'nl' => 'Gebouwd op ervaring,<br>gedreven door resultaat' ),
        'We are not a large agency. We are a focused team of senior professionals with deep domain expertise and a strong track record in defence and security.' => array( 'nl' => 'Wij zijn geen groot bureau. Wij zijn een scherp gefocust team van senior adviseurs met diepe domeinkennis en een bewezen staat van dienst in defensie en veiligheid.' ),
        'Defence Domain Expertise' => array( 'nl' => 'Diepe defensie-expertise' ),
        'Hands-on knowledge of defence procurement, government structures, and the political context of European security markets.' => array( 'nl' => 'Praktische kennis van defensieaanbestedingen, overheidsstructuren en de politieke context van Europese veiligheidsmarkten.' ),
        'International Reach' => array( 'nl' => 'Internationaal bereik' ),
        'Bridging international manufacturers and European end-users, understanding both sides of the conversation.' => array( 'nl' => 'Wij overbruggen internationale fabrikanten en Europese eindgebruikers en begrijpen beide kanten van het gesprek.' ),
        'Strategic Focus' => array( 'nl' => 'Strategische focus' ),
        'Every engagement starts with a clear objective. We invest time where it matters most and avoid unnecessary complexity.' => array( 'nl' => 'Elke samenwerking begint met een helder doel. Wij investeren tijd waar die het meeste verschil maakt en vermijden onnodige complexiteit.' ),
        'Proven Track Record' => array( 'nl' => 'Bewezen staat van dienst' ),
        'Award-winning tenders and successful market entries for defence companies in the Netherlands and beyond.' => array( 'nl' => 'Prijswinnende tenders en succesvolle markttoetredingen voor defensiebedrijven in Nederland en daarbuiten.' ),
        'Our Team' => array( 'nl' => 'Ons team' ),
        'Senior Professionals, Personal Approach' => array( 'nl' => 'Senior adviseurs, persoonlijke aanpak' ),
        'Our consultants bring decades of experience in defence, government, international business, and procurement.' => array( 'nl' => 'Onze consultants brengen tientallen jaren ervaring mee in defensie, overheid, internationale markten en aanbestedingen.' ),
        'Meet the Full Team' => array( 'nl' => 'Bekijk het volledige team' ),
        'Trusted by international defence companies including' => array( 'nl' => 'Vertrouwd door internationale defensiebedrijven, waaronder' ),
        'Ready to Discuss Your Next Opportunity?' => array( 'nl' => 'Klaar om uw volgende kans te bespreken?' ),
        'Whether you are exploring market entry, preparing for a tender, or seeking strategic support — we welcome the conversation.' => array( 'nl' => 'Of u nu markttoetreding verkent, een tender voorbereidt of strategische ondersteuning zoekt, wij gaan graag het gesprek aan.' ),
        'Contact Us' => array( 'nl' => 'Contact opnemen' ),
        'Consultant' => array( 'nl' => 'Consultant' ),
        'Team Member' => array( 'nl' => 'Teamlid' ),
        'Add team members in WordPress admin under Team Members.' => array( 'nl' => 'Voeg teamleden toe in WordPress onder Teamleden.' ),
        'About Floru' => array( 'nl' => 'Over Floru' ),
        'Our Modus Operandi' => array( 'nl' => 'Onze werkwijze' ),
        'We combine strategic advisory with hands-on support to help defence and security companies achieve measurable results.' => array( 'nl' => 'Wij combineren strategisch advies met praktische ondersteuning om defensie- en veiligheidsbedrijven meetbare resultaten te helpen behalen.' ),
        'Our Story' => array( 'nl' => 'Ons verhaal' ),
        'Founded on Experience, Focused on Outcomes' => array( 'nl' => 'Gebaseerd op ervaring, gericht op resultaat' ),
        $about_content_en => array( 'nl' => $about_content_nl ),
        'Floru consultancy approach' => array( 'nl' => 'Aanpak van Floru Consultancy' ),
        'Executive boardroom meeting' => array( 'nl' => 'Bestuursvergadering' ),
        'Senior defence network' => array( 'nl' => 'Senior defensienetwerk' ),
        'Government literacy' => array( 'nl' => 'Inzicht in de overheid' ),
        'Commercial judgment' => array( 'nl' => 'Commercieel beoordelingsvermogen' ),
        'Built for high-trust market work.' => array( 'nl' => 'Gebouwd voor marktwerk waarin vertrouwen centraal staat.' ),
        'Foundational strengths' => array( 'nl' => 'Fundamentele sterktes' ),
        'Working style' => array( 'nl' => 'Werkwijze' ),
        'Senior advisory, Dutch institutional fluency, and commercial judgment for long-cycle defence engagements.' => array( 'nl' => 'Senior advies, Nederlandse institutionele scherpte en commercieel oordeel voor defensietrajecten met een lange doorlooptijd.' ),
        'How We Work' => array( 'nl' => 'Hoe wij werken' ),
        'A Structured Approach to Every Engagement' => array( 'nl' => 'Een gestructureerde aanpak voor elke samenwerking' ),
        'Our methodology is designed to maximise clarity, efficiency, and results at every stage.' => array( 'nl' => 'Onze methodiek is ontworpen om in elke fase maximale helderheid, efficiëntie en resultaat te realiseren.' ),
        'Understand & Assess' => array( 'nl' => 'Begrijpen en beoordelen' ),
        'We start by thoroughly understanding your company, your product or capability, and your strategic objectives. We assess the market landscape, procurement environment, and competitive dynamics.' => array( 'nl' => 'Wij starten met een grondig begrip van uw organisatie, uw product of capaciteiten en uw strategische doelstellingen. Daarna beoordelen wij het marktlandschap, de aanbestedingsomgeving en de concurrentiedynamiek.' ),
        'Develop Strategy' => array( 'nl' => 'Strategie ontwikkelen' ),
        'We formulate a clear, actionable strategy for market entry, growth, or tender response — including stakeholder mapping, positioning, and timeline planning.' => array( 'nl' => 'Wij formuleren een heldere, uitvoerbare strategie voor markttoetreding, groei of tenderrespons, inclusief stakeholdermapping, positionering en tijdlijnplanning.' ),
        'Engage & Position' => array( 'nl' => 'Engageren en positioneren' ),
        'We facilitate introductions to key decision-makers, support your messaging and positioning, and help establish your presence in relevant networks and fora.' => array( 'nl' => 'Wij faciliteren introducties bij belangrijke beslissers, ondersteunen uw boodschap en positionering en helpen uw aanwezigheid in relevante netwerken en fora op te bouwen.' ),
        'Execute & Support' => array( 'nl' => 'Uitvoeren en ondersteunen' ),
        'Whether it is a tender submission, a partnership development, or a long-term market presence — we work alongside you through execution, providing practical and strategic support.' => array( 'nl' => 'Of het nu gaat om een tenderindiening, de ontwikkeling van partnerschappen of een langdurige marktpositie: wij werken tijdens de uitvoering naast u en bieden praktische en strategische ondersteuning.' ),
        'Review & Adapt' => array( 'nl' => 'Evalueren en bijsturen' ),
        'After every engagement phase, we review progress, refine the approach, and ensure continued alignment with your business objectives.' => array( 'nl' => 'Na iedere fase evalueren wij de voortgang, scherpen wij de aanpak aan en borgen wij blijvende aansluiting op uw bedrijfsdoelstellingen.' ),
        'What Guides Us' => array( 'nl' => 'Wat ons leidt' ),
        'Our Core Values' => array( 'nl' => 'Onze kernwaarden' ),
        'Integrity' => array( 'nl' => 'Integriteit' ),
        'We maintain the highest ethical standards. Our clients trust us because we are straightforward, discreet, and reliable.' => array( 'nl' => 'Wij hanteren de hoogste ethische standaarden. Onze klanten vertrouwen ons omdat wij open, discreet en betrouwbaar zijn.' ),
        'Results-Oriented' => array( 'nl' => 'Resultaatgericht' ),
        'We measure our success by our clients\' outcomes. Every action we take is directed towards clear, tangible results.' => array( 'nl' => 'Wij meten ons succes aan de resultaten van onze klanten. Elke stap die wij zetten is gericht op heldere, tastbare uitkomsten.' ),
        'Partnership' => array( 'nl' => 'Partnerschap' ),
        'We work as part of your team — not as outsiders. Our engagements are collaborative, trust-based, and long-term.' => array( 'nl' => 'Wij werken als onderdeel van uw team, niet als buitenstaander. Onze trajecten zijn samenwerkingsgericht, gebaseerd op vertrouwen en gericht op de lange termijn.' ),
        'Expertise' => array( 'nl' => 'Expertise' ),
        'Our deep sector knowledge, network, and market understanding set us apart in the defence and security domain.' => array( 'nl' => 'Onze diepe sectorkennis, ons netwerk en ons marktinzicht onderscheiden ons in het defensie- en veiligheidsdomein.' ),
        'Interested in Working Together?' => array( 'nl' => 'Interesse om samen te werken?' ),
        'We welcome the opportunity to discuss how we can support your objectives in the European defence market.' => array( 'nl' => 'Wij bespreken graag hoe wij uw doelstellingen in de Europese defensiemarkt kunnen ondersteunen.' ),
        'Our Services' => array( 'nl' => 'Onze diensten' ),
        'How We Support Your Success' => array( 'nl' => 'Hoe wij uw succes ondersteunen' ),
        'Comprehensive strategic advisory, stakeholder management, and tender support tailored to the defence and security sector.' => array( 'nl' => 'Integrale strategische advisering, stakeholdermanagement en tenderondersteuning, toegespitst op de defensie- en veiligheidssector.' ),
        'Service 01' => array( 'nl' => 'Dienst 01' ),
        'Service 02' => array( 'nl' => 'Dienst 02' ),
        'Service 03' => array( 'nl' => 'Dienst 03' ),
        'Service' => array( 'nl' => 'Dienst' ),
        $service_1_en => array( 'nl' => $service_1_nl ),
        $service_2_en => array( 'nl' => $service_2_nl ),
        $service_3_en => array( 'nl' => $service_3_nl ),
        'Strategic note' => array( 'nl' => 'Strategische notitie' ),
        'Clarify where to commit before resources are spread too thin.' => array( 'nl' => 'Maak scherp waar u zich committeert voordat middelen te dun worden uitgesmeerd.' ),
        'Market-entry map' => array( 'nl' => 'Markttoetredingskaart' ),
        'Focus' => array( 'nl' => 'Focus' ),
        'Market-entry sequencing' => array( 'nl' => 'Fasering van markttoetreding' ),
        'Typical support' => array( 'nl' => 'Typische ondersteuning' ),
        'Targeting, positioning, pipeline review' => array( 'nl' => 'Doelbepaling, positionering en beoordeling van de pijplijn' ),
        'Senior advisory, selective execution' => array( 'nl' => 'Senior advies, selectieve uitvoering' ),
        'Map institutions, shape the message, and build the right cadence with the right people.' => array( 'nl' => 'Breng instituties in kaart, scherp de boodschap aan en bouw met de juiste mensen het juiste ritme op.' ),
        'Stakeholder field' => array( 'nl' => 'Stakeholderveld' ),
        'Institutional mapping' => array( 'nl' => 'Institutionele mapping' ),
        'Introductions, narrative calibration, stakeholder cadence' => array( 'nl' => 'Introducties, aanscherping van het verhaal en stakeholdercadans' ),
        'Senior-led, discreet, relationship-based' => array( 'nl' => 'Senior geleid, discreet en relatiegedreven' ),
        'Strengthen bid positioning early so submissions are sharper and more defensible under pressure.' => array( 'nl' => 'Versterk biedingspositionering vroegtijdig zodat inzendingen scherper en beter verdedigbaar zijn onder druk.' ),
        'Tender structure' => array( 'nl' => 'Tenderstructuur' ),
        'Bid positioning and discipline' => array( 'nl' => 'Inschrijfpositionering en discipline' ),
        'Pre-qualification, win themes, submission review' => array( 'nl' => 'Prekwalificatie, winthema\'s en review van inschrijvingen' ),
        'Deadline-driven and collaborative' => array( 'nl' => 'Deadlinegedreven en samenwerkend' ),
        '%s support profile' => array( 'nl' => 'Ondersteuningsprofiel voor %s' ),
        'Let Us Help You Succeed' => array( 'nl' => 'Laat ons u helpen slagen' ),
        'Every client and project is unique. Contact us to discuss how our services can be tailored to your objectives.' => array( 'nl' => 'Elke klant en elk project is uniek. Neem contact op om te bespreken hoe onze diensten op uw doelstellingen kunnen worden afgestemd.' ),
        'Our People' => array( 'nl' => 'Onze mensen' ),
        'Meet the Team' => array( 'nl' => 'Maak kennis met het team' ),
        'Our strength lies in the experience, network, and commitment of our senior consultants.' => array( 'nl' => 'Onze kracht ligt in de ervaring, het netwerk en de toewijding van onze senior consultants.' ),
        $team_content_en => array( 'nl' => $team_content_nl ),
        'Senior roster' => array( 'nl' => 'Senior team' ),
        'Leadership, networks, and sector knowledge in one clear team view' => array( 'nl' => 'Leiderschap, netwerken en sectorkennis in één helder teamoverzicht' ),
        'A single, premium roster of Floru consultants with direct experience across defence, government, and industry.' => array( 'nl' => 'Eén scherp en premium overzicht van Floru-consultants met directe ervaring in defensie, overheid en industrie.' ),
        'Portret van %s' => array( 'en' => 'Portrait of %s' ),
        'Portret volgt' => array( 'en' => 'Portrait pending' ),
        '%s op LinkedIn (opent in nieuw venster)' => array( 'en' => '%s on LinkedIn (opens in new window)' ),
        'LinkedIn profile' => array( 'nl' => 'LinkedIn-profiel' ),
        'Bekijk profiel van %s' => array( 'en' => 'View %s profile' ),
        'Profiel op LinkedIn' => array( 'en' => 'LinkedIn profile' ),
        'Bekijk profiel' => array( 'en' => 'View profile' ),
        'No team members have been added yet. Go to <strong>Team Members</strong> in the WordPress admin to add them.' => array( 'nl' => 'Er zijn nog geen teamleden toegevoegd. Ga in WordPress naar <strong>Teamleden</strong> om ze toe te voegen.' ),
        'Work With Our Team' => array( 'nl' => 'Werk samen met ons team' ),
        'We bring a personal, senior-level approach to every engagement. Get in touch to discuss your objectives.' => array( 'nl' => 'Wij brengen in iedere samenwerking een persoonlijke aanpak op senior niveau. Neem contact op om uw doelstellingen te bespreken.' ),
        'Our Clients' => array( 'nl' => 'Onze opdrachtgevers' ),
        'Clients & References' => array( 'nl' => 'Opdrachtgevers en referenties' ),
        'We are proud to work with leading organisations in the international defence and security industry.' => array( 'nl' => 'Wij werken met trots samen met toonaangevende organisaties in de internationale defensie- en veiligheidsindustrie.' ),
        $clients_content_en => array( 'nl' => $clients_content_nl ),
        'How Floru is engaged' => array( 'nl' => 'Hoe Floru wordt ingezet' ),
        'Support ranges from market-entry and stakeholder positioning to tender preparation, local coordination, and selective long-term advisory mandates.' => array( 'nl' => 'Ondersteuning loopt uiteen van markttoetreding en stakeholderpositionering tot tendervoorbereiding, lokale coördinatie en selectieve langlopende adviesmandaten.' ),
        'Client overview' => array( 'nl' => 'Cliëntoverzicht' ),
        'Profiled organisations' => array( 'nl' => 'Geprofileerde organisaties' ),
        'Industry segments' => array( 'nl' => 'Industriesegmenten' ),
        'Discreet, long-term client relationships built around trust, continuity, and selective advisory mandates.' => array( 'nl' => 'Discrete, langlopende klantrelaties gebouwd op vertrouwen, continuïteit en selectieve adviesmandaten.' ),
        'Active across' => array( 'nl' => 'Actief in' ),
        'Client sectors' => array( 'nl' => 'Cliëntsectoren' ),
        'Defence & Military Engineering' => array( 'nl' => 'Defensie- en militaire engineering' ),
        'Physical Security & Asset Protection' => array( 'nl' => 'Fysieke beveiliging en asset protection' ),
        'Defence Sensor Systems' => array( 'nl' => 'Defensiesensorsystemen' ),
        'Global Security Solutions' => array( 'nl' => 'Wereldwijde beveiligingsoplossingen' ),
        'Critical Event Management' => array( 'nl' => 'Critical event management' ),
        'Security Consulting & Risk Management' => array( 'nl' => 'Beveiligingsadvies en risicomanagement' ),
        'Defence & Aerospace' => array( 'nl' => 'Defensie en luchtvaart' ),
        'Defence Talent Solutions' => array( 'nl' => 'Defensietalentoplossingen' ),
        'Defence Equipment & Distribution' => array( 'nl' => 'Defensie-uitrusting en distributie' ),
        'Secure Communications & Cyber Security' => array( 'nl' => 'Veilige communicatie en cyberbeveiliging' ),
        'Selected organisations' => array( 'nl' => 'Geselecteerde organisaties' ),
        'Trusted by specialist defence and security organisations' => array( 'nl' => 'Vertrouwd door gespecialiseerde defensie- en veiligheidsorganisaties' ),
        'A representative selection of companies Floru has supported across stakeholder engagement, market positioning, and tender preparation.' => array( 'nl' => 'Een representatieve selectie van bedrijven die Floru heeft ondersteund op het gebied van stakeholdermanagement, marktpositionering en tendervoorbereiding.' ),
        'Profiled references' => array( 'nl' => 'Uitgelichte referenties' ),
        'Where Floru has supported positioning, growth, and engagement' => array( 'nl' => 'Waar Floru heeft ondersteund bij positionering, groei en engagement' ),
        'Each profile gives a short view of the organisations Floru has supported and the strategic context in which those relationships sit.' => array( 'nl' => 'Elk profiel geeft een korte indruk van de organisaties die Floru heeft ondersteund en van de strategische context van die samenwerking.' ),
        'View details' => array( 'nl' => 'Bekijk details' ),
        'No clients have been added yet. Go to <strong>Clients</strong> in the WordPress admin to add them.' => array( 'nl' => 'Er zijn nog geen opdrachtgevers toegevoegd. Ga in WordPress naar <strong>Opdrachtgevers</strong> om ze toe te voegen.' ),
        'We would be happy to discuss our experience and how we can support your goals. References are available upon request.' => array( 'nl' => 'Wij bespreken graag onze ervaring en hoe wij uw doelstellingen kunnen ondersteunen. Referenties zijn op aanvraag beschikbaar.' ),
        'We welcome your enquiry and look forward to discussing how we can support your objectives.' => array( 'nl' => 'Wij verwelkomen uw aanvraag en bespreken graag hoe wij uw doelstellingen kunnen ondersteunen.' ),
        'Contact Information' => array( 'nl' => 'Contactinformatie' ),
        'Reach out directly or use the form to start a conversation. We typically respond within one business day.' => array( 'nl' => 'Neem direct contact op of gebruik het formulier om een gesprek te starten. Meestal reageren wij binnen één werkdag.' ),
        'Contact signals' => array( 'nl' => 'Contactsignalen' ),
        'Response within one business day' => array( 'nl' => 'Reactie binnen één werkdag' ),
        'Dutch, English, and German' => array( 'nl' => 'Nederlands, Engels en Duits' ),
        'Discreet by default' => array( 'nl' => 'Standaard discreet' ),
        'Direct and discreet' => array( 'nl' => 'Direct en discreet' ),
        'We keep our first response concise, senior, and focused on whether Floru is the right fit for your brief.' => array( 'nl' => 'Onze eerste reactie is kort, senior en gericht op de vraag of Floru de juiste match is voor uw briefing.' ),
        'First conversation' => array( 'nl' => 'Eerste gesprek' ),
        'Initial fit.' => array( 'nl' => 'Eerste fit.' ),
        'We assess the scope, timing, and decision context behind your brief.' => array( 'nl' => 'Wij beoordelen de scope, timing en besluitvormingscontext achter uw briefing.' ),
        'Brief review.' => array( 'nl' => 'Beoordeling van de briefing.' ),
        'We identify where Floru adds value and where a lighter route is more appropriate.' => array( 'nl' => 'Wij bepalen waar Floru waarde toevoegt en waar een lichtere route passender is.' ),
        'Next step.' => array( 'nl' => 'Volgende stap.' ),
        'We respond with a pragmatic direction, typically within one business day.' => array( 'nl' => 'Wij reageren met een pragmatische richting, doorgaans binnen één werkdag.' ),
        'Thank you!' => array( 'nl' => 'Dank u wel!' ),
        'Your message has been sent successfully. We will get back to you within one business day.' => array( 'nl' => 'Uw bericht is succesvol verzonden. Wij nemen binnen één werkdag contact met u op.' ),
        'Start the conversation' => array( 'nl' => 'Start het gesprek' ),
        'Send Us a Message' => array( 'nl' => 'Stuur ons een bericht' ),
        'Share a short outline of your objective, tender, or market-entry question and we will route it to the right person.' => array( 'nl' => 'Deel kort uw doelstelling, tender of vraag rond markttoetreding en wij zetten dit door naar de juiste persoon.' ),
        'Please review the highlighted fields and try again.' => array( 'nl' => 'Controleer de gemarkeerde velden en probeer het opnieuw.' ),
        'Fields marked with * are required.' => array( 'nl' => 'Velden gemarkeerd met * zijn verplicht.' ),
        'Full Name' => array( 'nl' => 'Volledige naam' ),
        'Your name' => array( 'nl' => 'Uw naam' ),
        'Email Address' => array( 'nl' => 'E-mailadres' ),
        'Phone Number' => array( 'nl' => 'Telefoonnummer' ),
        'Subject' => array( 'nl' => 'Onderwerp' ),
        'How can we help?' => array( 'nl' => 'Hoe kunnen wij helpen?' ),
        'Message' => array( 'nl' => 'Bericht' ),
        'Tell us about your project or question...' => array( 'nl' => 'Vertel ons over uw project of vraag...' ),
        'Send Message' => array( 'nl' => 'Bericht versturen' ),
        'Your information is secure and will not be shared with third parties.' => array( 'nl' => 'Uw gegevens zijn veilig en worden niet met derden gedeeld.' ),
        'Prefer a Direct Conversation?' => array( 'nl' => 'Liever direct contact?' ),
        'Our team is available by phone during business hours. We speak Dutch, English, and German.' => array( 'nl' => 'Ons team is telefonisch bereikbaar tijdens kantooruren. Wij spreken Nederlands, Engels en Duits.' ),
        'Call Us Now' => array( 'nl' => 'Bel ons nu' ),
        'Please enter your full name.' => array( 'nl' => 'Vul uw volledige naam in.' ),
        'Please enter your email address.' => array( 'nl' => 'Vul uw e-mailadres in.' ),
        'Please enter a valid email address.' => array( 'nl' => 'Vul een geldig e-mailadres in.' ),
        'Please enter a short message.' => array( 'nl' => 'Vul een kort bericht in.' ),
        'Something went wrong. Please try again or contact us directly via email.' => array( 'nl' => 'Er ging iets mis. Probeer het opnieuw of neem direct per e-mail contact met ons op.' ),
        '[Floru Contact] Message from ' => array( 'nl' => '[Floru Contact] Bericht van ' ),
        'Name: ' => array( 'nl' => 'Naam: ' ),
        'Email: ' => array( 'nl' => 'E-mail: ' ),
        'Phone: ' => array( 'nl' => 'Telefoon: ' ),
        'Message:' => array( 'nl' => 'Bericht:' ),
        'Breadcrumb' => array( 'nl' => 'Broodkruimel' ),
        'Strategic contribution' => array( 'nl' => 'Strategische bijdrage' ),
        'Where Floru added focus and momentum' => array( 'nl' => 'Waar Floru focus en vaart toevoegde' ),
        'A concise view of the themes and workstreams Floru supported for %s.' => array( 'nl' => 'Een beknopt overzicht van de thema’s en werkstromen waarin Floru %s heeft ondersteund.' ),
        'Visual context' => array( 'nl' => 'Visuele context' ),
        'A closer look at %s' => array( 'nl' => 'Nader bekeken: %s' ),
        'Toggle sound' => array( 'nl' => 'Geluid in- of uitschakelen' ),
        'Sound' => array( 'nl' => 'Geluid' ),
        'Engagement overview' => array( 'nl' => 'Overzicht van de samenwerking' ),
        'Context, positioning and delivery' => array( 'nl' => 'Context, positionering en uitvoering' ),
        'Strategic fit' => array( 'nl' => 'Strategische match' ),
        'Comparable brief?' => array( 'nl' => 'Vergelijkbare opdracht?' ),
        'Floru supports defence and security organisations that need sharper positioning, stronger stakeholder alignment, and clearer routes to decision in the Dutch and European market.' => array( 'nl' => 'Floru ondersteunt defensie- en veiligheidsorganisaties die scherpere positionering, sterkere stakeholderafstemming en duidelijkere routes naar besluitvorming nodig hebben in de Nederlandse en Europese markt.' ),
        'Contact Floru' => array( 'nl' => 'Neem contact op met Floru' ),
        'Back to all Clients' => array( 'nl' => 'Terug naar alle opdrachtgevers' ),
        'Additional visual' => array( 'nl' => 'Aanvullend beeld' ),
        'Further context from the engagement' => array( 'nl' => 'Meer context uit de samenwerking' ),
        'Interested in Working With Us?' => array( 'nl' => 'Interesse om met ons samen te werken?' ),
        'We would be happy to discuss our experience and how we can support your goals.' => array( 'nl' => 'Wij bespreken graag onze ervaring en hoe wij uw doelen kunnen ondersteunen.' ),
        'Page Not Found' => array( 'nl' => 'Pagina niet gevonden' ),
        'We could not find that page.' => array( 'nl' => 'We konden die pagina niet vinden.' ),
        'The address may be outdated, or the page may have moved within the Floru site.' => array( 'nl' => 'Het adres kan verouderd zijn, of de pagina is binnen de Floru-site verplaatst.' ),
        'Error 404' => array( 'nl' => 'Fout 404' ),
        'Recovery routes' => array( 'nl' => 'Herstelroutes' ),
        'Return to the homepage, start a discreet conversation, or continue through one of Floru\'s core routes below.' => array( 'nl' => 'Ga terug naar de homepage, start een discreet gesprek of vervolg via een van de kernroutes van Floru hieronder.' ),
        'Back Home' => array( 'nl' => 'Terug naar home' ),
        'Core routes' => array( 'nl' => 'Kernroutes' ),
        'Visit page' => array( 'nl' => 'Ga naar pagina' ),
        'Director FLORU Consultancy' => array( 'nl' => 'Directeur FLORU Consultancy' ),
        'Technical Support' => array( 'nl' => 'Technische ondersteuning' ),
        'Air Force, Major General ret.' => array( 'nl' => 'Luchtmacht, generaal-majoor b.d.' ),
        'Navy, Commander ret.' => array( 'nl' => 'Marine, commandeur b.d.' ),
        'Army, Major ret.' => array( 'nl' => 'Landmacht, majoor b.d.' ),
        'Navy, Rear Admiral LH ret.' => array( 'nl' => 'Marine, schout-bij-nacht LH b.d.' ),
    );

    return $catalog;
}

/**
 * Hardcoded post-specific translation overrides.
 *
 * These are a safe theme-level fallback for existing content that has not yet
 * been given explicit *_nl post meta or field overrides in WordPress.
 *
 * @return array<string, array<string, array<string, array<string, string>>>>
 */
function floru_get_post_translation_overrides() {
    static $overrides = null;

    if ( null !== $overrides ) {
        return $overrides;
    }

    $team_page_content_nl = <<<'HTML'
<p>Floru brengt een klein, toegewijd team van professionals samen met diepe wortels in de defensie- en veiligheidssector. Ieder van ons heeft functies vervuld binnen de overheid, de krijgsmacht of de defensie-industrie, en juist die gecombineerde ervaring zetten wij in om resultaten voor onze klanten te realiseren.</p>
HTML;

    $services_1_desc_nl = <<<'HTML'
<p>Toetreden tot of groeien binnen Europese defensiemarkten vraagt om meer dan een goed product. Het vraagt om inzicht in politieke dynamiek, aanbestedingscycli en institutionele verhoudingen.</p><p>Floru biedt strategische ondersteuning bij bedrijfsontwikkeling aan bedrijven die hun positie in de Nederlandse en Europese defensie- en veiligheidsmarkt willen versterken. Wij helpen u kansen te identificeren, het concurrentielandschap te begrijpen en een heldere route naar engagement te ontwikkelen.</p><h4>Wat wij leveren:</h4><ul><li>Marktanalyse en identificatie van kansen</li><li>Marktstrategie voor Nederland en Europa</li><li>Concurrentieanalyse en positionering</li><li>Monitoring van aanbestedingspijplijnen</li><li>Strategisch advies over partnerschappen en samenwerking</li></ul>
HTML;

    $services_2_desc_nl = <<<'HTML'
<p>Beslissingen over defensieaanbestedingen kennen meerdere lagen van stakeholders: militaire eindgebruikers, programmamanagers, politieke besluitvormers en inkoopfunctionarissen. De juiste mensen bereiken met de juiste boodschap op het juiste moment is cruciaal.</p><p>Floru benut zijn opgebouwde netwerk en institutionele kennis om onze klanten te verbinden met de stakeholders die ertoe doen. Wij faciliteren introducties, ondersteunen relatieopbouw en helpen onze klanten door complexe organisatiestructuren te navigeren.</p><h4>Wat wij leveren:</h4><ul><li>Stakeholdermapping en analyse</li><li>Introducties bij sleutelcontacten binnen overheid en krijgsmacht</li><li>Ondersteuning bij events en beurzen</li><li>Communicatie- en boodschapstrategie</li><li>Advies over overheidsrelaties</li></ul>
HTML;

    $services_3_desc_nl = <<<'HTML'
<p>Overheidsinkoop in defensie en veiligheid is complex, tijdkritisch en sterk concurrerend. Een krachtige tenderrespons vraagt niet alleen om technische kwaliteit, maar ook om strategische positionering, heldere communicatie en volledige naleving van aanbestedingseisen.</p><p>Floru ondersteunt klanten gedurende het hele tenderproces: van vroege signalering en prekwalificatie tot uitwerking van de inschrijving, prijsstrategie en ondersteuning na indiening. Wij brengen diepgaande kennis mee van Nederlandse en Europese aanbestedingspraktijken.</p><h4>Wat wij leveren:</h4><ul><li>Signalering en opvolging van tenders</li><li>Prekwalificatie en compliancereview</li><li>Inschrijfstrategie en -management</li><li>Ontwikkeling van winthema\'s</li><li>Ondersteuning na indiening en begeleiding bij debriefs</li></ul>
HTML;

    $cnim_content_nl = <<<'HTML'
<p>CNIM is een Franse industriegroep die precies daar waar het ertoe doet beslissend voordeel ontwikkelt - in het veld. Hun defensiedivisie ontwerpt en produceert de Motorized Floating Bridge (PFM), een snel inzetbaar overbruggingssysteem waarop NAVO-bondgenoten vertrouwen om operationeel momentum te behouden over rivieren, meren en natte terreinen onder de meest veeleisende omstandigheden.</p>
<p>Waar anderen obstakels zien, levert CNIM doorgang. Het PFM-systeem heeft geen hulpboten nodig, opereert in het donker en verandert binnen enkele minuten van transportconfiguratie naar een volledig belastbare brug, zodat gepantserde colonnes zonder onderbreking kunnen doorstoten.</p>
<p>Naast defensie benut CNIM meer dan 160 jaar expertise in industriële engineering in de energie-, milieu- en geavanceerde technologiesector. Dit dual-use DNA zorgt ervoor dat hun militaire systemen profiteren van voortdurende civiele R&amp;D-investeringen en op schaal ongeëvenaarde betrouwbaarheid leveren.</p>
HTML;

    $dujardin_content_nl = <<<'HTML'
<p>Dujardin is de toonaangevende merkonafhankelijke specialist in Nederland op het gebied van fysieke asset protection. Van hoogbeveiligde kluisruimtes en explosiewerende deuren tot volledig ATM-lifecyclebeheer leveren zij geintegreerde oplossingen - niet alleen producten.</p>
<p>Actief in de Benelux is Dujardin de enige partij in de markt die voor ATM-infrastructuur van banken een volledig end-to-end dienstenpakket aanbiedt: installatie, preventief onderhoud en 24/7 noodrespons. Wanneer een kluisdeur stand moet houden of een geldautomaat operationeel moet blijven, is Dujardin de naam die wordt gebeld.</p>
<p>Hun aanpak is compromisloos: maatwerkengineering, gecertificeerde systemen en een responsorganisatie die elke inzet als mission-critical behandelt. Voor financiele instellingen, overheidslocaties en high-value ondernemingen die zich geen enkel zwak punt in hun fysieke beveiligingsketen kunnen veroorloven, zet Dujardin de standaard.</p>
HTML;

    $exensor_content_nl = <<<'HTML'
<p>Sinds 1987 is Exensor - nu Bertin Exensor - de internationale maatstaf voor Unattended Ground Sensor (UGS)-technologie. Hun Flexnet-platform is een volledig geintegreerd, draadloos sensornetwerk dat permanente surveillance, threat detection en force protection levert over elk terrein en onder alle omstandigheden.</p>
<p>Flexnet wordt snel uitgerold, opereert covert en houdt zichzelf in stand via een zelfherstellende mesh-radioarchitectuur die geen vaste infrastructuur vereist. Het systeem is batterijgevoed en klimaatbestendig en classificeert en identificeert dreigingen - voertuigen, personen en seismische signalen - voordat ze gevaarlijk worden. Het is de onzichtbare perimeter die nooit slaapt.</p>
<p>Flexnet is geleverd aan meer dan 20 landen voor militaire en civiele toepassingen en werkt als force multiplier voor grensbewaking, bescherming van kritieke infrastructuur en inlichtingenoperaties. Exensors Zweedse engineering-erfgoed garandeert dat elk component voldoet aan de eisen van de meest capabele krijgsmachten ter wereld.</p>
HTML;

    $g4s_content_nl = <<<'HTML'
<p>G4S is 's werelds grootste geintegreerde beveiligingsbedrijf, actief in meer dan 80 landen met een personeelsbestand dat tot de grootste particuliere werkgevers ter wereld behoort. Zij leveren het volledige beveiligingsspectrum: fysieke bewaking, technologiegedreven surveillance, cash management en strategisch advies - samengebracht onder een commandostructuur.</p>
<p>Hun operationele bereik omvat overheidslocaties, vitale nationale infrastructuur, hoofdkantoren, grote evenementen en high-risk omgevingen waar falen geen optie is. G4S combineert geavanceerde technologie met elite mankracht om beveiligingsecosystemen te bouwen die met precisie detecteren, afschrikken en reageren.</p>
<p>Van geintegreerde toegangscontrole en AI-gedreven monitoring tot gepantserde logistiek en crisismanagement: G4S biedt een aanspreekbare partner voor organisaties die institutionele bescherming van topniveau eisen. Hun schaal maakt capaciteiten mogelijk die geen regionale speler kan evenaren; hun discipline zorgt dat die op het hoogste professionele niveau worden geleverd.</p>
HTML;

    $everbridge_content_nl = <<<'HTML'
<p>Everbridge is de wereldstandaard in Critical Event Management - het enterprise-platform dat overheden, ondernemingen en instellingen inzetten wanneer seconden de uitkomst bepalen. Hun CEM-suite bundelt realtime dreigingsdata, lokaliseert personeel dat risico loopt, automatiseert vooraf bepaalde responsprotocollen en verstuurt meldingen gelijktijdig via meer dan 100 communicatiekanalen.</p>
<p>Wanneer zich een active shooter-situatie ontvouwt, een terroristische dreiging opkomt, extreem weer toeslaat of IT-infrastructuur uitvalt, vertrouwen meer dan 5.300 organisaties wereldwijd op Everbridge om ruis weg te nemen en uitvoering te brengen. Het platform vertaalt chaotische, informatierijke crisissituaties naar gestructureerde, uitvoerbare responsworkflows.</p>
<p>Everbridges architectuur is gebouwd voor de snelheid en schaal die kritieke gebeurtenissen vragen: levering in milliseconden, wereldwijd bereik en audit-grade logging voor compliance. Voor organisaties die opereren in high-threat omgevingen - of die de veiligheid van medewerkers simpelweg niet aan toeval overlaten - levert Everbridge de command-and-control ruggengraat die veerkracht operationeel maakt.</p>
HTML;

    $nu_security_content_nl = <<<'HTML'
<p>NU Security Consultancy, geleid door Rob van Nuland MSec, levert het soort helderheid dat alleen uit diepe operationele ervaring voortkomt. Elke organisatie heeft met dreigingen te maken; de vraag is of die dreigingen zijn geidentificeerd, gekwantificeerd en aangepakt voordat ze werkelijkheid worden. NU Security geeft daar het antwoord op.</p>
<p>Hun portfolio bestrijkt het volledige spectrum van security advisory: uitgebreide dreigings- en risicoanalyses, risicomanagementstrategie, advies over OBE (Organisatorische Beveiligingsmaatregelen), security audits, informatiebeveiligingskaders en awareness-programma's die de organisatiecultuur van reactief naar weerbaar brengen.</p>
<p>Wat NU Security onderscheidt, is precisie. Geen generieke frameworks, geen kant-en-klare checklists - elke opdracht resulteert in een maatwerkbeoordeling met concrete tegenmaatregelen, afgestemd op het specifieke dreigingslandschap van de klant. Van security briefings voor de directie tot hands-on implementatiebegeleiding opereert NU Security Consultancy als de vertrouwde adviseur die high-value organisaties nodig hebben in een steeds complexere dreigingsomgeving.</p>
HTML;

    $saab_content_nl = <<<'HTML'
<p>Saab is een wereldspeler in defensie en luchtvaart die landen op elk continent ondersteunt met toonaangevende producten, diensten en oplossingen voor lucht, land, zee en cyber. Van de Gripen multirole fighter tot geavanceerde radarsystemen, van onderwater-torpedo's tot complete combat management-suites: Saab levert soevereine capaciteit op het hoogste niveau.</p>
<p>Hun vijf kerndomeinen - aeronautics, advanced weapons systems, underwater systems, command and control en sensors - vertegenwoordigen decennia Zweedse engineeringprecisie, aangescherpt onder operationele druk. Alleen al het Gripen-programma laat Saabs filosofie zien: een fighter system ontworpen niet alleen voor prestaties, maar ook voor operationele onafhankelijkheid, snelle turnaround en echte lifecycle affordability.</p>
<p>Saabs sensorportfolio vormt de ogen en oren van moderne defensie: airborne early warning, oppervlakte-luchtradar, elektronische oorlogvoeringssuites en signature management-systemen waarmee strijdkrachten eerst kunnen zien, eerst kunnen beslissen en eerst kunnen handelen. In een tijdperk van multi-domain operations en versnellende dreigingen levert Saab de technologische voorsprong die het verschil bepaalt tussen voordeel en kwetsbaarheid.</p>
HTML;

    $stratego_work_content_nl = <<<'HTML'
<p>Stratego Work opereert precies op het snijvlak waar militaire excellentie en zakelijke vraag samenkomen. Als gespecialiseerd staffingbureau voor voormalig defensiepersoneel verbinden zij organisaties met professionals van wie de competenties zijn gevormd in de meest veeleisende operationele omgevingen ter wereld.</p>
<p>Hun kandidaten brengen meer mee dan een cv - zij brengen discipline, aanpassingsvermogen, leiderschap onder druk en een mission-completion mindset die de prestaties van organisaties aantoonbaar verhoogt. Stratego Work levert werving, detachering, projectinzet en maatwerkcapaciteit voor nationale en internationale bedrijven die uitzonderlijk talent nodig hebben voor uitzonderlijke uitdagingen.</p>
<p>Wat deze organisatie onderscheidt, is het intieme begrip van beide werelden. Stratego Work vertaalt militaire capaciteit naar zakelijke waarde en koppelt specialistische skillsets aan bedrijfsbehoeften met dezelfde precisie die hun kandidaten in operatie gebruikten. Voor organisaties die ervaren, gedreven professionals zoeken die resultaten leveren in high-pressure, high-stakes omgevingen, is Stratego Work de logische bron.</p>
HTML;

    $tbm_content_nl = <<<'HTML'
<p>TBM is een defensiehandelshuis met wortels die teruggaan tot de jaren vijftig - een bedrijf gebouwd op een duidelijke missie: de professionals die democratische waarden beschermen uitrusten met de best beschikbare middelen. Als exclusief vertegenwoordiger, agent en distributeur voor een select portfolio van gekwalificeerde internationale defensiepartners levert TBM een onovertroffen reeks wapens, tactische uitrusting en technologische oplossingen.</p>
HTML;

    $x_systems_content_nl = <<<'HTML'
<p>X-Systems bouwt de communicatieapparaten en platforms die overheden, defensieorganisaties en ondernemingen inzetten wanneer vertrouwelijkheid niet onderhandelbaar is. Hun ultraveilige smartphones en versleutelde communicatieoplossingen zijn vanaf de basis ontworpen - hardware en software - zodat gevoelige informatie soeverein blijft.</p>
<p>In een tijd van alomtegenwoordige surveillance, staatsgesponsorde cyberespionage en industriële inlichtingendiefstal levert X-Systems het tegenmiddel: apparaten met geharde besturingssystemen, end-to-end encryptie en sabotagebestendige architecturen die voldoen aan de eisen van geclassificeerde omgevingen. Geen backdoors. Geen compromissen. Geen uitzonderingen.</p>
HTML;

    $overrides = array(
        'page' => array(
            'our-team' => array(
                'content' => array(
                    'nl' => $team_page_content_nl,
                ),
            ),
            'services' => array(
                '_floru_svc1_desc' => array(
                    'nl' => $services_1_desc_nl,
                ),
                '_floru_svc2_desc' => array(
                    'nl' => $services_2_desc_nl,
                ),
                '_floru_svc3_desc' => array(
                    'nl' => $services_3_desc_nl,
                ),
            ),
        ),
        'floru_client' => array(
            'cnim' => array(
                'excerpt' => array(
                    'nl' => 'CNIM is een Franse industriegroep die precies daar waar het ertoe doet beslissend voordeel ontwikkelt - in het veld. Hun defensiedivisie ontwerpt en produceert de Motorized Floating Bridge...',
                ),
                'content' => array(
                    'nl' => $cnim_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Defensie- en militaire engineering',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Innoveren en handelen',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Wereldleider in militaire brugsystemen',
                        'Motorized Floating Bridge (PFM) ingezet bij NAVO-strijdkrachten',
                        'Dual-use innovatie - defensie en civiele techniek',
                        'Franse industriële erfenis sinds 1856',
                    ) ),
                ),
            ),
            'dujardin' => array(
                'excerpt' => array(
                    'nl' => 'Dujardin is de toonaangevende merkonafhankelijke specialist in Nederland op het gebied van fysieke asset protection. Van hoogbeveiligde kluisruimtes en explosiewerende deuren tot volledig ATM-lifecyclebeheer leveren zij geintegreerde oplossingen...',
                ),
                'content' => array(
                    'nl' => $dujardin_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Fysieke beveiliging en asset protection',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Totale beveiliging. Technisch doordacht.',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Merkonafhankelijke full-service partner',
                        'Kluizen, kluisruimtes en veiligheidsdeuren',
                        'ATM-lifecyclebeheer voor banken',
                        '24/7 noodresponscapaciteit',
                    ) ),
                ),
            ),
            'exensor' => array(
                'excerpt' => array(
                    'nl' => 'Sinds 1987 is Exensor - nu Bertin Exensor - de internationale maatstaf voor Unattended Ground Sensor (UGS)-technologie. Hun Flexnet-platform is een volledig geintegreerd, draadloos sensornetwerk...',
                ),
                'content' => array(
                    'nl' => $exensor_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Defensiesensorsystemen',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Ogen waar u ze nodig hebt',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Wereldleidende leverancier van Unattended Ground Sensor (UGS)-systemen',
                        'Flexnet-platform ingezet in meer dan 20 landen',
                        'Draadloos, batterijgevoed, zelfherstellend mesh-netwerk',
                        'Force protection en permanente brede gebiedsbewaking',
                    ) ),
                ),
            ),
            'g4s' => array(
                'excerpt' => array(
                    'nl' => 'G4S is wereldwijd het grootste geintegreerde beveiligingsbedrijf, actief in meer dan 80 landen met een personeelsbestand dat tot de grootste particuliere werkgevers ter wereld behoort. Zij leveren...',
                ),
                'content' => array(
                    'nl' => $g4s_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Wereldwijde beveiligingsoplossingen',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Uw wereld beveiligd',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Wereldwijd toonaangevend geintegreerd beveiligingsbedrijf',
                        'Activiteiten in meer dan 80 landen',
                        'End-to-end: bewaking, technologie, cash en consulting',
                        'Vertrouwd door overheden, vitale infrastructuur en Fortune 500',
                    ) ),
                ),
            ),
            'everbridge' => array(
                'excerpt' => array(
                    'nl' => 'Everbridge is de wereldstandaard in Critical Event Management - het enterprise-platform dat overheden, ondernemingen en instellingen inzetten wanneer seconden de uitkomst bepalen. Hun CEM-suite bundelt realtime...',
                ),
                'content' => array(
                    'nl' => $everbridge_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Kritiek incidentmanagement',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Mensen veilig. Organisaties operationeel.',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Wereldleider in Critical Event Management (CEM)',
                        'Meer dan 5.300 enterprise-klanten wereldwijd',
                        'Realtime dreigingsinformatie en massanotificatie',
                        'Uitrol via meer dan 100 communicatiekanalen',
                    ) ),
                ),
            ),
            'nu-security-consultancy' => array(
                'excerpt' => array(
                    'nl' => 'NU Security Consultancy, geleid door Rob van Nuland MSec, levert het soort helderheid dat alleen uit diepe operationele ervaring voortkomt. Elke organisatie heeft met dreigingen te maken; de vraag...',
                ),
                'content' => array(
                    'nl' => $nu_security_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Beveiligingsadvies en risicomanagement',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Helderheid in complexiteit',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Specialist in dreigings- en risicoanalyses',
                        'Advies over OBE-beveiligingsmaatregelen',
                        'Security awareness-programma\'s',
                        'Informatiebeveiliging en auditcapaciteit',
                    ) ),
                ),
            ),
            'saab' => array(
                'excerpt' => array(
                    'nl' => 'Saab is een wereldspeler in defensie en luchtvaart die landen op elk continent ondersteunt met toonaangevende producten, diensten en oplossingen voor lucht, land, zee en cyber. Van de Gripen...',
                ),
                'content' => array(
                    'nl' => $saab_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Defensie en luchtvaart',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Defensie en veiligheid voor een veranderende wereld',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Gevechtssystemen van wereldklasse, waaronder Gripen',
                        'Geavanceerde wapens, sensoren en elektronische oorlogvoering',
                        'Underwater systems en torpedotechnologie',
                        'Command & control over alle domeinen',
                    ) ),
                ),
            ),
            'stratego-work' => array(
                'excerpt' => array(
                    'nl' => 'Stratego Work opereert precies op het snijvlak waar militaire excellentie en zakelijke vraag samenkomen. Als gespecialiseerd staffingbureau voor voormalig defensiepersoneel verbinden zij organisaties met professionals...',
                ),
                'content' => array(
                    'nl' => $stratego_work_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Defensietalentoplossingen',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Militaire precisie. Zakelijke impact.',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Specialistische werving voor ex-militaire professionals',
                        'Werving, detachering en projectinzet',
                        'Brug tussen Defensie en bedrijfsleven',
                        'Internationale plaatsingscapaciteit',
                    ) ),
                ),
            ),
            'tbm' => array(
                'excerpt' => array(
                    'nl' => 'TBM is een defensiehandelshuis met wortels die teruggaan tot de jaren vijftig - een bedrijf gebouwd op een duidelijke missie: de professionals die democratische waarden beschermen uitrusten met de best beschikbare middelen...',
                ),
                'content' => array(
                    'nl' => $tbm_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Defensie-uitrusting en distributie',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Alleen voor professionals',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Vertrouwde overheidsleverancier sinds de jaren 50',
                        'Exclusief distributeur van toonaangevende internationale defensiemerken',
                        'Wapens, tactische uitrusting en integratie van robotica',
                        'Leverancier voor Defensie, Politie en partners in de Benelux',
                    ) ),
                ),
            ),
            'x-systems' => array(
                'excerpt' => array(
                    'nl' => 'X-Systems bouwt de communicatieapparaten en platforms die overheden, defensieorganisaties en ondernemingen inzetten wanneer vertrouwelijkheid niet onderhandelbaar is. Hun ultraveilige smartphones en versleutelde communicatieoplossingen zijn...',
                ),
                'content' => array(
                    'nl' => $x_systems_content_nl,
                ),
                '_floru_client_industry' => array(
                    'nl' => 'Veilige communicatie en cyberbeveiliging',
                ),
                '_floru_client_tagline' => array(
                    'nl' => 'Compromisloze veilige communicatie',
                ),
                '_floru_client_highlights' => array(
                    'nl' => implode( "\n", array(
                        'Ultraveilige mobiele apparaten en communicatieplatformen',
                        'End-to-end versleutelde hardware- en softwareoplossingen',
                        'Ontworpen voor overheid, defensie en enterprise',
                        'Nederlands ontwikkelde privacy-first architectuur',
                    ) ),
                ),
            ),
        ),
    );

    return $overrides;
}

/**
 * Get a post-specific translation override.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Field name or meta key.
 * @return string
 */
function floru_get_post_translation_override( $post_id, $key ) {
    $post_id = (int) $post_id;

    if ( ! $post_id || 'en' === floru_get_current_language() ) {
        return '';
    }

    $post_type = get_post_type( $post_id );
    $slug      = get_post_field( 'post_name', $post_id );
    $language  = floru_get_current_language();
    $overrides = floru_get_post_translation_overrides();

    if ( ! $post_type || ! is_string( $slug ) || '' === $slug ) {
        return '';
    }

    if ( ! isset( $overrides[ $post_type ][ $slug ][ $key ][ $language ] ) ) {
        return '';
    }

    $value = $overrides[ $post_type ][ $slug ][ $key ][ $language ];

    return is_string( $value ) ? $value : '';
}

/**
 * Translate an arbitrary text fragment.
 *
 * @param string $text Source string.
 * @return string
 */
function floru_translate_text( $text ) {
    if ( ! is_string( $text ) || '' === $text ) {
        return $text;
    }

    $language = floru_get_current_language();

    if ( 'en' === $language && false === strpos( $text, 'Portret' ) && false === strpos( $text, 'Bekijk' ) && false === strpos( $text, 'Profiel' ) ) {
        return $text;
    }

    $catalog = floru_get_translation_catalog();
    $lookup  = array(
        $text,
        preg_replace( "/\r\n?/", "\n", $text ),
        trim( preg_replace( "/\r\n?/", "\n", $text ) ),
    );

    foreach ( $lookup as $candidate ) {
        if ( isset( $catalog[ $candidate ][ $language ] ) ) {
            return $catalog[ $candidate ][ $language ];
        }
    }

    return $text;
}

/**
 * Translate then format a string.
 *
 * @param string $text Source string.
 * @param mixed  ...$args Replacement values.
 * @return string
 */
function floru_tf( $text, ...$args ) {
    $translated = floru_translate_text( $text );

    return $args ? vsprintf( $translated, $args ) : $translated;
}

/**
 * Short alias for translated text.
 *
 * @param string $text Source string.
 * @return string
 */
function floru_t( $text ) {
    return floru_translate_text( $text );
}

/**
 * Translate theme and Astra gettext strings on the frontend.
 */
add_filter( 'gettext', 'floru_translate_gettext_strings', 25, 3 );
function floru_translate_gettext_strings( $translation, $text, $domain ) {
    if ( ! floru_is_public_request() ) {
        return $translation;
    }

    if ( ! in_array( $domain, array( 'astra-child-floru', 'astra' ), true ) ) {
        return $translation;
    }

    return floru_translate_text( $text );
}

/**
 * Translate menu item labels without changing menu assignments.
 */
add_filter( 'wp_nav_menu_objects', 'floru_translate_menu_titles', 30, 2 );
function floru_translate_menu_titles( $items ) {
    if ( ! floru_is_public_request() ) {
        return $items;
    }

    foreach ( $items as $item ) {
        if ( isset( $item->title ) ) {
            $item->title = floru_translate_text( (string) $item->title );
        }
    }

    return $items;
}

/**
 * Translate site options with a manual per-language override fallback.
 *
 * @param string $option_name Option name.
 * @param string $default     Default value.
 * @return string
 */
function floru_get_translated_option( $option_name, $default = '' ) {
    $value = get_option( $option_name, $default );

    if ( 'en' !== floru_get_current_language() ) {
        $translated_value = get_option( $option_name . '_' . floru_get_current_language(), null );
        if ( is_string( $translated_value ) && '' !== trim( $translated_value ) ) {
            return $translated_value;
        }
    }

    return is_string( $value ) ? floru_translate_text( $value ) : $value;
}

/**
 * Translate arbitrary post meta with optional per-language overrides.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param string $default Default value.
 * @return string
 */
function floru_get_translated_post_meta( $post_id, $key, $default = '' ) {
    $value = get_post_meta( $post_id, $key, true );
    $value = ( '' !== $value && false !== $value ) ? $value : $default;

    if ( 'en' !== floru_get_current_language() ) {
        $translated_value = get_post_meta( $post_id, $key . '_' . floru_get_current_language(), true );
        if ( '' !== $translated_value && false !== $translated_value ) {
            return $translated_value;
        }

        $hardcoded_override = floru_get_post_translation_override( $post_id, $key );
        if ( '' !== $hardcoded_override ) {
            return $hardcoded_override;
        }
    }

    return is_string( $value ) ? floru_translate_text( $value ) : $value;
}

/**
 * Translate raw post fields with optional per-language overrides stored in meta.
 *
 * @param int    $post_id Post ID.
 * @param string $field   One of title, excerpt, content.
 * @return string
 */
function floru_get_translated_post_field( $post_id, $field ) {
    $post_id = (int) $post_id;
    if ( ! $post_id ) {
        return '';
    }

    $field_map = array(
        'title'   => 'post_title',
        'excerpt' => 'post_excerpt',
        'content' => 'post_content',
    );

    if ( ! isset( $field_map[ $field ] ) ) {
        return '';
    }

    if ( 'en' !== floru_get_current_language() ) {
        $override = get_post_meta( $post_id, '_floru_' . $field . '_' . floru_get_current_language(), true );
        if ( is_string( $override ) && '' !== trim( $override ) ) {
            return $override;
        }

        $hardcoded_override = floru_get_post_translation_override( $post_id, $field );
        if ( '' !== $hardcoded_override ) {
            return $hardcoded_override;
        }
    }

    $value = (string) get_post_field( $field_map[ $field ], $post_id, 'raw' );

    return floru_translate_text( $value );
}

/**
 * Translate a post title.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function floru_get_translated_post_title_raw( $post_id ) {
    return floru_get_translated_post_field( $post_id, 'title' );
}

/**
 * Translate a post excerpt.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function floru_get_translated_post_excerpt_raw( $post_id ) {
    return floru_get_translated_post_field( $post_id, 'excerpt' );
}

/**
 * Translate post content.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function floru_get_translated_post_content_raw( $post_id ) {
    return floru_get_translated_post_field( $post_id, 'content' );
}