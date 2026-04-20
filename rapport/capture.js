/**
 * capture.js — Automated screenshot capture for Floru.nl comparison report
 * 
 * Captures full-page and per-section screenshots of:
 *   - OLD site: www.floru.nl (live production)
 *   - NEW site: florunl.local (local dev, English)
 *   - NEW-NL site: florunl.local?lang=nl (local dev, Dutch)
 */

const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');

// ── Site definitions ────────────────────────────────────────────────────────

const SITES = {
  oud: {
    label: 'Oud (www.floru.nl)',
    baseUrl: 'https://www.floru.nl',
    langSuffix: '',
  },
  nieuw: {
    label: 'Nieuw (EN)',
    baseUrl: 'http://florunl.local',
    langSuffix: '',
  },
  'nieuw-nl': {
    label: 'Nieuw (NL)',
    baseUrl: 'http://florunl.local',
    langSuffix: '?lang=nl',
  },
};

// ── Pages to capture ────────────────────────────────────────────────────────

const PAGES = [
  {
    id: 'home',
    label: 'Home',
    paths: { oud: '/', nieuw: '/', 'nieuw-nl': '/' },
    sections: [
      { id: 'hero',     selector: '.elementor-section:first-child, .floru-hero', scrollTo: true },
      { id: 'nav',      selector: '.elementor-widget-theme-site-logo, .ast-primary-header-bar, header.site-header', scrollTo: false },
    ],
  },
  {
    id: 'about',
    label: 'About / Modus Operandi',
    paths: { oud: '/modus-operandi/', nieuw: '/about/', 'nieuw-nl': '/about/' },
    sections: [
      { id: 'header',   selector: '.elementor-section:first-child, .floru-page-header' },
      { id: 'content',  selector: '.elementor-section:nth-child(2), .floru-story' },
    ],
  },
  {
    id: 'services',
    label: 'Services',
    paths: { oud: null, nieuw: '/services/', 'nieuw-nl': '/services/' },
    sections: [
      { id: 'header',   selector: '.floru-page-header' },
      { id: 'panels',   selector: '.floru-service-panel:first-of-type, .floru-section:nth-child(2)' },
    ],
  },
  {
    id: 'team',
    label: 'Team',
    paths: { oud: '/medewerkers/', nieuw: '/our-team/', 'nieuw-nl': '/our-team/' },
    sections: [
      { id: 'header',   selector: '.elementor-section:first-child, .floru-page-header' },
      { id: 'roster',   selector: '.elementor-section:nth-child(2), .floru-team-roster' },
    ],
  },
  {
    id: 'clients',
    label: 'Clients',
    paths: { oud: '/clients-2/', nieuw: '/clients/', 'nieuw-nl': '/clients/' },
    sections: [
      { id: 'header',   selector: '.elementor-section:first-child, .floru-page-header' },
      { id: 'grid',     selector: '.elementor-section:nth-child(2), .floru-clients-band, .floru-clients-grid' },
    ],
  },
  {
    id: 'client-detail',
    label: 'Client Detail (CNIM)',
    paths: { oud: '/cnim-2/', nieuw: '/client/cnim/', 'nieuw-nl': '/client/cnim/' },
    sections: [
      { id: 'hero', selector: '.elementor-section:first-child, .floru-client-page-hero' },
    ],
  },
  {
    id: 'contact',
    label: 'Contact',
    paths: { oud: '/contact/', nieuw: '/contact/', 'nieuw-nl': '/contact/' },
    sections: [
      { id: 'header',  selector: '.elementor-section:first-child, .floru-page-header' },
      { id: 'form',    selector: '.elementor-section:nth-child(2), .floru-contact-grid, .floru-contact' },
    ],
  },
  {
    id: 'news',
    label: 'News (alleen oud)',
    paths: { oud: '/news/', nieuw: null, 'nieuw-nl': null },
    sections: [],
  },
];

// ── Viewports ───────────────────────────────────────────────────────────────

const VIEWPORTS = [
  { id: 'desktop', width: 1440, height: 900 },
  { id: 'mobile',  width: 375,  height: 812 },
];

// ── Helpers ─────────────────────────────────────────────────────────────────

function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

async function dismissOverlays(page) {
  // Try to close cookie banners, popups, etc.
  const dismissSelectors = [
    '#floraCookieAccept',
    '.floru-cookie-consent__btn--accept',
    '.cookie-notice-accept',
    '.elementor-popup-modal .dialog-close-button',
    '#onetrust-accept-btn-handler',
    '.cc-dismiss',
    '.cc-btn.cc-allow',
  ];
  for (const sel of dismissSelectors) {
    try {
      const el = await page.$(sel);
      if (el && await el.isVisible()) {
        await el.click();
        await page.waitForTimeout(500);
      }
    } catch { /* ignore */ }
  }
}

async function autoScroll(page) {
  await page.evaluate(async () => {
    await new Promise((resolve) => {
      let totalHeight = 0;
      const distance = 400;
      const timer = setInterval(() => {
        window.scrollBy(0, distance);
        totalHeight += distance;
        if (totalHeight >= document.body.scrollHeight) {
          clearInterval(timer);
          resolve();
        }
      }, 100);
    });
  });
  // Scroll back to top
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(500);
}

