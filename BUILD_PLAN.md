# Dookhtak Website — Phased Build Plan v2 (6 Claude Code Prompts)

**How to use (for Ali):** Run ONE phase at a time. After each phase: run its acceptance checklist locally, bring the bash report back for review, then proceed to the next phase. Never run two phases in one session.

---

## GLOBAL RULES — repeated in every phase, non-negotiable

**Project independence:** This is the Dookhtak MARKETING WEBSITE — a fully independent, standalone project. It has NO relation to AppDookhtak-Client, PanelDookhtak-Master, or any `PROJECT_MEMORY_V2.md` file from those projects. Never read, reference, or modify anything outside this project folder.

**Project memory:** This project uses its own memory file: `SITE_MEMORY.md` in the project root (created in Phase 1). Every phase starts by reading it and ends by updating it with decisions made. It belongs only to this website project.

**Stack & environment:** Target is shared cPanel hosting in Iran — PHP 8.x, MySQL (cPanel DB), Apache/.htaccess, no Composer, no Node on server. Plain PHP 8.x, no frameworks. PDO MySQL utf8mb4, prepared statements for every query. Schema delivered as `database/schema.sql` for MANUAL phpMyAdmin import — never auto-run; if schema changes later, regenerate the full file and flag it loudly.

**Language & UI:** Fully Persian (fa), RTL, Persian digits, Jalali dates. No emojis anywhere; Lucide icons (CDN) only. UI text Persian, code comments English.

**Design:** The exported design (ZIP in project root, extracted in Phase 1) is APPROVED and FINAL — preserve pixel-for-pixel. We add a backend; we never redesign. Tokens: coral `#E76F51`, coral-hover `#D45A3D`, coral-soft `#FCEFEA`, navy `#1F2A44`, body `#3D4A6B`, bg `#FAF8F4`, border `#E8E6E1`, admin work-area `#F7F6F3`.

**CENTRAL ASSET SYSTEM (critical — keeps the project light):** Everything shared is defined ONCE, in central files, and included everywhere. NO page may redefine or duplicate any of it:
- `assets/css/site.css` — the ONLY place for: `@font-face` IRANSans (woff2, `font-display: swap`), all design tokens as `:root` CSS variables, base/reset styles, shared component styles (buttons, cards, badges, forms, stitch/chalk signatures, header/footer styles)
- `assets/js/site.js` — the ONLY place for shared behaviors: mobile drawer, accordions, scroll-reveal, toasts
- `includes/site_header.php` / `includes/site_footer.php` — the ONLY header/footer, driven by ONE nav config array
- Per-page CSS/JS allowed only as small page-specific files for that page's unique sections — never fonts, colors, header, footer, or shared components
- If the design export contains per-page duplicated fonts/tokens/header/footer styles, CONSOLIDATE them into the central files during Phase 1 conversion with zero visual change

**Media:** Videos are ALWAYS Aparat embeds — no video file uploads anywhere in the project. Uploads are images only (max 2MB).

**Scope discipline:** Each phase touches ONLY its own scope. No features beyond the phase document (no user roles, no comments, no newsletter, no analytics dashboards, no dark mode).

---

---

## PHASE 1 — Foundation & Static Conversion

Apply the GLOBAL RULES above. Create `SITE_MEMORY.md` in the project root and record in it: the Global Rules, the project structure, and every decision you make in this phase. (Reminder: this project is fully independent — no relation to any other Dookhtak project or its memory files.)

