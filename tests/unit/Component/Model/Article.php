<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Relation\AutoSave;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\OneToOne;
use Imi\Test\Component\Model\Base\ArticleBase;

/**
 * Article.
 *
 * @Inherit
 * @Entity(camel=false)
 *
 * @property ArticleEx|null $ex
 * @property ArticleEx|null $exWith
 * @property ArticleEx|null $queryRelationsList
 */
class Article extends ArticleBase
{
    /**
     * @OneToOne(model="ArticleEx")
     * @JoinFrom("id")
     * @JoinTo("article_id")
     * @AutoSave
     */
    protected ?ArticleEx $ex = null;

    /**
     * Get the value of ex.
     */
    public function getEx(): ?ArticleEx
    {
        return $this->ex;
    }

    /**
     * Set the value of ex.
     *
     * @return self
     */
    public function setEx(?ArticleEx $ex)
    {
        $this->ex = $ex;

        return $this;
    }

    /**
     * @OneToOne(model="ArticleEx", with=true)
     * @JoinFrom("id")
     * @JoinTo("article_id")
     */
    protected ?ArticleEx $exWith = null;

    /**
     * Get the value of ex.
     */
    public function getExWith(): ?ArticleEx
    {
        return $this->exWith;
    }

    /**
     * Set the value of ex.
     *
     * @return self
     */
    public function setExWith(?ArticleEx $exWith)
    {
        $this->exWith = $exWith;

        return $this;
    }

    /**
     * @OneToOne(model="ArticleEx")
     * @JoinFrom("id")
     * @JoinTo("article_id")
     * @AutoSelect(false)
     * @JsonNotNull
     *
     * @var Article|null
     */
    protected $queryRelationsList;

    /**
     * Get the value of queryRelationsList.
     *
     * @return Article|null
     */
    public function getQueryRelationsList()
    {
        return $this->queryRelationsList;
    }

    /**
     * Set the value of queryRelationsList.
     *
     * @param Article|null $queryRelationsList
     *
     * @return self
     */
    public function setQueryRelationsList($queryRelationsList)
    {
        $this->queryRelationsList = $queryRelationsList;

        return $this;
    }
}
