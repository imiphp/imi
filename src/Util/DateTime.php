<?php
namespace Imi\Util;

/**
 * 日期时间工具类
 */
abstract class DateTime
{
    /**
     * 将一个 \DateInterval，与当前时间进行计算，获取秒数
     *
     * @param \DateInterval $dateInterval
     * @return int
     */
    public static function getSecondsByInterval(\DateInterval $dateInterval)
    {
        $dateTime = new \DateTime;
        $dateTime->add($dateInterval);
        return $dateTime->getTimestamp() - time();
    }

}