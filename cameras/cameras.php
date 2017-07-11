<?php
//// Set variables

// PuckaPi source
$vpnip = "10.0.5.6";

// Camera #
if (isset($_GET["cam"])) {
	$cam = $_GET["cam"];
	$overlay = (isset($_GET["overlay"])) ? $_GET["overlay"] : "n";
}
else { 
	$cam = -1;
}

//// GET THE SOURCE IMAGE
// Pull from camera still image output (auth required)
switch ($cam) {
	case 1:
		$imageurl = "http://$vpnip:18122/Streaming/Channels/1/picture";
		$txtLocation = "Bird Feeders";
		break;
	case 2:
		$imageurl = "http://$vpnip:18123/Streaming/Channels/1/picture";
		$txtLocation = "Pole Barn";
		break;
	case 3:
		$imageurl = "http://10.0.0.10/Streaming/Channels/1/picture";
		$txtLocation = "Driveway";
		break;
	case 9:
		$imageurl = "test";
		$txtLocation = "Test";
		break;
	case 0:
		$imageurl = "traffic";
		$txtLocation = "Traffic Cams";
		break;
	default:
		die("Invalid camera: $cam");
}
if ($imageurl == "traffic") {
	$srcImage = imagecreatetruecolor(1280,720);
	$trafcam1 = imagecreatefromstring(file_get_contents('http://www.511wi.gov/web/CctvImageHandler.ashx?networkID=MILWAUKEE&deviceID=CCTV-70-0074')); // County Z
	imagecopyresampled($srcImage, $trafcam1, 0, 44, 3, 3, 426, 315, 346, 256);
	imagedestroy($trafcam1);
	$trafcam2 = imagecreatefromstring(file_get_contents('http://www.511wi.gov/web/CctvImageHandler.ashx?networkID=MILWAUKEE&deviceID=CCTV-70-0061')); // Wis 26
	imagecopyresampled($srcImage, $trafcam2, 427, 44, 3, 3, 426, 315, 346, 256);
	imagedestroy($trafcam2);
	$trafcam3 = imagecreatefromstring(file_get_contents('http://www.511wi.gov/web/CctvImageHandler.ashx?networkID=MILWAUKEE&deviceID=CCTV-70-0062')); // Wis 44
	imagecopyresampled($srcImage, $trafcam3, 854, 44, 3, 3, 426, 315, 346, 256);
	imagedestroy($trafcam3);
	$trafcam4 = imagecreatefromstring(file_get_contents('http://www.511wi.gov/web/CctvImageHandler.ashx?networkID=MILWAUKEE&deviceID=CCTV-70-0063')); // 9th Ave
	imagecopyresampled($srcImage, $trafcam4, 0, 360, 3, 3, 426, 315, 346, 256);
	imagedestroy($trafcam4);
	$trafcam5 = imagecreatefromstring(file_get_contents('http://www.511wi.gov/web/CctvImageHandler.ashx?networkID=MILWAUKEE&deviceID=CCTV-70-0064')); // Wis 21
	imagecopyresampled($srcImage, $trafcam5, 427, 360, 3, 3, 426, 315, 346, 256);
	imagedestroy($trafcam5);
	$trafcam6 = imagecreatefromstring(file_get_contents('http://www.511wi.gov/web/CctvImageHandler.ashx?networkID=MILWAUKEE&deviceID=CCTV-70-0065')); // US 45
	imagecopyresampled($srcImage, $trafcam6, 854, 360, 3, 3, 426, 315, 346, 256);
	imagedestroy($trafcam6);
	$txtDateStamp = NULL;
}
elseif ($imageurl == "test") {
	$srcImage = imagecreatefromstring(file_get_contents('/volume1/web/cam/error.jpg'));
}
else {
	include 'cam-passwd.php';//stores camera password as $curlpass
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $imageurl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_USERPWD, "admin:$curlpass");
	$data = curl_exec($ch);
	if(curl_errno($ch)){
		// Show placeholder image and don't try to get sensor data or generate overlay
		$srcImage = imagecreatefromstring(file_get_contents('https://puckaway.org/cam/error.jpg'));
		$noDHT = 1;
		$disableTTF = 1;
	}
	else {
		// Create image from camera capture stream
		$srcImage = imagecreatefromstring($data);
	}
	curl_close($ch);
	
	if (empty($noDHT)) {
		//// TEMPERATURE AND HUMIDITY DATA
		// This is gathered from a CSV file on the Pi server
		function getDHT($sensor) {
			//Sensor values:
			//  3 - Outdoor
			//  4 - Indoor
			global $vpnip;
			$file = "http://$vpnip/dht-$sensor.txt";
			$data = str_getcsv(file_get_contents($file));
			if ($data[1] != NULL) {
				$outh = ($data[2] == 99.9) ? 100 : $data[2]; // rounding fix for 100% humidity
				$out = html_entity_decode("$data[1]&deg;\r\n$outh%");
			}
			else {
				$out = "Error.";
			}
			return $out;
		}
		// Text content
		switch ($overlay) {
			case "b":
				$txtDHT2 = "Outdoor\r\n".getDHT(3);
				$txtDHT1 = "Indoor\r\n".getDHT(4);
				break;
			case "i":
				$txtDHT1 = getDHT(4);
				break;
			case "o":
				$txtDHT1 = getDHT(3);
				break;
			case "x":
				$disableTTF = 1;
				break;
		}
	}
	$txtDateStamp = date('Y/m/d g:i:s a');
}
function writeImgText($image,$content,$size,$pos) {
	// Path to image font
	$font = './cam.ttf';
	// Define text color (currently white $xith black shadow)
	$color = imagecolorallocate($image, 255, 255, 255);
	$shadow = imagecolorallocate($image, 0, 0, 0);
	// Text angle (0 = straight left to right)
	$angle = 0;
	$box  = imagettfbbox($size,$angle,$font,$content);
	$boxpad = imagesx($image) - 20 - $box[4] - $box[6];
	// Figure out where to put it
	switch($pos) {
		case 1: // lower right corner
			$x = $boxpad; 
			$y  = imagesy($image) - 12;
			break;
		case 2: // lower left corner
			$x = 20; 
			$y = imagesy($image) - 12;
			break;
		case 3: // upper right corner
			$x = $boxpad; 
			$y = 32;
			break;
		case 4: // upper right corner + space for other box
			$x = (2 * $boxpad) - imagesx($image);
			$y = 32;
			break;
		default: // just dump it upper left with no padding
			$x = 0;
			$y = 0;
	}
	// Create text shadows
	imagettftext($image,$size,$angle,$x+1,$y+1,$shadow,$font,$content);
	imagettftext($image,$size,$angle,$x+1,$y-1,$shadow,$font,$content);
	imagettftext($image,$size,$angle,$x-1,$y+1,$shadow,$font,$content);
	imagettftext($image,$size,$angle,$x-1,$y-1,$shadow,$font,$content);
	// Write actual text
	imagettftext($image,$size,$angle,$x,$y,$color,$font,$content);
}
if (empty($disableTTF)) {
	writeImgText($srcImage,$txtDateStamp,18,1);
	writeImgText($srcImage,$txtLocation,24,2);
	writeImgText($srcImage,$txtDHT1,16,3);
	writeImgText($srcImage,$txtDHT2,16,4);
}
header('Content-Type: image/jpeg');
imagejpeg($srcImage);
?>
