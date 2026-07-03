<?php
/**
 * Shared builder form collection/sanitization — used by the editor
 * save path and the preview endpoint (single behavior for both).
 */

require_once __DIR__ . '/helpers.php';

/** Build the sanitized field set from a submitted form (shared with preview). */
function tutorial_collect_post(array $src, array $base): array
{
    $t = $base;
    $t['title'] = trim((string) ($src['title'] ?? ''));
    $t['slug'] = slugify((string) ($src['slug'] ?? ''));
    $t['category_id'] = (int) ($src['category_id'] ?? 0) ?: null;
    $t['duration_minutes'] = max(1, (int) en_digits((string) ($src['duration_minutes'] ?? '3')));
    $t['sort_order'] = (int) en_digits((string) ($src['sort_order'] ?? '0'));
    $t['status'] = ($src['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    $t['video_type'] = ($src['video_type'] ?? 'none') === 'aparat' ? 'aparat' : 'none';
    $t['video_embed'] = trim((string) ($src['video_embed'] ?? ''));
    $t['intro_text'] = trim((string) ($src['intro_text'] ?? ''));
    // Prerequisites: plain text + <a> links only (href http/https/# whitelist).
    $prereq = trim(strip_tags((string) ($src['prerequisites_html'] ?? ''), '<a>'));
    $prereq = preg_replace_callback('/<a\b[^>]*>/i', function ($m) {
        if (preg_match('/href\s*=\s*["\']([^"\']+)["\']/i', $m[0], $h)
            && preg_match('#^(https?://|/|\#)#i', trim($h[1]))) {
            return '<a href="' . e(trim($h[1])) . '" style="color:#E76F51;font-weight:700;text-decoration:underline;text-underline-offset:3px;">';
        }
        return '';
    }, $prereq);
    $t['prerequisites_html'] = $prereq;
    $t['seo_title'] = trim((string) ($src['seo_title'] ?? '')) ?: null;
    $t['seo_description'] = trim((string) ($src['seo_description'] ?? '')) ?: null;

    // Steps + troubleshooting arrive as JSON from the repeater UI.
    $steps = json_decode((string) ($src['content_json'] ?? '[]'), true) ?: [];
    $clean_steps = [];
    foreach ($steps as $s) {
        if (!is_array($s)) {
            continue;
        }
        $step = [
            'title'      => trim((string) ($s['title'] ?? '')),
            'text'       => trim((string) ($s['text'] ?? '')),
            'tip'        => trim((string) ($s['tip'] ?? '')) ?: null,
            'warning'    => trim((string) ($s['warning'] ?? '')) ?: null,
            'image'      => trim((string) ($s['image'] ?? '')) ?: null,
            'image_alt'  => trim((string) ($s['image_alt'] ?? '')) ?: null,
            'annotation' => trim((string) ($s['annotation'] ?? '')) ?: null,
        ];
        if ($step['title'] === '' && $step['text'] === '') {
            continue; // skip empty cards
        }
        $clean_steps[] = $step;
    }
    $t['content_json'] = json_encode($clean_steps, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $trouble = json_decode((string) ($src['troubleshooting_json'] ?? '[]'), true) ?: [];
    $clean_trouble = [];
    foreach ($trouble as $x) {
        if (!is_array($x)) {
            continue;
        }
        $q = trim((string) ($x['q'] ?? ''));
        $a = trim((string) ($x['a'] ?? ''));
        if ($q === '' && $a === '') {
            continue;
        }
        $clean_trouble[] = ['q' => $q, 'a' => $a];
    }
    $t['troubleshooting_json'] = json_encode($clean_trouble, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $t;
}
