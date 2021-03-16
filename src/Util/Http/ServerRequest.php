<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Config;
use Imi\Server\Http\Message\UploadedFile;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\Util\Http\Contract\IServerRequest;
use Imi\Util\Uri;

class ServerRequest extends \Imi\Util\Http\Request implements IServerRequest
{
    /**
     * 服务器信息.
     */
    protected array $server = [];

    /**
     * cookie数据.
     */
    protected array $cookies = [];

    /**
     * get数据.
     */
    protected array $get = [];

    /**
     * post数据.
     */
    protected array $post = [];

    /**
     * 包含 GET/POST/Cookie 数据.
     */
    protected ?array $request = null;

    /**
     * 上传的文件.
     *
     * @var \Yurun\Util\YurunHttp\Http\Psr7\UploadedFile[]
     */
    protected array $files = [];

    /**
     * 处理过的主体内容.
     *
     * @var array|object|null
     */
    protected $parsedBody = null;

    /**
     * 属性数组.
     */
    protected array $attributes = [];

    /**
     * server 是否初始化.
     */
    protected bool $serverInited = false;

    /**
     * 请求参数是否初始化.
     */
    protected bool $requestParamsInited = false;

    /**
     * 上传文件是否初始化.
     */
    protected bool $uploadedFilesInited = false;

    /**
     * 初始化 server.
     */
    protected function initServer(): void
    {
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     */
    public function getServerParams(): array
    {
        if (!$this->serverInited)
        {
            $this->initServer();
            $this->serverInited = true;
        }

        return $this->server;
    }

    /**
     * 获取server参数.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return string
     */
    public function getServerParam($name, $default = null)
    {
        if (!$this->serverInited)
        {
            $this->initServer();
            $this->serverInited = true;
        }

        return $this->server[$name] ?? $default;
    }

    /**
     * 初始化请求参数.
     */
    protected function initRequestParams(): void
    {
    }

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
    public function setCookieParams(array $cookies): self
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $this->cookies = $cookies;

        return $this;
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
    public function getCookieParams()
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return $this->cookies;
    }

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
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies array of key/value pairs representing cookies
     *
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $self = clone $this;
        if (!$self->requestParamsInited)
        {
            $self->initRequestParams();
            $self->requestParamsInited = true;
        }
        $self->cookies = $cookies;

