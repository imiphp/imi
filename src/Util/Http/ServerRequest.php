<?php

declare(strict_types=1);

namespace Imi\Util\Http;

use Imi\Config;
use Imi\Server\Http\Message\UploadedFile;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\Util\Http\Contract\IServerRequest;

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
    public function getServerParam(string $name, $default = null)
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $self = clone $this;

        // @phpstan-ignore-next-line
        return $self->setUploadedFiles($uploadedFiles);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function withParsedBody($data)
    {
        $self = clone $this;
        $self->parsedBody = $data;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setParsedBody($data): self
    {
        $this->parsedBody = $data;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function withAttribute($name, $value)
    {
        $self = clone $this;
        $self->attributes[$name] = $value;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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

        return isset($this->post[$name]);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
