<?php

declare(strict_types=1);

namespace Imi\Validate;

use Imi\Enum\BaseEnum;

/**
 * 验证器工具类.
 */
class ValidatorHelper
{
    /**
     * 正则验证
     */
    public static function regex(mixed $value, string $rule): bool
    {
        return preg_match($rule, (string) $value) > 0;
    }

    /**
     * 小数验证
     */
    public static function decimal(mixed $value, ?float $min = null, ?float $max = null, ?int $accuracy = null): bool
    {
        return static::number($value, $min, $max, $accuracy) && str_contains((string) $value, '.');
    }

    /**
     * 整数验证
     */
    public static function int(mixed $value, ?int $min = null, ?int $max = null): bool
    {
        // 整数验证
        if ((string) (int) $value !== (string) $value)
        {
            return false;
        }
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

        return true;
    }

    /**
     * 数值验证，允许整数和小数.
     *
     * @param float|int|null $min
     * @param float|int|null $max
     */
    public static function number(mixed $value, $min = null, $max = null, ?int $accuracy = null): bool
    {
        if (!is_numeric($value))
        {
            return false;
        }
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
            $value = (string) $value;

            return \strlen($value) - strrpos($value, '.') - 1 <= $accuracy;
        }

        return true;
    }

    /**
     * 判断文本长度，以字节为单位.
     */
    public static function length(mixed $value, int $min, ?int $max = null): bool
    {
        return isset($value[$min - 1]) && (null === $max || !isset($value[$max]));
    }

    /**
     * 判断文本长度，以字符为单位.
     */
    public static function lengthChar(mixed $value, int $min, ?int $max = null): bool
    {
        $len = mb_strlen((string) $value, 'utf8');
        $result = ($len >= $min);
        if (null !== $max)
        {
            $result = ($result && $len <= $max);
        }

        return $result;
    }

    /**
     * 判断空文本.
     */
    public static function emptyStr(mixed $value): bool
    {
        return '' === $value;
    }

    /**
     * 判断不为空文本.
     */
    public static function notEmptyStr(mixed $value): bool
    {
        return '' !== $value;
    }

    /**
     * 检测邮箱格式.
     */
    public static function email(mixed $email): bool
    {
        return false !== filter_var($email, \FILTER_VALIDATE_EMAIL);
    }

    /**
     * 检测中国手机号码格式.
     */
    public static function cnMobile(mixed $str): bool
    {
        return preg_match('/1\d{10}/', (string) $str) > 0;
    }

    /**
     * 检测中国电话号码格式，支持400、800等.
     */
    public static function tel(mixed $str): bool
    {
        return preg_match('/^(((\d{3,4}-)?(\d{7,8}){1}(-\d{2,4})?)|((\d{3,4}-)?(\d{3,4}){1}(-\d{3,4})))$/', (string) $str) > 0;
    }

    /**
     * 检测中国手机电话号码格式.
     */
    public static function mobile(mixed $str): bool
    {
        return preg_match('/^(1(([35789][0-9])|(47)))\d{8}$/', (string) $str) > 0;
    }

    /**
     * 检测是否符合中国固话或手机格式，支持400、800等.
     */
    public static function phone(mixed $str): bool
    {
        return static::mobile($str) || static::tel($str);
    }

    /**
     * 检测中国邮政编码
     */
    public static function postcode(mixed $str): bool
    {
        return preg_match('/^\d{6}$/', (string) $str) > 0;
    }

    /**
     * 检测URL地址
     */
    public static function url(mixed $str): bool
    {
        return false !== filter_var($str, \FILTER_VALIDATE_URL);
    }

    /**
     * 检测QQ号是否符合规则.
     */
    public static function qq(mixed $str): bool
    {
        return preg_match('/^[1-9]{1}[0-9]{4,10}$/', (string) $str) > 0;
    }

    /**
     * 判断IP地址是否符合IP的格式，ipv4或ipv6.
     */
    public static function ip(mixed $str): bool
    {
        return static::ipv4($str) || static::ipv6($str);
    }

    /**
     * 判断IP地址是否是合法的ipv4格式.
     */
    public static function ipv4(mixed $str): bool
    {
        return false !== filter_var($str, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4);
    }

    /**
     * 判断IP地址是否是合法的ipv6格式.
     */
    public static function ipv6(mixed $str): bool
    {
        return false !== filter_var($str, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6);
    }

    /**
     * 在两个数之间，不包含这2个数字.
     *
     * @param float|int $value
     * @param float|int $max
     * @param float|int $min
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
     */
    public static function gtEqual($value, $num): bool
    {
        return $value >= $num;
    }

    /**
     * 等于.
     */
    public static function equal(mixed $value, mixed $num): bool
    {
        return $value == $num;
    }

    /**
     * 不等于.
     */
    public static function unequal(mixed $value, mixed $num): bool
    {
        return $value != $num;
    }

    /**
     * 比较.
     */
    public static function compare(mixed $valueLeft, string $operation, mixed $valueRight): bool
    {
        return match ($operation)
        {
            '=='    => $valueLeft == $valueRight,
            '!='    => $valueLeft != $valueRight,
            '==='   => $valueLeft === $valueRight,
            '!=='   => $valueLeft !== $valueRight,
            '<'     => $valueLeft < $valueRight,
            '<='    => $valueLeft <= $valueRight,
            '>'     => $valueLeft > $valueRight,
            '>='    => $valueLeft >= $valueRight,
            default => throw new \InvalidArgumentException(sprintf('Unsupport operation %s', $operation)),
        };
    }

    /**
     * 值在范围内.
     *
     * @param string|array $list
     */
    public static function in(mixed $value, $list): bool
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
     * @param string|array $list
     */
    public static function notIn(mixed $value, $list): bool
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
     * @param class-string<BaseEnum> $enumClass
     */
    public static function inEnum(mixed $value, string $enumClass): bool
    {
        return \in_array($value, $enumClass::getValues());
    }

    /**
     * 值不在枚举值范围内.
     *
     * @param class-string<BaseEnum> $enumClass
     */
    public static function notInEnum(mixed $value, string $enumClass): bool
    {
        return !\in_array($value, $enumClass::getValues());
    }

    /**
     * 检测中国居民身份证，支持15位和18位.
     */
    public static function cnIdcard(mixed $id_card): bool
    {
        $id_card = (string) $id_card;
        /**
         * 计算身份证校验码，根据国家标准GB 11643-1999.
         *
         * @param string $idcard_base
         *
         * @return int|false
         */
        $idcard_verify_number = static function () use (&$id_card) {
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
         */
        $idcard_checksum18 = static function () use (&$id_card, $idcard_verify_number): bool {
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
         */
        $idcard_15to18 = static function () use (&$id_card, $idcard_verify_number): string {
            if (15 !== \strlen($id_card))
            {
                return '';
            }
            else
            {
                // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
                if (\in_array(substr($id_card, 12, 3), ['996', '997', '998', '999']))
                {
                    $id_card = substr($id_card, 0, 6) . '18' . substr($id_card, 6, 9);
                }
                else
                {
                    $id_card = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);
                }
            }
            $id_card .= $idcard_verify_number();

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
     */
    public static function text(mixed $str, int $min, ?int $max = null, bool $char = false): bool
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
