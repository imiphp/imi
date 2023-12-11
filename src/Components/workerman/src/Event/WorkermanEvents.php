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
    public const BEFORE_START_SERVER = 'imi.workerman.server.before_start';

    /**
     * Workerman 事件：onBufferDrain.
     */
    public const SERVER_BUFFER_DRAIN = 'imi.workerman.server.buffer_drain';

    /**
     * Workerman 事件：onBufferFull.
     */
    public const SERVER_BUFFER_FULL = 'imi.workerman.server.buffer_full';

    /**
     * Workerman 事件：onClose.
     */
    public const SERVER_CLOSE = 'imi.workerman.server.close';

    /**
     * Workerman 事件：onConnect.
     */
    public const SERVER_CONNECT = 'imi.workerman.server.connect';

    /**
     * Workerman 事件：onError.
     */
    public const SERVER_ERROR = 'imi.workerman.server.error';

    /**
     * Workerman 事件：onWorkerReload.
     */
    public const SERVER_WORKER_RELOAD = 'imi.workerman.server.worker_reload';

    /**
     * Workerman 事件：onWorkerStart.
     */
    public const SERVER_WORKER_START = 'imi.workerman.server.worker_start';

    /**
     * Workerman 事件：onWorkerStop.
     */
    public const SERVER_WORKER_STOP = 'imi.workerman.server.worker_stop';

    /**
     * Workerman http onMessage.
     */
    public const SERVER_HTTP_REQUEST = 'imi.workerman.server.http.request';

    /**
     * Workerman websocket onWebSocketConnect.
     */
    public const SERVER_WEBSOCKET_CONNECT = 'imi.workerman.server.websocket.connect';

    /**
     * Workerman websocket onMessage.
     */
    public const SERVER_WEBSOCKET_MESSAGE = 'imi.workerman.server.websocket.message';

    /**
     * Workerman tcp onMessage.
     */
    public const SERVER_TCP_MESSAGE = 'imi.workerman.server.tcp.message';

    /**
     * Workerman udp onMessage.
     */
    public const SERVER_UDP_MESSAGE = 'imi.workerman.server.udp.message';
}
