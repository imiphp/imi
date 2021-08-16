# imi-access-control

[![Latest Version](https://img.shields.io/packagist/v/imiphp/imi-access-control.svg)](https://packagist.org/packages/imiphp/imi-access-control)
[![Php Version](https://img.shields.io/badge/php-%3E=7.4-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/imiphp/imi-access-control.svg)](https://github.com/imiphp/imi-access-control/blob/master/LICENSE)

## 介绍

imi 框架的权限控制组件，不提供具体 API、管理界面，仅提供基础操作组件。

本组件中支持：角色关联操作，用户关联角色，用于关联操作。

用户除了角色赋予的操作权限以外，还可以单独赋予操作权限。

> 本仓库仅用于浏览，不接受 issue 和 Pull Requests，请前往：<https://github.com/imiphp/imi>

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "imiphp/imi-access-control": "2.0.x-dev"
    }
}
```

然后执行 `composer update` 安装。

## 使用

在项目 `config/config.php` 中配置：

```php
[
    'components'    =>  [
        // 引入本组件
        'AccessControl'    =>  'Imi\AC',
    ],
]
```

### 操作权限

#### 创建操作权限

```php
use Imi\AC\AccessControl\Operation;

Operation::create('权限名称');

// 权限代码不传或为null，则和权限名称相同，不可重复
Operation::create('权限名称', '权限代码');

// 指定父级ID、排序索引
Operation::create('权限名称', '权限代码', $parentId, $index, '介绍');
```

### 角色

#### 创建角色

```php
use Imi\AC\AccessControl\Role;

// 与 Operation::create 一样，不多做说明了
$role = Role::create('权限名称', '权限代码', '介绍');
```

#### 获取角色信息

```php
// 支持ID、Code两种模式
$role = new Role('权限ID');
$role = new Role('权限代码', 'code');
$roleInfo = $role->getRoleInfo(); // $roleInfo->id/code/name/description
```

#### 获取角色操作权限

```php
// 数组，成员为 \Imi\AC\Model\Operation 类型
$operations = $role->getOperations();

// 树形结构，成员为 \Imi\AC\Model\Filter\OperationTreeItem 类型，$item->children 为其下一级角色，同样为 \Imi\AC\Model\Filter\OperationTreeItem 类型
$operationTree = $role->getOperationTree();
```

#### 增加、设置权限

```php
$role->addOperations('code1', 'code2'); // 只在当前基础上增加这两个权限

$role->setOperations('code1', 'code2'); // 将角色权限设置为仅有这两个权限
```

#### 移除权限

```php
$role->removeOperations('code1', 'code2');
```

#### 判断角色是否拥有权限

```php
$result = $role->hasOperations('code1', 'code2');
```

### 用户

#### 获取该用户所有角色

```php
use Imi\AC\AccessControl\Member;

$memberId = 1;
$member = new Member(1);

$roles = $member->getRoles();
```

#### 增加、设置角色

```php
$member->addRoles('code1', 'code2'); // 只在当前基础上增加这两个角色

$member->setRoles('code1', 'code2'); // 将用户角色设置为仅有这两个角色
```

#### 移除角色

```php
$member->removeRoles('code1', 'code2');
```

#### 判断用户是否拥有角色

```php
$result = $member->hasRoles('code1', 'code2');
```

#### 获取用户操作权限

```php
// 数组，成员为 \Imi\AC\Model\Operation 类型
$operations = $member->getOperations();

// 树形结构，成员为 \Imi\AC\Model\Filter\OperationTreeItem 类型，$item->children 为其下一级角色，同样为 \Imi\AC\Model\Filter\OperationTreeItem 类型
$operationTree = $member->getOperationTree();
```

#### 增加、设置权限

```php
$member->addOperations('code1', 'code2'); // 只在当前基础上增加这两个权限

$member->setOperations('code1', 'code2'); // 将角色权限设置为仅有这两个权限
```

#### 移除权限

```php
$member->removeOperations('code1', 'code2');
```

#### 判断用户是否拥有权限

```php
$result = $member->hasOperations('code1', 'code2');
```

## 免费技术支持

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.4
- [Composer](https://getcomposer.org/) >= 2.0
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

`imi-access-control` 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://cdn.jsdelivr.net/gh/imiphp/imi@2.0/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
