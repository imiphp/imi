<?php
namespace Imi\Server\DataParser;

use Imi\RequestContext;
use Imi\ConnectContext;

/**
 * 数据处理器
 */
class DataParser
{
    /**
     * 编码为存储格式
     * @param mixed $data
     * @return mixed
     */
    public function encode($data)
    {
        return RequestContext::getServerBean($this->getParserClass())->encode($data);
    }

    /**
     * 解码为php变量
     * @param mixed $data
     * @return mixed
     */
    public function decode($data)
    {
        return RequestContext::getServerBean($this->getParserClass())->decode($data);
    }

    /**
     * 获取处理器类
     *
     * @return string
     */
    public function getParserClass()
    {
        $server = RequestContext::getServer();
        if($server instanceof \Imi\Server\WebSocket\Server)
        {
            return ConnectContext::get('httpRouteResult')['wsConfig']->parserClass ?? JsonObjectParser::class;
        }
        else if($server instanceof \Imi\Server\TcpServer\Server || $server instanceof \Imi\Server\UdpServer\Server)
        {
            return $server->getConfig()['dataParser'] ?? JsonObjectParser::class;
        }
        else
        {
            return JsonObjectParser::class;
        }
    }

}