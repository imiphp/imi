<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

// Inject
use Imi\Aop\Annotation\Inject;
// Bean
use Imi\Bean\Annotation\Bean;

#[Bean('ConstructorPropertyBean')]
class ConstructorPropertyBean
{
    public function __construct(
        #[Inject]
        protected ?BeanA $beanA = null
    ) {
    }

    public function getBeanA(): BeanA
    {
        return $this->beanA;
    }
}
