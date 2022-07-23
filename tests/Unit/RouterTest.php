<?php /** @noinspection ALL */

namespace Tests\Unit;

use App\Enums\RequestType;
use App\Router\Router;
use PHPUnit\Framework\TestCase;
use Tests\DataProviders\RouteDataProvider;

class RouterTest extends TestCase
{
  public Router $router;

  /**
   * @return void
   */
  protected function setUp(): void
  {
    $this->router = new Router();
  }

  /**
   * Test that routes are resolved
   *
   * @test
   * @param RequestType $method
   * @param string $uri
   * @param callable|array $action
   * @dataProvider \Tests\DataProviders\RouteDataProvider::routeRegisterCases
   * @return void
   */
  public function it_registers_routes(
    RequestType $method, string $uri, callable|array $action
  ): void
  {
    $this->router->register($method, $uri, $action);

    $params = $this->router->getParams($uri);
    $expected = [
      $method->value => [
        $uri => [
          'action' => $action,
          'params' => $params
        ]
      ]
    ];

    $this->assertEquals($expected, $this->router->getRoutes());
  }

  /**
   * Test that routes are resolved
   *
   * @test
   * @param RequestType $method
   * @param string $uri
   * @param callable|array $action
   * @dataProvider Tests\DataProviders\RouteDataProvider::routeResolveCases
   * @return void
   */
  public function it_resolves_routes(
    RequestType $method, string $uri, callable|array $action
  ): void
  {
    $this->router->register($method, $uri, $action);

    $params = $this->router->getParams($uri);

    if (is_callable($action)) {
      $expected = call_user_func($action);
    } else {
      [$class, $methodName] = $action;
      $expected = call_user_func_array([new $class, $methodName], []);
    }

    $this->assertEquals($expected, $this->router->resolve($method, $uri));
  }
}