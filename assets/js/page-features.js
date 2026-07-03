/* Features page behaviors — journey seam fill, station markers and the
   sticky chapter spy. Everything shared lives in site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(180, 80);

  // Place each numbered station marker on the seam, level with its chapter.
  // Needs measurement, so it stays JS (runs on scroll, resize and the tick).
  function positionStations() {
    var j = document.getElementById('journey');
    var seam = document.getElementById('seam');
    if (!j || !seam) return;
    var jr = j.getBoundingClientRect();
    var sr = seam.getBoundingClientRect();
    var x = sr.left + sr.width / 2 - jr.left;
    document.querySelectorAll('[data-station]').forEach(function (m) {
      var ch = document.getElementById(m.getAttribute('data-for'));
      if (!ch) return;
      m.style.left = (x - 22) + 'px';
      m.style.top = (ch.offsetTop + 4) + 'px';
    });
  }

  // Coral stitch grows down the seam as the journey scrolls by.
  function updateSeam() {
    var j = document.getElementById('journey');
    var f = document.getElementById('seamFill');
    if (!j || !f) return;
    var r = j.getBoundingClientRect();
    var p = Math.min(1, Math.max(0, (window.innerHeight * 0.55 - r.top) / r.height));
    f.style.height = (p * 100).toFixed(2) + '%';
  }

  // Scroll spy: highlight the chip of the chapter currently in view.
  function updateSpy() {
    var vh = window.innerHeight;
    var active = null;
    ['ch1', 'ch2', 'ch3', 'ch4', 'ch5'].forEach(function (id) {
      var s = document.getElementById(id);
      if (s && s.getBoundingClientRect().top < vh * 0.45) active = id;
    });
    document.querySelectorAll('[data-chip]').forEach(function (c) {
      var on = c.getAttribute('data-chip') === active;
      c.style.background = on ? '#E76F51' : 'transparent';
      c.style.color = on ? '#fff' : '#3D4A6B';
    });
  }

  // Station activation with a small pulse when it scrolls into view.
  function checkStations() {
    if (DK.reduce) return;
    var vh = window.innerHeight;
    document.querySelectorAll('[data-station]').forEach(function (m) {
      if (m.getAttribute('data-on')) return;
      var r = m.getBoundingClientRect();
      if (r.top < vh * 0.8 && r.bottom > 0) {
        m.setAttribute('data-on', '1');
        m.style.background = '#E76F51';
        m.style.color = '#fff';
        m.style.transform = 'scale(1.14)';
        setTimeout(function () { m.style.transform = 'scale(1)'; }, 380);
      }
    });
  }

  // Reduced motion: DK already shows [data-rv]/[data-hero]; the journey
  // extras (coral stations, fully stitched seam) are page-specific.
  if (DK.reduce) {
    document.querySelectorAll('[data-station]').forEach(function (m) {
      m.style.background = '#E76F51';
      m.style.color = '#fff';
      m.setAttribute('data-on', '1');
    });
    var fill = document.getElementById('seamFill');
    if (fill) fill.style.height = '100%';
  }

  DK.reveal({
    threshold: 0.88,
    tick: 700,
    onScroll: function () {
      if (!DK.reduce) updateSeam();
      updateSpy();
      checkStations();
      positionStations();
    }
  });

  DK.onResize(positionStations);

  // Safety tick, matching the design's 700ms interval: keeps stations
  // placed and the seam/pulses honest while images/fonts settle.
  setInterval(function () {
    positionStations();
    checkStations();
    if (!DK.reduce) updateSeam();
  }, 700);
});
