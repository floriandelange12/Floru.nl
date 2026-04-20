/**
 * build-report.js — Editorial-style comparison report
 * Floru.nl Redesign · 2026
 *
 * Genereert een zelfstandig HTML-bestand (rapport.html) dat als PDF
 * geprint kan worden via Ctrl+P. Geen externe assets, geen JavaScript,
 * geen interactieve elementen — puur een redactioneel document.
 */

const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');
const OUTPUT_FILE    = path.join(__dirname, 'rapport.html');

// ─── Helpers ─────────────────────────────────────────────────────────────────

function imgToBase64(filePath) {
  // Try JPEG first, then fall back to PNG
  const jpgPath = filePath.replace(/\.png$/, '.jpg');
  if (fs.existsSync(jpgPath)) {
    const data = fs.readFileSync(jpgPath);
    return `data:image/jpeg;base64,${data.toString('base64')}`;
  }
  if (!fs.existsSync(filePath)) return null;
  const data = fs.readFileSync(filePath);
  return `data:image/png;base64,${data.toString('base64')}`;
}

function shotPath(site, viewport, page, section = 'full') {
  return path.join(SCREENSHOT_DIR, site, viewport, `${page}-${section}.png`);
}

function img(site, viewport, page, section = 'full', alt = '') {
  const b64 = imgToBase64(shotPath(site, viewport, page, section));
  if (!b64) {
    return `<div class="img-missing"><span>Niet beschikbaar</span></div>`;
  }
  return `<img src="${b64}" alt="${alt}" />`;
}

// ─── Content: pagina-spreads ────────────────────────────────────────────────
//
// Per pagina:
//   - intro: leesbare inleiding voor leken
//   - changes: drie korte bullets ("wat is er veranderd")
//   - reason: waarom het beter is
//   - annotations: cirkels op het 'nieuw' screenshot
//       { num, x, y, title, text }   — x/y in %
//
const PAGES = [
  {
    n: '01', id: 'home', title: 'Home',
    oudUrl: 'www.floru.nl',
    nieuwUrl: 'florunl.local',
    intro: 'De homepage is het visitekaartje. Een bezoeker beslist binnen enkele seconden of een organisatie geloofwaardig overkomt. De oude pagina opende met een statisch beeld en losse tekstblokken. De nieuwe homepage vertelt direct wat Floru doet, voor wie, en waarom.',
    changes: [
      'Heldere hero met één duidelijke kernboodschap en een directe call-to-action.',
      'Geanimeerde kerncijfers (jaren ervaring, klanten, projecten) die vertrouwen wekken.',
      'Korte previews van diensten en cases — bezoekers zien meteen waar Floru sterk in is.',
    ],
    reason: 'De homepage is herontworpen rondom één principe: binnen drie seconden moet duidelijk zijn wie Floru is en wat ze bieden. Ruime witregels, een rustige kleurpalet en een logische opbouw doen dat werk — zonder dat de pagina luid hoeft te zijn.',
    annotations: [
      { num: 1, x: 50, y: 4,  title: 'Schoon hoofdmenu',     text: 'Eén regel, vijf keuzes, taalwissel rechts. Geen rommel.' },
      { num: 2, x: 30, y: 18, title: 'Krachtige kernzin',     text: 'Direct duidelijk wat Floru doet en voor wie.' },
      { num: 3, x: 50, y: 38, title: 'Bewijs in cijfers',      text: 'Geanimeerde tellers — vertrouwen zonder reclame-toon.' },
    ],
  },
  {
    n: '02', id: 'about', title: 'About — Onze werkwijze',
    oudUrl: 'www.floru.nl/modus-operandi',
    nieuwUrl: 'florunl.local/about',
    intro: 'De "Modus Operandi"-pagina was lang en moeilijk leesbaar. Klanten willen op één scherm begrijpen hoe Floru werkt en waar ze voor staan. De nieuwe About-pagina maakt dat helder.',
    changes: [
      'Genummerde stappen (01 → 04) die de werkwijze visueel uitleggen.',
      'Een aparte values-grid met de kernwaarden van Floru.',
      'Vereenvoudigd webadres: /about/ in plaats van /modus-operandi/.',
    ],
    reason: 'Ritme en herhaling helpen lezen. Door de werkwijze in vier genummerde stappen op te bouwen, wordt complexe consultancy in één oogopslag begrijpelijk. Korte, krachtige adressen voelen ook professioneler en zijn beter vindbaar in Google.',
    annotations: [
      { num: 1, x: 50, y: 5,  title: 'Vereenvoudigd adres',    text: '/about/ — kort en internationaal herkenbaar.' },
      { num: 2, x: 25, y: 35, title: 'Genummerde stappen',     text: 'Werkwijze in 4 logische fasen.' },
      { num: 3, x: 75, y: 65, title: 'Kernwaarden visueel',    text: 'Eigen blok met waarden — niet meer verstopt in tekst.' },
    ],
  },
  {
    n: '03', id: 'services', title: 'Services',
    oudUrl: null,
    nieuwUrl: 'florunl.local/services',
    isNew: true,
    intro: 'De oude site had geen aparte dienstenpagina. Bezoekers moesten gokken wat Floru precies aanbiedt. Voor een consultancy is dat een gemiste kans — diensten zijn de kern van wat je verkoopt.',
    changes: [
      'Drie heldere service-panels: Business Development, Stakeholder Engagement en Tender Support.',
      'Per dienst een korte uitleg, voorbeeld-toepassingen en wanneer hij relevant is.',
      'Doorlinks naar het contactformulier op elke service.',
    ],
    reason: 'Een potentiële klant die op zoek is naar tender-ondersteuning wil dat woord zien staan. De Services-pagina is gestructureerd vanuit de zoekvraag van de klant — niet vanuit de interne organisatie. Dat helpt zowel conversie als vindbaarheid.',
    annotations: [
      { num: 1, x: 50, y: 12, title: 'Eigen pagina',          text: 'Voor het eerst hebben de diensten een eigen plek.' },
      { num: 2, x: 50, y: 42, title: 'Drie duidelijke panels', text: 'Elk met titel, uitleg en concrete voorbeelden.' },
      { num: 3, x: 50, y: 88, title: 'Conversie-pad',          text: 'Direct doorlink naar contact onderaan elke dienst.' },
    ],
  },
  {
    n: '04', id: 'team', title: 'Team',
    oudUrl: 'www.floru.nl/medewerkers',
    nieuwUrl: 'florunl.local/our-team',
    intro: 'Een consultancy verkoopt mensen. De oude teampagina toonde zwart-witte foto\'s zonder context — gebouwd met een externe widget die niet meer onderhouden werd. Nieuwe collega\'s toevoegen kostte technische kennis.',
    changes: [
      'Volwaardige teamprofielen met foto, naam, rol, bio en LinkedIn-link.',
      'Beheerd via een eigen "Team-beheermenu" in WordPress — toevoegen kan iedereen.',
      'Uniforme presentatie: elk profiel even groot, zelfde uitlijning, professionele uitstraling.',
    ],
    reason: 'Mensen kopen van mensen. Een teampagina die laat zien wíé er werkt — met een gezicht, een rol en een korte achtergrond — bouwt vertrouwen op vóórdat het eerste gesprek plaatsvindt. En doordat het team voortaan in een eigen beheermenu staat, kan Floru zelf groeien zonder externe bouwer.',
    annotations: [
      { num: 1, x: 50, y: 12, title: 'Heldere kop',            text: 'Eén regel die uitlegt waar je naar kijkt.' },
      { num: 2, x: 25, y: 50, title: 'Uniforme cards',         text: 'Foto, naam, rol, bio — voor iedereen gelijk.' },
      { num: 3, x: 75, y: 50, title: 'LinkedIn-koppeling',     text: 'Direct doorklikken voor extra geloofwaardigheid.' },
    ],
  },
  {
    n: '05', id: 'clients', title: 'Clients',
    oudUrl: 'www.floru.nl/clients-2',
    nieuwUrl: 'florunl.local/clients',
    intro: 'De oude clients-pagina toonde logo\'s op een rommelige achtergrond, zonder verdere informatie. Een bezoeker zag bekende namen, maar kreeg geen bewijs van wat Floru voor hen heeft gedaan.',
    changes: [
      'Logo-grid met hover-effecten en doorklik-mogelijkheid naar detailpagina\'s.',
      'Een "evidence panel" dat in één oogopslag samenvat wat Floru bereikt heeft.',
      'Beheerd via een eigen "Klanten-beheermenu" — logo\'s toevoegen vereist geen technische hulp.',
    ],
    reason: 'Logo\'s zijn sociaal bewijs, maar pas waardevol als ze een klikbaar verhaal hebben. De nieuwe pagina zet logo\'s in als ingang naar concrete cases. Dat maakt het verschil tussen "ze hebben grote klanten" en "ze hebben grote klanten met aantoonbare resultaten".',
    annotations: [
      { num: 1, x: 50, y: 8,  title: 'Heldere intro',          text: 'Korte uitleg waarom deze namen ertoe doen.' },
      { num: 2, x: 50, y: 38, title: 'Klikbare logo-grid',     text: 'Elk logo opent een eigen referentiepagina.' },
      { num: 3, x: 50, y: 78, title: 'Evidence panel',         text: 'Bereikte resultaten in één blik.' },
    ],
  },
  {
    n: '06', id: 'client-detail', title: 'Klant-detailpagina',
    oudUrl: 'www.floru.nl/cnim-2',
    nieuwUrl: 'florunl.local/client/cnim',
    intro: 'De oude site had per klant een losse pagina met een opvallend "-2" achter het webadres — een teken dat het systeem niet schoon was opgezet. Voor wie weet hoe Google werkt, is dat geen sterk signaal.',
    changes: [
      'Schone webadressen volgens het patroon /client/[naam]/.',
      'Per klant een hero met logo, korte tagline en breadcrumb-navigatie.',
      'Highlights-grid en optionele video-sectie voor uitgebreidere cases.',
    ],
    reason: 'Een schone URL-structuur is meer dan cosmetica. Het maakt de site beter vindbaar in Google, makkelijker te delen, en laat aan elke bezoeker zien dat de organisatie zorgvuldig met details omgaat. Dat detail straalt af op de hele dienstverlening.',
    annotations: [
      { num: 1, x: 50, y: 5,  title: 'Schone URL',             text: '/client/cnim/ — geen rare nummers meer.' },
      { num: 2, x: 50, y: 22, title: 'Logo + tagline',         text: 'Direct herkenbaar wie de klant is.' },
      { num: 3, x: 50, y: 60, title: 'Highlights',             text: 'Korte feiten in plaats van lappen tekst.' },
    ],
  },
  {
    n: '07', id: 'contact', title: 'Contact',
    oudUrl: 'www.floru.nl/contact',
    nieuwUrl: 'florunl.local/contact',
    intro: 'De oude contactpagina was een formulier zonder context — geen telefoonnummer, geen adres, geen reactietijd. De nieuwe pagina is opgezet als een uitnodiging tot gesprek.',
    changes: [
      'Twee-koloms layout: contactgegevens links, formulier rechts.',
      'Anti-spam ingebouwd via verborgen veld + beveiligingstoken — geen externe captcha nodig.',
      'Duidelijke verwachtingen: wat gebeurt er na het verzenden, en wanneer hoort u terug.',
    ],
    reason: 'Een goed contactformulier neemt drempels weg. Door telefoonnummer, e-mail én formulier op één pagina te bieden, kiest de bezoeker zelf hoe hij contact opneemt. De spam-bescherming werkt onzichtbaar — geen storende vinkjes of plaatjes.',
    annotations: [
      { num: 1, x: 25, y: 35, title: 'Contactgegevens',        text: 'E-mail en details direct zichtbaar.' },
      { num: 2, x: 75, y: 50, title: 'Compact formulier',      text: 'Alleen wat echt nodig is.' },
      { num: 3, x: 75, y: 90, title: 'Onzichtbare beveiliging', text: 'Anti-spam zonder hinderlijke captcha.' },
    ],
  },
];

