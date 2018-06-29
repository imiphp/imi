# 热更新

由于 IMI 基于 Swoole 常驻内存，所以 PHP 的一大特点热更新就没有了。

为此，IMI 中实现了业务代码的热更新，方便开发调试、动态部署，支持平滑重载。

## 配置

热更新通过配置文件中的`beans`节配置。

详见下面的注释：

```php
'hotUpdate'	=>	[
	// 'status'	=>	false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

	// --- 文件修改时间监控 ---
	// 'monitorClass'	=>	\Imi\HotUpdate\Monitor\FileMTime::class,
	// 'timespan'	=>	1, // 检测时间间隔，单位：秒

	// --- Inotify 扩展监控 ---
	// 'monitorClass'	=>	\Imi\HotUpdate\Monitor\Inotify::class,
	// 'timespan'	=>	0, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

	// 'includePaths'	=>	[], // 要包含的路径数组
	// 'excludePaths'	=>	[], // 要排除的路径数组，支持通配符*
	// 'defaultPath'	=>	[], // 设为数组则覆盖默认的监控路径
],
```

> 默认是根据文件修改时间监控，建议有条件的上`Inotify`，性能更佳！