<?php

namespace Imi\Server\Http\Message\Proxy;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Http\Contract\IResponse;
use Psr\Http\Message\StreamInterface;

/**
 * @Bean("HttpResponseProxy")
 * 自动切换协程上下文的响应代理类
 */
class ResponseProxy implements IResponse
{
    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int status code
     */
    public function getStatusCode()
    {
        return static::getResponseInstance()->getStatusCode();
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int    $code         the 3-digit integer result code to set
     * @param string $reasonPhrase the reason phrase to use with the
     *                             provided status code; if none is provided, implementations MAY
     *                             use the defaults as suggested in the HTTP specification
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException for invalid status code arguments
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        return static::getResponseInstance()->withStatus($code, $reasonPhrase);
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int    $code         the 3-digit integer result code to set
     * @param string $reasonPhrase the reason phrase to use with the
     *                             provided status code; if none is provided, implementations MAY
     *                             use the defaults as suggested in the HTTP specification
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException for invalid status code arguments
     */
    public function setStatus(int $code, string $reasonPhrase = ''): IResponse
    {
        return static::getResponseInstance()->setStatus($code, $reasonPhrase);
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string reason phrase; must return an empty string if none present
     */
    public function getReasonPhrase()
    {
        return static::getResponseInstance()->getReasonPhrase();
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version
     */
    public function getProtocolVersion()
    {
        return static::getResponseInstance()->getProtocolVersion();
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     *
     * @return IResponse
     */
    public function withProtocolVersion($version)
    {
        return static::getResponseInstance()->withProtocolVersion($version);
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * @param string $version HTTP protocol version
     *
     * @return IResponse
     */
    public function setProtocolVersion(string $version): IResponse
    {
        return static::getResponseInstance()->setProtocolVersion($version);
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *                    key MUST be a header name, and each value MUST be an array of strings
     *                    for that header.
     */
    public function getHeaders()
    {
        return static::getResponseInstance()->getHeaders();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name case-insensitive header field name
     *
     * @return bool Returns true if any header names match the given header
     *              name using a case-insensitive string comparison. Returns false if
     *              no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return static::getResponseInstance()->hasHeader($name);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name case-insensitive header field name
     *
     * @return string[] An array of string values as provided for the given
     *                  header. If the header does not appear in the message, this method MUST
     *                  return an empty array.
     */
    public function getHeader($name)
    {
        return static::getResponseInstance()->getHeader($name);
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name case-insensitive header field name
     *
     * @return string A string of values as provided for the given header
     *                concatenated together using a comma. If the header does not appear in
     *                the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        return static::getResponseInstance()->getHeaderLine($name);
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name  case-insensitive header field name
     * @param string|string[] $value header value(s)
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException for invalid header names or values
     */
    public function withHeader($name, $value)
    {
        return static::getResponseInstance()->withHeader($name, $value);
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * @param string          $name  case-insensitive header field name
     * @param string|string[] $value header value(s)
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException for invalid header names or values
     */
    public function setHeader(string $name, $value): IResponse
    {
        return static::getResponseInstance()->setHeader($name, $value);
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string          $name  case-insensitive header field name to add
     * @param string|string[] $value header value(s)
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException for invalid header names or values
     */
    public function withAddedHeader($name, $value)
    {
        return static::getResponseInstance()->withAddedHeader($name, $value);
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * @param string          $name  case-insensitive header field name to add
     * @param string|string[] $value header value(s)
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException for invalid header names or values
     */
    public function addHeader(string $name, $value): IResponse
    {
        return static::getResponseInstance()->addHeader($name, $value);
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name case-insensitive header field name to remove
     *
     * @return IResponse
     */
    public function withoutHeader($name)
    {
        return static::getResponseInstance()->withoutHeader($name);
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * @param string $name case-insensitive header field name to remove
     *
     * @return IResponse
     */
    public function removeHeader(string $name): IResponse
    {
        return static::getResponseInstance()->removeHeader($name);
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface returns the body as a stream
     */
    public function getBody()
    {
        return static::getResponseInstance()->getBody();
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body body
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException when the body is not valid
     */
    public function withBody(StreamInterface $body)
    {
        return static::getResponseInstance()->withBody($body);
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * @param StreamInterface $body body
     *
     * @return IResponse
     *
     * @throws \InvalidArgumentException when the body is not valid
     */
    public function setBody(StreamInterface $body): IResponse
    {
        return static::getResponseInstance()->setBody($body);
    }

    /**
     * 获取实例对象
     *
     * @param \Imi\Server\Base      $server
     * @param \Swoole\Http\Response $response
     *
     * @return IResponse
     */
    public static function getInstance(\Imi\Server\Base $server, \Swoole\Http\Response $response)
    {
        return static::getResponseInstance()->getInstance($server, $response);
    }

    /**
     * 设置cookie.
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return IResponse
     */
    public function withCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): IResponse
    {
        return static::getResponseInstance()->withCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * 设置cookie.
     *
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     *
     * @return IResponse
     */
    public function setCookie(string $key, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): IResponse
    {
        return static::getResponseInstance()->setCookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return static::getResponseInstance()->getCookieParams();
    }

    /**
     * 获取cookie值
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getCookie(string $name, $default = null)
    {
        return static::getResponseInstance()->getCookie($name, $default);
    }

    /**
     * 获取 Trailer 列表.
     *
     * @return array
     */
    public function getTrailers(): array
    {
        return static::getResponseInstance()->getTrailers();
    }

    /**
     * Trailer 是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasTrailer(string $name): bool
    {
        return static::getResponseInstance()->hasTrailer($name);
    }

    /**
     * 获取 Trailer 值
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getTrailer(string $name): ?string
    {
        return static::getResponseInstance()->getTrailer($name);
    }

    /**
     * 设置 Trailer.
     *
     * @param string $name
     * @param string $value
     *
     * @return IResponse
     */
    public function withTrailer(string $name, string $value): IResponse
    {
        return static::getResponseInstance()->withTrailer($name, $value);
    }

    /**
     * 设置 Trailer.
     *
     * @param string $name
     * @param string $value
     *
     * @return IResponse
     */
    public function setTrailer(string $name, string $value): IResponse
    {
        return static::getResponseInstance()->setTrailer($name, $value);
    }

    /**
     * 设置服务器端重定向
     * 默认状态码为302.
     *
     * @param string $url
     * @param int    $status
     *
     * @return IResponse
     */
    public function redirect(string $url, int $status = StatusCode::FOUND): IResponse
    {
        return static::getResponseInstance()->redirect($url, $status);
    }

    /**
     * 发送文件，一般用于文件下载.
     *
     * @param string $filename 要发送的文件名称，文件不存在或没有访问权限sendfile会失败
     * @param int    $offset   上传文件的偏移量，可以指定从文件的中间部分开始传输数据。此特性可用于支持断点续传。
     * @param int    $length   发送数据的尺寸，默认为整个文件的尺寸
     *
     * @return IResponse
     */
    public function sendFile(string $filename, int $offset = 0, int $length = 0): IResponse
    {
        return static::getResponseInstance()->sendFile($filename, $offset, $length);
    }

    /**
     * 获取发送文件参数.
     *
     * @return array
     */
    public function getSendFile(): array
    {
        return static::getResponseInstance()->getSendFile();
    }

    /**
     * 获取当前上下文中的对象实例.
     *
     * @return IResponse
     */
    public static function getResponseInstance(): Response
    {
        return RequestContext::get('response');
    }

    /**
     * 设置当前上下文中的对象实例.
     *
     * @param IResponse $response
     *
     * @return void
     */
    public function setResponseInstance(Response $response)
    {
        RequestContext::set('response', $response);
    }
}
