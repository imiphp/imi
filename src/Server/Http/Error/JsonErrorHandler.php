<?php

declare(strict_types=1);

namespace Imi\Server\Http\Error;

use Imi\App;
use Imi\RequestContext;
use Imi\Server\View\Annotation\View;

class JsonErrorHandler implements IErrorHandler
{
    /**
     * debug 为 false时也显示错误信息.
     */
    protected bool $releaseShow = false;

    /**
     * 取消继续抛出异常.
     */
    protected bool $cancelThrow = false;

    /**
     * 异常时响应的 Http Code.
     */
    protected ?int $httpCode = null;

    protected View $viewAnnotation;

    public function __construct()
    {
        $this->viewAnnotation = new View();
    }

    /**
     * {@inheritDoc}
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
        $requestContext = RequestContext::getContext();
        /** @var \Imi\Server\View\Handler\Json $jsonView */
        $jsonView = $requestContext['server']->getBean('JsonView');
        $request = $jsonView->handle($this->viewAnnotation, null, $data, $requestContext['response'] ?? null);
        if (null !== $this->httpCode)
        {
            $request->setStatus($this->httpCode);
        }
        $request->send();

        return $this->cancelThrow;
    }
}
