<?php
namespace Imi\Tool\Parser;

use Imi\Tool\Annotation\Tool;
use Imi\Bean\Parser\BaseParser;
use Imi\Tool\Annotation\Operation;
use Imi\App;
use Imi\Tool\Annotation\Arg;
use Imi\Event\Event;

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
        if($annotation instanceof Tool)
        {
            $this->data['class'][$className]['Tool'] = $annotation;
            Event::trigger('TOOL_PARSER.PARSE_TOOL.' . $className);
        }
        else if($annotation instanceof Operation)
        {
            if(isset($this->data['class'][$className]['Methods'][$targetName]['Operation']) && $this->data['class'][$className]['Methods'][$targetName]['Operation'] != $annotation)
            {
                throw new \RuntimeException(sprintf('Tool %s/%s is already exists!', isset($this->data['class'][$className]['Tool']) ? $this->data['class'][$className]['Tool']->name : $className, $annotation->name));
            }
            $this->data['class'][$className]['Methods'][$targetName]['Operation'] = $annotation;
            if(isset($this->data['class'][$className]['Tool']))
            {
                $this->data['tool'][$this->data['class'][$className]['Tool']->name][$annotation->name] = [$className, $targetName];
            }
            else
            {
                $operation = $annotation;
                Event::one('TOOL_PARSER.PARSE_TOOL.' . $className, function() use($className, $operation, $targetName){
                    $this->data['tool'][$this->data['class'][$className]['Tool']->name][$operation->name] = [$className, $targetName];
                });
            }
            
        }
        else if($annotation instanceof Arg)
        {
            $this->data['class'][$className]['Methods'][$targetName]['Args'][$annotation->name] = $annotation;
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
        if(isset($this->data['tool'][$tool][$operation]))
        {
            $callable = $this->data['tool'][$tool][$operation];
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
        if(isset($this->data['tool'][$tool][$operation]))
        {
            $callable = $this->data['tool'][$tool][$operation];
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