/* Blog archive behaviors — category chip filtering.
   Everything shared lives in site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(160, 80);
  var revealNow = DK.reveal({ threshold: 0.9, tick: 600 });

  var grid = document.getElementById('postGrid');
  var chips = document.querySelectorAll('[data-chip-cat]');
  var cards = grid ? grid.querySelectorAll('[data-cat]') : [];
  var current = 'همه';

  function styleChips() {
    chips.forEach(function (b) {
      var on = b.getAttribute('data-chip-cat') === current;
      b.style.background = on ? 'var(--color-primary)' : '#fff';
      b.style.color = on ? '#fff' : '#3D4A6B';
      b.style.borderColor = on ? 'var(--color-primary)' : 'var(--color-border)';
    });
  }

  function applyFilter() {
    cards.forEach(function (card) {
      var show = current === 'همه' || card.getAttribute('data-cat') === current;
      // Cards are display:flex inline; restore that value when showing.
      card.style.display = show ? 'flex' : 'none';
    });
  }

  chips.forEach(function (b) {
    b.addEventListener('click', function () {
      var c = b.getAttribute('data-chip-cat');
      if (c === current) return;
      current = c;
      styleChips();

      if (DK.reduce || !grid) {
        applyFilter();
        revealNow();
        return;
      }

      // Fade the grid out, swap the visible cards, fade back in.
      grid.style.opacity = '0';
      setTimeout(function () {
        applyFilter();
        grid.style.opacity = '1';
        // Newly shown cards must reveal immediately.
        setTimeout(revealNow, 40);
      }, 180);
    });
  });
});
