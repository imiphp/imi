# MemoryTable

imi 支持 [MemoryTableModel](/components/orm/MemoryTableModel.html)，也支持直接定义和操作MemoryTable。

## 配置方法

```php
// 内存表配置
'memoryTable'   =>  [
    // name => 配置
    't1'    =>  [
        // 定义字段
        'columns'   =>  [
            ['name' => 'name', 'type' => \Swoole\Table::TYPE_STRING, 'size' => 16],
            ['name' => 'quantity', 'type' => \Swoole\Table::TYPE_INT],
        ],
        'lockId'    =>  'atomic', // 锁 ID，非必设。不配置就不允许使用锁，其它的可以正常用
    ],
    'connectContext'    =>  [
        // 指定实现了 Imi\Util\MemoryTable\IMemoryTableOption 接口的来，来定义表结构
        'class' =>  \Imi\Server\ConnectContext\StoreHandler\MemoryTable\ConnectContextOption::class,
        'lockId'=>  'atomic', // 同上
    ],
],
```

## 使用方法

### 写入

```php
use Imi\Util\MemoryTableManager;
$key = 'a'; // 主键
$value = [
    'name'      =>  'abc',
    'quantity'  =>  123,
];
MemoryTableManager::set($tableName, $key, $value);
```

### 读取

```php
// 获取一行
$row = MemoryTableManager::get($tableName, $key);

// 获取单个字段
$field = 'quantity';
$value = MemoryTableManager::get($tableName, $key, $field);
```

### 删除

```php
MemoryTableManager::del($tableName, $key);
```

### 数据行是否存在

```php
MemoryTableManager::exist($tableName, $key);
```

### 原子自增

```php
MemoryTableManager::incr($tableName, $key, $field); // +1
MemoryTableManager::incr($tableName, $key, $field, 123); // +123
```

### 原子自减

```php
MemoryTableManager::decr($tableName, $key, $field); // -1
MemoryTableManager::decr($tableName, $key, $field, 123); // -123
```

### 获取行数

```php
MemoryTableManager::count($tableName);
```

### 加锁执行

> 需要配置 `lockId`

```php
MemoryTableManager::lock($tableName, function(){
    // 方法体内部都在锁中执行，执行完自动释放锁
    $row = MemoryTableManager::get($tableName, $key);
    $row['quantity'] = 456;
    MemoryTableManager::set($tableName, $key, $row);
});
```

## 实现 IMemoryTableOption 接口

示例代码：

```php
<?php
namespace Imi\Server\ConnectContext\StoreHandler\MemoryTable;

use Imi\Util\MemoryTable\IMemoryTableOption;

/**
 * Swoole 内存表
 */
class ConnectContextOption implements IMemoryTableOption
{
    /**
     * 获取配置
     *
     * @return array
     */
    public function getOption($option = null): array
    {
        if(!$option)
        {
            $option = [];
        }
        if(!isset($option['size']))
        {
            $option['size'] = 65536;
        }
        $option['columns'] = [
            ['name' => 'data', 'type' => \Swoole\Table::TYPE_STRING, 'size' => $option['dataLength'] ?? 1024],
        ];
        return $option;
    }

}
```