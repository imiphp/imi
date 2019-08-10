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
 * @Bean
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
        $sessionManager = RequestContext::getBean('SessionManager');

        $sessionID = '';
        if(null !== $this->sessionIdHandler && is_callable($this->sessionIdHandler)) {
            $sessionID = ($this->sessionIdHandler)($request);
        }
        $sessionID = $sessionID ?: $request->getCookie($sessionManager->getName());

        // 开启session
        $this->start($sessionManager, $sessionID);

        try{
            // 执行其它中间件
            $response = $handler->handle($request);

            if($sessionManager->isNewSession() && $sessionManager->isChanged())
            {
                // 发送cookie
                $response = $this->sendCookie($sessionManager, $response);
            }
        } catch(\Throwable $ex){
            throw $ex;
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
        return $response->withCookie($sessionManager->getName(), $sessionManager->getID(), time() + $config->cookie->lifetime, $config->cookie->path, $config->cookie->domain, $config->cookie->secure);
    }
}
