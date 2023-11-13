<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 创建表语句注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class DDL extends Base
{
    public function __construct(
        /**
         * 表结构 SQL；CREATE TABLE 语句.
         */
        public string $sql = '',
        /**
         * SQL 解码函数.
         *
         * @var callable|string|null
         */
        public $decode = null
    ) {
    }

    /**
     * 获取真实 SQL，如果需要解码会自动解码
     */
    public function getRawSql(): string
    {
        if (null === $this->decode || '' === $this->decode)
        {
            return $this->sql;
        }
        else
        {
            return ($this->decode)($this->sql);
        }
    }
}
