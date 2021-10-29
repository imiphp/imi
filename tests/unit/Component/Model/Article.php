<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Relation\AutoSave;
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
}
