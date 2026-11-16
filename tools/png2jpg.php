<?php
// Convert ComfyUI PNG output -> 800x600 JPG. Usage: php tools/png2jpg.php <src.png> <dst.jpg>
[$_, $src, $dst] = $argv + [null, null, null];
if (! $src || ! $dst || ! file_exists($src)) {
    fwrite(STDERR, "usage: php tools/png2jpg.php <src.png> <dst.jpg>\n");
    exit(1);
}
$s = imagecreatefrompng($src);
if (! $s) {
    exit(2);
}
$d = imagecreatetruecolor(800, 600);
imagecopyresampled($d, $s, 0, 0, 0, 0, 800, 600, imagesx($s), imagesy($s));
imagejpeg($d, $dst, 82);
echo "OK $dst\n";
