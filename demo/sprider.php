<?php
$url = 'http://www.iyi8.com/uploadfile/2014/0521/20140521105216901.jpg';
$content = file_get_contents($url);
$filename = 'tmp.jpg';
file_put_contents($filename, $content);
$url = 'http://www.easyicon.net/api/resizeApi.php?id=1200814&size=128';
file_put_contents('logo.png', file_get_contents($url));
$im = imagecreatefromjpeg($filename);
$logo = imagecreatefrompng('logo.png');
$size = getimagesize('logo.png');
imagecopy($im, $logo, 15, 15, 0, 0, $size[0], $size[1]);
header("content-type: image/jpeg");
imagejpeg($im);