// ─── HTML-bouwfuncties ──────────────────────────────────────────────────────

function renderAnnotations(annos) {
  return annos.map(a => `
    <span class="anno" style="left:${a.x}%;top:${a.y}%;" aria-hidden="true">${a.num}</span>
  `).join('');
}

function renderAnnotationLegend(annos) {
  return `
    <ol class="anno-legend">
      ${annos.map(a => `
        <li>
          <span class="anno-num">${a.num}</span>
          <div>
            <strong>${a.title}</strong>
            <p>${a.text}</p>
          </div>
        </li>
      `).join('')}
    </ol>
  `;
}

function renderSpreadIntro(p) {
  return `
    <article class="page page--intro" id="page-${p.id}">
      <header class="page-head">
        <span class="eyebrow">Pagina ${p.n}</span>
        <h2 class="page-title">${p.title}</h2>
      </header>

      <div class="page-body">
        <p class="lead">${p.intro}</p>

        <section class="block">
          <h3 class="block-title">Wat is er veranderd</h3>
          <ul class="bullets">
            ${p.changes.map(c => `<li>${c}</li>`).join('')}
          </ul>
        </section>

        <section class="block">
          <h3 class="block-title">Waarom is dit beter</h3>
          <p>${p.reason}</p>
        </section>

        <section class="block">
          <h3 class="block-title">Wijst naar in de afbeeldingen</h3>
          ${renderAnnotationLegend(p.annotations)}
        </section>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>${p.n}A</span>
      </footer>
    </article>
  `;
}

function renderSpreadVisual(p) {
  const oudShot = p.oudUrl
    ? `<figure class="shot shot--old">
         <figcaption><span class="caption-label">Huidige site</span><span class="caption-url">${p.oudUrl}</span></figcaption>
         <div class="shot-frame">${img('oud', 'desktop', p.id, 'full', 'Huidige site - ' + p.title)}</div>
       </figure>`
    : `<figure class="shot shot--old shot--missing">
         <figcaption><span class="caption-label">Huidige site</span><span class="caption-url">— bestond niet —</span></figcaption>
         <div class="shot-frame">
           <div class="img-missing"><span>Deze pagina bestond niet op de oude site</span></div>
         </div>
       </figure>`;

  const nieuwShot = `
    <figure class="shot shot--new">
      <figcaption><span class="caption-label">Nieuwe site</span><span class="caption-url">${p.nieuwUrl}</span></figcaption>
      <div class="shot-frame shot-frame--annotated">
        ${img('nieuw', 'desktop', p.id, 'full', 'Nieuwe site - ' + p.title)}
        ${renderAnnotations(p.annotations)}
      </div>
    </figure>
  `;

  const oudMobile = p.oudUrl
    ? `<div class="mob"><span class="mob-label">Huidig</span><div class="mob-frame">${img('oud', 'mobile', p.id, 'full', '')}</div></div>`
    : `<div class="mob mob--missing"><span class="mob-label">Huidig</span><div class="mob-frame"><div class="img-missing"><span>—</span></div></div></div>`;

  const nieuwMobile = `<div class="mob"><span class="mob-label">Nieuw</span><div class="mob-frame">${img('nieuw', 'mobile', p.id, 'full', '')}</div></div>`;

  return `
    <article class="page page--visual">
      <header class="page-head page-head--minimal">
        <span class="eyebrow">Pagina ${p.n} — Vergelijking</span>
        <h2 class="page-title-sm">${p.title}</h2>
      </header>

      <div class="visuals">
        ${oudShot}
        ${nieuwShot}
      </div>

      <div class="mobile-strip">
        <span class="mobile-strip-label">Mobiel</span>
        ${oudMobile}
        ${nieuwMobile}
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>${p.n}B</span>
      </footer>
    </article>
  `;
}

