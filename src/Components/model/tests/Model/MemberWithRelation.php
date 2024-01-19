<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Relation\AutoDelete;
use Imi\Model\Annotation\Relation\AutoSave;
use Imi\Model\Annotation\Relation\Relation;

/**
 * Member.
 *
 * @property array|null $relation
 * @property array|null $relationNoWith
 */
#[Inherit]
class MemberWithRelation extends Member
{
    public bool $inserted = false;

    public bool $updated = false;

    public bool $deleted = false;

    public bool $queryed = false;

    public bool $noWithQueryed = false;

    #[Relation]
    #[AutoSave]
    #[AutoDelete]
    public ?array $relation = null;

    public function getRelation(): ?array
    {
        return $this->relation;
    }

    public function setRelation(?array $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public static function __insertRelation(self $model, Relation $annotation): void
    {
        $model->inserted = true;
        $model->relation = [$model->password];
    }

    public static function __updateRelation(self $model, Relation $annotation): void
    {
        $model->updated = true;
        $model->relation = [$model->password];
    }

    public static function __deleteRelation(self $model, Relation $annotation): void
    {
        $model->deleted = true;
        $model->relation = null;
    }

    /**
     * @param self[] $models
     */
    public static function __queryRelation(array $models, Relation $annotation): void
    {
        foreach ($models as $model)
        {
            $model->queryed = true;
            $model->relation = [$model->password];
        }
    }

    #[Relation(with: false)]
    public ?array $relationNoWith = null;

    public function getRelationNoWith(): ?array
    {
        return $this->relationNoWith;
    }

    public function setRelationNoWith(?array $relationNoWith): self
    {
        $this->relationNoWith = $relationNoWith;

        return $this;
    }

    /**
     * @param self[] $models
     */
    public static function __queryRelationNoWith(array $models, Relation $annotation): void
    {
        foreach ($models as $model)
        {
            $model->noWithQueryed = true;
        }
    }
}
