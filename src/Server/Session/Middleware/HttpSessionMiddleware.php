<?php
namespace Imi\Server\Session\Middleware;

use Imi\Bean\Annotation\Bean;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Imi\RequestContext;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;

/**
 * @Bean("HttpSessionMiddleware")
 */
class HttpSessionMiddleware implements MiddlewareInterface
{
    /**
     * SessionID处理器
     *
     * @var callable
     */
    protected $sessionIdHandler = null;

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Imi\Server\Session\SessionManager $sessionManager */
        $sessionManager = RequestContext::getBean('SessionManager');

        $sessionID = '';
        $sessionIdHandler = $this->sessionIdHandler;
        if(null !== $sessionIdHandler && is_callable($sessionIdHandler)) {
            $sessionID = ($sessionIdHandler)($request);
        }
        $sessionID = $sessionID ?: $request->getCookie($sessionManager->getName());

        // 开启session
        $this->start($sessionManager, $sessionID);

        try{
            // 执行其它中间件
            $response = $handler->handle($request);

            if($sessionManager->getConfig()->cookie->enable && $sessionManager->isNewSession() && $sessionManager->isChanged())
            {
                // 发送cookie
                $response = $this->sendCookie($sessionManager, $response);
            }
        } finally{
            // 尝试进行垃圾回收
            $sessionManager->tryGC();
            // 保存关闭session
            $sessionManager->commit();
        }

        return $response;
    }

    /**
     * 开启session
     * @param \Imi\Server\Session\SessionManager $sessionManager
     * @param string $sessionID
     * @return void
     */
    private function start($sessionManager, $sessionID)
    {
        $sessionManager->start($sessionID);
    }

    /**
     * 发送cookie
     * @param \Imi\Server\Session\SessionManager $sessionManager
     * @param Response $response
     * @return ResponseInterface
     */
    private function sendCookie($sessionManager, Response $response): ResponseInterface
    {
        $config = $sessionManager->getConfig();
        $cookie = $config->cookie;
        return $response->withCookie($sessionManager->getName(), $sessionManager->getID(), 0 === $cookie->lifetime ? 0 : (time() + $cookie->lifetime), $cookie->path, $cookie->domain, $cookie->secure, $cookie->httponly);
    }
}
