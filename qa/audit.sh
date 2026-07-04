#!/bin/bash
# Phase 1 acceptance audit for the Dookhtak website.
# Standing regression suite — run after EVERY phase (dev machine).
cd /home/user/Dookhtak-Website || exit 1
PASS=0; FAIL=0
ok()   { echo "  PASS: $1"; PASS=$((PASS+1)); }
bad()  { echo "  FAIL: $1"; FAIL=$((FAIL+1)); }

echo "== 1. PHP lint =="
LINT_FAIL=0
while IFS= read -r f; do
  php -l "$f" >/dev/null 2>&1 || { bad "syntax error in $f"; LINT_FAIL=1; }
done < <(find . -name '*.php' -not -path './cache/*')
[ "$LINT_FAIL" = 0 ] && ok "all PHP files lint clean"

echo "== 2. Pages render over php -S (router.php) =="
for route in / /features /pricing /tutorials /blog /blog/pricing-mistakes /about /contact; do
  body=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080$route")
  if [ "$body" = 200 ]; then ok "GET $route -> 200"; else bad "GET $route -> $body"; fi
done
code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080/no-such-page")
[ "$code" = 404 ] && ok "unknown route -> 404" || bad "unknown route -> $code"

echo "== 3. No leftover template syntax in rendered output =="
for route in / /features /pricing /tutorials /blog /blog/pricing-mistakes /about /contact; do
  n=$(curl -s "http://localhost:8080$route" | grep -c '{{\|sc-for\|sc-if\|dc-import')
  [ "$n" = 0 ] && ok "$route clean" || bad "$route has $n template remnants"
done

