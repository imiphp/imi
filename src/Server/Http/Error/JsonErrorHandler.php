<?php
namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\RequestContext;
use Imi\Util\Format\Json;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;

class JsonErrorHandler implements IErrorHandler
{
    /**
     * debug 为 false时也显示错误信息
     * @var boolean
     */
    protected $releaseShow = false;

    /**
     * 取消继续抛出异常
     * @var boolean
     */
    protected $cancelThrow = false;

    public function handle(\Throwable $throwable): bool
    {
        if($this->releaseShow || App::isDebug())
        {
            $data = [
                'message'   => $throwable->getMessage(),
                'code'      => $throwable->getCode(),
                'file'      => $throwable->getFile(),
                'line'      => $throwable->getLine(),
                'trace'     => explode(PHP_EOL, $throwable->getTraceAsString()),
            ];
        }
        else
        {
            $data = [
                'success' => false,
                'message' => 'error',
            ];
        }
        RequestContext::get('response')
        ->withAddedHeader(RequestHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON)
        ->write(json_encode($data))
        ->send();
        return $this->cancelThrow;
    }
}