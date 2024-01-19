<?php declare(strict_types=1);
echo '<?php'; ?>


declare(strict_types=1);

namespace <?php echo $namespace; ?>\Base;

use <?php echo $baseClassName; ?> as Model;

/**
 * <?php echo $tableComment; ?> 基类.
 *
 * 此文件是自动生成，请勿手动修改此文件！
 *
<?php foreach ($fields as $field)
{ ?>
 * @property <?php echo $field['phpType']; ?> $<?php echo $field['varName']; ?> <?php echo '' === $field['comment'] ? '' : $field['comment']; ?>

<?php } ?>
 */
<?php echo $classAttributeCode; ?>

abstract class <?php echo $className; ?>Base extends Model
{
    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEY = '<?php echo addcslashes($table['id'][0] ?? '', '\'\\'); ?>';

    /**
     * {@inheritdoc}
     */
    public const PRIMARY_KEYS = <?php echo json_encode($table['id'], \JSON_UNESCAPED_UNICODE); ?>;

<?php
    foreach ($fields as $field)
    {
        ?>
    /**
<?php if ('' === $field['comment'])
{ ?>
     * <?php echo $field['name']; ?>.
<?php }
else
{ ?>
     * <?php echo $field['comment']; ?>.
     * <?php echo $field['name']; ?>

<?php } ?>
     * @var <?php echo $field['phpType']; ?>

     */
    <?php echo $field['attributesCode']; ?>

    protected <?php if ($field['typeDefinition'] && $field['phpDefinitionType'])
    { ?><?php echo $field['phpDefinitionType']; ?> <?php } ?>$<?php echo $field['varName']; ?> = <?php var_export($field['defaultValue']); ?>;

    /**
     * 获取 <?php echo $field['varName']; ?><?php echo '' === $field['comment'] ? '' : (' - ' . $field['comment']); ?>.
     *
     * @return <?php echo $field['phpType']; ?>

     */
    public function <?php if ($field['ref'])
    {?> & <?php } ?>get<?php echo ucfirst($field['varName']); ?>()<?php if ($field['typeDefinition'] && $field['phpDefinitionType'])
    { ?>: <?php echo $field['phpDefinitionType']; ?><?php } ?>

    {
        return $this-><?php echo $field['varName']; ?>;
    }

    /**
     * 赋值 <?php echo $field['varName']; ?><?php echo '' === $field['comment'] ? '' : (' - ' . $field['comment']); ?>.
     *
     * @param <?php echo $field['phpType']; ?> $<?php echo $field['varName']; ?> <?php echo $field['name']; ?>

     * @return static
     */
    public function set<?php echo ucfirst($field['varName']); ?>(mixed $<?php echo $field['varName']; ?>): self
    {
<?php if ($lengthCheck && $length = [
    'char'       => $field['length'],
    'varchar'    => $field['length'],
    'tinyblob'   => 2 ** 8 - 1,
    'tinytext'   => 2 ** 8 - 1,
    'blob'       => 2 ** 16 - 1,
    'text'       => 2 ** 16 - 1,
    'mediumblob' => 2 ** 24 - 1,
    'mediumtext' => 2 ** 24 - 1,
    'longblob'   => 2 ** 32 - 1,
    'longtext'   => 2 ** 32 - 1,
][$field['type']] ?? null)
{ ?>
        if (is_string($<?php echo $field['varName']; ?>) && mb_strlen($<?php echo $field['varName']; ?>) > <?php echo $length; ?>)
        {
            throw new \InvalidArgumentException('The maximum length of $<?php echo $field['varName']; ?> is <?php echo $length; ?>');
        }
<?php } ?>
        $this-><?php echo $field['varName']; ?> = null === $<?php echo $field['varName']; ?> ? null : <?php echo $field['typeConvert']; ?>$<?php echo $field['varName']; ?>;
        return $this;
    }

<?php
    }
?>
}
