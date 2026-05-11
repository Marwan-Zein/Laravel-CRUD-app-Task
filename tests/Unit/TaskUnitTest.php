<?php

namespace Tests\Unit;

use App\Exceptions\ApiNotFoundException;
use Tests\TestCase;
use Mockery;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Eloquent\EloquentTaskRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Log;

class TaskUnitTest extends TestCase
{
    protected User $user;
    protected $mockRepo;

    protected function setup(): void {
        parent::setUp();
        $this->user = new User([
            'id'       => 1,
            'name'     => 'user1',
            'email'    => 'test@gmail.com',
            'password' => '123'
        ])->forceFill(['id' => 1]);

        $this->user->exists = true;

        $this->mockRepo = Mockery::mock(EloquentTaskRepository::class);
        $this->app->instance(TaskRepositoryInterface::class,$this->mockRepo);
    }
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_find_task_by_id()
    {

        $this->mockRepo->shouldReceive('FindById')
            ->once()
            ->with(1)
            ->andReturn(new Task([
                'id'          => 1,
                'title'       => 'first task',
                'description' => 'anything'
            ]));

        $response = $this->actingAs($this->user)->get('/api/tasks/1');
        $response->assertStatus(200);
    }

    public function test_find_task_by_id_not_found(){
        $this->mockRepo->shouldReceive('FindById')
        ->once()
        ->with(999)
        ->andThrow(new ApiNotFoundException(Task::class));

        $response = $this->actingAs($this->user)->get('/api/tasks/999');
        $response->assertStatus(404);
    }

    public function test_find_all_tasks_succeeds()
    {
        $tasks = collect([
            new Task(['id' => 1, 'user_id' => 1, 'title' => 'task 1', 'description' => 'desc 1']),
            new Task(['id' => 2, 'user_id' => 1, 'title' => 'task 2', 'description' => 'desc 2']),
        ]);

        $this->mockRepo->shouldReceive('FindAll')
            ->once()
            ->andReturn($tasks);


        $response = $this->actingAs($this->user)->get('/api/tasks?paginate=true');
        Log::info($response->json());
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'tasks');
    }

    public function test_find_all_tasks_returns_empty()
    {
        $this->mockRepo->shouldReceive('FindAll')
            ->once()
            ->andReturn(collect([]));

        $response = $this->actingAs($this->user)->get('/api/tasks');
        $response->assertStatus(200)
                 ->assertJsonCount(0, 'tasks');
    }

     public function test_create_task_succeeds()
    {
        $this->mockRepo->shouldReceive('create')
            ->once()
            ->andReturn(new Task([
                'id'          => 1,
                'user_id'     => 1,
                'title'       => 'new task',
                'description' => 'new desc'
            ]));

        $response = $this->actingAs($this->user)->postJson('/api/tasks', [
            'title'       => 'do your home work23123',
            'description' => 'new descsdwadaw'
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'new task']);
    }

    public function test_create_task_fails_with_missing_fields()
    {
        $response = $this->actingAs($this->user)->postJson('/api/tasks', []);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }

    public function test_create_task_fails_for_unknown_user()
    {
        $this->mockRepo->shouldReceive('create')
            ->once()
            ->andThrow(new \App\Exceptions\ApiNotFoundException(User::class));

        $response = $this->actingAs($this->user)->postJson('/api/tasks', [
            'title'       => 'new tasksajdowjadlksjaldk',
            'description' => 'new descsadadlsahdkjs'
        ]);

        $response->assertStatus(404);
    }

    public function test_update_task_succeeds()
    {
        $this->mockRepo->shouldReceive('UpdateById')
            ->once()
            ->andReturn(new Task([
                'id'          => 1,
                'user_id'     => 1,
                'title'       => 'updated task',
                'description' => 'updated desc'
            ]));

        $response = $this->actingAs($this->user)->putJson('/api/tasks/1', [
            'title'       => 'updated taskkkkkkkk',
            'description' => 'updated desc'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'updated task']);
    }

    public function test_update_task_not_found()
    {
        $this->mockRepo->shouldReceive('UpdateById')
            ->once()
            ->andThrow(new \App\Exceptions\ApiNotFoundException(Task::class));

        $response = $this->actingAs($this->user)->putJson('/api/tasks/999', [
            'title'       => 'updated task',
            'description' => 'updated desc'
        ]);

        $response->assertStatus(404);
    }

    public function test_delete_task_succeeds(){
        $this->mockRepo->shouldReceive('DeleteById')
        ->once()
        ->andReturn(new Task([
                'id'      => 1,
                'user_id' => 1,
                'title'   => 'task to delete'
        ]));

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/1");
        $response->assertStatus(200);

    }

    public function test_search_deleted_task_fails()
    {
        $this->mockRepo->shouldReceive('FindById')
            ->once()
            ->with(999)
            ->andThrow(new ApiNotFoundException(Task::class));

        $response = $this->actingAs($this->user)->get('/api/tasks/999');
        $response->assertStatus(404);
    }

}
