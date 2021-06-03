<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\HttpsTestServer\Controller;

use Imi\RequestContext;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Util\Http\MessageUtil;

/**
 * @Controller(prefix="/", singleton=true)
 */
class IndexController extends HttpController
{
    /**
     * @Action
     */
    public function info(): array
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return [
            'get'       => $request->get(),
            'post'      => $request->post(),
            'cookie'    => $request->getCookieParams(),
            'headers'   => MessageUtil::headersToStringList($request->getHeaders()),
            'server'    => $request->getServerParams(),
            'request'   => $request->request(),
            'uri'       => (string) $request->getUri(),
        ];
    }
}
