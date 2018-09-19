<?php
namespace Imi\Server\View;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Response;
use \Imi\Server\View\Annotation\View as ViewAnnotation;
use Imi\App;
use Imi\RequestContext;

/**
 * 视图类
 * @Bean("View")
 */
class View
{
    /**
     * 核心处理器
     * @var array
     */
    protected $coreHandlers = [
        'html'  => \Imi\Server\View\Handler\Html::class,
        'json'  => \Imi\Server\View\Handler\Json::class,
        'xml'   => \Imi\Server\View\Handler\Xml::class,
    ];

    /**
     * 扩展处理器
     * @var array
     */
    protected $exHandlers = [];

    /**
     * 传入视图处理器的数据
     * @var array
     */
    protected $data = [];

    public function render(ViewAnnotation $view, Response $response = null): Response
    {
        if(is_array($view->data))
        {
            $view->data = array_merge($this->data, $view->data);
        }
        if(null === $response)
        {
            $response = RequestContext::get('response');
        }
        if(isset($this->exHandlers[$view->renderType]))
        {
            return $this->handle($this->exHandlers[$view->renderType], $view, $response);
        }
        else if(isset($this->coreHandlers[$view->renderType]))
        {
            return $this->handle($this->coreHandlers[$view->renderType], $view, $response);
        }
        else
        {
            throw new \RuntimeException('Unsupport View renderType: ' . $view->renderType);
        }
        return $response;
    }

    protected function handle($handlerClass, ViewAnnotation $view, Response $response = null): Response
    {
        $handler = RequestContext::getServerBean($handlerClass);
        return $handler->handle($view, $response);
    }
}