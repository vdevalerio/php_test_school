<?php declare(strict_types=1);

namespace Tests\Unit {

use App\Core\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();

        $_POST                     = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/';
    }

    // -------------------------------------------------------------------------
    // Route registration
    // -------------------------------------------------------------------------

    public function test_registration_methods_return_same_instance(): void
    {
        [$uri, $action] = ['/a', 'B@c'];
        $this->assertSame($this->router, $this->router->get($uri, $action));
        $this->assertSame($this->router, $this->router->post($uri, $action));
        $this->assertSame($this->router, $this->router->put($uri, $action));
        $this->assertSame($this->router, $this->router->delete($uri, $action));
    }

    public function test_registering_route_does_not_dispatch_it(): void
    {
        $this->router->get('/turmas', 'NonExistentController@index');

        $this->assertTrue(true);
    }

    // -------------------------------------------------------------------------
    // toRegex — Static URIs
    // -------------------------------------------------------------------------

    public function test_static_uri_matches_exactly(): void
    {
        $regex = $this->toRegex('/turmas');

        $this->assertMatchesRegularExpression($regex, '/turmas');
    }

    public function test_static_uri_does_not_match_with_trailing_slash(): void
    {
        $regex = $this->toRegex('/turmas');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas/');
    }

    public function test_static_uri_does_not_match_with_extra_segment(): void
    {
        $regex = $this->toRegex('/turmas');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas/1');
    }

    public function test_static_uri_does_not_match_partial(): void
    {
        $regex = $this->toRegex('/turmas');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas-antigas');
    }

    // -------------------------------------------------------------------------
    // toRegex — URIs with placeholders
    // -------------------------------------------------------------------------

    public function test_placeholder_matches_numeric_segment(): void
    {
        $regex = $this->toRegex('/turmas/{id}');

        $this->assertMatchesRegularExpression($regex, '/turmas/1');
        $this->assertMatchesRegularExpression($regex, '/turmas/999');
    }

    public function test_placeholder_matches_string_segment(): void
    {
        $regex = $this->toRegex('/turmas/{id}');

        $this->assertMatchesRegularExpression($regex, '/turmas/abc');
        $this->assertMatchesRegularExpression($regex, '/turmas/turma-a');
    }

    public function test_placeholder_does_not_match_missing_segment(): void
    {
        $regex = $this->toRegex('/turmas/{id}');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas');
        $this->assertDoesNotMatchRegularExpression($regex, '/turmas/');
    }

    public function test_placeholder_does_not_cross_slash(): void
    {
        $regex = $this->toRegex('/turmas/{id}');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas/1/edit');
    }

    public function test_placeholder_captures_named_group(): void
    {
        $regex = $this->toRegex('/turmas/{id}');
        preg_match($regex, '/turmas/42', $matches);

        $this->assertSame('42', $matches['id']);
    }

    // -------------------------------------------------------------------------
    // toRegex — Nested URIs
    // -------------------------------------------------------------------------

    public function test_nested_static_segment_after_placeholder_matches(): void
    {
        $regex = $this->toRegex('/turmas/{id}/edit');

        $this->assertMatchesRegularExpression($regex, '/turmas/5/edit');
    }

    public function test_nested_static_segment_does_not_match_without_suffix(): void
    {
        $regex = $this->toRegex('/turmas/{id}/edit');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas/5');
    }

    public function test_nested_static_segment_does_not_match_extra_suffix(): void
    {
        $regex = $this->toRegex('/turmas/{id}/edit');

        $this->assertDoesNotMatchRegularExpression($regex, '/turmas/5/edit/extra');
    }

    public function test_multiple_placeholders_capture_correct_values(): void
    {
        $regex = $this->toRegex('/turmas/{turma_id}/alunos/{aluno_id}');
        preg_match($regex, '/turmas/3/alunos/7', $matches);

        $this->assertSame('3', $matches['turma_id']);
        $this->assertSame('7', $matches['aluno_id']);
    }

    // -------------------------------------------------------------------------
    // dispatch — method override via $_POST['_method']
    // -------------------------------------------------------------------------

    public function test_method_override_is_normalized_to_uppercase(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method']          = 'put';

        $this->assertSame('PUT', $this->router->resolveMethod());
    }

    public function test_method_override_delete_is_recognized(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method']          = 'DELETE';

        $this->assertSame('DELETE', $this->router->resolveMethod());
    }

    public function test_without_override_uses_server_method(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->assertSame('GET', $this->router->resolveMethod());
    }

    public function test_server_method_is_normalized_to_uppercase(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'get';

        $this->assertSame('GET', $this->router->resolveMethod());
    }

    // -------------------------------------------------------------------------
    // resolveUri
    // -------------------------------------------------------------------------

    public function test_resolve_uri_returns_path_from_request_uri(): void
    {
        $_SERVER['REQUEST_URI'] = '/turmas';

        $this->assertSame('/turmas', $this->resolveUri());
    }

    public function test_resolve_uri_strips_query_string(): void
    {
        $_SERVER['REQUEST_URI'] = '/turmas?error=campos_obrigatorios';

        $this->assertSame('/turmas', $this->resolveUri());
    }

    public function test_resolve_uri_handles_nested_path(): void
    {
        $_SERVER['REQUEST_URI'] = '/turmas/5/edit';

        $this->assertSame('/turmas/5/edit', $this->resolveUri());
    }

    // -------------------------------------------------------------------------
    // matchesRoute
    // -------------------------------------------------------------------------

    public function test_matches_route_returns_true_for_matching_method_and_uri(): void
    {
        $route   = [
            'method' => 'GET',
            'uri'    => '/turmas',
            'action' => 'TurmaController@index'
        ];
        $matches = null;

        $result = $this->matchesRoute($route, 'GET', '/turmas', $matches);

        $this->assertTrue($result);
    }

    public function test_matches_route_returns_false_for_wrong_method(): void
    {
        $route   = [
            'method' => 'GET',
            'uri'    => '/turmas',
            'action' => 'TurmaController@index'
        ];
        $matches = null;

        $result = $this->matchesRoute($route, 'POST', '/turmas', $matches);

        $this->assertFalse($result);
    }

    public function test_matches_route_returns_false_for_wrong_uri(): void
    {
        $route   = [
            'method' => 'GET',
            'uri'    => '/turmas',
            'action' => 'TurmaController@index'
        ];
        $matches = null;

        $result = $this->matchesRoute($route, 'GET', '/alunos', $matches);

        $this->assertFalse($result);
    }

    public function test_matches_route_matches_delete_method(): void
    {
        $route   = [
            'method' => 'DELETE',
            'uri'    => '/turmas/{id}',
            'action' => 'TurmaController@destroy'
        ];
        $matches = null;

        $result = $this->matchesRoute($route, 'DELETE', '/turmas/7', $matches);

        $this->assertTrue($result);
    }

    // -------------------------------------------------------------------------
    // dispatch
    // -------------------------------------------------------------------------

    public function test_dispatch_calls_abort_when_no_route_matches(): void
    {
        $router = new TestableRouter();
        $_SERVER['REQUEST_URI']    = '/non-existent';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        try {
            $router->dispatch();
            $this->fail('Expected RuntimeException from terminate');
        } catch (\RuntimeException $e) {
            $this->assertSame('terminated', $e->getMessage());
        } finally {
            ob_end_clean();
        }
    }

    public function test_dispatch_calls_action_when_route_matches_void_action(): void
    {
        $router = new TestableRouter();
        $router->get('/stub', 'RouterTestStubController@voidAction');
        $_SERVER['REQUEST_URI']    = '/stub';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router->dispatch();

        $this->assertTrue(true);
    }

    public function test_dispatch_sends_response_when_action_returns_response(): void
    {
        $router = new TestableRouter();
        $router->get('/stub', 'RouterTestStubController@responseAction');
        $_SERVER['REQUEST_URI']    = '/stub';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('terminated');
        $router->dispatch();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function toRegex(string $uri): string
    {
        $reflection = new \ReflectionClass(Router::class);
        $method     = $reflection->getMethod('toRegex');

        return $method->invoke($this->router, $uri);
    }

    private function resolveUri(): string
    {
        $reflection = new \ReflectionClass(Router::class);
        $method     = $reflection->getMethod('resolveUri');

        return $method->invoke($this->router);
    }

    private function matchesRoute(array $route, string $method, string $uri, ?array &$matches): bool
    {
        $reflection = new \ReflectionClass(Router::class);
        $methodRef  = $reflection->getMethod('matchesRoute');

        $args   = [$route, $method, $uri];
        $args[] = &$matches;

        return $methodRef->invokeArgs($this->router, $args);
    }
}

class TestableRouter extends Router
{
    protected function terminate(): never
    {
        throw new \RuntimeException('terminated');
    }
}

class RouterTestableResponse extends \App\Core\Response
{
    protected function terminate(): never
    {
        throw new \RuntimeException('terminated');
    }
}

}

namespace App\Controllers {

if (!class_exists(\App\Controllers\RouterTestStubController::class)) {
    class RouterTestStubController
    {
        public function voidAction(): void {}

        public function responseAction(): \App\Core\Response
        {
            return \Tests\Unit\RouterTestableResponse::redirect('/test');
        }
    }
}

}
