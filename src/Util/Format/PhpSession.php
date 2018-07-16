<?php
namespace Imi\Util\Format;

class PhpSession implements IFormat
{
	/**
	 * 编码为存储格式
	 * @param mixed $data
	 * @return mixed
	 */
	public function encode($data)
	{
		$result = '';
		foreach($data as $k => $v)
		{
			$result .= $k . '|' . serialize($v);
		}
		return $result;
	}

	/**
	 * 解码为php变量
	 * @param mixed $data
	 * @return mixed
	 */
	public function decode($data)
	{
		$result = [];
		$offset = 0;
		$length = strlen($data);
		while ($offset < $length)
		{
			if (!strstr(substr($data, $offset), '|'))
			{
				throw new \Exception('invalid data, remaining: ' . substr($data, $offset));
			}
			$pos = strpos($data, '|', $offset);
			$num = $pos - $offset;
			$varname = substr($data, $offset, $num);
			$offset += $num + 1;
			$data = unserialize(substr($data, $offset));
			$result[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $result;
	}
}