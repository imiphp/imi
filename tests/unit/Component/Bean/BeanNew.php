<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

use Imi\Bean\Annotation\Bean;

#[Bean(name: 'BeanNew', instanceType: Bean::INSTANCE_TYPE_EACH_NEW)]
class BeanNew
{
}
