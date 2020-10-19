<?php

namespace Imi\Test\Component\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Tree\Annotation\TreeModel;
use Imi\Model\Tree\TTreeModel;
use Imi\Test\Component\Model\Base\TreeBase;

/**
 * Tree.
 *
 * @Inherit
 * @TreeModel
 */
class Tree extends TreeBase
{
    use TTreeModel;
}
