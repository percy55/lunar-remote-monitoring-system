<?php
$inverter=051312345;
header("Content-type: image/png");
$arrval = array(12,123,21,32,77,85,166,176,163,121);
$height = 480;
$width = 740;

$im = imagecreate($width,$height);
$white = imagecolorallocate($im,255,255,255);
$gray = imagecolorallocate($im,200,200,200);
$black = imagecolorallocate($im,0,0,0);
$red = imagecolorallocate($im,255,0,0);
$blue = imagecolorallocate($im,0,0,255);

$x = 21;
$y = 11;
$num = 0;

while($x<=$width && $y<=$height)
{
	$prcnt = ((($height-50)-($y-1))/($height-60))*100;
	imageline($im, 21, $y, $width-10, $y, $gray);
	imageline($im, $x, 11, $x, $height-50, $gray);
	imagestring($im,2,1,$y-10,$prcnt.'W',$red);
	imagestring($im,2,$x-3,$height-40,$num,$red);
	$x += 30;
	$y += 20;
	$num++;
}

/* draw the plot points */
$tx = 20;
$ty = 210;
foreach($arrval as $values)
{
	$cx = $tx + 30;
	$cy = 200-$values;
	imageline($im,$tx,$ty,$cx,$cy,$red);
	imagestring($im,5,$cx-3,$cy-13,'.',$red);
	$ty = $cy;
	$tx = $cx;
}

imageline($im, 20, 11, 20, $height-50, $black);
imageline($im, 20, $height-49, $width-10, $height-49, $black);
imagestring($im,3,10,$height-20,'Line Graph by: LUNAR Remote Monitoring System',$blue);
imagepng($im);
?>
