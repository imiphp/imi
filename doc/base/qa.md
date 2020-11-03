# 常见问题

**PHP Warning:  exec() has been disabled for security reasons**

解决方案：不要禁用 `exec、shell_exec`，在 `php.ini` 中修改 `disable_functions` 项

---

**[error] Uncaught RuntimeException: Tool /xxx/imi/vendor/yurunsoft/imi/bin/imi does not exists!**

**Could not find package imiphp/project-http with stability stable**

解决方案：

1. 如果你是 Windows 用户，请不要使用 Windows 中的 Composer！
2. 不要使用`pkg.phpcomposer.com`镜像，建议使用阿里云家的：`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`
3. 将 imi 更新到最新版本

---
