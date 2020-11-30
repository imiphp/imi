<?php

declare(strict_types=1);

namespace Imi\Server\Session\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Session\SessionManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("HttpSessionMiddleware")
 */
class HttpSessionMiddleware implements MiddlewareInterface
{
    /**
     * SessionId处理器.
     *
     * @var callable|null
     */
    protected $sessionIdHandler = null;

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param IHttpRequest            $request
     * @param RequestHandlerInterface $handler
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Imi\Server\Session\SessionManager $sessionManager */
        $sessionManager = RequestContext::getBean('SessionManager');

        $sessionId = '';
        $sessionIdHandler = $this->sessionIdHandler;
        if (null !== $sessionIdHandler && \is_callable($sessionIdHandler))
        {
            $sessionId = ($sessionIdHandler)($request);
        }
        $sessionId = $sessionId ?: $request->getCookie($sessionManager->getName());

        // 开启session
        $this->start($sessionManager, $sessionId);

        try
        {
            // 执行其它中间件
            $response = $handler->handle($request);

            if ($sessionManager->getConfig()->cookie->enable && $sessionManager->isNewSession() && $sessionManager->isChanged())
            {
                // 发送cookie
                $response = $this->sendCookie($sessionManager, $response);
            }
        }
        finally
        {
            // 尝试进行垃圾回收
            $sessionManager->tryGC();
            // 保存关闭session
            $sessionManager->commit();
        }

        return $response;
    }

    /**
     * 开启session.
     *
     * @param \Imi\Server\Session\SessionManager $sessionManager
     * @param string|null                        $sessionId
     *
     * @return void
     */
    private function start(SessionManager $sessionManager, ?string $sessionId)
    {
        $sessionManager->start($sessionId);
    }

    /**
     * 发送cookie.
     *
     * @param \Imi\Server\Session\SessionManager $sessionManager
     * @param Response                           $response
     *
     * @return IHttpResponse
     */
    private function sendCookie(SessionManager $sessionManager, Response $response): IHttpResponse
    {
        $config = $sessionManager->getConfig();
        $cookie = $config->cookie;

        return $response->setCookie($sessionManager->getName(), $sessionManager->getId(), 0 === $cookie->lifetime ? 0 : (time() + $cookie->lifetime), $cookie->path, $cookie->domain, $cookie->secure, $cookie->httponly);
    }
}
