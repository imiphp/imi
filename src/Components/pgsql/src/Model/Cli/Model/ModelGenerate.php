<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model\Cli\Model;

use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Contract\BaseCommand;
use Imi\Config;
use Imi\Db\Db;
use Imi\Model\Model;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Text;

#[Command(name: 'generate')]
class ModelGenerate extends BaseCommand
{
    /**
     * Postgresql 模型生成.
     * 如果设置了`include`或`exclude`，则按照相应规则过滤表。
     */
    #[CommandAction(name: 'pgModel')]
    #[Argument(name: 'namespace', type: \Imi\Cli\ArgType::STRING, required: true, comments: '生成的Model所在命名空间')]
    #[Argument(name: 'baseClass', type: \Imi\Cli\ArgType::STRING, default: \Imi\Pgsql\Model\PgModel::class, comments: '生成的Model所继承的基类,默认\\Imi\\Model\\Model,可选')]
    #[Option(name: 'database', type: \Imi\Cli\ArgType::STRING, comments: '数据库名，不传则取连接池默认配置的库名')]
    #[Option(name: 'poolName', type: \Imi\Cli\ArgType::STRING, comments: '连接池名称，不传则取默认连接池')]
    #[Option(name: 'prefix', type: \Imi\Cli\ArgType::ARRAY, default: [], comments: '传值则去除该表前缀，以半角逗号分隔多个前缀')]
    #[Option(name: 'include', type: \Imi\Cli\ArgType::ARRAY, default: [], comments: '要包含的表名，以半角逗号分隔')]
    #[Option(name: 'exclude', type: \Imi\Cli\ArgType::ARRAY, default: [], comments: '要排除的表名，以半角逗号分隔')]
    #[Option(name: 'override', type: \Imi\Cli\ArgType::STRING, default: false, comments: '是否覆盖已存在的文件，请慎重！true-全覆盖;false-不覆盖;base-覆盖基类;model-覆盖模型类;默认缺省状态为false')]
    #[Option(name: 'config', type: \Imi\Cli\ArgType::STRING, default: true, comments: '配置文件。true-项目配置；false-忽略配置；php配置文件名-使用该配置文件。默认为true')]
    #[Option(name: 'basePath', type: \Imi\Cli\ArgType::STRING, comments: '指定命名空间对应的基准路径，可选')]
    #[Option(name: 'entity', type: \Imi\Cli\ArgType::BOOLEAN, default: true, comments: '序列化时是否使用驼峰命名(true or false),默认true,可选')]
    #[Option(name: 'lengthCheck', type: \Imi\Cli\ArgType::BOOLEAN, default: false, comments: '是否检查字符串字段长度,可选')]
    #[Option(name: 'bean', type: \Imi\Cli\ArgType::BOOLEAN, comments: '模型对象是否作为 bean 类使用', default: true)]
    #[Option(name: 'incrUpdate', type: \Imi\Cli\ArgType::BOOLEAN, comments: '模型是否启用增量更新', default: false)]
    public function generate(string $namespace, string $baseClass, ?string $database, ?string $poolName, array $prefix, array $include, array $exclude, string|bool $override, string|bool $config, ?string $basePath, bool $entity, bool $lengthCheck, bool $bean, bool $incrUpdate): void
    {
        $db = Db::getInstance($poolName);
        $tablePrefix = $db->getConfig()->prefix;
        if ('' !== $tablePrefix && !\in_array($tablePrefix, $prefix))
        {
            $prefix[] = $tablePrefix;
        }
        $override = (string) $override;
        switch ($override)
        {
            case 'base':
                break;
            case 'model':
                break;
            default:
                $override = (bool) json_decode($override, false);
        }
        if (\in_array($config, ['true', 'false'], true))
        {
            $config = (bool) json_decode($config, false);
        }
        if (true === $config)
        {
            $configData = Config::get('@app.tools.generate/model');
        }
        elseif (\is_string($config))
        {
            $configData = include $config;
            $configData = $configData['tools']['generate/model'];
        }
        else
        {
            $configData = null;
        }
        $query = Db::query($poolName);
        // 数据库
        if (null === $database)
        {
            $database = $query->execute('select current_database()')->getScalar();
        }
        // 表
        $list = $db->query(<<<'SQL'
        SELECT A.oid,
            A.relname AS "name",
            b.description AS "comment",
            A.relkind
        FROM
            pg_class A
        LEFT OUTER JOIN pg_description b ON b.objsubid = 0
            AND A.oid = b.objoid
        WHERE
            A.relnamespace = ( SELECT oid FROM pg_namespace WHERE nspname = 'public' ) AND A.relkind IN ( 'r', 'v' )
        SQL)->fetchAll();
        // model保存路径
        if (null === $basePath)
        {
            $modelPath = Imi::getNamespacePath($namespace, true);
        }
        else
        {
            $modelPath = $basePath;
        }
        if (null === $modelPath)
        {
            $this->output->writeln('<error>Namespace</error> <comment>' . $namespace . '</comment> <error>cannot found</error>');
            exit(255);
        }
        $this->output->writeln('<info>modelPath:</info> <comment>' . $modelPath . '</comment>');
        if (empty($baseClass) || !class_exists($baseClass))
        {
            echo 'BaseClass ', $baseClass, ' cannot found', \PHP_EOL;

            return;
        }
        if (Model::class !== $baseClass && !is_subclass_of($baseClass, Model::class))
        {
            echo 'BaseClass ', $baseClass, ' not extends ', Model::class, \PHP_EOL;

            return;
        }
        $this->output->writeln('<info>baseClass:</info> <comment>' . $baseClass . '</comment>');
        File::createDir($modelPath);
        $baseModelPath = $modelPath . '/Base';
        File::createDir($baseModelPath);
        foreach ($list as $item)
        {
            $table = $item['name'];
            if (!$this->checkTable($table, $include, $exclude))
            {
                // 不符合$include和$exclude
                continue;
            }
            $className = $this->getClassName($table, $prefix);
            $hasResult = false;
            $fileName = '';
            $modelNamespace = '';
            $tableConfig = null;
            foreach ($configData['namespace'] ?? [] as $namespaceName => $namespaceItem)
            {
                if (($tableConfig = ($namespaceItem['tables'][$table] ?? null)) || \in_array($table, $namespaceItem['tables'] ?? []))
                {
                    $modelNamespace = $namespaceName;
                    $path = Imi::getNamespacePath($modelNamespace, true);
                    if (null === $path)
                    {
                        $this->output->writeln('<error>Namespace</error> <comment>' . $modelNamespace . '</comment> <error>cannot found</error>');
                        exit(255);
                    }
                    File::createDir($path);
                    $basePath = $path . '/Base';
                    File::createDir($basePath);
                    $fileName = File::path($path, $className . '.php');
                    $hasResult = true;
                    break;
                }
            }
            if (!$hasResult)
            {
                $modelNamespace = $namespace;
                $fileName = File::path($modelPath, $className . '.php');
                $basePath = $baseModelPath;
            }
            if (false === $override && is_file($fileName))
            {
                // 不覆盖
                $this->output->writeln('Skip <info>' . $table . '</info>');
                continue;
            }
            if ($usePrefix = ('' !== $tablePrefix && str_starts_with((string) $table, (string) $tablePrefix)))
            {
                $tableName = Text::ltrimText($table, $tablePrefix);
            }
            else
            {
                $tableName = $table;
            }
            $tableComment = Text::isEmpty($item['comment']) ? $tableName : $item['comment'];
            if ('@' === ($tableComment[0] ?? ''))
            {
                $tableComment = '@' . $tableComment;
            }
            $data = [
                'namespace'     => $modelNamespace,
                'baseClassName' => $baseClass,
                'className'     => $className,
                'table'         => [
                    'name'      => $tableName,
                    'id'        => [],
                    'usePrefix' => $usePrefix,
                ],
                'fields'        => [],
                'entity'        => $entity,
                'bean'          => $tableConfig['bean'] ?? $bean,
                'incrUpdate'    => $tableConfig['incrUpdate'] ?? $incrUpdate,
                'poolName'      => $poolName,
                'tableComment'  => $tableComment,
                'lengthCheck'   => $lengthCheck,
            ];
            $fields = $query->execute(<<<SQL
            select *, pg_get_expr ( adbin, adrelid ) as adsrc, array_position(conkey, pg_attribute.attnum) AS ordinal_position
            from pg_attribute
            join pg_type on pg_type.oid = pg_attribute.atttypid
            left join pg_description on pg_attribute.attrelid = pg_description.objoid AND pg_attribute.attnum = pg_description.objsubid
            left join pg_attrdef on pg_attrdef.adrelid = pg_attribute.attrelid and pg_attrdef.adnum = pg_attribute.attnum
            left join pg_constraint on pg_constraint.conrelid = pg_attribute.attrelid and contype = 'p'
            where attnum > 0 and pg_attribute.attrelid = (select oid from pg_class where relname = '{$table}' limit 1)
            order by attnum
            ;
            SQL)->getArray();
            $typeDefinitions = [];
            foreach ($fields as $field)
            {
                $typeDefinitions[$field['attname']] = ($tableConfig['fields'][$field['attname']]['typeDefinition'] ?? null) ?? true;
            }
            $this->parseFields($poolName, $fields, $data, 'v' === $item['relkind'], $table, $typeDefinitions);

            $data['classAttributeCode'] = \Imi\Bean\Util\AttributeUtil::generateAttributesCode([
                new \Imi\Model\Annotation\Entity(camel: $data['entity'], bean: $data['bean'], incrUpdate: $data['incrUpdate']),
                new \Imi\Model\Annotation\Table(name: $data['table']['name'], usePrefix: $data['table']['usePrefix'], id: $data['table']['id'], dbPoolName: $data['poolName']),
            ]);

            $baseFileName = File::path($basePath, $className . 'Base.php');
            if (!is_file($baseFileName) || true === $override || 'base' === $override)
            {
                $this->output->writeln('Generating <info>' . $table . '</info> BaseClass...');
                $baseContent = $this->renderTemplate('base-template', $data);
                File::putContents($baseFileName, $baseContent);
            }

            if (!is_file($fileName) || true === $override || 'model' === $override)
            {
                $this->output->writeln('Generating <info>' . $table . '</info> Class...');
                $content = $this->renderTemplate('template', $data);
                File::putContents($fileName, $content);
            }
        }
        $this->output->writeln('<info>Complete</info>');
    }

