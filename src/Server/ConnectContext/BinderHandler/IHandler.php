<?php

namespace Imi\Server\ConnectContext\BinderHandler;

/**
 * 连接绑定器存储器.
 */
interface IHandler
{
    /**
     * 绑定一个标记到当前连接.
     *
     * @param string $flag
     * @param int    $fd
     *
     * @return void
     */
    public function bind(string $flag, int $fd);

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param string $flag
     * @param int    $fd
     *
     * @return bool
     */
    public function bindNx(string $flag, int $fd): bool;

    /**
     * 取消绑定.
     *
     * @param string   $flag
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     *
     * @return void
     */
    public function unbind(string $flag, ?int $keepTime = null);

    /**
     * 使用标记获取连接编号.
     *
     * @param string $flag
     *
     * @return int|null
     */
    public function getFdByFlag(string $flag): ?int;

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]
     */
    public function getFdsByFlags(array $flags): array;

    /**
     * 使用连接编号获取标记.
     *
     * @param int $fd
     *
     * @return string|null
     */
    public function getFlagByFd(int $fd): ?string;

    /**
     * 使用连接编号获取标记.
     *
     * @param int[] $fds
     *
     * @return string[]
     */
    public function getFlagsByFds(array $fds): array;

    /**
     * 使用标记获取旧的连接编号.
     *
     * @param string $flag
     *
     * @return int|null
     */
    public function getOldFdByFlag(string $flag): ?int;
}
