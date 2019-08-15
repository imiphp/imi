<?php
namespace Imi\Util;

/**
 * 数组数据基类
 */
class ArrayData implements \ArrayAccess
{
    /**
     * 数据
     */
    protected $data = array ();

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 设置数据
     * @param string $name            
     * @param mixed $value            
     */
    public function set($name, $value = null)
    {
        if (is_array($name))
        {
            // 如果传入数组就合并当前数据
            $this->data = ArrayUtil::recursiveMerge($this->data, $name);
        }
        else
        {
            // 设置数据
            $this->data[$name] = $value;
        }
        return true;
    }

    /**
     * 设置数据
     * @param string $name            
     * @param mixed $value            
     * @return boolean
     */
    public function setVal($name, $value = null)
    {
        $type = gettype($name);
        if('string' === $type)
        {
            $name = explode('.', $name);
        }
        else if('array' !== $type)
        {
            return false;
        }
        $last = array_pop($name);
        $data = &$this->data;
        foreach ($name as $val)
        {
            if (! isset($data[$val]))
            {
                $data[$val] = array ();
            }
            $data = &$data[$val];
        }
        $data[$last] = $value;
        return true;
    }

    /**
     * 获取数据
     * @param string $name            
     * @param mixed $default            
     * @return mixed
     */
    public function &get($name = null, $default = false)
    {
        if (empty($name))
        {
            return $this->data;
        }
        $type = gettype($name);
        if('string' === $type)
        {
            $name = explode('.', $name);
        }
        else if('array' !== $type)
        {
            return $default;
        }
        $result = &$this->data;
        foreach ($name as $value)
        {
            $type = gettype($result);
            if('array' === $type)
            {
                // 数组
                if (isset($result[$value]))
                {
                    $result = &$result[$value];
                }
                else
                {
                    return $default;
                }
            }
            else if('object' === $type)
            {
                // 对象
                if (property_exists($result, $value))
                {
                    $result = &$result->$value;
                }
                else
                {
                    return $default;
                }
            }
            else
            {
                return $default;
            }
        }
        if (count($name) > 0)
        {
            return $result;
        }
        else
        {
            return $default;
        }
    }

    /**
     * 删除数据
     * @param string $name            
     */
    public function remove($name)
    {
        if(!is_array($name))
        {
            $name = func_get_args();
        }
        foreach($name as $val)
        {
            $type = gettype($val);
            if('string' === $type)
            {
                $val = explode('.', $val);
            }
            else if('array' !== $type)
            {
                return false;
            }
            $last = array_pop($val);
            $result = &$this->data;
            foreach ($val as $value)
            {
                if (isset($result[$value]))
                {
                    $result = &$result[$value];
                }
            }
            unset($result[$last]);
        }
        return true;
    }

    /**
     * 清空数据
     */
    public function clear()
    {
        $this->data = array ();
    }

    /**
     * 获取数据的数量
     * @return int
     */
    public function length()
    {
        return count($this->data);
    }

    /**
     * 键名对应的值是否存在
     * @param string $name            
     * @return boolean
     */
    public function exists($name)
    {
        return isset($this->data[$name]);
    }
    
    public function &__get ($key)
    {
        return $this->get($key);
    }
    
    public function __set($key,$value)
    {
        $this->set($key,$value);
    }
    
    public function __isset ($key)
    {
        return null!==$this->get($key,null);
    }
    
    public function __unset($key)
    {
        $this->remove($key);
    }
    
    public function offsetSet($offset,$value)
    {
        if (is_null($offset))
        {
            $this->data[] = $value;
        }
        else
        {
            $this->setVal($offset,$value);
        }
    }
    
    public function offsetExists($offset)
    {
        return null!==$this->get($offset,null);
    }
    
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    
    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    public function &getRawData() 
    {
        return $this->data;
    }
}