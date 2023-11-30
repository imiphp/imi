<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Event;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Event\CommonEvent;
use Imi\Model\Annotation\Relation\RelationBase;
use Imi\Model\Model;

class ModelRelationQueryEvent extends CommonEvent
{
    public function __construct(string $__eventName,
        public readonly Model $model,
        public readonly string $propertyName,
        public readonly ?RelationBase $annotation,
        public readonly ?object $struct = null,
        public readonly ?IQuery $query = null,
    ) {
        parent::__construct($__eventName);
    }
}
