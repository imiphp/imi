<?php echo '<?php'; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Imi\Facade\BaseFacade;
use Imi\Facade\Annotation\Facade;

/**
 * <?php echo $facadeAnnotation; ?>
<?php foreach ($methods as $item)
 { ?>

 * <?php echo $item; ?>
<?php } ?>

 */
class <?php echo $shortClassName; ?> extends BaseFacade
{

}
