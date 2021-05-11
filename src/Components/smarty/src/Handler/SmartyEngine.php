<?php

namespace Imi\Smarty\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Http\Message\Response;
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
     *
     * @var array
     */
    private static $instances = [];

    /**
     * 缓存目录.
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * 编译目录.
     *
     * @var string
     */
    protected $compileDir;

    /**
     * 是否开启缓存.
     *
     * \Smarty::CACHING_OFF
     * \Smarty::CACHING_LIFETIME_CURRENT
     * \Smarty::CACHING_LIFETIME_SAVED
     *
     * @var int
     */
    protected $caching;

    /**
     * 缓存有效时间.
     *
     * @var int
     */
    protected $cacheLifetime;

    public function render(Response $response, $fileName, $data = []): Response
    {
        $smarty = $this->newSmartyInstance($response->getServerInstance()->getName());
        $smarty->assign($data);
        if (!is_file($fileName))
        {
            return $response;
        }
        $content = $smarty->fetch($fileName, 'abc');

        return $response->write($content);
    }

    /**
     * 获取 Smarty 实例.
     *
     * @param string|null $serverName
     *
     * @return \Smarty
     */
    public function newSmartyInstance($serverName = null)
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
