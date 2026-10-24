<?php

namespace App\Services;

use Illuminate\Http\Request;
use RuntimeException;

class ContactCaptcha
{
    public function image(Request $request): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($index = 0; $index < 5; $index++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        $image = imagecreatetruecolor(220, 72);
        $background = imagecolorallocate($image, 245, 242, 250);
        imagefill($image, 0, 0, $background);

        for ($index = 0; $index < 12; $index++) {
            $color = imagecolorallocate($image, random_int(160, 210), random_int(150, 200), random_int(170, 220));
            imageline($image, random_int(0, 219), random_int(0, 71), random_int(0, 219), random_int(0, 71), $color);
        }

        for ($index = 0; $index < 5; $index++) {
            $glyph = imagecreatetruecolor(12, 20);
            $clear = imagecolorallocate($glyph, 245, 242, 250);
            imagefill($glyph, 0, 0, $clear);
            $ink = imagecolorallocate($glyph, random_int(30, 70), random_int(20, 55), random_int(65, 100));
            imagestring($glyph, 5, 1, 2, $code[$index], $ink);
            $scaled = imagescale($glyph, 30, 50);
            $rotated = imagerotate($scaled, random_int(-15, 15), $clear);
            imagecopy($image, $rotated, 12 + $index * 40, random_int(5, 12), 0, 0, imagesx($rotated), imagesy($rotated));
        }

        $stream = fopen('php://temp', 'r+');
        if ($stream === false || !imagepng($image, $stream)) {
            throw new RuntimeException('Unable to render the contact CAPTCHA.');
        }
        rewind($stream);
        $png = stream_get_contents($stream);
        fclose($stream);
        if ($png === false) {
            throw new RuntimeException('Unable to read the contact CAPTCHA.');
        }

        $request->session()->put('contact_captcha', [
            'digest' => hash('sha256', $code),
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        return $png;
    }

    public function verify(Request $request, string $answer): bool
    {
        $challenge = $request->session()->pull('contact_captcha');

        return is_array($challenge)
            && isset($challenge['digest'], $challenge['expires_at'])
            && $challenge['expires_at'] > now()->timestamp
            && hash_equals($challenge['digest'], hash('sha256', strtoupper(trim($answer))));
    }
}
