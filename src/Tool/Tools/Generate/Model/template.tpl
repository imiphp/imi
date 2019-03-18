<?= '<?php' ?>

namespace <?= $namespace ?>;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use <?= $namespace ?>\Base\<?= $className ?>Base;

/**
 * <?= $className ?>

 * @Entity
 * @Table(name="<?= $table['name'] ?>"<?php if(isset($table['id'][0])):?>, id={<?= '"', implode('", "', $table['id']), '"' ?>}<?php endif;?>)
 */
class <?= $className ?> extends <?= $className ?>Base
{

}
