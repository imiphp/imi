<?='<?php'?>

namespace <?= $namespace ?>;

use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Controller;

/**
 * @Controller("<?= $prefix ?>")
 * @View(renderType="<?= $render ?>")
 */
class <?= $name ?> extends HttpController
{
	/**
	 * index
	 * 
	 * @Action
	 * @return void
	 */
	public function index()
	{
		return ['success'=>true];
	}
}