<?= '<?php' ?>

namespace <?= $namespace ?>;

use Imi\Model\Model;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * <?= $className ?>

 * @Entity
 * @Table(name="<?= $table['name'] ?>"<?php if(isset($table['id'][0])):?>, id={<?= '"', implode('", "', $table['id']), '"' ?>}<?php endif;?>)
 */
class <?= $className ?> extends Model
{
<?php
	foreach($fields as $field):
?>
	/**
	 * <?= $field['name'] ?>

	 * @Column(name="<?= $field['name'] ?>", type="<?= $field['type'] ?>", length=<?= $field['length'] ?>, accuracy=<?= $field['accuracy'] ?>, nullable=<?= json_encode($field['nullable']) ?>, default="<?= $field['default'] ?>", isPrimaryKey=<?= json_encode($field['isPrimaryKey']) ?>, primaryKeyIndex=<?= $field['primaryKeyIndex'] ?>, isAutoIncrement=<?= json_encode($field['isAutoIncrement']) ?>)
	 * @var <?= $field['phpType'] ?>

	 */
	protected $<?= $field['varName'] ?>;

	/**
	 * 获取 <?= $field['varName'] ?>

	 *
	 * @return <?= $field['phpType'] ?>

	 */ 
	public function get<?= ucfirst($field['varName']) ?>()
	{
		return $this-><?= $field['varName'] ?>;
	}

	/**
	 * 赋值 <?= $field['varName'] ?>

	 * @param <?= $field['phpType'] ?> $<?= $field['varName'] ?> <?= $field['name'] ?>

	 * @return static
	 */ 
	public function set<?= ucfirst($field['varName']) ?>($<?= $field['varName'] ?>)
	{
		$this-><?= $field['varName'] ?> = $<?= $field['varName'] ?>;
		return $this;
	}

<?php
	endforeach;
?>
}