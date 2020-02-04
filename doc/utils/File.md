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

// 创建文件，目录不存在，指定内容为abc，则自动创建，权限默认0755
File::createFile('a/b/c.txt', 'abc');

// 创建文件，目录不存在则自动创建，权限为0777
File::createFile('a/b/c.txt', '', 0777);
```

### isEmptyDir

判断是否为空目录

```php
File::isEmptyDir(__DIR__);
```

### deleteDir

递归删除目录及目录中所有文件

```php
File::deleteDir('xxx');
```

### putContents

写入内容到文件，如果目录不存在自动创建多级目录。

> 参数、返回值与 `file_put_contents` 完全一致

```php
File::putContents('./a/b/c/1.txt', '123');
```

### absolute

获取绝对路径

相比 `realpath()` 函数，此方法不需要路径是真实存在的。

```php
// /a/b/c/1.jpg
File::absolute('/a/b/d/e/../../c/./1.jpg');
```
