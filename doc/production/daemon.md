# 守护进程

## 命令模式

守护进程方式启动：`bin/imi server/start -d`

重定向标准输入输出：`bin/imi server/start -d 文件名.log`

> 此方法只可让服务在后台运行，退出 ssh 后不被终止。

> 无法在服务崩溃后重新拉起，建议使用 systemd

## systemd

创建文件 `test.service`

```
[Unit]
Description=test
After=network.target
After=syslog.target

[Service]
Type=simple
LimitNOFILE=65535
ExecStart=/your app path/vendor/bin/imi server/start
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
