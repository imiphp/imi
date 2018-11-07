# 验证器

imi 提供了基本数据类型的验证，以及可扩展的验证方法，这一切都可以通过注解来使用。

## 注解

### @Condition

通用验证条件，传入回调进行验证

该注解可以写在类、属性、方法上。

参数：

```php
/**
 * 参数名称
 * 属性注解可省略
 *
 * @var string
 */
public $name;

/**
 * 当值不符合条件时的默认值
 *
 * @var mixed
 */
public $default;

/**
 * 对结果取反
 *
 * @var boolean
 */
public $inverseResult = false;

/**
 * 当验证条件不符合时的信息
 * 
 * 支持代入{:value}原始值
 * 支持以{name}这样的形式，代入注解参数值
 *
 * @var string
 */
public $message = '{name} validate failed';

/**
 * 验证回调
 *
 * @var callable
 */
public $callable;

/**
 * 参数名数组
 *
 * @var array
 */
public $args;

/**
 * 异常类
 *
 * @var string
 */
public $exception = null;

/**
 * 异常编码
 *
 * @var integer
 */
public $exCode = null;
```

`callable` 是回调，支持`is_int`或`XXX::check`写法

`args` 是回调方法参数，如：`{":value"}`，即为将验证值；`{:data}`，即为代入整个数据数组或对象。代入第1个参数。

你也可以传入多个参数，如：`{":value", 'name'}`，其中`name`代表将当前字段名代入，其它参数同理。

`inverseResult` 参数为`true`时，会对验证回调方法结果取反后，判断是否为true

`message` 是验证失败的消息，可以将`{name}`形式的注解参数值代入，也可以使用`{:value}`代入验证值。

`exception`和`exCode`可以设定验证失败时抛出的异常类及异常编码，默认为`\InvalidArgumentException`类。

除了`callable`和`args`以外，其它参数都可以作为其它验证条件注解（如：`@Required` 等）的参数

### @Required

判断值是否存在

### @Text

文本验证

必须>=6位长度，最长不限制：

`@Text(min=6)`

长度必须>=6 && <=12：

`@Text(min=6, max=12)`

### @Integer

整数验证

验证必须为整数：

`@Integer`

验证必须为>=1024的整数：

`@Integer(min=1024)`

验证必须为<=1024的整数：

`@Integer(max=1024)`

验证必须为>=1 && <=10的整数：

`@Integer(min=1, max=10)`

### @Decimal

小数验证

验证必须为小数：

`@Decimal`

验证必须为>=10.24的小数：

`@Decimal(min=10.24)`

验证必须为<=10.24的小数：

`@Decimal(max=10.24)`

验证必须为>=1 && <=10.24的小数：

`@Decimal(min=1, max=10.24)`

> 传入`1`，结果为`false`
> 
> 传入`1.0`，结果为`true`

### @Number

数值验证，允许是整数或者小数

验证必须为数值：

`@Decimal`

验证必须为>=10.24的数值：

`@Decimal(min=10.24)`

验证必须为<=10.24的数值：

`@Decimal(max=10.24)`

验证必须为>=1 && <=10.24的数值：

`@Decimal(min=1, max=10.24)`

> 传入`1`，结果为`true`
> 
> 传入`1.0`，结果为`true`
> 
### @InList

列表验证，判断值是否存在于列表中

`@InList(list={1, 2, 3})`

相当于：

```php
$result = in_array($value, [1, 2, 3]);
```

## 自动验证

注解：`@AutoValidation`

### 验证类属性

imi 支持在类、属性上使用 `@AutoValidation` 注解，当构造方法执行完毕后，触发验证。验证失败抛出异常。

如下代码，写在类上的注解以及属性上的注解，都因为加了`@AutoValidation` 注解，所以在构造方法执行完成后，会自动进行验证，验证失败则抛出异常。