### Scope
1. **Extract & inventory the design export.** Find the single design-export ZIP in the project root, extract, and inventory: pages (home, features, pricing, tutorials SPA with right sidebar + `#slug` hash routing, blog archive, blog single template, about, contact, admin panel design), assets, CSS, scripts. Report the inventory.
2. **Project structure:**
```
/            public PHP pages
/assets      css/site.css, js/site.js, fonts, images (CENTRAL — see Global Rules)
/includes    config, db, auth, helpers, cache, site_header.php, site_footer.php
/admin       (Phase 2 — placeholder folder)
/api         (later phases)
/uploads     images only: /uploads/images/YYYY/MM (web-writable)
/database    schema.sql + create_admin.php (CLI-only)
/cache       file cache (deny web access)
```
3. **Build the CENTRAL ASSET SYSTEM** per Global Rules: consolidate the export's fonts, tokens, shared styles and shared scripts into `assets/css/site.css` and `assets/js/site.js`; strip all per-page duplication; every page loads the central files + optionally one small page-specific file. Zero visual change — verify page by page.
4. **Complete `database/schema.sql`** (utf8mb4) — the FULL schema for all phases, imported once:
   - `admin_users` (id, username, password_hash, last_login_at, created_at)
   - `settings` (key PK, value) — seed: support_phone, support_email, working_hours, instagram_url/enabled, telegram_url/enabled, bale_url/enabled, enamad_code, app_url=`https://app.dookhtak.ir`, trial_days=10
   - `blog_categories` (id, slug, title, sort_order)
   - `blog_posts` (id, slug UNIQUE, title, excerpt, body_html MEDIUMTEXT, cover_image, cover_alt, category_id FK, status ENUM('draft','published'), published_at, updated_at, seo_title, seo_description, reading_minutes)
   - `tutorial_categories` (id, slug, title, lucide_icon, sort_order)
   - `tutorials` (id, slug UNIQUE, title, category_id FK, duration_minutes, status, sort_order, updated_at, video_type ENUM('none','aparat'), video_embed TEXT, intro_text TEXT, prerequisites_html TEXT NULL, content_json MEDIUMTEXT, troubleshooting_json TEXT, seo_title, seo_description)
   - `faqs` (id, page ENUM('home','pricing'), question, answer_html, sort_order)
   - `testimonials` (id, quote, person_name, city, business_type, sort_order, status)
   - `pricing_values` (key PK, value, label) — seed: setup_fee, sub_1m, sub_3m, sub_6m, sub_12m, vip_price, module_gallery, module_sms, module_gateway, module_domain, module_dedicated_line, discount_3m_percent, discount_6m_percent, discount_12m_percent
   - `submissions` (id, name, phone, business_type, subject, message, created_at, is_read, ip_address, forward_status ENUM('pending','sent','failed') DEFAULT 'pending', forward_attempts, last_forward_error)
   - `seo_pages` (id, page_key UNIQUE: home/features/pricing/tutorials/blog/about/contact, meta_title, meta_description, og_image)
   - `seo_settings` (key PK, value) — seed: site_title_suffix, default_meta_description, default_og_image, head_scripts, body_scripts, robots_txt
   - `redirects` (id, from_path UNIQUE, to_url, status_code, created_at)
   - `media` (id, file_path, type ENUM('image'), alt_text, width, height, size_bytes, created_at)
   - `login_attempts` (id, ip_address, attempted_at)
   - Seed all current placeholder content of the export (sample posts, tutorials as structured JSON, FAQs, testimonials, prices) so the converted site looks identical.
   - `database/create_admin.php`: CLI-only (die over HTTP), prompts username/password, `password_hash()`; must be deleted after production use.
5. **Foundation code (`/includes`):** `config.example.php`→`config.php` (git-ignored; DB creds, BASE_URL, session name, MOTHER_API placeholder block, CACHE_ENABLED=false for now); `db.php` PDO singleton; `helpers.php` (`e()`, `fa_digits()`, `fa_number_format()`, `jalali_date()` — standard Gregorian→Jalali algorithm, `slugify()`, `csrf_token()/csrf_verify()`); `auth.php` (hardened sessions: httponly, samesite=Lax, secure-on-HTTPS, regenerate on login; `require_admin()`; login rate limit 5 fails/IP/15min via `login_attempts`); `cache.php` (file page-cache with `cache_flush()` — infrastructure only, disabled until Phase 6).
6. **Layout conversion:** replace the export's JS-injected header/footer with the central PHP partials driven by ONE nav config array (same items, same auto active-state). Footer contact info, enabled socials, enamad_code read from `settings`.
7. **Convert every public page to PHP** using the central partials and central assets, preserving markup/CSS/JS behavior exactly. Tutorials SPA keeps its static data for now (rewired Phase 4); blog keeps sample content (rewired Phase 3).
8. **Base `.htaccess`:** deny /includes, /database, /cache; disable PHP execution in /uploads; security headers (X-Content-Type-Options, X-Frame-Options SAMEORIGIN, Referrer-Policy strict-origin-when-cross-origin); HTTPS redirect present but commented with toggle note; canonical host policy (choose non-www, document it).