// ─── Vaste pagina's (cover, voorwoord, filosofie, etc.) ─────────────────────

function renderCover(today) {
  return `
    <article class="page page--cover">
      <div class="cover-grid"></div>
      <div class="cover-content">
        <span class="cover-eyebrow">Vertrouwelijk · Strategisch document</span>
        <h1 class="cover-title">Floru.nl</h1>
        <p class="cover-sub">Een nieuwe digitale fundering</p>
        <div class="cover-line"></div>
        <p class="cover-desc">Vergelijking van de huidige website met het herontwerp.<br>Visuele analyse, ontwerpkeuzes en wat het oplevert.</p>
      </div>
      <footer class="cover-foot">
        <div>
          <span class="cover-foot-label">Opgesteld voor</span>
          <span class="cover-foot-value">Florian de Lange</span>
        </div>
        <div>
          <span class="cover-foot-label">Datum</span>
          <span class="cover-foot-value">${today}</span>
        </div>
        <div>
          <span class="cover-foot-label">Status</span>
          <span class="cover-foot-value">Definitief</span>
        </div>
      </footer>
    </article>
  `;
}

function renderForeword() {
  return `
    <article class="page page--text">
      <header class="page-head">
        <span class="eyebrow">Voorwoord</span>
        <h2 class="page-title">Dit rapport in 60 seconden</h2>
      </header>

      <div class="page-body page-body--prose">
        <p class="lead lead--dropcap">De website van Floru is volledig opnieuw opgebouwd. Niet als visuele facelift, maar als fundament: een platform dat past bij wie Floru in 2026 is — een internationaal opererende consultancy in defensie en veiligheid, met klanten die kwaliteit verwachten vanaf het eerste klik-moment.</p>

        <p>Dit document zet de huidige site naast de nieuwe site. Per pagina ziet u twee dingen: <em>wat</em> er veranderd is, en — belangrijker — <em>waarom</em>. De keuzes voor kleur, typografie, structuur en techniek worden in mensentaal toegelicht, zodat u zelf kunt beoordelen of het herontwerp doet wat het beoogt.</p>

        <p>Het rapport is opgebouwd uit drie delen. Eerst de <strong>ontwerpfilosofie</strong>: het verhaal achter de keuzes. Daarna de <strong>pagina-vergelijkingen</strong>: per pagina een spread met uitleg, screenshots en gemarkeerde detailpunten. Tot slot een blok over <strong>mobiel, tweetaligheid en techniek</strong> — onderwerpen die niet zichtbaar zijn op één enkele pagina, maar wel het verschil maken voor de bezoeker en voor Floru zelf.</p>

        <p>Neem rustig de tijd. Het document is bedoeld om door te bladeren én om bij stil te staan.</p>

        <div class="reading-guide">
          <h3 class="block-title">Hoe leest u dit document</h3>
          <ul class="guide-list">
            <li><span class="guide-mark">←</span> <span><strong>Linker pagina van een spread:</strong> uitleg, context en waarom.</span></li>
            <li><span class="guide-mark">→</span> <span><strong>Rechter pagina van een spread:</strong> screenshots — boven het oude beeld, daaronder het nieuwe.</span></li>
            <li><span class="guide-mark anno-mark">①</span> <span><strong>Genummerde cirkels op de afbeeldingen:</strong> verwijzen naar de uitleg op de linker pagina.</span></li>
            <li><span class="guide-mark">▢</span> <span><strong>Mobiele preview onderaan:</strong> hoe dezelfde pagina er op een telefoon uitziet.</span></li>
          </ul>
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>i</span>
      </footer>
    </article>
  `;
}

function renderCoreIdea() {
  return `
    <article class="page page--text page--core">
      <header class="page-head">
        <span class="eyebrow">Het kernidee</span>
        <h2 class="page-title">Drie ankers van het herontwerp</h2>
      </header>

      <div class="page-body">
        <div class="anchors">
          <div class="anchor">
            <span class="anchor-num">01</span>
            <h3>Identiteit</h3>
            <p>Een visuele taal die ernst, vakmanschap en rust uitstraalt — zoals de markten waarin Floru opereert dat verlangen.</p>
          </div>
          <div class="anchor">
            <span class="anchor-num">02</span>
            <h3>Vertrouwen</h3>
            <p>Bewijs in plaats van beloftes: cases, klanten en mensen krijgen ruimte. Geen marketingtaal, wel concrete signalen.</p>
          </div>
          <div class="anchor">
            <span class="anchor-num">03</span>
            <h3>Bereik</h3>
            <p>Volledig tweetalig (NL/EN), volledig mobiel, klaar voor zoekmachines — Floru is internationaal, de site nu ook.</p>
          </div>
        </div>

        <div class="core-summary">
          <p class="core-summary-text">
            Van een verouderde site naar een platform dat past bij wie Floru in 2026 is.
          </p>
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>ii</span>
      </footer>
    </article>
  `;
}

function renderColors() {
  const swatches = [
    { name: 'Floru Navy',    hex: '#1B2A4A', text: '#FFFFFF', why: 'De kleur van uniformen, marine en bestuur. Communiceert ernst, betrouwbaarheid en gezag. Voor een consultancy in defensie en veiligheid is dit geen modekeuze maar een industrie-conventie — én precies waar de markt op vertrouwt.' },
    { name: 'Floru Goud',    hex: '#C9913B', text: '#FFFFFF', why: 'Een warm, getemperd goud — geen neon, geen glitter. Suggereert kwaliteit, zorgvuldigheid en exclusiviteit zonder opzichtig te zijn. Wordt spaarzaam ingezet (knoppen, accenten); juist dáár krijgt het kracht.' },
    { name: 'Charcoal',      hex: '#2D3748', text: '#FFFFFF', why: 'Voor koppen en hoofdtekst. Pure zwart op wit is hard voor de ogen; deze warm-grijze tint leest prettiger en oogt moderner.' },
    { name: 'Slate',         hex: '#4A5568', text: '#FFFFFF', why: 'Voor secundaire tekst en bijschriften. Geeft hiërarchie zonder dat tekst grijs en onleesbaar wordt.' },
    { name: 'Off-white',     hex: '#F7F8FA', text: '#1B2A4A', why: 'Geen kil wit, een nuance warmer. Geeft secties rust en ademruimte zonder de aandacht weg te trekken.' },
  ];

  return `
    <article class="page page--text page--philosophy">
      <header class="page-head">
        <span class="eyebrow">Ontwerpfilosofie · 1 van 3</span>
        <h2 class="page-title">De kleurkeuze, uitgelegd</h2>
      </header>

      <div class="page-body">
        <p class="lead">Kleur is geen decoratie maar communicatie. De volgende vijf kleuren vormen samen de visuele grammatica van Floru.nl. Elke keuze is bewust — hieronder waarom.</p>

        <div class="swatches">
          ${swatches.map(s => `
            <div class="swatch">
              <div class="swatch-color" style="background:${s.hex};color:${s.text};">
                <span class="swatch-name">${s.name}</span>
                <span class="swatch-hex">${s.hex}</span>
              </div>
              <p class="swatch-why">${s.why}</p>
            </div>
          `).join('')}
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>iii</span>
      </footer>
    </article>
  `;
}

