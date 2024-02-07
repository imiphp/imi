<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;

if (\PHP_VERSION_ID >= 80100 && !class_exists(EnumController::class, false))
{
    eval(<<<'PHP'
    namespace Imi\Swoole\Test\HttpServer\ApiServer\Controller;
    use Imi\Server\Http\Controller\HttpController;
    use Imi\Server\Http\Route\Annotation\Action;
    use Imi\Server\Http\Route\Annotation\Controller;
    use Imi\Test\Component\Enum\TestEnumBean;
    use Imi\Test\Component\Enum\TestEnumBeanBacked;

    #[Controller(prefix: '/enum/')]
    class EnumController extends HttpController
    {
        #[Action]
        public function test1(TestEnumBean $enum, TestEnumBeanBacked $enumBacked): array
        {
            return [
                'enum'       => $enum,
                'enumBacked' => $enumBacked,
            ];
        }

        #[Action]
        public function test2(TestEnumBean|string $enum = '', TestEnumBeanBacked|string $enumBacked = ''): array
        {
            return [
                'enum'       => $enum,
                'enumBacked' => $enumBacked,
            ];
        }
    }
    PHP);
}
