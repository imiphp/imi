<?php
namespace Imi\Bean;

use Imi\App;
use Imi\Main\Helper as MainHelper;
use Imi\Config;
use Imi\Util\Traits\TSingleton;

/**
 * 注解处理类
 */
class Annotation
{
    use TSingleton;

    /**
     * 加载器
     * @var AnnotationLoader
     */
    private $loader;

    /**
     * 处理器
     * @var AnnotationParser
     */
    private $parser;

    public function __construct()
    {
        $this->loader = new AnnotationLoader;
        $this->parser = new AnnotationParser;
    }

    /**
     * 初始化
     * @param \Imi\Main\BaseMain[] $mains
     * @return void
     */
    public function init($mains = null)
    {
        if(null === $mains)
        {
            $mains = MainHelper::getMains();
        }
        foreach($mains as $main)
        {
            // 扫描注解
            $this->loadModuleAnnotations($main->getNamespace());
        }
    }

    /**
     * 获取加载器
     *
     * @return AnnotationLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * 获取处理器
     *
     * @return AnnotationParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * 加载模块注解
     * @param string $namespace
     * @return void
     */
    private function loadModuleAnnotations($namespace)
    {
        $this->loader->loadModuleAnnotations($namespace, function($fileNamespace){
            $this->parser->parse($fileNamespace);
            $this->parser->execParse($fileNamespace);
        });
    }

}