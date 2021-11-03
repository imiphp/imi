<?php

declare(strict_types=1);

namespace Imi\Server\Session\Handler;

use Imi\RequestContext;
use Imi\Util\Format\IFormat;
use Imi\Util\Format\PhpSerialize;

abstract class Base implements ISessionHandler
{
    /**
     * 数据格式化处理类.
     */
    protected string $formatHandlerClass = PhpSerialize::class;

    /**
     * 数据格式化处理器对象
     */
    private IFormat $formatHandler;

    /**
     * 进程ID.
     */
    private int $pid;

    /**
     * 自增值
     */
    private int $atomic = 0;

    public function __init(): void
    {
        $this->pid = getmypid();
        $this->formatHandler = RequestContext::getServerBean($this->formatHandlerClass);
    }

    /**
     * {@inheritDoc}
     */
    public function createSessionId(): string
    {
        return md5($this->pid . '/' . ++$this->atomic . microtime(true));
    }

    /**
     * {@inheritDoc}
     */
    public function encode(array $data): string
    {
        return $this->formatHandler->encode($data);
    }

    /**
     * {@inheritDoc}
     */
    public function decode(string $data): array
    {
        if ('' === $data)
        {
            return [];
        }
        $result = $this->formatHandler->decode($data);
        if (!\is_array($result))
        {
            $result = [];
        }

        return $result;
    }
}
