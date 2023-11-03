<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Config;
use Imi\Server\Http\Message\UploadedFile;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\Util\Http\Contract\IServerRequest;
use Imi\Util\ObjectArrayHelper;

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
     *
     * @var mixed
     */
    protected $post = [];

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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getServerParam(string $name, mixed $default = null): mixed
    {
        if (!$this->serverInited)
        {
            $this->initServer();
            $this->serverInited = true;
        }

        return $this->server[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    protected function initRequestParams(): void
    {
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getCookieParams(): array
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return $this->cookies;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withCookieParams(array $cookies): self
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
     * {@inheritDoc}
     */
    public function getCookie(string $name, ?string $default = null): ?string
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return $this->cookies[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getQueryParams(): array
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return $this->get;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withQueryParams(array $query): self
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
     * {@inheritDoc}
     */
    public function getUploadedFiles(): array
    {
        if (!$this->uploadedFilesInited)
        {
            $this->initUploadedFiles();
            $this->uploadedFilesInited = true;
        }

        return $this->files;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withUploadedFiles(array $uploadedFiles): self
    {
        $self = clone $this;

        // @phpstan-ignore-next-line
        return $self->setUploadedFiles($uploadedFiles);
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function setUploadedFiles(array $uploadedFiles): self
    {
        $objectFiles = &$this->files;
        $objectFiles = [];
        if ($uploadedFiles)
        {
            foreach ($uploadedFiles as $key => $file)
            {
                if (\is_array($file))
                {
                    $objectFiles[$key] = new UploadedFile($file['name'], $file['type'], $file['tmp_name'], $file['size'], $file['error']);
                }
                else
                {
                    $objectFiles[$key] = $file;
                }
            }
        }
        $this->uploadedFilesInited = true;

        return $this;
    }

    /**
     * {@inheritDoc}
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
            $contentType = $this->getHeaderLine(RequestHeader::CONTENT_TYPE);
            if ('' === $contentType)
            {
                $parsedBody = null;
                $this->post = [];
            }
            else
            {
                $contentType = strtolower(trim(explode(';', $contentType, 2)[0]));
                // post
                if (\in_array($contentType, [
                    MediaType::APPLICATION_FORM_URLENCODED,
                    MediaType::MULTIPART_FORM_DATA,
                ]))
                {
                    $this->post = $parsedBody = $this->post();
                }
                // json
                elseif (MediaType::APPLICATION_JSON === $contentType)
                {
                    $content = $this->body->getContents();
                    if ('' !== $content)
                    {
                        $parsedBody = json_decode($content, !Config::get('@currentServer.jsonBodyIsObject', false), 512, \JSON_THROW_ON_ERROR);
                        if ($parsedBody)
                        {
                            $this->post = $parsedBody;
                        }
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
                    $this->post = $parsedBody = new \DOMDocument();
                    $parsedBody->loadXML($this->body->getContents());
                }
                // 其它
                else
                {
                    $parsedBody = null;
                    $this->post = [];
                }
            }
        }

        return $parsedBody;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withParsedBody($data): self
    {
        $self = clone $this;
        $self->parsedBody = $data;

        return $self;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function setParsedBody($data): self
    {
        $this->parsedBody = $data;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute(string $name, mixed $default = null): mixed
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
     * {@inheritDoc}
     *
     * @return static
     */
    public function withAttribute(string $name, $value): self
    {
        $self = clone $this;
        $self->attributes[$name] = $value;

        return $self;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withoutAttribute(string $name): self
    {
        $self = clone $this;
        if (\array_key_exists($name, $self->attributes))
        {
            unset($self->attributes[$name]);
        }

        return $self;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function get(?string $name = null, mixed $default = null): mixed
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
     * {@inheritDoc}
     */
    public function post(?string $name = null, mixed $default = null): mixed
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
            return ObjectArrayHelper::get($this->post, $name, $default);
        }
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function hasPost(string $name): bool
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return ObjectArrayHelper::exists($this->post, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function request(?string $name = null, mixed $default = null): mixed
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }
        $request = &$this->request;
        if (null === $request)
        {
            $request = array_merge($this->get, \is_array($this->post) ? $this->post : ObjectArrayHelper::toArray($this->post), $this->cookies);
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
     * {@inheritDoc}
     */
    public function hasRequest(string $name): bool
    {
        if (!$this->requestParamsInited)
        {
            $this->initRequestParams();
            $this->requestParamsInited = true;
        }

        return isset($this->request()[$name]);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     *
     * @return static
     */
    public function withPost(mixed $post): self
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
     * {@inheritDoc}
     *
     * @return static
     */
    public function setPost(mixed $post): self
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
