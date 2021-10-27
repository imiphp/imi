<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model\Cli\Model;

use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Config;
use Imi\Db\Db;
use Imi\Model\Model;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Text;

/**
 * @Command("generate")
 */
class ModelGenerate extends BaseCommand
{
    /**
     * Postgresql 模型生成.
     *
     * 如果设置了`include`或`exclude`，则按照相应规则过滤表。
     *
     * @CommandAction("pgModel")
     *
     * @Argument(name="namespace", type=ArgType::STRING, required=true, comments="生成的Model所在命名空间")
     * @Argument(name="baseClass", type=ArgType::STRING, default="Imi\Pgsql\Model\PgModel", comments="生成的Model所继承的基类,默认\Imi\Model\Model,可选")
     * @Option(name="database", type=ArgType::STRING, comments="数据库名，不传则取连接池默认配置的库名")
     * @Option(name="poolName", type=ArgType::STRING, comments="连接池名称，不传则取默认连接池")
     * @Option(name="prefix", type=ArgType::ARRAY, default={}, comments="传值则去除该表前缀，以半角逗号分隔多个前缀")
     * @Option(name="include", type=ArgType::ARRAY, default={}, comments="要包含的表名，以半角逗号分隔")
     * @Option(name="exclude", type=ArgType::ARRAY, default={}, comments="要排除的表名，以半角逗号分隔")
     * @Option(name="override", type=ArgType::STRING, default=false, comments="是否覆盖已存在的文件，请慎重！true-全覆盖;false-不覆盖;base-覆盖基类;model-覆盖模型类;默认缺省状态为false")
     * @Option(name="config", type=ArgType::STRING, default=true, comments="配置文件。true-项目配置；false-忽略配置；php配置文件名-使用该配置文件。默认为true")
     * @Option(name="basePath", type=ArgType::STRING, default=null, comments="指定命名空间对应的基准路径，可选")
     * @Option(name="entity", type=ArgType::BOOLEAN, default=true, comments="序列化时是否使用驼峰命名(true or false),默认true,可选")
     * @Option(name="lengthCheck", type=ArgType::BOOLEAN, default=false, comments="是否检查字符串字段长度,可选")
     *
     * @param string|bool $override
     * @param string|bool $config
     */
    public function generate(string $namespace, string $baseClass, ?string $database, ?string $poolName, array $prefix, array $include, array $exclude, $override, $config, ?string $basePath, bool $entity, bool $lengthCheck): void
    {
        $override = (string) $override;
        switch ($override)
        {
            case 'base':
                break;
            case 'model':
                break;
            default:
                $override = (bool) json_decode($override);
        }
        if (\in_array($config, ['true', 'false'], true))
        {
            $config = (bool) json_decode($config);
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
        $db = Db::getInstance($poolName);
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
            $modelPath = Imi::getNamespacePath($namespace);
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
            if (isset($configData['relation'][$table]))
            {
                $configItem = $configData['relation'][$table];
                $modelNamespace = $configItem['namespace'] ?? $namespace;
                $path = Imi::getNamespacePath($modelNamespace);
                if (null === $path)
                {
                    $this->output->writeln('<error>Namespace</error> <comment>' . $modelNamespace . '</comment> <error>cannot found</error>');
                    exit(255);
                }
                File::createDir($path);
                $basePath = $path . '/Base';
                File::createDir($basePath);
                $fileName = File::path($path, $className . '.php');
            }
            else
            {
                $hasResult = false;
                $fileName = '';
                $modelNamespace = '';
                foreach ($configData['namespace'] ?? [] as $namespaceName => $namespaceItem)
                {
                    if (\in_array($table, $namespaceItem['tables'] ?? []))
                    {
                        $modelNamespace = $namespaceName;
                        $path = Imi::getNamespacePath($modelNamespace);
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
            }
            if (false === $override && is_file($fileName))
            {
                // 不覆盖
                $this->output->writeln('Skip <info>' . $table . '</info>');
                continue;
            }
            $data = [
                'namespace'     => $modelNamespace,
                'baseClassName' => $baseClass,
                'className'     => $className,
                'table'         => [
                    'name'  => $table,
                    'id'    => [],
                ],
                'fields'        => [],
                'entity'        => $entity,
                'poolName'      => $poolName,
                'tableComment'  => Text::isEmpty($item['comment']) ? $table : $item['comment'],
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
            $this->parseFields($fields, $data, 'v' === $item['relkind'], $table, $configData);

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
            $prefixLen = \strlen($prefix);
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
    private function parseFields(array $fields, ?array &$data, bool $isView, string $table, ?array $config): void
    {
        $idCount = 0;
        foreach ($fields as $i => $field)
        {
            if ($field['atttypmod'] > -1)
            {
                $length = (($field['atttypmod'] - 4) >> 16) & 65535;
                $accuracy = ($field['atttypmod'] - 4) & 65535;
            }
            else
            {
                $length = -1;
                $accuracy = 0;
            }

            $isPk = $field['ordinal_position'] > 0;
            [$phpType, $phpDefinitionType] = $this->dbFieldTypeToPhp($field);
            if (!empty($phpDefinitionType))
            {
                $phpDefinitionType = '?' . $phpDefinitionType;
            }
            $data['fields'][] = [
                'name'              => $field['attname'],
                'varName'           => Text::toCamelName($field['attname']),
                'type'              => $type = ('_' === $field['typname'][0] ? substr($field['typname'], 1) : $field['typname']),
                'ndims'             => $field['attndims'],
                'phpType'           => $phpType . '|null',
                'phpDefinitionType' => $phpDefinitionType,
                'length'            => $length,
                'accuracy'          => $accuracy,
                'nullable'          => 'f' === $field['attnotnull'],
                'default'           => $field['adsrc'],
                'defaultValue'      => $this->parseFieldDefaultValue($type, $field['adsrc']),
                'isPrimaryKey'      => $isPk,
                'primaryKeyIndex'   => $field['ordinal_position'] ?? -1,
                'isAutoIncrement'   => '' !== $field['attidentity'],
                'comment'           => $field['description'] ?? '',
                'typeDefinition'    => $config['relation'][$table]['fields'][$field['attname']]['typeDefinition'] ?? true,
                'ref'               => \in_array($type, ['json', 'jsonb']),
            ];
            if ($isPk)
            {
                $data['table']['id'][] = $field['attname'];
                ++$idCount;
            }
        }
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

    /**
     * 数据库字段类型转PHP的字段类型.
     *
     * 返回格式：[显示类型，定义类型]
     */
    private function dbFieldTypeToPhp(array $field): array
    {
        $isArray = $field['typelem'] > 0;
        if ($isArray)
        {
            $type = substr($field['typname'], 1);
        }
        else
        {
            $type = $field['typname'];
        }
        static $map = [
            'int'         => ['int', 'int'],
            'int2'        => ['int', 'int'],
            'int4'        => ['int', 'int'],
            'int8'        => ['int', 'int'],
            'integer'     => ['int', 'int'],
            'smallint'    => ['int', 'int'],
            'bigint'      => ['int', 'int'],
            'smallserial' => ['int', 'int'],
            'serial'      => ['int', 'int'],
            'bigserial'   => ['int', 'int'],
            'serial2'     => ['int', 'int'],
            'serial4'     => ['int', 'int'],
            'serial8'     => ['int', 'int'],
            'bool'        => ['bool', 'bool'],
            'boolean'     => ['bool', 'bool'],
            'double'      => ['float', 'float'],
            'float4'      => ['float', 'float'],
            'float8'      => ['float', 'float'],
            'json'        => ['\\' . \Imi\Util\LazyArrayObject::class . '|array', ''],
            'jsonb'       => ['\\' . \Imi\Util\LazyArrayObject::class . '|array', ''],
        ];

        $result = $map[$type] ?? ['string', 'string'];
        if ($isArray)
        {
            $result[0] .= '[]';
            $result[1] = 'array';
        }

        return $result;
    }

    /**
     * 处理字段默认值
     *
     * @param mixed $default
     *
     * @return mixed
     */
    private function parseFieldDefaultValue(string $type, $default)
    {
        if (null === $default)
        {
            return null;
        }
        switch ($type)
        {
            case 'int':
            case 'int2':
            case 'int4':
            case 'int8':
            case 'smallint':
            case 'bigint':
            case 'smallserial':
            case 'serial':
            case 'bigserial':
            case 'serial2':
            case 'serial4':
            case 'serial8':
                return (int) $default;
            case 'bool':
            case 'boolean':
                return (bool) $default;
            case 'double':
            case 'float4':
            case 'float8':
                return (float) $default;
            case 'varchar':
            case 'char':
            case 'text':
                return (string) $default;
            default:
                return null;
        }
    }
}
