<?php
namespace Imi\Tool\Tools\Generate\Model;

use Imi\App;
use Imi\Db\Db;
use Imi\Config;
use Imi\Util\File;
use Imi\Main\Helper;
use Imi\Tool\ArgType;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;
use Imi\Util\Text;
use Imi\Util\Imi;

/**
 * @Tool("generate")
 */
class ModelGenerate
{
	/**
	 * 生成数据库中所有表的模型文件，如果设置了`include`或`exclude`，则按照相应规则过滤表。
	 * @Operation("model")
	 *
	 * @Arg(name="namespace", type=ArgType::STRING, required=true, comments="生成的Model所在命名空间")
	 * @Arg(name="database", type=ArgType::STRING, comments="数据库名，不传则取连接池默认配置的库名")
	 * @Arg(name="poolName", type=ArgType::STRING, comments="连接池名称，不传则取默认连接池")
	 * @Arg(name="prefix", type=ArgType::STRING, default="", comments="传值则去除该表前缀")
	 * @Arg(name="include", type=ArgType::ARRAY, default={}, comments="要包含的表名，以半角逗号分隔")
	 * @Arg(name="exclude", type=ArgType::ARRAY, default={}, comments="要排除的表名，以半角逗号分隔")
	 * @Arg(name="override", type=ArgType::BOOLEAN, default=false, comments="是否覆盖已存在的文件，请慎重！(true/false)")
	 * @return void
	 */
	public function generate($namespace, $database, $poolName, $prefix, $include, $exclude, $override)
	{
		$query = Db::query($poolName);
		// 数据库
		if(null === $database)
		{
			$database = $query->execute('select database()')->getScalar();
		}
		// 表
		$tables = $query->tableRaw('information_schema.TABLES')
						->where('TABLE_SCHEMA', '=', $database)
						->whereIn('TABLE_TYPE', [
							'BASE TABLE',
							'VIEW',
						])
						->field('TABLE_NAME')
						->select()
						->getColumn();
		// model保存路径
		$modelPath = Imi::getNamespacePath($namespace);
		foreach($tables as $table)
		{
			if(!$this->checkTable($table, $include, $exclude))
			{
				// 不符合$include和$exclude
				continue;
			}
			$className = $this->getClassName($table, $prefix);
			$fileName = File::path($modelPath, $className . '.php');
			if(is_file($fileName) && !$override)
			{
				// 不覆盖
				continue;
			}
			$data = [
				'namespace'	=>	$namespace,
				'className'	=>	$className,
				'table'		=>	[
					'name'	=>	$table,
					'id'	=>	[],
				],
				'fields'	=>	[],
			];
			$fields = $query->bindValue(':table', $table)->execute('show full columns from ' . $table)->getArray();
			$this->parseFields($fields, $data);
			$content = $this->renderTemplate($data);
			File::writeFile($fileName, $content);
		}
	}

	/**
	 * 检查表是否生成
	 * @param string $table
	 * @param array $include
	 * @param array $exclude
	 * @return boolean
	 */
	private function checkTable($table, $include, $exclude)
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
	 * @param string $prefix
	 * @return string
	 */
	private function getClassName($table, $prefix)
	{
		$prefixLen = strlen($prefix);
		if(substr($table, 0, $prefixLen) === $prefix)
		{
			$table = substr($table, $prefixLen);
		}
		return Text::toPascalName($table);
	}

	/**
	 * 处理字段信息
	 * @param array $fields
	 * @param array $data
	 * @return void
	 */
	private function parseFields($fields, &$data)
	{
		$idCount = 0;
		foreach($fields as $field)
		{
			$this->parseFieldType($field['Type'], $typeName, $length, $accuracy);
			$isPk = 'PRI' === $field['Key'];
			$data['fields'][] = [
				'name'				=>	$field['Field'],
				'varName'			=>	Text::toCamelName($field['Field']),
				'type'				=>	$typeName,
				'phpType'			=>	$this->dbFieldTypeToPhp($typeName),
				'length'			=>	$length,
				'accuracy'			=>	$accuracy,
				'nullable'			=>	$field['Null'] !== 'YES',
				'default'			=>	$field['Default'],
				'isPrimaryKey'		=>	$isPk,
				'primaryKeyIndex'	=>	$isPk ? $idCount : -1,
				'isAutoIncrement'	=>	false !== strpos($field['Extra'], 'auto_increment'),
				'comment'			=>	$field['Comment'],
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
	public function parseFieldType($text, &$typeName, &$length, &$accuracy)
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
	 * @param string $data
	 * @return string
	 */
	private function renderTemplate($data)
	{
		extract($data);
		ob_start();
		include __DIR__ . '/template.tpl';
		return ob_get_clean();
	}

	/**
	 * 数据库字段类型转PHP的字段类型
	 * @param string $type
	 * @return string
	 */
	private function dbFieldTypeToPhp($type)
	{
		static $map = [
			'int'			=>	'int',
			'smallint'		=>	'int',
			'tinyint'		=>	'int',
			'mediumint'		=>	'int',
			'bigint'		=>	'int',
			'bit'			=>	'boolean',
			'year'			=>	'int',
		];
		return $map[$type] ?? 'string';
	}
}