<?php

namespace Tajul\Saajan\Tests\Unit;

use Illuminate\Http\Request;
use Tajul\Saajan\Http\Middleware\InjectHelloWorld;
use Tajul\Saajan\Tests\TestCase;

class InjectHelloWorldMiddlewareTest extends TestCase
{
    /** @test */
    function it_checks_for_a_hello_word_in_response()
    {
        // Given we have a request
        $request = new Request();

        // when we pass the request to this middleware,
        // the response should contain 'Hello World'
        $response = (new InjectHelloWorld())->handle($request, function ($request) { });

        $this->assertStringContainsString('Hello World', $response);
    }
}