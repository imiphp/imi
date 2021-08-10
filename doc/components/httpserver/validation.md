# HTTP 验证器

请先阅读 [验证器](/v2.0/components/validation/index.html) 章节，HTTP 验证器基于验证器扩展，基本使用方式相似。

首先，HTTP 验证器是`@HttpValidation`注解，它只能写在方法上。写了这个注解，在`action`方法调用前会触发验证，验证失败同样的抛出异常

另外，验证规则注解，如`@Required`等的`name`属性用法也略有不同。

你可以使用`$get`、`$post`、`$body`、`$headers`、`$cookie`、`$session`、`$this`后面跟上`.参数名`指定参数，其中`get`和`post`自然不用多说，这`body`的用处就是，比如参数是以`json`为`body`传给你的，他会自动给你`json_decode`，你用$body就可以指定了。

`$this` 代表是当前控制器对象。

还有一个`@ExtractData`注解，它可以把`get/post/body`中的参数导出到`action`方法的参数中。

`@ExtractData`注解可以独立使用，不依赖`@HttpValidation`注解，但只能在控制器中使用。

如下方法所示：

```php
/**
 * http参数验证测试
 * 
 * @Action
 * 
 * @HttpValidation
 * 
 * @Required(name="$get.id", message="用户ID为必传参数")
 * @Integer(name="$get.id", min="1", message="用户ID不符合规则")
 * @Required(name="$get.name", message="用户姓名为必传参数")
 * @Text(name="$get.name", min="2", message="用户姓名长度不得少于2位")
 * @Required(name="$get.age", default=-1)
 * 
 * @ExtractData(name="$get.id", to="id")
 * @ExtractData(name="$get.name", to="name")
 * @ExtractData(name="$get.age", to="age")
 *
 * @return void
 */
public function httpValidation($id, $name, $age)
{
    return compact('id', 'name', 'age');
}
```