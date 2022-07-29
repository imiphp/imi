<?php

declare(strict_types=1);

namespace Imi\Server\View\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\BaseViewOption;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;
use Imi\Server\View\Engine\IEngine;
use Imi\Util\File;

/**
 * Html视图处理器.
 *
 * @Bean(name="HtmlView", recursion=false)
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
    protected ?IEngine $templateEngineInstance = null;

    public function __init(): void
    {
        $this->templateEngineInstance = RequestContext::getServerBean($this->templateEngine);
    }

    /**
     * {@inheritDoc}
     */
    public function handle(View $viewAnnotation, ?BaseViewOption $viewOption, $data, IHttpResponse $response): IHttpResponse
    {
        if (!$viewOption instanceof HtmlView)
        {
            return $response;
        }
        $fileName = $this->getTemplateFilePath($viewOption);

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
    protected function getTemplateFilePath(HtmlView $viewOption)
    {
        if (null !== $viewOption->template)
        {
            $fileName = realpath($viewOption->template);
            if ($fileName && is_file($fileName))
            {
                return $fileName;
            }
        }
        $fileName = File::path($this->templatePath ?? '', $viewOption->baseDir ?? '', $viewOption->template ?? '');
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
