<?php

declare(strict_types=1);
final class GoogleQrUrl
{
    private function __construct(){}

    public static function generate(string $accountName, string $secret, ?string $issuer = null, int $size = 200): string
    {
        if ('' === $accountName || str_contains($accountName, ':')) {
            throw new RuntimeException('Invalid Account Name: '.$accountName);
        }
        if ('' === $secret) {
            throw new RuntimeException('Invalid Secret Code');
        }
        $label = $accountName;
        $otpauthString = 'otpauth://totp/%s?secret=%s';

        if (null !== $issuer) {
            if ('' === $issuer || str_contains($issuer, ':')) {
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