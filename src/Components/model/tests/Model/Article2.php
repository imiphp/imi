<?php

declare(strict_types=1);

namespace Imi\Model\Test\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Test\Model\Base\Article2Base;

/**
 * tb_article2.
 */
#[Inherit]
class Article2 extends Article2Base
{
}
