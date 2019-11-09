# Text

**类名:** `Imi\Util\Text`

字符串工具类

## 方法

### startwith

字符串是否以另一个字符串开头

```php
// true
var_dump(Text::startwith('http://www.baidu.com', 'http://'));

// false
var_dump(Text::startwith('http://www.baidu.com', 'https://'));
```

### endwith

字符串是否以另一个字符串结尾

```php
// true
var_dump(Text::endwith('http://www.baidu.com/index.html', '.html'));

// false
var_dump(Text::endwith('http://www.baidu.com/index.html', '.htm'));
```

### insert

插入字符串

```php
$str = 'abde';
// abcde
echo Text::insert($str, 2, 'c'), PHP_EOL;
```

### isEmpty

字符串是否为空字符串或者为null

```php
// true
var_dump(Text::isEmpty(''));

// true
var_dump(Text::isEmpty(null));

// false
var_dump(Text::isEmpty(0));

// false
var_dump(Text::isEmpty('0'));
```

### toCamelName

转为驼峰命名，会把下划线后字母转为大写

```php
// adminUserAge
echo Text::toCamelName('admin_user_age'), PHP_EOL;
```

### toPascalName

转为每个单词大写的命名，会把下划线后字母转为大写

```php
// AdminUserAge
echo Text::toPascalName('admin_user_age'), PHP_EOL;
```

### toUnderScoreCase

转为下划线命名

```php
// admin_user_age
echo Text::toUnderScoreCase('AdminUserAge'), PHP_EOL;

// Admin_User_Age
echo Text::toUnderScoreCase('AdminUserAge', false), PHP_EOL;
```
