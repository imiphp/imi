# File

**类名:** `Imi\Util\File`

文件相关工具类

## 方法

### enum

方法返回一个迭代器对象。

```php
// 枚举当前目录及所有子目录中的文件，不包含.和..
foreach(File::enum(__DIR__) as $fileName)
{
	echo (string)$fileName, PHP_EOL;
}
```

### enumPHPFile

方法返回一个迭代器对象。

```php
// 枚举当前目录及所有子目录中的PHP文件
foreach(File::enumPHPFile(__DIR__) as $fileName)
{
	echo (string)$fileName, PHP_EOL;
}
```

### path

组合路径，目录后的/不是必须

```php
// abc/index.html
echo File::path('abc', 'index.html');

// 支持协议uri，多余的/会合并为一个：http:/www.baidu.com/a/b/index.html
echo File::path('http://www.baidu.com', 'a//b///', 'index.html');
```

### readAll

根据文件打开句柄，读取文件所有内容

```php
$fp = fopen(__FILE__, 'r');
echo File::readAll($fp);
fclose($fp);
```

### readFile

读取文件所有内容，优先使用协程，如果不支持则使用传统阻塞方式

```php
echo File::readFile(__FILE__);
```

### writeFile

写入文件，优先使用协程，如果不支持则使用传统阻塞方式

```php
// 写入文件
File::writeFile('a.txt', '123');

// 追加进文件
File::writeFile('a.txt', '456', FILE_APPEND);
```

### createDir

创建一个目录

```php
// 递归创建目录，权限默认0755
File::createDir('a/b/c');

// 递归创建目录，权限为0777
File::createDir('a/b/c', 0777);
```

### createFile

创建一个文件

```php
// 创建文件，目录不存在则自动创建，权限默认0755
File::createFile('a/b/c.txt');

// 创建文件，目录不存在则自动创建，权限为0777
File::createFile('a/b/c.txt', 0777);
```
