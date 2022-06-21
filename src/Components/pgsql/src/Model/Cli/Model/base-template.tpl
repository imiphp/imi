<?php declare(strict_types=1);
echo '<?php'; ?>


declare(strict_types=1);

namespace <?php echo $namespace; ?>\Base;

use Imi\Config\Annotation\ConfigValue;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Table;
use <?php echo $baseClassName; ?> as Model;

/**
 * <?php echo $tableComment; ?> 基类.
 *
 * <?php if (true === $entity) { ?>@Entity<?php }
else
{ ?>@Entity(false)<?php } ?>

 * @Table(name=@ConfigValue(name="@app.models.<?php echo $namespace; ?>\<?php echo $className; ?>.name", default="<?php echo $table['name']; ?>"), usePrefix=<?php var_export($table['usePrefix']); ?><?php if (isset($table['id'][0])) { ?>, id={<?php echo '"', implode('", "', $table['id']), '"'; ?>}<?php } ?>, dbPoolName=@ConfigValue(name="@app.models.<?php echo $namespace; ?>\<?php echo $className; ?>.poolName"<?php if (null !== $poolName) {?>, default="<?php echo $poolName; ?>"<?php }?>))
 *
<?php foreach ($fields as $field) { ?>
 * @property <?php echo $field['phpType']; ?> $<?php echo $field['varName']; ?> <?php echo '' === $field['comment'] ? '' : $field['comment']; ?>

<?php } ?>
 */
abstract class <?php echo $className; ?>Base extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = '<?php echo addcslashes($table['id'][0] ?? '', '\'\\'); ?>';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = <?php echo json_encode($table['id'], JSON_UNESCAPED_UNICODE); ?>;

<?php
    foreach ($fields as $field)
    {
        ?>
    /**
<?php if ('' === $field['comment']) { ?>
     * <?php echo $field['name']; ?>.
<?php }
        else
        { ?>
     * <?php echo $field['comment']; ?>.
     * <?php echo $field['name']; ?>

<?php } ?>

     * @Column(name="<?php echo $field['name']; ?>", type="<?php echo $field['type']; ?>", length=<?php echo $field['length']; ?>, accuracy=<?php echo $field['accuracy']; ?>, nullable=<?php echo json_encode($field['nullable']); ?>, default="<?php echo $field['default']; ?>", isPrimaryKey=<?php echo json_encode($field['isPrimaryKey']); ?>, primaryKeyIndex=<?php echo $field['primaryKeyIndex']; ?>, isAutoIncrement=<?php echo json_encode($field['isAutoIncrement']); ?>, ndims=<?php echo $field['ndims']; ?>)
     * @var <?php echo $field['phpType']; ?>

     */
    protected <?php if ($field['typeDefinition'] && $field['phpDefinitionType']) { ?><?php echo $field['phpDefinitionType']; ?> <?php } ?>$<?php echo $field['varName']; ?> = <?php var_export($field['defaultValue']); ?>;

    /**
     * 获取 <?php echo $field['varName']; ?><?php echo '' === $field['comment'] ? '' : (' - ' . $field['comment']); ?>.
     *
     * @return <?php echo $field['phpType']; ?>

     */
    public function <?php if ($field['ref']){?> & <?php } ?>get<?php echo ucfirst($field['varName']); ?>()<?php if ($field['typeDefinition'] && $field['phpDefinitionType']) { ?>: <?php echo $field['phpDefinitionType']; ?><?php } ?>

    {
        return $this-><?php echo $field['varName']; ?>;
    }

    /**
     * 赋值 <?php echo $field['varName']; ?><?php echo '' === $field['comment'] ? '' : (' - ' . $field['comment']); ?>.
     *
     * @param <?php echo $field['phpType']; ?> $<?php echo $field['varName']; ?> <?php echo $field['name']; ?>

     * @return static
     */
    public function set<?php echo ucfirst($field['varName']); ?>(<?php if ($field['typeDefinition'] && $field['phpDefinitionType']) { ?><?php echo $field['phpDefinitionType']; ?> <?php } ?>$<?php echo $field['varName']; ?>)
    {
<?php if ($lengthCheck && $length = [
    'character'         => $field['length'],
    'char'              => $field['length'],
    'varchar'           => $field['length'],
    'character varying' => $field['length'],
    'bit'               => $field['length'],
    'bit varying'       => $field['length'],
    'varbit'            => $field['length'],
][$field['type']] ?? null) { ?>
        if (is_string($<?php echo $field['varName']; ?>) && mb_strlen($<?php echo $field['varName']; ?>) > <?php echo $length; ?>)
        {
            throw new \InvalidArgumentException('The maximum length of $<?php echo $field['varName']; ?> is <?php echo $length; ?>');
        }
<?php } ?>
        $this-><?php echo $field['varName']; ?> = $<?php echo $field['varName']; ?>;
        return $this;
    }

<?php
    }
?>
}
