<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 关联关系注解基类.
 *
 * @property bool          $with       关联预加载查询
 * @property string[]|null $withFields 设置结果模型的序列化字段
 * @property int|string    $poolName   连接池名称
 */
abstract class RelationBase extends Base
{
}
