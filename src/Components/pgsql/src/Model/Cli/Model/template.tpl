<?= '<?php'; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Imi\Bean\Annotation\Inherit;
use <?= $namespace; ?>\Base\<?= $className; ?>Base;

/**
 * <?= $tableComment; ?>

 * @Inherit
 */
class <?= $className; ?> extends <?= $className; ?>Base
{

}
