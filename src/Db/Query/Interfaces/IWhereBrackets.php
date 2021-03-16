<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IWhereBrackets extends IBaseWhere
{
    /**
     * 回调.
     */
    public function getCallback(): callable;

    /**
     * 回调.
     */
    public function setCallback(callable $callback): void;
}
