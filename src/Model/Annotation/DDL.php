<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 创建表语句注解.
 *
 * @Annotation
 *
 * @Target("CLASS")
 *
 * @property string               $sql    表结构 SQL；CREATE TABLE 语句
 * @property callable|string|null $decode SQL 解码函数
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class DDL extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'sql';

    /**
     * @todo $decode 类型改为：?callable
     *
     * @param callable|string|null $decode
     */
    public function __construct(?array $__data = null, string $sql = '', $decode = null)
    {
        parent::__construct(...\func_get_args());
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
