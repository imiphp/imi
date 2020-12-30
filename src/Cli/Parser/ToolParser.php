<?php

declare(strict_types=1);

namespace Imi\Cli\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Event\Event;

class ToolParser extends BaseParser
{
    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     *
     * @return void
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName)
    {
        $data = &$this->data;
        if ($annotation instanceof Command)
        {
            $data['class'][$className]['Tool'] = $annotation;
            Event::trigger('TOOL_PARSER.PARSE_TOOL.' . $className);
        }
        elseif ($annotation instanceof CommandAction)
        {
            if (isset($data['class'][$className]['Methods'][$targetName]['Operation']) && $data['class'][$className]['Methods'][$targetName]['Operation'] != $annotation)
            {
                throw new \RuntimeException(sprintf('Tool %s/%s is already exists!', isset($data['class'][$className]['Tool']) ? $data['class'][$className]['Tool']->name : $className, $annotation->name));
            }
            $data['class'][$className]['Methods'][$targetName]['Operation'] = $annotation;
            if (isset($data['class'][$className]['Tool']))
            {
                $data['tool'][$data['class'][$className]['Tool']->name][$annotation->name] = [$className, $targetName];
            }
            else
            {
                $operation = $annotation;
                Event::one('TOOL_PARSER.PARSE_TOOL.' . $className, function () use ($className, $operation, $targetName, &$data) {
                    $data['tool'][$data['class'][$className]['Tool']->name][$operation->name] = [$className, $targetName];
                });
            }
        }
        elseif ($annotation instanceof Argument)
        {
            $data['class'][$className]['Methods'][$targetName]['Arguments'][$annotation->name] = $annotation;
        }
        elseif ($annotation instanceof Option)
        {
            $data['class'][$className]['Methods'][$targetName]['Options'][$annotation->name] = $annotation;
        }
    }
}
