<?='<?php'; ?>

namespace <?= $namespace; ?>;

use Imi\Facade\BaseFacade;
use Imi\Facade\Annotation\Facade;

/**
 * <?=$facadeAnnotation; ?>
<?php foreach ($methods as $item):?>

 * <?=$item; ?>
<?php endforeach; ?>

 */
class <?= $shortClassName; ?> extends BaseFacade
{

}
