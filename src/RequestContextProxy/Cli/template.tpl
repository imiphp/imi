<?php declare(strict_types=1);
echo '<?php'; ?>

namespace <?php echo $namespace; ?>;

use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
<?php foreach ($methods as $item)
{ ?>
 * <?php echo $item; ?>

<?php } ?>
 */
<?php echo $classAttributesCode; ?>

class <?php echo $shortClassName; ?> extends BaseRequestContextProxy<?php if ($interface)
{ ?> implements \<?php echo $interface; ?><?php }?>

{
<?php echo $methodCodes; ?>
}
