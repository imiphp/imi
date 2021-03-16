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
     *
     * @return mixed
     */
    public function getValue();

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
     *
     * @param mixed $value
     */
    public function setValue($value): void;
}