function renderTypography() {
  return `
    <article class="page page--text page--philosophy">
      <header class="page-head">
        <span class="eyebrow">Ontwerpfilosofie · 2 van 3</span>
        <h2 class="page-title">Eén lettertype, vele rollen</h2>
      </header>

      <div class="page-body">
        <p class="lead">De hele site is opgebouwd in <strong>Inter</strong> — één lettertype dat van enorme koppen tot kleine bijschriften scherp blijft. Hieronder waarom dat een bewuste keuze is.</p>

        <div class="type-specimen">
          <div class="type-display">
            <span class="type-tag">96 px · Display</span>
            <span class="type-sample type-sample--display">Strategisch.</span>
          </div>
          <div class="type-display">
            <span class="type-tag">36 px · Heading</span>
            <span class="type-sample type-sample--heading">Defence &amp; Security Consultancy</span>
          </div>
          <div class="type-display">
            <span class="type-tag">16 px · Body</span>
            <span class="type-sample type-sample--body">Floru helpt internationale bedrijven zich te oriënteren in complexe overheidsmarkten, relaties op te bouwen en aanbestedingen te winnen.</span>
          </div>
          <div class="type-display">
            <span class="type-tag">11 px · Eyebrow</span>
            <span class="type-sample type-sample--micro">DEFENCE · SECURITY · STRATEGY</span>
          </div>
        </div>

        <div class="type-why">
          <h3 class="block-title">Waarom Inter?</h3>
          <p>Inter is ontworpen door Rasmus Andersson en wordt gebruikt door Apple App Store, GitHub, Mozilla en duizenden andere serieuze toepassingen. Het is een neutrale, modern-humanistische sans-serif: geen mode-letter, geen quasi-literair serif. Dat past bij een consultancy die strategisch advies levert — niet bij een lifestyle-merk en niet bij een traditioneel notariskantoor.</p>
          <p>Door één lettertype-familie in meerdere gewichten te gebruiken (regular, medium, semibold, bold) ontstaat hiërarchie zonder visuele ruis. Dat schept rust en sluit aan bij de premium-positionering: één goed gekozen instrument, virtuoos bespeeld.</p>
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>iv</span>
      </footer>
    </article>
  `;
}

function renderRhythm() {
  return `
    <article class="page page--text page--philosophy">
      <header class="page-head">
        <span class="eyebrow">Ontwerpfilosofie · 3 van 3</span>
        <h2 class="page-title">Witruimte als statement</h2>
      </header>

      <div class="page-body">
        <p class="lead">De duurste merken ter wereld — van Apple tot Hermès — delen één eigenschap: ze laten ruimte. Witruimte is geen leegte, het is een signaal van zelfvertrouwen.</p>

        <div class="rhythm-compare">
          <div class="rhythm-side">
            <span class="rhythm-label">Voorheen</span>
            <div class="rhythm-mock rhythm-mock--busy">
              <div class="m-bar"></div><div class="m-bar"></div><div class="m-bar"></div>
              <div class="m-block"></div>
              <div class="m-bar"></div><div class="m-bar"></div>
              <div class="m-block m-block--small"></div>
              <div class="m-bar"></div>
            </div>
            <p class="rhythm-caption">Veel elementen, weinig adem. Ogen weten niet waar ze moeten landen.</p>
          </div>
          <div class="rhythm-side">
            <span class="rhythm-label">Nu</span>
            <div class="rhythm-mock rhythm-mock--calm">
              <div class="m-bar m-bar--wide"></div>
              <div class="m-block m-block--big"></div>
              <div class="m-bar m-bar--narrow"></div>
            </div>
            <p class="rhythm-caption">Weinig elementen, veel ruimte. Elke regel krijgt aandacht.</p>
          </div>
        </div>

        <div class="rhythm-quote">
          <p>Een rustige pagina vraagt impliciet om vertrouwen. Floru hoeft de bezoeker niet te overtuigen met luidruchtigheid — de stilte tussen de elementen doet het werk.</p>
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>v</span>
      </footer>
    </article>
  `;
}

function renderNewsNote() {
  return `
    <article class="page page--text page--note">
      <header class="page-head">
        <span class="eyebrow">Pagina 08 — Toelichting</span>
        <h2 class="page-title">News — bewust niet overgenomen</h2>
      </header>

      <div class="page-body page-body--prose">
        <p class="lead">Niet elke pagina van de oude site verdient een plek in het herontwerp. De News-sectie is hier een voorbeeld van.</p>

        <p>De nieuwsberichten op de oude site dateren grotendeels uit 2019 en 2020. Een nieuwspagina die jaren stil staat werkt averechts: het suggereert dat de organisatie inactief is, terwijl het tegendeel waar is. Daarom is bewust gekozen om News niet over te nemen in de eerste versie van de nieuwe site.</p>

        <p>Mocht Floru in de toekomst structureel nieuwsberichten of inzichten willen publiceren — bijvoorbeeld marktanalyses of beleidsupdates — dan is de techniek erop voorbereid. Een nieuws- of insights-rubriek kan op elk moment worden toegevoegd zonder herontwerp.</p>

        <div class="callout">
          <span class="callout-label">In één regel</span>
          <p>Liever geen nieuwspagina dan een verlaten nieuwspagina.</p>
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>08</span>
      </footer>
    </article>
  `;
}

function renderMobileIntro() {
  return `
    <article class="page page--text">
      <header class="page-head">
        <span class="eyebrow">Mobiel</span>
        <h2 class="page-title">De helft van uw bezoekers kijkt op een telefoon</h2>
      </header>

      <div class="page-body page-body--prose">
        <p class="lead">Voor B2B-websites in zakelijke sectoren ligt het mobiele aandeel rond de 40 tot 55 procent — en het stijgt nog steeds. Een site die niet werkt op een telefoon, werkt feitelijk niet.</p>

        <p>De oude website was opgebouwd in een tijd waarin de telefoon een bijgedachte was. Tekstblokken vielen naast elkaar, knoppen werden te klein, het menu klapte rommelig open. Bezoekers haakten af voordat ze überhaupt begrepen wat Floru aanbood.</p>

        <p>De nieuwe website is omgekeerd opgebouwd: eerst is bedacht hoe elke pagina op een telefoon moet werken, daarna hoe diezelfde pagina opschaalt naar een groter scherm. Dat heet <em>mobile-first</em>. Het resultaat ziet u op de volgende pagina: drie kerntoegangen — de homepage, het team, en de diensten — naast elkaar, telkens oud links en nieuw rechts.</p>

        <p>Let op de details: hoe de typografie ademt, hoe de afbeeldingen volledig de breedte vullen, hoe het menu een duim-vriendelijke knop is in plaats van een minuscuul icoon. Dit zijn geen cosmetische verschillen — ze bepalen of een bezoeker doorklikt of wegklikt.</p>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>M1</span>
      </footer>
    </article>
  `;
}

