# 测试说明

## 命令

生成 MySQL 模型：`src/Cli/bin/imi-cli generate/model --app-namespace "Imi\Test\Component" "Imi\Test\Component\Model" --prefix tb_ --override base --sqlSingleLine --lengthCheck`

生成 PostgreSQL 模型：`src/Cli/bin/imi-cli generate/pgModel --app-namespace "Imi\Pgsql\Test" "Imi\Pgsql\Test\Model" --prefix tb_ --override base --lengthCheck`
