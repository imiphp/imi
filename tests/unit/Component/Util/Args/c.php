<?php

use Imi\Util\Args;

require dirname(__DIR__, 5) . '/vendor/autoload.php';

Args::init(2);

var_dump(Args::get());
var_dump(Args::get('null'));
var_dump(Args::exists('null'));

Args::set('null', 'not null');

var_dump(Args::get());
var_dump(Args::get('null'));
var_dump(Args::exists('null'));
