<?php

declare(strict_types=1);

namespace Imi\Smarty\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Engine\IEngine;
use Imi\Util\Imi;

/**
 * @Bean("SmartyEngine")
 * Smarty 模版引擎
 */
class SmartyEngine implements IEngine
{
    /**
     * Smarty 实例列表.
     */
    private static array $instances = [];

    /**
     * 缓存目录.
     */
    protected ?string $cacheDir = null;

    /**
     * 编译目录.
     */
    protected ?string $compileDir = null;

    /**
     * 是否开启缓存.
     *
     * \Smarty::CACHING_OFF
     * \Smarty::CACHING_LIFETIME_CURRENT
     * \Smarty::CACHING_LIFETIME_SAVED
     */
    protected int $caching = 0;

    /**
     * 缓存有效时间.
     */
    protected int $cacheLifetime = 0;

    /**
     * @param mixed $data
     */
    public function render(IHttpResponse $response, string $fileName, $data = []): IHttpResponse
    {
        $smarty = $this->newSmartyInstance();
        $smarty->assign($data);
        if (!is_file($fileName))
        {
            return $response;
        }
        $content = $smarty->fetch($fileName, 'abc');
        $response->getBody()->write($content);

        return $response;
    }

    /**
     * 获取 Smarty 实例.
     */
    public function newSmartyInstance(?string $serverName = null): \Smarty
    {
        if (null === $serverName)
        {
            $server = RequestContext::getServer();
            if ($server)
            {
                $serverName = $server->getName();
            }
            else
            {
                throw new \RuntimeException('Not found current server');
            }
        }
        if (!isset(self::$instances[$serverName]))
        {
            $smarty = new \Smarty();
            $smarty->setCacheDir($this->cacheDir ?? Imi::getRuntimePath('smarty/cache'));
            $smarty->setCompileDir($this->compileDir ?? Imi::getRuntimePath('smarty/compile'));
            if (\Smarty::CACHING_OFF !== $this->caching)
            {
                $smarty->setCaching($this->caching);
                $smarty->setCacheLifetime($this->cacheLifetime);
            }
            Event::trigger('IMI.SMARTY.NEW', [
                'smarty'        => $smarty,
                'serverName'    => $serverName,
            ]);
            self::$instances[$serverName] = $smarty;
        }

        return clone self::$instances[$serverName];
    }
}
