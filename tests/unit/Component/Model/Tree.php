<?php
namespace Imi\Test\Component\Model;

use Imi\Model\Model;
use Imi\Model\Tree\TTreeModel;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Tree\Annotation\TreeModel;
use Imi\Test\Component\Model\Base\TreeBase;

/**
 * Tree
 * @Entity
 * @TreeModel
 * @Table(name="tb_tree", id={"id"})
 */
class Tree extends TreeBase
{
    use TTreeModel;

}
