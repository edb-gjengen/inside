<?php
/*
 *This simple script is written by Gunnar Lium (post@gunnarlium.com).
 *
 *Usage: <img src="imageResize.php?pic=source.type&amp;maxwidth=xxx>
 *Replace source.type with the relevant imagesource and xxx with desired maxwidth.
 *
 *This script supports .gif, .jpg and .png formats, but further extensions should be
 *quite easy to implement.
 *
 *Please feel free to modify and use this script at your own leisure!
 *
 *Update 18/03-05: Added support for transparent gifs/pngs.
 ***********************************************************************************/


// Filename and new maxwidth from GET
$filename  = $_GET['pic'];
if (isset($_GET['maxwidth'])){
  $maxwidth  = $_GET['maxwidth'];
}
if (isset($_GET['maxheight'])){
  $maxheight = $_GET['maxheight'];
}

//Quality setting for jpegs, if not supplied as argument, default value is used (75);
if (isset($_GET['quality'])){
  $quality = $_GET['quality'];
}else {
  $quality = 75;
}

list($width, $height, $format) = getimagesize($filename);

// Define content type and load source
switch ($format){
case 0:
  break;
case 1:
  header('Content-type: image/gif');
  $source = imagecreatefromgif($filename);
  break;
case 2:
  header('Content-type: image/jpeg');
  $source = imagecreatefromjpeg($filename);
  break;
case 3:
  header('Content-type: image/png');
  $source = imagecreatefrompng($filename);
  break;
}

if (isset($maxwidth) && isset($maxheight)){
  // Get new sizes
  if ($width < $maxwidth){
    $newwidth = $width;
    $newheight = $height;
  }else {
    $div = $maxwidth/$width;
    $newwidth = $width * $div;
    $newheight = $height * $div;
  }
  if ($newheight > $maxheight){
    $div = $maxheight/$newheight;
    $newwidth = $newwidth * $div;
    $newheight = $newheight * $div;
  }
}else if (isset($maxwidth)){
  // Get new sizes
  if ($width < $maxwidth){
    $newwidth = $width;
    $newheight = $height;
  }else {
    $div = $maxwidth/$width;
    $newwidth = $width * $div;
    $newheight = $height * $div;
  }
}else if (isset($maxheight)){
  // Get new sizes
  if ($height < $maxheight){
    $newwidth = $width;
    $newheight = $height;
  }else {
    $div = $maxheight/$height;
    $newwidth = $width * $div;
    $newheight = $height * $div;
  }
}

// Load new image
if ($format != 2){
  $newimage = imagecreate($newwidth, $newheight);
  $color = ImageColorAllocate($newimage, 255, 255, 255);
  imagefill($newimage, 0, 0, $color);
}else {
  $newimage = imagecreatetruecolor($newwidth, $newheight);
}
// Resize image
imagecopyresized($newimage, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

// Output image
switch ($format){
case 0:
  break;
case 1:
  imagegif($newimage);
  break;
case 2:
  imagejpeg($newimage, "", $quality);
  break;
case 3:
  imagepng($newimage);
  break;
}

?>