    /**
     * 检查表是否生成.
     */
    private function checkTable(string $table, array $include, array $exclude): bool
    {
        if (\in_array($table, $exclude))
        {
            return false;
        }

        return !isset($include[0]) || \in_array($table, $include);
    }

    /**
     * 表名转短类名.
     */
    private function getClassName(string $table, array $prefixs): string
    {
        foreach ($prefixs as $prefix)
        {
            $prefixLen = \strlen((string) $prefix);
            if (substr($table, 0, $prefixLen) === $prefix)
            {
                $table = substr($table, $prefixLen);
                break;
            }
        }

        return Text::toPascalName($table);
    }

    /**
     * 处理字段信息.
     */
    private function parseFields(?string $poolName, array $fields, ?array &$data, bool $isView, string $table, ?array $typeDefinitions): void
    {
        foreach ($fields as $field)
        {
            $atttypmod = $field['atttypmod'];
            if ($atttypmod > -1)
            {
                if (-1 === $field['attlen'])
                {
                    $atttypmod -= 4;
                }
                $length = ($atttypmod >> 16) & 65535;
                $accuracy = $atttypmod & 65535;
                if (0 === $length)
                {
                    $length = $accuracy;
                    $accuracy = 0;
                }
            }
            else
            {
                $length = -1;
                $accuracy = 0;
            }

            $isPk = $field['ordinal_position'] > 0;
            [$phpType, $phpDefinitionType, $typeConvert] = $this->dbFieldTypeToPhp($field);
            $fieldData = [
                'name'              => $field['attname'],
                'varName'           => Text::toCamelName($field['attname']),
                'type'              => $type = ('_' === $field['typname'][0] ? substr((string) $field['typname'], 1) : $field['typname']),
                'ndims'             => $field['attndims'],
                'phpType'           => $phpType . '|null',
                'phpDefinitionType' => $phpDefinitionType,
                'typeConvert'       => $typeConvert,
                'length'            => $length,
                'accuracy'          => $accuracy,
                'nullable'          => 'f' === $field['attnotnull'],
                'default'           => $field['adsrc'],
                'defaultValue'      => $this->parseFieldDefaultValue($poolName, $type, $field['adsrc']),
                'isPrimaryKey'      => $isPk,
                'primaryKeyIndex'   => $primaryKeyIndex = ($field['ordinal_position'] ?? 0) - 1,
                'isAutoIncrement'   => '' !== $field['attidentity'],
                'comment'           => $field['description'] ?? '',
                'typeDefinition'    => $typeDefinitions[$field['attname']],
                'ref'               => \in_array($type, ['json', 'jsonb']),
                'virtual'           => 's' === $field['attgenerated'],
            ];
            $fieldData['attributesCode'] = \Imi\Bean\Util\AttributeUtil::generateAttributesCode([
                new \Imi\Model\Annotation\Column(name: $fieldData['name'], type: $fieldData['type'], length: $fieldData['length'], accuracy: $fieldData['accuracy'], nullable: $fieldData['nullable'], default: $fieldData['default'], isPrimaryKey: $fieldData['isPrimaryKey'], primaryKeyIndex: $fieldData['primaryKeyIndex'], isAutoIncrement: $fieldData['isAutoIncrement'], ndims: $fieldData['ndims'], virtual: $fieldData['virtual']),
            ]);
            $data['fields'][] = $fieldData;
            if ($isPk)
            {
                $data['table']['id'][$primaryKeyIndex] = $field['attname'];
            }
        }
        ksort($data['table']['id']);
        $data['table']['id'] = array_values($data['table']['id']);
    }

