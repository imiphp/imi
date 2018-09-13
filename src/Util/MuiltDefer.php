<?php
namespace Imi\Util;

abstract class MuiltDefer
{
	public function call(Defer ...$defers)
	{
		$result = [];
		foreach($defers as $defer)
		{
			$result[] = $defer->call();
		}
		return $result;
	}
}