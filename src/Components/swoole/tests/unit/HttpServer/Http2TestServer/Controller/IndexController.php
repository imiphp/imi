<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Http2TestServer\Controller;

use Imi\ConnectionContext;
use Imi\RequestContext;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Util\Http\MessageUtil;

/**
 * @Controller(prefix="/")
 */
class IndexController extends HttpController
{
    /**
     * @Action
     */
    public function info(): array
    {
        ConnectionContext::use(function (array $context) {
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
            'get'                  => $request->get(),
            'post'                 => $request->post(),
            'cookie'               => $request->getCookieParams(),
            'headers'              => MessageUtil::headersToStringList($request->getHeaders()),
            'server'               => $request->getServerParams(),
            'request'              => $request->request(),
            'uri'                  => (string) $request->getUri(),
            'ConnectionContext'    => ConnectionContext::get(),
        ];
    }
}
