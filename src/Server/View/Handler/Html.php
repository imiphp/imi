<?php
namespace Imi\Server\View\Handler;

use Imi\Util\File;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Message\Response;

/**
 * Html视图处理器
 * @Bean("HtmlView")
 */
class Html implements IHandler
{
	/**
	 * 模版文件根路径
	 * @var string
	 */
	protected $templatePath;

	/**
	 * 支持的模版文件扩展名，优先级按先后顺序
	 * @var array
	 */
	protected $fileSuffixs = [
		'tpl',
		'html',
		'php'
	];

	/**
	 * 模版引擎处理类
	 * @var string
	 */
	protected $templateEngine = \Imi\Server\View\Engine\Php::class;

	public function handle(View $viewAnnotation, Response $response): Response
	{
		$fileName = $this->getTemplateFilePath($viewAnnotation->template);

		if(!is_file($fileName))
		{
			return $response;
		}

		$engine = RequestContext::getBean($this->templateEngine);

		return $engine->render($response, $fileName, $viewAnnotation->data);
	}

	/**
	 * 获取模版文件真实路径，失败返回false
	 * @param string $template
	 * @return string|boolean
	 */
	protected function getTemplateFilePath($template)
	{
		$fileName = realpath($template);
		if(is_file($fileName))
		{
			return $template;
		}
		$fileName = File::path($this->templatePath, $template);
		foreach($this->fileSuffixs as $suffix)
		{
			$tryFileName = $fileName . '.' . $suffix;
			if(is_file($tryFileName))
			{
				return $tryFileName;
			}
		}
		return false;
	}
}