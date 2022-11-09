<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

// Inject
use Imi\Aop\Annotation\Inject;
// Bean
use Imi\Bean\Annotation\Bean;

if (\PHP_VERSION_ID >= 80000)
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Bean;

    use Imi\Aop\Annotation\Inject;
    use Imi\Bean\Annotation\Bean;

    if (!class_exists(ConstructorPropertyBean::class, false))
    {
        #[Bean(['ConstructorPropertyBean'])]
        class ConstructorPropertyBean
        {
            public function __construct(
                #[Inject]
                protected ?BeanA $beanA = null
                )
            {
            }

            public function getBeanA(): BeanA
            {
                return $this->beanA;
            }
        }
    }
    PHP);
}
