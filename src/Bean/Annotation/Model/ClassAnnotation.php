<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation\Model;

class ClassAnnotation
{
    /**
     * 类名.
     */
    private string $className = '';

    /**
     * 类注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[]
     */
    private array $classAnnotations = [];

    /**
     * 方法注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[][]
     */
    private array $methodAnnotations = [];

    /**
     * 属性注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[][]
     */
    private array $propertyAnnotations = [];

    /**
     * 常量注解列表.
     *
     * @var \Imi\Bean\Annotation\Base[][]
     */
    private array $constantAnnotations = [];

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * Get 类注解列表.
     *
     * @return \Imi\Bean\Annotation\Base[]
     */
    public function getClassAnnotations(): array
    {
        return $this->classAnnotations;
    }

    /**
     * Add 类注解列表.
     *
     * @param \Imi\Bean\Annotation\Base[] $classAnnotations 类注解列表
     */
    public function addClassAnnotations(array $classAnnotations): self
    {
        $this->classAnnotations = array_merge($this->classAnnotations, $classAnnotations);

        return $this;
    }

    /**
     * Get 方法注解列表.
     *
     * @param string|null $methodName
     *
     * @return \Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]
     */
    public function getMethodAnnotations($methodName = null): array
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
     * @param \Imi\Bean\Annotation\Base[] $methodAnnotations 方法注解列表
     */
    public function addMethodAnnotations(string $methodName, array $methodAnnotations): self
    {
        $this->methodAnnotations[$methodName] = array_merge($this->methodAnnotations[$methodName] ?? [], $methodAnnotations);

        return $this;
    }

    /**
     * Get 属性注解列表.
     *
     * @return \Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]
     */
    public function getPropertyAnnotations(?string $propertyName = null): array
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
     * @param \Imi\Bean\Annotation\Base[] $propertyAnnotations 属性注解列表
     */
    public function addPropertyAnnotations(string $propertyName, array $propertyAnnotations): self
    {
        $this->propertyAnnotations[$propertyName] = array_merge($this->propertyAnnotations[$propertyName] ?? [], $propertyAnnotations);

        return $this;
    }

    /**
     * Get 常量注解列表.
     *
     * @return \Imi\Bean\Annotation\Base[]|\Imi\Bean\Annotation\Base[][]
     */
    public function getConstantAnnotations(?string $constantName = null): array
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
     * @param \Imi\Bean\Annotation\Base[] $constantAnnotations 常量注解列表
     */
    public function addConstantAnnotations(string $constantName, array $constantAnnotations): self
    {
        $this->constantAnnotations[$constantName] = array_merge($this->constantAnnotations[$constantName] ?? [], $constantAnnotations);

        return $this;
    }

    /**
     * 清空类注解.
     */
    public function clearClassAnnotations(): void
    {
        $this->classAnnotations = [];
    }

    /**
     * 清空方法注解.
     */
    public function clearMethodAnnotations(?string $methodName = null): void
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
     */
    public function clearPropertyAnnotations(?string $propertyName = null): void
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
     */
    public function clearConstantAnnotations(?string $constantName = null): void
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
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}
