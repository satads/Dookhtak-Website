/* Home page behaviors — everything shared lives in site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Signature: hero stitch draws, then hero elements stagger in.
  if (!DK.reduce) {
    var stitch = document.getElementById('heroStitch');
    if (stitch) stitch.style.animation = 'stitchDraw .9s cubic-bezier(.22,.61,.36,1) .25s both';
  }
  DK.hero(380, 80);

  // Tape-measure scroll progress (desktop only).
  var fill = document.getElementById('tapeFill');
  function updateTape() {
    if (!fill) return;
    var h = document.documentElement;
    var max = Math.max(1, h.scrollHeight - h.clientHeight);
    fill.style.width = (Math.min(1, h.scrollTop / max) * 100).toFixed(2) + '%';
  }

  DK.reveal({ threshold: 0.88, tick: 700, initialDelay: 120, onScroll: updateTape });
  updateTape();

  DK.faq('#homeFaq');
});
