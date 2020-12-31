<?php

declare(strict_types=1);

namespace Imi\Server\View;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Response;

/**
 * 视图类.
 *
 * @Bean("View")
 */
class View
{
    /**
     * 核心处理器.
     *
     * @var array
     */
    protected array $coreHandlers = [
        'html'  => \Imi\Server\View\Handler\Html::class,
        'json'  => \Imi\Server\View\Handler\Json::class,
        'xml'   => \Imi\Server\View\Handler\Xml::class,
    ];

    /**
     * 扩展处理器.
     *
     * @var array
     */
    protected array $exHandlers = [];

    /**
     * 传入视图处理器的数据.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * 视图处理器对象列表.
     *
     * @var \Imi\Server\View\Handler\IHandler[]
     */
    protected array $handlers = [];

    public function __init()
    {
        $handlers = &$this->handlers;
        foreach ([$this->coreHandlers, $this->exHandlers] as $list)
        {
            foreach ($list as $name => $class)
            {
                $handlers[$name] = RequestContext::getServerBean($class);
            }
        }
    }

    public function render(string $renderType, $data, array $options, ?Response $response = null): Response
    {
        $handlers = &$this->handlers;
        if (isset($handlers[$renderType]))
        {
            if ($this->data && \is_array($data))
            {
                $data = array_merge($this->data, $data);
            }
            if (null === $response)
            {
                $response = RequestContext::get('response');
            }

            return $handlers[$renderType]->handle($data, $options, $response);
        }
        else
        {
            throw new \RuntimeException('Unsupport View renderType: ' . $renderType);
        }
    }
}
