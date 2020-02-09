# 守护进程

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

启用服务：`systemctl --user enable $PWD/test.service`

启动服务：`systemctl start test`

重启服务：`systemctl restart test`

停止服务：`systemctl stop test`

禁用服务：`systemctl --user disable $PWD/test.service`
