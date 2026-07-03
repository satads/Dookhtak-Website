<?php
/**
 * Media pipeline (images only, GD).
 * - validate via getimagesize() + whitelist jpg/jpeg/png/webp, max 2MB
 * - random filename into /uploads/images/YYYY/MM
 * - auto-generate full-size WebP + width variants 1600/800/400
 * - record in the media table
 * Reused by the Phase-3 editor image insert.
 */

require_once __DIR__ . '/db.php';

const MEDIA_MAX_BYTES = 2 * 1024 * 1024;
const MEDIA_VARIANT_WIDTHS = [1600, 800, 400];
const MEDIA_WEBP_QUALITY = 82;

/** Allowed mime => canonical extension. */
function media_allowed_types(): array
{
    return [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
}

/** Absolute filesystem root of the project (public root). */
function media_root(): string
{
    return dirname(__DIR__);
}

/**
 * Validate + store an uploaded image and build its variants.
 * Returns ['ok'=>true,'media'=>row] or ['ok'=>false,'error'=>Persian message].
 */
function media_store_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? 0) === UPLOAD_ERR_FORM_SIZE) {
        return ['ok' => false, 'error' => 'حجم تصویر بیشتر از ۲ مگابایت است.'];
    }
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'آپلود ناموفق بود؛ دوباره تلاش کن.'];
    }
    if ($file['size'] > MEDIA_MAX_BYTES) {
        return ['ok' => false, 'error' => 'حجم تصویر بیشتر از ۲ مگابایت است.'];
    }

    // Content-based validation — the filename/extension is never trusted.
    $info = @getimagesize($file['tmp_name']);
    $allowed = media_allowed_types();
    if ($info === false || !isset($info['mime']) || !isset($allowed[$info['mime']])) {
        return ['ok' => false, 'error' => 'فقط تصویر JPG، PNG یا WebP مجاز است.'];
    }
    [$width, $height] = $info;
    $ext = $allowed[$info['mime']];

    $rel_dir = 'uploads/images/' . date('Y') . '/' . date('m');
    $abs_dir = media_root() . '/' . $rel_dir;
    if (!is_dir($abs_dir) && !@mkdir($abs_dir, 0755, true)) {
        return ['ok' => false, 'error' => 'ساخت پوشه آپلود ممکن نشد.'];
    }

    $name = bin2hex(random_bytes(8));
    $rel_path = $rel_dir . '/' . $name . '.' . $ext;
    $abs_path = media_root() . '/' . $rel_path;

    $moved = is_uploaded_file($file['tmp_name'])
        ? move_uploaded_file($file['tmp_name'], $abs_path)
        : rename($file['tmp_name'], $abs_path); // CLI/test path
    if (!$moved) {
        return ['ok' => false, 'error' => 'ذخیره فایل ممکن نشد.'];
    }

    // Build WebP variants (full + capped widths).
    $src = match ($info['mime']) {
        'image/jpeg' => @imagecreatefromjpeg($abs_path),
        'image/png'  => @imagecreatefrompng($abs_path),
        'image/webp' => @imagecreatefromwebp($abs_path),
    };
    if ($src) {
        imagepalettetotruecolor($src);
        imagealphablending($src, false);
        imagesavealpha($src, true);
        // full-size webp
        @imagewebp($src, media_root() . '/' . $rel_dir . '/' . $name . '.webp', MEDIA_WEBP_QUALITY);
        foreach (MEDIA_VARIANT_WIDTHS as $vw) {
            $tw = min($vw, $width); // never upscale
            $th = (int) round($height * $tw / $width);
            $dst = imagecreatetruecolor($tw, $th);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $width, $height);
            @imagewebp($dst, media_root() . '/' . $rel_dir . '/' . $name . '-' . $vw . '.webp', MEDIA_WEBP_QUALITY);
            imagedestroy($dst);
        }
        imagedestroy($src);
    }

    $stmt = db()->prepare(
        'INSERT INTO media (file_path, type, alt_text, width, height, size_bytes) VALUES (?, "image", "", ?, ?, ?)'
    );
    $stmt->execute([$rel_path, $width, $height, (int) $file['size']]);

    return ['ok' => true, 'media' => [
        'id'         => (int) db()->lastInsertId(),
        'file_path'  => $rel_path,
        'width'      => $width,
        'height'     => $height,
        'size_bytes' => (int) $file['size'],
    ]];
}

/** All variant paths (relative) belonging to a media row. */
function media_variant_paths(string $rel_path): array
{
    $dir = dirname($rel_path);
    $name = pathinfo($rel_path, PATHINFO_FILENAME);
    $paths = [$dir . '/' . $name . '.webp'];
    foreach (MEDIA_VARIANT_WIDTHS as $vw) {
        $paths[] = $dir . '/' . $name . '-' . $vw . '.webp';
    }
    return $paths;
}

/** Delete a media row and all of its files. */
function media_delete(int $id): bool
{
    $stmt = db()->prepare('SELECT file_path FROM media WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }
    foreach (array_merge([$row['file_path']], media_variant_paths($row['file_path'])) as $rel) {
        $abs = media_root() . '/' . $rel;
        if (is_file($abs)) {
            @unlink($abs);
        }
    }
    db()->prepare('DELETE FROM media WHERE id = ?')->execute([$id]);
    return true;
}
