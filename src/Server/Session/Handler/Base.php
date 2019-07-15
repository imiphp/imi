<?php
namespace Imi\Server\Session\Handler;

use Imi\RequestContext;
use Imi\Util\AtomicManager;
use Imi\Util\Format\PhpSerialize;

abstract class Base implements ISessionHandler
{
    /**
     * 数据格式化处理类
     * @var string
     */
    protected $formatHandlerClass = PhpSerialize::class;

    /**
     * 生成SessionID
     * @return string
     */
    public function createSessionID()
    {
        // 时间+自增，md5 id+时间各自的一半，重复概率很小了吧
        $id = AtomicManager::add('session');
        $time = microtime();
        return substr(md5($id), 0, 16) . substr(md5($time), 0, 16);
    }

    /**
     * 编码为存储格式
     * @param array $data
     * @return mixed
     */
    public function encode(array $data)
    {
        return RequestContext::getServerBean($this->formatHandlerClass)->encode($data);
    }

    /**
     * 解码为php数组
     * @param mixed $data
     * @return array
     */
    public function decode($data): array
    {
        $result = RequestContext::getServerBean($this->formatHandlerClass)->decode($data);
        if(!is_array($result))
        {
            $result = [];
        }
        return $result;
    }
}