        return $self;
    }

    /**
     * 获取cookie值
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCookie(string $name, $default = null)
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return $this->cookies[$name] ?? $default;
    }

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
    public function setQueryParams(array $query): self
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $this->get = $query;

        return $this;
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return $this->get;
    }

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
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query array of query string arguments, typically from
     *                     $_GET
     *
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $self = clone $this;
        if (!$self->requestParamsInited)
        {
            $self->initRequestParams();
            $self->requestParamsInited = true;
        }
        $self->get = $query;

        return $self;
    }

    /**
     * 初始化上传文件.
     */
    protected function initUploadedFiles(): void
    {
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return \Psr\Http\Message\UploadedFileInterface[] an array tree of UploadedFileInterface instances; an empty
     *                                                   array MUST be returned if no data is present
     */
    public function getUploadedFiles()
    {
        if (!$this->uploadedFilesInited)
        {
            $this->initUploadedFiles();
            $this->uploadedFilesInited = true;
        }

        return $this->files;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles an array tree of UploadedFileInterface instances
     *
     * @return static
     *
     * @throws \InvalidArgumentException if an invalid structure is provided
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $self = clone $this;

        return $self->setUploadedFiles($uploadedFiles);
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * @param array $uploadedFiles an array tree of UploadedFileInterface instances
     *
     * @return static
     *
     * @throws \InvalidArgumentException if an invalid structure is provided
     */
    public function setUploadedFiles(array $uploadedFiles): self
    {
        $objectFiles = &$this->files;
        $objectFiles = [];
        foreach ($uploadedFiles as $key => $file)
        {
            $objectFiles[$key] = new UploadedFile($file['name'], $file['type'], $file['tmp_name'], $file['size'], $file['error']);
        }
        $this->uploadedFilesInited = true;

        return $this;
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return array|object|null The deserialized body parameters, if any.
     *                           These will typically be an array or object.
     */
    public function getParsedBody()
    {
        $parsedBody = &$this->parsedBody;
        if (null === $parsedBody)
        {
            if (!$this->bodyInited)
            {
                $this->initBody();
                $this->bodyInited = true;
            }
            $contentType = strtolower($this->getHeaderLine(RequestHeader::CONTENT_TYPE));
            // post
            if ('POST' === $this->getMethod() && \in_array($contentType, [
                MediaType::APPLICATION_FORM_URLENCODED,
                MediaType::MULTIPART_FORM_DATA,
            ]))
            {
                $parsedBody = $this->post();
            }
            // json
            elseif (\in_array($contentType, [
                MediaType::APPLICATION_JSON,
                MediaType::APPLICATION_JSON_UTF8,
            ]))
            {
                $content = $this->body->getContents();
                $this->post = $data = json_decode($content, true);
                if (Config::get('@currentServer.jsonBodyIsObject', false))
                {
                    $parsedBody = json_decode($content, false);
                }
                else
                {
                    $parsedBody = $data;
                }
            }
            // xml
            elseif (\in_array($contentType, [
                MediaType::TEXT_XML,
                MediaType::APPLICATION_ATOM_XML,
                MediaType::APPLICATION_RSS_XML,
                MediaType::APPLICATION_XHTML_XML,
                MediaType::APPLICATION_XML,
            ]))
            {
                $parsedBody = new \DOMDocument();
                $parsedBody->loadXML($this->body->getContents());
            }
            // 其它
            else
            {
                $parsedBody = null;
                $this->post = [];
            }
        }

        return $parsedBody;
    }

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
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array|object|null $data The deserialized body data. This will
     *                                typically be in an array or object.
     *
     * @return static
     *
     * @throws \InvalidArgumentException if an unsupported argument type is
     *                                   provided
     */
    public function withParsedBody($data)
    {
        $self = clone $this;
        $self->parsedBody = $data;

        return $self;
    }

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
    public function setParsedBody($data): self
    {
        $this->parsedBody = $data;

        return $this;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array attributes derived from the request
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     *
     * @param string $name    the attribute name
     * @param mixed  $default default value to return if the attribute does not exist
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        $attributes = $this->attributes;
        if (\array_key_exists($name, $attributes))
        {
            return $attributes[$name];
        }
        else
        {
            return $default;
        }
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     *
     * @param string $name  the attribute name
     * @param mixed  $value the value of the attribute
     *
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $self = clone $this;
        $self->attributes[$name] = $value;

        return $self;
    }

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
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     *
     * @param string $name the attribute name
     *
     * @return static
     */
    public function withoutAttribute($name)
    {
        $self = clone $this;
        if (\array_key_exists($name, $self->attributes))
        {
            unset($self->attributes[$name]);
        }

        return $self;
    }

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
    public function removeAttribute(string $name): self
    {
        if (\array_key_exists($name, $this->attributes))
        {
            unset($this->attributes[$name]);
        }

        return $this;
    }

    /**
     * 获取 GET 参数
     * 当 $name 为 null 时，返回所有.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(?string $name = null, $default = null)
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        if (null === $name)
        {
            return $this->get;
        }
        else
        {
            return $this->get[$name] ?? $default;
        }
    }

    /**
     * 获取 POST 参数
     * 当 $name 为 null 时，返回所有.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function post(?string $name = null, $default = null)
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        if (null === $name)
        {
            return $this->post;
        }
        else
        {
            return $this->post[$name] ?? $default;
        }
    }

    /**
     * 判断是否存在 GET 参数.
     */
    public function hasGet(string $name): bool
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return isset($this->get[$name]);
    }

    /**
     * 判断是否存在 POST 参数.
     */
    public function hasPost(string $name): bool
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return isset($this->post[$name]);
    }

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
    public function request(?string $name = null, $default = null)
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $request = &$this->request;
        if (null === $request)
        {
            $request = array_merge($this->get, $this->post, $this->cookies);
        }
        if (null === $name)
        {
            return $request;
        }
        else
        {
            return $request[$name] ?? $default;
        }
    }

    /**
     * 判断是否存在 REQUEST 参数
     * REQUEST 中包括：GET/POST/COOKIE.
     */
    public function hasRequest(string $name): bool
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $request = &$this->request;
        if (null === $request)
        {
            $request = array_merge($this->get, $this->post, $this->cookies);
        }

        return isset($request[$name]);
    }

    /**
     * 设置 GET 数据.
     *
     * @return static
     */
    public function withGet(array $get): self
    {
        $self = clone $this;
        if (!$self->requestParamsInited)
        {
            $self->initRequestParams();
            $self->requestParamsInited = true;
        }
        $self->get = $get;

        return $self;
    }

    /**
     * 设置 GET 数据.
     *
     * @return static
     */
    public function setGet(array $get): self
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $this->get = $get;

        return $this;
    }

    /**
     * 设置 POST 数据.
     *
     * @return static
     */
    public function withPost(array $post): self
    {
        $self = clone $this;
        if (!$self->requestParamsInited)
        {
            $self->initRequestParams();
            $self->requestParamsInited = true;
        }
        $self->post = $post;

        return $self;
    }

    /**
     * 设置 POST 数据.
     *
     * @return static
     */
    public function setPost(array $post): self
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $this->post = $post;

        return $this;
    }

    /**
     * 设置 Request 数据.
     *
     * @return static
     */
    public function withRequest(array $request): self
    {
        $self = clone $this;
        if (!$self->requestParamsInited)
        {
            $self->initRequestParams();
            $self->requestParamsInited = true;
        }
        $self->request = $request;

        return $self;
    }

    /**
     * 设置 Request 数据.
     *
     * @return static
     */
    public function setRequest(array $request): self
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $this->request = $request;

        return $this;
    }
}
