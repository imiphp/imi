<?php
namespace Imi\Server\Session\Handler;

/**
 * Session处理器接口
 */
interface ISessionHandler extends \SessionHandlerInterface
{
    /**
     * 销毁session数据
     * @param string $sessionID
     * @return void
     */
    public function destroy($sessionID);

    /**
     * 垃圾回收
     * @param int $maxLifeTime 最大存活时间，单位：秒
     * @return void
     */
    public function gc($maxLifeTime);

    /**
     * 读取session
     * @param string $sessionID
     * @return mixed
     */
    public function read($sessionID);

    /**
     * 写入session
     * @param string $sessionID
     * @param string $sessionData
     * @return void
     */
    public function write($sessionID, $sessionData);

    /**
     * 生成SessionID
     * @return string
     */
    public function createSessionID();

    /**
     * 编码为存储格式
     * @param array $data
     * @return mixed
     */
    public function encode(array $data);

    /**
     * 解码为php数组
     * @param mixed $data
     * @return array
     */
    public function decode($data): array;

    /**
     * 设置gc时间
     * @param $maxLifeTime
     *
     * @return bool
     */
    public function setMaxLifeTime($maxLifeTime):bool;

    /**
     * 获取gc时间
     * @return int
     */
    public function getMaxLifeTime():int;
}