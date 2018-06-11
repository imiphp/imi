使用 IMI 提供的路由请求方法判断，可以实现 RESTful 风格的 api 开发。

```php
/**
 * @Controller
 */
class Index extends HttpController
{
	/**
	 * @Action()
	 * @Route(url="/", method="GET")
	 * @return void
	 */
	public function index()
	{
		return $this->response->write('GET');
	}

	/**
	 * @Action()
	 * @Route(url="/", method="POST")
	 * @return void
	 */
	public function test()
	{
		return $this->response->write('POST');
	}
}
```