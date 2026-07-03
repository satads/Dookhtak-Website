#!/usr/bin/env node
/**
 * QA screenshot harness (dev machine only — never deployed).
 * Usage: node qa/screenshot.js <setName>   e.g. baselines | current
 * Captures desktop (1440x2400) + mobile (390x2400) full-height-clipped
 * screenshots of all public pages into qa/<setName>/.
 * Animations/transitions are disabled so diffs are deterministic.
 */
const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const BASE = process.env.QA_BASE_URL || 'http://localhost:8080';
const SET = process.argv[2] || 'current';
const OUT = path.join(__dirname, SET);

const PAGES = [
  ['home', '/'],
  ['features', '/features'],
  ['pricing', '/pricing'],
  ['tutorials', '/tutorials'],
  ['blog', '/blog'],
  ['blog-post', '/blog/pricing-mistakes'],
  ['about', '/about'],
  ['contact', '/contact'],
];

(async () => {
  fs.mkdirSync(OUT, { recursive: true });
  const browser = await chromium.launch({
    executablePath: '/opt/pw-browsers/chromium',
    args: ['--no-sandbox'],
  });
  for (const [w, h, tag] of [[1440, 2400, 'desktop'], [390, 2400, 'mobile']]) {
    const ctx = await browser.newContext({
      viewport: { width: w, height: h },
      reducedMotion: 'reduce', // design shows everything instantly => deterministic
    });
    for (const [name, route] of PAGES) {
      const p = await ctx.newPage();
      await p.goto(BASE + route, { waitUntil: 'networkidle' });
      await p.waitForTimeout(900);
      await p.screenshot({ path: path.join(OUT, `${name}-${tag}.png`) });
      await p.close();
      console.log(`${name}-${tag}.png`);
    }
    await ctx.close();
  }
  await browser.close();
})();