echo "== 4. Central-asset audit =="
n=$(grep -rl '@font-face' assets/ includes/ *.php 2>/dev/null | sort -u | wc -l)
f=$(grep -rl '@font-face' assets/ includes/ *.php 2>/dev/null | sort -u)
[ "$n" = 1 ] && [ "$f" = "assets/css/site.css" ] && ok "@font-face only in site.css" || bad "@font-face found in: $f"
n=$(grep -l ':root' assets/css/*.css | grep -v site.css | wc -l)
[ "$n" = 0 ] && ok "design tokens (:root) only in site.css" || bad ":root redefined in page css"
n=$(grep -rl 'navPill\|dkDrawer' assets/css/page-*.css 2>/dev/null | wc -l)
[ "$n" = 0 ] && ok "no header styles in page css" || bad "header styles leaked into page css"
for route in / /features /pricing /tutorials /blog /blog/pricing-mistakes /about /contact; do
  html=$(curl -s "http://localhost:8080$route")
  css=$(echo "$html" | grep -o 'assets/css/[a-z0-9.-]*\.css' | sort -u | wc -l)
  js=$(echo "$html" | grep -o 'assets/js/[a-z0-9.-]*\.js' | sort -u | grep -v tutorials-data | wc -l)
  # expect: site.css (+ at most 1 page css) and site.js (+ at most 1 page js)
  [ "$css" -le 2 ] && [ "$js" -le 2 ] && ok "$route loads $css css / $js js (within budget)" || bad "$route loads $css css / $js js"
  echo "$html" | grep -q 'assets/css/site.css' || bad "$route missing site.css"
  echo "$html" | grep -q 'assets/js/site.js' || bad "$route missing site.js"
done

echo "== 5. Footer values from DB settings =="
# The page cache is enabled (Phase 6); a real settings save flushes it, so the
# test flushes after the direct DB write to mirror that path.
mariadb --default-character-set=utf8mb4 dookhtak_site -e "UPDATE settings SET value='۰۲۱-۹۹۹۹۹۹۹۹' WHERE \`key\`='support_phone';" 2>/dev/null
php -r "require '$PWD/includes/cache.php'; cache_flush();" >/dev/null 2>&1
out=$(curl -s http://localhost:8080/ | grep -o 'پشتیبانی: [^<]*')
[ "$out" = "پشتیبانی: ۰۲۱-۹۹۹۹۹۹۹۹" ] && ok "footer reads support_phone live from DB" || bad "footer phone: $out"
mariadb --default-character-set=utf8mb4 dookhtak_site -e "UPDATE settings SET value='۰۲۱-۱۲۳۴۵۶۷۸' WHERE \`key\`='support_phone';" 2>/dev/null
php -r "require '$PWD/includes/cache.php'; cache_flush();" >/dev/null 2>&1

echo "== 6. Schema imports cleanly into a FRESH db =="
mariadb --default-character-set=utf8mb4 -e "DROP DATABASE IF EXISTS dookhtak_fresh; CREATE DATABASE dookhtak_fresh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
if mariadb dookhtak_fresh < database/schema.sql 2>/tmp/schema.err; then
  cnt=$(mariadb -N dookhtak_fresh -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='dookhtak_fresh';")
  ok "schema.sql imported into fresh DB ($cnt tables)"
else
  bad "schema import failed: $(head -2 /tmp/schema.err)"
fi
mariadb --default-character-set=utf8mb4 -e "DROP DATABASE dookhtak_fresh;" 2>/dev/null

echo "== 7. create_admin.php refuses HTTP =="
code_body=$(curl -s "http://localhost:8080/database/create_admin.php")
code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080/database/create_admin.php")
if [ "$code" = 403 ]; then ok "HTTP request to create_admin.php -> 403"; else bad "create_admin over HTTP -> $code ($code_body)"; fi

echo "== 8. Sensitive dirs blocked (router simulation of .htaccess) =="
for p in /includes/config.php /includes/db.php /cache/x /database/schema.sql; do
  code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080$p")
  [ "$code" = 403 ] && ok "$p -> 403" || bad "$p -> $code"
done

echo "== 9. Nav active state =="
curl -s http://localhost:8080/features | grep -q 'font-weight:700;color:var(--color-navy).*امکانات' && ok "/features marks امکانات active" || bad "/features active state wrong"
curl -s http://localhost:8080/blog/pricing-mistakes | grep -q 'font-weight:700;color:var(--color-navy).*بلاگ' && ok "/blog/{slug} marks بلاگ active (prefix match)" || bad "blog post active state wrong"

echo "== 10. Tutorials SPA + API =="
code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080/api/tutorials.php")
[ "$code" = 200 ] && ok "tutorials index API 200" || bad "index API -> $code"
n=$(curl -s "http://localhost:8080/api/tutorials.php" | grep -o '"id"' | wc -l)
[ "$n" -ge 27 ] && ok "index API lists categories+tutorials ($n ids)" || bad "index API too small ($n ids)"
code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080/api/tutorial.php?slug=first-order")
[ "$code" = 200 ] && ok "fragment API 200" || bad "fragment API -> $code"
code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080/api/tutorial.php?slug=missing-x")
[ "$code" = 404 ] && ok "fragment API unknown slug -> 404" || bad "fragment API unknown -> $code"
curl -s http://localhost:8080/tutorials | grep -q 'type="module"' && ok "tutorials page loads module JS" || bad "tutorials module script missing"


echo "== 11. No external CDN/assets (whitelist: aparat iframes + own canonical host) =="
EXT_FAIL=0
# Own domain (canonical / og / sitemap self-references) and aparat embeds are allowed.
# rel="canonical"/og:/twitter: are metadata, not asset loads; only real cross-host
# script/style/font/img/iframe loads are violations.
for route in / /features /pricing /tutorials /blog /blog/pricing-mistakes /about /contact /admin/; do
  hits=$(curl -s "http://localhost:8080$route" | grep -oiE '<(script|link|img|iframe)[^>]+(src|href)="https?://[^"]*"' \
    | grep -viE 'rel="canonical"|property="og:|name="twitter:' | grep -viE 'aparat\.com|dookhtak\.ir')
  if [ -n "$hits" ]; then bad "$route loads external asset: $(echo "$hits" | head -1)"; EXT_FAIL=1; fi
done
srchits=$(grep -rnoE '@import[^;]*https?://|url\(https?://[^)]*\)|<script[^>]+src="https?://[^"]*"|<link[^>]+(rel="stylesheet"|rel="preload")[^>]+href="https?://[^"]*"' \
  --include='*.php' --include='*.css' --include='*.js' --include='*.html' . 2>/dev/null \
  | grep -v 'qa/node_modules\|\.git/\|./cache/' | grep -viE 'aparat\.com|dookhtak\.ir')
if [ -n "$srchits" ]; then bad "source tree references external asset: $(echo "$srchits" | head -1)"; EXT_FAIL=1; fi
[ "$EXT_FAIL" = 0 ] && ok "no external CDN/asset references (public + admin + source)"

echo "== 12. Phase 5 — contact API, admin surface, DB-driven content =="
# Contact API method guard (non-destructive).
code=$(curl -s -o /dev/null -w '%{http_code}' http://localhost:8080/api/contact.php)
[ "$code" = 405 ] && ok "GET /api/contact.php -> 405" || bad "GET /api/contact.php -> $code"
# Honeypot: filled 'company' -> ok:true and NO row stored.
cnt_before=$(mysql --default-character-set=utf8mb4 -u dookhtak_user -plocaltest dookhtak_site -N -B -e 'SELECT COUNT(*) FROM submissions' 2>/dev/null)
hp=$(curl -s -X POST http://localhost:8080/api/contact.php -H 'Content-Type: application/json' \
  --data '{"name":"bot","phone":"09121112233","company":"x","message":"m"}')
cnt_after=$(mysql --default-character-set=utf8mb4 -u dookhtak_user -plocaltest dookhtak_site -N -B -e 'SELECT COUNT(*) FROM submissions' 2>/dev/null)
{ echo "$hp" | grep -q '"ok":true' && [ "$cnt_before" = "$cnt_after" ]; } \
  && ok "honeypot -> ok:true, no row stored" || bad "honeypot mishandled (before=$cnt_before after=$cnt_after resp=$hp)"
# Invalid phone -> 422 (no row; count unchanged from the honeypot check).
code=$(curl -s -o /dev/null -w '%{http_code}' -X POST http://localhost:8080/api/contact.php \
  -H 'Content-Type: application/json' --data '{"name":"y","phone":"123","message":"m"}')
cnt_end=$(mysql --default-character-set=utf8mb4 -u dookhtak_user -plocaltest dookhtak_site -N -B -e 'SELECT COUNT(*) FROM submissions' 2>/dev/null)
{ [ "$code" = 422 ] && [ "$cnt_after" = "$cnt_end" ]; } && ok "invalid phone -> 422, no row" || bad "invalid phone -> $code (rows $cnt_after->$cnt_end)"
# New admin pages require auth (unauthenticated -> 302 to /admin/).
for p in pricing faq testimonials inbox; do
  code=$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8080/admin/$p.php")
  [ "$code" = 302 ] && ok "unauth /admin/$p.php -> 302" || bad "unauth /admin/$p.php -> $code (should redirect to login)"
done
# Adapter include not web-served.
code=$(curl -s -o /dev/null -w '%{http_code}' http://localhost:8080/includes/MotherApiClient.php)
[ "$code" = 403 ] && ok "/includes/MotherApiClient.php -> 403" || bad "/includes/MotherApiClient.php -> $code"
# Public content is DB-driven: FAQ (home 7 / pricing 7) and testimonials (3) render.
n=$(curl -s http://localhost:8080/ | grep -c 'data-faq-item'); [ "$n" -ge 1 ] && ok "home renders $n FAQ items from DB" || bad "home FAQ items missing"
n=$(curl -s http://localhost:8080/ | grep -c 'data-testi '); [ "$n" -ge 1 ] && ok "home renders $n testimonials from DB" || bad "home testimonials missing"
n=$(curl -s http://localhost:8080/pricing | grep -c 'data-faq-item'); [ "$n" -ge 1 ] && ok "pricing renders $n FAQ items from DB" || bad "pricing FAQ items missing"
# Pricing values come from pricing_values: yearly total appears Persian-formatted.
curl -s http://localhost:8080/pricing | grep -q '۲٬۶۹۰٬۰۰۰' && ok "pricing shows sub_12m from pricing_values" || bad "pricing sub_12m not rendered from DB"

echo "== 13. Phase 6 — SEO layer, sitemap, robots, redirects, cache =="
# Home: title/canonical/OG/twitter present.
home=$(curl -s http://localhost:8080/)
echo "$home" | grep -q '<link rel="canonical" href="https://dookhtak.ir/"' && ok "home canonical present" || bad "home canonical missing"
echo "$home" | grep -q '<meta property="og:title"' && echo "$home" | grep -q '<meta name="twitter:card"' && ok "home OG + twitter tags present" || bad "home OG/twitter missing"
# Home JSON-LD: Organization + WebSite + FAQPage, all valid JSON.
types=$(echo "$home" | grep -oP '"@type":"\K(Organization|WebSite|FAQPage)' | sort -u | tr '\n' ',')
[ "$types" = "FAQPage,Organization,WebSite," ] && ok "home JSON-LD: Organization+WebSite+FAQPage" || bad "home JSON-LD types: $types"
jn=$(echo "$home" | grep -oP '(?<=<script type="application/ld\+json">).*?(?=</script>)' | python3 -c "import sys,json;print(sum(1 for l in sys.stdin if l.strip() and (json.loads(l) or True)))" 2>/dev/null)
[ "$jn" = 3 ] && ok "home JSON-LD blocks valid ($jn)" || bad "home JSON-LD invalid or wrong count: $jn"
# Blog post: article OG + BlogPosting + BreadcrumbList.
post=$(curl -s http://localhost:8080/blog/pricing-mistakes)
echo "$post" | grep -q '<meta property="og:type" content="article">' && ok "post og:type=article" || bad "post og:type missing"
echo "$post" | grep -q '"@type":"BlogPosting"' && echo "$post" | grep -q '"@type":"BreadcrumbList"' && ok "post BlogPosting + BreadcrumbList" || bad "post JSON-LD missing"
# Sitemap.
sm=$(curl -s -o /dev/null -w '%{http_code} %{content_type}' http://localhost:8080/sitemap.xml)
echo "$sm" | grep -q '200 application/xml' && ok "sitemap.xml 200 application/xml" || bad "sitemap.xml -> $sm"
posts=$(mariadb --default-character-set=utf8mb4 dookhtak_site -N -B -e "SELECT COUNT(*) FROM blog_posts WHERE status='published'" 2>/dev/null)
locs=$(curl -s http://localhost:8080/sitemap.xml | grep -c '<loc>')
[ "$locs" -ge $((7 + posts)) ] && ok "sitemap lists 7 static + $posts posts ($locs locs)" || bad "sitemap loc count $locs (< 7+$posts)"
# Robots.
rb=$(curl -s http://localhost:8080/robots.txt)
echo "$rb" | grep -q 'Sitemap:' && echo "$rb" | grep -q 'Disallow: /admin' && ok "robots.txt has Sitemap + Disallow /admin" || bad "robots.txt content wrong"
# Redirect middleware (self-cleaning).
mariadb --default-character-set=utf8mb4 dookhtak_site -e "INSERT INTO redirects (from_path,to_url,status_code) VALUES ('/audit-redirect','/blog',301)" 2>/dev/null
rc=$(curl -s -o /dev/null -w '%{http_code}' http://localhost:8080/audit-redirect)
[ "$rc" = 301 ] && ok "redirect /audit-redirect -> 301" || bad "redirect -> $rc"
mariadb --default-character-set=utf8mb4 dookhtak_site -e "DELETE FROM redirects WHERE from_path='/audit-redirect'" 2>/dev/null
# Cache: enabled, anonymous request writes a cache file; admin session bypasses.
php -r "require '$PWD/includes/cache.php'; cache_flush();" >/dev/null 2>&1
grep -q "define('CACHE_ENABLED', true)" includes/config.php && ok "page cache enabled in config" || bad "CACHE_ENABLED not true"
curl -s -o /dev/null http://localhost:8080/
[ "$(ls cache/page_*.html 2>/dev/null | wc -l)" -ge 1 ] && ok "anonymous request populates page cache" || bad "page cache not written"
php -r "require '$PWD/includes/cache.php'; cache_flush();" >/dev/null 2>&1

echo
echo "RESULT: $PASS passed, $FAIL failed"
exit $FAIL
