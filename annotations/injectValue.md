# 注入值注解

imi 中有一类注解，他们支持将值动态注入到注解属性中，当调用获取注解属性时，才实时计算并返回。

## 注解说明

### @ConstValue

从常量中读取值

| 属性名称 | 说明 |
| ------------ | ------------ 
| name | 常量名 |
| default | 常量不存在时，返回的默认值 |

### @ConfigValue

从配置中读取值

| 属性名称 | 说明 |
| ------------ | ------------ 
| name | 配置名，支持`@app`、`@currentServer`等用法 |
| default | 配置名，支持`@app`、`@currentServer`等用法 | 不存在时，返回的默认值 |

### @Inject

对象注入，使用：`App::getBean()`

| 属性名称 | 说明 |
| ------------ | ------------ 
| name | Bean名称或类名 |
| args | Bean实例化参数 |

### @RequestInject

对象注入，使用：`RequestContext::getBean()`

同`@Inject`

### @Callback

回调注解

| 属性名称 | 说明 |
| ------------ | ------------ 
| class | 类名，或者传入对象，比如可以使用 `@Inject`、`@RequestInject` 再次值注入 |
| method | 方法名 |

## 用法示例

```php
/*
 * @Cacheable(
 *   key="index:{page}",
 *   ttl=10,
 *   lockable=@Lockable(
 *     id="index:{page}",
 *     waitTimeout=999999,
 *   ),
 *   preventBreakdown=true,
 * )
 */
```