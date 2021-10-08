<?php

declare(strict_types=1);

global $COMPONENTS_NS;

use Imi\Util\Text;

$components = [];
foreach ($COMPONENTS_NS as $name => $ns)
{
    $components[Text::toPascalName(str_replace('-', '_', $name))] = $ns;
}

return [
    'components' => $components,
];
