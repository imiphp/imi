<?php
namespace Imi\Cli\Tools\Generate\Model;

use Imi\Db\Db;
use Imi\Config;
use Imi\Util\Imi;
use Imi\Util\File;
use Imi\Util\Text;
use Imi\Cli\ArgType;
use Imi\Db\Util\SqlUtil;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\Argument;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Contract\BaseCommand;

/**
 * @Command("generate")
 */
class ModelGenerate extends BaseCommand
{
    /**
     * 生成数据库中所有表的模型文件，如果设置了`include`或`exclude`，则按照相应规则过滤表。
     * @CommandAction("model")
     *
     * @Argument(name="namespace", type=ArgType::STRING, required=true, comments="生成的Model所在命名空间")
     * @Option(name="database", type=ArgType::STRING, comments="数据库名，不传则取连接池默认配置的库名")
     * @Option(name="poolName", type=ArgType::STRING, comments="连接池名称，不传则取默认连接池")
     * @Option(name="prefix", type=ArgType::ARRAY, default={}, comments="传值则去除该表前缀，以半角逗号分隔多个前缀")
     * @Option(name="include", type=ArgType::ARRAY, default={}, comments="要包含的表名，以半角逗号分隔")
     * @Option(name="exclude", type=ArgType::ARRAY, default={}, comments="要排除的表名，以半角逗号分隔")
     * @Option(name="override", type=ArgType::STRING, default=false, comments="是否覆盖已存在的文件，请慎重！true-全覆盖;false-不覆盖;base-覆盖基类;model-覆盖模型类;默认缺省状态为false")
     * @Option(name="config", type=ArgType::STRING, default=true, comments="配置文件。true-项目配置；false-忽略配置；php配置文件名-使用该配置文件。默认为true")
     * @Option(name="basePath", type=ArgType::STRING, default=null, comments="指定命名空间对应的基准路径，可选")
     * @Option(name="entity", type=ArgType::BOOLEAN, default=true, comments="序列化时是否使用驼峰命名(true or false),默认true,可选")
     * @return void
     */
    public function generate(string $namespace, ?string $database, ?string $poolName, array $prefix, array $include, array $exclude, $override, $config, ?string $basePath, bool $entity): void
    {
        $override = (string)$override;
        switch($override)
        {
            case 'base':
                break;
            case 'model':
                break;
            default:
                $override = (bool)json_decode($override);
        }
        if(in_array($config, ['true', 'false']))
        {
            $config = (bool)json_decode($config);
        }
        if(true === $config)
        {
            $configData = Config::get('@app.tools.generate/model');
        }
        else if(is_string($config))
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
        if(null === $database)
        {
            $database = $query->execute('select database()')->getScalar();
        }
        // 表
        $list = $query->tableRaw('information_schema.TABLES')
                      ->where('TABLE_SCHEMA', '=', $database)
                      ->whereIn('TABLE_TYPE', [
                          'BASE TABLE',
                          'VIEW',
                      ])
                      ->field('TABLE_NAME', 'TABLE_TYPE', 'TABLE_COMMENT')
                      ->select()
                      ->getArray();
        // model保存路径
        if(null === $basePath)
        {
            $modelPath = Imi::getNamespacePath($namespace);
        }
        else
        {
            $modelPath = $basePath;
        }
        if(null === $modelPath)
        {
            $this->output->writeln('<error>Namespace</error> <comments>' . $namespace . '</comments> <error>cannot found</error>');
            exit;
        }
        $this->output->writeln('<info>modelPath:</info> <comments>' . $modelPath . '</comments>');
        File::createDir($modelPath);
        $baseModelPath = $modelPath . '/Base';
        File::createDir($baseModelPath);
        foreach($list as $item)
        {
            $table = $item['TABLE_NAME'];
            if(!$this->checkTable($table, $include, $exclude))
            {
                // 不符合$include和$exclude
                continue;
            }
            $className = $this->getClassName($table, $prefix);
            if(isset($configData['relation'][$table]))
            {
                $configItem = $configData['relation'][$table];
                $modelNamespace = $configItem['namespace'];
                $path = Imi::getNamespacePath($modelNamespace);
                if(null === $path)
                {
                    $this->output->writeln('<error>Namespace</error> <comments>' . $modelNamespace . '</comments> <error>cannot found</error>');
                    exit;
                }
                File::createDir($path);
                $basePath = $path . '/Base';
                File::createDir($basePath);
                $fileName = File::path($path, $className . '.php');
                $withRecords = $configItem['withRecords'] ?? false;
            }
            else
            {
                $hasResult = false;
                foreach($configData['namespace'] ?? [] as $namespaceName => $namespaceItem)
                {
                    if(in_array($table, $namespaceItem['tables'] ?? []))
                    {
                        $modelNamespace = $namespaceName;
                        $path = Imi::getNamespacePath($modelNamespace);
                        if(null === $path)
                        {
                            $this->output->writeln('<error>Namespace</error> <comments>' . $modelNamespace . '</comments> <error>cannot found</error>');
                            exit;
                        }
                        File::createDir($path);
                        $basePath = $path . '/Base';
                        File::createDir($basePath);
                        $fileName = File::path($path, $className . '.php');
                        $hasResult = true;
                        $withRecords = in_array($table, $namespaceItem['withRecords'] ?? []);
                        break;
                    }
                }
                if(!$hasResult)
                {
                    $modelNamespace = $namespace;
                    $fileName = File::path($modelPath, $className . '.php');
                    $basePath = $baseModelPath;
                    $withRecords = false;
                }
            }
            if(false === $override && is_file($fileName))
            {
                // 不覆盖
                $this->output->writeln('Skip <info>' . $table . '</info>');
                continue;
            }
            $ddl = $this->getDDL($query, $table);
            if($withRecords)
            {
                $dataList = $query->from($table)->select()->getArray();
                $ddl .= ';' . PHP_EOL . SqlUtil::buildInsertSql($table, $dataList);
            }
            $data = [
                'namespace'     => $modelNamespace,
                'className'     => $className,
                'table'         => [
                    'name'  => $table,
                    'id'    => [],
                ],
                'fields'        => [],
                'entity'        => $entity,
                'poolName'      => $poolName,
                'ddl'           => $ddl,
                'tableComment'  => '' === $item['TABLE_COMMENT'] ? $table : $item['TABLE_COMMENT'],
            ];
            $fields = $query->bindValue(':table', $table)->execute(sprintf('show full columns from `%s`.`%s`' , $database, $table))->getArray();
            $this->parseFields($fields, $data, 'VIEW' === $item['TABLE_TYPE']);

            $baseFileName = File::path($basePath, $className . 'Base.php');
            if(!is_file($baseFileName) || true === $override || 'base' === $override)
            {
                $this->output->writeln('Generating <info>' . $table . '</info> BaseClass...');
                $baseContent = $this->renderTemplate('base-template', $data);
                file_put_contents($baseFileName, $baseContent);
            }

            if(!is_file($fileName) || true === $override || 'model' === $override)
            {
                $this->output->writeln('Generating <info>' . $table . '</info> Class...');
                $content = $this->renderTemplate('template', $data);
                file_put_contents($fileName, $content);
            }
        }
        $this->output->writeln('<info>Complete</info>');
    }