### Acceptance checklist (test before reporting)
1. Local run via `php -S localhost:8080` (document .htaccess limitations under the built-in server and how to verify later on Apache).
2. Every page renders pixel-identical; nav active states correct; footer values come from DB settings.
3. **Central-asset audit:** `@font-face` exists in exactly ONE file; grep confirms no page redefines tokens, header, or footer; each page loads site.css + site.js + at most one page-specific file.
4. schema.sql imports cleanly into a fresh MySQL DB with all seeds.
5. create_admin.php refuses HTTP execution.

### Report (```bash block```)
File tree (2 levels), export inventory, central-asset consolidation summary (what was deduplicated), decisions, manual verification steps, assumptions needing my confirmation. Create/update `SITE_MEMORY.md`.

---

---

## PHASE 2 — Admin Shell, Auth, Settings & Media

Apply the GLOBAL RULES (top of this document). Read `SITE_MEMORY.md` first; update it at the end. Scope of this phase ONLY — do not touch public pages except where stated.

### Scope
1. **Login** (`/admin`): standalone minimal page per the approved admin design (centered card, logo, username/password, coral submit). Generic error text; lockout message after 5 failed attempts (Phase 1 auth). Logout everywhere.
2. **Admin shell:** fixed right navy sidebar (lucide icons, coral stitch indicator on active item, unread badge slot on «پیام‌های دریافتی»), top bar (page title, «مشاهده سایت», user menu + logout), work area `#F7F6F3`, white cards, compact density. Admin styles live in ONE central `assets/css/admin.css` + `assets/js/admin.js` (same central-asset discipline as the public site — no per-screen duplication). Responsive: <1024px sidebar becomes right drawer; test 360px. Sidebar items (unbuilt ones rendered disabled with «به‌زودی» tooltip): داشبورد، بلاگ، دسته‌های بلاگ، آموزش‌ها، دسته‌های آموزش، سؤالات متداول، نظر مشتریان، تعرفه‌ها، پیام‌های دریافتی، رسانه‌ها، سئو، تنظیمات سایت، حساب کاربری.
3. **داشبورد:** stat cards (published posts, published tutorials, unread submissions, active testimonials — real queries), latest 5 submissions (empty state for now), quick-action buttons (disabled until their phases).
4. **تنظیمات سایت:** grouped form over `settings` (contact / socials with enable toggles dimming the URL field / enamad textarea / general: app_url, trial_days) + «پاک‌کردن کش» button (cache_flush). CSRF on save; Persian toast. Saving reflects immediately in the public footer.
5. **حساب کاربری:** change password (current required, min-length validation).
6. **رسانه‌ها (images only):** upload endpoint + library grid (thumbnail, size, alt-text edit, copy-URL, delete with in-use caution). **Image pipeline on upload (GD):** validate via `getimagesize()` + whitelist jpg/jpeg/png/webp, max 2MB, random filename into /uploads/images/YYYY/MM, auto-generate WebP + width variants 1600/800/400, record in `media`.
7. Global admin components for all future phases (in the central admin assets): delete-confirm modal, toasts, skeleton loading, empty states, field-error styling, visible focus.

### Acceptance checklist
1. 5 wrong logins from one IP → locked 15 minutes; correct login works after.
2. POST without CSRF token → rejected.
3. Renamed `.php`→`.jpg` upload → rejected; real JPG → WebP + 3 variants on disk + media record.
4. Change support phone in settings → public footer shows it immediately.
5. Admin fully usable at 360px.

### Report
```bash block```: files, decisions, manual verification steps, assumptions. Update `SITE_MEMORY.md`.

---

---

## PHASE 3 — Blog (Admin + Public SSR)

Apply the GLOBAL RULES (top of this document). Read `SITE_MEMORY.md` first; update it at the end. Scope: blog only.

### Scope
1. **دسته‌های بلاگ:** modal CRUD (title, slug, sort_order).
2. **بلاگ list:** table (title, category, status badge, Jalali date, actions), search, category/status filters, pagination, empty state.
3. **Editor:** two-column per approved design — main: big title, auto-slug (editable, latin), **Quill 2 (CDN) configured RTL** (H2/H3, bold, lists, links, blockquote, image insert via Phase 2 media endpoint); side: publish card (draft/published + save + published_at), category, cover image (upload + alt + preview + remove), SEO card (seo_title, seo_description with char counters), reading_minutes. Unsaved-changes warning. Delete with confirm.
4. **Public archive `/blog`:** SSR from DB (published only): featured latest, category chips (server-side `?cat=`), grid, pagination — approved design exactly. Jalali dates, Persian digits.
5. **Public single `/blog/{slug}`:** clean URL via .htaccess → `blog/post.php`. Full SSR: approved template, per-post SEO fallback chain, related posts (same category, 3), prev/next.
6. Home blog section: 3 latest published posts.

