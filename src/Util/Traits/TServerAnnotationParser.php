<?php
namespace Imi\Util\Traits;

use Imi\Util\Text;
use Imi\ServerManage;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Config;

/**
 * 注解处理器按服务器名获取
 */
trait TServerAnnotationParser
{
    /**
     * 根据服务器获取的控制器缓存
     * @var array
     */
    private $cache = [];
    
    /**
     * 根据服务器获取对应的控制器数据
     * @param string $serverName
     * @return array
     */
    public function getByServer($serverName)
    {
        if(isset($this->cache[$serverName]))
        {
            return $this->cache[$serverName];
        }
        $namespaces = Config::get('@server.' . $serverName . '.beanScan', []);
        foreach($namespaces as &$namespace)
        {
            if('\\' !== substr($namespace, -1, 1))
            {
                $namespace .= '\\';
            }
        }
        unset($namespace);
        $result = [];
        foreach(AnnotationManager::getAnnotationPoints($this->controllerAnnotationClass, 'class') as $option)
        {
            $class = $option->getClass();
            foreach($namespaces as $namespace)
            {
                if(Text::startwith($class, $namespace))
                {
                    $result[$class] = $option;
                    continue 2;
                }
            }
        }
        $this->cache[$serverName] = $result;
        return $result;
    }
}