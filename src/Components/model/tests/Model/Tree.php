<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Test\Model\Base\TreeBase;
use Imi\Model\Tree\Annotation\TreeModel;
use Imi\Model\Tree\TTreeModel;

/**
 * Tree.
 */
#[Inherit]
#[TreeModel]
class Tree extends TreeBase
{
    use TTreeModel;
}
