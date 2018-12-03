<?php
namespace Imi\Util;

use Imi\Util\Interfaces\IArrayable;

/**
 * 过滤字段的列表，每一个成员应该是数组或对象
 */
class FilterableList implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable
{
    /**
     * 模式
     * allow-白名单
     * deny-黑名单
     *
     * @var string
     */
    private $mode;

    /**
     * 字段名数组
     * 
     * 为null则不过滤
     *
     * @var string[]|null
     */
    private $fields;
    
    /**
     * 数组列表
     *
     * @var array
     */
    private $list = [];

    public function __construct($list = [], $fields = null, $mode = 'allow')
    {
        $this->mode = $mode;
        $this->fields = $fields;
        $this->list = $this->parseList($list);
    }

    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    public function &offsetGet($offset)
    {
        if(isset($this->list[$offset]))
        {
            $value = &$this->list[$offset];
        }
        else
        {
            $value = null;
        }
        return $value;
    }

    public function offsetSet($offset, $value)
    {
        if(!$value instanceof $this->itemType)
        {
            throw new \InvalidArgumentException('ArrayList item must be an instance of ' . $this->itemType);
        }
        if(null === $offset)
        {
            $this->list[] = $value;
        }
        else
        {
            $this->list[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if(isset($this->list[$offset]))
        {
            unset($this->list[$offset]);
        }
    }

    public function current()
    {
        return current($this->list);
    }

    public function key()
    {
        return key($this->list);
    }

    public function next()
    {
        next($this->list);
    }

    public function rewind()
    {
        reset($this->list);
    }

    public function valid()
    {
        return null !== key($this->list);
    }

    /**
     * 将当前对象作为数组返回
     * @return array
     */
    public function toArray(): array
    {
        return $this->list;
    }

    /**
     * json 序列化
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 从数组列表中移除
     *
     * @param mixed ...$value
     * @return void
     */
    public function remove(...$value)
    {
        $this->list = ArrayUtil::remove($this->list, ...$value);
    }

    /**
     * 清空
     *
     * @return void
     */
    public function clear()
    {
        $this->list = [];
    }

    /**
     * 加入数组列表
     *
     * @param mixed ...$value
     * @return void
     */
    public function append(...$value)
    {
        foreach($value as $row)
        {
            $this[] = $row;
        }
    }

    /**
     * 数组列表长度
     *
     * @return int
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * 处理列表
     *
     * @param array $list
     * @return void
     */
    private function parseList($list)
    {
        if(null === $this->fields)
        {
            return $list;
        }
        $result = [];
        if('allow' === $this->mode)
        {
            foreach($list as $item)
            {
                if(is_object($item))
                {
                    $item = clone $item;
                }
                foreach($item as $field => $value)
                {
                    if(!in_array($field, $this->fields))
                    {
                        unset($item[$field]);
                    }
                }
                $result[] = $item;
            }
        }
        else if('deny' === $this->mode)
        {
            foreach($list as $item)
            {
                if(is_object($item))
                {
                    $item = clone $item;
                }
                foreach($this->fields as $field)
                {
                    ObjectArrayHelper::remove($item, $field);
                    $result[] = $item;
                }
            }
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('unknow mode %s', $this->mode));
        }
        return $result;
    }
}