# 模型配置

[toc]

**项目配置文件：**

```php
[
    'models' => [
        // 模型类型，带命名空间，开头不要有斜杠
        'Imi\Pgsql\Test\Model\Article' => [
            'name' => 'tb_article', // 覆盖注解中定义的表名，还支持：数据库名.表名
            'dbPoolName' => null, // 覆盖注解中定义的连接池名
            'prefix' => null, // 覆盖注解中定义的表名前缀
        ],
    ],
]
```