async function forceReveal(page) {
  await page.addStyleTag({
    content: `
      [data-animate],
      [data-animate-stagger] > * {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
        animation: none !important;
      }
    `,
  });

  await page.evaluate(() => {
    document.querySelectorAll('[data-animate], [data-animate-stagger]').forEach((el) => {
      el.classList.add('is-visible');
    });
  });
}

// ── Main capture logic ──────────────────────────────────────────────────────

async function captureSite(browser, siteKey, site) {
  console.log(`\n${'═'.repeat(60)}`);
  console.log(`  Capturing: ${site.label}`);
  console.log(`${'═'.repeat(60)}`);

  for (const vp of VIEWPORTS) {
    const context = await browser.newContext({
      viewport: { width: vp.width, height: vp.height },
      ignoreHTTPSErrors: true,
      // Spoof a real user-agent to avoid bot-blocking
      userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
    });
    const page = await context.newPage();
    await page.addInitScript(() => {
      try {
        window.localStorage.setItem('floru_cookie_consent', 'accepted');
      } catch {
        // Ignore storage restrictions on third-party origins.
      }
    });

    for (const pg of PAGES) {
      const pagePath = pg.paths[siteKey];
      if (!pagePath) {
        console.log(`  ⊘ ${pg.id} — niet beschikbaar voor ${siteKey}`);
        continue;
      }

      const langSuffix = site.langSuffix;
      const separator = pagePath.includes('?') ? '&' : '?';
      const url = `${site.baseUrl}${pagePath}${langSuffix ? (separator.replace('?', '') === '' ? '?' : separator) + langSuffix.replace('?', '') : ''}`;

      // Fix URL construction for lang suffix
      let fullUrl;
      if (langSuffix) {
        fullUrl = `${site.baseUrl}${pagePath}${pagePath.includes('?') ? '&' : '?'}${langSuffix.replace('?', '')}`;
      } else {
        fullUrl = `${site.baseUrl}${pagePath}`;
      }

      console.log(`  → ${pg.id} (${vp.id}): ${fullUrl}`);

      try {
        await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 30000 });
      } catch (e) {
        console.log(`    ⚠ Timeout/error loading page, continuing with partial content...`);
        // Try with domcontentloaded instead
        try {
          await page.goto(fullUrl, { waitUntil: 'domcontentloaded', timeout: 15000 });
          await page.waitForTimeout(3000);
        } catch {
          console.log(`    ✗ Failed to load ${fullUrl}, skipping.`);
          continue;
        }
      }

      // Dismiss overlays
      await dismissOverlays(page);

      // Auto-scroll to trigger lazy images and animations
      await autoScroll(page);

      // Force animated sections/cards into their visible state for stable report captures.
      await forceReveal(page);

      // Wait for images
      await page.waitForTimeout(900);

      // ── Full-page screenshot ──
      const dir = path.join(SCREENSHOT_DIR, siteKey, vp.id);
      ensureDir(dir);
      const fullPath = path.join(dir, `${pg.id}-full.jpg`);
      await page.screenshot({ path: fullPath, fullPage: true, type: 'jpeg', quality: 80 });
      console.log(`    ✓ full-page → ${path.relative(SCREENSHOT_DIR, fullPath)}`);

      // ── Section screenshots ──
      for (const sec of pg.sections) {
        const selectors = sec.selector.split(', ');
        let captured = false;

        for (const sel of selectors) {
          try {
            const el = await page.$(sel.trim());
            if (el) {
              const box = await el.boundingBox();
              if (box && box.width > 0 && box.height > 0) {
                if (sec.scrollTo !== false) {
                  await el.scrollIntoViewIfNeeded();
                  await page.waitForTimeout(400);
                }
                const secPath = path.join(dir, `${pg.id}-${sec.id}.jpg`);
                await el.screenshot({ path: secPath, type: 'jpeg', quality: 80 });
                console.log(`    ✓ ${sec.id} → ${path.relative(SCREENSHOT_DIR, secPath)}`);
                captured = true;
                break;
              }
            }
          } catch { /* try next selector */ }
        }

        if (!captured) {
          console.log(`    ○ ${sec.id} — geen match gevonden`);
        }
      }
    }

    await context.close();
  }
}

// ── Entry point ─────────────────────────────────────────────────────────────

(async () => {
  console.log('╔══════════════════════════════════════════════════════════════╗');
  console.log('║  Floru.nl Vergelijkingsrapport — Screenshot Capture        ║');
  console.log('╚══════════════════════════════════════════════════════════════╝');
  console.log(`Output: ${SCREENSHOT_DIR}\n`);

  const browser = await chromium.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  try {
    for (const [key, site] of Object.entries(SITES)) {
      await captureSite(browser, key, site);
    }
  } finally {
    await browser.close();
  }

  // ── Summary ──
  console.log(`\n${'═'.repeat(60)}`);
  console.log('  Klaar! Screenshot-overzicht:');
  console.log(`${'═'.repeat(60)}`);

  for (const siteKey of Object.keys(SITES)) {
    for (const vp of VIEWPORTS) {
      const dir = path.join(SCREENSHOT_DIR, siteKey, vp.id);
      if (fs.existsSync(dir)) {
        const files = fs.readdirSync(dir).filter(f => f.endsWith('.jpg'));
        console.log(`  ${siteKey}/${vp.id}: ${files.length} screenshots`);
      }
    }
  }

  console.log('\nDone. Run "node build-report.js" om het rapport te genereren.');
})();
