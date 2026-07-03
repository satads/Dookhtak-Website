/* About page behaviors — everything shared lives in site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(160, 80);

  // Gentle swing of the hanging care-label — the page's only loop animation.
  if (!DK.reduce) {
    var tag = document.getElementById('tagWrap');
    if (tag) tag.style.animation = 'tagSwing 7s ease-in-out infinite';
  }

  function fillDot(dot) {
    dot.style.background = '#E76F51';
  }

  // Reduced motion: timeline dots fill immediately (DK.showAll skips them).
  if (DK.reduce) {
    document.querySelectorAll('[data-dot]').forEach(fillDot);
  }

  // Scroll reveal; each timeline milestone fills its station dot shortly
  // after the milestone itself appears.
  DK.reveal({
    threshold: 0.9,
    tick: 600,
    onReveal: function (e) {
      var dot = e.querySelector('[data-dot]');
      if (!dot) return;
      var delay = parseInt(e.getAttribute('data-rv-delay') || '0', 10);
      setTimeout(function () { fillDot(dot); }, delay + 260);
    }
  });
});
