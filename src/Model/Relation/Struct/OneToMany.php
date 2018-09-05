<?php
namespace Imi\Model\Relation\Struct;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;

class OneToMany
{
	use TLeftAndRight;

	public function __construct($className, $propertyName)
	{
		$this->initLeftAndRight($className, $propertyName);
	}
}