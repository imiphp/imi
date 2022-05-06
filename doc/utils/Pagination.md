# Pagination

[toc]

**类名:** `Imi\Util\Pagination`

分页计算类

## 方法

### 构造方法

`public function __construct($page, $count)`

`$page` 当前页码

`$count` 每页数量

### getPage

字面意思

### setPage

字面意思

### getCount

字面意思

### setCount

字面意思

### getLimitOffset

获取偏移量，如 `limit 20, 10` 中的 `20`

### getLimitEndOffset

获取结束的偏移量，如 `limit 20, 10` 中的 `29`

### calcPageCount

根据记录数计算总页数

```php
$records = 101;
$page = new \Imi\Util\Pagination(1, 10);
$pagination->calcPageCount($records); // 11
```

## 例子

```php
$page = new \Imi\Util\Pagination(10, 15);
$limit = 'limit ' . $page->getLimitOffset() . ',' . $page->getCount();
echo $limit;
```