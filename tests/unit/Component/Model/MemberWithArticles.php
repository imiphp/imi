<?php

declare(strict_types=1);

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Relation\JoinFrom;
use Imi\Model\Annotation\Relation\JoinTo;
use Imi\Model\Annotation\Relation\OneToMany;

/**
 * Member.
 *
 * @Inherit
 *
 * @property Article[]|null $articles
 */
class MemberWithArticles extends Member
{
    /**
     * @OneToMany("Article")
     * @JoinFrom("id")
     * @JoinTo("member_id")
     *
     * @var Article[]|null
     */
    protected $articles = null;

    /**
     * Get the value of articles.
     *
     * @return Article[]|null
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set the value of articles.
     *
     * @param Article[]|null $articles
     *
     * @return self
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;

        return $this;
    }
}
