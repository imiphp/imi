# AOP 拦截请求

在 imi 中更加推荐使用 AOP 来拦截请求。

## AOP

AOP（Aspect-Oriented Programming 面向切面编程）是一种在运行时， 动态地将一段代码片段（切面）切入到指定类、类的指定方法（切入点）等位置， 从而改变其原有的行为的编程思想。可以在不改变原有逻辑的基础上， 增加一些额外的功能，例如日志记录，性能统计，安全控制，事务处理，异常处理等。 
  
详细介绍请见：<https://doc.imiphp.com/v2.0/core/server.html>

## Demo

```php
<?php
namespace ImiApp\ApiServer\Aop;

use Imi\RequestContext;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\ResponseHeader;

/**
 * 拦截控制器动作请求 Demo
 * 
 * @Aspect
 */
class Fuck
{
    /**
     * 直接获取 Response 对象，强行输出
     * 
     * @PointCut(
     *         allow={
     *             "ImiApp\ApiServer\Controller\IndexController::fuck1",
     *         }
     * )
     * @Around
     *
     * @return void
     */
    public function fuck1(AroundJoinPoint $joinPoint)
    {
        /** @var \Imi\Server\Http\Message\Response $response */
        $response = RequestContext::get('response');
        $response->withHeader(ResponseHeader::CONTENT_TYPE, MediaType::APPLICATION_JSON_UTF8)
                 ->write(json_encode([
                    'result' => 'fuck1',
                 ]))
                 ->send();
        // 如果需要执行原动作方法，可以去掉注释
        // $returnValue = $joinPoint->proceed();
        // return $returnValue; // 返回原返回值
    }

    /**
     * 强行修改返回值
     * 
     * @PointCut(
     *         allow={
     *             "ImiApp\ApiServer\Controller\IndexController::fuck2",
     *         }
     * )
     * @Around
     *
     * @return void
     */
    public function fuck2(AroundJoinPoint $joinPoint)
    {
        // 如果需要执行原动作方法，可以去掉注释
        // $returnValue = $joinPoint->proceed();
        // return $returnValue; // 返回原返回值

        // 强行修改返回值
        return [
            'result' => 'fuck2',
        ];
    }
}
```
