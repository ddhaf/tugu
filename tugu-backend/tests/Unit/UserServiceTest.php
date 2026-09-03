<?php

namespace Tests\Unit;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Services\UserService;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_find_by_email_returns_user_data_from_repository(): void
    {
        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $repository = Mockery::mock(UserRepositoryInterface::class);

        $repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        $service = new UserService($repository);

        $result = $service->findByEmail('test@example.com');

        $this->assertTrue($result['found']);

        $this->assertSame([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ], $result['data']);

        $this->assertNull($result['message']);
    }

    public function test_find_by_email_returns_not_found_result(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);

        $repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('missing@example.com')
            ->andReturn(null);

        $service = new UserService($repository);

        $result = $service->findByEmail('missing@example.com');

        $this->assertFalse($result['found']);
        $this->assertNull($result['data']);
        $this->assertSame('User not found', $result['message']);
    }

    public function test_find_by_name_returns_user_from_repository(): void
    {
        $user = new User([
            'id' => 2,
            'name' => 'Dhafa',
            'email' => 'dhafa@example.com',
        ]);

        $repository = Mockery::mock(UserRepositoryInterface::class);

        $repository
            ->shouldReceive('findByName')
            ->once()
            ->with('Dhafa')
            ->andReturn($user);

        $service = new UserService($repository);

        $result = $service->findByName('Dhafa');

        $this->assertSame($user, $result);
    }

    public function test_find_by_name_returns_null_when_user_does_not_exist(): void
    {
        $repository = Mockery::mock(UserRepositoryInterface::class);

        $repository
            ->shouldReceive('findByName')
            ->once()
            ->with('Unknown User')
            ->andReturn(null);

        $service = new UserService($repository);

        $result = $service->findByName('Unknown User');

        $this->assertNull($result);
    }
}