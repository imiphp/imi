<?php

declare(strict_types=1);

namespace Imi\Validate;

interface IValidator
{
    /**
     * 设置验证器中的数据.
     */
    public function setData(array|object &$data): void;

    /**
     * 获取验证器中的数据.
     */
    public function getData(): array|object;

    /**
     * 设置校验规则.
     *
     * @param \Imi\Validate\Annotation\Condition[] $rules
     */
    public function setRules(array $rules): void;

    /**
     * 获得所有校验规则.
     *
     * @return \Imi\Validate\Annotation\Condition[]
     */
    public function getRules(): array;

    /**
     * Get 场景定义.
     */
    public function getScene(): ?array;

    /**
     * Set 场景定义.
     *
     * @param array|null $scene 场景定义
     */
    public function setScene(?array $scene): self;

    /**
     * Get 当前场景.
     */
    public function getCurrentScene(): ?string;

    /**
     * Set 当前场景.
     *
     * @param string|null $currentScene 当前场景
     */
    public function setCurrentScene(?string $currentScene): self;

    /**
     * 获得所有注解校验规则.
     *
     * @return \Imi\Validate\Annotation\Condition[]
     */
    public function getAnnotationRules(): array;

    /**
     * 验证，返回是否通过
     * 当遇到不通过时结束验证流程.
     */
    public function validate(): bool;

    /**
     * 验证所有，返回是否通过.
     */
    public function validateAll(): bool;

    /**
     * 获取第一条失败信息.
     */
    public function getMessage(): ?string;

    /**
     * 获取所有验证结果.
     */
    public function getResults(): array;
}