```php
/**
 * @Bean("ValidatorTest")
 * 
 * @AutoValidation
 * 
 * 
 * @InList(name="in", list={1, 2, 3}, message="{:value} 不在列表内")
 * @Integer(name="int", min=0, max=100, message="{:value} 不符合大于等于{min}且小于等于{max}")
 * @Required(name="required", message="{name}为必须参数")
 * @Number(name="number", min=0.01, max=999.99, accuracy=2, message="数值必须大于等于{min}，小于等于{max}，小数点最多保留{accuracy}位小数，当前值为{:value}")
 * @Text(name="text", min=6, max=12, message="{name}参数长度必须>={min} && <={max}")
 * @Condition(name="my", callable="\ImiDemo\HttpDemo\MainServer\Validator\Test::myValidate", args={":value"}, message="{name}值必须为1")
 */
class Test
{
    /**
     * @Decimal(min=-0.01, max=999.99, accuracy=2, message="小数必须大于等于{min}，小于等于{max}，小数点最多保留{accuracy}位小数，当前值为{:value}")
     *
     * @var float
     */
    public $decimal;

    public function __construct($data = [], $rules = null)
    {
        foreach($data as $name => $value)
        {
            $this->$name = $value;
        }
    }

    public static function myValidate($value)
    {
        return 1 == $value;
    }
}

```

### 验证方法参数

在 imi 中，如果你在方法上使用 `@AutoValidation` 注解，当方法被调用前，会触发验证操作，验证失败则抛出异常。验证的参数是传入方法的参数，如下代码，验证通过则进入方法体中，验证失败会抛出异常。

```php
/**
 * @AutoValidation
 * 
 * @Required(name="id", message="用户ID为必传参数")
 * @Integer(name="id", min="1", message="用户ID不符合规则")
 * @Required(name="name", message="用户姓名为必传参数")
 * @Text(name="name", min="2", message="用户姓名长度不得少于2位")
 *
 * @param int $id
 * @param string $name
 * @return void
 */
public function test222($id, $name)
{
    var_dump($id, $name);
}
```

## 手动验证

你也可以自己定义一个专门用于验证的类，将数据传入该类，手动调用验证方法。

```php
/**
 * @Bean("ValidatorTest")
 * 
 * @InList(name="in", list={1, 2, 3}, message="{:value} 不在列表内")
 * @Integer(name="int", min=0, max=100, message="{:value} 不符合大于等于{min}且小于等于{max}")
 * @Required(name="required", message="{name}为必须参数")
 * @Number(name="number", min=0.01, max=999.99, accuracy=2, message="数值必须大于等于{min}，小于等于{max}，小数点最多保留{accuracy}位小数，当前值为{:value}")
 * @Text(name="text", min=6, max=12, message="{name}参数长度必须>={min} && <={max}")
 * @Condition(name="my", callable="\ImiDemo\HttpDemo\MainServer\Validator\Test::myValidate", args={":value"}, message="{name}值必须为1")
 */
class Test extends Validator
{
    /**
     * @Decimal(min=-0.01, max=999.99, accuracy=2, message="小数必须大于等于{min}，小于等于{max}，小数点最多保留{accuracy}位小数，当前值为{:value}")
     *
     * @var float
     */
    public $decimal;

    public function __construct($data = [], $rules = null)
    {
        parent::__construct($data, $rules);
        foreach($data as $name => $value)
        {
            $this->$name = $value;
        }
    }

    public static function myValidate($value)
    {
        return 1 == $value;
    }
}

```

当你使用手动验证时，可以直接`new`出来使用，不是很有必要使用容器。

使用代码示例：

```php
$v = new Test([
    // 'decimal'   =>  1.1,
    // 'in'        =>  1,
    'int'       =>  1,
    'required'  =>  1,
    'number'    =>  1,
    'my'        =>  1,
]);

// 也可以设置数据
// $v->setData([]);

// 验证，当遇到不通过时结束验证流程
$result = $v->validate();
if(!$result)
{
    echo 'error: ', $v->getMessage(), PHP_EOL;
    // 当前错误的注解规则
    var_dump($v->getFailRule());
}

// 验证所有
$result = $v->validateAll();
if(!$result)
{
    var_dump(
        // 所有错误数组，注意每个成员也是个数组，里面可能有多个
        $v->getResults()
        // 所有错误的注解规则
        , $v->getFailRules()
    );
}

// 获得数据，如果你配置有default属性，并且验证失败，可以获得默认值
$data = $v->getData();
```