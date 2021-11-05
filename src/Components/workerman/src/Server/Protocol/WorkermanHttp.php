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
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;

/**
 * Class Http.
 */
class WorkermanHttp
{
    /**
     * Request class name.
     */
    protected static string $_requestClass = 'Workerman\Protocols\Http\Request';

    /**
     * Session name.
     */
    protected static string $_sessionName = 'PHPSID';

    /**
     * Upload tmp dir.
     */
    protected static string $_uploadTmpDir = '';

    /**
     * Open cache.
     */
    protected static bool $_enableCache = true;

    /**
     * Get or set session name.
     */
    public static function sessionName(?string $name = null): string
    {
        if (null !== $name && '' !== $name)
        {
            static::$_sessionName = (string) $name;
        }

        return static::$_sessionName;
    }

    /**
     * Get or set the request class name.
     */
    public static function requestClass(?string $class_name = null): string
    {
        if ($class_name)
        {
            static::$_requestClass = $class_name;
        }

        return static::$_requestClass;
    }

    /**
     * Enable or disable Cache.
     */
    public static function enableCache(bool $value): void
    {
        static::$_enableCache = $value;
    }

    /**
     * Check the integrity of the package.
     */
    public static function input(string $recv_buffer, TcpConnection $connection): int
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
            if ($recv_len = \strlen($recv_buffer) >= 16384)
            {
                $connection->close("HTTP/1.1 413 Request Entity Too Large\r\n\r\n");

                return 0;
            }

