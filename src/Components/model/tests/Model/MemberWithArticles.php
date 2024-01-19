<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

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
    protected ?iterable $articles = null;

    /**
     * Get the value of articles.
     *
     * @return Article[]|null
     */
    public function getArticles(): ?iterable
    {
        return $this->articles;
    }

    /**
     * Set the value of articles.
     *
     * @param Article[]|null $articles
     */
    public function setArticles(?iterable $articles): self
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
    protected ?iterable $articlesWith = null;

    /**
     * Get the value of articles.
     *
     * @return Article[]|null
     */
    public function getArticlesWith(): ?iterable
    {
        return $this->articlesWith;
    }

    /**
     * Set the value of articles.
     *
     * @param Article[]|null $articlesWith
     */
    public function setArticlesWith(?iterable $articlesWith): self
    {
        $this->articlesWith = $articlesWith;

        return $this;
    }
}
