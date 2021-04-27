<?php

declare(strict_types=1);

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\View;
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
     */
    protected ?string $templatePath = null;

    /**
     * 支持的模版文件扩展名，优先级按先后顺序.
     */
    protected array $fileSuffixs = [
        'tpl',
        'html',
        'php',
    ];

    /**
     * 模版引擎处理类.
     */
    protected string $templateEngine = \Imi\Server\View\Engine\Php::class;

    /**
     * 模版引擎处理对象
     */
    protected IEngine $templateEngineInstance;

    public function __init(): void
    {
        $this->templateEngineInstance = RequestContext::getServerBean($this->templateEngine);
    }

    /**
     * @param mixed $data
     */
    public function handle(View $viewAnnotation, $data, IHttpResponse $response): IHttpResponse
    {
        $fileName = $this->getTemplateFilePath($viewAnnotation);

        if (false === $fileName || !is_file($fileName))
        {
            return $response;
        }

        return $this->templateEngineInstance->render($response, $fileName, $data);
    }

    /**
     * 获取模版文件真实路径，失败返回false.
     *
     * @return string|bool
     */
    protected function getTemplateFilePath(View $viewAnnotation)
    {
        $fileName = realpath($viewAnnotation->template);
        if ($fileName && is_file($fileName))
        {
            return $fileName;
        }
        $fileName = File::path($this->templatePath ?: '', $viewAnnotation->baseDir ?? '', $viewAnnotation->template);
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
