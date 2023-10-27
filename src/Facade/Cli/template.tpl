<?php declare(strict_types=1);
echo '<?php'; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Imi\Facade\BaseFacade;

/**
<?php foreach ($methods as $item)
{ ?>

 * <?php echo $item; ?>
<?php } ?>

 */
<?php echo $facadeAttribute; ?>

class <?php echo $shortClassName; ?> extends BaseFacade
{

}
