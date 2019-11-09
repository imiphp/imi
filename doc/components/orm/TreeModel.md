# TreeModel

## 介绍

我们开发中有一些表是树形结构的，比如地区、分销关系等。imi 特地为此做了增强支持，可以非常方便地操作树形结构的数据表。

## 定义模型

```php
<?php
namespace Imi\Test\Component\Model;

use Imi\Model\Model;
use Imi\Model\Tree\TTreeModel;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Tree\Annotation\TreeModel;
use Imi\Test\Component\Model\Base\TreeBase;

/**
 * Tree
 * @Entity
 * @TreeModel(parentField="parent_id", childrenField="children")
 * @Table(name="tb_tree", id={"id"})
 */
class Tree extends TreeBase // TreeBase 为通过 generate/model 工具生成出来的基类
{
    use TTreeModel;

    /**
     * 子节点集合
     * 
     * @Column(virtual=true)
     *
     * @var static[]
     */
    protected $children = [];

    /**
     * Get 子节点集合
     *
     * @return static[]
     */ 
    public function &getChildren()
    {
        return $this->children;
    }

    /**
     * Set 子节点集合
     *
     * @param static[] $children
     *
     * @return self
     */ 
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

}
```

## 使用

### 方法列表

#### getChildIds

获取一级子节点的ID们

```php
$item = Tree::find(1);
$ids = $item->getChildIds(); // 当前对象子节点
$ids = $item->getChildIds(123); // 指定 ID 子节点
```

#### getChildrenIds

获取下属 N 级子节点的ID

非递归实现，相比递归实现性能更高，更省内存

```php
$item = Tree::find(1);
$item->getChildrenIds(null, true); // 包含父级ID（1）
$item->getChildrenIds(); // 不包含父级ID（1）
$item->getChildrenIds(123, false, 1); // 不包含父级ID（123），限制获取子层级为1级
```

#### getChildrenList

获取子成员对象列表，可以指定层级，默认无限级

```php
$item = Tree::find(1);
$tree = $item->getChildrenList(); // 获取父级ID（1）下所有记录
$tree = $item->getChildrenList(null, 1); // 获取父级ID（1）下所有记录，层级1级
$tree = $item->getChildrenList(123, 1); // 获取父级ID（123）下所有记录，层级1级

```

#### getParent

获取父级对象

```php
$item = Tree::find(1);
$parentItem = $item->getParent();
```

#### getParents

获取所有父级对象列表

```php
$item = Tree::find(1);
$parentItemList = $item->getParents();
```

#### getAssocList

获取关联列表，`@TreeModel`注解中配置的`childrenField`属性生效，放入配置的属性中

```php
$list = Tree::getAssocList(); // 所有数据

// 指定只显示 ID 为 1、2、3 的数据，他们之间如果有上下级关系，会在 children 属性中体现
$query = Tree::query()->whereIn('id', [1, 2, 3]);
$list = Tree::getAssocList($query);
$children = $list[0]->children; // $children 与 $list 结构相同
```
