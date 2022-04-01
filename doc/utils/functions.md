# 全局函数

## imigo

启动一个协程，自动创建和销毁上下文

```php
imigo(function(){
    $value = Redis::get('key');
    // 无需手动释放任何资源
});
```

传入参数：

```php
imigo(function($id, $name){
    echo $id, ':', $name, PHP_EOL; // 1:test
}, 1, 'test');
```

## imiCallable

为传入的回调自动创建和销毁上下文，并返回新的回调

```php
$callable = imiCallable(function(){
    return 'abc';
});
function test($a)
{
    $value = $a(); // abc
}
test($callable);
```

开新协程去执行回调，无法获取返回值：

```php
$callable = imiCallable(function(){
    return 'abc';
}, true); // 这里加了 true
function test($a)
{
    $value = $a(); // 协程ID
}
test($callable);
```

## imiGetEnv

获取环境变量值

定义：`imiGetEnv($varname = null, $default = null, $localOnly = false);`

> 将在 imi v3.0.0 废弃，请使用 `Imi\env()`

## Imi\env

获取环境变量值

定义：`env($varname = null, $default = null, $localOnly = false);`

## Imi\cmd

处理命令行，执行后不会有 sh 进程

```php
echo \Imi\cmd('ls');
```

## Imi\ttyExec

尝试使用 tty 模式执行命令，可以保持带颜色格式的输出

返回进程退出码

定义：`function ttyExec(string|array $commands, ?float $timeout = null, ?\Symfony\Component\Process\Process &$process = null): int`

```php
\Imi\tty('ls'); // 默认不超时

\Imi\tty('sleep 2', 1.5); // 1.5 秒超时

\Imi\tty('ls', null, $process); // 捕获 \Symfony\Component\Process\Process 对象
```

超时会抛出异常：`\Symfony\Component\Process\Exception\ProcessTimedOutException`

## Imi\dump

调试输出函数，用法同 `var_dump()`。

cli 模式运行时，会通过 `Log::debug()` 来记录运行结果。

例：

```php
\Imi\dump('Hello imi!');
```

```log
[2022-03-21 16:21:34] imi.DEBUG: 
string(10) "Hello imi!"
```
