<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function generate()
    {
        $captchaCode = '';
        $captchaImageHeight = 50;
        $captchaImageWidth = 130;
        $totalCharactersOnImage = 6;

        // Karakter yang ditampilkan dalam kode captcha.
        $possibleCaptchaLetters = 'bcdfghjkmnpqrstvwxyz23456789';
        $captchaFont = public_path('fonts/monofont.ttf'); // Lokasi font
        $randomCaptchaDots = 50;
        $randomCaptchaLines = 25;
        $captchaTextColor = "0x142864";
        $captchaNoiseColor = "0x142864";

        $count = 0;
        while ($count < $totalCharactersOnImage) {
            $captchaCode .= substr(
                $possibleCaptchaLetters,
                mt_rand(0, strlen($possibleCaptchaLetters) - 1),
                1
            );
            $count++;
        }

        $captchaFontSize = $captchaImageHeight * 0.65;
        $captchaImage = @imagecreate($captchaImageWidth, $captchaImageHeight);

        // Setting warna background dan text
        $backgroundColor = imagecolorallocate($captchaImage, 255, 255, 255);

        $arrayTextColor = $this->hextorgb($captchaTextColor);
        $captchaTextColor = imagecolorallocate(
            $captchaImage,
            $arrayTextColor['red'],
            $arrayTextColor['green'],
            $arrayTextColor['blue']
        );

        $arrayNoiseColor = $this->hextorgb($captchaNoiseColor);
        $imageNoiseColor = imagecolorallocate(
            $captchaImage,
            $arrayNoiseColor['red'],
            $arrayNoiseColor['green'],
            $arrayNoiseColor['blue']
        );

        // Menambahkan titik-titik acak dalam image captcha
        for ($count = 0; $count < $randomCaptchaDots; $count++) {
            imagefilledellipse(
                $captchaImage,
                mt_rand(0, $captchaImageWidth),
                mt_rand(0, $captchaImageHeight),
                2,
                3,
                $imageNoiseColor
            );
        }

        // Menambahkan garis-garis acak dalam image captcha
        for ($count = 0; $count < $randomCaptchaLines; $count++) {
            imageline(
                $captchaImage,
                mt_rand(0, $captchaImageWidth),
                mt_rand(0, $captchaImageHeight),
                mt_rand(0, $captchaImageWidth),
                mt_rand(0, $captchaImageHeight),
                $imageNoiseColor
            );
        }

        // Menampilkan text box dan karakter acak captcha
        $textBox = imagettfbbox($captchaFontSize, 0, $captchaFont, $captchaCode);
        $x = ($captchaImageWidth - $textBox[4]) / 2;
        $y = ($captchaImageHeight - $textBox[5]) / 2;
        imagettftext($captchaImage, $captchaFontSize, 0, $x, $y, $captchaTextColor, $captchaFont, $captchaCode);

        // Menyimpan kode captcha di session Laravel
        session(['captcha' => $captchaCode]);

        // Menampilkan image captcha
        header('Content-Type: image/jpeg');
        imagejpeg($captchaImage);
        imagedestroy($captchaImage);
    }

    public function validateCaptcha(Request $request)
    {
        $inputCaptcha = $request->input('captcha');
        $sessionCaptcha = session('captcha');

        if ($inputCaptcha === $sessionCaptcha) {
            return response()->json(['success' => true, 'message' => 'Captcha valid']);
        } else {
            return response()->json(['success' => false, 'message' => 'Captcha invalid']);
        }
    }

    private function hextorgb($hexstring)
    {
        $integer = hexdec($hexstring);
        return [
            "red" => 0xFF & ($integer >> 0x10),
            "green" => 0xFF & ($integer >> 0x8),
            "blue" => 0xFF & $integer
        ];
    }
}
