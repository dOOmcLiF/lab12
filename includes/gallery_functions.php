<?php
function create_thumbnail($source, $dest, $max_size = 200) {
    list($width, $height) = getimagesize($source);

    $ratio = $width / $height;

    if ($ratio > 1) {
        $new_width = $max_size;
        $new_height = $max_size / $ratio;
    } else {
        $new_width = $max_size * $ratio;
        $new_height = $max_size;
    }

    $src = imagecreatefromjpeg($source);
    $dst = imagecreatetruecolor($new_width, $new_height);

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagejpeg($dst, $dest, 85);

    imagedestroy($src);
    imagedestroy($dst);
}

function add_watermark($source, $watermark_path = 'watermark.png') {
    if (!file_exists($watermark_path)) return;

    $image = imagecreatefromjpeg($source);
    $watermark = imagecreatefrompng($watermark_path);

    $w_w = imagesx($watermark);
    $w_h = imagesy($watermark);

    $i_w = imagesx($image);
    $i_h = imagesy($image);

    $dest_x = $i_w - $w_w - 10;
    $dest_y = $i_h - $w_h - 10;

    imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $w_w, $w_h);

    imagejpeg($image, $source, 90);

    imagedestroy($image);
    imagedestroy($watermark);
}
?>