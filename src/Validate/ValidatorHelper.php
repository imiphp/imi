<?php

declare(strict_types=1);

namespace Imi\Validate;

/**
 * 验证器工具类.
 */
class ValidatorHelper
{
    /**
     * 正则验证
     *
     * @param mixed  $value
     * @param string $rule
     *
     * @return bool
     */
    public static function regex($value, string $rule): bool
    {
        return preg_match($rule, (string) $value) > 0;
    }

    /**
     * 小数验证
     *
     * @param mixed      $value
     * @param float|null $min
     * @param float|null $max
     * @param int|null   $accuracy
     *
     * @return bool
     */
    public static function decimal($value, ?float $min = null, ?float $max = null, ?int $accuracy = null): bool
    {
        // 最小值
        if (null !== $min && $value < $min)
        {
            return false;
        }
        // 最大值
        if (null !== $max && $value > $max)
        {
            return false;
        }
        // 小数精度
        if (null !== $accuracy)
        {
            return preg_match('/^-?\d+\.\d{1,' . ((int) $accuracy) . '}$/', (string) $value) > 0;
        }

        return is_numeric($value) && false !== strpos((string) $value, '.');
    }

    /**
     * 整数验证
     *
     * @param mixed    $value
     * @param int|null $min
     * @param int|null $max
     *
     * @return bool
     */
    public static function int($value, ?int $min = null, ?int $max = null): bool
    {
        // 最小值
        if (null !== $min && $value < $min)
        {
            return false;
        }
        // 最大值
        if (null !== $max && $value > $max)
        {
            return false;
        }
        // 整数验证
        return (string) (int) $value === (string) $value;
    }

    /**
     * 数值验证，允许整数和小数.
     *
     * @param mixed          $value
     * @param float|int|null $min
     * @param float|int|null $max
     * @param int|null       $accuracy
     *
     * @return bool
     */
    public static function number($value, $min = null, $max = null, ?int $accuracy = null): bool
    {
        // 最小值
        if (null !== $min && $value < $min)
        {
            return false;
        }
        // 最大值
        if (null !== $max && $value > $max)
        {
            return false;
        }
        // 小数精度
        if (null !== $accuracy)
        {
            return preg_match('/^-?\d+(\.\d{1,' . ((int) $accuracy) . '})?$/', (string) $value) > 0;
        }

        return is_numeric($value);
    }

    /**
     * 判断文本长度，以字节为单位.
     *
     * @param string $val
     * @param int    $min
     * @param int    $max
     *
     * @return bool
     */
    public static function length(string $val, int $min, ?int $max = null): bool
    {
        return isset($val[$min - 1]) && (null === $max || !isset($val[$max]));
    }

    /**
     * 判断文本长度，以字符为单位.
     *
     * @param string   $val
     * @param int      $min
     * @param int|null $max
     *
     * @return bool
     */
    public static function lengthChar(string $val, int $min, ?int $max = null): bool
    {
        $len = mb_strlen($val, 'utf8');
        $result = ($len >= $min);
        if (null !== $max)
        {
            $result = ($result && $len <= $max);
        }

        return $result;
    }