### Acceptance checklist
1. Draft → NOT on site; publish → archive + home section + clean URL.
2. Slug collision → unique validation with Persian error.
3. Quill-inserted image lands in /uploads and renders.
4. Unknown slug → styled Persian 404.
5. View-source of a post: correct `<title>` + meta description.

### Report
```bash block``` + `SITE_MEMORY.md` update (include editor architecture notes).

---

---

## PHASE 4 — Tutorials Structured Builder (Admin + Renderer + SPA Wiring)

Apply the GLOBAL RULES (top of this document). Read `SITE_MEMORY.md` first; update it at the end. Heaviest phase — scope: tutorials only. Public output must match the approved tutorial template EXACTLY (numbered coral step circles connected by the stitch line, framed step screenshots, «نکته» and «مواظب باش» boxes, prerequisites box, troubleshooting accordion, Aparat video block with click-to-load).

### Scope
1. **دسته‌های آموزش:** modal CRUD (title, slug, lucide_icon with live preview, sort_order).
2. **آموزش‌ها list:** table filterable by category (title, category, video icon, duration, sort, status, actions).
3. **Structured builder (NO freeform body editor):**
   - Meta: title, auto-slug, category, duration_minutes, sort_order, status, SEO fields
   - Video: «بدون ویدئو / آپارات» selector → Aparat embed textarea (sanitizer: allow ONLY iframe from aparat.com)
   - intro_text + optional «قبل از شروع» prerequisites (text + links)
   - **Steps repeater** (add / remove / move up / move down): each step = step_title (one action sentence), step_text, optional image (media upload + alt), optional annotation note, optional tip_text («نکته»), optional warning_text («مواظب باش»)
   - **Troubleshooting repeater:** question/answer pairs
   - Persist to `content_json` / `troubleshooting_json`. Define and document the JSON block format in `SITE_MEMORY.md`.
   - «پیش‌نمایش» button: server-rendered fragment styled with the real central site CSS.
4. **Server-side renderer:** `includes/tutorial_renderer.php` — JSON → approved HTML fragment. Single source of truth for tutorial markup.
5. **API for the SPA:** `api/tutorials.php` (JSON: categories + published tutorials: id/slug/title/category/duration/has_video) and `api/tutorial.php?slug=` (rendered fragment + meta). Replace the SPA's static data with these endpoints, preserving: hash deep links `/tutorials#slug`, back/forward, sidebar auto-open of parent category, client-side fragment cache, live search, skeleton loading, error+retry.
6. Migrate seeded sample tutorials into proper structured JSON — page looks unchanged.

### Acceptance checklist
1. Admin-built tutorial (5 steps, images, tip, warning, prerequisites, 2 troubleshooting items, Aparat embed) → public render visually identical to the approved template.
2. `/tutorials#that-slug` direct entry opens it, parent category auto-opens, Back/Forward works.
3. Drafts never appear in api/tutorials.php.
4. Reordering steps in admin reorders public output.
5. Non-aparat iframe rejected by sanitizer.

### Report
```bash block``` + `SITE_MEMORY.md` update (JSON format spec is mandatory there).

---

---

## PHASE 5 — Pricing, FAQ, Testimonials, Contact Form & Mother Adapter

Apply the GLOBAL RULES (top of this document). Read `SITE_MEMORY.md` first; update it at the end.

