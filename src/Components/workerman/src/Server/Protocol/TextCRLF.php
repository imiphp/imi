<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Protocol;

use Workerman\Connection\ConnectionInterface;

/**
 * Text CRLF Protocol.
 */
class TextCRLF
{
    /**
     * Check the integrity of the package.
     */
    public static function input(string $buffer, ConnectionInterface $connection): int
    {
        // Judge whether the package length exceeds the limit.
        if (isset($connection->maxPackageSize) && \strlen($buffer) >= $connection->maxPackageSize)
        {
            $connection->close();

            return 0;
        }
        //  Find the position of  "\r\n".
        $pos = strpos($buffer, "\r\n");
        // No "\r\n", packet length is unknown, continue to wait for the data so return 0.
        if (false === $pos)
        {
            return 0;
        }
        // Return the current package length.
        return $pos + 2;
    }

    /**
     * Encode.
     */
    public static function encode(string $buffer): string
    {
        // Add "\r\n"
        return $buffer . "\r\n";
    }

    /**
     * Decode.
     */
    public static function decode(string $buffer): string
    {
        // Remove "\r\n"
        return rtrim($buffer, "\r\n");
    }
}
