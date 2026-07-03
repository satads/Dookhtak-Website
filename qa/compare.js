#!/usr/bin/env node
/**
 * QA visual diff (dev machine only).
 * Usage: node qa/compare.js [baselineDir] [currentDir]
 * Pixel-compares each PNG pair; prints per-page diff ratio.
 * Exit code = number of pages exceeding the threshold.
 */
const fs = require('fs');
const path = require('path');
const { PNG } = require('pngjs');

const A = process.argv[2] || path.join(__dirname, 'baselines');
const B = process.argv[3] || path.join(__dirname, 'current');
const THRESHOLD = 0.001; // 0.1% differing pixels tolerated (AA noise)

let failures = 0;
for (const f of fs.readdirSync(A).filter((x) => x.endsWith('.png')).sort()) {
  const fa = path.join(A, f);
  const fb = path.join(B, f);
  if (!fs.existsSync(fb)) {
    console.log(`MISSING  ${f} (no current screenshot)`);
    failures++;
    continue;
  }
  const a = PNG.sync.read(fs.readFileSync(fa));
  const b = PNG.sync.read(fs.readFileSync(fb));
  if (a.width !== b.width || a.height !== b.height) {
    console.log(`SIZE     ${f} ${a.width}x${a.height} vs ${b.width}x${b.height}`);
    failures++;
    continue;
  }
  let diff = 0;
  for (let i = 0; i < a.data.length; i += 4) {
    if (
      Math.abs(a.data[i] - b.data[i]) > 12 ||
      Math.abs(a.data[i + 1] - b.data[i + 1]) > 12 ||
      Math.abs(a.data[i + 2] - b.data[i + 2]) > 12
    ) diff++;
  }
  const ratio = diff / (a.width * a.height);
  const verdict = ratio <= THRESHOLD ? 'OK  ' : 'DIFF';
  if (ratio > THRESHOLD) failures++;
  console.log(`${verdict}     ${f}  ${(ratio * 100).toFixed(3)}% pixels differ`);
}
process.exit(failures);