    /**
     * 判断空文本.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function emptyStr(string $str): bool
    {
        return '' === $str;
    }

    /**
     * 判断不为空文本.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function notEmptyStr(string $str): bool
    {
        return '' !== $str;
    }

    /**
     * 检测邮箱格式.
     *
     * @param string $email
     *
     * @return bool
     */
    public static function email(string $email): bool
    {
        return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $email) > 0;
    }

    /**
     * 检测中国手机号码格式.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function cnMobile(string $str): bool
    {
        return preg_match('/1\d{10}/', $str) > 0;
    }

    /**
     * 检测中国电话号码格式，支持400、800等.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function tel(string $str): bool
    {
        return preg_match('/^(((\d{3,4}-)?(\d{7,8}){1}(-\d{2,4})?)|((\d{3,4}-)?(\d{3,4}){1}(-\d{3,4})))$/', $str) > 0;
    }

    /**
     * 检测中国手机电话号码格式.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function mobile(string $str): bool
    {
        return preg_match('/^(1(([35789][0-9])|(47)))\d{8}$/', $str) > 0;
    }

    /**
     * 检测是否符合中国固话或手机格式，支持400、800等.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function phone(string $str): bool
    {
        return static::mobile($str) || static::tel($str);
    }

    /**
     * 检测中国邮政编码
     *
     * @param string $str
     *
     * @return bool
     */
    public static function postcode(string $str): bool
    {
        return preg_match('/^\d{6}$/', $str) > 0;
    }

    /**
     * 检测URL地址
     *
     * @param string $str
     *
     * @return bool
     */
    public static function url(string $str): bool
    {
        return preg_match('/^([a-z]*:\/\/)?(localhost|(([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?))\.?\/?/i', $str) > 0;
    }

    /**
     * 检测QQ号是否符合规则.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function qq(string $str): bool
    {
        return preg_match('/^[1-9]{1}[0-9]{4,10}$/', $str) > 0;
    }

    /**
     * 判断IP地址是否符合IP的格式，ipv4或ipv6.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function ip(string $str): bool
    {
        return static::ipv4($str) || static::ipv6($str);
    }

    /**
     * 判断IP地址是否是合法的ipv4格式.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function ipv4(string $str): bool
    {
        return preg_match('/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/', $str) > 0;
    }

    /**
     * 判断IP地址是否是合法的ipv6格式.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function ipv6(string $str): bool
    {
        return preg_match('/\A
(?:
(?:
(?:[a-f0-9]{1,4}:){6}
|
::(?:[a-f0-9]{1,4}:){5}
|
(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}
|
(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}
|
(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}
|
(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:
|
(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
)
(?:
[a-f0-9]{1,4}:[a-f0-9]{1,4}
|
(?:(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
)
|
(?:
(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}
|
(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
)
)\Z/ix', $str) > 0;
    }

    /**
     * 在两个数之间，不包含这2个数字.
     *
     * @param float|int $value
     * @param float|int $max
     * @param float|int $min
     *
     * @return bool
     */
    public static function between($value, $min, $max): bool
    {
        return $value > $min && $value < $max;
    }

    /**
     * 在两个数之间，包含这2个数字.
     *
     * @param float|int $value
     * @param float|int $max
     * @param float|int $min
     *
     * @return bool
     */
    public static function betweenEqual($value, $min, $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    /**
     * 小于.
     *
     * @param float|int $value
     * @param float|int $num
     *
     * @return bool
     */
    public static function lt($value, $num): bool
    {
        return $value < $num;
    }

    /**
     * 小于等于.
     *
     * @param float|int $value
     * @param float|int $num
     *
     * @return bool
     */
    public static function ltEqual($value, $num): bool
    {
        return $value <= $num;
    }

    /**
     * 大于.
     *
     * @param float|int $value
     * @param float|int $num
     *
     * @return bool
     */
    public static function gt($value, $num): bool
    {
        return $value > $num;
    }

    /**
     * 大于等于.
     *
     * @param float|int $value
     * @param float|int $num
     *
     * @return bool
     */
    public static function gtEqual($value, $num): bool
    {
        return $value >= $num;
    }

    /**
     * 等于.
     *
     * @param mixed $value
     * @param mixed $num
     *
     * @return bool
     */
    public static function equal($value, $num): bool
    {
        return $value == $num;
    }

    /**
     * 不等于.
     *
     * @param mixed $value
     * @param mixed $num
     *
     * @return bool
     */
    public static function unequal($value, $num): bool
    {
        return $value != $num;
    }

    /**
     * 比较.
     *
     * @param mixed  $valueLeft
     * @param string $operation
     * @param mixed  $valueRight
     *
     * @return bool
     */
    public static function compare($valueLeft, string $operation, $valueRight): bool
    {
        //eval is not safe、abandoned
        switch ($operation) {
            case '==':
                return $valueLeft == $valueRight;
            case '!=':
                return $valueLeft != $valueRight;
            case '===':
                return $valueLeft === $valueRight;
            case '!==':
                return $valueLeft !== $valueRight;
            case '<':
                return $valueLeft < $valueRight;
            case '<=':
                return $valueLeft <= $valueRight;
            case '>':
                return $valueLeft > $valueRight;
            case '>=':
                return $valueLeft >= $valueRight;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupport operation %s', $operation));
        }
    }

    /**
     * 值在范围内.
     *
     * @param mixed $value
     * @param array $list
     *
     * @return bool
     */
    public static function in($value, array $list): bool
    {
        if (!\is_array($list))
        {
            $list = explode(',', $list);
        }

        return \in_array($value, $list);
    }

    /**
     * 值不在范围内.
     *
     * @param mixed $value
     * @param array $list
     *
     * @return bool
     */
    public static function notIn($value, array $list): bool
    {
        if (!\is_array($list))
        {
            $list = explode(',', $list);
        }

        return !\in_array($value, $list);
    }

    /**
     * 值在枚举值范围内.
     *
     * @param mixed  $value
     * @param string $enumClass
     *
     * @return bool
     */
    public static function inEnum($value, string $enumClass): bool
    {
        return \in_array($value, $enumClass::getValues());
    }

    /**
     * 值不在枚举值范围内.
     *
     * @param mixed  $value
     * @param string $enumClass
     *
     * @return bool
     */
    public static function notInEnum($value, string $enumClass): bool
    {
        return !\in_array($value, $enumClass::getValues());
    }

    /**
     * 检测中国居民身份证，支持15位和18位.
     *
     * @param string $id_card
     *
     * @return bool
     */
    public static function cnIdcard(string $id_card): bool
    {
        /**
         * 计算身份证校验码，根据国家标准GB 11643-1999.
         *
         * @param string $idcard_base
         *
         * @return int|false
         */
        $idcard_verify_number = function () use (&$id_card) {
            if (17 !== \strlen($id_card))
            {
                return false;
            }
            // 加权因子
            $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            // 校验码对应值
            $verify_number_list = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            $checksum = 0;
            $len = \strlen($id_card);
            for ($i = 0; $i < $len; ++$i)
            {
                $checksum += (int) $id_card[$i] * $factor[$i];
            }
            $mod = $checksum % 11;
            $verify_number = $verify_number_list[$mod];

            return $verify_number;
        };
        /**
         * 18位身份证校验码有效性检查.
         *
         * @param string $idcard
         *
         * @return bool
         */
        $idcard_checksum18 = function () use (&$id_card, $idcard_verify_number): bool {
            if (18 !== \strlen($id_card))
            {
                return false;
            }
            $id_card1 = $id_card;
            $id_card = substr($id_card, 0, 17);

            return $idcard_verify_number() === strtoupper($id_card1[17]);
        };
        /**
         * 将15位身份证升级到18位.
         *
         * @param string $idcard
         *
         * @return string
         */
        $idcard_15to18 = function () use (&$id_card, $idcard_verify_number): string {
            if (15 !== \strlen($id_card))
            {
                return '';
            }
            else
            {
                // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
                if (false !== array_search(substr($id_card, 12, 3), ['996', '997', '998', '999']))
                {
                    $id_card = substr($id_card, 0, 6) . '18' . substr($id_card, 6, 9);
                }
                else
                {
                    $id_card = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);
                }
            }
            $id_card = $id_card . $idcard_verify_number();

            return $id_card;
        };
        $len = \strlen($id_card);
        if (18 === $len)
        {
            return $idcard_checksum18();
        }
        elseif (15 === $len)
        {
            $id_card = $idcard_15to18();

            return $idcard_checksum18();
        }
        else
        {
            return false;
        }
    }

    /**
     * 文本验证
     *
     * @param string   $str
     * @param int      $min
     * @param int|null $max
     * @param bool     $char
     *
     * @return bool
     */
    public static function text(string $str, int $min, ?int $max = null, bool $char = false): bool
    {
        if ($char)
        {
            return static::lengthChar($str, $min, $max);
        }
        else
        {
            return static::length($str, $min, $max);
        }
    }
}
