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
 * @property Article[]|null $articles
 * @property Article[]|null $articlesWith
 */
#[Inherit]
class MemberWithArticles extends Member
{
    /**
     * @var Article[]|null
     */
    #[OneToMany(model: 'Article')]
    #[JoinFrom(field: 'id')]
    #[JoinTo(field: 'member_id')]
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
    public function setArticles(?array $articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * @var Article[]|null
     */
    #[OneToMany(model: 'Article', with: true)]
    #[JoinFrom(field: 'id')]
    #[JoinTo(field: 'member_id')]
    protected $articlesWith = null;

    /**
     * Get the value of articles.
     *
     * @return Article[]|null
     */
    public function getArticlesWith()
    {
        return $this->articlesWith;
    }

    /**
     * Set the value of articles.
     *
     * @param Article[]|null $articlesWith
     *
     * @return self
     */
    public function setArticlesWith(?array $articlesWith)
    {
        $this->articlesWith = $articlesWith;

        return $this;
    }
}
