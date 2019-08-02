<?php
namespace Imi\Test\Component\Model;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Test\Component\Model\Base\ArticleBase;

/**
 * Article
 * @Entity
 * @Table(name="tb_article", id={"id"})
 */
class Article extends ArticleBase
{

}
