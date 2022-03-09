<?php

// configuration
$verdanaBold = '/usr/share/fonts/truetype/msttcorefonts/Verdana_Bold.ttf'; // location of verdana bold true type font

require_once( 'phpqrcode.php' );

// generate the qr code based on the params
$size = $_GET['size'];
$data =  $_GET['data'];
$margin = 2;
$ecc = 'M'; //$_GET['ecc'];
$qrFile = uniqid('/tmp/qr_') . '.png'; // tmp file name deleted later

QRcode::png('http://qr.rbge.org.uk/' . $data, $qrFile, $ecc, $size, $margin);

// we need the size of this thing a lot
list($width, $height) = getimagesize($qrFile);

// load the qr image and create a target image
$qrImage = imagecreatefrompng($qrFile);

// we can work out how wide the text needs to be
// it must stretch the width of the qr code but not the
// margin. 
$textWidthDesired = $width - (($size * $margin) * 2);
$text = 'RBGE: ' . $data;

$textDimensions = imagettfbbox(20, 0, $verdanaBold, $text);
$textWidthAt20 = $textDimensions[2] - $textDimensions[0];
$textHeightAt20 = $textDimensions[1] - $textDimensions[7];

$scale = ($textWidthDesired / $textWidthAt20);
$fontSize = 20 * $scale;
$textHeight = $textHeightAt20 * $scale;

$heightPlus = $height  + $textHeight + ( $margin * $size);

$outImage = imagecreatetruecolor($width, $heightPlus);

// set up the colours used in the final
$white = imagecolorallocate($outImage, 255, 255, 255);
$green = imagecolorallocate($outImage, 66, 80, 32);

for ($w = 0; $w< $width; $w++){
    for ($h = 0; $h< $heightPlus; $h++){
        
        // if we are beyond the height of the qr code then just white it
        if($h >= $height){
            imagesetpixel($outImage, $w, $h, $white);
            continue;
        }
        
        $qrPixel = imagecolorsforindex($qrImage, imagecolorat($qrImage, $w, $h));
        $qrDensity = $qrPixel['green'];

        $sibDensity = 255;
      
        // not qr should be white
        if($qrDensity > 100){
            imagesetpixel($outImage, $w, $h, $white);
            continue;
        }
        
        // if it is in qr it should be green
        if($qrDensity <= 100){
            imagesetpixel($outImage, $w, $h, $green);
            continue;
        }
        
    }
}

// get rid of the used image
imagedestroy($qrImage);

// adding the txt onto the bottom
imagettftext($outImage, $fontSize, 0, ($margin * $size) -2,   $height + ($margin * $size) + ($textHeight/4) , $green, $verdanaBold, $text);

// draw a border
$borderWidth = $size/4;
if($borderWidth < 2) $borderWdith = 2;
imagefilledrectangle($outImage, 0, 0, $width, $borderWidth, $green);
imagefilledrectangle($outImage, 0, 0, $borderWidth, $heightPlus, $green);
imagefilledrectangle($outImage, $width - $borderWidth, 0, $width, $heightPlus, $green);
imagefilledrectangle($outImage, 0, $heightPlus - $borderWidth, $width, $heightPlus, $green);

header ('Content-Type: image/png');

if(@$_GET['download']){
    header("Content-Disposition: attachment; filename=\"qr_code_$data.png\"");
}

imagepng($outImage);
imagedestroy($outImage);
unlink($qrFile);

?>