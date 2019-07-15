<?php
$composerJson = json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'), true);
unset($composerJson['require-dev']['phpunit/phpunit']);
file_put_contents(dirname(__DIR__) . '/composer.json', json_encode($composerJson));
