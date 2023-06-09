<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IWhereFullText extends IBaseWhere
{
    /**
     * 全文搜索配置.
     */
    public function getOptions(): ?IFullTextOptions;
}
