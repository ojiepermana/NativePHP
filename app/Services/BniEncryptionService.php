<?php

declare(strict_types=1);

namespace App\Services;

class BniEncryptionService
{
    private const TIME_DIFF_LIMIT = 480;

    public function __construct(
        private readonly string $clientId,
        private readonly string $secret
    ) {}

    public function encrypt(array $jsonData): string
    {
        return $this->doubleEncrypt(
            strrev((string) time()).'.'.json_encode($jsonData),
            $this->clientId,
            $this->secret
        );
    }

    public function decrypt(string $hashedString): ?array
    {
        $parsedString = $this->doubleDecrypt($hashedString, $this->clientId, $this->secret);

        [$timestamp, $data] = array_pad(explode('.', $parsedString, 2), 2, null);

        if ($this->isTimestampValid(strrev($timestamp))) {
            return json_decode($data, true);
        }

        return null;
    }

    private function isTimestampValid(string $timestamp): bool
    {
        return abs((int) $timestamp - time()) <= self::TIME_DIFF_LIMIT;
    }

    private function doubleEncrypt(string $string, string $cid, string $secret): string
    {
        $result = $this->enc($string, $cid);
        $result = $this->enc($result, $secret);

        return strtr(rtrim(base64_encode($result), '='), '+/', '-_');
    }

    private function enc(string $string, string $key): string
    {
        $result = '';
        $strLength = strlen($string);
        $keyLength = strlen($key);

        for ($i = 0; $i < $strLength; $i++) {
            $char = substr($string, $i, 1);
            $keyChar = substr($key, ($i % $keyLength) - 1, 1);
            $char = chr((ord($char) + ord($keyChar)) % 128);
            $result .= $char;
        }

        return $result;
    }

    private function doubleDecrypt(string $string, string $cid, string $secret): string
    {
        $result = base64_decode(
            strtr(
                str_pad($string, (int) ceil(strlen($string) / 4) * 4, '=', STR_PAD_RIGHT),
                '-_',
                '+/'
            )
        );

        $result = $this->dec($result, $cid);
        $result = $this->dec($result, $secret);

        return $result;
    }

    private function dec(string $string, string $key): string
    {
        $result = '';
        $strLength = strlen($string);
        $keyLength = strlen($key);

        for ($i = 0; $i < $strLength; $i++) {
            $char = substr($string, $i, 1);
            $keyChar = substr($key, ($i % $keyLength) - 1, 1);
            $char = chr(((ord($char) - ord($keyChar)) + 256) % 128);
            $result .= $char;
        }

        return $result;
    }
}
