<?php
/**
 * Tutorial fragment renderer — turns a tutorials row (with structured
 * content_json / troubleshooting_json) into the HTML fragment markup
 * approved in the Phase 1 static fragments (content/*.html).
 *
 * Blocks, in order:
 *   1. video cover + <template data-video-embed> (only video_type='aparat')
 *   2. intro paragraph (optional)
 *   3. prerequisites box (optional, trusted admin HTML)
 *   4. numbered steps on the dashed stitch line, each with optional
 *      tip box, warning box and phone-framed screenshot/placeholder
 *   5. troubleshooting accordion card + ticket link (only when items exist)
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/aparat.php';

/** Decode a JSON column that may arrive as a string or an already-decoded array. */
function tutorial_json_list(mixed $value): array
{
    if (is_array($value)) {
        return $value;
    }
    if (is_string($value) && trim($value) !== '') {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    return [];
}

/** Video cover button + embed template + "prefer to read?" line. */
function tutorial_render_video_block(array $t): string
{
    $minutes = fa_digits((string) ($t['duration_minutes'] ?? ''));
    $embed = aparat_sanitize((string) ($t['video_embed'] ?? ''));
    if ($embed !== null) {
        $inner = '    ' . $embed . "\n";
    } else {
        // No usable embed yet — keep the fragment's placeholder verbatim.
        $inner = <<<HTML
    <!-- [VIDEO: کد embed آپارات اینجا قرار گیرد] -->
    <div style="width:100%;aspect-ratio:16/9;border-radius:18px;background:#1F2A44;display:flex;align-items:center;justify-content:center;color:#9aa4bd;font-size:14px;margin-bottom:10px;">[VIDEO: کد embed آپارات — placeholder]</div>

HTML;
    }
    return <<<HTML
  <button data-video-cover style="position:relative;display:flex;align-items:center;justify-content:center;width:100%;aspect-ratio:16/9;border:none;border-radius:18px;background:linear-gradient(140deg,#2A3550,#1F2A44);box-shadow:0 20px 44px -26px rgba(31,42,68,.5);overflow:hidden;cursor:pointer;margin-bottom:10px;">
    <span style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:46px 46px;"></span>
    <span style="position:relative;display:flex;flex-direction:column;align-items:center;gap:14px;">
      <span style="display:inline-flex;width:74px;height:74px;align-items:center;justify-content:center;background:#E76F51;border-radius:50%;color:#fff;box-shadow:0 12px 30px rgba(231,111,81,.45);">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor" style="margin-inline-start:4px;"><polygon points="6 3 20 12 6 21 6 3"/></svg>
      </span>
      <span style="font-size:14px;color:#dfe4f0;">پخش ویدئوی آموزش ({$minutes} دقیقه)</span>
    </span>
  </button>
  <template data-video-embed>
{$inner}  </template>
  <div style="text-align:center;font-size:13.5px;color:#6B7280;margin:0 0 8px;">ترجیح می‌دهی بخوانی؟ همین آموزش قدم‌به‌قدم در ادامه ↓</div>

HTML;
}

/** «نکته» box (lightbulb icon) inside a step. */
function tutorial_render_tip_box(string $tip): string
{
    $tip = e($tip);
    return <<<HTML
      <div style="margin:0 0 16px;background:#fff;border:1px solid #E8E6E1;border-radius:14px;padding:16px 18px;display:flex;gap:12px;align-items:flex-start;">
        <span style="flex:none;display:inline-flex;width:38px;height:38px;align-items:center;justify-content:center;background:#FAF8F4;border:1px solid #E8E6E1;border-radius:50%;color:#9a9587;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
        </span>
        <div>
          <div style="font-size:13px;font-weight:700;color:#6B7280;margin-bottom:3px;">نکته</div>
          <p style="margin:0;font-size:14.5px;line-height:1.9;color:#3D4A6B;">{$tip}</p>
        </div>
      </div>

HTML;
}

/** «مواظب باش» box (warning triangle icon) inside a step. */
function tutorial_render_warning_box(string $warning): string
{
    $warning = e($warning);
    return <<<HTML
      <div style="margin:0 0 16px;background:#fff;border:1.5px solid #E76F51;border-radius:14px;padding:16px 18px;display:flex;gap:12px;align-items:flex-start;">
        <span style="flex:none;display:inline-flex;width:38px;height:38px;align-items:center;justify-content:center;background:#FCEFEA;border:none;border-radius:50%;color:#E76F51;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
        </span>
        <div>
          <div style="font-size:13px;font-weight:700;color:#D45A3D;margin-bottom:3px;">مواظب باش</div>
          <p style="margin:0;font-size:14.5px;line-height:1.9;color:#3D4A6B;">{$warning}</p>
        </div>
      </div>

HTML;
}

/**
 * Phone-framed screenshot. With an image: the real <img> (plus the
 * annotation pill overlaid at the bottom). Without an image but with an
 * annotation: the [TUT-SHOT: …] placeholder pill. Neither: no block.
 */
function tutorial_render_screenshot(?string $image, ?string $alt, ?string $annotation): string
{
    $image = trim((string) $image);
    $annotation = trim((string) $annotation);
    if ($image === '' && $annotation === '') {
        return '';
    }
    if ($image !== '') {
        $src = e('/' . ltrim($image, '/'));
        $altAttr = e((string) $alt);
        $screen = '            <img src="' . $src . '" alt="' . $altAttr . '" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">' . "\n";
        if ($annotation !== '') {
            $screen .= '            <span style="position:absolute;bottom:12px;inset-inline-start:50%;transform:translateX(50%);font-size:11px;color:#6B7280;background:rgba(255,255,255,.85);border:1px dashed #d9d3c6;border-radius:999px;padding:4px 12px;text-align:center;white-space:nowrap;">' . e($annotation) . '</span>' . "\n";
        }
    } else {
        $screen = '            <span style="font-size:11px;color:#6B7280;background:rgba(255,255,255,.85);border:1px dashed #d9d3c6;border-radius:999px;padding:4px 12px;text-align:center;">[TUT-SHOT: ' . e($annotation) . ']</span>' . "\n"
            . "            \n";
    }
    return <<<HTML
      <div style="position:relative;max-width:290px;background:#1F2A44;border-radius:24px;padding:8px;border:1px solid #E8E6E1;box-shadow:0 16px 36px -20px rgba(31,42,68,.4);">
        <div style="background:#FAF8F4;border-radius:18px;overflow:hidden;">
          <div style="height:22px;background:#1F2A44;display:flex;justify-content:center;align-items:flex-start;"><span style="width:44px;height:5px;background:#3D4A6B;border-radius:0 0 6px 6px;"></span></div>
          <div style="aspect-ratio:4/5;display:flex;align-items:center;justify-content:center;position:relative;">
{$screen}          </div>
        </div>
      </div>

HTML;
}

/** One numbered step on the stitch line. */
function tutorial_render_step(array $step, int $number): string
{
    $num = fa_digits($number);
    $title = e((string) ($step['title'] ?? ''));
    $text = e((string) ($step['text'] ?? ''));
    $out = <<<HTML
    <div style="position:relative;padding-bottom:38px;">
      <span style="position:absolute;inset-inline-start:-52px;top:0;display:inline-flex;width:40px;height:40px;align-items:center;justify-content:center;border-radius:50%;background:#E76F51;color:#fff;font-size:17px;font-weight:700;box-shadow:0 6px 16px rgba(231,111,81,.35);">{$num}</span>
      <h2 style="font-size:20px;font-weight:700;color:#1F2A44;margin:6px 0 10px;">{$title}</h2>
      <p style="font-size:17px;line-height:2;color:#3D4A6B;margin:0 0 16px;">{$text}</p>

HTML;
    $out .= "      \n";
    if (!empty($step['tip'])) {
        $out .= tutorial_render_tip_box((string) $step['tip']);
    }
    if (!empty($step['warning'])) {
        $out .= tutorial_render_warning_box((string) $step['warning']);
    }
    $out .= tutorial_render_screenshot(
        $step['image'] ?? null,
        $step['image_alt'] ?? null,
        $step['annotation'] ?? null
    );
    $out .= "    </div>\n";
    return $out;
}

/** Troubleshooting accordion card («اگر مشکلی پیش آمد») + ticket link. */
function tutorial_render_troubleshooting(array $items): string
{
    $rows = '';
    foreach ($items as $item) {
        $q = e((string) ($item['q'] ?? ''));
        $a = e((string) ($item['a'] ?? ''));
        $rows .= <<<HTML
    <div style="border-bottom:2px dashed #F0EBE1;">
      <button data-acc-btn style="width:100%;min-height:48px;background:none;border:none;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 2px;text-align:start;font-size:15px;font-weight:700;color:#1F2A44;font-family:inherit;cursor:pointer;">
        <span>{$q}</span>
        <span data-acc-icon style="flex:none;color:#E76F51;font-size:21px;line-height:1;display:inline-block;transition:transform .3s ease;">+</span>
      </button>
      <div data-acc-panel style="display:grid;grid-template-rows:0fr;transition:grid-template-rows .3s ease;"><div style="overflow:hidden;"><div style="padding:0 2px 16px;font-size:14.5px;line-height:1.9;color:#3D4A6B;">{$a}</div></div></div>
    </div>

HTML;
    }
    $ticketUrl = e(setting('app_url', 'https://app.dookhtak.ir'));
    return <<<HTML
  <div style="background:#fff;border:1px solid #E8E6E1;border-radius:16px;padding:22px 24px;box-shadow:0 10px 30px -24px rgba(31,42,68,.4);margin-top:18px;">
    <h2 style="font-size:19px;font-weight:700;color:#1F2A44;margin:0 0 8px;">اگر مشکلی پیش آمد</h2>

{$rows}    <a href="{$ticketUrl}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:7px;margin-top:14px;min-height:44px;color:#E76F51;font-weight:700;font-size:14.5px;text-decoration:none;">باز هم حل نشد؟ تیکت بفرست ←</a>
  </div>
HTML;
}

/**
 * Render a full tutorial fragment from a tutorials row.
 * content_json / troubleshooting_json may be JSON strings or arrays.
 */
function tutorial_render_fragment(array $t): string
{
    $steps = tutorial_json_list($t['content_json'] ?? null);
    $trouble = tutorial_json_list($t['troubleshooting_json'] ?? null);
    $out = '';

    // 1. Video block — only for aparat tutorials.
    if (($t['video_type'] ?? 'none') === 'aparat') {
        $out .= tutorial_render_video_block($t);
    }

    // 2. Intro paragraph (admin-authored tutorials).
    $intro = trim((string) ($t['intro_text'] ?? ''));
    if ($intro !== '') {
        $out .= '  <p style="font-size:17px;line-height:2;color:#3D4A6B;margin:14px 0 0;">' . e($intro) . "</p>\n";
    }

    // 3. Prerequisites box (trusted admin HTML from the editor).
    $prereq = trim((string) ($t['prerequisites_html'] ?? ''));
    if ($prereq !== '') {
        $out .= "\n" . <<<HTML
  <div style="display:flex;gap:12px;align-items:flex-start;background:#FCEFEA;border-radius:14px;padding:16px 18px;margin:18px 0 8px;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D45A3D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex:none;margin-top:3px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
    <p style="margin:0;font-size:14.5px;line-height:1.9;color:#3D4A6B;">{$prereq}</p>
  </div>

HTML;
    }

    // 4. Steps on the dashed stitch line.
    if ($steps !== []) {
        $out .= "\n" . '  <div style="position:relative;padding-inline-start:52px;margin-top:26px;">' . "\n"
            . '    <div style="position:absolute;top:10px;bottom:10px;inset-inline-start:19px;width:2px;background-image:repeating-linear-gradient(180deg,#E76F51 0 9px,transparent 9px 16px);opacity:.5;"></div>' . "\n"
            . "    \n";
        $n = 0;
        foreach ($steps as $step) {
            if (!is_array($step)) {
                continue;
            }
            $out .= tutorial_render_step($step, ++$n);
        }
        $out .= "  </div>\n";
    }

    // 5. Troubleshooting card.
    if ($trouble !== []) {
        $out .= "\n" . tutorial_render_troubleshooting($trouble);
    }

    return $out;
}
