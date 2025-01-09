<?php
session_start();
// if (!isset($_SESSION['captcha'])) {
//     error_log("Captcha session not set.");
// } else {
//     error_log("Captcha session value: " . $_SESSION['captcha']);
// }




$captcha_code = '';
$captcha_image_height = 50;
$captcha_image_width = 130;
$total_characters_on_image = 6;

//Karakter yang ditampilkan dalam code captcha.
$possible_captcha_letters = 'bcdfghjkmnpqrstvwxyz23456789';
$captcha_font = __DIR__ . '/monofont.ttf'; // Lokasi yang benar untuk font


$random_captcha_dots = 50;
$random_captcha_lines = 25;
$captcha_text_color = "0x142864";
$captcha_noise_color = "0x142864";


$count = 0;
while ($count < $total_characters_on_image) { 
$captcha_code .= substr(
	$possible_captcha_letters,
	mt_rand(0, strlen($possible_captcha_letters)-1),
	1);
$count++;
}

$captcha_font_size = $captcha_image_height * 0.65;
$captcha_image = @imagecreate(
	$captcha_image_width,
	$captcha_image_height
	);

/* setting the background, text and noise colours here */
$background_color = imagecolorallocate(
	$captcha_image,
	255,
	255,
	255
	);

$array_text_color = hextorgb($captcha_text_color);
$captcha_text_color = imagecolorallocate(
	$captcha_image,
	$array_text_color['red'],
	$array_text_color['green'],
	$array_text_color['blue']
	);

$array_noise_color = hextorgb($captcha_noise_color);
$image_noise_color = imagecolorallocate(
	$captcha_image,
	$array_noise_color['red'],
	$array_noise_color['green'],
	$array_noise_color['blue']
	);

/* menampilkan titik-titik acak dalam image captcha */
for( $count=0; $count<$random_captcha_dots; $count++ ) {
imagefilledellipse(
	$captcha_image,
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	2,
	3,
	$image_noise_color
	);
}

/* menampilkan garis-garis acak dalam image captcha */
for( $count=0; $count<$random_captcha_lines; $count++ ) {
imageline(
	$captcha_image,
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	$image_noise_color
	);
}

/* Menampilkan text box dan 6 karakter acak dalam captcha */
$text_box = imagettfbbox(
	$captcha_font_size,
	0,
	$captcha_font,
	$captcha_code
	); 
$x = ($captcha_image_width - $text_box[4])/2;
$y = ($captcha_image_height - $text_box[5])/2;
imagettftext(
	$captcha_image,
	$captcha_font_size,
	0,
	$x,
	$y,
	$captcha_text_color,
	$captcha_font,
	$captcha_code
	);

/* Menampilkan image captcha dalam halaman web */
header('Content-Type: image/jpeg'); 
imagejpeg($captcha_image);
imagedestroy($captcha_image);
$_SESSION['captcha'] = $captcha_code;

function hextorgb ($hexstring){
  $integar = hexdec($hexstring);
  return array("red" => 0xFF & ($integar >> 0x10),
               "green" => 0xFF & ($integar >> 0x8),
               "blue" => 0xFF & $integar);
			   }
?>