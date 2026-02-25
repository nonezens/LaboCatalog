<?php
// Usage: php optimize_image.php input_path [max_width=300] [png_compression=6]
if ($argc < 2) {
    echo "Usage: php optimize_image.php input_path [max_width=300] [png_compression=6]\n";
    exit(1);
}

$input = $argv[1];
$maxWidth = isset($argv[2]) ? (int)$argv[2] : 300;
$pngCompression = isset($argv[3]) ? (int)$argv[3] : 6; // 0 (no) - 9 (max)

if (!file_exists($input)) {
    echo "File not found: $input\n";
    exit(2);
}

$info = getimagesize($input);
if (!$info) {
    echo "Unable to read image info.\n";
    exit(3);
}

$mime = $info['mime'];
switch ($mime) {
    case 'image/png':
        $src = imagecreatefrompng($input);
        break;
    case 'image/jpeg':
    case 'image/jpg':
        $src = imagecreatefromjpeg($input);
        break;
    case 'image/gif':
        $src = imagecreatefromgif($input);
        break;
    default:
        echo "Unsupported image type: $mime\n";
        exit(4);
}

$width = imagesx($src);
$height = imagesy($src);

if ($width <= $maxWidth) {
    echo "Image width ($width) <= max width ($maxWidth). Still optimizing compression.\n";
    $newW = $width; $newH = $height;
} else {
    $ratio = $height / $width;
    $newW = $maxWidth;
    $newH = (int)round($maxWidth * $ratio);
}

$dst = imagecreatetruecolor($newW, $newH);

// Preserve transparency for PNG and GIF
if ($mime === 'image/png' || $mime === 'image/gif') {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
    imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
}

imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

$tmp = $input . '.opt';

if ($mime === 'image/png') {
    // PNG compression: 0 (no compression) to 9
    imagepng($dst, $tmp, max(0, min(9, $pngCompression)));
} elseif ($mime === 'image/jpeg' || $mime === 'image/jpg') {
    imagejpeg($dst, $tmp, 85);
} elseif ($mime === 'image/gif') {
    imagegif($dst, $tmp);
}

// Optionally create a WebP copy if supported
if (function_exists('imagewebp')) {
    $webpPath = preg_replace('/\.[^.]+$/', '.webp', $input);
    imagewebp($dst, $webpPath, 80);
}

imagedestroy($src);
imagedestroy($dst);

// Replace original with optimized
if (file_exists($tmp)) {
    // backup original
    copy($input, $input . '.bak');
    rename($tmp, $input);
    echo "Optimized and replaced: $input (backup at {$input}.bak)\n";
} else {
    echo "Optimization failed, temp file not created.\n";
    exit(5);
}

exit(0);