function renderMobileGrid() {
  const pages = [
    { id: 'home',     label: 'Home' },
    { id: 'team',     label: 'Team' },
    { id: 'services', label: 'Services',  oudMissing: true },
  ];

  return `
    <article class="page page--visual page--mobile-grid">
      <header class="page-head page-head--minimal">
        <span class="eyebrow">Mobiel — Vergelijking</span>
        <h2 class="page-title-sm">Drie kerntoegangen op de telefoon</h2>
      </header>

      <div class="mobile-grid">
        ${pages.map(p => `
          <div class="mobile-grid-cell">
            <h3 class="mobile-grid-title">${p.label}</h3>
            <div class="mobile-grid-pair">
              ${p.oudMissing
                ? `<div class="mob mob--missing"><span class="mob-label">Huidig</span><div class="mob-frame"><div class="img-missing"><span>Bestond niet</span></div></div></div>`
                : `<div class="mob"><span class="mob-label">Huidig</span><div class="mob-frame">${img('oud', 'mobile', p.id, 'full', '')}</div></div>`}
              <div class="mob mob--accent"><span class="mob-label">Nieuw</span><div class="mob-frame">${img('nieuw', 'mobile', p.id, 'full', '')}</div></div>
            </div>
          </div>
        `).join('')}
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>M2</span>
      </footer>
    </article>
  `;
}

function renderBilingual() {
  return `
    <article class="page page--visual page--bilingual">
      <header class="page-head">
        <span class="eyebrow">Tweetaligheid</span>
        <h2 class="page-title">Eén klik wisselt taal</h2>
      </header>

      <div class="page-body page-body--compact">
        <p class="lead">Floru werkt voor internationale klanten. De nieuwe site is volledig tweetalig opgezet: elke pagina, elke knop, elk veld bestaat in zowel Engels als Nederlands. De bezoeker kiest in de header — de inhoud past zich aan.</p>
      </div>

      <div class="lang-pair">
        <figure class="shot shot--lang">
          <figcaption><span class="caption-label">Engels</span><span class="caption-url">florunl.local/</span></figcaption>
          <div class="shot-frame">${img('nieuw', 'desktop', 'home', 'full', 'Engelse versie')}</div>
        </figure>
        <figure class="shot shot--lang shot--lang-nl">
          <figcaption><span class="caption-label">Nederlands</span><span class="caption-url">florunl.local/nl/</span></figcaption>
          <div class="shot-frame">${img('nieuw-nl', 'desktop', 'home', 'full', 'Nederlandse versie')}</div>
        </figure>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>T1</span>
      </footer>
    </article>
  `;
}

function renderEngineroom1() {
  const items = [
    { label: 'Eigen onderhoud', text: 'Voorheen was de site opgebouwd met een externe paginabouwer die alleen door specialisten goed te beheren was. Een nieuwe medewerker toevoegen of een logo wijzigen kostte een mailtje, een wachttijd en soms geld. <strong>Nu</strong> staan team, klanten en teksten in een eigen, helder beheermenu binnen WordPress. Iedereen die met een tekstverwerker overweg kan, kan de site bijhouden.' },
    { label: 'Snelheid',         text: 'De oude site laadde traag, vooral op mobiele verbindingen. Beelden waren niet geoptimaliseerd, het thema droeg overtollige code mee. <strong>Nu</strong> is de site lichtgewicht: pagina\'s openen in een fractie van een seconde, ook op een matige verbinding onderweg. Snelheid is geen technische luxe — het bepaalt of een bezoeker blijft.' },
  ];

  return `
    <article class="page page--text">
      <header class="page-head">
        <span class="eyebrow">Onder de motorkap · 1 van 2</span>
        <h2 class="page-title">Wat u niet ziet, maar wel merkt</h2>
      </header>

      <div class="page-body">
        <p class="lead">Een goede website lijkt vanzelfsprekend. Dat is precies de bedoeling — de complexiteit zit eronder. Hieronder vier dingen die niet zichtbaar zijn op één scherm, maar bepalend voor het dagelijks gebruik. Eerst twee.</p>

        <div class="engine-list">
          ${items.map(it => `
            <div class="engine-item">
              <h3 class="engine-label">${it.label}</h3>
              <p>${it.text}</p>
            </div>
          `).join('')}
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>E1</span>
      </footer>
    </article>
  `;
}

function renderEngineroom2() {
  const items = [
    { label: 'Vindbaarheid',     text: 'Google houdt van schone webadressen, juiste tags en snelle pagina\'s. De oude site faalde op alle drie. <strong>Nu</strong> heeft elke pagina een logisch adres (zoals <em>/client/cnim/</em>), de juiste meta-informatie voor zoekmachines, en laadtijden die Google beloont met hogere posities. Dat betekent: meer organische bezoekers zonder advertentiebudget.' },
    { label: 'Toegankelijkheid', text: 'Wettelijk en moreel: een site moet ook werken voor mensen die slecht zien, kleurenblind zijn, of een schermlezer gebruiken. <strong>Nu</strong> voldoet de site aan internationale toegankelijkheidsstandaarden (WCAG): voldoende contrast, toetsenbord-navigatie, alt-teksten op afbeeldingen, en respect voor systeem-instellingen zoals "minder beweging". Onzichtbaar voor de meeste bezoekers — onmisbaar voor wie het nodig heeft.' },
  ];

  return `
    <article class="page page--text">
      <header class="page-head">
        <span class="eyebrow">Onder de motorkap · 2 van 2</span>
        <h2 class="page-title">En nog twee dingen</h2>
      </header>

      <div class="page-body">
        <div class="engine-list">
          ${items.map(it => `
            <div class="engine-item">
              <h3 class="engine-label">${it.label}</h3>
              <p>${it.text}</p>
            </div>
          `).join('')}
        </div>

        <div class="callout">
          <span class="callout-label">Samengevat</span>
          <p>Eigenaar — sneller — beter vindbaar — voor iedereen bruikbaar. Dat zijn de vier eigenschappen die u niet ziet op één pagina, maar die elke dag het verschil maken.</p>
        </div>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>E2</span>
      </footer>
    </article>
  `;
}

function renderOutcome() {
  return `
    <article class="page page--text page--outcome">
      <header class="page-head">
        <span class="eyebrow">Wat dit oplevert</span>
        <h2 class="page-title">Drie horizonten</h2>
      </header>

      <div class="page-body">
        <div class="outcome-grid">
          <div class="outcome-card">
            <span class="outcome-icon">→</span>
            <h3>Voor de bezoeker</h3>
            <p>Begrijpt binnen drie seconden wie Floru is en wat er wordt aangeboden. Vindt sneller wat hij zoekt, op elk apparaat, in zijn eigen taal.</p>
          </div>
          <div class="outcome-card">
            <span class="outcome-icon">→</span>
            <h3>Voor Floru intern</h3>
            <p>Eigenaarschap. Team, klanten en teksten zijn zelf bij te werken — geen wachttijd, geen externe afhankelijkheid, geen kosten per kleine wijziging.</p>
          </div>
          <div class="outcome-card">
            <span class="outcome-icon">→</span>
            <h3>Voor de toekomst</h3>
            <p>Een fundament dat meegroeit. Nieuwe diensten, talen, of een eventuele nieuws- of inzichten-rubriek kunnen worden toegevoegd zonder opnieuw te bouwen.</p>
          </div>
        </div>

        <blockquote class="closing-quote">
          <p>De website is niet af. Een goede website is dat nooit. Maar het fundament staat — stevig, schoon, en klaar voor wat Floru de komende jaren wil bouwen.</p>
        </blockquote>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>R1</span>
      </footer>
    </article>
  `;
}

