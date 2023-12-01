<?php

declare(strict_types=1);

namespace Imi\Workerman\Event;

use Imi\Util\Traits\TStaticClass;

final class WorkermanEvents
{
    use TStaticClass;

    /**
     * 启动服务器前置事件.
     */
    public const BEFORE_START_SERVER = 'IMI.WORKERMAN.SERVER.BEFORE_START';

    /**
     * Workerman 事件：onBufferDrain.
     */
    public const SERVER_BUFFER_DRAIN = 'IMI.WORKERMAN.SERVER.BUFFER_DRAIN';

    /**
     * Workerman 事件：onBufferFull.
     */
    public const SERVER_BUFFER_FULL = 'IMI.WORKERMAN.SERVER.BUFFER_FULL';

    /**
     * Workerman 事件：onClose.
     */
    public const SERVER_CLOSE = 'IMI.WORKERMAN.SERVER.CLOSE';

    /**
     * Workerman 事件：onConnect.
     */
    public const SERVER_CONNECT = 'IMI.WORKERMAN.SERVER.CONNECT';

    /**
     * Workerman 事件：onError.
     */
    public const SERVER_ERROR = 'IMI.WORKERMAN.SERVER.ERROR';

    /**
     * Workerman 事件：onWorkerReload.
     */
    public const SERVER_WORKER_RELOAD = 'IMI.WORKERMAN.SERVER.WORKER_RELOAD';

    /**
     * Workerman 事件：onWorkerStart.
     */
    public const SERVER_WORKER_START = 'IMI.WORKERMAN.SERVER.WORKER_START';

    /**
     * Workerman 事件：onWorkerStop.
     */
    public const SERVER_WORKER_STOP = 'IMI.WORKERMAN.SERVER.WORKER_STOP';

    /**
     * Workerman http onMessage.
     */
    public const SERVER_HTTP_REQUEST = 'IMI.WORKERMAN.SERVER.HTTP.REQUEST';

    /**
     * Workerman websocket onWebSocketConnect.
     */
    public const SERVER_WEBSOCKET_CONNECT = 'IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT';

    /**
     * Workerman websocket onMessage.
     */
    public const SERVER_WEBSOCKET_MESSAGE = 'IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE';

    /**
     * Workerman tcp onMessage.
     */
    public const SERVER_TCP_MESSAGE = 'IMI.WORKERMAN.SERVER.TCP.MESSAGE';

    /**
     * Workerman udp onMessage.
     */
    public const SERVER_UDP_MESSAGE = 'IMI.WORKERMAN.SERVER.UDP.MESSAGE';
}
