<?php
namespace Imi\Bean;

use Imi\App;
use Imi\Config;
use Imi\Util\Imi;
use ReflectionClass;
use Imi\Util\Traits\TSingleton;
use Imi\Main\Helper as MainHelper;

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
            if(!$this->parser->isParsed($fileNamespace))
            {
                $this->parser->parse($fileNamespace);
                $this->parser->execParse($fileNamespace);
            }
        });
    }

    /**
     * 注解类转注释文本
     *
     * @param \Imi\Bean\Annotation\Base $annotation
     * @param bool $skipDefaultValue 过滤默认值不显示
     * @return string
     */
    public static function toComments(\Imi\Bean\Annotation\Base $annotation, $skipDefaultValue = true): string
    {
        $result = '@' . Imi::getClassShortName(get_class($annotation));
        $properties = [];
        if($skipDefaultValue)
        {
            $refClass = new ReflectionClass($annotation);
            $defaultProperties = $refClass->getDefaultProperties();
        }
        foreach($annotation as $k => $v)
        {
            if($skipDefaultValue && $v === $defaultProperties[$k] ?? null)
            {
                continue;
            }
            if(is_string($v))
            {
                $value = '"' . $v . '"';
            }
            else
            {
                $value = json_encode($v);
                if(is_array($v))
                {
                    $value = '{' . substr($value, 1, -1) . '}';
                }
            }
            $properties[] = $k . '=' . $value;
        }
        if(isset($properties[0]))
        {
            $result .= '(' . implode(', ', $properties) . ')';
        }
        return $result;
    }

}