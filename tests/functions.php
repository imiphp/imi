<?php
function testEnv($name, $default = null)
{
    $result = getenv($name);
    if(false === $result)
    {
        return $default;
    }
    return $result;
}
