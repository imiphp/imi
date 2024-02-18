<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;

if (\PHP_VERSION_ID >= 80100 && !class_exists(ParamsController::class, false))
{
    eval(<<<'PHP'
    namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;
    use Imi\Server\Http\Controller\HttpController;
    use Imi\Server\Http\Route\Annotation\Action;
    use Imi\Server\Http\Route\Annotation\Controller;
    use Imi\Test\Component\Enum\TestEnumBean;
    use Imi\Test\Component\Enum\TestEnumBeanBacked;
    use Imi\Test\Component\Enum\TestEnumBeanBackedInt;

    #[Controller(prefix: '/params/')]
    class ParamsController extends HttpController
    {
        #[Action]
        public function test1(TestEnumBean $enum, TestEnumBeanBacked $enumBacked, TestEnumBeanBackedInt $enumBackedInt): array
        {
            return [
                'enum'          => $enum,
                'enumBacked'    => $enumBacked,
                'enumBackedInt' => $enumBackedInt,
            ];
        }

        #[Action]
        public function test2(TestEnumBean|string $enum = '', TestEnumBeanBacked|string $enumBacked = '', TestEnumBeanBackedInt|int $enumBackedInt = 0): array
        {
            return [
                'enum'          => $enum,
                'enumBacked'    => $enumBacked,
                'enumBackedInt' => $enumBackedInt,
            ];
        }

        #[Action]
        public function test3(string|int $value): array
        {
            return [
                'value' => $value,
            ];
        }
    }
    PHP);
}
