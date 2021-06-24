# 守护进程

## 命令模式

守护进程方式启动：`bin/imi swoole/start -d`

重定向标准输入输出：`bin/imi swoole/start -d 文件名.log`

> 此方法只可让服务在后台运行，退出 ssh 后不被终止。

> 无法在服务崩溃后重新拉起，建议使用 systemd

## Systemd

Systemd 一般都已经集成在了现代 Linux 发行版中，使用它可以实现开机自启动和守护进程等功能。

但 Systemd 在 WSL、Docker 环境中，可能难以使用。

创建服务配置文件 `test.service`

```ini
[Unit]
Description=test
After=network.target
After=syslog.target

[Service]
Type=simple
LimitNOFILE=65535
ExecStart=/your app path/vendor/bin/imi-swoole swoole/start
ExecReload=/bin/kill -USR1 $MAINPID
Restart=always

[Install]
WantedBy=multi-user.target graphical.target
```

> 修改`ExecStart`为你的启动命令，该文件适用于任何应用，不仅限于 imi 项目

启用服务：`systemctl --user enable $PWD/test.service`

启动服务：`systemctl start test`

重启服务：`systemctl restart test`

停止服务：`systemctl stop test`

禁用服务：`systemctl --user disable $PWD/test.service`

## Supervisor

Supervisor是用 Python 开发的一套通用的进程管理程序，能将一个普通的命令行进程变为后台 daemon，并监控进程状态，异常退出时能自动重启。

### 安装和启动

**apt 安装：**

`apt-get install -y supervisor`

**yum 安装：**

`yum install -y supervisor`

**pip 安装：**

`pip install supervisor`

**启动 Supervisor 服务：**

`service supervisor start`

### 服务配置文件

文件名：`/etc/supervisord.d/服务名.conf`

文件内容：

```ini
# 项目名
[program:服务名]

# 脚本目录
directory=/your app path

# 脚本执行命令
command=/your app path/vendor/bin/imi-swoole swoole/start

# supervisor启动的时候是否随着同时启动，默认True
autostart=true

# 当程序exit的时候，这个program不会自动重启,默认unexpected，设置子进程挂掉后自动重启的情况，有三个选项，false,unexpected和true。如果为false的时候，无论什么情况下，都不会被重新启动，如果为unexpected，只有当进程的退出码不在下面的exitcodes里面定义的
autorestart = false

# 这个选项是子进程启动多少秒之后，此时状态如果是running，则我们认为启动成功了。默认值为1
startsecs = 1

# 脚本运行的用户身份 
user = test

# 日志输出 
stderr_logfile=/tmp/stderr.log
stdout_logfile=/tmp/stdout.log

# 把stderr重定向到stdout，默认 false
redirect_stderr = true

# stdout日志文件大小，默认 50MB
stdout_logfile_maxbytes = 20MB

# stdout日志文件备份数
stdout_logfile_backups = 20
```

### Supervisor 服务管理命令说明

```shell
# 查看所有进程的状态
supervisorctl status

# 启动服务名
supervisorctl start 服务名

# 停止服务
supervisorctl stop 服务名

# 重启服务名
supervisorctl restart 服务名

# 配置文件修改后使用该命令加载新的配置
supervisorctl update

# 重新启动配置中的所有程序
supervisorctl reload
```
