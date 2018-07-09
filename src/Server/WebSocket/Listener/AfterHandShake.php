<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\HandShakeEventParam;
use Imi\Server\Event\Listener\IHandShakeEventListener;
use Imi\Util\Http\Consts\StatusCode;

/**
 * HandShake事件后置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="handShake",priority=PHP_INT_MIN)
 */
class AfterHandShake implements IHandShakeEventListener
{
	/**
	 * 默认的 WebSocket 握手
	 * @param RequestEventParam $e
	 * @return void
	 */
	public function handle(HandShakeEventParam $e)
	{
		$response = &$e->response;
		if($response->isEnded())
		{
			// 已经处理过事件的不再做处理
			$this->parseAfter($e);
			return;
		}
		$request = $e->request;
		$secWebSocketKey = $request->getHeaderLine('sec-websocket-key');
		static $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
		if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey)))
		{
			$response->send();
			return;
		}

		$key = base64_encode(sha1($secWebSocketKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

		$headers = [
			'Upgrade' => 'websocket',
			'Connection' => 'Upgrade',
			'Sec-WebSocket-Accept' => $key,
			'Sec-WebSocket-Version' => '13',
		];

		if($request->hasHeader('Sec-WebSocket-Protocol'))
		{
			$headers['Sec-WebSocket-Protocol'] = $request->getHeaderLine('Sec-WebSocket-Protocol');
		}

		foreach ($headers as $key => $val)
		{
			$response = $response->withHeader($key, $val);
		}

		// $response->status(101);
		// $response->end();
		$response = $response->withStatus(StatusCode::SWITCHING_PROTOCOLS);
		$response->send();

		$this->parseAfter($e);
	}

	private function parseAfter(HandShakeEventParam $e)
	{
		
	}
}