<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Protocol;

use Workerman\Connection\TcpConnection;

/**
 * Frame Protocol with length.
 */
class FrameWithLength
{
    /**
     * Check the integrity of the package.
     */
    public static function input(string $buffer, TcpConnection $connection): int
    {
        if (\strlen($buffer) < 4)
        {
            return 0;
        }
        $unpack_data = unpack('Ntotal_length', $buffer);

        return $unpack_data['total_length'] + 4;
    }

    /**
     * Decode.
     */
    public static function decode(string $buffer): string
    {
        return $buffer;
    }

    /**
     * Encode.
     */
    public static function encode(string $buffer): string
    {
        $total_length = \strlen($buffer);

        return pack('N', $total_length) . $buffer;
    }
}
