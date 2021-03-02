<?php

declare(strict_types=1);

namespace Imi\Workerman\Cron\Protocol;

use Workerman\Connection\TcpConnection;

/**
 * Frame Protocol.
 */
class Frame
{
    /**
     * Check the integrity of the package.
     *
     * @param string        $buffer
     * @param TcpConnection $connection
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
        return substr($buffer, 4);
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
        return pack('N', \strlen($buffer)) . $buffer;
    }
}
