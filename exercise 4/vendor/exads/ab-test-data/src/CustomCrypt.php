<?php

/**
 * PHP Version 7.4
 *
 * Custom Crypt File
 *
 * @category Static_Class
 * @package  Exads\Assessment
 * @license  GPLv3 https://www.gnu.org/licenses/gpl-3.0.en.html
 * @link     https://packagist.org/packages/exads/ab-test-data
 */

namespace Exads;

/**
 * Custom Encryption / Decryption Class
 */
class CustomCrypt
{
    /**
     * Decrypt SIMPLE encrypted data parameter.
     */
    public static function decrypt($encodedData)
    {
        $decodedData = $encodedData;
        $decodedData = str_replace(" ", "+", $decodedData);
        $decodedData = strtr($decodedData, "._-", "+/=");
        $decodedData = base64_decode($decodedData);

        $decompressedData = @gzdecode($decodedData);

        return ($decompressedData === false) ? $decodedData : $decompressedData;
    }

    /**
     * SIMPLE Encrypt data parameter.
     */
    public static function encrypt($data)
    {
        $encodedData = gzencode($data);
        $encodedData = base64_encode($encodedData);
        $encodedData = strtr($encodedData, "+/=", "._-");

        return $encodedData;
    }
}
