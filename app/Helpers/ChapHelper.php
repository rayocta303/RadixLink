<?php

namespace App\Helpers;

class ChapHelper
{
    public static function verify(string $password, string $chapPassword, ?string $chapChallenge): bool
    {
        if (empty($chapPassword) || empty($chapChallenge)) {
            return false;
        }

        $chapPassword = self::hexToStr($chapPassword);
        $chapChallenge = self::hexToStr($chapChallenge);

        if (strlen($chapPassword) < 17) {
            return false;
        }

        $chapId = $chapPassword[0];
        $chapResponse = substr($chapPassword, 1, 16);

        $expectedResponse = md5($chapId . $password . $chapChallenge, true);

        return hash_equals($expectedResponse, $chapResponse);
    }

    public static function generateChapResponse(string $chapId, string $password, string $challenge): string
    {
        $response = md5(chr($chapId) . $password . self::hexToStr($challenge), true);
        return sprintf('%02x', $chapId) . bin2hex($response);
    }

    public static function hexToStr(string $hex): string
    {
        $hex = str_replace(['0x', ' ', '-'], '', $hex);
        
        if (!ctype_xdigit($hex) || strlen($hex) % 2 !== 0) {
            return $hex;
        }

        return hex2bin($hex);
    }

    public static function strToHex(string $str): string
    {
        return bin2hex($str);
    }

    public static function verifyMschap(string $password, string $ntResponse, string $authChallenge, string $peerChallenge): bool
    {
        return false;
    }

    public static function verifyMschap2(
        string $username,
        string $password,
        string $authChallenge,
        string $peerChallenge,
        string $ntResponse
    ): bool {
        return false;
    }
}
