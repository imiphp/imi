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
     *
     * @param string $sessionId
     *
     * @return void
     */
    public function destroy(string $sessionId);

    /**
     * 垃圾回收.
     *
     * @param int $maxLifeTime 最大存活时间，单位：秒
     *
     * @return void
     */
    public function gc(int $maxLifeTime);

    /**
     * 读取session.
     *
     * @param string $sessionId
     *
     * @return mixed
     */
    public function read(string $sessionId);

    /**
     * 写入session.
     *
     * @param string $sessionId
     * @param string $sessionData
     * @param int    $maxLifeTime
     *
     * @return void
     */
    public function write(string $sessionId, string $sessionData, int $maxLifeTime);

    /**
     * 生成SessionId.
     *
     * @return string
     */
    public function createSessionId(): string;

    /**
     * 编码为存储格式.
     *
     * @param array $data
     *
     * @return string
     */
    public function encode(array $data): string;

    /**
     * 解码为php数组.
     *
     * @param string $data
     *
     * @return array
     */
    public function decode(string $data): array;
}
