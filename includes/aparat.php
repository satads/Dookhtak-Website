<?php
/**
 * Aparat embed sanitizer — the ONLY video source allowed in the project.
 * Accepts pasted embed code, returns a clean <iframe> whose src is on
 * aparat.com, or null when nothing acceptable is found.
 */

/**
 * Extract and rebuild the first aparat.com iframe from pasted code.
 * Whitelisted attributes only; anything else (scripts, other hosts,
 * event handlers) is discarded.
 */
function aparat_sanitize(string $embed): ?string
{
    if (trim($embed) === '') {
        return null;
    }
    if (!preg_match_all('/<iframe\b[^>]*>/i', $embed, $matches)) {
        return null;
    }
    foreach ($matches[0] as $tag) {
        if (!preg_match('/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $tag, $m)) {
            continue;
        }
        $src = html_entity_decode($m[1]);
        $host = parse_url($src, PHP_URL_HOST) ?: '';
        $scheme = parse_url($src, PHP_URL_SCHEME) ?: '';
        if ($scheme !== 'https' || !preg_match('/(^|\.)aparat\.com$/i', $host)) {
            continue;
        }
        // Rebuild a clean iframe with whitelisted attributes.
        $attrs = 'src="' . e($src) . '"';
        foreach (['width', 'height', 'title'] as $name) {
            if (preg_match('/\b' . $name . '\s*=\s*["\']([^"\']*)["\']/i', $tag, $a)) {
                $attrs .= ' ' . $name . '="' . e($a[1]) . '"';
            }
        }
        if (preg_match('/\ballow\s*=\s*["\']([^"\']*)["\']/i', $tag, $a)) {
            $attrs .= ' allow="' . e($a[1]) . '"';
        }
        if (preg_match('/allowfullscreen/i', $tag)) {
            $attrs .= ' allowfullscreen';
        }
        // Design-consistent presentation: responsive 16:9 rounded block.
        $attrs .= ' style="width:100%;aspect-ratio:16/9;border:none;border-radius:18px;display:block;margin-bottom:10px;"';
        return '<iframe ' . $attrs . ' loading="lazy"></iframe>';
    }
    return null;
}
