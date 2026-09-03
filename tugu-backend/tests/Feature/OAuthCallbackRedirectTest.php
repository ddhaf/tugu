<?php

namespace Tests\Feature;

use App\Services\AuthService;
use Mockery;
use Tests\TestCase;

class OAuthCallbackRedirectTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_callback_redirects_to_configured_frontend_url(): void
    {
        config(['services.frontend.url' => 'http://172.16.204.64:5173']);

        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->shouldReceive('handleGoogleCallback')
            ->once()
            ->andReturn('mock-jwt-token-123');

        $this->app->instance(AuthService::class, $mockAuthService);

        $response = $this->get('/api/auth/google/callback');

        $response->assertRedirect('http://172.16.204.64:5173/oauth/google/callback#token=mock-jwt-token-123');
    }

    public function test_facebook_callback_redirects_to_configured_frontend_url(): void
    {
        config(['services.frontend.url' => 'http://172.16.204.64:5173']);

        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->shouldReceive('handleFacebookCallback')
            ->once()
            ->andReturn('mock-jwt-token-456');

        $this->app->instance(AuthService::class, $mockAuthService);

        $response = $this->get('/api/auth/facebook/callback');

        $response->assertRedirect('http://172.16.204.64:5173/oauth/facebook/callback#token=mock-jwt-token-456');
    }
}
