<?php

declare(strict_types=1);

namespace Imi\Util\Http\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface IServerRequest extends ServerRequestInterface, IRequest
{
    /**
     * 获取服务器参数.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getServerParam(string $name, $default = null);

    /**
     * 获取cookie值
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCookie(string $name, $default = null);

    /**
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * @param array $cookies array of key/value pairs representing cookies
     *
     * @return static
     */
    public function setCookieParams(array $cookies): self;

    /**
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * @param array $query array of query string arguments, typically from
     *                     $_GET
     *
     * @return static
     */
    public function setQueryParams(array $query): self;

    /**
     * Create a new instance with the specified uploaded files.
     *
     * @param array $uploadedFiles an array tree of UploadedFileInterface instances
     *
     * @return static
     *
     * @throws \InvalidArgumentException if an invalid structure is provided
     */
    public function setUploadedFiles(array $uploadedFiles): self;

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * @param array|object|null $data The deserialized body data. This will
     *                                typically be in an array or object.
     *
     * @return static
     *
     * @throws \InvalidArgumentException if an unsupported argument type is
     *                                   provided
     */
    public function setParsedBody($data): self;

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * @see getAttributes()
     *
     * @param string $name  the attribute name
     * @param mixed  $value the value of the attribute
     *
     * @return static
     */
    public function setAttribute(string $name, $value): self;

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * @see getAttributes()
     *
     * @param string $name the attribute name
     *
     * @return static
     */
    public function removeAttribute(string $name): self;

    /**
     * 获取 GET 参数
     * 当 $name 为 null 时，返回所有.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(?string $name = null, $default = null);

    /**
     * 获取 POST 参数
     * 当 $name 为 null 时，返回所有.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function post(?string $name = null, $default = null);

    /**
     * 判断是否存在 GET 参数.
     */
    public function hasGet(string $name): bool;

    /**
     * 判断是否存在 POST 参数.
     */
    public function hasPost(string $name): bool;

    /**
     * 获取 REQUEST 参数
     * 当 $name 为 null 时，返回所有
     * REQUEST 中包括：GET/POST/COOKIE.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function request(?string $name = null, $default = null);

    /**
     * 判断是否存在 REQUEST 参数
     * REQUEST 中包括：GET/POST/COOKIE.
     */
    public function hasRequest(string $name): bool;

    /**
     * 设置 GET 数据.
     *
     * @return static
     */
    public function withGet(array $get): self;

    /**
     * 设置 GET 数据.
     *
     * @return static
     */
    public function setGet(array $get): self;

    /**
     * 设置 POST 数据.
     *
     * @return static
     */
    public function withPost(array $post): self;

    /**
     * 设置 POST 数据.
     *
     * @return static
     */
    public function setPost(array $post): self;

    /**
     * 设置 Request 数据.
     *
     * @return static
     */
    public function withRequest(array $request): self;

    /**
     * 设置 Request 数据.
     *
     * @return static
     */
    public function setRequest(array $request): self;
}