### Scope
1. **تعرفه‌ها (admin):** grouped form over `pricing_values` per approved design (setup / base plan 4 periods + 3 discount percents / VIP / 5 modules), Persian thousand-separated numeric inputs with «تومان», yellow warning bar, sticky save. **Public pricing:** inject all values (Persian formatting) incl. period-selector data and discount badges — design unchanged.
2. **سؤالات متداول (admin):** tabs (صفحه اصلی / تعرفه‌ها), modal CRUD, sort_order. Public: home + pricing FAQ from DB.
3. **نظر مشتریان (admin):** card list, modal CRUD, status. Public: home testimonials (published only).
4. **Contact backend:** `api/contact.php` — normalize Persian digits, validate (name required; `^09\d{9}$`; honeypot; rate limit 5/IP/hour), insert `submissions`, JSON response driving existing form states without visual change.
5. **پیام‌های دریافتی (admin):** two-pane inbox per approved design (mobile: list→detail with back), unread bold + coral dot + sidebar badge, filters, detail (all fields, `tel:` link, Jalali datetime), read/unread, delete, **forward-status badge (در انتظار / ارسال شد / ناموفق) + «ارسال مجدد به سامانه مادر».**
6. **Mother adapter:** `includes/MotherApiClient.php` reading `MOTHER_API` config (base_url, endpoint, api_key, instance_id, enabled=false — placeholders, `// TODO: fill from mother API docs`). `forwardSubmission(array $data): array` — cURL POST, 10s timeout, headers `X-Api-Key`, `X-Instance-Id`, JSON `{name, phone, business_type, subject, message, submitted_at}`. Flow: save locally first → forward if enabled → update forward_status/attempts/last_forward_error. With enabled=false everything stays 'pending' and the site works fully.

### Acceptance checklist
1. Change sub_12m → pricing page shows it (Persian formatted) everywhere.
2. Persian-digit phone submission → normalized, stored, success state; 6th within an hour → Persian rate-limit error.
3. Honeypot-filled submission silently dropped.
4. Badge counts unread correctly; retry updates forward status (simulate failure: enabled=true + fake URL).
5. FAQ/testimonial edits reflect on the correct pages only.

### Report
```bash block``` + `SITE_MEMORY.md` update.

---

---

## PHASE 6 — SEO Infrastructure, Performance & Delivery Package

Apply the GLOBAL RULES (top of this document). Read `SITE_MEMORY.md` first; update it at the end. Final phase: site-wide, but change public markup ONLY where required for SEO/perf — zero visual change.

### Scope
1. **سئو (admin):** three tabs — (a) per-page meta over `seo_pages`; (b) general: site_title_suffix, default_meta_description, default_og_image, robots.txt editor; (c) اسکریپت‌ها: head_scripts / body_scripts injected on all public pages. Plus **مدیریت ریدایرکت‌ها** CRUD (from_path, to_url, 301/302).
2. **Public SEO layer:** every page emits title (page/post title + suffix), meta description, canonical, OG + twitter:card, og:image fallback chain. **JSON-LD:** Organization + WebSite (home), BlogPosting (posts), BreadcrumbList (blog single), FAQPage (home FAQ). `time datetime` Gregorian with Jalali display. Redirect middleware checks `redirects` early on every public request.
3. **Sitemap & robots:** `/sitemap.xml` → `sitemap.php` (static pages + published posts with lastmod, 1h internal cache); `/robots.txt` → from seo_settings, disallow /admin /includes /api /cache, sitemap reference.
4. **Performance:** enable the page cache for anonymous visitors (verify flush hooks fire on EVERY admin save across all sections); `.htaccess` gzip/deflate + far-future Cache-Control for /assets and /uploads (HTML no-cache); templates use WebP variants via `<picture>`/`srcset` where visually identical; width/height + `loading="lazy"` below the fold; preload IRANSans woff2 (`font-display: swap` already central); defer non-critical JS. No new libraries. Re-verify the central-asset audit: still zero duplication after all phases.
5. **Security & delivery audit:** re-verify all .htaccess protections under Apache semantics; session/CSRF spot-check; confirm create_admin.php deletion instruction; regenerate schema.sql if anything changed (flag loudly).

### Acceptance checklist
1. View-source of home, a post, pricing: correct title/canonical/OG/JSON-LD (validate JSON-LD syntax).
2. `/sitemap.xml` lists all published posts; a newly published post appears after the cache window.
3. Admin-added redirect `/old-test` → `/blog` responds 301.
4. Admin-injected script appears in `<head>` of every public page.
5. Cache on: second anonymous request served from cache; any admin save invalidates it.
6. Perf sanity: WebP where supported, gzip active, font swap, no page loads duplicated shared assets.

### Report (final)
```bash block```: full file tree, **complete cPanel deployment runbook** (create DB+user, import schema.sql via phpMyAdmin, upload, config.php, run then DELETE create_admin.php, /uploads + /cache permissions, verify .htaccess incl. HTTPS toggle, submit sitemap in Search Console via the admin scripts tab), full regression checklist across all 6 phases, all assumptions. Final `SITE_MEMORY.md` update.
