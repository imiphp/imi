<?php
namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterFindEventParam extends EventParam
{
	/**
	 * 主键值们
	 * 
	 * @var array
	 */
	public $ids;

	/**
	 * 查询器
	 *
	 * @var \Imi\Model\BaseModel
	 */
	public $model;
}