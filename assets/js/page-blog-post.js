/* Blog single-post behaviors — everything shared lives in site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(160, 80);

  // Reading progress tape: #readFill width tracks scroll progress
  // through #postBody (same math as the design's updateTape()).
  function updateTape() {
    var fill = document.getElementById('readFill');
    var body = document.getElementById('postBody');
    if (!fill || !body) return;
    var r = body.getBoundingClientRect();
    var total = r.height - window.innerHeight * 0.5;
    var p = Math.min(1, Math.max(0, (window.innerHeight * 0.5 - r.top) / Math.max(1, total)));
    fill.style.width = (p * 100).toFixed(2) + '%';
  }

  DK.reveal({ threshold: 0.9, tick: 600, onScroll: updateTape });
  updateTape();

  // Copy-link buttons: copy the page URL, show "کپی شد" for 1.8s.
  var copyTimer = null;
  function setCopyLabels(copied) {
    document.querySelectorAll('[data-copy-label]').forEach(function (el) {
      el.textContent = copied ? 'کپی شد' : 'کپی لینک';
    });
  }
  document.querySelectorAll('[data-copy-link]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var done = function () {
        setCopyLabels(true);
        clearTimeout(copyTimer);
        copyTimer = setTimeout(function () { setCopyLabels(false); }, 1800);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(location.href).then(done).catch(done);
      } else {
        done();
      }
    });
  });
});
