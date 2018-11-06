# 模型事件

## 事件列表

| 事件名 | 常量 | 描述 |
| ------ | ------ | ------ |
| BeforeFind | ModelEvents::BEFORE_FIND | 查找前，Model::find()触发 |
| AfterFind | ModelEvents::AFTER_FIND | 查找后，Model::find()触发 |
| BeforeSelect | ModelEvents::BEFORE_SELECT | 查询前，Model::select()触发 |
| AfterSelect | ModelEvents::AFTER_SELECT | 查询后，Model::select()触发 |
| BeforeInit | ModelEvents::BEFORE_INIT | 初始化值前，newInstance()触发 |
| AfterInit | ModelEvents::AFTER_INIT | 初始化值后，newInstance()触发 |
| BeforeInsert | ModelEvents::BEFORE_INSERT | 插入前，insert()/save()触发 |
| AfterInsert | ModelEvents::AFTER_INSERT | 插入后，insert()/save()触发 |
| BeforeUpdate | ModelEvents::BEFORE_UPDATE | 更新前，update()/save()触发 |
| AfterUpdate | ModelEvents::AFTER_UPDATE | 更新后，update()/save()触发 |
| BeforeDelete | ModelEvents::BEFORE_DELETE | 删除前，delete()触发 |
| AfterDelete | ModelEvents::AFTER_DELETE | 删除后，delete()触发 |
| BeforeSave | ModelEvents::BEFORE_SAVE | 保存前，先于插入前和更新前触发 |
| AfterSave | ModelEvents::AFTER_SAVE | 保存后，后于插入后和更新后触发 |
| BeforeBatchUpdate | ModelEvents::BEFORE_BATCH_UPDATE | 批量更新前 |
| AfterBatchUpdate | ModelEvents::AFTER_BATCH_UPDATE | 批量更新后 |
| BeforeBatchDelete | ModelEvents::BEFORE_BATCH_DELETE | 批量删除前 |
| AfterBatchDelete | ModelEvents::AFTER_BATCH_DELETE | 批量删除后 |
| AfterQuery | ModelEvents::AFTER_QUERY | 只要最终查询出该模型就会触发 |
| BeforeParseData | ModelEvents::BEFORE_PARSE_DATA | 处理 save、insert、update 数据前 |
| AfterParseData | ModelEvents::AFTER_PARSE_DATA | 处理 save、insert、update 数据后 |

事件传递过来的参数类型为`Imi\Model\Event\Param\事件名EventParam`

`BeforeInit`和`AfterInit`是例外，共用`Imi\Model\Event\Param\InitEventParam`类

## 事件监听

事件监听分两种，一种是仅限于**对象的事件**，另一种是**静态方法触发的事件**（批量操作）。

监听方式分两种：**1、在类里写监听代码**；**2、定义监听类**

批量操作的事件监听，一般建议用监听类方式。对象的事件监听根据习惯选择即可。

### 对象事件监听方法1-监听代码

```php
/**
 * Test
 * @Entity
 * @Table(name="tb_test", id={"id", "a"})
 */
class Test extends Model
{
	public function __init($data = [])
	{
		$this->on(ModelEvents::AFTER_INIT, [$this, 'onAfterInit']);
		parent::__init($data);
	}

	public function onAfterInit(\Imi\Model\Event\Param\InitEventParam $data)
	{
		var_dump($data->data);
	}
}
```

### 对象事件监听方法2-定义监听类

```php
<?php
namespace XXX\ModelEvent\Logs;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Model\Event\Param\BeforeInsertEventParam;
use Imi\Model\Event\Listener\IBeforeInsertEventListener;

/**
 * 插入前处理
 * @ClassEventListener(className="\XXX\Model\Test",eventName=\Imi\Model\Event\ModelEvents::BEFORE_INSERT)
 */
class BeforeInsert implements IBeforeInsertEventListener
{
	/**
	 * 事件处理方法
	 * @param BeforeInsertEventParam $e
	 * @return void
	 */
	public function handle(BeforeInsertEventParam $e)
	{
		$e->data->data = json_encode($e->data->data);
		$e->data->ip = inet_pton($e->data->ip);
	}
}
```

### 批量操作事件监听

事件名称为`模型类名:事件名`，如：`XXX\Model\TestBefore`+`BatchUpdate`=`XXX\Model\Test:BeforeBatchUpdate`

```php
<?php
namespace XXX\Listener;

use Imi\Model\Event\Param\BeforeBatchUpdateEventParam;
use Imi\Model\Event\Listener\IBeforeBatchUpdateEventListener;

/**
 * @Listener("XXX\Model\TestBeforeBatchUpdate")
 */
class BeforeBatchUpdate implements IBeforeBatchUpdateEventListener
{
	/**
	 * 事件处理方法
	 * @param BeforeBatchUpdateEventParam $e
	 * @return void
	 */
	public function handle(BeforeBatchUpdateEventParam $e)
	{
		// $e->data->name = '123'; // 在更新前可以对数据赋值
	}
}

```