    /**
     * 渲染模版.
     */
    private function renderTemplate(string $template, array $data): string
    {
        extract($data);
        ob_start();
        include __DIR__ . '/' . $template . '.tpl';

        return ob_get_clean();
    }

    public const DB_FIELD_TYPE_MAP = [
        'int'         => ['int', '?int', '(int)'],
        'int2'        => ['int', '?int', '(int)'],
        'int4'        => ['int', '?int', '(int)'],
        'int8'        => ['int', '?int', '(int)'],
        'integer'     => ['int', '?int', '(int)'],
        'smallint'    => ['int', '?int', '(int)'],
        'bigint'      => ['int', '?int', '(int)'],
        'smallserial' => ['int', '?int', '(int)'],
        'serial'      => ['int', '?int', '(int)'],
        'bigserial'   => ['int', '?int', '(int)'],
        'serial2'     => ['int', '?int', '(int)'],
        'serial4'     => ['int', '?int', '(int)'],
        'serial8'     => ['int', '?int', '(int)'],
        'bool'        => ['bool', '?bool', '(bool)'],
        'boolean'     => ['bool', '?bool', '(bool)'],
        'double'      => ['float', '?float', '(float)'],
        'float4'      => ['float', '?float', '(float)'],
        'float8'      => ['float', '?float', '(float)'],
        'numeric'     => ['string|float|int', 'string|float|int|null', ''],
        'json'        => ['\\' . \Imi\Util\LazyArrayObject::class . '|array', '', ''],
        'jsonb'       => ['\\' . \Imi\Util\LazyArrayObject::class . '|array', '', ''],
    ];

