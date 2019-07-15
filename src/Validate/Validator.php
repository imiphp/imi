<?php
namespace Imi\Validate;

use Imi\Util\ObjectArrayHelper;
use Imi\Util\Traits\TBeanRealClass;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Validate\Annotation\Condition;
use Imi\Validate\Annotation\ValidateValue;

/**
 * 验证器类
 */
class Validator implements IValidator
{
    use TBeanRealClass;

    /**
     * 验证器中的数据
     *
     * @var array|object
     */
    protected $data;

    /**
     * 第一条失败信息
     *
     * @var string
     */
    protected $message;

    /**
     * 验证结果
     *
     * @var array
     */
    protected $results;

    /**
     * 第一条失败的规则
     *
     * @var \Imi\Validate\Annotation\Condition
     */
    protected $failRule;

    /**
     * 验证失败的规则列表
     *
     * @var array
     */
    protected $failRules;

    /**
     * 校验规则
     *
     * @var \Imi\Validate\Annotation\Condition[]
     */
    private $rules;

    /**
     * 注解校验规则集合
     *
     * @var array
     */
    private static $annotationRules;

    public function __construct(&$data = [], $rules = null)
    {
        $this->data = &$data;
        if(null === $rules)
        {
            $this->rules = $this->getAnnotationRules();
        }
        else
        {
            $this->rules = $rules;
        }
    }

    /**
     * 设置验证器中的数据
     *
     * @param array|object $data
     * @return void
     */
    public function setData(&$data)
    {
        $this->data = &$data;
    }

    /**
     * 获取验证器中的数据
     *
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 设置校验规则
     *
     * @param \Imi\Validate\Annotation\Condition[] $rules
     * 
     * @return void
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * 获得所有校验规则
     *
     * @return \Imi\Validate\Annotation\Condition[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * 获得所有注解校验规则
     *
     * @return \Imi\Validate\Annotation\Condition[]
     */
    public function getAnnotationRules()
    {
        $className = static::__getRealClassName();
        if(!isset(self::$annotationRules[$className]))
        {
            self::$annotationRules[$className] = AnnotationManager::getClassAnnotations($className);

            $propertyAnnotations = AnnotationManager::getPropertiesAnnotations($className);
            foreach($propertyAnnotations as $propertyName => $tAnnotations)
            {
                foreach($tAnnotations as $annotation)
                {
                    $annotation = clone $annotation;
                    $annotation->name = $propertyName;
                    self::$annotationRules[$className][] = $annotation;
                }
            }
        }
        return self::$annotationRules[$className];
    }

    /**
     * 验证，返回是否通过
     * 当遇到不通过时结束验证流程
     *
     * @return boolean
     */
    public function validate()
    {
        return $this->__validateAll($this->data, true);
    }

    /**
     * 验证所有，返回是否通过
     *
     * @return boolean
     */
    public function validateAll()
    {
        return $this->__validateAll($this->data, false);
    }

    /**
     * 获取第一条失败信息
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 获取所有验证结果
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * 内部验证方法
     *
     * @param array|object $data
     * @param boolean $break 遇到验证失败是否中断
     * @return boolean
     */
    protected function __validateAll(&$data, $break)
    {
        $this->message = null;
        $this->results = [];
        $result = true;
        foreach($this->rules as $annotation)
        {
            if(!$annotation instanceof Condition)
            {
                continue;
            }
            if(!$this->validateByAnnotation($data, $annotation))
            {
                if(null === $annotation->default)
                {
                    $result = false;
                    $message = $this->buildMessage($data, $annotation);
                    $this->results[$annotation->name][] = $message;
                    $this->failRules[$annotation->name][] = $annotation;
                    if(null === $this->message)
                    {
                        $this->message = $message;
                        $this->failRule = $annotation;
                    }
                    if($break)
                    {
                        break;
                    }
                }
                else
                {
                    ObjectArrayHelper::set($data, $annotation->name, $annotation->default);
                }
            }
        }
        return $result;
    }

    /**
     * 组建消息
     *
     * @param array|object $data
     * @param \Imi\Validate\Annotation\Condition $annotation
     * @return string
     */
    protected function buildMessage($data, $annotation)
    {
        $message = $annotation->message;
        if(false !== strpos($message, '{:value}'))
        {
            $message = str_replace('{:value}', ObjectArrayHelper::get($data, $annotation->name), $message);
        }
        $message = preg_replace_callback('/\{([^\}]+)\}/', function($matches) use($data, $annotation){
            $name = $matches[1];
            if(isset($name[0]) && ':' === $name[0])
            {
                $name = substr($name, 1);
                $list = explode('.', $name, 2);
                if('data' === $list[0])
                {
                    if(isset($list[1]))
                    {
                        return ObjectArrayHelper::get($data, $list[1]);
                    }
                    else
                    {
                        return $data;
                    }
                }
            }
            else
            {
                return ObjectArrayHelper::get($annotation, $name);
            }
            return null;
        }, $message);
        return $message;
    }

    /**
     * 验证
     *
     * @param array|object $data
     * @param \Imi\Validate\Annotation\Condition $annotation
     * @return boolean
     */
    protected function validateByAnnotation($data, $annotation)
    {
        $args = [];
        foreach($annotation->args as $arg)
        {
            $value = $this->getArgValue($data, $arg, $annotation);
            if($value instanceof ValidateValue)
            {
                $value = $this->getArgValue($data, $value->value, $annotation, false);
            }
            $args[] = $value;
        }
        $callable = $annotation->callable;
        if(is_array($callable) && isset($callable[0]) && '$this' === $callable[0])
        {
            $callable[0] = $this;
        }
        $result = $callable(...$args);
        if($annotation->inverseResult)
        {
            return !$result;
        }
        else
        {
            return $result;
        }
    }

    /**
     * 获取参数值
     *
     * @param array|object $data
     * @param mixed $arg
     * @param \Imi\Validate\Annotation\Condition $annotation
     * @param boolean $includeAnnotationProperty
     * @return mixed
     */
    protected function getArgValue($data, $arg, $annotation, $includeAnnotationProperty = true)
    {
        if(!is_string($arg))
        {
            return $arg;
        }
        else if(preg_match('/\{:([^\}]+)\}/', $arg, $matches) > 0)
        {
            $argName = $matches[1];
            if('value' === $argName)
            {
                return ObjectArrayHelper::get($data, $annotation->name);
            }
            $list = explode('.', $argName, 2);
            if('data' === $list[0])
            {
                if(isset($list[1]))
                {
                    return ObjectArrayHelper::get($data, $list[1]);
                }
                else
                {
                    return $data;
                }
            }
        }
        else if($includeAnnotationProperty && preg_match('/\{([^\}]+)\}/', $arg, $matches) > 0)
        {
            $argName = $matches[1];
            return ObjectArrayHelper::get($annotation, $argName);
        }
        else
        {
            return $arg;
        }
        return null;
    }

    /**
     * Get 第一条失败的规则
     *
     * @return  \Imi\Validate\Annotation\Condition
     */ 
    public function getFailRule()
    {
        return $this->failRule;
    }

    /**
     * Get 验证失败的规则列表
     *
     * @return  array
     */ 
    public function getFailRules()
    {
        return $this->failRules;
    }
}