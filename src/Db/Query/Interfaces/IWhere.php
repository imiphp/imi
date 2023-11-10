<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IWhere extends IBaseWhere
{
    /**
     * 字段名.
     */
    public function getFieldName(): ?string;

    /**
     * 比较符.
     */
    public function getOperation(): ?string;

    /**
     * 值
     */
    public function getValue(): mixed;

    /**
     * 字段名.
     */
    public function setFieldName(?string $fieldName): void;

    /**
     * 比较符.
     */
    public function setOperation(?string $operation): void;

    /**
     * 值
     */
    public function setValue(mixed $value): void;
}
