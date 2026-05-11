<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Test\TestTaskRespository;
use App\Repositories\Test\TestUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private TestUserRepository $userRepo;
    private TestTaskRespository $taskRepo;
    private int $nextUserId = 1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepo = new TestUserRepository();
        $this->taskRepo = new TestTaskRespository();

        $this->app->instance(UserRepositoryInterface::class, $this->userRepo);
        $this->app->instance(TaskRepositoryInterface::class, $this->taskRepo);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_example(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_task_status_must_be_valid(): void
    {
        $user = $this->createUser();

        Sanctum::actingAs($user);

        $this->postJson('/api/tasks', [
            'title' => 'Valid Task Title',
            'status' => 'invalid_status',
        ])->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Invalid Fields Provided',
            ]);
    }

    public function test_task_can_only_mark_compeleted_by_admins(): void
    {
        Role::create(["name"=>"admin"]);
        Role::create(["name"=>"user"]);

        $regularUser = $this->createUser(role: 'user');
        $adminUser = $this->createUser([
            'name'=>"Admin",
            'email'=>'admin@test.com'
        ],role:'admin');

        $regularTask = $this->createTask($regularUser->id, [
            'title' => 'Regular user task',
        ]);

        $adminTask = $this->createTask($adminUser->id, [
            'title' => 'Admin user task',
        ]);

        Sanctum::actingAs($regularUser);

        $this->patchJson("/api/tasks/{$regularTask->id}/complete")
            ->assertStatus(403);

        Sanctum::actingAs($adminUser);

        $this->patchJson("/api/tasks/{$adminTask->id}/complete")
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Task marked as completed',
            ]);
    }

    public function test_task_accepts_valid_status(): void
    {
        $user = $this->createUser();

        Sanctum::actingAs($user);

        $this->postJson('/api/tasks', [
            'title' => 'Valid Task Title',
            'description' => 'Task description',
        ])->assertStatus(201)
            ->assertJsonFragment([
                'status' => true,
                'title' => 'Valid Task Title',
                'description' => 'Task description',
            ]);
    }

    public function test_user_can_only_see_their_tasks(): void
    {
        $user1 = $this->createUser([
            'email' => 'user3@test.com',
        ]);
        $user2 = $this->createUser([
            'email' => 'user4@test.com',
        ]);

        $this->createTask($user1->id, [
            'title' => 'User 1 private task',
        ]);
        $this->createTask($user2->id, [
            'title' => 'User 2 visible task',
        ]);

        Sanctum::actingAs($user2);

        $this->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonMissing([
                'title' => 'User 1 private task',
            ])
            ->assertJsonFragment([
                'title' => 'User 2 visible task',
            ]);
    }

    public function test_user_cannot_access_other_users_task(): void
    {
        $user1 = $this->createUser([
            'email' => 'user5@test.com',
        ]);
        $user2 = $this->createUser([
            'email' => 'user6@test.com',
        ]);

        $task = $this->createTask($user1->id, [
            'title' => 'Secret Task Name',
        ]);

        Sanctum::actingAs($user2);

        $this->getJson("/api/tasks/{$task->id}")
            ->assertStatus(404);
    }

    private function createUser(array $overrides = [], ?string $role = null): User
    {
        $user = $this->userRepo->create(array_merge([
            'name' => 'User '.$this->nextUserId,
            'email' => 'user'.$this->nextUserId.'@test.com',
            'password' => bcrypt('password123'),
        ], $overrides));

        $user->id = $this->nextUserId;
        $user->exists = true;

        $this->nextUserId++;

        if ($role !== null) {
            Role::firstOrCreate(['name' => $role]);
            $user->assignRole($role);
        }

        return $user;
    }

    private function createTask(int $userId, array $overrides = [])
    {
        return $this->taskRepo->create($userId, array_merge([
            'title' => 'Default Task Title',
            'description' => 'Default task description',
            'status' => 'pending',
        ], $overrides));
    }
}
