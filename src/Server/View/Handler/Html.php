<?php

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Response;
use Imi\Server\View\Engine\IEngine;
use Imi\Util\File;

/**
 * Html视图处理器.
 *
 * @Bean("HtmlView")
 */
class Html implements IHandler
{
    /**
     * 模版文件根路径.
     *
     * @var string|null
     */
    protected ?string $templatePath = null;

    /**
     * 支持的模版文件扩展名，优先级按先后顺序.
     *
     * @var array
     */
    protected array $fileSuffixs = [
        'tpl',
        'html',
        'php',
    ];

    /**
     * 模版引擎处理类.
     *
     * @var string
     */
    protected string $templateEngine = \Imi\Server\View\Engine\Php::class;

    /**
     * 模版引擎处理对象
     *
     * @var \Imi\Server\View\Engine\IEngine
     */
    protected IEngine $templateEngineInstance;

    public function __init()
    {
        $this->templateEngineInstance = RequestContext::getServerBean($this->templateEngine);
    }

    public function handle($data, array $options, Response $response): Response
    {
        $fileName = $this->getTemplateFilePath($options);

        if (!is_file($fileName))
        {
            return $response;
        }

        return $this->templateEngineInstance->render($response, $fileName, $data);
    }

    /**
     * 获取模版文件真实路径，失败返回false.
     *
     * @param array $options
     *
     * @return string|bool
     */
    protected function getTemplateFilePath(array $options)
    {
        $fileName = realpath($options['template']);
        if (is_file($fileName))
        {
            return $fileName;
        }
        $fileName = File::path($this->templatePath, $options['baseDir'] ?? '', $options['template']);
        foreach ($this->fileSuffixs as $suffix)
        {
            $tryFileName = $fileName . '.' . $suffix;
            if (is_file($tryFileName))
            {
                return $tryFileName;
            }
        }

        return false;
    }
}
