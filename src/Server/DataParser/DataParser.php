<?php

declare(strict_types=1);

namespace Imi\Server\DataParser;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\Contract\IServer;
use Imi\Server\Protocol;
use Imi\Server\ServerManager;

/**
 * 数据处理器.
 */
class DataParser
{
    /**
     * 编码为存储格式.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function encode($data, ?string $serverName = null)
    {
        if ($serverName)
        {
            return ServerManager::getServer($serverName)->getBean($this->getParserClass($serverName))->encode($data);
        }
        else
        {
            return RequestContext::getServerBean($this->getParserClass($serverName))->encode($data);
        }
    }

    /**
     * 解码为php变量.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function decode($data, ?string $serverName = null)
    {
        if ($serverName)
        {
            return ServerManager::getServer($serverName)->getBean($this->getParserClass($serverName))->decode($data);
        }
        else
        {
            return RequestContext::getServerBean($this->getParserClass($serverName))->decode($data);
        }
    }

    /**
     * 获取处理器类.
     */
    public function getParserClass(?string $serverName = null): string
    {
        $requestContext = RequestContext::getContext();
        if ($serverName)
        {
            $server = ServerManager::getServer($serverName);
        }
        else
        {
            $server = $requestContext['server'] ?? null;
        }
        /** @var IServer $server */
        switch ($server->getProtocol())
        {
            case Protocol::WEBSOCKET:
                if (!($requestContext['clientId'] ?? null))
                {
                    return JsonObjectParser::class;
                }

                return ConnectContext::get('httpRouteResult')->routeItem->wsConfig->parserClass ?? JsonObjectParser::class;
            case Protocol::TCP:
            case Protocol::UDP:
                return $server->getConfig()['dataParser'] ?? JsonObjectParser::class;
            default:
                return JsonObjectParser::class;
        }
    }
}
