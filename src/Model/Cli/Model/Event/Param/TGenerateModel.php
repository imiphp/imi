<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Model\Event\Param;

trait TGenerateModel
{
    public string $namespace = '';

    public string $baseClassName = '';

    public string $className = '';

    public string $fullClassName = '';

    public string $tableName = '';

    public array $table = [];

    public array $fields = [];

    public bool $entity = false;

    public bool $bean = false;

    public bool $incrUpdate = false;

    public ?string $poolName = null;

    public string $ddl = '';

    public string $rawDDL = '';

    public ?string $ddlDecode = null;

    public string $tableComment = '';

    public bool $lengthCheck = false;
}
