<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Tree\Annotation\TreeModel;
use Imi\Model\Tree\TTreeModel;
use Imi\Pgsql\Test\Model\Base\TreeBase;

/**
 * tb_tree.
 *
 * @Inherit
 * @TreeModel
 */
class Tree extends TreeBase
{
    use TTreeModel;
}
