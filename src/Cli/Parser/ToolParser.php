<?php
namespace Imi\Cli\Parser;

use Imi\App;
use Imi\Event\Event;
use Imi\Cli\Annotation\Arg;
use Imi\Bean\Parser\BaseParser;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\CommandAction;

class ToolParser extends BaseParser
{
    /**
     * 处理方法
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string $className 类名
     * @param string $target 注解目标类型（类/属性/方法）
     * @param string $targetName 注解目标名称
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        $data = &$this->data;
        if($annotation instanceof Command)
        {
            $data['class'][$className]['Tool'] = $annotation;
            Event::trigger('TOOL_PARSER.PARSE_TOOL.' . $className);
        }
        else if($annotation instanceof CommandAction)
        {
            if(isset($data['class'][$className]['Methods'][$targetName]['Operation']) && $data['class'][$className]['Methods'][$targetName]['Operation'] != $annotation)
            {
                throw new \RuntimeException(sprintf('Tool %s/%s is already exists!', isset($data['class'][$className]['Tool']) ? $data['class'][$className]['Tool']->name : $className, $annotation->name));
            }
            $data['class'][$className]['Methods'][$targetName]['Operation'] = $annotation;
            if(isset($data['class'][$className]['Tool']))
            {
                $data['tool'][$data['class'][$className]['Tool']->name][$annotation->name] = [$className, $targetName];
            }
            else
            {
                $operation = $annotation;
                Event::one('TOOL_PARSER.PARSE_TOOL.' . $className, function() use($className, $operation, $targetName, &$data){
                    $data['tool'][$data['class'][$className]['Tool']->name][$operation->name] = [$className, $targetName];
                });
            }
            
        }
        else if($annotation instanceof Argument)
        {
            $data['class'][$className]['Methods'][$targetName]['Arguments'][$annotation->name] = $annotation;
        }
        else if($annotation instanceof Option)
        {
            $data['class'][$className]['Methods'][$targetName]['Options'][$annotation->name] = $annotation;
        }
    }

    /**
     * 获取回调，根据工具名和操作名
     * @param string $tool
     * @param string $operation
     * @return array
     */
    public function getCallable($tool, $operation)
    {
        $data = &$this->data;
        if(isset($data['tool'][$tool][$operation]))
        {
            $callable = $data['tool'][$tool][$operation];
            $callable[0] = App::getBean($callable[0]);
            return $callable;
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取工具类名和方法名
     * 
     * 返回格式：[
     *     'class'  =>  '',
     *     'method' =>  '',
     * ]
     *
     * @param string $tool
     * @param string $operation
     * @return array|null
     */
    public function getToolClassAndMethod($tool, $operation)
    {
        $data = &$this->data;
        if(isset($data['tool'][$tool][$operation]))
        {
            $callable = $data['tool'][$tool][$operation];
            return [
                'class'     =>  $callable[0],
                'method'    =>  $callable[1],
            ];
        }
        else
        {
            return null;
        }
    }

}