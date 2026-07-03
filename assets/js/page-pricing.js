/* Pricing page behaviors — everything shared lives in site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(160, 80);

  // VIP stitched frame draws once, when the VIP card reveals.
  var vipDone = false;
  function stitchVip() {
    if (vipDone || DK.reduce) return;
    var rect = document.querySelector('#vipFrame rect');
    if (!rect) return;
    vipDone = true;
    rect.style.animation = 'vipStitch 1.4s ease-out .15s both';
  }

  DK.reveal({
    threshold: 0.88,
    tick: 700,
    onReveal: function (e) {
      if (e.getAttribute('data-plan') === 'vip') stitchVip();
    }
  });

  // Tape-measure period selector: swap the base-plan price texts and
  // slide the needle/fill; texts change behind a 170ms opacity fade.
  var cfg = window.DK_PRICING;
  if (cfg) {
    var current = cfg['default'];
    var needle = document.getElementById('periodNeedle');
    var fill = document.getElementById('periodFill');
    var wrap = document.getElementById('basePriceWrap');
    var price = document.getElementById('basePrice');
    var unit = document.getElementById('baseUnit');
    var monthly = document.getElementById('baseMonthly');
    var buttons = document.querySelectorAll('[data-period]');

    var applyPeriod = function (p) {
      var sub = cfg.periods[p];
      var pos = (cfg.order.indexOf(p) * 25) + '%';
      if (needle) needle.style.insetInlineStart = pos;
      if (fill) fill.style.insetInlineStart = pos;
      buttons.forEach(function (b) {
        var on = b.getAttribute('data-period') === p;
        var label = b.querySelector('[data-period-label]');
        var badge = b.querySelector('[data-period-badge]');
        if (label) label.style.color = on ? '#fff' : '#3D4A6B';
        if (badge) badge.style.color = on ? 'rgba(255,255,255,.85)' : '#D45A3D';
      });
      if (price) price.textContent = sub.total;
      if (unit) unit.textContent = 'تومان / ' + sub.unit;
      if (monthly) monthly.textContent = sub.monthly;
    };

    var choosePeriod = function (p) {
      if (p === current || !cfg.periods[p]) return;
      current = p;
      if (DK.reduce) { applyPeriod(p); return; }
      if (wrap) wrap.style.opacity = '0';
      setTimeout(function () {
        applyPeriod(current);
        if (wrap) wrap.style.opacity = '1';
      }, 170);
    };

    buttons.forEach(function (b) {
      b.addEventListener('click', function () {
        choosePeriod(b.getAttribute('data-period'));
      });
    });
  }

  DK.faq('#pricingFaq');
});
