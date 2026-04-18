<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Response;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    // -------------------------------------------------------------------------
    // redirect
    // -------------------------------------------------------------------------

    public function test_redirect_sets_url(): void
    {
        $response = Response::redirect('/turmas');

        $this->assertSame('/turmas', $response->getRedirectUrl());
    }

    public function test_redirect_default_status_code_is_302(): void
    {
        $response = Response::redirect('/turmas');

        $this->assertSame(302, $response->getStatusCode());
    }

    public function test_redirect_accepts_custom_status_code(): void
    {
        $response = Response::redirect('/turmas', 301);

        $this->assertSame(301, $response->getStatusCode());
    }

    public function test_redirect_is_redirect(): void
    {
        $response = Response::redirect('/turmas');

        $this->assertTrue($response->isRedirect());
    }

    public function test_redirect_is_not_view(): void
    {
        $response = Response::redirect('/turmas');

        $this->assertFalse($response->isView());
    }

    public function test_redirect_has_null_view(): void
    {
        $response = Response::redirect('/turmas');

        $this->assertNull($response->getView());
    }

    public function test_redirect_has_empty_data(): void
    {
        $response = Response::redirect('/turmas');

        $this->assertSame([], $response->getData());
    }

    // -------------------------------------------------------------------------
    // view
    // -------------------------------------------------------------------------

    public function test_view_sets_path(): void
    {
        $response = Response::view('turmas/index');

        $this->assertSame('turmas/index', $response->getView());
    }

    public function test_view_sets_data(): void
    {
        $response = Response::view('turmas/index', ['heading' => 'Turmas']);

        $this->assertSame(['heading' => 'Turmas'], $response->getData());
    }

    public function test_view_default_data_is_empty(): void
    {
        $response = Response::view('turmas/index');

        $this->assertSame([], $response->getData());
    }

    public function test_view_default_status_code_is_200(): void
    {
        $response = Response::view('turmas/index');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_view_is_view(): void
    {
        $response = Response::view('turmas/index');

        $this->assertTrue($response->isView());
    }

    public function test_view_is_not_redirect(): void
    {
        $response = Response::view('turmas/index');

        $this->assertFalse($response->isRedirect());
    }

    public function test_view_has_null_redirect_url(): void
    {
        $response = Response::view('turmas/index');

        $this->assertNull($response->getRedirectUrl());
    }

    // -------------------------------------------------------------------------
    // send
    // -------------------------------------------------------------------------

    public function test_send_redirect_terminates(): void
    {
        $response = TestableResponse::redirect('/turmas');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('terminated');
        $response->send();
    }

    public function test_send_view_terminates(): void
    {
        $response = TestableResponse::view('_test_stub');

        ob_start();
        try {
            $response->send();
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertSame('terminated', $e->getMessage());
        } finally {
            ob_end_clean();
        }
    }

    public function test_send_plain_terminates(): void
    {
        $response = new TestableResponse();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('terminated');
        $response->send();
    }
}

class TestableResponse extends Response
{
    protected function terminate(): never
    {
        throw new \RuntimeException('terminated');
    }
}