    /**
     * 检查表是否生成
     * @param string $table
     * @param array $include
     * @param array $exclude
     * @return boolean
     */
    private function checkTable(string $table, array $include, array $exclude): bool
    {
        if(in_array($table, $exclude))
        {
            return false;
        }

        return !isset($include[0]) || in_array($table, $include);
    }

    /**
     * 表名转短类名
     * @param string $table
     * @param array $prefixs
     * @return string
     */
    private function getClassName(string $table, array $prefixs): string
    {
        foreach($prefixs as $prefix)
        {
            $prefixLen = strlen($prefix);
            if(substr($table, 0, $prefixLen) === $prefix)
            {
                $table = substr($table, $prefixLen);
                break;
            }
        }
        return Text::toPascalName($table);
    }

    /**
     * 处理字段信息
     * @param array $fields
     * @param array $data
     * @param boolean $isView
     * @return void
     */
    private function parseFields(array $fields, ?array &$data, bool $isView): void
    {
        $idCount = 0;
        foreach($fields as $i => $field)
        {
            $this->parseFieldType($field['Type'], $typeName, $length, $accuracy);
            if($isView && 0 === $i)
            {
                $isPk = true;
            }
            else
            {
                $isPk = 'PRI' === $field['Key'];
            }
            $data['fields'][] = [
                'name'              => $field['Field'],
                'varName'           => Text::toCamelName($field['Field']),
                'type'              => $typeName,
                'phpType'           => $this->dbFieldTypeToPhp($typeName),
                'length'            => $length,
                'accuracy'          => $accuracy,
                'nullable'          => $field['Null'] === 'YES',
                'default'           => $field['Default'],
                'isPrimaryKey'      => $isPk,
                'primaryKeyIndex'   => $isPk ? $idCount : -1,
                'isAutoIncrement'   => false !== strpos($field['Extra'], 'auto_increment'),
                'comment'           => $field['Comment'],
            ];
            if($isPk)
            {
                $data['table']['id'][] = $field['Field'];
                ++$idCount;
            }
        }
    }

    /**
     * 处理类似varchar(32)和decimal(10,2)格式的字段类型
     * @param string $text
     * @param string $typeName
     * @param int $length
     * @param int $accuracy
     * @return bool
     */
    public function parseFieldType(string $text, ?string &$typeName, ?int &$length, ?int &$accuracy): bool
    {
        if(preg_match('/([^(]+)(\((\d+)(,(\d+))?\))?/', $text, $match))
        {
            $typeName = $match[1];
            $length = (int)($match[3] ?? 0);
            if(isset($match[5]))
            {
                $accuracy = (int)$match[5];
            }
            else
            {
                $accuracy = 0;
            }
            return true;
        }
        else
        {
            $typeName = '';
            $length = 0;
            $accuracy = 0;
            return false;
        }
    }

    /**
     * 渲染模版
     * @param string $template
     * @param array $data
     * @return string
     */
    private function renderTemplate(string $template, array $data): string
    {
        extract($data);
        ob_start();
        include __DIR__ . '/' . $template . '.tpl';
        return ob_get_clean();
    }

    /**
     * 数据库字段类型转PHP的字段类型
     * @param string $type
     * @return string
     */
    private function dbFieldTypeToPhp(string $type): string
    {
        $firstType = explode(' ', $type)[0];
        static $map = [
            'int'       => 'int',
            'smallint'  => 'int',
            'tinyint'   => 'int',
            'mediumint' => 'int',
            'bigint'    => 'int',
            'bit'       => 'boolean',
            'year'      => 'int',
            'double'    => 'float',
            'float'     => 'float',
            'decimal'   => 'float',
            'json'      =>  \Imi\Util\LazyArrayObject::class,
        ];
        return $map[$firstType] ?? 'string';
    }

    /**
     * 获取创建表的 DDL
     *
     * @param \Imi\Db\Query\Interfaces\IQuery $query
     * @param string $table
     * @return string
     */
    public function getDDL(IQuery $query, string $table): string
    {
        $result = $query->execute('show create table `' . $table . '`');
        $sql = $result->get()['Create Table'] ?? '';
        $sql = preg_replace('/ AUTO_INCREMENT=\d+ /', ' ', $sql, 1);
        return $sql;
    }

}
