<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Protocol;

/*
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http;

/**
 * Class Http.
 */
class WorkermanHttp extends Http
{
    /**
     * {@inheritDoc}
     */
    public static function input($recv_buffer, TcpConnection $connection)
    {
        static $input = [];
        if (!isset($recv_buffer[512]) && isset($input[$recv_buffer]))
        {
            return $input[$recv_buffer];
        }
        $crlf_pos = strpos($recv_buffer, "\r\n\r\n");
        if (false === $crlf_pos)
        {
            // Judge whether the package length exceeds the limit.
            if (\strlen($recv_buffer) >= 16384)
            {
                $connection->close("HTTP/1.1 413 Request Entity Too Large\r\n\r\n", true);

                return 0;
            }

            return 0;
        }

        $length = $crlf_pos + 4;
        $method = strstr($recv_buffer, ' ', true);

        if (!\in_array($method, ['GET', 'POST', 'OPTIONS', 'HEAD', 'DELETE', 'PUT', 'PATCH']))
        {
            $connection->close("HTTP/1.1 400 Bad Request\r\n\r\n", true);

            return 0;
        }

        $header = substr($recv_buffer, 0, $crlf_pos);
        $has_content_length = false;
        if ($pos = strpos($header, "\r\nContent-Length: "))
        {
            $length = $length + (int) substr($header, $pos + 18, 10);
            $has_content_length = true;
        }
        elseif (preg_match("/\r\ncontent-length: ?(\d+)/i", $header, $match))
        {
            $length = $length + $match[1];
            $has_content_length = true;
        }

        if ($has_content_length)
        {
            if ($length > $connection->maxPackageSize)
            {
                $connection->close("HTTP/1.1 413 Request Entity Too Large\r\n\r\n", true);

                return 0;
            }
        }
        // RFC 规定可以不存在 content-length
        // https://www.rfc-editor.org/rfc/rfc7230#section-3.3.2
        // elseif (\in_array($method, ['POST', 'PUT', 'PATCH']))
        // {
        //     $connection->close("HTTP/1.1 400 Bad Request\r\n\r\n", true);

        //     return 0;
        // }

        if (!isset($recv_buffer[512]))
        {
            $input[$recv_buffer] = $length;
            if (\count($input) > 512)
            {
                unset($input[key($input)]);
            }
        }

        return $length;
    }
}