            return 0;
        }

        $head_len = $crlf_pos + 4;
        $method = strstr($recv_buffer, ' ', true);

        if ('GET' === $method || 'OPTIONS' === $method || 'HEAD' === $method || 'DELETE' === $method)
        {
            if (!isset($recv_buffer[512]))
            {
                $input[$recv_buffer] = $head_len;
                if (\count($input) > 512)
                {
                    unset($input[key($input)]);
                }
            }
        }
        elseif ('POST' !== $method && 'PUT' !== $method)
        {
            $connection->close("HTTP/1.1 400 Bad Request\r\n\r\n", true);

            return 0;
        }

        $header = substr($recv_buffer, 0, $crlf_pos);
        if ($pos = strpos($header, "\r\nContent-Length: "))
        {
            $length = $head_len + (int) substr($header, $pos + 18, 10);
        }
        elseif (preg_match("/\r\ncontent-length: ?(\d+)/i", $header, $match))
        {
            $length = $head_len + $match[1];
        }
        else
        {
            $length = $head_len;
        }

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

    /**
     * Http decode.
     */
    public static function decode(string $recv_buffer, TcpConnection $connection): Request
    {
        static $requests = [];
        $cacheable = static::$_enableCache && !isset($recv_buffer[512]);
        if (true === $cacheable && isset($requests[$recv_buffer]))
        {
            $request = $requests[$recv_buffer];
            $request->connection = $connection;
            // @phpstan-ignore-next-line
            $connection->__request = $request;
            $request->properties = [];

            return $request;
        }
        $request = new static::$_requestClass($recv_buffer);
        $request->connection = $connection;
        // @phpstan-ignore-next-line
        $connection->__request = $request;
        if (true === $cacheable)
        {
            $requests[$recv_buffer] = $request;
            if (\count($requests) > 512)
            {
                unset($requests[key($requests)]);
            }
        }

        return $request;
    }

    /**
     * Http encode.
     *
     * @param string|Response|null $response
     */
    public static function encode($response, TcpConnection $connection): string
    {
        if (isset($connection->__request))
        {
            $connection->__request->session = null;
            $connection->__request->connection = null;
            $connection->__request = null;
        }
        if (is_scalar($response) || null === $response)
        {
            $ext_header = '';
            if (isset($connection->__header))
            {
                foreach ($connection->__header as $name => $value)
                {
                    if (\is_array($value))
                    {
                        foreach ($value as $item)
                        {
                            $ext_header = "$name: $item\r\n";
                        }
                    }
                    else
                    {
                        $ext_header = "$name: $value\r\n";
                    }
                }
                unset($connection->__header);
            }
            $body_len = \strlen($response);

            return "HTTP/1.1 200 OK\r\nServer: workerman\r\n{$ext_header}Connection: keep-alive\r\nContent-Type: text/html;charset=utf-8\r\nContent-Length: $body_len\r\n\r\n$response";
        }

        if (isset($connection->__header))
        {
            $response->withHeaders($connection->__header);
            unset($connection->__header);
        }

        if ($response->file)
        {
            $file = $response->file['file'];
            $offset = $response->file['offset'];
            $length = $response->file['length'];
            $file_size = (int) filesize($file);
            $body_len = $length > 0 ? $length : $file_size - $offset;
            $response->withHeaders([
                'Content-Length' => $body_len,
                'Accept-Ranges'  => 'bytes',
            ]);
            if ($offset || $length)
            {
                $offset_end = $offset + $body_len - 1;
                $response->header('Content-Range', "bytes $offset-$offset_end/$file_size");
            }
            if ($body_len < 2 * 1024 * 1024)
            {
                $connection->send((string) $response . file_get_contents($file, false, null, $offset, $body_len), true);

                return '';
            }
            $handler = fopen($file, 'r');
            if (false === $handler)
            {
                $connection->close(new Response(403, [], '403 Forbidden'));

                return '';
            }
            $connection->send((string) $response, true);
            static::sendStream($connection, $handler, $offset, $length);

            return '';
        }

        return (string) $response;
    }

    /**
     * Send remainder of a stream to client.
     *
     * @param resource $handler
     */
    protected static function sendStream(TcpConnection $connection, $handler, int $offset = 0, int $length = 0): void
    {
        // @phpstan-ignore-next-line
        $connection->bufferFull = false;
        if (0 !== $offset)
        {
            fseek($handler, $offset);
        }
        $offset_end = $offset + $length;
        // Read file content from disk piece by piece and send to client.
        $do_write = function () use ($connection, $handler, $length, $offset_end) {
            // Send buffer not full.
            while (false === $connection->bufferFull)
            {
                // Read from disk.
                $size = 1024 * 1024;
                if (0 !== $length)
                {
                    $tell = ftell($handler);
                    $remain_size = $offset_end - $tell;
                    if ($remain_size <= 0)
                    {
                        fclose($handler);
                        // @phpstan-ignore-next-line
                        $connection->onBufferDrain = null;

                        return;
                    }
                    $size = $remain_size > $size ? $size : $remain_size;
                }

                $buffer = fread($handler, $size);
                // Read eof.
                if ('' === $buffer || false === $buffer)
                {
                    fclose($handler);
                    // @phpstan-ignore-next-line
                    $connection->onBufferDrain = null;

                    return;
                }
                $connection->send($buffer, true);
            }
        };
        // Send buffer full.
        $connection->onBufferFull = function ($connection) {
            $connection->bufferFull = true;
        };
        // Send buffer drain.
        $connection->onBufferDrain = function ($connection) use ($do_write) {
            $connection->bufferFull = false;
            $do_write();
        };
        $do_write();
    }

    /**
     * Set or get uploadTmpDir.
     *
     * @return bool|string
     */
    public static function uploadTmpDir(?string $dir = null)
    {
        if (null !== $dir)
        {
            static::$_uploadTmpDir = $dir;
        }
        if ('' === static::$_uploadTmpDir)
        {
            if ($upload_tmp_dir = ini_get('upload_tmp_dir'))
            {
                static::$_uploadTmpDir = $upload_tmp_dir;
            }
            elseif ($upload_tmp_dir = sys_get_temp_dir())
            {
                static::$_uploadTmpDir = $upload_tmp_dir;
            }
        }

        return static::$_uploadTmpDir;
    }
}
