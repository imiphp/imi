<?php

namespace Imi\Tool\Tools\Generate\HttpController;

use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * @Tool("generate")
 */
class ControllerGenerate
{
    /**
     * 生成一个 Http Controller.
     *
     * @Operation("httpController")
     *
     * @Arg(name="name", type=ArgType::STRING, required=true, comments="生成的 Controller 类名")
     * @Arg(name="namespace", type=ArgType::STRING, required=true, comments="生成的 Controller 所在命名空间")
     * @Arg(name="prefix", type=ArgType::STRING, default=null, comments="路由前缀，不传则为类名")
     * @Arg(name="render", type=ArgType::STRING, default="json", comments="渲染方式，默认为json，可选：html/json/xml")
     * @Arg(name="rest", type=ArgType::BOOLEAN, default=false, comments="是否生成 RESTful 风格，默认 false")
     * @Arg(name="override", type=ArgType::BOOLEAN, default=false, comments="是否覆盖已存在的文件，请慎重！(true/false)")
     *
     * @param string      $name
     * @param string      $namespace
     * @param string|null $prefix
     * @param string      $render
     * @param bool        $rest
     * @param bool        $override
     *
     * @return void
     */
    public function generate($name, $namespace, $prefix, $render, $rest, $override)
    {
        if (null === $prefix)
        {
            $prefix = '/' . $name . '/';
        }
        $data = compact('name', 'namespace', 'prefix', 'render', 'override');
        if ($rest)
        {
            $content = $this->renderTemplate($data, 'restTemplate');
        }
        else
        {
            $content = $this->renderTemplate($data);
        }
        $fileName = File::path(Imi::getNamespacePath($namespace), $name . '.php');
        if (is_file($fileName) && !$override)
        {
            // 不覆盖
            return;
        }
        file_put_contents($fileName, $content);
    }

    /**
     * 渲染模版.
     *
     * @param array  $data
     * @param string $template
     *
     * @return string
     */
    private function renderTemplate($data, $template = 'template')
    {
        extract($data);
        ob_start();
        include __DIR__ . '/' . $template . '.tpl';

        return ob_get_clean();
    }
}
