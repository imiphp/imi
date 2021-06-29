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
     *
     * @param string $buffer
     *
     * @return int
     */
    public static function input($buffer, TcpConnection $connection)
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
     *
     * @param string $buffer
     *
     * @return string
     */
    public static function decode($buffer)
    {
        return $buffer;
    }

    /**
     * Encode.
     *
     * @param string $buffer
     *
     * @return string
     */
    public static function encode($buffer)
    {
        $total_length = \strlen($buffer);

        return pack('N', $total_length) . $buffer;
    }
}
