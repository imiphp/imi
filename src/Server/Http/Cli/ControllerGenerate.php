<?php

declare(strict_types=1);

namespace Imi\Server\Http\Cli;

use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * @Command("generate")
 */
class ControllerGenerate extends BaseCommand
{
    /**
     * 生成一个 Http Controller.
     *
     * @CommandAction("httpController")
     *
     * @Argument(name="name", type=ArgType::STRING, required=true, comments="生成的 Controller 类名")
     * @Argument(name="namespace", type=ArgType::STRING, required=true, comments="生成的 Controller 所在命名空间")
     * @Option(name="prefix", type=ArgType::STRING, default=null, comments="路由前缀，不传则为类名")
     * @Option(name="render", type=ArgType::STRING, default="json", comments="渲染方式，默认为json，可选：html/json/xml")
     * @Option(name="rest", type=ArgType::BOOLEAN, default=false, comments="是否生成 RESTful 风格，默认 false")
     * @Option(name="override", type=ArgType::BOOLEAN, default=false, comments="是否覆盖已存在的文件，请慎重！(true/false)")
     */
    public function generate(string $name, string $namespace, ?string $prefix, ?string $render, bool $rest, bool $override): void
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
     */
    private function renderTemplate(array $data, string $template = 'template'): string
    {
        extract($data);
        ob_start();
        include __DIR__ . '/' . $template . '.tpl';

        return ob_get_clean();
    }
}
