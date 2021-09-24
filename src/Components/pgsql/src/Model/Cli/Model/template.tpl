<?php declare(strict_types=1);
echo '<?php'; ?>


declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Imi\Bean\Annotation\Inherit;
use <?php echo $namespace; ?>\Base\<?php echo $className; ?>Base;

/**
 * <?php echo $tableComment; ?>.
 *
 * @Inherit
 */
class <?php echo $className; ?> extends <?php echo $className; ?>Base
{

}
