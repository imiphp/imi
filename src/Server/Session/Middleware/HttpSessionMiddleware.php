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
	 * Session管理类对象
	 * @var \Imi\Server\Session\SessionManager
	 */
	private $sessionManager;

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$this->sessionManager = RequestContext::getBean('SessionManager');
		$sessionID = $request->getCookie($this->sessionManager->getName());

		// 开启session
		$this->start($request, $sessionID);

		try{
			// 执行其它中间件
			$response = $handler->handle($request);

			if(null === $sessionID)
			{
				// 发送cookie
				$response = $this->sendCookie($response);
			}
		} catch(\Throwable $ex){
			throw $ex;
		} finally{
			// 尝试进行垃圾回收
			$this->sessionManager->tryGC();
			// 保存关闭session
			$this->sessionManager->commit();
		}

		return $response;
	}

	/**
	 * 开启session
	 * @param Request $request
	 * @return void
	 */
	private function start(Request $request, $sessionID)
	{
		$this->sessionManager->start($sessionID);
	}

	/**
	 * 发送cookie
	 * @param Response $response
	 * @return ResponseInterface
	 */
	private function sendCookie(Response $response): ResponseInterface
	{
		$config = $this->sessionManager->getConfig();
		return $response->withCookie($this->sessionManager->getName(), $this->sessionManager->getID(), time() + $config->cookie->lifetime, $config->cookie->path, $config->cookie->domain, $config->cookie->secure);
	}
}