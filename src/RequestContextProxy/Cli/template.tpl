<?php echo '<?php'; ?>

namespace <?php echo $namespace; ?>;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContextProxy\BaseRequestContextProxy;
use Imi\RequestContextProxy\Annotation\RequestContextProxy;

/**
<?php if ($beanAnnotation) { ?>
 * <?php echo $beanAnnotation; ?>

<?php }?>
 * <?php echo $requestContextProxyAnnotation; ?>

<?php foreach ($methods as $item) { ?>

 * <?php echo $item; ?>
<?php } ?>

 */
class <?php echo $shortClassName; ?> extends BaseRequestContextProxy<?php if ($interface) { ?> implements \<?php echo $interface; ?><?php }?>

{

}
