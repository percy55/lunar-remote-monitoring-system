<?php
include("sqlite3.php");
//$serial="051312345";
$serial=$_GET['serial'];
$query= "SELECT recordedat, watts FROM readings where inverter='".$serial."' ORDER BY recordedat DESC LIMIT 288";
$base=sqlite_open("../data/records.sq3", 0666);
$results = $base->query($query);

/* our image dimensions */
header("Content-type: image/png");
$height = 450;
$width = 650;

/* define our colours */
$img = imagecreate($width, $height);
$background_color = imagecolorallocate ($img, 234, 234, 234);
$text_colour = imagecolorallocate ($img, 233, 14, 91);
$graph_colour = imagecolorallocate ($img, 25, 25, 25);
$color1 =  imagecolorallocate($img, 42, 170, 255);
$black =   imagecolorallocate($img, 0, 0, 0);
$red =     imagecolorallocate($img, 255, 0, 0);
$gray =    imagecolorallocate($img, 200, 200, 200);

/* starter variables */
$x_gap=abs($width/288);
$x1=0;
$y1=$height;
$y_max=$height-10;
$thick=1;
$first_one="yes";

$x=10;
$y=10;
$num=0;
while($x<=$width && $y<=$height)
{
        //$prcnt = ((($height-50)-($y-1))/($height-60))*100;
        imageline($img, 21, $y, $width-60, $y, $gray);
        imageline($img, $x, 11, $x, $height-50, $gray);
        //imagestring($im,2,1,$y-10,$prcnt.'W',$red);
        imagestring($img,2, $x-3, $height-40,$num,$red);
        $x += 20;
        $y += 20;
        $num++;
}

/*
imagelinethick($img, 0, 220, 10, 40, $color1, $thick);
imagelinethick($img,60,40,160,140,$color1, $thick);
imagelinethick($img,160,140,210,70,$color1, $thick);
*/

while($row=$results->fetchArray())
{
	$x2=$x1+$x_gap;
	$y2=$y_max-$row['watts'];
	//ImageString($img, 2, $x2, $y2, $row['recordedat'], $graph_colour);
	if($first_one=="no")
	{ // this is to prevent from starting $x1= and $y1=0
		imageline ($img, $x1, $y1, $x2, $y2, $color1); // Drawing the line between two points
		imagestring($img, 0, $x1-1, $y1-5,'.',$red);
	}
	$x1=$x2;
	$y1=$y2;
	$first_one="no";
}

imagestring($img,3,10,$height-20,'Line Graph by: LUNAR Remote Monitoring System',$black);
imagepng($img);
imagedestroy($img);
$base->close();

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
   /* this way it works well only for orthogonal lines
   imagesetthickness($image, $thick);
   return imageline($image, $x1, $y1, $x2, $y2, $color);
   */
   if ($thick == 1) {
       return imageline($image, $x1, $y1, $x2, $y2, $color);
   }
   $t = $thick / 2 - 0.5;
   if ($x1 == $x2 || $y1 == $y2) {
       return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
   }
   $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
   $a = $t / sqrt(1 + pow($k, 2));
   $points = array(
       round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
       round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
       round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
       round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
   );
   imagefilledpolygon($image, $points, 4, $color);
   return imagepolygon($image, $points, 4, $color);
}
?>