function renderColophon(today) {
  return `
    <article class="page page--text page--colophon">
      <header class="page-head">
        <span class="eyebrow">Colofon</span>
        <h2 class="page-title">Verantwoording</h2>
      </header>

      <div class="page-body">
        <dl class="colophon-list">
          <div><dt>Onderwerp</dt><dd>Vergelijkingsrapport Floru.nl redesign</dd></div>
          <div><dt>Opgesteld voor</dt><dd>Florian de Lange</dd></div>
          <div><dt>Datum</dt><dd>${today}</dd></div>
          <div><dt>Vergeleken</dt><dd>www.floru.nl (huidige site) en florunl.local (herontwerp)</dd></div>
          <div><dt>Screenshots</dt><dd>Geautomatiseerd vastgelegd op desktop (1440 px) en mobiel (375 px)</dd></div>
          <div><dt>Typografie rapport</dt><dd>Inter — dezelfde familie als de site zelf</dd></div>
          <div><dt>Kleuren rapport</dt><dd>Floru Navy #1B2A4A · Floru Goud #C9913B — afkomstig uit het ontwerpsysteem van de site</dd></div>
          <div><dt>Format</dt><dd>Zelfstandige HTML — printbaar als PDF via Ctrl+P / Cmd+P</dd></div>
          <div><dt>Status</dt><dd>Definitief</dd></div>
        </dl>

        <p class="colophon-tail">Dit document is opgesteld om — naast de site zelf — als zelfstandig stuk inhoud te kunnen functioneren: voor intern gebruik, voor presentatie aan derden, of als naslag bij toekomstige beslissingen over de digitale aanwezigheid van Floru.</p>
      </div>

      <footer class="page-foot">
        <span>Floru.nl — Redesign Rapport</span>
        <span>—</span>
      </footer>
    </article>
  `;
}

// ─── CSS ────────────────────────────────────────────────────────────────────

