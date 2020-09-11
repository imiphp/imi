<?php

namespace Imi\Db\Query\Interfaces;

interface IWhereBrackets extends IBaseWhere
{
    /**
     * 回调.
     *
     * @return callable
     */
    public function getCallback(): callable;

    /**
     * 回调.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function setCallback(callable $callback);
}
