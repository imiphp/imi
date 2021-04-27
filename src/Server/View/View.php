<?php

declare(strict_types=1);

namespace Imi\Server\View;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\BaseViewOption;
use Imi\Server\View\Annotation\View as ViewAnnotation;

/**
 * 视图类.
 *
 * @Bean("View")
 */
class View
{
    /**
     * 核心处理器.
     */
    protected array $coreHandlers = [
        'html'  => \Imi\Server\View\Handler\Html::class,
        'json'  => \Imi\Server\View\Handler\Json::class,
        'xml'   => \Imi\Server\View\Handler\Xml::class,
    ];

    /**
     * 扩展处理器.
     */
    protected array $exHandlers = [];

    /**
     * 传入视图处理器的数据.
     */
    protected array $data = [];

    /**
     * 视图处理器对象列表.
     *
     * @var \Imi\Server\View\Handler\IHandler[]
     */
    protected array $handlers = [];

    public function __init(): void
    {
        $handlers = &$this->handlers;
        foreach ([$this->coreHandlers, $this->exHandlers] as $list)
        {
            if ($list)
            {
                foreach ($list as $name => $class)
                {
                    $handlers[$name] = RequestContext::getServerBean($class);
                }
            }
        }
    }

    /**
     * @param mixed $data
     */
    public function render(ViewAnnotation $viewAnnotation, ?BaseViewOption $viewOption, $data, IHttpResponse $response): IHttpResponse
    {
        $handlers = &$this->handlers;
        $renderType = $viewAnnotation->renderType;
        if (isset($handlers[$renderType]))
        {
            if ($this->data && \is_array($data))
            {
                $data = array_merge($this->data, $data);
            }

            return $handlers[$renderType]->handle($viewAnnotation, $viewOption, $data, $response);
        }
        else
        {
            throw new \RuntimeException('Unsupport View renderType: ' . $renderType);
        }
    }
}