function renderCSS() {
  return `
    :root {
      --navy: #1B2A4A;
      --navy-dark: #0F1B33;
      --navy-soft: #2A3A5E;
      --charcoal: #2D3748;
      --slate: #4A5568;
      --muted: #718096;
      --gold: #C9913B;
      --gold-soft: #E8D5B0;
      --gold-deep: #A8772E;
      --off-white: #F7F8FA;
      --white: #FFFFFF;
      --hairline: rgba(27, 42, 74, 0.08);
      --hairline-strong: rgba(27, 42, 74, 0.18);

      --serif: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --sans:  'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

      --page-w: 210mm;
      --page-h: 297mm;
      --page-pad: 22mm;
    }

    * { box-sizing: border-box; }
    html, body {
      margin: 0; padding: 0;
      background: #e9ebef;
      font-family: var(--sans);
      color: var(--charcoal);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      font-feature-settings: 'kern' 1, 'liga' 1, 'calt' 1;
    }

    .page {
      width: var(--page-w);
      min-height: var(--page-h);
      margin: 12mm auto;
      background: var(--white);
      padding: var(--page-pad);
      position: relative;
      box-shadow: 0 8px 28px rgba(15, 27, 51, 0.12);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      page-break-after: always;
      break-after: page;
    }

    /* ── Headers / footers ── */
    .page-head {
      border-bottom: 1px solid var(--hairline);
      padding-bottom: 14mm;
      margin-bottom: 12mm;
    }
    .page-head--minimal {
      padding-bottom: 6mm;
      margin-bottom: 8mm;
    }
    .eyebrow {
      display: inline-block;
      font-size: 9.5pt;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 600;
      margin-bottom: 8mm;
    }
    .page-title {
      font-size: 32pt;
      line-height: 1.1;
      font-weight: 600;
      color: var(--navy);
      margin: 0;
      letter-spacing: -0.01em;
    }
    .page-title-sm {
      font-size: 18pt;
      font-weight: 600;
      color: var(--navy);
      margin: 0;
      letter-spacing: -0.005em;
    }
    .page-foot {
      position: absolute;
      bottom: 12mm;
      left: var(--page-pad);
      right: var(--page-pad);
      display: flex;
      justify-content: space-between;
      font-size: 8pt;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--muted);
      border-top: 1px solid var(--hairline);
      padding-top: 4mm;
    }

    /* ── Body / typography ── */
    .page-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 10mm;
    }
    .page-body--prose { gap: 6mm; }
    .page-body--compact { gap: 4mm; }

    .lead {
      font-size: 13pt;
      line-height: 1.55;
      color: var(--charcoal);
      margin: 0;
      max-width: 165mm;
    }
    .lead--dropcap::first-letter {
      float: left;
      font-size: 56pt;
      line-height: 0.9;
      padding: 4pt 8pt 0 0;
      color: var(--navy);
      font-weight: 600;
    }
    .page-body p {
      font-size: 11pt;
      line-height: 1.65;
      color: var(--slate);
      margin: 0;
    }
    .block { display: flex; flex-direction: column; gap: 4mm; }
    .block-title {
      font-size: 9.5pt;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--navy);
      font-weight: 600;
      margin: 0;
    }
    .bullets {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 3mm;
    }
    .bullets li {
      font-size: 11pt;
      line-height: 1.55;
      color: var(--slate);
      padding-left: 8mm;
      position: relative;
    }
    .bullets li::before {
      content: '';
      position: absolute;
      left: 0; top: 0.7em;
      width: 4mm; height: 1px;
      background: var(--gold);
    }

    /* ── Cover ── */
    .page--cover {
      background: linear-gradient(140deg, var(--navy-dark) 0%, var(--navy) 60%, #142141 100%);
      color: var(--white);
      padding: 0;
      justify-content: space-between;
    }
    .cover-grid {
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(to right, rgba(255,255,255,0.04) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255,255,255,0.04) 1px, transparent 1px),
        radial-gradient(ellipse at 20% 80%, rgba(201, 145, 59, 0.12) 0%, transparent 55%);
      background-size: 40mm 40mm, 40mm 40mm, 100% 100%;
      pointer-events: none;
    }
    .cover-content {
      position: relative;
      z-index: 1;
      padding: 40mm var(--page-pad) 0;
    }
    .cover-eyebrow {
      display: inline-block;
      font-size: 9pt;
      letter-spacing: 0.32em;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 500;
      margin-bottom: 20mm;
    }
    .cover-title {
      font-size: 96pt;
      line-height: 0.95;
      font-weight: 500;
      letter-spacing: -0.03em;
      color: var(--white);
      margin: 0 0 6mm;
    }
    .cover-sub {
      font-size: 22pt;
      font-weight: 300;
      color: rgba(255,255,255,0.85);
      margin: 0 0 18mm;
      letter-spacing: -0.005em;
    }
    .cover-line {
      width: 60mm;
      height: 2px;
      background: var(--gold);
      margin-bottom: 12mm;
    }
    .cover-desc {
      font-size: 12pt;
      line-height: 1.7;
      color: rgba(255,255,255,0.72);
      max-width: 130mm;
      margin: 0;
    }
    .cover-foot {
      position: relative;
      z-index: 1;
      padding: 0 var(--page-pad) 30mm;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12mm;
      border-top: 1px solid rgba(255,255,255,0.12);
      padding-top: 10mm;
      margin: 0 var(--page-pad);
    }
    .cover-foot-label {
      display: block;
      font-size: 8pt;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.5);
      margin-bottom: 3mm;
    }
    .cover-foot-value {
      display: block;
      font-size: 12pt;
      font-weight: 500;
      color: var(--white);
    }

    /* ── Reading guide / voorwoord ── */
    .reading-guide {
      margin-top: 6mm;
      padding: 8mm 10mm;
      background: var(--off-white);
      border-left: 3px solid var(--gold);
    }
    .guide-list {
      list-style: none;
      padding: 0;
      margin: 5mm 0 0;
      display: flex;
      flex-direction: column;
      gap: 4mm;
    }
    .guide-list li {
      display: flex;
      gap: 6mm;
      align-items: baseline;
      font-size: 10.5pt;
      line-height: 1.55;
      color: var(--slate);
    }
    .guide-mark {
      flex-shrink: 0;
      width: 8mm;
      font-size: 14pt;
      color: var(--navy);
      font-weight: 500;
      text-align: center;
    }
    .guide-mark.anno-mark { color: var(--gold); }

    /* ── Core idea / anchors ── */
    .anchors {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8mm;
      margin-top: 8mm;
    }
    .anchor {
      padding: 10mm 8mm;
      border: 1px solid var(--hairline);
      border-top: 3px solid var(--gold);
    }
    .anchor-num {
      display: block;
      font-size: 28pt;
      font-weight: 300;
      color: var(--gold);
      letter-spacing: -0.02em;
      margin-bottom: 6mm;
      line-height: 1;
    }
    .anchor h3 {
      font-size: 14pt;
      color: var(--navy);
      margin: 0 0 4mm;
      font-weight: 600;
    }
    .anchor p {
      font-size: 10pt;
      line-height: 1.55;
      color: var(--slate);
      margin: 0;
    }
    .core-summary {
      margin-top: auto;
      padding: 10mm;
      background: var(--navy);
      color: var(--white);
      text-align: center;
    }
    .core-summary-text {
      font-size: 16pt;
      font-weight: 300;
      line-height: 1.4;
      letter-spacing: -0.005em;
      color: var(--white);
      margin: 0;
      font-style: italic;
    }

    /* ── Swatches ── */
    .swatches {
      display: flex;
      flex-direction: column;
      gap: 6mm;
    }
    .swatch {
      display: grid;
      grid-template-columns: 60mm 1fr;
      gap: 8mm;
      align-items: stretch;
    }
    .swatch-color {
      padding: 8mm;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 32mm;
    }
    .swatch-name {
      font-size: 11pt;
      font-weight: 600;
      letter-spacing: -0.005em;
    }
    .swatch-hex {
      font-size: 10pt;
      font-family: 'SFMono-Regular', Consolas, 'Courier New', monospace;
      letter-spacing: 0.04em;
      opacity: 0.85;
    }
    .swatch-why {
      font-size: 10pt;
      line-height: 1.6;
      color: var(--slate);
      margin: 0;
      align-self: center;
    }

    /* ── Type specimen ── */
    .type-specimen {
      display: flex;
      flex-direction: column;
      gap: 6mm;
      padding: 10mm;
      background: var(--off-white);
      border-left: 3px solid var(--gold);
    }
    .type-display {
      display: grid;
      grid-template-columns: 30mm 1fr;
      gap: 6mm;
      align-items: baseline;
      padding-bottom: 4mm;
      border-bottom: 1px solid var(--hairline);
    }
    .type-display:last-child { border-bottom: none; padding-bottom: 0; }
    .type-tag {
      font-size: 8pt;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--muted);
      font-weight: 500;
    }
    .type-sample { color: var(--navy); display: block; }
    .type-sample--display { font-size: 36pt; font-weight: 600; line-height: 1; letter-spacing: -0.02em; }
    .type-sample--heading { font-size: 18pt; font-weight: 600; letter-spacing: -0.005em; }
    .type-sample--body { font-size: 11pt; font-weight: 400; line-height: 1.6; color: var(--slate); }
    .type-sample--micro { font-size: 9pt; letter-spacing: 0.22em; text-transform: uppercase; color: var(--gold); font-weight: 600; }

    .type-why { display: flex; flex-direction: column; gap: 4mm; }
    .type-why p { font-size: 11pt; line-height: 1.65; color: var(--slate); margin: 0; }

    /* ── Rhythm mock ── */
    .rhythm-compare {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10mm;
      margin-top: 4mm;
    }
    .rhythm-side { display: flex; flex-direction: column; gap: 4mm; }
    .rhythm-label {
      font-size: 9pt;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 600;
    }
    .rhythm-mock {
      border: 1px solid var(--hairline);
      padding: 6mm;
      min-height: 80mm;
      display: flex;
      flex-direction: column;
      gap: 3mm;
    }
    .rhythm-mock--busy { gap: 2mm; }
    .rhythm-mock--calm { gap: 8mm; padding: 12mm; }
    .m-bar { height: 3mm; background: var(--hairline-strong); border-radius: 1mm; }
    .m-bar--wide { width: 80%; }
    .m-bar--narrow { width: 40%; }
    .m-block { height: 18mm; background: var(--hairline); border-radius: 1mm; }
    .m-block--small { height: 10mm; width: 60%; }
    .m-block--big { height: 30mm; }
    .rhythm-caption { font-size: 9.5pt; line-height: 1.5; color: var(--slate); margin: 0; font-style: italic; }
    .rhythm-quote {
      margin-top: auto;
      padding: 8mm 10mm;
      border-left: 3px solid var(--gold);
      background: var(--off-white);
    }
    .rhythm-quote p {
      font-size: 12pt;
      line-height: 1.6;
      color: var(--navy);
      margin: 0;
      font-style: italic;
    }

    /* ── Annotation legend ── */
    .anno-legend {
      list-style: none;
      counter-reset: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 5mm;
    }
    .anno-legend li {
      display: grid;
      grid-template-columns: 9mm 1fr;
      gap: 4mm;
      align-items: start;
    }
    .anno-num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 8mm;
      height: 8mm;
      border-radius: 50%;
      background: var(--navy);
      color: var(--white);
      font-size: 10pt;
      font-weight: 600;
      box-shadow: 0 0 0 1.5px var(--gold);
      flex-shrink: 0;
    }
    .anno-legend strong {
      font-size: 10.5pt;
      color: var(--navy);
      font-weight: 600;
      display: block;
      margin-bottom: 1mm;
    }
    .anno-legend p {
      font-size: 9.5pt;
      line-height: 1.5;
      color: var(--slate);
      margin: 0;
    }

    /* ── Visual page (screenshots side-by-side STACKED) ── */
    .page--visual { gap: 6mm; }
    .visuals {
      display: flex;
      flex-direction: column;
      gap: 8mm;
      flex: 1;
    }
    .shot {
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 3mm;
    }
    .shot figcaption {
      display: flex;
      align-items: baseline;
      gap: 4mm;
    }
    .caption-label {
      font-size: 8.5pt;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      font-weight: 600;
    }
    .shot--old .caption-label { color: var(--muted); }
    .shot--new .caption-label { color: var(--gold); }
    .shot--lang .caption-label { color: var(--navy); }
    .caption-url {
      font-family: 'SFMono-Regular', Consolas, monospace;
      font-size: 8pt;
      color: var(--slate);
      letter-spacing: 0.02em;
    }
    .shot-frame {
      position: relative;
      border: 1px solid var(--hairline-strong);
      background: var(--off-white);
      box-shadow: 0 4px 18px rgba(15, 27, 51, 0.06);
      overflow: hidden;
      max-height: 105mm;
    }
    .shot-frame img {
      display: block;
      width: 100%;
      height: auto;
      max-height: 105mm;
      object-fit: cover;
      object-position: top center;
    }
    .shot--old .shot-frame { border-top: 2px solid var(--muted); }
    .shot--new .shot-frame { border-top: 2px solid var(--gold); }

    /* Annotation circles on screenshots */
    .anno {
      position: absolute;
      width: 8mm;
      height: 8mm;
      margin: -4mm 0 0 -4mm;
      border-radius: 50%;
      background: var(--navy);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10pt;
      font-weight: 700;
      box-shadow: 0 0 0 1.5px var(--gold), 0 2px 6px rgba(0,0,0,0.25);
      z-index: 2;
      print-color-adjust: exact;
      -webkit-print-color-adjust: exact;
    }

    .img-missing {
      width: 100%;
      min-height: 60mm;
      display: flex;
      align-items: center;
      justify-content: center;
      background: repeating-linear-gradient(45deg, var(--off-white) 0 8px, #fff 8px 16px);
      color: var(--muted);
      font-size: 9pt;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    /* Mobile preview strip onderaan visual page */
    .mobile-strip {
      display: flex;
      align-items: stretch;
      gap: 6mm;
      padding-top: 6mm;
      border-top: 1px solid var(--hairline);
    }
    .mobile-strip-label {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
      font-size: 8.5pt;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--muted);
      font-weight: 600;
      align-self: stretch;
      display: flex;
      align-items: center;
    }
    .mob {
      display: flex;
      flex-direction: column;
      gap: 2mm;
      width: 28mm;
    }
    .mob-label {
      font-size: 7.5pt;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--muted);
      font-weight: 600;
    }
    .mob-frame {
      border: 1px solid var(--hairline-strong);
      border-radius: 3mm;
      overflow: hidden;
      background: var(--off-white);
      height: 50mm;
    }
    .mob-frame img {
      display: block;
      width: 100%;
      height: auto;
      object-fit: cover;
      object-position: top center;
    }
    .mob-frame .img-missing { min-height: 100%; height: 100%; font-size: 7pt; }
    .mob--accent .mob-frame { border-color: var(--gold); }

    /* ── Mobile grid page ── */
    .mobile-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8mm;
      flex: 1;
    }
    .mobile-grid-cell { display: flex; flex-direction: column; gap: 4mm; }
    .mobile-grid-title {
      font-size: 12pt;
      color: var(--navy);
      font-weight: 600;
      margin: 0;
      padding-bottom: 2mm;
      border-bottom: 1px solid var(--hairline);
    }
    .mobile-grid-pair { display: flex; gap: 4mm; }
    .mobile-grid-pair .mob { width: auto; flex: 1; }
    .mobile-grid-pair .mob-frame { height: 130mm; }

    /* ── Bilingual ── */
    .lang-pair {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8mm;
      flex: 1;
    }
    .shot--lang .shot-frame { max-height: 130mm; border-top: 2px solid var(--navy); }
    .shot--lang .shot-frame img { max-height: 130mm; }
    .shot--lang-nl .shot-frame { border-top-color: var(--gold); }

    /* ── Engineroom ── */
    .engine-list {
      display: flex;
      flex-direction: column;
      gap: 8mm;
    }
    .engine-item {
      padding: 8mm 10mm;
      background: var(--off-white);
      border-left: 3px solid var(--gold);
    }
    .engine-label {
      font-size: 14pt;
      color: var(--navy);
      font-weight: 600;
      margin: 0 0 4mm;
    }
    .engine-item p {
      font-size: 11pt;
      line-height: 1.65;
      color: var(--slate);
      margin: 0;
    }
    .engine-item p strong { color: var(--navy); font-weight: 600; }

    .callout {
      margin-top: 8mm;
      padding: 8mm 10mm;
      background: var(--navy);
      color: var(--white);
      print-color-adjust: exact;
      -webkit-print-color-adjust: exact;
    }
    .callout-label {
      display: block;
      font-size: 8.5pt;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 600;
      margin-bottom: 3mm;
    }
    .callout p {
      font-size: 12pt;
      line-height: 1.55;
      color: var(--white);
      margin: 0;
    }

    /* ── Outcome ── */
    .outcome-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 6mm;
    }
    .outcome-card {
      padding: 10mm 8mm;
      border: 1px solid var(--hairline);
      display: flex;
      flex-direction: column;
      gap: 4mm;
    }
    .outcome-icon {
      font-size: 20pt;
      color: var(--gold);
      line-height: 1;
    }
    .outcome-card h3 {
      font-size: 13pt;
      color: var(--navy);
      margin: 0;
      font-weight: 600;
    }
    .outcome-card p {
      font-size: 10pt;
      line-height: 1.6;
      color: var(--slate);
      margin: 0;
    }
    .closing-quote {
      margin: auto 0 0;
      padding: 12mm 0 0;
      border-top: 1px solid var(--hairline);
    }
    .closing-quote p {
      font-size: 14pt;
      font-weight: 300;
      line-height: 1.5;
      color: var(--navy);
      margin: 0;
      font-style: italic;
      max-width: 150mm;
    }

    /* ── Colophon ── */
    .colophon-list {
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
    }
    .colophon-list > div {
      display: grid;
      grid-template-columns: 50mm 1fr;
      gap: 6mm;
      padding: 4mm 0;
      border-bottom: 1px solid var(--hairline);
    }
    .colophon-list dt {
      font-size: 9pt;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--muted);
      font-weight: 600;
    }
    .colophon-list dd {
      margin: 0;
      font-size: 11pt;
      color: var(--charcoal);
      line-height: 1.45;
    }
    .colophon-tail {
      margin-top: 10mm;
      font-size: 10pt;
      line-height: 1.65;
      color: var(--slate);
      font-style: italic;
    }

    /* ── Print ── */
    @page {
      size: A4 portrait;
      margin: 0;
    }
    @media print {
      html, body { background: #fff; }
      .page {
        margin: 0;
        box-shadow: none;
        page-break-after: always;
        break-after: page;
      }
      .page:last-child { page-break-after: auto; }
      * { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    }
  `;
}

