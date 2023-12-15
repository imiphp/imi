# PostgreSQL

[toc]

需要引入 `imi-pgsql` 组件：`composer require imiphp/imi-pgsql`

## 驱动类

### PdoPgsqlDriver

基于 PDO 实现，支持所有环境，不支持 Swoole 协程。

### SwoolePgsqlDriver

基于 [Swoole 协程 PostgreSQL 客户端](http://wiki.swoole.com/#/coroutine_client/postgresql) 实现
