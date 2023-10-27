<?php declare(strict_types=1);
echo '<?php'; ?>

declare(strict_types=1);

namespace <?php echo $namespace; ?>;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

<?php echo $classAttributesCode; ?>

class <?php echo $name; ?> extends HttpController
{
    /**
     * query
     */
    #[
        Action,
        Route(url: '', method=['GET'])
    ]
    public function query(): array
    {
        return [1, 2, 3];
    }
    
    /**
     * find
     */
    #[
        Action,
        Route(url: './{id}', method=['GET'])
    ]
    public function find(int $id): array
    {
        return [
            'id'	=>	$id,
        ];
    }

    /**
     * create
     */
    #[
        Action,
        Route(url: '', method=['POST'])
    ]
    public function create(): array
    {
        return [
            'operation'	=>	'create',
            'postData'	=>	$this->request->getParsedBody(),
            'success'	=>	true,
        ];
    }

    /**
     * update
     */
    #[
        Action,
        Route(url: './{id}', method=['PUT'])
    ]
    public function update(int $id): array
    {
        return [
            'id'		=>	$id,
            'operation'	=>	'update',
            'success'	=>	true,
        ];
    }

    /**
     * delete
     */
    #[
        Action,
        Route(url: './{id}', method=['DELETE'])
    ]
    public function delete(int $id): array
    {
        return [
            'id'		=>	$id,
            'operation'	=>	'delete',
            'success'	=>	true,
        ];
    }
}