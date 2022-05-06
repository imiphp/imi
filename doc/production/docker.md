# Docker

[toc]

## Swoole 模式

推荐使用 Swoole 官方 Docker：

* <https://github.com/swoole/docker-swoole>

* <https://hub.docker.com/r/phpswoole/swoole>

**Dockerfile:**

```yml
# 版本也可以自行修改
FROM phpswoole/swoole:4.8-php7.4

RUN apt update && apt install unzip

# 安装必要的扩展
RUN docker-php-ext-install mysqli pdo_mysql > /dev/null

# 安装 Redis 扩展
RUN pecl install redis && docker-php-ext-enable redis
```

## Workerman 模式

推荐使用 PHP 公共 Docker：<https://hub.docker.com/_/php>

**Dockerfile:**

```yml
# 版本也可以自行修改
FROM php:7.4-cli

RUN apt update && apt install unzip libevent-dev libssl-dev

# 安装必要的扩展
RUN docker-php-ext-install mysqli pdo_mysql pcntl sockets > /dev/null

# 安装 Redis 扩展
RUN pecl install redis && docker-php-ext-enable redis

# 安装 Event 扩展，提升 Workerman 性能
RUN pecl install event && docker-php-ext-enable event

# 安装 Composer，如果不需要可以注释
RUN curl -o /usr/bin/composer https://getcomposer.org/composer.phar && chmod +x /usr/bin/composer
```

## php-fpm 模式

推荐使用 PHP 公共 Docker：<https://hub.docker.com/_/php>

**Dockerfile:**

```yml
# 版本也可以自行修改
FROM php:7.4

RUN apt update && apt install unzip

# 安装必要的扩展
RUN docker-php-ext-install mysqli pdo_mysql > /dev/null

# 安装 Redis 扩展
RUN pecl install redis && docker-php-ext-enable redis

# 安装 Composer，如果不需要可以注释
RUN curl -o /usr/bin/composer https://getcomposer.org/composer.phar && chmod +x /usr/bin/composer
```
