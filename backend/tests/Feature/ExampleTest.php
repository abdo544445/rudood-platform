<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test root redirects to login and login page returns 200.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $rootResponse = $this->get('/');
        $rootResponse->assertStatus(200);
    }
}
