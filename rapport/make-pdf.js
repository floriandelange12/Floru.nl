/**
 * make-pdf.js — exporteert rapport.html naar rapport.pdf
 *
 * Gebruikt Playwright-Chromium om de HTML te renderen en als A4-pdf
 * weg te schrijven. Behoudt achtergrondkleuren, page-breaks en alle
 * styling exact zoals in de browser-print preview.
 *
 * Gebruik:   node make-pdf.js
 */

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const HTML_FILE = path.join(__dirname, 'rapport.html');
const PDF_FILE  = path.join(__dirname, 'rapport.pdf');

(async () => {
  if (!fs.existsSync(HTML_FILE)) {
    console.error(`✗ ${HTML_FILE} bestaat niet. Run eerst: node build-report.js`);
    process.exit(1);
  }

  console.log('Bezig met PDF-export…');
  const browser = await chromium.launch();
  const context = await browser.newContext();
  const page    = await context.newPage();

  await page.goto('file:///' + HTML_FILE.replace(/\\/g, '/'), { waitUntil: 'networkidle' });

  await page.pdf({
    path: PDF_FILE,
    format: 'A4',
    printBackground: true,
    preferCSSPageSize: true,
    margin: { top: 0, right: 0, bottom: 0, left: 0 },
  });

  await browser.close();

  const sizeMb = (fs.statSync(PDF_FILE).size / 1024 / 1024).toFixed(1);
  console.log(`✓ PDF gegenereerd: ${PDF_FILE}`);
  console.log(`  Bestandsgrootte: ${sizeMb} MB`);
})();
