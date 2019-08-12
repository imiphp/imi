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

    public function render($renderType, $data, $options, Response $response = null): Response
    {
        if(is_array($data))
        {
            $data = array_merge($this->data, $data);
        }
        if(null === $response)
        {
            $response = RequestContext::get('response');
        }
        if(isset($this->exHandlers[$renderType]))
        {
            return $this->handle($this->exHandlers[$renderType], $data, $options, $response);
        }
        else if(isset($this->coreHandlers[$renderType]))
        {
            return $this->handle($this->coreHandlers[$renderType], $data, $options, $response);
        }
        else
        {
            throw new \RuntimeException('Unsupport View renderType: ' . $renderType);
        }
        return $response;
    }

    protected function handle($handlerClass, $data, $options, Response $response = null): Response
    {
        $handler = RequestContext::getServerBean($handlerClass);
        return $handler->handle($data, $options, $response);
    }
}