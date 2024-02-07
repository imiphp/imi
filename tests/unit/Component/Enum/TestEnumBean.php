<?php

declare(strict_types=1);

namespace Imi\Test\Component\Enum;

if (\PHP_VERSION_ID >= 80100 && !enum_exists(TestEnumBean::class, false))
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Enum;
    enum TestEnumBean implements \JsonSerializable
    {
        case A;
        case B;

        public function jsonSerialize(): string
        {
            return $this->name;
        }
    }
    PHP);
}
