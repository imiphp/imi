<?php

namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\RequestContext;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

class JsonErrorHandler implements IErrorHandler
{
    /**
     * debug 为 false时也显示错误信息.
     *
     * @var bool
     */
    protected bool $releaseShow = false;

    /**
     * 取消继续抛出异常.
     *
     * @var bool
     */
    protected bool $cancelThrow = false;

    /**
     * 捕获错误
     * 返回值为 true 则取消继续抛出异常.
     *
     * @param \Throwable $throwable
     *
     * @return bool
     */
    public function handle(\Throwable $throwable): bool
    {
        if ($this->releaseShow || App::isDebug())
        {
            $data = [
                'message'   => $throwable->getMessage(),
                'code'      => $throwable->getCode(),
                'file'      => $throwable->getFile(),
                'line'      => $throwable->getLine(),
                'trace'     => explode(\PHP_EOL, $throwable->getTraceAsString()),
            ];
        }
        else
        {
            $data = [
                'success' => false,
                'message' => 'error',
            ];
        }
        /** @var \Imi\Server\Http\Message\Response $response */
        $response = RequestContext::get('response');
        $response->addHeader(ResponseHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON)
                 ->getBody()
                 ->write(json_encode($data));
        $response->send();

        return $this->cancelThrow;
    }
}
