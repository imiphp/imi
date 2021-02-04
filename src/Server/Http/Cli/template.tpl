<?='<?php'; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

/**
 * @Controller("<?= $prefix; ?>")
 * @View(renderType="<?= $render; ?>")
 */
class <?= $name; ?> extends HttpController
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