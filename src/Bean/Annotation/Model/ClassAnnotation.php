<?php

namespace Imi\Bean\Annotation\Model;

class ClassAnnotation
{
    /**
     * 类名.
     *
     * @var string
     */
    private $className;

    /**
     * 类注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[]
     */
    private $classAnnotations = [];

    /**
     * 方法注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[][]
     */
    private $methodAnnotations = [];

    /**
     * 属性注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[][]
     */
    private $propertyAnnotations = [];

    /**
     * 常量注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[][]
     */
    private $constantAnnotations = [];

    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Get 类注解列表.
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getClassAnnotations()
    {
        return $this->classAnnotations;
    }

    /**
     * Add 类注解列表.
     *
     * @param \Imi\Bean\Annotation\Base[] $classAnnotations 类注解列表
     *
     * @return self
     */
    public function addClassAnnotations(array $classAnnotations)
    {
        $this->classAnnotations = array_merge($this->classAnnotations, $classAnnotations);

        return $this;
    }

    /**
     * Get 方法注解列表.
     *
     * @param string|null $methodName
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getMethodAnnotations($methodName = null)
    {
        if (null === $methodName)
        {
            return $this->methodAnnotations;
        }
        else
        {
            return $this->methodAnnotations[$methodName] ?? [];
        }
    }

    /**
     * Add 方法注解列表.
     *
     * @param string                      $methodName
     * @param \Imi\Bean\Annotation\Base[] $methodAnnotations 方法注解列表
     *
     * @return self
     */
    public function addMethodAnnotations(string $methodName, array $methodAnnotations)
    {
        $this->methodAnnotations[$methodName] = array_merge($this->methodAnnotations[$methodName] ?? [], $methodAnnotations);

        return $this;
    }

    /**
     * Get 属性注解列表.
     *
     * @param string|null $propertyName
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getPropertyAnnotations($propertyName = null)
    {
        if (null === $propertyName)
        {
            return $this->propertyAnnotations;
        }
        else
        {
            return $this->propertyAnnotations[$propertyName] ?? [];
        }
    }

    /**
     * Add 属性注解列表.
     *
     * @param string                      $propertyName
     * @param \Imi\Bean\Annotation\Base[] $propertyAnnotations 属性注解列表
     *
     * @return self
     */
    public function addPropertyAnnotations(string $propertyName, array $propertyAnnotations)
    {
        $this->propertyAnnotations[$propertyName] = array_merge($this->propertyAnnotations[$propertyName] ?? [], $propertyAnnotations);

        return $this;
    }

    /**
     * Get 常量注解列表.
     *
     * @param string|null $constantName
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getConstantAnnotations($constantName = null)
    {
        if (null === $constantName)
        {
            return $this->constantAnnotations;
        }
        else
        {
            return $this->constantAnnotations[$constantName] ?? [];
        }
    }

    /**
     * Add 常量注解列表.
     *
     * @param string                      $constantName
     * @param \Imi\Bean\Annotation\Base[] $constantAnnotations 常量注解列表
     *
     * @return self
     */
    public function addConstantAnnotations(string $constantName, array $constantAnnotations)
    {
        $this->constantAnnotations[$constantName] = array_merge($this->constantAnnotations[$constantName] ?? [], $constantAnnotations);

        return $this;
    }

    /**
     * 清空类注解.
     *
     * @return void
     */
    public function clearClassAnnotations()
    {
        $this->classAnnotations = [];
    }

    /**
     * 清空方法注解.
     *
     * @param string|null $methodName
     *
     * @return void
     */
    public function clearMethodAnnotations(?string $methodName = null)
    {
        if (null === $methodName)
        {
            $this->methodAnnotations = [];
        }
        elseif (isset($this->methodAnnotations[$methodName]))
        {
            unset($this->methodAnnotations[$methodName]);
        }
    }

    /**
     * 清空属性注解.
     *
     * @param string|null $propertyName
     *
     * @return void
     */
    public function clearPropertyAnnotations(?string $propertyName = null)
    {
        if (null === $propertyName)
        {
            $this->propertyAnnotations = [];
        }
        elseif (isset($this->propertyAnnotations[$propertyName]))
        {
            unset($this->propertyAnnotations[$propertyName]);
        }
    }

    /**
     * 清空常量注解.
     *
     * @param string|null $constantName
     *
     * @return void
     */
    public function clearConstantAnnotations(?string $constantName = null)
    {
        if (null === $constantName)
        {
            $this->constantAnnotations = [];
        }
        elseif (isset($this->constantAnnotations[$constantName]))
        {
            unset($this->constantAnnotations[$constantName]);
        }
    }

    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
