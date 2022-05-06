# RESTful

[toc]

使用 imi 提供的路由请求方法判断，可以实现 RESTful 风格的 api 开发。

RESTful 风格控制器示例 (query/find/create/update/delete)：

```php
<?php
namespace ImiDemo\HttpDemo\MainServer\Controller;

use Imi\Controller\HttpController;
use Imi\Server\View\Annotation\View;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;

/**
 * @Controller(prefix="/rest")
 */
class Rest extends HttpController
{
	/**
	 * query
	 * 
	 * @Action
	 * @Route(url="", method={"GET"})
	 * @return void
	 */
	public function query()
	{
		return [1, 2, 3];
	}
	
	/**
	 * find
	 * 
	 * @Action
	 * @Route(url="./{id}", method={"GET"})
	 * 
	 * @param int $id
	 * @return void
	 */
	public function find($id)
	{
		return [
			'id'	=>	$id,
		];
	}

	/**
	 * create
	 * 
	 * @Action
	 * @Route(url="", method={"POST"})
	 * @return void
	 */
	public function create()
	{
		return [
			'operation'	=>	'create',
			'postData'	=>	$this->request->getParsedBody(),
			'success'	=>	true,
		];
	}

	/**
	 * update
	 * 
	 * @Action
	 * @Route(url="./{id}", method={"PUT"})
	 * 
	 * @param int $id
	 * @return void
	 */
	public function update($id)
	{
		return [
			'id'		=>	$id,
			'operation'	=>	'update',
			'success'	=>	true,
		];
	}

	/**
	 * delete
	 * 
	 * @Action
	 * @Route(url="./{id}", method={"DELETE"})
	 * 
	 * @param int $id
	 * @return void
	 */
	public function delete($id)
	{
		return [
			'id'		=>	$id,
			'operation'	=>	'delete',
			'success'	=>	true,
		];
	}
}
```