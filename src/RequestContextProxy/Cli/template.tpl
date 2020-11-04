<?='<?php'; ?>

namespace <?= $namespace; ?>;

use Imi\RequestContextProxy\BaseRequestContextProxy;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;

/**
 * <?=$requestContextProxyAnnotation; ?>
<?php foreach ($methods as $item):?>

 * <?=$item; ?>
<?php endforeach; ?>

 */
class <?= $shortClassName; ?> extends BaseRequestContextProxy
{

}