    /**
     * 数据库字段类型转PHP的字段类型.
     * 返回格式：[显示类型，定义类型].
     */
    private function dbFieldTypeToPhp(array $field): array
    {
        $isArray = $field['typelem'] > 0;
        if ($isArray)
        {
            $type = substr((string) $field['typname'], 1);
        }
        else
        {
            $type = $field['typname'];
        }

        $result = self::DB_FIELD_TYPE_MAP[$type] ?? ['string', '?string', ''];
        if ($isArray)
        {
            $count = $field['attndims'];
            $result = [
                str_repeat('array<', $count) . $result[0] . str_repeat('>', $count),
                '?array',
                '',
            ];
        }

        return $result;
    }

    /**
     * 处理字段默认值
     */
    private function parseFieldDefaultValue(?string $poolName, string $type, mixed $default): mixed
    {
        if (null === $default)
        {
            return null;
        }
        try
        {
            $result = Db::query($poolName)->execute('select ' . $default);
            $resultAfterExec = $result->getScalar();
        }
        catch (\Throwable)
        {
            $resultAfterExec = $default;
        }

        return match ($type)
        {
            'int', 'int2', 'int4', 'int8', 'smallint', 'bigint', 'smallserial', 'serial', 'bigserial', 'serial2', 'serial4', 'serial8' => (int) $resultAfterExec,
            'bool', 'boolean' => (bool) $resultAfterExec,
            'double', 'float4', 'float8' => (float) $resultAfterExec,
            'varchar', 'char', 'text' => (string) $resultAfterExec,
            default => null,
        };
    }
}