// ─── Compose ────────────────────────────────────────────────────────────────

function buildHTML() {
  const today = new Date().toLocaleDateString('nl-NL', {
    year: 'numeric', month: 'long', day: 'numeric',
  });

  const spreads = PAGES.map(p => renderSpreadIntro(p) + renderSpreadVisual(p)).join('\n');

  return `<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Floru.nl — Vergelijkingsrapport Redesign</title>
  <style>${renderCSS()}</style>
</head>
<body>
  ${renderCover(today)}
  ${renderForeword()}
  ${renderCoreIdea()}
  ${renderColors()}
  ${renderTypography()}
  ${renderRhythm()}
  ${spreads}
  ${renderNewsNote()}
  ${renderMobileIntro()}
  ${renderMobileGrid()}
  ${renderBilingual()}
  ${renderEngineroom1()}
  ${renderEngineroom2()}
  ${renderOutcome()}
  ${renderColophon(today)}
</body>
</html>`;
}

// ─── Run ────────────────────────────────────────────────────────────────────

console.log('Bezig met genereren rapport…');
const html = buildHTML();
fs.writeFileSync(OUTPUT_FILE, html, 'utf8');
const sizeKb = Math.round(fs.statSync(OUTPUT_FILE).size / 1024);
console.log(`✓ Rapport gegenereerd: ${OUTPUT_FILE}`);
console.log(`  Bestandsgrootte: ${sizeKb} KB`);
console.log(`  Open in browser, dan Ctrl+P → "Save as PDF" voor een nette PDF.`);
