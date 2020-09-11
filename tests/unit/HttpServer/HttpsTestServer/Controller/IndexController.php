<?php

namespace Imi\Test\HttpServer\HttpsTestServer\Controller;

use Imi\Controller\HttpController;
use Imi\RequestContext;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;
use Imi\Util\Http\MessageUtil;

/**
 * @Controller(prefix="/", singleton=true)
 */
class IndexController extends HttpController
{
    /**
     * @Action
     *
     * @return void
     */
    public function info()
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
