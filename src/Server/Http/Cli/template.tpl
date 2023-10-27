<?php declare(strict_types=1);
echo '<?php'; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

<?php echo $classAttributesCode; ?>

class <?php echo $name; ?> extends HttpController
{
    /**
     * index
     */
    #[Action]
    public function index(): array
    {
        return ['success'=>true];
    }
}