<?php

declare(strict_types=1);

namespace Imi\Cli\Tools\Generate\Table;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Scanner;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\ArgType;
use Imi\Cli\Contract\BaseCommand;
use Imi\Db\Db;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Table;
use Imi\Util\ClassObject;

/**
 * @Command("generate")
 */
class TableGenerate extends BaseCommand
{
    /**
     * 根据模型中 DDL 注解定义，生成表.
     *
     * 注意，本工具是删除重建表，会丢失数据，生产环境慎重使用！
     *
     * @CommandAction("table")
     *
     * @Option(name="namespace", type=ArgType::STRING, default=null, comments="模型所在命名空间，支持半角逗号分隔传多个，默认不传则为全部")
     * @Option(name="database", type=ArgType::STRING, comments="数据库名，不传则取连接池默认配置的库名")
     * @Option(name="poolName", type=ArgType::STRING, comments="连接池名称，不传则取默认连接池")
     * @Option(name="include", type=ArgType::ARRAY, default={}, comments="要包含的表名，以半角逗号分隔")
     * @Option(name="exclude", type=ArgType::ARRAY, default={}, comments="要排除的表名，以半角逗号分隔")
     * @Option(name="override", type=ArgType::STRING, default=false, comments="是否覆盖已存在的表，请慎重！true-全覆盖;false-不覆盖;默认缺省状态为false")
     *
     * @return void
     */
    public function generate(?string $namespace, ?string $database, ?string $poolName, array $include, array $exclude, $override): void
    {
        Scanner::scanVendor();
        Scanner::scanApp();
        $override = (bool) json_decode((string) $override);
        $query = Db::query($poolName);
        // 数据库
        if (null === $database)
        {
            $database = $query->execute('select database()')->getScalar();
        }
        if (null !== $namespace)
        {
            $namespace = explode(',', $namespace);
        }
        $tables = [];
        foreach (AnnotationManager::getAnnotationPoints(DDL::class, 'class') as $point)
        {
            $class = $point->getClass();
            if (!(null === $namespace || $this->checkNamespace($namespace, $class)))
            {
                continue;
            }
            /** @var \Imi\Model\Annotation\Table $tableAnnotation */
            $tableAnnotation = AnnotationManager::getClassAnnotations($class, Table::class)[0] ?? null;
            if (!$tableAnnotation)
            {
                continue;
            }
            $table = $tableAnnotation->name;
            if (\in_array($table, $tables))
            {
                continue;
            }
            if (!$this->checkTable($table, $include, $exclude))
            {
                // 不符合$include和$exclude
                continue;
            }
            if ($override)
            {
                // 尝试删除表
                $query->execute('DROP TABLE IF EXISTS `' . $table . '`');
            }
            else
            {
                if (1 == $query->tableRaw('information_schema.TABLES')
                ->where('TABLE_SCHEMA', '=', $database)
                ->where('TABLE_NAME', '=', $table)
                ->fieldRaw('1')
                ->limit(1)
                ->select()
                ->getScalar())
                {
                    $tables[] = $table;
                    // 表存在跳过
                    $this->output->writeln('Skip ' . $table);
                    continue;
                }
            }
            /** @var \Imi\Model\Annotation\DDL $ddlAnnotation */
            $ddlAnnotation = $point->getAnnotation();
            // 创建表
            Db::getInstance()->batchExec($ddlAnnotation->sql . ';');
            $tables[] = $table;
            $this->output->writeln('Create <info>' . $table . '</info>');
        }
    }

    /**
     * 检查命名空间.
     *
     * @param array  $namespace
     * @param string $class
     *
     * @return bool
     */
    public function checkNamespace(array $namespace, string $class): bool
    {
        foreach ($namespace as $ns)
        {
            if (ClassObject::inNamespace($ns, $class))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查表是否允许创建.
     *
     * @param string $table
     * @param array  $include
     * @param array  $exclude
     *
     * @return bool
     */
    private function checkTable(string $table, array $include, array $exclude): bool
    {
        if (\in_array($table, $exclude))
        {
            return false;
        }

        return !isset($include[0]) || \in_array($table, $include);
    }
}
