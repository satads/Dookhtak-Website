/* Blog archive behaviors. Category filtering is server-side (?cat=)
   since Phase 3 — chips are plain links. Shared behaviors live in
   site.js (DK). */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  DK.hero(160, 80);
  DK.reveal({ threshold: 0.9, tick: 600 });
});
