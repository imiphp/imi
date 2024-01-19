<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Model\Event\Param;

trait TGenerateModel
{
    public function __construct(
        /**
         * 命名空间.
         *
         * 例：app\Model
         */
        public string $namespace = '',

        /**
         * 基类名称.
         *
         * 例：app\Model\Base\ArticleBase
         */
        public string $baseClassName = '',

        /**
         * 类名.
         *
         * 例：Article
         */
        public string $className = '',

        /**
         * 完整类名.
         *
         * 包含命名空间
         *
         * 例：app\Model\Article
         */
        public string $fullClassName = '',

        /**
         * 表名.
         *
         * 例：article
         */
        public string $tableName = '',

        /**
         * Table 注解相关参数.
         *
         * [
         *     'name' => '表名',
         *     'id' => ['id'], // 主键列表
         *     'usePrefix' => false, // 是否使用表前缀
         * ].
         */
        public array $table = [],

        /**
         * 字段列表.
         *
         * [
         *     [
         *         'name'              => '字段名',
         *         'varName'           => '字段变量名',
         *         'type'              => '字段类型',
         *         'phpType'           => 'PHP注释字段类型',
         *         'phpDefinitionType' => 'PHP声明字段类型',
         *         'typeConvert'       => false, // 是否需要类型转换
         *         'length'            => 0, // 长度
         *         'accuracy'          => $accuracy, // 精度
         *         'nullable'          => true, // 是否允许为null
         *         'default'           => '默认值',
         *         'defaultValue'      => mixed, // PHP默认值
         *         'isPrimaryKey'      => false, // 是否为主键
         *         'primaryKeyIndex'   => -1, // 主键索引，-1为非主键
         *         'isAutoIncrement'   => false, // 是否自增
         *         'comment'           => '字段注释',
         *         'typeDefinition'    => false, // 是否声明PHP类型
         *         'ref'               => false, // 是否引用返回
         *         'unsigned'          => false, // 是否无符号
         *         'virtual'           => false, // 是否虚拟字段
         *     ]
         * ]
         */
        public array $fields = [],

        /**
         * 是否驼峰命名.
         */
        public bool $camel = false,

        /**
         * 是否使用 Bean 特性.
         */
        public bool $bean = false,

        /**
         * 是否增量更新.
         */
        public bool $incrUpdate = false,

        /**
         * 连接池名称.
         */
        public ?string $poolName = null,

        /**
         * 建表语句.
         *
         * 可能非 SQL 原文
         */
        public string $ddl = '',

        /**
         * 原始建表语句.
         */
        public string $rawDDL = '',

        /**
         * 建表语句解析回调.
         *
         * @var callable|null
         */
        public $ddlDecode = null,

        /**
         * 表注释.
         */
        public string $tableComment = '',

        /**
         * 是否检查字段长度.
         */
        public bool $lengthCheck = false,

        /**
         * 类注解代码
         *
         * @var string
         */
        public string $classAttributeCode = '',
    ) {
        parent::__construct(static::class);
    }
}
