<?php

namespace Imi\Db\Annotation;

abstract class TransactionType
{
    /**
     * 事务嵌套.
     */
    const NESTING = 'Nesting';

    /**
     * 该方法必须在事务中被调用.
     */
    const REQUIREMENT = 'requirement';

    /**
     * 如果当前不在事务中则开启事务
     */
    const AUTO = 'auto';
}
