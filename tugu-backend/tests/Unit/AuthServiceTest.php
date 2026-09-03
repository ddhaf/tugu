<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    private function mockAuthLogin(string $expectedToken = 'test-jwt'): void
    {
        $guard = Mockery::mock(Guard::class);

        $guard
            ->shouldReceive('login')
            ->once()
            ->with(Mockery::type(User::class))
            ->andReturn($expectedToken);

        $authFactory = Mockery::mock(AuthFactory::class);

        $authFactory
            ->shouldReceive('guard')
            ->with('api')
            ->andReturn($guard);

        $this->app->instance(AuthFactory::class, $authFactory);
    }

    private function mockSocialiteUser(
        string $provider,
        string $id,
        string $name,
        ?string $email
    ): void {
        $socialUser = Mockery::mock();

        $socialUser
            ->shouldReceive('getId')
            ->andReturn($id);

        $socialUser
            ->shouldReceive('getName')
            ->andReturn($name);

        $socialUser
            ->shouldReceive('getEmail')
            ->andReturn($email);

        $driver = Mockery::mock();

        $driver
            ->shouldReceive('stateless')
            ->andReturnSelf();

        $driver
            ->shouldReceive('user')
            ->once()
            ->andReturn($socialUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with($provider)
            ->andReturn($driver);
    }

    public function test_login_returns_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'name' => 'Auth Test User',
            'email' => 'auth-test@example.com',
            'password' => 'password123',
        ]);

        $guard = Mockery::mock(Guard::class);

        $guard
            ->shouldReceive('login')
            ->once()
            ->with(Mockery::type(User::class))
            ->andReturn('login-jwt');

        $authFactory = Mockery::mock(AuthFactory::class);

        $authFactory
            ->shouldReceive('guard')
            ->with('api')
            ->andReturn($guard);

        $this->app->instance(AuthFactory::class, $authFactory);

        $service = app(AuthService::class);

        $token = $service->login(
            'auth-test@example.com',
            'password123'
        );

        $this->assertSame('login-jwt', $token);
    }

    public function test_login_returns_null_for_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'auth-test@example.com',
            'password' => 'password123',
        ]);

        $service = app(AuthService::class);

        $result = $service->login(
            'auth-test@example.com',
            'wrong-password'
        );

        $this->assertNull($result);
    }

    public function test_register_creates_user_and_returns_token(): void
    {
        $guard = Mockery::mock(Guard::class);

        $guard
            ->shouldReceive('login')
            ->once()
            ->with(Mockery::type(User::class))
            ->andReturn('register-jwt');

        $authFactory = Mockery::mock(AuthFactory::class);

        $authFactory
            ->shouldReceive('guard')
            ->with('api')
            ->andReturn($guard);

        $this->app->instance(AuthFactory::class, $authFactory);

        $service = app(AuthService::class);

        $token = $service->register([
            'name' => 'New Auth User',
            'email' => 'new-auth@example.com',
            'password' => 'password123',
        ]);

        $this->assertSame('register-jwt', $token);

        $this->assertDatabaseHas('users', [
            'name' => 'New Auth User',
            'email' => 'new-auth@example.com',
        ]);
    }

    public function test_google_callback_logs_in_existing_user_by_google_id(): void
    {
        $user = User::factory()->create([
            'name' => 'Google User',
            'email' => 'google@example.com',
            'google_id' => 'google-123',
        ]);

        $this->mockSocialiteUser(
            'google',
            'google-123',
            'Google User',
            'google@example.com'
        );

        $this->mockAuthLogin('google-jwt');

        $service = app(AuthService::class);

        $token = $service->handleGoogleCallback();

        $this->assertSame('google-jwt', $token);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google-123',
        ]);
    }

    public function test_google_callback_links_existing_user_by_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Existing User',
            'email' => 'google-link@example.com',
            'google_id' => null,
        ]);

        $this->mockSocialiteUser(
            'google',
            'google-456',
            'Existing User',
            'google-link@example.com'
        );

        $this->mockAuthLogin('google-link-jwt');

        $service = app(AuthService::class);

        $token = $service->handleGoogleCallback();

        $this->assertSame('google-link-jwt', $token);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => 'google-456',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_google_callback_creates_new_user(): void
    {
        $this->mockSocialiteUser(
            'google',
            'google-new',
            'New Google User',
            'newgoogle@example.com'
        );

        $this->mockAuthLogin('google-new-jwt');

        $service = app(AuthService::class);

        $token = $service->handleGoogleCallback();

        $this->assertSame('google-new-jwt', $token);

        $this->assertDatabaseHas('users', [
            'name' => 'New Google User',
            'email' => 'newgoogle@example.com',
            'google_id' => 'google-new',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_facebook_callback_logs_in_existing_user_by_facebook_id(): void
    {
        $user = User::factory()->create([
            'name' => 'Facebook User',
            'email' => 'facebook_existing@facebook.local',
            'facebook_id' => 'facebook-123',
        ]);

        $this->mockSocialiteUser(
            'facebook',
            'facebook-123',
            'Facebook User',
            null
        );

        $this->mockAuthLogin('facebook-jwt');

        $service = app(AuthService::class);

        $token = $service->handleFacebookCallback();

        $this->assertSame('facebook-jwt', $token);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'facebook_id' => 'facebook-123',
        ]);
    }

    public function test_facebook_callback_creates_new_user(): void
    {
        $facebookId = 'facebook-new';

        $this->mockSocialiteUser(
            'facebook',
            $facebookId,
            'New Facebook User',
            null
        );

        $this->mockAuthLogin('facebook-new-jwt');

        $service = app(AuthService::class);

        $token = $service->handleFacebookCallback();

        $this->assertSame('facebook-new-jwt', $token);

        $this->assertDatabaseHas('users', [
            'name' => 'New Facebook User',
            'email' => 'facebook_' . $facebookId . '@facebook.local',
            'facebook_id' => $facebookId,
        ]);

        $this->assertDatabaseCount('users', 1);
    }
}