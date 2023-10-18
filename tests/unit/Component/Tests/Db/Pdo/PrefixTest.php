<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db\Pdo;

class PrefixTest extends QueryCurdTest
{
    /**
     * 连接池名.
     */
    protected ?string $poolName = 'dbPrefix';

    protected string $tableArticle = 'article';

    protected string $tableTestJson = 'test_json';
}
