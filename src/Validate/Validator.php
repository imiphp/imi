<?php

declare(strict_types=1);

namespace Imi\Validate;

use Imi\Aop\Annotation\BaseInjectValue;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Traits\TBeanRealClass;
use Imi\Validate\Annotation\Condition;
use Imi\Validate\Annotation\Scene;
use Imi\Validate\Annotation\ValidateValue;

/**
 * 验证器类.
 */
class Validator implements IValidator
{
    use TBeanRealClass;

    /**
     * 验证器中的数据.
     *
     * @var array|object
     */
    protected $data;

    /**
     * 第一条失败信息.
     */
    protected ?string $message = null;

    /**
     * 验证结果.
     */
    protected array $results = [];

    /**
     * 第一条失败的规则.
     */
    protected ?Condition $failRule = null;

    /**
     * 验证失败的规则列表.
     */
    protected array $failRules = [];

    /**
     * 场景定义.
     */
    protected ?array $scene = null;

    /**
     * 当前场景.
     */
    protected ?string $currentScene = null;

    /**
     * 校验规则.
     *
     * @var \Imi\Validate\Annotation\Condition[]
     */
    private array $rules = [];

    /**
     * 注解校验规则集合.
     */
    private static array $annotationRules = [];

    public function __construct(?array &$data = [], ?array $rules = null)
    {
        $this->data = &$data;
        if (null === $rules)
        {
            if (!$this->rules)
            {
                $this->rules = $this->getAnnotationRules();
            }
        }
        else
        {
            $this->rules = $rules;
        }
        if (!$this->scene)
        {
            $this->scene = $this->getAnnotationScene();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setData(&$data): void
    {
        $this->data = &$data;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * {@inheritDoc}
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritDoc}
     */
    public function getAnnotationRules(): array
    {
        $className = static::__getRealClassName();
        $selfAnnotationRules = &self::$annotationRules;
        if (!isset($selfAnnotationRules[$className]))
        {
            $annotationRules = AnnotationManager::getClassAnnotations($className);

            $propertyAnnotations = AnnotationManager::getPropertiesAnnotations($className);
            foreach ($propertyAnnotations as $propertyName => $tAnnotations)
            {
                foreach ($tAnnotations as $annotation)
                {
                    $annotation = clone $annotation;
                    $annotation->name = $propertyName;
                    $annotationRules[] = $annotation;
                }
            }

            return $selfAnnotationRules[$className] = $annotationRules;
        }

        return $selfAnnotationRules[$className];
    }

    /**
     * {@inheritDoc}
     */
    public function validate(): bool
    {
        return $this->__validateAll($this->data, true);
    }

    /**
     * {@inheritDoc}
     */
    public function validateAll(): bool
    {
        return $this->__validateAll($this->data, false);
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * {@inheritDoc}
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * 内部验证方法.
     *
     * @param array|object $data
     * @param bool         $break 遇到验证失败是否中断
     */
    protected function __validateAll(&$data, bool $break): bool
    {
        $thisMessage = &$this->message;
        $thisResults = &$this->results;
        $thisFailRules = &$this->failRules;
        $thisFailRule = &$this->failRule;
        $thisMessage = null;
        $thisResults = [];
        $result = true;
        $sceneOption = $this->scene[$this->currentScene] ?? null;
        foreach ($this->rules as $annotation)
        {
            if (!$annotation instanceof Condition)
            {
                continue;
            }
            $annotationName = $annotation->name;
            if ($sceneOption && !\in_array($annotationName, $sceneOption))
            {
                continue;
            }
            if (!$this->validateByAnnotation($data, $annotation))
            {
                if (null === $annotation->default)
                {
                    $result = false;
                    $message = $this->buildMessage($data, $annotation);
                    $thisResults[$annotationName][] = $message;
                    $thisFailRules[$annotationName][] = $annotation;
                    if (null === $thisMessage)
                    {
                        $thisMessage = $message;
                        $thisFailRule = $annotation;
                    }
                    if ($break)
                    {
                        break;
                    }
                }
                else
                {
                    ObjectArrayHelper::set($data, $annotationName, $annotation->default);
                }
            }
        }

        return $result;
    }

    /**
     * 组建消息.
     *
     * @param array|object $data
     */
    protected function buildMessage($data, Condition $annotation): string
    {
        $message = $annotation->message;
        if (str_contains($message, '{:value}'))
        {
            $message = str_replace('{:value}', (string) ObjectArrayHelper::get($data, $annotation->name), $message);
        }

        return preg_replace_callback('/\{([^\}]+)\}/', function (array $matches) use ($data, $annotation): string {
            $name = $matches[1];
            if (isset($name[0]) && ':' === $name[0])
            {
                $name = substr($name, 1);
                $list = explode('.', $name, 2);
                if ('data' === $list[0])
                {
                    if (isset($list[1]))
                    {
                        return (string) ObjectArrayHelper::get($data, $list[1]);
                    }
                    else
                    {
                        return (string) $data;
                    }
                }
            }
            else
            {
                return (string) ObjectArrayHelper::get($annotation, $name);
            }

            return '';
        }, $message);
    }

    /**
     * 验证
     *
     * @param array|object $data
     */
    protected function validateByAnnotation($data, Condition $annotation): bool
    {
        if ($annotation->optional && !ObjectArrayHelper::exists($data, $annotation->name))
        {
            return true;
        }
        $args = [];
        if ($annotation->args)
        {
            foreach ($annotation->args as $arg)
            {
                $value = $this->getArgValue($data, $arg, $annotation);
                if ($value instanceof ValidateValue)
                {
                    $value = $this->getArgValue($data, $value->value, $annotation, false);
                }
                $args[] = $value;
            }
        }
        $callable = $annotation->callable;
        if (\is_array($callable) && isset($callable[0]))
        {
            if ('$this' === $callable[0])
            {
                $callable[0] = $this;
            }
            elseif ($callable[0] instanceof BaseInjectValue)
            {
                $callable[0] = $callable[0]->getRealValue();
            }
        }
        $result = $callable(...$args);
        if ($annotation->inverseResult)
        {
            return !$result;
        }
        else
        {
            return $result;
        }
    }

    /**
     * 获取参数值
     *
     * @param array|object $data
     * @param mixed        $arg
     *
     * @return mixed
     */
    protected function getArgValue($data, $arg, Condition $annotation, bool $includeAnnotationProperty = true)
    {
        if (!\is_string($arg))
        {
            return $arg;
        }
        elseif (preg_match('/\{:([^\}]+)\}/', $arg, $matches) > 0)
        {
            $argName = $matches[1];
            if ('value' === $argName)
            {
                return ObjectArrayHelper::get($data, $annotation->name);
            }
            $list = explode('.', $argName, 2);
            if ('data' === $list[0])
            {
                if (isset($list[1]))
                {
                    return ObjectArrayHelper::get($data, $list[1]);
                }
                else
                {
                    return $data;
                }
            }
        }
        elseif ($includeAnnotationProperty && preg_match('/\{([^\}]+)\}/', $arg, $matches) > 0)
        {
            $argName = $matches[1];

            return ObjectArrayHelper::get($annotation, $argName);
        }
        else
        {
            return $arg;
        }

        return null;
    }

    /**
     * Get 第一条失败的规则.
     */
    public function getFailRule(): Condition
    {
        return $this->failRule;
    }

    /**
     * Get 验证失败的规则列表.
     */
    public function getFailRules(): array
    {
        return $this->failRules;
    }

    /**
     * Get 场景定义.
     */
    public function getScene(): ?array
    {
        return $this->scene;
    }

    /**
     * Set 场景定义.
     *
     * @param array|null $scene 场景定义
     */
    public function setScene(?array $scene): self
    {
        $this->scene = $scene;

        return $this;
    }

    /**
     * Get 当前场景.
     */
    public function getCurrentScene(): ?string
    {
        return $this->currentScene;
    }

    /**
     * Set 当前场景.
     *
     * @param string|null $currentScene 当前场景
     */
    public function setCurrentScene(?string $currentScene): self
    {
        $this->currentScene = $currentScene;

        return $this;
    }

    /**
     * 获取注解定义的场景.
     */
    public function getAnnotationScene(): array
    {
        $className = static::__getRealClassName();
        /** @var \Imi\Validate\Annotation\Scene[] $scenes */
        $scenes = AnnotationManager::getClassAnnotations($className, Scene::class);
        if (!$scenes)
        {
            return [];
        }
        $result = [];
        foreach ($scenes as $item)
        {
            $result[$item->name] = $item->fields;
        }

        return $result;
    }
}
