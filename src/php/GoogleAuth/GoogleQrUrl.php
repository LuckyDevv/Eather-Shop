<?php

declare(strict_types=1);
final class GoogleQrUrl
{
    private function __construct(){}

    /**
     * @param string      $accountName The account name to show and identify
     * @param string      $secret      The secret is the generated secret unique to that user
     * @param string|null $issuer      Where you log in to
     * @param int         $size        Image size in pixels, 200 will make it 200x200
     */
    public static function generate(string $accountName, string $secret, ?string $issuer = null, int $size = 200): string
    {
        if ('' === $accountName || false !== strpos($accountName, ':')) {
            throw new RuntimeException('Invalid Account Name: '.$accountName);
        }
        if ('' === $secret) {
            throw new RuntimeException('Invalid Secret Code');
        }
        $label = $accountName;
        $otpauthString = 'otpauth://totp/%s?secret=%s';

        if (null !== $issuer) {
            if ('' === $issuer || false !== strpos($issuer, ':')) {
                throw new RuntimeException('Invalid Issuer: '.$issuer);
            }
            $label = $issuer.':'.$label;
            $otpauthString .= '&issuer=%s';
        }

        $otpauthString = rawurlencode(sprintf($otpauthString, $label, $secret, $issuer));
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?size=%1$dx%1$d&data=%2$s&ecc=M',
            $size,
            $otpauthString
        );
    }
}