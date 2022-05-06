# Random

[toc]

**类名:** `Imi\Util\Random`

随机生成一些东西的工具类

## 方法

### int

随机整数

```php
// 随机范围：PHP_INT_MIN-PHP_INT_MAX
echo Random::int(), PHP_EOL;

// 随机范围：1-20
echo Random::int(1, 20), PHP_EOL;
```

### number

随机生成小数

```php
// 随机范围：PHP_INT_MIN-PHP_INT_MAX
echo Random::number(), PHP_EOL;

// 随机范围：12-20
echo Random::number(1.2, 20), PHP_EOL;
```

### text

随机生成文本

```php
// 从abcdefg中随机4-6个字符
echo Random::text('abcdefg', 4, 6), PHP_EOL;

// 从abcdefg中随机5个字符
echo Random::text('abcdefg', 5), PHP_EOL;
```

### letter

随机生成字母

```php
// 随机大小写字母4-6个
echo Random::letter(4, 6), PHP_EOL;
// 随机大小写字母5个
echo Random::letter(5), PHP_EOL;
```

### digital

随机生成数字

和`Random::int()`方法不同的是，这个是生成字符串，所以不限制数值的大小。

```php
// 随机100-200个数字
echo Random::digital(100, 200), PHP_EOL;
// 随机100个数字
echo Random::digital(100), PHP_EOL;
```

### letterAndNumber

```php
// 随机生成4-6个字母+数字
echo Random::letterAndNumber(4, 6);
// 随机生成5个字母+数字
echo Random::letterAndNumber(5);
```
