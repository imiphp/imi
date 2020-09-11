<?php

namespace Imi\Test\HttpServer\Http2TestServer\Controller;

use Imi\ConnectContext;
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
        ConnectContext::use(function ($context) {
            $context['count'] = ($context['count'] ?? 0) + 1;

            return $context;
        });
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $response = RequestContext::get('response')
        ->withHeader('trailer', 'yurun')
        ->withTrailer('yurun', 'niubi');
        RequestContext::set('response', $response);

        return [
            'get'               => $request->get(),
            'post'              => $request->post(),
            'cookie'            => $request->getCookieParams(),
            'headers'           => MessageUtil::headersToStringList($request->getHeaders()),
            'server'            => $request->getServerParams(),
            'request'           => $request->request(),
            'uri'               => (string) $request->getUri(),
            'connectContext'    => ConnectContext::get(),
        ];
    }
}
