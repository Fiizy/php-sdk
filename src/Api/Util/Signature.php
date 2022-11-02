<?php

namespace Fiizy\Api\Util;

use Exception;

/**
 * Signature verification utilities.
 */
class Signature
{
    public const DEFAULT_DIFFERENCE = 300;
    public const HEADER_KEY = 'Fiizy-Signature';

    /**
     * Verify header signature.
     *
     * @param string $secret secret used to generate the signature
     * @param string $header the contents of the signature header
     * @param string $payload the payload
     * @param int|null $difference allowed between headers timestamp and current system time
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function verifyHeader($secret, $header, $payload, $difference = null)
    {
        $timestamp = -1;
        $signatures = [];
        $items = explode(',', $header);

        if (count($items) < 2) {
            throw new Exception('incorrect signature');
        }

        foreach ($items as $item) {
            $itemParts = explode('=', $item, 2);
            if ('t' === $itemParts[0] && is_numeric($itemParts[1])) {
                $timestamp = (int) ($itemParts[1]);
            } else {
                $signatures[] = $itemParts[1];
            }
        }

        if (-1 === $timestamp) {
            throw new Exception('unable to extract timestamp from header');
        }

        if (empty($signatures)) {
            throw new Exception('no signatures found');
        }

        return self::verify($secret, $timestamp, $signatures, $payload, $difference);
    }

    /**
     * Verify signatures.
     *
     * @param string $secret secret used to generate the signature
     * @param int $timestamp the signature timestamp
     * @param array<string> $signatures the signatures
     * @param string $payload the payload
     * @param int|null $difference allowed between headers timestamp and current system time
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function verify($secret, $timestamp, array $signatures, $payload, $difference = null)
    {
        if ($difference > 0 && (abs(time() - $timestamp) > $difference)) {
            throw new Exception('timestamp difference too large');
        }

        $signedPayload = "{$timestamp}.{$payload}";
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);
        $found = false;

        foreach ($signatures as $signature) {
            if (hash_equals($expectedSignature, $signature)) {
                $found = true;
                break;
            }
        }

        return $found;
    }
}
