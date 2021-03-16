<?php

declare(strict_types=1);

namespace Imi\Server\Session\Handler;

/**
 * Session处理器接口.
 */
interface ISessionHandler
{
    /**
     * 销毁session数据.
     */
    public function destroy(string $sessionId): void;

    /**
     * 垃圾回收.
     *
     * @param int $maxLifeTime 最大存活时间，单位：秒
     */
    public function gc(int $maxLifeTime): void;

    /**
     * 读取session.
     *
     * @return mixed
     */
    public function read(string $sessionId);

    /**
     * 写入session.
     */
    public function write(string $sessionId, string $sessionData, int $maxLifeTime): void;

    /**
     * 生成SessionId.
     */
    public function createSessionId(): string;

    /**
     * 编码为存储格式.
     */
    public function encode(array $data): string;

    /**
     * 解码为php数组.
     */
    public function decode(string $data): array;
}
